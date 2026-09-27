<?php

namespace App\Http\Controllers;

use App\Models\FinancialEntry;
use App\Models\FinancialSetting;
use App\Models\InventoryLayer;
use App\Models\Supplier;
use App\Services\FifoService;
use Illuminate\Http\Request;

class CapitalController extends Controller
{
    /**
     * Главная страница капитала.
     */
    public function index(FifoService $fifoService)
    {
        /*
         * ============================================================
         * 1. ДЕНЬГИ НА РУКАХ
         * ============================================================
         */

        $setting = FinancialSetting::first();

        if (!$setting) {
            $setting = FinancialSetting::create([
                'cash_on_hand' => 0,
            ]);
        }

        $cashOnHand = (float) $setting->cash_on_hand;


        /*
         * ============================================================
         * 2. ЛИЧНЫЕ РАСХОДЫ
         * ============================================================
         */

        $personalExpenses = (float) FinancialEntry::query()
            ->where('type', 'personal_expense')
            ->sum('amount');

        $personalExpenseEntries = FinancialEntry::query()
            ->where('type', 'personal_expense')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->get();


        /*
         * ============================================================
         * 3. СЕБЕСТОИМОСТЬ ТОВАРА НА СКЛАДАХ
         * ============================================================
         *
         * FIFO НЕ изменяем.
         *
         * Используем уже существующие методы:
         *
         * getRemainingQuantity()
         * getLayerUnitCost()
         *
         * Считаем только тот товар, который реально
         * остался на складах.
         */

        $inventoryLayers = InventoryLayer::query()
            ->where('quantity', '>', 0)
            ->with([
                'variant.product',
            ])
            ->get();

        $inventoryCost = 0;

        foreach ($inventoryLayers as $layer) {

            /*
             * Сколько товара из этого FIFO-слоя
             * реально осталось.
             */
            $remainingQuantity =
                $fifoService->getRemainingQuantity($layer);

            if ($remainingQuantity <= 0) {
                continue;
            }

            /*
             * Себестоимость одной единицы.
             *
             * Используем существующую логику FIFO,
             * ничего в ней не меняем.
             */
            $unitCost =
                $fifoService->getLayerUnitCost($layer);

            /*
             * Себестоимость оставшегося товара
             * в этом слое.
             */
            $inventoryCost +=
                $remainingQuantity * $unitCost;
        }

        $inventoryCost = round(
            $inventoryCost,
            2
        );


        /*
         * ============================================================
         * 4. ДОЛГИ ПОСТАВЩИКАМ
         * ============================================================
         *
         * Формула:
         *
         * товары по приходам
         * + начальный долг
         * + корректировки
         * - оплаты по приходам
         * - ручные оплаты
         */

        $supplierDebt = 0;

        $suppliers = Supplier::query()
            ->with([
                'receipts.items',
                'receipts.payments',
                'transactions',
            ])
            ->get();

        foreach ($suppliers as $supplier) {

            /*
             * Общая стоимость всех приходов поставщика.
             */
            $receiptsTotal = 0;

            foreach ($supplier->receipts as $receipt) {

                $receiptsTotal +=
                    $receipt->items->sum(
                        function ($item) {
                            return
                                (float) $item->quantity *
                                (float) $item->purchase_price;
                        }
                    );
            }


            /*
             * Оплаты, которые были сделаны
             * непосредственно по приходам.
             */
            $receiptPaymentsTotal = 0;

            foreach ($supplier->receipts as $receipt) {

                $receiptPaymentsTotal +=
                    $receipt->payments->sum(
                        function ($payment) {
                            return (float) $payment->amount;
                        }
                    );
            }


            /*
             * Начальный долг + корректировки.
             */
            $openingAndAdjustments =
                $supplier->transactions
                    ->whereIn(
                        'type',
                        [
                            'opening_debt',
                            'adjustment',
                        ]
                    )
                    ->sum(
                        function ($transaction) {
                            return (float) $transaction->amount;
                        }
                    );


            /*
             * Ручные оплаты поставщику.
             */
            $manualPaymentsTotal =
                $supplier->transactions
                    ->where(
                        'type',
                        'payment'
                    )
                    ->sum(
                        function ($transaction) {
                            return (float) $transaction->amount;
                        }
                    );


            /*
             * Текущий долг конкретному поставщику.
             */
            $debt =
                $receiptsTotal
                + $openingAndAdjustments
                - $receiptPaymentsTotal
                - $manualPaymentsTotal;


            /*
             * Общий долг не может быть отрицательным.
             */
            $supplierDebt += max(
                0,
                $debt
            );
        }


        /*
         * ============================================================
         * 5. ПЕРЕДАЁМ ДАННЫЕ В BLADE
         * ============================================================
         */

        return view(
            'admin.capital.index',
            [
                'cashOnHand' =>
                    $cashOnHand,

                'inventoryCost' =>
                    $inventoryCost,

                'personalExpenses' =>
                    $personalExpenses,

                'supplierDebt' =>
                    $supplierDebt,

                'personalExpenseEntries' =>
                    $personalExpenseEntries,

                'setting' =>
                    $setting,
            ]
        );
    }


    /**
     * Сохранение денег на руках.
     */
    public function updateCash(Request $request)
    {
        $validated = $request->validate([
            'cash_on_hand' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $setting = FinancialSetting::first();

        if (!$setting) {
            $setting = new FinancialSetting();
        }

        $setting->cash_on_hand =
            $validated['cash_on_hand'];

        $setting->save();

        return redirect()
            ->route('admin.capital.index')
            ->with(
                'success',
                'Деньги на руках сохранены.'
            );
    }


    /**
     * Добавление личного расхода.
     */
    public function storePersonalExpense(Request $request)
    {
        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'entry_date' => [
                'required',
                'date',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        FinancialEntry::create([
            'type' =>
                'personal_expense',

            'amount' =>
                $validated['amount'],

            'entry_date' =>
                $validated['entry_date'],

            'comment' =>
                $validated['comment'] ?? null,
        ]);

        return redirect()
            ->route('admin.capital.index')
            ->with(
                'success',
                'Личный расход добавлен.'
            );
    }


    /**
     * Удаление личного расхода.
     */
    public function destroyPersonalExpense(
        FinancialEntry $entry
    ) {
        /*
         * Нельзя удалить запись другого типа
         * через этот метод.
         */
        if ($entry->type !== 'personal_expense') {
            abort(404);
        }

        $entry->delete();

        return redirect()
            ->route('admin.capital.index')
            ->with(
                'success',
                'Личный расход удалён.'
            );
    }
}

