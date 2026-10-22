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
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        ExpenseType::create($validated);

        return back()->with(
            'success',
            'Категория расходов добавлена.'
        );
    }

    public function destroyExpenseType($id)
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403);
        }

        $expenseType = ExpenseType::findOrFail($id);

        if ($expenseType->outgoingPayments()->exists()) {
            return back()->with(
                'error',
                'Нельзя удалить категорию, к которой привязаны расходы.'
            );
        }

        $expenseType->delete();

        return back()->with(
            'success',
            'Категория расходов удалена.'
        );
    }

    public function storeOutgoingPayment(Request $request)
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403);
        }

        $validated = $request->validate([
            'expense_type_id' => ['required', 'exists:expense_types,id'],
            'point_of_sale_id' => ['nullable', 'exists:point_of_sales,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        OutgoingPayment::create($validated);

        return back()->with(
            'success',
            'Расход добавлен.'
        );
    }

    public function destroyOutgoingPayment($id)
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403);
        }

        $payment = OutgoingPayment::findOrFail($id);
        $payment->delete();

        return back()->with(
            'success',
            'Расход удалён.'
        );
    }

    public function payments(Request $request)
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

        /*
        |--------------------------------------------------------------------------
        | Продажи по способам оплаты
        |--------------------------------------------------------------------------
        */

        $ordersQuery = Order::with('payments')
            ->whereBetween('order_date', [
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
        | Возвраты по способам оплаты
        |--------------------------------------------------------------------------
        */

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

        $returnPaymentMethodTotals = [];

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

        return response()->json([
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'salesPaymentMethodTotals' => $salesPaymentMethodTotals,
            'returnPaymentMethodTotals' => $returnPaymentMethodTotals,
        ]);
    }
}
