<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use App\Models\StockMovement;
use App\Models\PointOfSale;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalesAnalyticsController extends Controller
{
    /**
     * Проверка доступа к аналитике.
     *
     * Доступ разрешён только пользователям,
     * у которых can_view_analytics = true.
     */
    private function checkAnalyticsAccess(): void
    {
        if (
            !Auth::check() ||
            !Auth::user()->can_view_analytics
        ) {
            abort(
                403,
                'Доступ к аналитике запрещён'
            );
        }
    }


    /**
     * Аналитика продаж.
     *
     * Общая аналитика:
     * /admin/analytics/sales
     *
     * Аналитика конкретного SKU:
     * /admin/analytics/sales/{sku}
     */
    public function index(Request $request, ?string $sku = null)
    {
        /*
        |--------------------------------------------------------------------------
        | Проверка доступа
        |--------------------------------------------------------------------------
        */

        $this->checkAnalyticsAccess();


        /*
        |--------------------------------------------------------------------------
        | SKU для страницы конкретного товара
        |--------------------------------------------------------------------------
        |
        | ВАЖНО:
        | На общей странице поиск SKU через AJAX не должен менять URL.
        |
        | Поэтому здесь sku из GET-параметра НЕ используется
        | как фильтр общей страницы.
        |
        */

        $searchSku = '';


        /*
        |--------------------------------------------------------------------------
        | Если открыт конкретный SKU
        |--------------------------------------------------------------------------
        */

        if ($sku !== null) {
            $searchSku = $sku;
        }


        /*
        |--------------------------------------------------------------------------
        | Период
        |--------------------------------------------------------------------------
        */

        $from = $request->filled('from')
            ? Carbon::createFromFormat(
                'Y-m-d',
                $request->from
            )->startOfDay()
            : now()->startOfMonth()->startOfDay();


        $to = $request->filled('to')
            ? Carbon::createFromFormat(
                'Y-m-d',
                $request->to
            )->endOfDay()
            : now()->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | Точка продаж
        |--------------------------------------------------------------------------
        */

        $pointOfSaleId = $request->filled('point_of_sale_id')
            ? (int) $request->point_of_sale_id
            : null;


        $pointsOfSale = PointOfSale::orderBy('name')->get();


        $fifoService = app(FifoService::class);


        /*
        |--------------------------------------------------------------------------
        | Аналитика конкретного SKU
        |--------------------------------------------------------------------------
        */

        if ($sku !== null) {

            $variant = Variant::with('product')
                ->where('sku', $sku)
                ->firstOrFail();


            $items = OrderItem::with([
                'variant.product'
            ])
                ->where('variant_id', $variant->id)
                ->where('quantity', '>', 0)
                ->get();


            $rows = [];


            $totalQuantity = 0;
            $totalSales = 0;
            $totalCost = 0;


            foreach ($items as $item) {

                $order = Order::find($item->order_id);


                if (!$order) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Фильтр по точке продаж
                |--------------------------------------------------------------------------
                */

                if (
                    $pointOfSaleId !== null &&
                    (int) $order->point_of_sale_id !== $pointOfSaleId
                ) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Дата заказа
                |--------------------------------------------------------------------------
                */

                $orderDate =
                    $order->order_date
                    ?? $order->created_at;


                if (!$orderDate) {
                    continue;
                }


                $orderDate = Carbon::parse($orderDate);


                /*
                |--------------------------------------------------------------------------
                | Фильтр периода
                |--------------------------------------------------------------------------
                */

                if (
                    $orderDate->lt($from) ||
                    $orderDate->gt($to)
                ) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | FIFO себестоимость
                |--------------------------------------------------------------------------
                */

                $movement = StockMovement::with([
                    'allocations.layer'
                ])
                    ->where('type', 'sale')
                    ->where('source_id', $item->id)
                    ->first();


                $cost = 0;


                if ($movement) {

                    foreach (
                        $movement->allocations
                        as $allocation
                    ) {

                        $layer = $allocation->layer;


                        if (!$layer) {
                            continue;
                        }


                        $unitCost =
                            $fifoService->getLayerUnitCost(
                                $layer
                            );


                        $cost +=
                            (int) $allocation->quantity
                            * (float) $unitCost;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Продажа
                |--------------------------------------------------------------------------
                */

                $quantity = (int) $item->quantity;

                $salePrice = (float) $item->price;


                $salesAmount =
                    $quantity * $salePrice;


                $averageCost =
                    $quantity > 0
                        ? $cost / $quantity
                        : 0;


                $rows[] = [

                    'order_id' => $order->id,

                    'date' => $orderDate->format('d.m.Y'),

                    'time' => $orderDate->format('H:i'),

                    'quantity' => $quantity,

                    'sale_price' => $salePrice,

                    'average_cost' => $averageCost,

                    'sales_amount' => $salesAmount,

                    'cost_amount' => $cost,

                    'profit' => $salesAmount - $cost,

                ];


                $totalQuantity += $quantity;

                $totalSales += $salesAmount;

                $totalCost += $cost;
            }


            /*
            |--------------------------------------------------------------------------
            | Сортировка операций по дате
            |--------------------------------------------------------------------------
            */

            usort(
                $rows,
                function ($a, $b) {

                    $dateA = Carbon::createFromFormat(
                        'd.m.Y H:i',
                        $a['date'] . ' ' . $a['time']
                    );


                    $dateB = Carbon::createFromFormat(
                        'd.m.Y H:i',
                        $b['date'] . ' ' . $b['time']
                    );


                    return $dateB <=> $dateA;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Итоги
            |--------------------------------------------------------------------------
            */

            $averageSalePrice =
                $totalQuantity > 0
                    ? $totalSales / $totalQuantity
                    : 0;


            $averageCost =
                $totalQuantity > 0
                    ? $totalCost / $totalQuantity
                    : 0;


            $profit =
                $totalSales - $totalCost;


            return view(
                'admin.analytics.sales',
                compact(
                    'from',
                    'to',
                    'sku',
                    'variant',
                    'rows',
                    'totalQuantity',
                    'totalSales',
                    'totalCost',
                    'averageSalePrice',
                    'averageCost',
                    'profit',
                    'pointOfSaleId',
                    'pointsOfSale',
                    'searchSku'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Общая аналитика по товарам
        |--------------------------------------------------------------------------
        |
        | ВАЖНО:
        | searchSku здесь пустой.
        |
        | Поиск SKU на общей странице выполняется ТОЛЬКО через
        | AJAX-метод productTable().
        |
        */

        $products = $this->getProducts(
            $from,
            $to,
            $pointOfSaleId,
            $fifoService,
            ''
        );


        return view(
            'admin.analytics.sales',
            compact(
                'from',
                'to',
                'products',
                'pointOfSaleId',
                'pointsOfSale',
                'searchSku'
            )
        );
    }


    /**
     * AJAX:
     * Таблица продаж по товарам.
     *
     * /admin/analytics/sales/product-table
     */
    public function productTable(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Проверка доступа
        |--------------------------------------------------------------------------
        */

        $this->checkAnalyticsAccess();


        /*
        |--------------------------------------------------------------------------
        | Период
        |--------------------------------------------------------------------------
        */

        $from = $request->filled('from')
            ? Carbon::createFromFormat(
                'Y-m-d',
                $request->from
            )->startOfDay()
            : now()->startOfMonth()->startOfDay();


        $to = $request->filled('to')
            ? Carbon::createFromFormat(
                'Y-m-d',
                $request->to
            )->endOfDay()
            : now()->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | Точка продаж
        |--------------------------------------------------------------------------
        */

        $pointOfSaleId = $request->filled('point_of_sale_id')
            ? (int) $request->point_of_sale_id
            : null;


        /*
        |--------------------------------------------------------------------------
        | Поиск по артикулу
        |--------------------------------------------------------------------------
        |
        | Это значение приходит ТОЛЬКО AJAX-запросом.
        |
        | Например:
        |
        | sku=11526
        |
        | Найдёт:
        |
        | 11526-05
        | 11526-06
        | 11526-10
        |
        */

        $searchSku = trim(
            (string) $request->input('sku', '')
        );


        /*
        |--------------------------------------------------------------------------
        | Сортировка
        |--------------------------------------------------------------------------
        */

        $sortBy = $request->get(
            'sort_by',
            'sales'
        );


        $sortDirection = $request->get(
            'sort_direction',
            'desc'
        );


        /*
        |--------------------------------------------------------------------------
        | Разрешённые поля сортировки
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [

            'quantity',

            'average_sale_price',

            'average_cost',

            'sales',

            'cost',

            'profit',

        ];


        if (!in_array(
            $sortBy,
            $allowedSorts,
            true
        )) {

            $sortBy = 'sales';
        }


        if (!in_array(
            $sortDirection,
            ['asc', 'desc'],
            true
        )) {

            $sortDirection = 'desc';
        }


        $fifoService = app(FifoService::class);


        /*
        |--------------------------------------------------------------------------
        | Получаем товары
        |--------------------------------------------------------------------------
        */

        $products = $this->getProducts(
            $from,
            $to,
            $pointOfSaleId,
            $fifoService,
            $searchSku
        );


        /*
        |--------------------------------------------------------------------------
        | AJAX сортировка
        |--------------------------------------------------------------------------
        */

        usort(
            $products,
            function ($a, $b) use (
                $sortBy,
                $sortDirection
            ) {

                $valueA =
                    (float) ($a[$sortBy] ?? 0);


                $valueB =
                    (float) ($b[$sortBy] ?? 0);


                if ($valueA == $valueB) {

                    $skuA =
                        (string) ($a['sku'] ?? '');


                    $skuB =
                        (string) ($b['sku'] ?? '');


                    return strnatcasecmp(
                        $skuA,
                        $skuB
                    );
                }


                $result =
                    $valueA <=> $valueB;


                return $sortDirection === 'asc'
                    ? $result
                    : -$result;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Возвращаем только таблицу
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.analytics.partials.sales-products-table',
            compact(
                'products',
                'sortBy',
                'sortDirection'
            )
        );
    }


    /**
     * Получение товаров для аналитики.
     */
    private function getProducts(
        Carbon $from,
        Carbon $to,
        ?int $pointOfSaleId,
        FifoService $fifoService,
        string $searchSku = ''
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Получаем все положительные продажи
        |--------------------------------------------------------------------------
        */

        $items = OrderItem::with([
            'variant.product'
        ])
            ->where('quantity', '>', 0)
            ->get();


        $products = [];


        foreach ($items as $item) {

            $variant = $item->variant;


            if (!$variant) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Поиск по артикулу
            |--------------------------------------------------------------------------
            */

            if ($searchSku !== '') {

                $variantSku =
                    (string) $variant->sku;


                if (
                    stripos(
                        $variantSku,
                        $searchSku
                    ) === false
                ) {
                    continue;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Заказ
            |--------------------------------------------------------------------------
            */

            $order = Order::find(
                $item->order_id
            );


            if (!$order) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Фильтр по точке продаж
            |--------------------------------------------------------------------------
            */

            if (
                $pointOfSaleId !== null &&
                (int) $order->point_of_sale_id !== $pointOfSaleId
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Дата заказа
            |--------------------------------------------------------------------------
            */

            $orderDate =
                $order->order_date
                ?? $order->created_at;


            if (!$orderDate) {
                continue;
            }


            $orderDate =
                Carbon::parse($orderDate);


            /*
            |--------------------------------------------------------------------------
            | Фильтр периода
            |--------------------------------------------------------------------------
            */

            if (
                $orderDate->lt($from) ||
                $orderDate->gt($to)
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | ID варианта
            |--------------------------------------------------------------------------
            */

            $variantId =
                $variant->id;


            /*
            |--------------------------------------------------------------------------
            | Создаём строку товара
            |--------------------------------------------------------------------------
            */

            if (!isset(
                $products[$variantId]
            )) {

                $products[$variantId] = [

                    'variant_id' => $variantId,

                    'sku' => $variant->sku,

                    'name' =>
                        $variant->product?->name
                        ?? '—',

                    'quantity' => 0,

                    'sales' => 0,

                    'cost' => 0,

                    'profit' => 0,

                    'average_sale_price' => 0,

                    'average_cost' => 0,

                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Количество и цена
            |--------------------------------------------------------------------------
            */

            $quantity =
                (int) $item->quantity;


            $salePrice =
                (float) $item->price;


            $salesAmount =
                $quantity * $salePrice;


            /*
            |--------------------------------------------------------------------------
            | FIFO себестоимость
            |--------------------------------------------------------------------------
            */

            $movement = StockMovement::with([
                'allocations.layer'
            ])
                ->where('type', 'sale')
                ->where('source_id', $item->id)
                ->first();


            $cost = 0;


            if ($movement) {

                foreach (
                    $movement->allocations
                    as $allocation
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


                    $cost +=
                        (int) $allocation->quantity
                        * (float) $unitCost;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Добавляем в статистику товара
            |--------------------------------------------------------------------------
            */

            $products[$variantId]['quantity']
                += $quantity;


            $products[$variantId]['sales']
                += $salesAmount;


            $products[$variantId]['cost']
                += $cost;


            $products[$variantId]['profit']
                += $salesAmount - $cost;
        }


        /*
        |--------------------------------------------------------------------------
        | Рассчитываем средние значения
        |--------------------------------------------------------------------------
        */

        foreach (
            $products as &$product
        ) {

            $quantity =
                $product['quantity'];


            $product['average_sale_price'] =
                $quantity > 0
                    ? $product['sales']
                        / $quantity
                    : 0;


            $product['average_cost'] =
                $quantity > 0
                    ? $product['cost']
                        / $quantity
                    : 0;


            /*
            |--------------------------------------------------------------------------
            | Итоговая прибыль
            |--------------------------------------------------------------------------
            */

            $product['profit'] =
                $product['sales']
                - $product['cost'];
        }


        unset($product);


        return array_values(
            $products
        );
    }
}

