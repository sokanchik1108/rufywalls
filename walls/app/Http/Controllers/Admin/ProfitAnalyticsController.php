<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfitAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Проверка доступа к аналитике
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        /* 
        |--------------------------------------------------------------------------
        | Период 
        |--------------------------------------------------------------------------
        */

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | Заказы
        |--------------------------------------------------------------------------
        |
        | Берём все заказы за период.
        | order_date используется первым.
        | Если order_date отсутствует — используем created_at.
        |
        */

        $orders = Order::with([
            'items.variant.product'
        ])
            ->get()
            ->filter(function ($order) use ($from, $to) {

                $date = $order->order_date
                    ? Carbon::parse($order->order_date)
                    : Carbon::parse($order->created_at);

                return $date->between(
                    $from,
                    $to
                );
            });


        /*
        |--------------------------------------------------------------------------
        | Общие показатели
        |--------------------------------------------------------------------------
        */

        $revenue = 0;
        $cost = 0;
        $soldQuantity = 0;
        $returnsQuantity = 0;

        $productStats = [];


        foreach ($orders as $order) {

            foreach ($order->items as $item) {

                $quantity = (int) ($item->quantity ?? 0);

                if ($quantity === 0) {
                    continue;
                }


                $salePrice = (float) ($item->price ?? 0);

                $product = $item->variant?->product;

                if (!$product) {
                    continue;
                }


                $purchasePrice = (float) (
                    $product->purchase_price ?? 0
                );


                /*
                |--------------------------------------------------------------------------
                | Суммы
                |--------------------------------------------------------------------------
                */

                $itemRevenue =
                    $quantity * $salePrice;

                $itemCost =
                    $quantity * $purchasePrice;


                $revenue += $itemRevenue;

                $cost += $itemCost;


                /*
                |--------------------------------------------------------------------------
                | Количество
                |--------------------------------------------------------------------------
                */

                if ($quantity > 0) {

                    $soldQuantity += $quantity;
                } else {

                    $returnsQuantity += abs($quantity);
                }


                /*
                |--------------------------------------------------------------------------
                | Аналитика по SKU
                |--------------------------------------------------------------------------
                */

                $sku = $item->variant?->sku ?? 'Без SKU';


                if (!isset($productStats[$sku])) {

                    $productStats[$sku] = [
                        'sku' => $sku,
                        'quantity' => 0,
                        'returns' => 0,
                        'revenue' => 0,
                        'cost' => 0,
                        'profit' => 0,
                    ];
                }


                if ($quantity > 0) {

                    $productStats[$sku]['quantity']
                        += $quantity;
                } else {

                    $productStats[$sku]['returns']
                        += abs($quantity);
                }


                $productStats[$sku]['revenue']
                    += $itemRevenue;

                $productStats[$sku]['cost']
                    += $itemCost;

                $productStats[$sku]['profit']
                    += $itemRevenue - $itemCost;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Общая прибыль
        |--------------------------------------------------------------------------
        */

        $profit = $revenue - $cost;


        /*
        |--------------------------------------------------------------------------
        | Маржинальность
        |--------------------------------------------------------------------------
        */

        $margin = $revenue > 0
            ? ($profit / $revenue) * 100
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Сортировка товаров
        |--------------------------------------------------------------------------
        */

        $productStats = collect($productStats)
            ->sortByDesc('profit')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Дневная статистика
        |--------------------------------------------------------------------------
        */

        $dailyStats = [];

        $date = $from->copy()->startOfDay();

        while ($date->lte($to)) {

            $key = $date->toDateString();

            $dailyStats[$key] = [
                'date' => $key,
                'revenue' => 0,
                'cost' => 0,
                'profit' => 0,
            ];

            $date->addDay();
        }


        /*
        |--------------------------------------------------------------------------
        | Расчёт по дням
        |--------------------------------------------------------------------------
        */

        foreach ($orders as $order) {

            $orderDate = $order->order_date
                ? Carbon::parse($order->order_date)
                : Carbon::parse($order->created_at);

            $key = $orderDate->toDateString();


            if (!isset($dailyStats[$key])) {
                continue;
            }


            foreach ($order->items as $item) {

                $quantity = (int) ($item->quantity ?? 0);

                if ($quantity === 0) {
                    continue;
                }


                $salePrice = (float) ($item->price ?? 0);

                $product = $item->variant?->product;

                if (!$product) {
                    continue;
                }


                $purchasePrice = (float) (
                    $product->purchase_price ?? 0
                );


                $itemRevenue =
                    $quantity * $salePrice;

                $itemCost =
                    $quantity * $purchasePrice;


                $dailyStats[$key]['revenue']
                    += $itemRevenue;

                $dailyStats[$key]['cost']
                    += $itemCost;

                $dailyStats[$key]['profit']
                    += $itemRevenue - $itemCost;
            }
        }


        return view(
            'admin.analytics.profit',
            compact(
                'from',
                'to',
                'revenue',
                'cost',
                'profit',
                'margin',
                'soldQuantity',
                'returnsQuantity',
                'productStats',
                'dailyStats'
            )
        );
    }
}
