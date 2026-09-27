<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptPayment;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupplierDebtController extends Controller
{
    /**
     * Поставщик, долг которого уменьшается
     * оплатами через QR Лезговко.
     */
    private const QR_SUPPLIER = 'Артекс';

    /**
     * Способ оплаты заказа,
     * который считается погашением долга Артекс.
     */
    private const QR_PAYMENT_METHOD = 'Qr Лезговко';

    /**
     * Получить общую сумму оплат заказов
     * способом QR Лезговко.
     */
    private function getQrLegzovkoPaymentsTotal(): float
    {
        return (float) Order::query()
            ->whereHas('payments', function ($query) {
                $query->where(
                    'payment_method',
                    self::QR_PAYMENT_METHOD
                );
            })
            ->with('payments')
            ->get()
            ->sum(function ($order) {
                return $order->payments
                    ->where(
                        'payment_method',
                        self::QR_PAYMENT_METHOD
                    )
                    ->sum(function ($payment) {
                        return (float) $payment->amount;
                    });
            });
    }

    /**
     * Проверяем, является ли поставщик Артекс.
     */
    private function isArtex(Supplier $supplier): bool
    {
        return mb_strtolower(
            trim($supplier->name)
        ) === mb_strtolower(
            self::QR_SUPPLIER
        );
    }

    /**
     * Главная страница долгов поставщикам.
     */
    public function index()
    {
        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        /*
         * Общая сумма всех заказов,
         * оплаченных через QR Лезговко.
         *
         * Эта сумма относится к поставщику Артекс.
         */
        $qrLegzovkoPaymentsTotal =
            $this->getQrLegzovkoPaymentsTotal();

        $supplierData = $suppliers->map(function ($supplier) use (
            $qrLegzovkoPaymentsTotal
        ) {

            /*
             * Общая стоимость всех приёмок.
             */
            $receiptsTotal = Receipt::query()
                ->where(
                    'supplier_id',
                    $supplier->id
                )
                ->with('items')
                ->get()
                ->sum(function ($receipt) {
                    return $receipt->items->sum(function ($item) {
                        return (float) $item->quantity
                            * (float) $item->purchase_price;
                    });
                });

            /*
             * Оплаты, сделанные непосредственно
             * при создании приёмок.
             */
            $receiptPaymentsTotal = ReceiptPayment::query()
                ->whereHas('receipt', function ($query) use ($supplier) {
                    $query->where(
                        'supplier_id',
                        $supplier->id
                    );
                })
                ->sum('amount');

            /*
             * Отдельные операции по долгу.
             */
            $transactions = SupplierTransaction::query()
                ->where(
                    'supplier_id',
                    $supplier->id
                )
                ->get();

            /*
             * Начальный долг + корректировки.
             */
            $openingAndAdjustments = $transactions
                ->whereIn('type', [
                    'opening_debt',
                    'adjustment',
                ])
                ->sum('amount');

            /*
             * Ручные погашения.
             */
            $manualPaymentsTotal = $transactions
                ->where(
                    'type',
                    'payment'
                )
                ->sum('amount');

            /*
             * Оплаты через QR Лезговко.
             *
             * Они учитываются только у Артекс.
             */
            $qrLegzovkoPayments = 0;

            if ($this->isArtex($supplier)) {
                $qrLegzovkoPayments =
                    $qrLegzovkoPaymentsTotal;
            }

            /*
             * Итоговый долг.
             */
            $debt = $receiptsTotal
                + $openingAndAdjustments
                - $receiptPaymentsTotal
                - $manualPaymentsTotal
                - $qrLegzovkoPayments;

            return [
                'supplier' =>
                    $supplier,

                'receipts_total' =>
                    $receiptsTotal,

                'receipt_payments' =>
                    $receiptPaymentsTotal,

                'opening_and_adjustments' =>
                    $openingAndAdjustments,

                'manual_payments' =>
                    $manualPaymentsTotal,

                'qr_legzovko_payments' =>
                    $qrLegzovkoPayments,

                'debt' =>
                    $debt,
            ];
        });

        /*
         * Общий долг всех поставщиков.
         */
        $totalDebt = $supplierData->sum(function ($item) {
            return max(
                (float) $item['debt'],
                0
            );
        });

        return view(
            'admin.suppliers.debts.index',
            compact(
                'supplierData',
                'totalDebt'
            )
        );
    }

    /**
     * Страница конкретного поставщика.
     */
    public function show(
        Supplier $supplier
    ) {
        /*
         * Приёмки поставщика.
         */
        $receipts = Receipt::query()
            ->where(
                'supplier_id',
                $supplier->id
            )
            ->with([
                'items',
                'payments',
                'warehouse',
            ])
            ->orderByDesc('receipt_date')
            ->orderByDesc('id')
            ->get();

        /*
         * Ручные операции по долгу.
         */
        $transactions = SupplierTransaction::query()
            ->where(
                'supplier_id',
                $supplier->id
            )
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        /*
         * Общая стоимость приёмок.
         */
        $receiptsTotal = $receipts->sum(
            function ($receipt) {
                return $receipt->items->sum(
                    function ($item) {
                        return (float) $item->quantity
                            * (float) $item->purchase_price;
                    }
                );
            }
        );

        /*
         * Оплаты, сделанные прямо при приёмках.
         */
        $receiptPaymentsTotal = $receipts->sum(
            function ($receipt) {
                return $receipt->payments->sum(
                    function ($payment) {
                        return (float) $payment->amount;
                    }
                );
            }
        );

        /*
         * Начальный долг + корректировки.
         */
        $openingAndAdjustments = $transactions
            ->whereIn('type', [
                'opening_debt',
                'adjustment',
            ])
            ->sum('amount');

        /*
         * Ручные погашения.
         */
        $manualPaymentsTotal = $transactions
            ->where(
                'type',
                'payment'
            )
            ->sum('amount');

        /*
         * Оплаты через QR Лезговко.
         *
         * Только для Артекс.
         */
        $qrLegzovkoPayments = 0;

        if ($this->isArtex($supplier)) {
            $qrLegzovkoPayments =
                $this->getQrLegzovkoPaymentsTotal();
        }

        /*
         * Итоговый долг.
         */
        $debt = $receiptsTotal
            + $openingAndAdjustments
            - $receiptPaymentsTotal
            - $manualPaymentsTotal
            - $qrLegzovkoPayments;

        /*
         * Долг для отображения не может быть отрицательным.
         */
        $debt = max(
            0,
            $debt
        );

        return view(
            'admin.suppliers.debts.show',
            compact(
                'supplier',
                'receipts',
                'transactions',
                'receiptsTotal',
                'receiptPaymentsTotal',
                'openingAndAdjustments',
                'manualPaymentsTotal',
                'qrLegzovkoPayments',
                'debt'
            )
        );
    }

    /**
     * Добавить начальный долг.
     */
    public function storeOpeningDebt(
        Request $request
    ) {
        $data = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        SupplierTransaction::create([
            'supplier_id' =>
                $data['supplier_id'],

            'type' =>
                'opening_debt',

            'amount' =>
                $data['amount'],

            'transaction_date' =>
                $data['transaction_date'],

            'comment' =>
                $data['comment'] ?? null,
        ]);

        return back()->with(
            'success',
            'Начальный долг добавлен.'
        );
    }

    /**
     * Погашение долга вручную.
     *
     * Эта операция НЕ привязана к приёмке.
     */
    public function storePayment(
        Request $request
    ) {
        $data = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        try {

            DB::transaction(function () use ($data) {

                $supplier = Supplier::findOrFail(
                    $data['supplier_id']
                );

                /*
                 * Приёмки.
                 */
                $receiptsTotal = Receipt::query()
                    ->where(
                        'supplier_id',
                        $supplier->id
                    )
                    ->with('items')
                    ->get()
                    ->sum(function ($receipt) {
                        return $receipt->items->sum(function ($item) {
                            return (float) $item->quantity
                                * (float) $item->purchase_price;
                        });
                    });

                /*
                 * Оплаты при приёмках.
                 */
                $receiptPaymentsTotal = ReceiptPayment::query()
                    ->whereHas('receipt', function ($query) use ($supplier) {
                        $query->where(
                            'supplier_id',
                            $supplier->id
                        );
                    })
                    ->sum('amount');

                /*
                 * Ручные операции.
                 */
                $transactions = SupplierTransaction::query()
                    ->where(
                        'supplier_id',
                        $supplier->id
                    )
                    ->get();

                /*
                 * Начальный долг + корректировки.
                 */
                $openingAndAdjustments = $transactions
                    ->whereIn('type', [
                        'opening_debt',
                        'adjustment',
                    ])
                    ->sum('amount');

                /*
                 * Уже внесённые ручные оплаты.
                 */
                $manualPaymentsTotal = $transactions
                    ->where(
                        'type',
                        'payment'
                    )
                    ->sum('amount');

                /*
                 * QR Лезговко.
                 *
                 * Учитываем только для Артекс.
                 */
                $qrLegzovkoPayments = 0;

                if ($this->isArtex($supplier)) {
                    $qrLegzovkoPayments =
                        $this->getQrLegzovkoPaymentsTotal();
                }

                /*
                 * Текущий долг.
                 */
                $currentDebt = $receiptsTotal
                    + $openingAndAdjustments
                    - $receiptPaymentsTotal
                    - $manualPaymentsTotal
                    - $qrLegzovkoPayments;

                $currentDebt = max(
                    0,
                    $currentDebt
                );

                $paymentAmount =
                    (float) $data['amount'];

                /*
                 * Нельзя оплатить больше текущего долга.
                 */
                if ($paymentAmount > $currentDebt) {

                    throw new RuntimeException(
                        'Сумма погашения (' .
                        number_format(
                            $paymentAmount,
                            2,
                            ',',
                            ' '
                        ) .
                        ' ₸) больше текущего долга (' .
                        number_format(
                            $currentDebt,
                            2,
                            ',',
                            ' '
                        ) .
                        ' ₸).'
                    );
                }

                SupplierTransaction::create([
                    'supplier_id' =>
                        $supplier->id,

                    'type' =>
                        'payment',

                    'amount' =>
                        $paymentAmount,

                    'transaction_date' =>
                        $data['transaction_date'],

                    'comment' =>
                        $data['comment'] ?? null,
                ]);
            });

        } catch (RuntimeException $e) {

            return back()
                ->withErrors([
                    'amount' =>
                        $e->getMessage(),
                ])
                ->withInput();
        }

        return back()->with(
            'success',
            'Погашение долга добавлено.'
        );
    }

    /**
     * Ручная корректировка долга.
     *
     * +100000 = увеличить долг
     * -100000 = уменьшить долг
     */
    public function storeAdjustment(
        Request $request
    ) {
        $data = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'not_in:0',
            ],

            'transaction_date' => [
                'required',
                'date',
            ],

            'comment' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        /*
         * Если корректировка отрицательная,
         * проверяем, чтобы долг не ушёл ниже нуля.
         */
        if ((float) $data['amount'] < 0) {

            $supplier = Supplier::findOrFail(
                $data['supplier_id']
            );

            $receiptsTotal = Receipt::query()
                ->where(
                    'supplier_id',
                    $supplier->id
                )
                ->with('items')
                ->get()
                ->sum(function ($receipt) {
                    return $receipt->items->sum(function ($item) {
                        return (float) $item->quantity
                            * (float) $item->purchase_price;
                    });
                });

            $receiptPaymentsTotal = ReceiptPayment::query()
                ->whereHas('receipt', function ($query) use ($supplier) {
                    $query->where(
                        'supplier_id',
                        $supplier->id
                    );
                })
                ->sum('amount');

            $transactions = SupplierTransaction::query()
                ->where(
                    'supplier_id',
                    $supplier->id
                )
                ->get();

            $openingAndAdjustments = $transactions
                ->whereIn('type', [
                    'opening_debt',
                    'adjustment',
                ])
                ->sum('amount');

            $manualPaymentsTotal = $transactions
                ->where(
                    'type',
                    'payment'
                )
                ->sum('amount');

            /*
             * QR Лезговко.
             *
             * Учитываем только для Артекс.
             */
            $qrLegzovkoPayments = 0;

            if ($this->isArtex($supplier)) {
                $qrLegzovkoPayments =
                    $this->getQrLegzovkoPaymentsTotal();
            }

            $currentDebt = $receiptsTotal
                + $openingAndAdjustments
                - $receiptPaymentsTotal
                - $manualPaymentsTotal
                - $qrLegzovkoPayments;

            $currentDebt = max(
                0,
                $currentDebt
            );

            $reduction =
                abs(
                    (float) $data['amount']
                );

            if ($reduction > $currentDebt) {

                return back()
                    ->withErrors([
                        'amount' =>
                            'Корректировка уменьшает долг ниже нуля.'
                            . ' Текущий долг: '
                            . number_format(
                                $currentDebt,
                                2,
                                ',',
                                ' '
                            )
                            . ' ₸.',
                    ])
                    ->withInput();
            }
        }

        SupplierTransaction::create([
            'supplier_id' =>
                $data['supplier_id'],

            'type' =>
                'adjustment',

            'amount' =>
                $data['amount'],

            'transaction_date' =>
                $data['transaction_date'],

            'comment' =>
                $data['comment'],
        ]);

        return back()->with(
            'success',
            'Корректировка добавлена.'
        );
    }

    /**
     * Удаление отдельной операции.
     */
    public function destroyTransaction(
        SupplierTransaction $transaction
    ) {
        $transaction->delete();

        return back()->with(
            'success',
            'Операция удалена.'
        );
    }
}
