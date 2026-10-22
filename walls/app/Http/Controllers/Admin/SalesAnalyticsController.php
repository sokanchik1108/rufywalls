<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use App\Models\StockMovement;
use App\Models\PointOfSale;
use App\Models\CustomerReturn;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalesAnalyticsController extends Controller
{
    private function checkAnalyticsAccess(): void
    {
        if (!Auth::check() || !Auth::user()->can_view_analytics) {
            abort(403, 'Доступ к аналитике запрещён');
        }
    }

    /**
     * Общая аналитика и аналитика конкретного SKU.
     */
    public function index(Request $request, ?string $sku = null)
    {
        $this->checkAnalyticsAccess();

        $searchSku = $sku ?? '';

        $from = $request->filled('from')
            ? Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay()
            : now()->endOfDay();

        $pointOfSaleId = $request->filled('point_of_sale_id')
            ? (int) $request->point_of_sale_id
            : null;

        $pointsOfSale = PointOfSale::orderBy('name')->get();

        $fifoService = app(FifoService::class);

        /*
        |--------------------------------------------------------------------------
        | Аналитика конкретного артикула
        |--------------------------------------------------------------------------
        */
        if ($sku !== null) {
            $variant = Variant::with('product')
                ->where('sku', $sku)
                ->firstOrFail();

            $items = OrderItem::with('variant.product')
                ->where('variant_id', $variant->id)
                ->where('quantity', '>', 0)
                ->get();

            $rows = [];

            $totalQuantity = 0;
            $totalSales = 0;
            $totalCost = 0;

            /*
            |--------------------------------------------------------------------------
            | Продажи
            |--------------------------------------------------------------------------
            */
            foreach ($items as $item) {
                $order = Order::find($item->order_id);

                if (!$order) {
                    continue;
                }

                if (
                    $pointOfSaleId !== null &&
                    (int) $order->point_of_sale_id !== $pointOfSaleId
                ) {
                    continue;
                }

                $orderDate = $order->order_date ?? $order->created_at;

                if (!$orderDate) {
                    continue;
                }

                $orderDate = Carbon::parse($orderDate);

                if ($orderDate->lt($from) || $orderDate->gt($to)) {
                    continue;
                }

                $movement = StockMovement::with('allocations.layer')
                    ->where('type', 'sale')
                    ->where('source_id', $item->id)
                    ->first();

                $cost = 0;

                if ($movement) {
                    foreach ($movement->allocations as $allocation) {
                        $layer = $allocation->layer;

                        if (!$layer) {
                            continue;
                        }

                        $unitCost = $fifoService->getLayerUnitCost($layer);

                        $cost += (int) $allocation->quantity * (float) $unitCost;
                    }
                }

                $quantity = (int) $item->quantity;
                $salePrice = (float) $item->price;
                $salesAmount = $quantity * $salePrice;

                $rows[] = [
                    'operation' => 'Продажа',
                    'return_id' => null,
                    'order_id' => $order->id,
                    'date' => $orderDate->format('d.m.Y'),
                    'time' => $orderDate->format('H:i'),
                    'quantity' => $quantity,
                    'sale_price' => $salePrice,
                    'average_cost' => $quantity > 0 ? $cost / $quantity : 0,
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
            | Возвраты по этому SKU
            |--------------------------------------------------------------------------
            */
            $returnOperations = $this->getReturnOperations(
                $from,
                $to,
                $pointOfSaleId,
                $fifoService,
                $variant->id
            );

            foreach ($returnOperations as $operation) {
                $returnItem = $operation['item'];
                $return = $operation['return'];
                $returnDate = $operation['date'];
                $order = $operation['order'];

                $quantity = abs((int) $returnItem->quantity);

                if ($quantity === 0) {
                    continue;
                }

                $orderItem = $returnItem->orderItem;
                $salePrice = (float) ($orderItem?->price ?? 0);

                $unitCost = $this->getReturnUnitCost(
                    $returnItem,
                    $fifoService,
                    $variant
                );

                $refundAmount = $quantity * $salePrice;
                $returnedCost = $quantity * $unitCost;

                $rows[] = [
                    'operation' => 'Возврат',
                    'return_id' => $return->id,
                    'order_id' => $order?->id ?? '—',
                    'date' => $returnDate->format('d.m.Y'),
                    'time' => $returnDate->format('H:i'),
                    'quantity' => -$quantity,
                    'sale_price' => $salePrice,
                    'average_cost' => $unitCost,
                    'sales_amount' => -$refundAmount,
                    'cost_amount' => -$returnedCost,
                    'profit' => -$refundAmount + $returnedCost,
                ];

                $totalQuantity -= $quantity;
                $totalSales -= $refundAmount;
                $totalCost -= $returnedCost;
            }

            /*
            |--------------------------------------------------------------------------
            | Объединяем строки одного возврата
            |--------------------------------------------------------------------------
            |
            | Для возвратов используем настоящий return_id.
            | Для продаж оставляем отдельные позиции заказа.
            | Несколько партий одного возврата объединяются в одну строку.
            |
            */
            $rows = collect($rows)
                ->groupBy(function ($row) {
                    if (($row['operation'] ?? '') === 'Возврат') {
                        return 'return|' . ($row['return_id'] ?? '');
                    }

                    return 'sale|' . ($row['order_id'] ?? '') . '|'
                        . ($row['date'] ?? '') . '|'
                        . ($row['time'] ?? '');
                })
                ->map(function ($group) {
                    $first = $group->first();

                    $quantity = $group->sum(
                        fn ($row) => (int) ($row['quantity'] ?? 0)
                    );

                    $absoluteQuantity = $group->sum(
                        fn ($row) => abs((int) ($row['quantity'] ?? 0))
                    );

                    $salesAmount = $group->sum(
                        fn ($row) => (float) ($row['sales_amount'] ?? 0)
                    );

                    $costAmount = $group->sum(
                        fn ($row) => (float) ($row['cost_amount'] ?? 0)
                    );

                    $profit = $group->sum(
                        fn ($row) => (float) ($row['profit'] ?? 0)
                    );

                    $salePrice = $absoluteQuantity > 0
                        ? $group->sum(function ($row) {
                            return abs((int) ($row['quantity'] ?? 0))
                                * (float) ($row['sale_price'] ?? 0);
                        }) / $absoluteQuantity
                        : 0;

                    $averageCost = $absoluteQuantity > 0
                        ? $group->sum(function ($row) {
                            return abs((int) ($row['quantity'] ?? 0))
                                * (float) ($row['average_cost'] ?? 0);
                        }) / $absoluteQuantity
                        : 0;

                    return array_merge($first, [
                        'quantity' => $quantity,
                        'sale_price' => $salePrice,
                        'average_cost' => $averageCost,
                        'sales_amount' => $salesAmount,
                        'cost_amount' => $costAmount,
                        'profit' => $profit,
                    ]);
                })
                ->sortByDesc(function ($row) {
                    return Carbon::createFromFormat(
                        'd.m.Y H:i',
                        $row['date'] . ' ' . $row['time']
                    )->timestamp;
                })
                ->values()
                ->all();

            /*
            |--------------------------------------------------------------------------
            | Итоговые показатели
            |--------------------------------------------------------------------------
            */
            $averageSalePrice = $totalQuantity > 0
                ? $totalSales / $totalQuantity
                : 0;

            $averageCost = $totalQuantity > 0
                ? $totalCost / $totalQuantity
                : 0;

            $profit = $totalSales - $totalCost;

            return view('admin.analytics.sales', compact(
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
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Общая аналитика по товарам
        |--------------------------------------------------------------------------
        */
        $products = $this->getProducts(
            $from,
            $to,
            $pointOfSaleId,
            $fifoService,
            ''
        );

        return view('admin.analytics.sales', compact(
            'from',
            'to',
            'products',
            'pointOfSaleId',
            'pointsOfSale',
            'searchSku'
        ));
    }

    /**
     * AJAX-таблица товаров.
     */
    public function productTable(Request $request)
    {
        $this->checkAnalyticsAccess();

        $from = $request->filled('from')
            ? Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay()
            : now()->endOfDay();

        $pointOfSaleId = $request->filled('point_of_sale_id')
            ? (int) $request->point_of_sale_id
            : null;

        $searchSku = trim((string) $request->input('sku', ''));

        $sortBy = $request->get('sort_by', 'sales');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSorts = [
            'quantity',
            'average_sale_price',
            'average_cost',
            'sales',
            'cost',
            'profit',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'sales';
        }

        if (!in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'desc';
        }

        $fifoService = app(FifoService::class);

        $products = $this->getProducts(
            $from,
            $to,
            $pointOfSaleId,
            $fifoService,
            $searchSku
        );

        usort($products, function ($a, $b) use ($sortBy, $sortDirection) {
            $valueA = (float) ($a[$sortBy] ?? 0);
            $valueB = (float) ($b[$sortBy] ?? 0);

            if ($valueA == $valueB) {
                $result = strnatcasecmp(
                    (string) ($a['sku'] ?? ''),
                    (string) ($b['sku'] ?? '')
                );
            } else {
                $result = $valueA <=> $valueB;
            }

            return $sortDirection === 'asc' ? $result : -$result;
        });

        return view(
            'admin.analytics.partials.sales-products-table',
            compact('products', 'sortBy', 'sortDirection')
        );
    }

    /**
     * Рассчитывает статистику продаж с учётом возвратов.
     */
    private function getProducts(
        Carbon $from,
        Carbon $to,
        ?int $pointOfSaleId,
        FifoService $fifoService,
        string $searchSku = ''
    ): array {
        $items = OrderItem::with('variant.product')
            ->where('quantity', '>', 0)
            ->get();

        $products = [];

        /*
        |--------------------------------------------------------------------------
        | Продажи
        |--------------------------------------------------------------------------
        */
        foreach ($items as $item) {
            $variant = $item->variant;

            if (!$variant) {
                continue;
            }

            if (
                $searchSku !== '' &&
                stripos((string) $variant->sku, $searchSku) === false
            ) {
                continue;
            }

            $order = Order::find($item->order_id);

            if (!$order) {
                continue;
            }

            if (
                $pointOfSaleId !== null &&
                (int) $order->point_of_sale_id !== $pointOfSaleId
            ) {
                continue;
            }

            $orderDate = $order->order_date ?? $order->created_at;

            if (!$orderDate) {
                continue;
            }

            $orderDate = Carbon::parse($orderDate);

            if ($orderDate->lt($from) || $orderDate->gt($to)) {
                continue;
            }

            $variantId = $variant->id;

            if (!isset($products[$variantId])) {
                $products[$variantId] = [
                    'variant_id' => $variantId,
                    'sku' => $variant->sku,
                    'name' => $variant->product?->name ?? '—',
                    'quantity' => 0,
                    'sales' => 0,
                    'cost' => 0,
                    'profit' => 0,
                    'average_sale_price' => 0,
                    'average_cost' => 0,
                ];
            }

            $quantity = (int) $item->quantity;
            $salePrice = (float) $item->price;
            $salesAmount = $quantity * $salePrice;

            $movement = StockMovement::with('allocations.layer')
                ->where('type', 'sale')
                ->where('source_id', $item->id)
                ->first();

            $cost = 0;

            if ($movement) {
                foreach ($movement->allocations as $allocation) {
                    $layer = $allocation->layer;

                    if (!$layer) {
                        continue;
                    }

                    $unitCost = $fifoService->getLayerUnitCost($layer);

                    $cost += (int) $allocation->quantity * (float) $unitCost;
                }
            }

            $products[$variantId]['quantity'] += $quantity;
            $products[$variantId]['sales'] += $salesAmount;
            $products[$variantId]['cost'] += $cost;
        }

        /*
        |--------------------------------------------------------------------------
        | Возвраты
        |--------------------------------------------------------------------------
        */
        $returnOperations = $this->getReturnOperations(
            $from,
            $to,
            $pointOfSaleId,
            $fifoService
        );

        foreach ($returnOperations as $operation) {
            $returnItem = $operation['item'];
            $variant = $operation['variant'];

            if (!$variant) {
                continue;
            }

            if (
                $searchSku !== '' &&
                stripos((string) $variant->sku, $searchSku) === false
            ) {
                continue;
            }

            $quantity = abs((int) $returnItem->quantity);

            if ($quantity === 0) {
                continue;
            }

            $variantId = $variant->id;

            if (!isset($products[$variantId])) {
                $products[$variantId] = [
                    'variant_id' => $variantId,
                    'sku' => $variant->sku,
                    'name' => $variant->product?->name ?? '—',
                    'quantity' => 0,
                    'sales' => 0,
                    'cost' => 0,
                    'profit' => 0,
                    'average_sale_price' => 0,
                    'average_cost' => 0,
                ];
            }

            $orderItem = $returnItem->orderItem;
            $salePrice = (float) ($orderItem?->price ?? 0);

            $unitCost = $this->getReturnUnitCost(
                $returnItem,
                $fifoService,
                $variant
            );

            $refundAmount = $quantity * $salePrice;
            $returnedCost = $quantity * $unitCost;

            $products[$variantId]['quantity'] -= $quantity;
            $products[$variantId]['sales'] -= $refundAmount;
            $products[$variantId]['cost'] -= $returnedCost;
        }

        /*
        |--------------------------------------------------------------------------
        | Средние показатели и прибыль
        |--------------------------------------------------------------------------
        */
        foreach ($products as &$product) {
            $quantity = $product['quantity'];

            $product['average_sale_price'] = $quantity > 0
                ? $product['sales'] / $quantity
                : 0;

            $product['average_cost'] = $quantity > 0
                ? $product['cost'] / $quantity
                : 0;

            $product['profit'] = $product['sales'] - $product['cost'];
        }

        unset($product);

        return array_values($products);
    }

    /**
     * Возвращает позиции возвратов за выбранный период.
     */
    private function getReturnOperations(
        Carbon $from,
        Carbon $to,
        ?int $pointOfSaleId,
        FifoService $fifoService,
        ?int $variantId = null
    ): array {
        $returns = CustomerReturn::with([
            'order',
            'items.orderItem.variant.product',
            'items.variant.product',
            'items.originalAllocation.layer',
        ])->get();

        $operations = [];

        foreach ($returns as $return) {
            $returnDateValue = $return->return_date ?? $return->created_at;

            if (!$returnDateValue) {
                continue;
            }

            $returnDate = Carbon::parse($returnDateValue);

            if ($returnDate->lt($from) || $returnDate->gt($to)) {
                continue;
            }

            $order = $return->order;

            if (
                $pointOfSaleId !== null &&
                (!$order || (int) $order->point_of_sale_id !== $pointOfSaleId)
            ) {
                continue;
            }

            foreach ($return->items as $returnItem) {
                $variant = $returnItem->variant
                    ?? $returnItem->orderItem?->variant;

                if (!$variant) {
                    continue;
                }

                if (
                    $variantId !== null &&
                    (int) $variant->id !== $variantId
                ) {
                    continue;
                }

                $operations[] = [
                    'item' => $returnItem,
                    'return' => $return,
                    'order' => $order,
                    'variant' => $variant,
                    'date' => $returnDate,
                ];
            }
        }

        return $operations;
    }

    /**
     * Получает себестоимость возвращённого товара.
     */
    private function getReturnUnitCost(
        $returnItem,
        FifoService $fifoService,
        Variant $variant
    ): float {
        if ($returnItem->unit_cost !== null) {
            return (float) $returnItem->unit_cost;
        }

        $allocation = $returnItem->originalAllocation;

        if ($allocation) {
            if ($allocation->unit_cost !== null) {
                return (float) $allocation->unit_cost;
            }

            if ($allocation->layer) {
                return (float) $fifoService->getLayerUnitCost(
                    $allocation->layer
                );
            }
        }

        return (float) ($variant->product?->purchase_price ?? 0);
    }
}
