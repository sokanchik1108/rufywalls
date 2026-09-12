<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use App\Models\Order;
use App\Models\OutgoingPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FinancialAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Проверка доступа к аналитике
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
        | Заказы
        |--------------------------------------------------------------------------
        */

        $orders = Order::with([
            'items.variant.product'
        ])->get();

        $revenue = 0;
        $cost = 0;

        foreach ($orders as $order) {

            $orderDate = $order->order_date
                ? Carbon::parse($order->order_date)
                : Carbon::parse($order->created_at);

            if ($orderDate->lt($from) || $orderDate->gt($to)) {
                continue;
            }

            foreach ($order->items as $item) {

                if (!$item->variant || !$item->variant->product) {
                    continue;
                }

                $quantity = (float) $item->quantity;
                $salePrice = (float) $item->price;
                $purchasePrice = (float) $item->variant->product->purchase_price;

                $revenue += $quantity * $salePrice;

                $cost += $quantity * $purchasePrice;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Прибыль с продаж
        |--------------------------------------------------------------------------
        */

        $salesProfit = $revenue - $cost;

        /*
        |--------------------------------------------------------------------------
        | Исходящие платежи
        |--------------------------------------------------------------------------
        */

        $outgoingPayments = OutgoingPayment::with('expenseType')
            ->whereBetween('payment_date', [
                $from->startOfDay(),
                $to->endOfDay(),
            ])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        $otherExpenses = $outgoingPayments->sum(function ($payment) {
            return (float) $payment->amount;
        });

        /*
        |--------------------------------------------------------------------------
        | Чистая прибыль
        |--------------------------------------------------------------------------
        */

        $netProfit = $salesProfit - $otherExpenses;

        /*
        |--------------------------------------------------------------------------
        | Виды расходов
        |--------------------------------------------------------------------------
        */

        $expenseTypes = ExpenseType::withCount([
            'outgoingPayments as outgoing_payments_count' => function ($query) use ($from, $to) {
                $query->whereDate('payment_date', '>=', $from->toDateString())
                    ->whereDate('payment_date', '<=', $to->toDateString());
            }
        ])
            ->orderBy('name')
            ->get();

        return view('admin.analytics.finance', compact(
            'from',
            'to',
            'revenue',
            'cost',
            'salesProfit',
            'otherExpenses',
            'netProfit',
            'expenseTypes',
            'outgoingPayments'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Добавить вид расхода
    |--------------------------------------------------------------------------
    */

    public function storeExpenseType(Request $request)
    {
        // Проверка доступа к аналитике
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        ExpenseType::create([
            'name' => trim($validated['name']),
        ]);

        return back()->with('success', 'Вид расхода добавлен.');
    }

    /*
    |--------------------------------------------------------------------------
    | Удалить вид расхода
    |--------------------------------------------------------------------------
    */

    public function destroyExpenseType(ExpenseType $expenseType)
    {
        // Проверка доступа к аналитике
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        if ($expenseType->outgoingPayments()->exists()) {
            return back()->with(
                'error',
                'Нельзя удалить этот вид расхода, потому что он используется в исходящих платежах.'
            );
        }

        $expenseType->delete();

        return back()->with('success', 'Вид расхода удалён.');
    }

    /*
    |--------------------------------------------------------------------------
    | Добавить исходящий платёж
    |--------------------------------------------------------------------------
    */

    public function storeOutgoingPayment(Request $request)
    {
        // Проверка доступа к аналитике
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $validated = $request->validate([
            'expense_type_id' => ['required', 'exists:expense_types,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        OutgoingPayment::create($validated);

        return back()->with('success', 'Исходящий платёж добавлен.');
    }

    /*
    |--------------------------------------------------------------------------
    | Удалить исходящий платёж
    |--------------------------------------------------------------------------
    */

    public function destroyOutgoingPayment(OutgoingPayment $outgoingPayment)
    {
        // Проверка доступа к аналитике
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $outgoingPayment->delete();

        return back()->with('success', 'Исходящий платёж удалён.');
    }

    /*
    |--------------------------------------------------------------------------
    | Платежи
    |--------------------------------------------------------------------------
    */

    public function payments(Request $request)
    {
        // Проверка доступа к аналитике
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $outgoingPayments = OutgoingPayment::with('expenseType')
            ->whereDate('payment_date', '>=', $from->toDateString())
            ->whereDate('payment_date', '<=', $to->toDateString())
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        $expenseTypes = ExpenseType::withCount([
            'outgoingPayments as outgoing_payments_count' => function ($query) use ($from, $to) {
                $query->whereDate('payment_date', '>=', $from->toDateString())
                    ->whereDate('payment_date', '<=', $to->toDateString());
            }
        ])
            ->orderBy('name')
            ->get();

        return view('admin.analytics.payments', compact(
            'from',
            'to',
            'outgoingPayments',
            'expenseTypes'
        ));
    }
}
