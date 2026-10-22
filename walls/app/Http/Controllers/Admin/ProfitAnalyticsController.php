<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PointOfSale;
use App\Models\StockMovement;
use App\Models\CustomerReturn;
use App\Services\FifoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfitAnalyticsController extends Controller
{
    private string $timezone = 'Asia/Almaty';

    public function index(Request $request, FifoService $fifoService)
    {
        /*
        |--------------------------------------------------------------------------
        | ПРОВЕРКА ДОСТУПА
        |--------------------------------------------------------------------------
        */

        if (
            !Auth::check() ||
            !Auth::user()->can_view_analytics
        ) {
            abort(403, 'Доступ к аналитике запрещён');
        }

        /*
        |--------------------------------------------------------------------------
        | ПЕРИОД
        |--------------------------------------------------------------------------
        */

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'), $this->timezone)->startOfDay()
            : now($this->timezone)->startOfMonth()->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'), $this->timezone)->endOfDay()
            : now($this->timezone)->endOfDay();

        if ($from->gt($to)) {
            return back()
                ->withInput()
                ->withErrors([
                    'to' => 'Дата окончания не может быть раньше даты начала.',
                ]);
        }

        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        /*
        |--------------------------------------------------------------------------
        | ТОЧКИ ПРОДАЖ
        |--------------------------------------------------------------------------
        */

        $pointsOfSale = PointOfSale::orderBy('name')->get();

        $selectedPointOfSale = $request->filled('point_of_sale_id')
            ? (int) $request->input('point_of_sale_id')
            : null;

        /*
        |--------------------------------------------------------------------------
        | ЗАКАЗЫ ЗА ПЕРИОД
        |--------------------------------------------------------------------------
        */

        $orders = Order::with([
            'items.variant.product',
        ])
            ->when($selectedPointOfSale, function ($query) use ($selectedPointOfSale) {
                $query->where('point_of_sale_id', $selectedPointOfSale);
            })
            ->get()
            ->filter(function ($order) use ($fromDate, $toDate) {
                $date = Carbon::parse(
                    $order->order_date ?? $order->created_at,
                    'Asia/Almaty'
                )->toDateString();

                return $date >= $fromDate && $date <= $toDate;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | ВОЗВРАТЫ ЗА ПЕРИОД
        |--------------------------------------------------------------------------
        |
        | Возврат определяется по return_date.
        | Если дата не заполнена, используем created_at.
        | Точка продаж берётся из исходного заказа.
        |
        */

        $returns = CustomerReturn::with([
            'order',
            'items.orderItem.variant.product',
            'items.variant.product',
            'items.originalAllocation.layer',
        ])
            ->when($selectedPointOfSale, function ($query) use ($selectedPointOfSale) {
                $query->whereHas('order', function ($orderQuery) use ($selectedPointOfSale) {
                    $orderQuery->where('point_of_sale_id', $selectedPointOfSale);
                });
            })
            ->get()
            ->filter(function ($return) use ($fromDate, $toDate) {
                $date = Carbon::parse(
                    $return->return_date ?? $return->created_at,
                    'Asia/Almaty'
                )->toDateString();

                return $date >= $fromDate && $date <= $toDate;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | FIFO-СЕБЕСТОИМОСТЬ ПРОДАННЫХ ТОВАРОВ
        |--------------------------------------------------------------------------
        |
        | StockMovement.source_id = OrderItem.id.
        |
        */

        $fifoMovements = StockMovement::with([
            'allocations.layer',
        ])
            ->where('type', 'sale')
            ->get();

        $fifoStats = [];

        foreach ($fifoMovements as $movement) {
            $orderItemId = (int) $movement->source_id;

            if ($orderItemId <= 0) {
                continue;
            }

            $movementCost = 0;

            foreach ($movement->allocations as $allocation) {
                $layer = $allocation->layer;

                if (!$layer) {
                    continue;
                }

                $unitCost = (float) $fifoService->getLayerUnitCost($layer);

                $movementCost +=
                    abs((int) $allocation->quantity) * $unitCost;
            }

            $fifoStats[$orderItemId] = [
                'quantity' => abs((int) $movement->quantity),
                'cost' => $movementCost,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | ИНИЦИАЛИЗАЦИЯ ПОКАЗАТЕЛЕЙ
        |--------------------------------------------------------------------------
        */

        $revenue = 0;
        $cost = 0;
        $soldQuantity = 0;
        $returnsQuantity = 0;
        $productStats = [];

        /*
        |--------------------------------------------------------------------------
        | ПОДГОТОВКА ДНЕВНОЙ СТАТИСТИКИ
        |--------------------------------------------------------------------------
        */

        $dailyStats = [];

        $date = $from->copy()->startOfDay();

        while ($date->toDateString() <= $toDate) {
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
        | ПРОДАЖИ
        |--------------------------------------------------------------------------
        */

        foreach ($orders as $order) {
            $orderDate = Carbon::parse(
                $order->order_date ?? $order->created_at,
                'Asia/Almaty'
            );

            $dayKey = $orderDate->toDateString();

            foreach ($order->items as $item) {
                $quantity = (int) ($item->quantity ?? 0);

                if ($quantity === 0) {
                    continue;
                }

                $salePrice = (float) ($item->price ?? 0);

                $variant = $item->variant;
                $product = $variant?->product;

                if (!$variant || !$product) {
                    continue;
                }

                $sku = $variant->sku ?? 'Без SKU';

                /*
                 * Старые отрицательные строки заказа:
                 * учитываем их только как вычет выручки.
                 *
                 * Новые возвраты учитываются отдельным блоком ниже.
                 */
                if ($quantity < 0) {
                    $returnedQuantity = abs($quantity);
                    $refundAmount = $returnedQuantity * $salePrice;

                    $revenue -= $refundAmount;
                    $returnsQuantity += $returnedQuantity;

                    if (isset($dailyStats[$dayKey])) {
                        $dailyStats[$dayKey]['revenue'] -= $refundAmount;
                    }

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

                    $productStats[$sku]['returns'] += $returnedQuantity;
                    $productStats[$sku]['revenue'] -= $refundAmount;

                    continue;
                }

                /*
                 * Обычная продажа.
                 */
                $itemRevenue = $quantity * $salePrice;
                $orderItemId = (int) $item->id;

                $itemCost = $fifoStats[$orderItemId]['cost'] ?? 0;

                $revenue += $itemRevenue;
                $cost += $itemCost;
                $soldQuantity += $quantity;

                if (isset($dailyStats[$dayKey])) {
                    $dailyStats[$dayKey]['revenue'] += $itemRevenue;
                    $dailyStats[$dayKey]['cost'] += $itemCost;
                }

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

                $productStats[$sku]['quantity'] += $quantity;
                $productStats[$sku]['revenue'] += $itemRevenue;
                $productStats[$sku]['cost'] += $itemCost;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ВОЗВРАЩЁННЫЕ ТОВАРЫ
        |--------------------------------------------------------------------------
        |
        | Для каждого возврата:
        | 1. Количество берём из CustomerReturnItem.
        | 2. Цена продажи берётся из исходного OrderItem.
        | 3. Себестоимость берётся из unit_cost.
        | 4. Выручка и себестоимость уменьшаются.
        |
        */

        foreach ($returns as $return) {
            $returnDate = Carbon::parse(
                $return->return_date ?? $return->created_at,
                'Asia/Almaty'
            );

            $dayKey = $returnDate->toDateString();

            foreach ($return->items as $returnItem) {
                $quantity = abs((int) ($returnItem->quantity ?? 0));

                if ($quantity === 0) {
                    continue;
                }

                $orderItem = $returnItem->orderItem;

                $variant = $returnItem->variant
                    ?? $orderItem?->variant;

                /*
                 * Не пропускаем возврат только из-за отсутствующей
                 * связи variant.product. Для SKU используем доступные данные.
                 */
                $sku = $variant?->sku
                    ?? $orderItem?->batch_code
                    ?? 'Без SKU';

                /*
                 * Цена продажи исходного заказа.
                 * unit_cost — это себестоимость, а не цена возврата.
                 */
                $salePrice = (float) ($orderItem?->price ?? 0);

                /*
                 * Себестоимость единицы возвращённого товара.
                 */
                if ($returnItem->unit_cost !== null) {
                    $unitCost = max(0, (float) $returnItem->unit_cost);
                } else {
                    $layer = $returnItem->originalAllocation?->layer;

                    $unitCost = $layer
                        ? max(0, (float) $fifoService->getLayerUnitCost($layer))
                        : 0;
                }

                $refundAmount = $quantity * $salePrice;
                $returnedCost = $quantity * $unitCost;

                /*
                 * Вычитаем возврат из общих сумм.
                 */
                $revenue -= $refundAmount;
                $cost -= $returnedCost;
                $returnsQuantity += $quantity;

                /*
                 * Отражаем возврат в день возврата.
                 */
                if (isset($dailyStats[$dayKey])) {
                    $dailyStats[$dayKey]['revenue'] -= $refundAmount;
                    $dailyStats[$dayKey]['cost'] -= $returnedCost;
                }

                /*
                 * Статистика по SKU.
                 */
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

                $productStats[$sku]['returns'] += $quantity;
                $productStats[$sku]['revenue'] -= $refundAmount;
                $productStats[$sku]['cost'] -= $returnedCost;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ПРИБЫЛЬ И МАРЖИНАЛЬНОСТЬ
        |--------------------------------------------------------------------------
        */

        $profit = $revenue - $cost;

        $margin = $revenue > 0
            ? ($profit / $revenue) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | ФИНАЛЬНЫЕ ЗНАЧЕНИЯ ПО ТОВАРАМ
        |--------------------------------------------------------------------------
        */

        foreach ($productStats as &$item) {
            $item['profit'] = $item['revenue'] - $item['cost'];
        }

        unset($item);

        $productStats = collect($productStats)
            ->sortByDesc('profit')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | ФИНАЛЬНЫЕ ЗНАЧЕНИЯ ПО ДНЯМ
        |--------------------------------------------------------------------------
        */

        foreach ($dailyStats as &$day) {
            $day['profit'] = $day['revenue'] - $day['cost'];
        }

        unset($day);

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
