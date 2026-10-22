<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointOfSale;
use App\Models\CustomerReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AnalyticsController extends Controller
{
    private string $timezone = 'Asia/Almaty';

    /*
    |--------------------------------------------------------------------------
    | ПРОВЕРКА ДОСТУПА
    |--------------------------------------------------------------------------
    */

    private function checkAnalyticsAccess(): void
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ЗАПРОС ЗАКАЗОВ ЗА ПЕРИОД
    |--------------------------------------------------------------------------
    */

    private function ordersQuery(
        Carbon $from,
        Carbon $to,
        ?int $pointOfSaleId = null
    ) {
        $query = Order::with([
            'items.variant.product',
        ])->where(function ($q) use ($from, $to) {
            $q->whereBetween('order_date', [$from, $to])
                ->orWhere(function ($sub) use ($from, $to) {
                    $sub->whereNull('order_date')
                        ->whereBetween('created_at', [$from, $to]);
                });
        });

        if ($pointOfSaleId) {
            $query->where('point_of_sale_id', $pointOfSaleId);
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | ЗАПРОС ВОЗВРАТОВ ЗА ПЕРИОД
    |--------------------------------------------------------------------------
    |
    | Возврат относится к дате CustomerReturn.return_date,
    | а точка продаж определяется через исходный заказ.
    |
    */

    private function returnsQuery(
        Carbon $from,
        Carbon $to,
        ?int $pointOfSaleId = null
    ) {
        $query = CustomerReturn::with([
            'order',
            'items.orderItem.variant.product',
            'items.variant.product',
        ])->where(function ($q) use ($from, $to) {
            $q->whereBetween('return_date', [$from, $to])
                ->orWhere(function ($sub) use ($from, $to) {
                    $sub->whereNull('return_date')
                        ->whereBetween('created_at', [$from, $to]);
                });
        });

        if ($pointOfSaleId) {
            $query->whereHas('order', function ($q) use ($pointOfSaleId) {
                $q->where('point_of_sale_id', $pointOfSaleId);
            });
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | ДАТА ЗАКАЗА
    |--------------------------------------------------------------------------
    */

    private function orderDate(Order $order): Carbon
    {
        return Carbon::parse(
            $order->order_date ?? $order->created_at,
            $this->timezone
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ДАТА ВОЗВРАТА
    |--------------------------------------------------------------------------
    */

    private function returnDate(CustomerReturn $return): Carbon
    {
        return Carbon::parse(
            $return->return_date ?? $return->created_at,
            $this->timezone
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ЦЕНА ПРОДАЖИ ВОЗВРАЩЁННОГО ТОВАРА
    |--------------------------------------------------------------------------
    |
    | CustomerReturnItem.unit_cost — себестоимость.
    | Для суммы возврата используем исходную цену OrderItem.price.
    |
    */

    private function returnItemPrice($returnItem): float
    {
        return (float) ($returnItem->orderItem?->price ?? 0);
    }

    /*
    |--------------------------------------------------------------------------
    | РАСЧЁТ ПРОДАЖ И ВОЗВРАТОВ
    |--------------------------------------------------------------------------
    */

    private function calculateTotals($orders, $returns): array
    {
        $sales = 0;
        $returnAmount = 0;

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $quantity = (int) ($item->quantity ?? 0);
                $price = (float) ($item->price ?? 0);

                // Продажи считаются только по положительному количеству.
                if ($quantity > 0) {
                    $sales += $quantity * $price;
                }
            }
        }

        foreach ($returns as $return) {
            foreach ($return->items as $item) {
                $quantity = max(0, (int) ($item->quantity ?? 0));
                $price = $this->returnItemPrice($item);

                $returnAmount += $quantity * $price;
            }
        }

        return [
            'sales' => $sales,
            'returns' => $returnAmount,
            'profit' => $sales - $returnAmount,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ОСНОВНАЯ АНАЛИТИКА
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $this->checkAnalyticsAccess();

        $pointsOfSale = PointOfSale::orderBy('name')->get();

        $selectedPointOfSale = $request->filled('point_of_sale_id')
            ? (int) $request->point_of_sale_id
            : null;

        $from = $request->filled('from')
            ? Carbon::parse($request->from, $this->timezone)->startOfDay()
            : now($this->timezone)->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to, $this->timezone)->endOfDay()
            : now($this->timezone)->endOfDay();

        if ($from->gt($to)) {
            abort(422, 'Начало периода не может быть позже его окончания.');
        }

        /*
        |--------------------------------------------------------------------------
        | ЗАКАЗЫ И ВОЗВРАТЫ ЗА ВЫБРАННЫЙ ПЕРИОД
        |--------------------------------------------------------------------------
        */

        $orders = $this->ordersQuery(
            $from,
            $to,
            $selectedPointOfSale
        )
            ->orderByRaw('COALESCE(order_date, created_at) DESC')
            ->get();

        $returns = $this->returnsQuery(
            $from,
            $to,
            $selectedPointOfSale
        )->get();

        $totalOrders = $orders->count();

        $totals = $this->calculateTotals($orders, $returns);

        $totalSales = $totals['sales'];
        $totalReturns = $totals['returns'];
        $totalProfit = $totals['profit'];

        /*
        |--------------------------------------------------------------------------
        | ПРОДАЖИ ПО ДНЯМ
        |--------------------------------------------------------------------------
        */

        $salesByDay = [];

        $getDay = function (string $date) use (&$salesByDay) {
            if (!isset($salesByDay[$date])) {
                $salesByDay[$date] = [
                    'date' => $date,
                    'orders' => 0,
                    'sales' => 0,
                    'returns' => 0,
                    'profit' => 0,
                ];
            }

            return $date;
        };

        foreach ($orders as $order) {
            $date = $this->orderDate($order)->format('Y-m-d');

            $getDay($date);
            $salesByDay[$date]['orders']++;

            foreach ($order->items as $item) {
                $quantity = (int) ($item->quantity ?? 0);
                $price = (float) ($item->price ?? 0);

                if ($quantity > 0) {
                    $salesByDay[$date]['sales'] += $quantity * $price;
                }
            }
        }

        foreach ($returns as $return) {
            $date = $this->returnDate($return)->format('Y-m-d');

            $getDay($date);

            foreach ($return->items as $item) {
                $quantity = max(0, (int) ($item->quantity ?? 0));
                $price = $this->returnItemPrice($item);

                $salesByDay[$date]['returns'] += $quantity * $price;
            }
        }

        foreach ($salesByDay as &$day) {
            $day['profit'] = $day['sales'] - $day['returns'];
        }

        unset($day);

        krsort($salesByDay);

        /*
        |--------------------------------------------------------------------------
        | АНАЛИТИКА ПО ДНЯМ НЕДЕЛИ
        |--------------------------------------------------------------------------
        */

        $weekDays = [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];

        $weeklyStats = [];

        foreach ($weekDays as $number => $name) {
            $weeklyStats[$number] = [
                'day' => $name,
                'days_count' => 0,
                'sales' => 0,
                'returns' => 0,
                'profit' => 0,
                'average_profit' => 0,
            ];
        }

        foreach ($salesByDay as $day) {
            $number = Carbon::parse(
                $day['date'],
                $this->timezone
            )->dayOfWeekIso;

            $weeklyStats[$number]['days_count']++;
            $weeklyStats[$number]['sales'] += $day['sales'];
            $weeklyStats[$number]['returns'] += $day['returns'];
            $weeklyStats[$number]['profit'] += $day['profit'];
        }

        foreach ($weeklyStats as &$day) {
            $day['average_profit'] = $day['days_count'] > 0
                ? $day['profit'] / $day['days_count']
                : 0;
        }

        unset($day);

        /*
        |--------------------------------------------------------------------------
        | СТАТИСТИКА ПО SKU
        |--------------------------------------------------------------------------
        */

        $variantStats = [];

        // Проданные товары учитываем по дате заказа.
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $quantity = (int) ($item->quantity ?? 0);

                if ($quantity <= 0) {
                    continue;
                }

                $variant = $item->variant;
                $product = $variant?->product;

                if (!$variant) {
                    continue;
                }

                $variantId = $variant->id;

                if (!isset($variantStats[$variantId])) {
                    $variantStats[$variantId] = [
                        'variant_id' => $variantId,
                        'product_id' => $product?->id,
                        'product_name' => $product?->name ?? 'Без названия',
                        'sku' => $variant->sku ?? $item->batch_code ?? '—',
                        'color' => $variant->color ?? '—',
                        'sold_quantity' => 0,
                        'return_quantity' => 0,
                        'sales' => 0,
                        'returns' => 0,
                        'profit' => 0,
                    ];
                }

                $variantStats[$variantId]['sold_quantity'] += $quantity;
                $variantStats[$variantId]['sales'] +=
                    $quantity * (float) ($item->price ?? 0);
            }
        }

        // Возвращённые товары учитываем по дате самого возврата.
        foreach ($returns as $return) {
            foreach ($return->items as $item) {
                $quantity = max(0, (int) ($item->quantity ?? 0));

                if ($quantity <= 0) {
                    continue;
                }

                $variant = $item->variant ?? $item->orderItem?->variant;
                $product = $variant?->product;

                if (!$variant) {
                    continue;
                }

                $variantId = $variant->id;

                if (!isset($variantStats[$variantId])) {
                    $variantStats[$variantId] = [
                        'variant_id' => $variantId,
                        'product_id' => $product?->id,
                        'product_name' => $product?->name ?? 'Без названия',
                        'sku' => $variant->sku ?? '—',
                        'color' => $variant->color ?? '—',
                        'sold_quantity' => 0,
                        'return_quantity' => 0,
                        'sales' => 0,
                        'returns' => 0,
                        'profit' => 0,
                    ];
                }

                $variantStats[$variantId]['return_quantity'] += $quantity;
                $variantStats[$variantId]['returns'] +=
                    $quantity * $this->returnItemPrice($item);
            }
        }

        foreach ($variantStats as &$variant) {
            $variant['profit'] = $variant['sales'] - $variant['returns'];
        }

        unset($variant);

        $productStats = collect($variantStats)
            ->sortByDesc('sold_quantity')
            ->values();

        $topProducts = $productStats->take(10);
        $bestProduct = $productStats->first();

        /*
        |--------------------------------------------------------------------------
        | СЕГОДНЯ
        |--------------------------------------------------------------------------
        */

        $todayStart = now($this->timezone)->startOfDay();
        $todayEnd = now($this->timezone)->endOfDay();

        $todayOrders = $this->ordersQuery(
            $todayStart,
            $todayEnd,
            $selectedPointOfSale
        )->get();

        $todayReturnsCollection = $this->returnsQuery(
            $todayStart,
            $todayEnd,
            $selectedPointOfSale
        )->get();

        $todayTotals = $this->calculateTotals(
            $todayOrders,
            $todayReturnsCollection
        );

        $todaySales = $todayTotals['sales'];
        $todayReturns = $todayTotals['returns'];
        $todayProfit = $todayTotals['profit'];

        /*
        |--------------------------------------------------------------------------
        | ТЕКУЩИЙ МЕСЯЦ
        |--------------------------------------------------------------------------
        */

        $monthStart = now($this->timezone)->startOfMonth();
        $monthEnd = now($this->timezone)->endOfMonth();

        $monthOrders = $this->ordersQuery(
            $monthStart,
            $monthEnd,
            $selectedPointOfSale
        )->get();

        $monthReturnsCollection = $this->returnsQuery(
            $monthStart,
            $monthEnd,
            $selectedPointOfSale
        )->get();

        $monthTotals = $this->calculateTotals(
            $monthOrders,
            $monthReturnsCollection
        );

        $monthSales = $monthTotals['sales'];
        $monthReturns = $monthTotals['returns'];
        $monthProfit = $monthTotals['profit'];

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view('admin.analytics.index', compact(
            'from',
            'to',
            'orders',
            'totalOrders',
            'totalSales',
            'totalReturns',
            'totalProfit',
            'salesByDay',
            'weeklyStats',
            'todaySales',
            'todayReturns',
            'todayProfit',
            'todayOrders',
            'monthSales',
            'monthReturns',
            'monthProfit',
            'monthOrders',
            'productStats',
            'topProducts',
            'bestProduct',
            'pointsOfSale',
            'selectedPointOfSale'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | МЕНЮ АНАЛИТИКИ
    |--------------------------------------------------------------------------
    */

    public function menu()
    {
        $this->checkAnalyticsAccess();

        return view('admin.analytics.menu');
    }

    /*
    |--------------------------------------------------------------------------
    | ВЫДАТЬ ДОСТУП К АНАЛИТИКЕ
    |--------------------------------------------------------------------------
    */

    public function makeMeCanViewAnalytics(Request $request)
    {
        $user = auth()->user();

        if (
            $user &&
            $user->email === 'analytics@mail.ru' &&
            Hash::check('kurbanov', $user->password)
        ) {
            $user->can_view_analytics = true;
            $user->save();

            return redirect()
                ->route('admin.analytics.menu')
                ->with('status', 'Вы получили доступ к аналитике.');
        }

        abort(403, 'Доступ запрещён.');
    }
}