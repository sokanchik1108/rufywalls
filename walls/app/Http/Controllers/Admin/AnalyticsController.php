<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Проверка доступа к аналитике
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        /*
        |--------------------------------------------------------------------------
        | ПЕРИОД
        |--------------------------------------------------------------------------
        */

        $from = $request->filled('from')
            ? Carbon::parse($request->from, 'Asia/Almaty')->startOfDay()
            : now('Asia/Almaty')->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to, 'Asia/Almaty')->endOfDay()
            : now('Asia/Almaty')->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | ЗАКАЗЫ
        |--------------------------------------------------------------------------
        */

        $orders = Order::with([
            'items.variant.product'
        ])
            ->where(function ($query) use ($from, $to) {

                $query->whereBetween('order_date', [$from, $to]);

                $query->orWhere(function ($q) use ($from, $to) {

                    $q->whereNull('order_date')
                        ->whereBetween('created_at', [$from, $to]);
                });
            })
            ->orderByRaw('COALESCE(order_date, created_at) DESC')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ОБЩАЯ АНАЛИТИКА
        |--------------------------------------------------------------------------
        */

        $totalOrders = $orders->count();

        $totalSales = 0;
        $totalReturns = 0;

        foreach ($orders as $order) {

            foreach ($order->items as $item) {

                $quantity = (int) ($item->quantity ?? 0);
                $price = (float) ($item->price ?? 0);

                $sum = $quantity * $price;

                if ($quantity > 0) {

                    $totalSales += $sum;
                } elseif ($quantity < 0) {

                    $totalReturns += abs($sum);
                }
            }
        }

        $totalProfit = $totalSales - $totalReturns;


        /*
        |--------------------------------------------------------------------------
        | ПРОДАЖИ ПО ДНЯМ
        |--------------------------------------------------------------------------
        */

        $salesByDay = [];

        foreach ($orders as $order) {

            $date = $order->order_date
                ? Carbon::parse($order->order_date, 'Asia/Almaty')->format('Y-m-d')
                : Carbon::parse($order->created_at, 'Asia/Almaty')->format('Y-m-d');


            if (!isset($salesByDay[$date])) {

                $salesByDay[$date] = [
                    'date' => $date,
                    'orders' => 0,
                    'sales' => 0,
                    'returns' => 0,
                    'profit' => 0,
                ];
            }


            $salesByDay[$date]['orders']++;


            foreach ($order->items as $item) {

                $quantity = (int) ($item->quantity ?? 0);
                $price = (float) ($item->price ?? 0);

                $sum = $quantity * $price;


                if ($quantity > 0) {

                    $salesByDay[$date]['sales'] += $sum;
                } elseif ($quantity < 0) {

                    $salesByDay[$date]['returns'] += abs($sum);
                }
            }
        }


        foreach ($salesByDay as &$day) {

            $day['profit'] =
                $day['sales'] -
                $day['returns'];
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

            $date = Carbon::parse(
                $day['date'],
                'Asia/Almaty'
            );

            $dayNumber = $date->dayOfWeekIso;


            $weeklyStats[$dayNumber]['days_count']++;

            $weeklyStats[$dayNumber]['sales'] +=
                $day['sales'];

            $weeklyStats[$dayNumber]['returns'] +=
                $day['returns'];

            $weeklyStats[$dayNumber]['profit'] +=
                $day['profit'];
        }


        foreach ($weeklyStats as &$day) {

            if ($day['days_count'] > 0) {

                $day['average_profit'] =
                    $day['profit'] /
                    $day['days_count'];
            } else {

                $day['average_profit'] = 0;
            }
        }

        unset($day);


        /*
        |--------------------------------------------------------------------------
        | АНАЛИТИКА ПО ТОВАРАМ
        |--------------------------------------------------------------------------
        |
        | OrderItem
        |     ↓
        | Variant
        |     ↓
        | Product
        |
        |--------------------------------------------------------------------------
        */

        $productStats = [];


        foreach ($orders as $order) {

            foreach ($order->items as $item) {

                $variant = $item->variant;
                $product = $variant?->product;


                /*
                |--------------------------------------------------------------------------
                | Если товар удалён, пропускаем
                |--------------------------------------------------------------------------
                */

                if (!$variant || !$product) {
                    continue;
                }


                $productId = $product->id;


                /*
                |--------------------------------------------------------------------------
                | Создаём товар
                |--------------------------------------------------------------------------
                */

                if (!isset($productStats[$productId])) {

                    $productStats[$productId] = [

                        'product_id' => $product->id,

                        'name' => $product->name ?? 'Без названия',

                        'sold_quantity' => 0,

                        'return_quantity' => 0,

                        'sales' => 0,

                        'returns' => 0,

                        'profit' => 0,

                        'variants' => [],

                    ];
                }


                /*
                |--------------------------------------------------------------------------
                | Данные позиции
                |--------------------------------------------------------------------------
                */

                $quantity = (int) ($item->quantity ?? 0);

                $price = (float) ($item->price ?? 0);

                $sum = $quantity * $price;


                /*
                |--------------------------------------------------------------------------
                | ПРОДАЖА
                |--------------------------------------------------------------------------
                */

                if ($quantity > 0) {

                    $productStats[$productId]['sold_quantity'] +=
                        $quantity;

                    $productStats[$productId]['sales'] +=
                        $sum;
                }


                /*
                |--------------------------------------------------------------------------
                | ВОЗВРАТ
                |--------------------------------------------------------------------------
                */ elseif ($quantity < 0) {

                    $returnQuantity = abs($quantity);

                    $returnSum = abs($sum);


                    $productStats[$productId]['return_quantity'] +=
                        $returnQuantity;

                    $productStats[$productId]['returns'] +=
                        $returnSum;
                }


                /*
                |--------------------------------------------------------------------------
                | ИТОГ ТОВАРА
                |--------------------------------------------------------------------------
                */

                $productStats[$productId]['profit'] =
                    $productStats[$productId]['sales']
                    -
                    $productStats[$productId]['returns'];


                /*
                |--------------------------------------------------------------------------
                | ВАРИАНТ / SKU
                |--------------------------------------------------------------------------
                */

                $sku = $variant->sku
                    ?? $item->batch_code
                    ?? '—';


                if (!isset(
                    $productStats[$productId]['variants'][$sku]
                )) {

                    $productStats[$productId]['variants'][$sku] = [

                        'sku' => $sku,

                        'color' => $variant->color ?? '—',

                        'sold_quantity' => 0,

                        'return_quantity' => 0,

                        'sales' => 0,

                        'returns' => 0,

                    ];
                }


                /*
                |--------------------------------------------------------------------------
                | ПРОДАЖИ ВАРИАНТА
                |--------------------------------------------------------------------------
                */

                if ($quantity > 0) {

                    $productStats[$productId]['variants'][$sku]['sold_quantity'] +=
                        $quantity;

                    $productStats[$productId]['variants'][$sku]['sales'] +=
                        $sum;
                }


                /*
                |--------------------------------------------------------------------------
                | ВОЗВРАТЫ ВАРИАНТА
                |--------------------------------------------------------------------------
                */ elseif ($quantity < 0) {

                    $productStats[$productId]['variants'][$sku]['return_quantity'] +=
                        abs($quantity);

                    $productStats[$productId]['variants'][$sku]['returns'] +=
                        abs($sum);
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ПРЕОБРАЗУЕМ В COLLECTION
        |--------------------------------------------------------------------------
        */

        $productStats = collect($productStats)
            ->map(function ($product) {

                $product['variants'] = collect(
                    $product['variants']
                )
                    ->sortByDesc('sold_quantity')
                    ->values()
                    ->all();

                return $product;
            })
            ->sortByDesc('sold_quantity')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | ТОП-10 ТОВАРОВ
        |--------------------------------------------------------------------------
        */

        $topProducts = $productStats->take(10);


        /*
        |--------------------------------------------------------------------------
        | САМЫЙ ПРОДАВАЕМЫЙ ТОВАР
        |--------------------------------------------------------------------------
        */

        $bestProduct = $productStats->first();


        /*
        |--------------------------------------------------------------------------
        | СЕГОДНЯ
        |--------------------------------------------------------------------------
        */

        $todayStart = now('Asia/Almaty')->startOfDay();

        $todayEnd = now('Asia/Almaty')->endOfDay();


        $todayOrders = Order::with('items')
            ->where(function ($query) use (
                $todayStart,
                $todayEnd
            ) {

                $query->whereBetween(
                    'order_date',
                    [$todayStart, $todayEnd]
                );

                $query->orWhere(function ($q) use (
                    $todayStart,
                    $todayEnd
                ) {

                    $q->whereNull('order_date')
                        ->whereBetween(
                            'created_at',
                            [$todayStart, $todayEnd]
                        );
                });
            })
            ->get();


        $todaySales = 0;

        $todayReturns = 0;


        foreach ($todayOrders as $order) {

            foreach ($order->items as $item) {

                $quantity = (int) ($item->quantity ?? 0);

                $price = (float) ($item->price ?? 0);

                $sum = $quantity * $price;


                if ($quantity > 0) {

                    $todaySales += $sum;
                } elseif ($quantity < 0) {

                    $todayReturns += abs($sum);
                }
            }
        }


        $todayProfit =
            $todaySales -
            $todayReturns;


        /*
        |--------------------------------------------------------------------------
        | ТЕКУЩИЙ МЕСЯЦ
        |--------------------------------------------------------------------------
        */

        $monthStart =
            now('Asia/Almaty')->startOfMonth();

        $monthEnd =
            now('Asia/Almaty')->endOfMonth();


        $monthOrders = Order::with('items')
            ->where(function ($query) use (
                $monthStart,
                $monthEnd
            ) {

                $query->whereBetween(
                    'order_date',
                    [$monthStart, $monthEnd]
                );

                $query->orWhere(function ($q) use (
                    $monthStart,
                    $monthEnd
                ) {

                    $q->whereNull('order_date')
                        ->whereBetween(
                            'created_at',
                            [$monthStart, $monthEnd]
                        );
                });
            })
            ->get();


        $monthSales = 0;

        $monthReturns = 0;


        foreach ($monthOrders as $order) {

            foreach ($order->items as $item) {

                $quantity = (int) ($item->quantity ?? 0);

                $price = (float) ($item->price ?? 0);

                $sum = $quantity * $price;


                if ($quantity > 0) {

                    $monthSales += $sum;
                } elseif ($quantity < 0) {

                    $monthReturns += abs($sum);
                }
            }
        }


        $monthProfit =
            $monthSales -
            $monthReturns;


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.analytics.index',
            compact(

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

                'bestProduct'
            )
        );
    }

    public function menu()
    {
        // Проверка доступа к аналитике
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        return view('admin.analytics.menu');
    }



    public function makeMeCanViewAnalytics(Request $request)
    {
        $user = auth()->user();

        if (
            $user->email === 'analytics@mail.ru' &&
            Hash::check('analytics', $user->password)
        ) {
            $user->can_view_analytics = true;
            $user->save();

            return redirect()->route('admin.analytics.menu')
                ->with('status', 'Вы получили доступ к аналитике.');
        }

        abort(403, 'Доступ запрещён.');
    }
}
