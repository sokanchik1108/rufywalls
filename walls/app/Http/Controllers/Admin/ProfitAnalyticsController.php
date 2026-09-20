<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointOfSale;
use App\Models\StockMovement;
use App\Services\FifoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfitAnalyticsController extends Controller
{
    public function index(
        Request $request,
        FifoService $fifoService
    ) {
        /*
        |--------------------------------------------------------------------------
        | Проверка доступа
        |--------------------------------------------------------------------------
        */

        if (
            !Auth::check() ||
            !Auth::user()->can_view_analytics
        ) {
            abort(
                403,
                'Доступ к аналитике запрещён'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Период
        |--------------------------------------------------------------------------
        |
        | ВАЖНО:
        |
        | "До" является ВКЛЮЧИТЕЛЬНОЙ датой.
        |
        | Например:
        |
        | От: 16.09.2026
        | До: 16.09.2026
        |
        | Будут учитываться ВСЕ заказы за 16.09.2026.
        |
        */

        $from = $request->filled('from')
            ? Carbon::parse(
                $request->input('from'),
                'Asia/Almaty'
            )->startOfDay()
            : now('Asia/Almaty')
                ->startOfMonth()
                ->startOfDay();


        $to = $request->filled('to')
            ? Carbon::parse(
                $request->input('to'),
                'Asia/Almaty'
            )->endOfDay()
            : now('Asia/Almaty')
                ->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | Точки продаж
        |--------------------------------------------------------------------------
        */

        $pointsOfSale = PointOfSale::orderBy(
            'name'
        )->get();


        $selectedPointOfSale =
            $request->filled('point_of_sale_id')
                ? (int) $request->input('point_of_sale_id')
                : null;


        /*
        |--------------------------------------------------------------------------
        | Даты периода БЕЗ времени
        |--------------------------------------------------------------------------
        |
        | Используем именно календарные даты.
        |
        */

        $fromDate =
            $from->toDateString();

        $toDate =
            $to->toDateString();


        /*
        |--------------------------------------------------------------------------
        | Заказы
        |--------------------------------------------------------------------------
        */

        $orders = Order::with([
            'items.variant.product',
        ])
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
            ->get()
            ->filter(function ($order) use (
                $fromDate,
                $toDate
            ) {

                /*
                |--------------------------------------------------------------------------
                | Определяем дату заказа
                |--------------------------------------------------------------------------
                |
                | order_date имеет приоритет.
                |
                | Если order_date отсутствует,
                | используем created_at.
                |
                */

                if ($order->order_date) {

                    /*
                    |--------------------------------------------------------------------------
                    | order_date
                    |--------------------------------------------------------------------------
                    |
                    | Берём именно календарную дату.
                    |
                    */

                    $orderDate = Carbon::parse(
                        $order->order_date,
                        'Asia/Almaty'
                    )->toDateString();

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | created_at
                    |--------------------------------------------------------------------------
                    */

                    $orderDate = Carbon::parse(
                        $order->created_at,
                        'Asia/Almaty'
                    )->toDateString();
                }


                /*
                |--------------------------------------------------------------------------
                | ВАЖНО:
                |
                | Сравнение ВКЛЮЧИТЕЛЬНО:
                |
                | from <= orderDate <= to
                |
                |--------------------------------------------------------------------------
                */

                return $orderDate >= $fromDate
                    && $orderDate <= $toDate;
            });


        /*
        |--------------------------------------------------------------------------
        | FIFO движения
        |--------------------------------------------------------------------------
        |
        | В SalesAnalyticsController:
        |
        | source_id = $item->id
        |
        | Поэтому здесь используется тот же принцип.
        |
        */

        $fifoMovements = StockMovement::with([
            'allocations.layer.variant.product',
        ])
            ->where(
                'type',
                'sale'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Индекс FIFO
        |--------------------------------------------------------------------------
        */

        $fifoStats = [];


        foreach ($fifoMovements as $movement) {

            $orderItemId =
                (int) $movement->source_id;


            if (!$orderItemId) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Себестоимость OrderItem
            |--------------------------------------------------------------------------
            */

            $movementCost = 0;


            foreach (
                $movement->allocations as $allocation
            ) {

                $layer =
                    $allocation->layer;


                if (!$layer) {
                    continue;
                }


                $unitCost =
                    $fifoService->getLayerUnitCost(
                        $layer
                    );


                $movementCost +=
                    (int) $allocation->quantity
                    * (float) $unitCost;
            }


            $fifoStats[$orderItemId] = [

                'quantity' =>
                    (int) $movement->quantity,

                'cost' =>
                    $movementCost,
            ];
        }


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


        /*
        |--------------------------------------------------------------------------
        | Обработка заказов
        |--------------------------------------------------------------------------
        */

        foreach ($orders as $order) {

            foreach ($order->items as $item) {

                $quantity = (int) (
                    $item->quantity ?? 0
                );


                if ($quantity === 0) {
                    continue;
                }


                $salePrice = (float) (
                    $item->price ?? 0
                );


                $product =
                    $item->variant?->product;


                if (!$product) {
                    continue;
                }


                $sku =
                    $item->variant?->sku
                    ?? 'Без SKU';


                /*
                |--------------------------------------------------------------------------
                | Выручка
                |--------------------------------------------------------------------------
                */

                $itemRevenue =
                    $quantity *
                    $salePrice;


                /*
                |--------------------------------------------------------------------------
                | FIFO себестоимость
                |--------------------------------------------------------------------------
                */

                $itemCost = 0;


                $orderItemId =
                    (int) $item->id;


                if (
                    $quantity > 0 &&
                    isset(
                        $fifoStats[$orderItemId]
                    )
                ) {

                    $itemCost =
                        $fifoStats[$orderItemId]['cost'];
                }


                /*
                |--------------------------------------------------------------------------
                | Возврат
                |--------------------------------------------------------------------------
                |
                | Отрицательная строка = возврат.
                |
                */

                if ($quantity < 0) {
                    $itemCost = 0;
                }


                /*
                |--------------------------------------------------------------------------
                | Общие суммы
                |--------------------------------------------------------------------------
                */

                $revenue +=
                    $itemRevenue;


                $cost +=
                    $itemCost;


                /*
                |--------------------------------------------------------------------------
                | Количество
                |--------------------------------------------------------------------------
                */

                if ($quantity > 0) {

                    $soldQuantity +=
                        $quantity;

                } else {

                    $returnsQuantity +=
                        abs($quantity);
                }


                /*
                |--------------------------------------------------------------------------
                | Статистика по SKU
                |--------------------------------------------------------------------------
                */

                if (!isset(
                    $productStats[$sku]
                )) {

                    $productStats[$sku] = [

                        'sku' =>
                            $sku,

                        'quantity' =>
                            0,

                        'returns' =>
                            0,

                        'revenue' =>
                            0,

                        'cost' =>
                            0,

                        'profit' =>
                            0,
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
                    +=
                    $itemRevenue -
                    $itemCost;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Общая прибыль
        |--------------------------------------------------------------------------
        */

        $profit =
            $revenue -
            $cost;


        /*
        |--------------------------------------------------------------------------
        | Маржинальность
        |--------------------------------------------------------------------------
        */

        $margin =
            $revenue > 0
                ? (
                    $profit /
                    $revenue
                ) * 100
                : 0;


        /*
        |--------------------------------------------------------------------------
        | Сортировка товаров
        |--------------------------------------------------------------------------
        */

        $productStats = collect(
            $productStats
        )
            ->sortByDesc(
                'profit'
            )
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Дневная статистика
        |--------------------------------------------------------------------------
        */

        $dailyStats = [];


        $date =
            $from
                ->copy()
                ->startOfDay();


        /*
        |--------------------------------------------------------------------------
        | Создаём ВСЕ дни периода
        |--------------------------------------------------------------------------
        |
        | Включая день "До".
        |
        */

        while (
            $date->toDateString() <= $toDate
        ) {

            $key =
                $date->toDateString();


            $dailyStats[$key] = [

                'date' =>
                    $key,

                'revenue' =>
                    0,

                'cost' =>
                    0,

                'profit' =>
                    0,
            ];


            $date->addDay();
        }


        /*
        |--------------------------------------------------------------------------
        | Расчёт по дням
        |--------------------------------------------------------------------------
        */

        foreach ($orders as $order) {

            /*
            |--------------------------------------------------------------------------
            | Получаем календарную дату заказа
            |--------------------------------------------------------------------------
            */

            if ($order->order_date) {

                $orderDate = Carbon::parse(
                    $order->order_date,
                    'Asia/Almaty'
                );

            } else {

                $orderDate = Carbon::parse(
                    $order->created_at,
                    'Asia/Almaty'
                );
            }


            $key =
                $orderDate->toDateString();


            /*
            |--------------------------------------------------------------------------
            | Если дня нет в выбранном диапазоне — пропускаем
            |--------------------------------------------------------------------------
            */

            if (!isset(
                $dailyStats[$key]
            )) {
                continue;
            }


            foreach ($order->items as $item) {

                $quantity = (int) (
                    $item->quantity ?? 0
                );


                if ($quantity === 0) {
                    continue;
                }


                $salePrice = (float) (
                    $item->price ?? 0
                );


                $product =
                    $item->variant?->product;


                if (!$product) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Выручка
                |--------------------------------------------------------------------------
                */

                $itemRevenue =
                    $quantity *
                    $salePrice;


                /*
                |--------------------------------------------------------------------------
                | FIFO себестоимость
                |--------------------------------------------------------------------------
                */

                $itemCost = 0;


                $orderItemId =
                    (int) $item->id;


                if (
                    $quantity > 0 &&
                    isset(
                        $fifoStats[$orderItemId]
                    )
                ) {

                    $itemCost =
                        $fifoStats[$orderItemId]['cost'];
                }


                /*
                |--------------------------------------------------------------------------
                | Возврат
                |--------------------------------------------------------------------------
                */

                if ($quantity < 0) {
                    $itemCost = 0;
                }


                /*
                |--------------------------------------------------------------------------
                | Записываем в день
                |--------------------------------------------------------------------------
                */

                $dailyStats[$key]['revenue']
                    += $itemRevenue;


                $dailyStats[$key]['cost']
                    += $itemCost;


                $dailyStats[$key]['profit']
                    +=
                    $itemRevenue -
                    $itemCost;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

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
                'dailyStats',
                'pointsOfSale',
                'selectedPointOfSale'
            )
        );
    }
}
