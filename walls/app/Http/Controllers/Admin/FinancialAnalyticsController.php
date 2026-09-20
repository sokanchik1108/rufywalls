<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use App\Models\Order;
use App\Models\OutgoingPayment;
use App\Models\PointOfSale;
use App\Models\StockMovement;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FinancialAnalyticsController extends Controller
{
    public function index(
        Request $request,
        FifoService $fifoService
    ) {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $pointsOfSale = PointOfSale::orderBy('name')->get();

        $selectedPointOfSale = $request->filled('point_of_sale_id')
            ? (int) $request->point_of_sale_id
            : null;

        /*
        |--------------------------------------------------------------------------
        | ЗАКАЗЫ
        |--------------------------------------------------------------------------
        */

        $orders = Order::with([
            'items.variant.product',
            'payments'
        ])
            ->when(
                $selectedPointOfSale,
                function ($query) use ($selectedPointOfSale) {
                    $query->where(
                        'point_of_sale_id',
                        $selectedPointOfSale
                    );
                }
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ПРОДАЖИ И FIFO-СЕБЕСТОИМОСТЬ
        |--------------------------------------------------------------------------
        */

        $revenue = 0;
        $cost = 0;

        /*
        |--------------------------------------------------------------------------
        | Собираем ID позиций заказов,
        | которые попадают в выбранный период.
        |--------------------------------------------------------------------------
        */

        $orderItemIds = [];

        foreach ($orders as $order) {

            $orderDate = $order->order_date
                ? Carbon::parse($order->order_date)
                : Carbon::parse($order->created_at);

            if (
                $orderDate->lt($from) ||
                $orderDate->gt($to)
            ) {
                continue;
            }

            foreach ($order->items as $item) {

                if (
                    !$item->variant ||
                    !$item->variant->product
                ) {
                    continue;
                }

                $orderItemIds[] = $item->id;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Все FIFO движения продаж одним запросом
        |--------------------------------------------------------------------------
        |
        | source_id = order_items.id
        |
        */

        $saleMovements = StockMovement::with([
            'allocations.layer',
        ])
            ->where('type', 'sale')
            ->whereIn('source_id', $orderItemIds)
            ->get()
            ->keyBy('source_id');

        /*
        |--------------------------------------------------------------------------
        | Расчёт выручки и FIFO-себестоимости
        |--------------------------------------------------------------------------
        */

        foreach ($orders as $order) {

            $orderDate = $order->order_date
                ? Carbon::parse($order->order_date)
                : Carbon::parse($order->created_at);

            if (
                $orderDate->lt($from) ||
                $orderDate->gt($to)
            ) {
                continue;
            }

            foreach ($order->items as $item) {

                if (
                    !$item->variant ||
                    !$item->variant->product
                ) {
                    continue;
                }

                $quantity = (float) $item->quantity;

                $salePrice = (float) $item->price;

                /*
                |--------------------------------------------------------------------------
                | ВЫРУЧКА
                |--------------------------------------------------------------------------
                */

                $revenue +=
                    $quantity * $salePrice;

                /*
                |--------------------------------------------------------------------------
                | FIFO-СЕБЕСТОИМОСТЬ
                |--------------------------------------------------------------------------
                */

                $movement =
                    $saleMovements->get($item->id);

                if (!$movement) {
                    continue;
                }

                foreach (
                    $movement->allocations
                    as $allocation
                ) {

                    $layer =
                        $allocation->layer;

                    if (!$layer) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Для initial:
                    | текущая purchase_price товара
                    |
                    | Для receipt:
                    | сохранённая цена конкретной поставки
                    |--------------------------------------------------------------------------
                    */

                    $unitCost =
                        $fifoService
                            ->getLayerUnitCost($layer);

                    $cost +=
                        (int) $allocation->quantity
                        * (float) $unitCost;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ПРИБЫЛЬ ОТ ПРОДАЖ
        |--------------------------------------------------------------------------
        */

        $salesProfit =
            $revenue - $cost;

        /*
        |--------------------------------------------------------------------------
        | Способы оплаты
        |--------------------------------------------------------------------------
        */

        $paymentMethodTotals = [];

        foreach ($orders as $order) {

            $orderDate = $order->order_date
                ? Carbon::parse($order->order_date)
                : Carbon::parse($order->created_at);

            if (
                $orderDate->lt($from) ||
                $orderDate->gt($to)
            ) {
                continue;
            }

            foreach ($order->payments as $payment) {

                $method =
                    trim($payment->payment_method);

                if ($method === '') {
                    $method = 'Не указано';
                }

                if (
                    !isset(
                        $paymentMethodTotals[$method]
                    )
                ) {
                    $paymentMethodTotals[$method] = 0;
                }

                $paymentMethodTotals[$method]
                    += (float) $payment->amount;
            }
        }

        arsort($paymentMethodTotals);

        /*
        |--------------------------------------------------------------------------
        | QR Лезговко
        |--------------------------------------------------------------------------
        */

        $qrLezgovkaPayments =
            $paymentMethodTotals['QR Лезговко']
            ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Исходящие платежи / остальные расходы
        |--------------------------------------------------------------------------
        */

        $outgoingPayments =
            OutgoingPayment::with([
                'expenseType',
                'pointOfSale'
            ])
                ->whereBetween(
                    'payment_date',
                    [$from, $to]
                )
                ->when(
                    $selectedPointOfSale,
                    function ($query) use (
                        $selectedPointOfSale
                    ) {
                        $query->where(
                            'point_of_sale_id',
                            $selectedPointOfSale
                        );
                    }
                )
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->get();

        $otherExpenses =
            $outgoingPayments->sum(
                function ($payment) {
                    return (float) $payment->amount;
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Чистая прибыль
        |--------------------------------------------------------------------------
        */

        $netProfit =
            $salesProfit
            - $otherExpenses
            - $qrLezgovkaPayments;

        /*
        |--------------------------------------------------------------------------
        | Виды расходов
        |--------------------------------------------------------------------------
        */

        $expenseTypes =
            ExpenseType::withCount([
                'outgoingPayments as outgoing_payments_count'
                    => function ($query) use (
                        $from,
                        $to,
                        $selectedPointOfSale
                    ) {

                        $query->whereDate(
                            'payment_date',
                            '>=',
                            $from->toDateString()
                        )
                            ->whereDate(
                                'payment_date',
                                '<=',
                                $to->toDateString()
                            )
                            ->when(
                                $selectedPointOfSale,
                                function ($query) use (
                                    $selectedPointOfSale
                                ) {
                                    $query->where(
                                        'point_of_sale_id',
                                        $selectedPointOfSale
                                    );
                                }
                            );
                    }
            ])
                ->orderBy('name')
                ->get();

        return view(
            'admin.analytics.finance',
            compact(
                'from',
                'to',
                'revenue',
                'cost',
                'salesProfit',
                'otherExpenses',
                'netProfit',
                'qrLezgovkaPayments',
                'expenseTypes',
                'outgoingPayments',
                'pointsOfSale',
                'selectedPointOfSale',
                'paymentMethodTotals'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Добавить вид расхода
    |--------------------------------------------------------------------------
    */

    public function storeExpenseType(Request $request)
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        ExpenseType::create([
            'name' => trim($validated['name']),
        ]);

        return back()->with(
            'success',
            'Вид расхода добавлен.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Удалить вид расхода
    |--------------------------------------------------------------------------
    */

    public function destroyExpenseType(
        ExpenseType $expenseType
    ) {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        if (
            $expenseType
                ->outgoingPayments()
                ->exists()
        ) {
            return back()->with(
                'error',
                'Нельзя удалить этот вид расхода, потому что он используется в исходящих платежах.'
            );
        }

        $expenseType->delete();

        return back()->with(
            'success',
            'Вид расхода удалён.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Добавить исходящий платёж
    |--------------------------------------------------------------------------
    */

    public function storeOutgoingPayment(
        Request $request
    ) {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $validated = $request->validate([
            'expense_type_id' => [
                'required',
                'exists:expense_types,id'
            ],

            'point_of_sale_id' => [
                'nullable',
                'integer',
                'exists:points_of_sale,id'
            ],

            'payment_date' => [
                'required',
                'date'
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01'
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
        ]);

        OutgoingPayment::create(
            $validated
        );

        return back()->with(
            'success',
            'Исходящий платёж добавлен.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Удалить исходящий платёж
    |--------------------------------------------------------------------------
    */

    public function destroyOutgoingPayment(
        OutgoingPayment $outgoingPayment
    ) {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $outgoingPayment->delete();

        return back()->with(
            'success',
            'Исходящий платёж удалён.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Платежи
    |--------------------------------------------------------------------------
    */

    public function payments(Request $request)
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Точки продаж
        |--------------------------------------------------------------------------
        */

        $pointsOfSale =
            PointOfSale::orderBy('name')->get();

        $selectedPointOfSale =
            $request->filled('point_of_sale_id')
                ? (int) $request->point_of_sale_id
                : null;

        /*
        |--------------------------------------------------------------------------
        | Исходящие платежи
        |--------------------------------------------------------------------------
        */

        $outgoingPayments =
            OutgoingPayment::with([
                'expenseType',
                'pointOfSale'
            ])
                ->whereDate(
                    'payment_date',
                    '>=',
                    $from->toDateString()
                )
                ->whereDate(
                    'payment_date',
                    '<=',
                    $to->toDateString()
                )
                ->when(
                    $selectedPointOfSale,
                    function ($query) use (
                        $selectedPointOfSale
                    ) {
                        $query->where(
                            'point_of_sale_id',
                            $selectedPointOfSale
                        );
                    }
                )
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Виды расходов
        |--------------------------------------------------------------------------
        */

        $expenseTypes =
            ExpenseType::withCount([
                'outgoingPayments as outgoing_payments_count'
                    => function ($query) use (
                        $from,
                        $to,
                        $selectedPointOfSale
                    ) {

                        $query
                            ->whereDate(
                                'payment_date',
                                '>=',
                                $from->toDateString()
                            )
                            ->whereDate(
                                'payment_date',
                                '<=',
                                $to->toDateString()
                            )
                            ->when(
                                $selectedPointOfSale,
                                function ($query) use (
                                    $selectedPointOfSale
                                ) {
                                    $query->where(
                                        'point_of_sale_id',
                                        $selectedPointOfSale
                                    );
                                }
                            );
                    }
            ])
                ->orderBy('name')
                ->get();

        return view(
            'admin.analytics.payments',
            compact(
                'from',
                'to',
                'outgoingPayments',
                'expenseTypes',
                'pointsOfSale',
                'selectedPointOfSale'
            )
        );
    }
}

