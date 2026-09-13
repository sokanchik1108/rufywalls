<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointOfSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | ПРОВЕРКА ДОСТУПА
        |--------------------------------------------------------------------------
        */

        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }


        /*
        |--------------------------------------------------------------------------
        | ТОЧКИ ПРОДАЖ
        |--------------------------------------------------------------------------
        */

        $pointsOfSale = PointOfSale::orderBy('name')->get();

        $selectedPointOfSale = $request->filled('point_of_sale_id')
            ? (int) $request->point_of_sale_id
            : null;


        /*
        |--------------------------------------------------------------------------
        | ПЕРИОД
        |--------------------------------------------------------------------------
        */

        $from = $request->filled('from')
            ? Carbon::parse(
                $request->from,
                'Asia/Almaty'
            )->startOfDay()
            : now('Asia/Almaty')->startOfMonth();


        $to = $request->filled('to')
            ? Carbon::parse(
                $request->to,
                'Asia/Almaty'
            )->endOfDay()
            : now('Asia/Almaty')->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | ЗАКАЗЫ
        |--------------------------------------------------------------------------
        */

        $ordersQuery = Order::with([
            'items.variant.product'
        ])
            ->where(function ($query) use ($from, $to) {

                $query->whereBetween(
                    'order_date',
                    [$from, $to]
                );

                $query->orWhere(function ($q) use ($from, $to) {

                    $q->whereNull('order_date')
                        ->whereBetween(
                            'created_at',
                            [$from, $to]
                        );
                });
            });


        /*
        |--------------------------------------------------------------------------
        | ФИЛЬТР ПО ТОЧКЕ ПРОДАЖ
        |--------------------------------------------------------------------------
        */

        if ($selectedPointOfSale) {

            $ordersQuery->where(
                'point_of_sale_id',
                $selectedPointOfSale
            );
        }


        $orders = $ordersQuery
            ->orderByRaw(
                'COALESCE(order_date, created_at) DESC'
            )
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


                /*
                | Продажа
                */

                if ($quantity > 0) {

                    $totalSales += $sum;
                }


                /*
                | Возврат
                */

                elseif ($quantity < 0) {

                    $totalReturns += abs($sum);
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ОБЩАЯ ПРИБЫЛЬ / ИТОГ
        |--------------------------------------------------------------------------
        */

        $totalProfit =
            $totalSales -
            $totalReturns;


        /*
        |--------------------------------------------------------------------------
        | ПРОДАЖИ ПО ДНЯМ
        |--------------------------------------------------------------------------
        */

        $salesByDay = [];


        foreach ($orders as $order) {

            $date = $order->order_date

                ? Carbon::parse(
                    $order->order_date,
                    'Asia/Almaty'
                )->format('Y-m-d')

                : Carbon::parse(
                    $order->created_at,
                    'Asia/Almaty'
                )->format('Y-m-d');


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

                $quantity =
                    (int) ($item->quantity ?? 0);

                $price =
                    (float) ($item->price ?? 0);

                $sum =
                    $quantity * $price;


                /*
                | Продажа
                */

                if ($quantity > 0) {

                    $salesByDay[$date]['sales'] += $sum;
                }


                /*
                | Возврат
                */

                elseif ($quantity < 0) {

                    $salesByDay[$date]['returns'] += abs($sum);
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ИТОГ ПО КАЖДОМУ ДНЮ
        |--------------------------------------------------------------------------
        */

        foreach ($salesByDay as &$day) {

            $day['profit'] =
                $day['sales'] -
                $day['returns'];
        }

        unset($day);


        /*
        |--------------------------------------------------------------------------
        | СОРТИРОВКА ДНЕЙ
        |--------------------------------------------------------------------------
        */

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


            $dayNumber =
                $date->dayOfWeekIso;


            $weeklyStats[$dayNumber]['days_count']++;


            $weeklyStats[$dayNumber]['sales'] +=
                $day['sales'];


            $weeklyStats[$dayNumber]['returns'] +=
                $day['returns'];


            $weeklyStats[$dayNumber]['profit'] +=
                $day['profit'];
        }


        /*
        |--------------------------------------------------------------------------
        | СРЕДНИЙ ИТОГ ПО ДНЯМ НЕДЕЛИ
        |--------------------------------------------------------------------------
        */

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
        | АНАЛИТИКА ПО ВАРИАНТАМ / SKU
        |--------------------------------------------------------------------------
        */

        $variantStats = [];


        foreach ($orders as $order) {

            foreach ($order->items as $item) {

                $variant = $item->variant;

                $product = $variant?->product;


                /*
                | Если вариант или товар удалён
                */

                if (!$variant || !$product) {
                    continue;
                }


                /*
                | SKU
                */

                $sku = $variant->sku
                    ?? $item->batch_code
                    ?? '—';


                /*
                | ID варианта
                */

                $variantId = $variant->id;


                /*
                | Создаём статистику варианта
                */

                if (!isset($variantStats[$variantId])) {

                    $variantStats[$variantId] = [

                        'variant_id' =>
                            $variant->id,

                        'product_id' =>
                            $product->id,

                        'product_name' =>
                            $product->name
                                ?? 'Без названия',

                        'sku' =>
                            $sku,

                        'color' =>
                            $variant->color
                                ?? '—',

                        'sold_quantity' => 0,

                        'return_quantity' => 0,

                        'sales' => 0,

                        'returns' => 0,

                        'profit' => 0,
                    ];
                }


                /*
                | ДАННЫЕ ПОЗИЦИИ
                */

                $quantity =
                    (int) ($item->quantity ?? 0);


                $price =
                    (float) ($item->price ?? 0);


                $sum =
                    $quantity * $price;


                /*
                | ПРОДАЖА
                */

                if ($quantity > 0) {

                    $variantStats[$variantId]['sold_quantity'] +=
                        $quantity;


                    $variantStats[$variantId]['sales'] +=
                        $sum;
                }


                /*
                | ВОЗВРАТ
                */

                elseif ($quantity < 0) {

                    $variantStats[$variantId]['return_quantity'] +=
                        abs($quantity);


                    $variantStats[$variantId]['returns'] +=
                        abs($sum);
                }


                /*
                | ИТОГ ПО ВАРИАНТУ
                */

                $variantStats[$variantId]['profit'] =
                    $variantStats[$variantId]['sales']
                    -
                    $variantStats[$variantId]['returns'];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | COLLECTION
        |--------------------------------------------------------------------------
        */

        $productStats = collect($variantStats)
            ->sortByDesc('sold_quantity')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | ТОП-10 ВАРИАНТОВ
        |--------------------------------------------------------------------------
        */

        $topProducts =
            $productStats->take(10);


        /*
        |--------------------------------------------------------------------------
        | САМЫЙ ПРОДАВАЕМЫЙ ВАРИАНТ
        |--------------------------------------------------------------------------
        */

        $bestProduct =
            $productStats->first();


        /*
        |--------------------------------------------------------------------------
        | СЕГОДНЯ
        |--------------------------------------------------------------------------
        */

        $todayStart =
            now('Asia/Almaty')->startOfDay();


        $todayEnd =
            now('Asia/Almaty')->endOfDay();


        $todayOrdersQuery = Order::with('items')
            ->where(function ($query) use (
                $todayStart,
                $todayEnd
            ) {

                $query->whereBetween(
                    'order_date',
                    [
                        $todayStart,
                        $todayEnd
                    ]
                );


                $query->orWhere(function ($q) use (
                    $todayStart,
                    $todayEnd
                ) {

                    $q->whereNull('order_date')
                        ->whereBetween(
                            'created_at',
                            [
                                $todayStart,
                                $todayEnd
                            ]
                        );
                });
            });


        /*
        | ФИЛЬТР ТОЧКИ ДЛЯ СЕГОДНЯ
        */

        if ($selectedPointOfSale) {

            $todayOrdersQuery->where(
                'point_of_sale_id',
                $selectedPointOfSale
            );
        }


        $todayOrders =
            $todayOrdersQuery->get();


        $todaySales = 0;

        $todayReturns = 0;


        foreach ($todayOrders as $order) {

            foreach ($order->items as $item) {

                $quantity =
                    (int) ($item->quantity ?? 0);


                $price =
                    (float) ($item->price ?? 0);


                $sum =
                    $quantity * $price;


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


        $monthOrdersQuery = Order::with('items')
            ->where(function ($query) use (
                $monthStart,
                $monthEnd
            ) {

                $query->whereBetween(
                    'order_date',
                    [
                        $monthStart,
                        $monthEnd
                    ]
                );


                $query->orWhere(function ($q) use (
                    $monthStart,
                    $monthEnd
                ) {

                    $q->whereNull('order_date')
                        ->whereBetween(
                            'created_at',
                            [
                                $monthStart,
                                $monthEnd
                            ]
                        );
                });
            });


        /*
        | ФИЛЬТР ТОЧКИ ДЛЯ МЕСЯЦА
        */

        if ($selectedPointOfSale) {

            $monthOrdersQuery->where(
                'point_of_sale_id',
                $selectedPointOfSale
            );
        }


        $monthOrders =
            $monthOrdersQuery->get();


        $monthSales = 0;

        $monthReturns = 0;


        foreach ($monthOrders as $order) {

            foreach ($order->items as $item) {

                $quantity =
                    (int) ($item->quantity ?? 0);


                $price =
                    (float) ($item->price ?? 0);


                $sum =
                    $quantity * $price;


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

                'bestProduct',

                'pointsOfSale',

                'selectedPointOfSale'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | МЕНЮ АНАЛИТИКИ
    |--------------------------------------------------------------------------
    */

    public function menu()
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }


        return view(
            'admin.analytics.menu'
        );
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
            $user->email === 'analytics@mail.ru' &&
            Hash::check(
                'kurbanov',
                $user->password
            )
        ) {

            $user->can_view_analytics = true;

            $user->save();


            return redirect()
                ->route(
                    'admin.analytics.menu'
                )
                ->with(
                    'status',
                    'Вы получили доступ к аналитике.'
                );
        }


        abort(
            403,
            'Доступ запрещён.'
        );
    }
}

