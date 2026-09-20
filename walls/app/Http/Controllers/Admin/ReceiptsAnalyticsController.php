<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Variant;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReceiptsAnalyticsController extends Controller
{
    /**
     * Главная страница аналитики приёмок.
     */
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $selectedWarehouse = $request->filled('warehouse_id')
            ? (int) $request->warehouse_id
            : null;

        $sort = $request->get('sort', 'desc');

        if (!in_array($sort, ['asc', 'desc'], true)) {
            $sort = 'desc';
        }

        /*
        |--------------------------------------------------------------------------
        | Приёмки
        |--------------------------------------------------------------------------
        */

        $receiptsQuery = Receipt::with([
            'items.variant.product',
            'warehouse',
        ])
            ->whereDate(
                'receipt_date',
                '>=',
                $from->toDateString()
            )
            ->whereDate(
                'receipt_date',
                '<=',
                $to->toDateString()
            );

        if ($selectedWarehouse) {
            $receiptsQuery->where(
                'warehouse_id',
                $selectedWarehouse
            );
        }

        $receipts = $receiptsQuery
            ->orderBy('receipt_date')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Общие показатели
        |--------------------------------------------------------------------------
        */

        $totalReceipts = $receipts->count();

        $totalQuantity = 0;

        foreach ($receipts as $receipt) {
            foreach ($receipt->items as $item) {
                $totalQuantity += (int) $item->quantity;
            }
        }

        $averageQuantityPerReceipt = $totalReceipts > 0
            ? $totalQuantity / $totalReceipts
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Количество дней с приёмками
        |--------------------------------------------------------------------------
        */

        $receiptDays = $receipts
            ->map(function ($receipt) {
                return Carbon::parse(
                    $receipt->receipt_date
                )->format('Y-m-d');
            })
            ->unique()
            ->values();

        $receiptDaysCount = $receiptDays->count();

        /*
        |--------------------------------------------------------------------------
        | Статистика по товарам
        |--------------------------------------------------------------------------
        */

        $productStats = [];

        foreach ($receipts as $receipt) {

            foreach ($receipt->items as $item) {

                $variantId = $item->variant_id;

                if (!isset($productStats[$variantId])) {

                    $productStats[$variantId] = [
                        'variant_id' => $variantId,

                        'sku' => $item->variant?->sku
                            ?? '—',

                        'product' => $item->variant?->product?->name
                            ?? '—',

                        'receipts' => 0,

                        'receipt_ids' => [],

                        'quantity' => 0,

                        'average_quantity' => 0,
                    ];
                }

                $productStats[$variantId]['quantity']
                    += (int) $item->quantity;

                /*
                 * Запоминаем ID приёмки.
                 * Потом сделаем unique(), чтобы одна приёмка
                 * считалась только один раз.
                 */
                $productStats[$variantId]['receipt_ids'][]
                    = $receipt->id;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Рассчитываем количество приёмок
        | и средний завоз
        |--------------------------------------------------------------------------
        */

        foreach ($productStats as &$stat) {

            $stat['receipts'] = count(
                array_unique(
                    $stat['receipt_ids']
                )
            );

            $stat['average_quantity'] =
                $stat['receipts'] > 0
                    ? $stat['quantity']
                        / $stat['receipts']
                    : 0;

            unset($stat['receipt_ids']);
        }

        unset($stat);

        $productStats = collect(
            $productStats
        );

        /*
        |--------------------------------------------------------------------------
        | Сортировка
        |--------------------------------------------------------------------------
        */

        if ($sort === 'asc') {

            $productStats = $productStats
                ->sortBy('quantity')
                ->values();

        } else {

            $productStats = $productStats
                ->sortByDesc('quantity')
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | Топ-10 по среднему завозу
        |--------------------------------------------------------------------------
        */

        $topProducts = collect($productStats)
            ->sortByDesc('average_quantity')
            ->take(10)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Статистика по дням
        |--------------------------------------------------------------------------
        */

        $dailyStats = [];

        $currentDate = $from->copy()->startOfDay();

        while ($currentDate->lte($to)) {

            $date = $currentDate->format('Y-m-d');

            $dayReceipts = $receipts->filter(
                function ($receipt) use ($date) {

                    return Carbon::parse(
                        $receipt->receipt_date
                    )->format('Y-m-d') === $date;
                }
            );

            $dayQuantity = 0;

            foreach ($dayReceipts as $receipt) {

                foreach ($receipt->items as $item) {

                    $dayQuantity +=
                        (int) $item->quantity;
                }
            }

            $dailyStats[] = [
                'date' => $date,
                'receipts' => $dayReceipts->count(),
                'quantity' => $dayQuantity,
            ];

            $currentDate->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Склады
        |--------------------------------------------------------------------------
        */

        $warehouses = Warehouse::orderBy(
            'name'
        )->get();

        return view(
            'admin.analytics.receipts',
            compact(
                'from',
                'to',
                'selectedWarehouse',
                'sort',
                'totalReceipts',
                'totalQuantity',
                'averageQuantityPerReceipt',
                'receiptDaysCount',
                'productStats',
                'topProducts',
                'dailyStats',
                'warehouses'
            )
        );
    }


    /**
     * Полная аналитика товаров по приёмкам.
     */
    public function product(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $search = trim(
            (string) $request->get(
                'search',
                ''
            )
        );

        $sortBy = $request->get(
            'sort_by',
            'sku'
        );

        $allowedSorts = [
            'sku',
            'receipts',
            'quantity',
            'average_quantity',
        ];

        if (!in_array(
            $sortBy,
            $allowedSorts,
            true
        )) {
            $sortBy = 'sku';
        }

        $sortDirection = $request->get(
            'sort_direction',
            'asc'
        );

        if (!in_array(
            $sortDirection,
            ['asc', 'desc'],
            true
        )) {
            $sortDirection = 'asc';
        }

        $variantId = $request->filled(
            'variant_id'
        )
            ? (int) $request->variant_id
            : null;

        $selectedWarehouse = $request->filled(
            'warehouse_id'
        )
            ? (int) $request->warehouse_id
            : null;

        /*
        |--------------------------------------------------------------------------
        | Склады
        |--------------------------------------------------------------------------
        */

        $warehouses = Warehouse::orderBy(
            'name'
        )->get();

        /*
        |--------------------------------------------------------------------------
        | Все позиции приёмок
        |--------------------------------------------------------------------------
        */

        $receiptItemsQuery = ReceiptItem::with([
            'receipt.warehouse',
            'variant.product',
            'batch',
        ])
            ->whereHas(
                'receipt',
                function ($q) use (
                    $from,
                    $to,
                    $selectedWarehouse
                ) {

                    $q->whereDate(
                        'receipt_date',
                        '>=',
                        $from->toDateString()
                    )
                    ->whereDate(
                        'receipt_date',
                        '<=',
                        $to->toDateString()
                    );

                    if ($selectedWarehouse) {

                        $q->where(
                            'warehouse_id',
                            $selectedWarehouse
                        );
                    }
                }
            );

        $allReceiptItems =
            $receiptItemsQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Формируем статистику товаров
        |--------------------------------------------------------------------------
        */

        $allProductStats = [];

        foreach (
            $allReceiptItems as $item
        ) {

            $id = $item->variant_id;

            if (!isset(
                $allProductStats[$id]
            )) {

                $allProductStats[$id] = [

                    'variant_id' => $id,

                    'sku' => $item->variant?->sku
                        ?? '—',

                    'product' =>
                        $item->variant?->product?->name
                        ?? '—',

                    'receipts' => 0,

                    'receipt_ids' => [],

                    'quantity' => 0,

                    'total_cost' => 0,

                    'average_quantity' => 0,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Количество
            |--------------------------------------------------------------------------
            */

            $allProductStats[$id]['quantity']
                += (int) $item->quantity;

            /*
            |--------------------------------------------------------------------------
            | Себестоимость
            |--------------------------------------------------------------------------
            */

            $allProductStats[$id]['total_cost']
                +=
                (float) $item->quantity
                *
                (float) $item->purchase_price;

            /*
            |--------------------------------------------------------------------------
            | ID приёмки
            |--------------------------------------------------------------------------
            */

            $allProductStats[$id]['receipt_ids'][]
                = $item->receipt_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Уникальные приёмки + средний завоз
        |--------------------------------------------------------------------------
        */

        foreach (
            $allProductStats as &$stat
        ) {

            $stat['receipts'] = count(
                array_unique(
                    $stat['receipt_ids']
                )
            );

            $stat['average_quantity'] =
                $stat['receipts'] > 0
                    ? $stat['quantity']
                        / $stat['receipts']
                    : 0;

            unset(
                $stat['receipt_ids']
            );
        }

        unset($stat);

        $allProductStats = collect(
            $allProductStats
        );

        /*
        |--------------------------------------------------------------------------
        | Поиск
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $searchLower =
                mb_strtolower($search);

            $allProductStats =
                $allProductStats->filter(
                    function ($product)
                    use ($searchLower) {

                        $sku = mb_strtolower(
                            (string)
                            $product['sku']
                        );

                        $name = mb_strtolower(
                            (string)
                            $product['product']
                        );

                        return
                            str_contains(
                                $sku,
                                $searchLower
                            )
                            ||
                            str_contains(
                                $name,
                                $searchLower
                            );
                    }
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Сортировка
        |--------------------------------------------------------------------------
        */

        if ($sortBy === 'sku') {

            if ($sortDirection === 'asc') {

                $allProductStats =
                    $allProductStats->sortBy(
                        'sku',
                        SORT_NATURAL
                        | SORT_FLAG_CASE
                    );

            } else {

                $allProductStats =
                    $allProductStats->sortByDesc(
                        'sku',
                        SORT_NATURAL
                        | SORT_FLAG_CASE
                    );
            }

        } else {

            if ($sortDirection === 'asc') {

                $allProductStats =
                    $allProductStats->sortBy(
                        $sortBy
                    );

            } else {

                $allProductStats =
                    $allProductStats->sortByDesc(
                        $sortBy
                    );
            }
        }

        $allProductStats =
            $allProductStats->values();

        /*
        |--------------------------------------------------------------------------
        | Конкретный товар
        |--------------------------------------------------------------------------
        */

        $selectedVariant = null;

        $receiptItems = collect();

        $totalQuantity = 0;

        $totalReceiptCount = 0;

        $totalCost = 0;

        $averagePurchasePrice = 0;

        if ($variantId) {

            $selectedVariant =
                Variant::with('product')
                    ->find($variantId);

            $receiptItems =
                $allReceiptItems
                    ->where(
                        'variant_id',
                        $variantId
                    )
                    ->sortBy(
                        function ($item) {

                            return [

                                optional(
                                    $item->receipt
                                )
                                    ->receipt_date
                                    ?->format(
                                        'Y-m-d'
                                    ),

                                $item->receipt_id,

                                $item->id,
                            ];
                        }
                    )
                    ->values();

            /*
            |--------------------------------------------------------------------------
            | Общее количество
            |--------------------------------------------------------------------------
            */

            $totalQuantity =
                $receiptItems->sum(
                    function ($item) {

                        return (int)
                            $item->quantity;
                    }
                );

            /*
            |--------------------------------------------------------------------------
            | Количество приёмок
            |--------------------------------------------------------------------------
            */

            $totalReceiptCount =
                $receiptItems
                    ->pluck(
                        'receipt_id'
                    )
                    ->unique()
                    ->count();

            /*
            |--------------------------------------------------------------------------
            | Общая стоимость
            |--------------------------------------------------------------------------
            */

            $totalCost =
                $receiptItems->sum(
                    function ($item) {

                        return
                            (float)
                            $item->quantity
                            *
                            (float)
                            $item->purchase_price;
                    }
                );

            /*
            |--------------------------------------------------------------------------
            | Средняя цена закупки
            |--------------------------------------------------------------------------
            */

            $averagePurchasePrice =
                $totalQuantity > 0
                    ? $totalCost
                        / $totalQuantity
                    : 0;
        }

        return view(
            'admin.analytics.receipt-product',
            compact(
                'from',
                'to',
                'search',
                'sortBy',
                'sortDirection',
                'variantId',
                'selectedVariant',
                'selectedWarehouse',
                'warehouses',
                'allProductStats',
                'receiptItems',
                'totalQuantity',
                'totalReceiptCount',
                'totalCost',
                'averagePurchasePrice'
            )
        );
    }


    /**
     * AJAX: только таблица товаров.
     */
    public function productTable(
        Request $request
    ) {
        $from = $request->filled('from')
            ? Carbon::parse(
                $request->from
            )->startOfDay()
            : now()
                ->startOfMonth()
                ->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse(
                $request->to
            )->endOfDay()
            : now()->endOfDay();

        $search = trim(
            (string) $request->get(
                'search',
                ''
            )
        );

        $sortBy = $request->get(
            'sort_by',
            'sku'
        );

        $allowedSorts = [
            'sku',
            'receipts',
            'quantity',
            'average_quantity',
        ];

        if (!in_array(
            $sortBy,
            $allowedSorts,
            true
        )) {
            $sortBy = 'sku';
        }

        $sortDirection =
            $request->get(
                'sort_direction',
                'asc'
            );

        if (!in_array(
            $sortDirection,
            ['asc', 'desc'],
            true
        )) {
            $sortDirection = 'asc';
        }

        $selectedWarehouse =
            $request->filled(
                'warehouse_id'
            )
                ? (int)
                    $request->warehouse_id
                : null;

        /*
        |--------------------------------------------------------------------------
        | Получаем позиции приёмок
        |--------------------------------------------------------------------------
        */

        $receiptItemsQuery =
            ReceiptItem::with([
                'receipt.warehouse',
                'variant.product',
                'batch',
            ])
                ->whereHas(
                    'receipt',
                    function ($q)
                    use (
                        $from,
                        $to,
                        $selectedWarehouse
                    ) {

                        $q->whereDate(
                            'receipt_date',
                            '>=',
                            $from->toDateString()
                        )
                        ->whereDate(
                            'receipt_date',
                            '<=',
                            $to->toDateString()
                        );

                        if (
                            $selectedWarehouse
                        ) {

                            $q->where(
                                'warehouse_id',
                                $selectedWarehouse
                            );
                        }
                    }
                );

        $allReceiptItems =
            $receiptItemsQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Статистика
        |--------------------------------------------------------------------------
        */

        $allProductStats = [];

        foreach (
            $allReceiptItems as $item
        ) {

            $id = $item->variant_id;

            if (!isset(
                $allProductStats[$id]
            )) {

                $allProductStats[$id] = [

                    'variant_id' => $id,

                    'sku' =>
                        $item->variant?->sku
                        ?? '—',

                    'product' =>
                        $item->variant?->product?->name
                        ?? '—',

                    'receipts' => 0,

                    'receipt_ids' => [],

                    'quantity' => 0,

                    'total_cost' => 0,

                    'average_quantity' => 0,
                ];
            }

            $allProductStats[$id]['quantity']
                += (int)
                    $item->quantity;

            $allProductStats[$id]['total_cost']
                +=
                (float)
                    $item->quantity
                *
                (float)
                    $item->purchase_price;

            $allProductStats[$id]['receipt_ids'][]
                = $item->receipt_id;
        }

        foreach (
            $allProductStats as &$stat
        ) {

            $stat['receipts'] = count(
                array_unique(
                    $stat['receipt_ids']
                )
            );

            $stat['average_quantity'] =
                $stat['receipts'] > 0
                    ? $stat['quantity']
                        / $stat['receipts']
                    : 0;

            unset(
                $stat['receipt_ids']
            );
        }

        unset($stat);

        $allProductStats =
            collect($allProductStats);

        /*
        |--------------------------------------------------------------------------
        | Поиск
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $searchLower =
                mb_strtolower($search);

            $allProductStats =
                $allProductStats->filter(
                    function ($product)
                    use ($searchLower) {

                        $sku =
                            mb_strtolower(
                                (string)
                                $product['sku']
                            );

                        $name =
                            mb_strtolower(
                                (string)
                                $product['product']
                            );

                        return
                            str_contains(
                                $sku,
                                $searchLower
                            )
                            ||
                            str_contains(
                                $name,
                                $searchLower
                            );
                    }
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Сортировка
        |--------------------------------------------------------------------------
        */

        if ($sortBy === 'sku') {

            $allProductStats =
                $sortDirection === 'asc'

                    ? $allProductStats->sortBy(
                        'sku',
                        SORT_NATURAL
                        | SORT_FLAG_CASE
                    )

                    : $allProductStats->sortByDesc(
                        'sku',
                        SORT_NATURAL
                        | SORT_FLAG_CASE
                    );

        } else {

            $allProductStats =
                $sortDirection === 'asc'

                    ? $allProductStats->sortBy(
                        $sortBy
                    )

                    : $allProductStats->sortByDesc(
                        $sortBy
                    );
        }

        $allProductStats =
            $allProductStats->values();

        /*
        |--------------------------------------------------------------------------
        | Возвращаем только таблицу
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.analytics.partials.receipt-product-table',
            compact(
                'allProductStats',
                'sortBy',
                'sortDirection'
            )
        );
    }
}