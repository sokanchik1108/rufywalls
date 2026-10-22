<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointOfSale;
use App\Models\ExpenseType;
use App\Models\OutgoingPayment;
use App\Models\StockMovement;
use App\Services\FifoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialAnalyticsController extends Controller
{
    public function index(Request $request, FifoService $fifoService)
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403);
        }

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        $selectedPointOfSale = $request->filled('point_of_sale_id')
            ? (int) $request->input('point_of_sale_id')
            : null;

        $revenue = 0.0;
        $cost = 0.0;

        /*
        |--------------------------------------------------------------------------
        | 1. Продажи и себестоимость по FIFO
        |--------------------------------------------------------------------------
        */

        $ordersQuery = Order::with([
            'items.variant.product',
            'payments',
        ])->whereBetween('order_date', [
            $from->toDateTimeString(),
            $to->toDateTimeString(),
        ]);

        if ($selectedPointOfSale) {
            $ordersQuery->where(
                'point_of_sale_id',
                $selectedPointOfSale
            );
        }

        $orders = $ordersQuery->get();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $quantity = (float) $item->quantity;
                $price = (float) $item->price;

                // Возвраты учитываются отдельно.
                if ($quantity <= 0) {
                    continue;
                }

                $revenue += $quantity * $price;

                $movements = StockMovement::with('allocations.layer')
                    ->where('type', 'sale')
                    ->where('source_id', $item->id)
                    ->get();

                foreach ($movements as $movement) {
                    foreach ($movement->allocations as $allocation) {
                        if (!$allocation->layer) {
                            continue;
                        }

                        $unitCost = (float) $fifoService->getLayerUnitCost(
                            $allocation->layer
                        );

                        $allocatedQuantity = abs(
                            (float) $allocation->quantity
                        );

                        $cost += $allocatedQuantity * $unitCost;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Возвраты покупателей
        |--------------------------------------------------------------------------
        */

        $returnsQuery = DB::table('customer_returns as cr')
            ->join(
                'customer_return_items as cri',
                'cri.customer_return_id',
                '=',
                'cr.id'
            )
            ->join('orders as o', 'o.id', '=', 'cr.order_id')
            ->whereBetween('cr.return_date', [
                $from->toDateTimeString(),
                $to->toDateTimeString(),
            ]);

        if ($selectedPointOfSale) {
            $returnsQuery->where(
                'o.point_of_sale_id',
                $selectedPointOfSale
            );
        }

        $returnRevenue = (float) (clone $returnsQuery)
            ->join(
                'order_items as oi',
                'oi.id',
                '=',
                'cri.order_item_id'
            )
            ->sum(DB::raw('cri.quantity * oi.price'));

        $returnCost = (float) (clone $returnsQuery)
            ->sum(DB::raw('cri.quantity * cri.unit_cost'));

        /*
        |--------------------------------------------------------------------------
        | 3. Итоговые финансовые показатели
        |--------------------------------------------------------------------------
        */

        $revenue -= $returnRevenue;
        $cost -= $returnCost;

        $salesProfit = $revenue - $cost;

        /*
        |--------------------------------------------------------------------------
        | 4. Продажи по способам оплаты
        |--------------------------------------------------------------------------
        */

        $salesPaymentMethodTotals = [];

        foreach ($orders as $order) {
            foreach ($order->payments as $payment) {
                $method = $payment->payment_method
                    ?? $payment->method
                    ?? 'Не указан';

                if (!isset($salesPaymentMethodTotals[$method])) {
                    $salesPaymentMethodTotals[$method] = 0.0;
                }

                $salesPaymentMethodTotals[$method] +=
                    (float) $payment->amount;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Возвраты по способам оплаты
        |--------------------------------------------------------------------------
        */

        $returnPaymentMethodTotals = [];

        $returnPaymentsQuery = DB::table('customer_return_payments as crp')
            ->join(
                'customer_returns as cr',
                'cr.id',
                '=',
                'crp.customer_return_id'
            )
            ->join('orders as o', 'o.id', '=', 'cr.order_id')
            ->join(
                'payment_methods as pm',
                'pm.id',
                '=',
                'crp.payment_method_id'
            )
            ->whereBetween('cr.return_date', [
                $from->toDateTimeString(),
                $to->toDateTimeString(),
            ]);

        if ($selectedPointOfSale) {
            $returnPaymentsQuery->where(
                'o.point_of_sale_id',
                $selectedPointOfSale
            );
        }

        $returnPayments = $returnPaymentsQuery
            ->select([
                'pm.name as payment_method',
                'crp.amount',
            ])
            ->get();

        foreach ($returnPayments as $payment) {
            $method = $payment->payment_method ?: 'Не указан';

            if (!isset($returnPaymentMethodTotals[$method])) {
                $returnPaymentMethodTotals[$method] = 0.0;
            }

            $returnPaymentMethodTotals[$method] +=
                (float) $payment->amount;
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Прочие расходы
        |--------------------------------------------------------------------------
        */

        $outgoingPaymentsQuery = OutgoingPayment::with([
            'expenseType',
            'pointOfSale',
        ])->whereBetween('payment_date', [
            $from->toDateTimeString(),
            $to->toDateTimeString(),
        ]);

        if ($selectedPointOfSale) {
            $outgoingPaymentsQuery->where(
                'point_of_sale_id',
                $selectedPointOfSale
            );
        }

        $outgoingPayments = $outgoingPaymentsQuery
            ->orderByDesc('payment_date')
            ->get();

        $otherExpenses = (float) $outgoingPayments->sum('amount');

        $netProfit = $salesProfit - $otherExpenses;

        /*
        |--------------------------------------------------------------------------
        | 7. Справочники
        |--------------------------------------------------------------------------
        */

        $expenseTypes = ExpenseType::orderBy('name')->get();

        $pointsOfSale = PointOfSale::orderBy('name')->get();

        return view('admin.analytics.finance', compact(
            'from',
            'to',
            'revenue',
            'cost',
            'salesProfit',
            'otherExpenses',
            'netProfit',
            'expenseTypes',
            'outgoingPayments',
            'pointsOfSale',
            'selectedPointOfSale',
            'salesPaymentMethodTotals',
            'returnPaymentMethodTotals',
            'returnRevenue',
            'returnCost'
        ));
    }

    public function storeExpenseType(Request $request)
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
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
