<?php

namespace App\Http\Controllers;

use App\Models\CustomerReturn;
use App\Models\CustomerReturnItem;
use App\Models\CustomerReturnPayment;
use App\Models\FifoAllocation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use App\Models\InventoryLayer;
use App\Models\PaymentMethod;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CustomerReturnController extends Controller
{
    /**
     * Форма создания возврата.
     */
    public function create(Order $order)
    {
        $order->load(['items.variant.product', 'pointOfSale']);

        $rows = [];

        foreach ($order->items as $orderItem) {
            $movements = StockMovement::query()
                ->where('type', 'sale')
                ->where('source_id', $orderItem->id)
                ->with([
                    'allocations.layer.batch',
                    'allocations.layer.warehouse',
                ])
                ->get();

            $allocations = $movements
                ->flatMap(fn ($movement) => $movement->allocations)
                ->filter(
                    fn ($allocation) =>
                        $allocation->layer && $allocation->layer->batch
                )
                ->sortByDesc('id')
                ->values();

            $groups = $allocations->groupBy(function ($allocation) {
                $layer = $allocation->layer;

                return implode('|', [
                    $layer->variant_id,
                    $layer->batch_id,
                    $layer->warehouse_id,
                ]);
            });

            foreach ($groups as $group) {
                $soldQuantity = 0;
                $returnedQuantity = 0;
                $availableQuantity = 0;
                $selectedAllocation = null;

                $group = $group->sortByDesc('id')->values();

                foreach ($group as $allocation) {
                    $sold = max(0, (int) $allocation->quantity);

                    $returned = (int) CustomerReturnItem::query()
                        ->where(
                            'original_fifo_allocation_id',
                            $allocation->id
                        )
                        ->sum('quantity');

                    $available = max(0, $sold - $returned);

                    $soldQuantity += $sold;
                    $returnedQuantity += $returned;
                    $availableQuantity += $available;

                    if ($available > 0 && $selectedAllocation === null) {
                        $selectedAllocation = $allocation;
                    }
                }

                if ($availableQuantity <= 0 || !$selectedAllocation) {
                    continue;
                }

                $layer = $selectedAllocation->layer;

                $rows[] = [
                    'order_item' => $orderItem,
                    'allocation' => $selectedAllocation,
                    'layer' => $layer,
                    'batch' => $layer->batch,
                    'warehouse' => $layer->warehouse,
                    'sold_quantity' => $soldQuantity,
                    'returned_quantity' => $returnedQuantity,
                    'available_quantity' => $availableQuantity,
                    'unit_cost' => $this->allocationUnitCost(
                        $selectedAllocation,
                        $layer
                    ),
                ];
            }
        }

        $paymentMethods = PaymentMethod::orderBy('name')->get();

        $orderItemsTotal = (float) $order->items->sum(
            fn ($item) => (float) $item->price * (int) $item->quantity
        );

        $returnState = $this->getOrderReturnState($order);

        $orderNetTotal = max(
            0,
            round($orderItemsTotal - (float) ($order->discount ?? 0), 2)
        );

        $fullOrderRefundAmount = max(
            0,
            round(
                $orderNetTotal - $returnState['previous_refund_total'],
                2
            )
        );

        return view(
            'admin.orders.returns.create',
            compact(
                'order',
                'rows',
                'paymentMethods',
                'fullOrderRefundAmount'
            )
        );
    }

    /**
     * Создание возврата товаров и денег.
     *
     * Частичный возврат:
     * возвращаем полную стоимость возвращаемых товаров,
     * но не больше оставшейся суммы, доступной к возврату.
     *
     * Последний возврат:
     * возвращаем остаток суммы заказа после скидки
     * за вычетом ранее возвращённых денег.
     */
    public function store(Request $request, Order $order)
    {
        if ($request->has('payments')) {
            $payments = $request->input('payments', []);

            foreach ($payments as $index => $payment) {
                if (isset($payment['amount']) && is_string($payment['amount'])) {
                    $payments[$index]['amount'] = str_replace(
                        ',',
                        '.',
                        str_replace(' ', '', trim($payment['amount']))
                    );
                }
            }

            $request->merge(['payments' => $payments]);
        }

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0'],
            'comment' => ['nullable', 'string', 'max:5000'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.payment_method_id' => [
                'required',
                'integer',
                'distinct',
                'exists:payment_methods,id',
            ],
            'payments.*.amount' => ['required', 'numeric', 'gt:0'],
        ], [
            'items.required' => 'Выбери товары для возврата.',
            'items.*.integer' => 'Количество должно быть целым числом.',
            'items.*.min' => 'Количество не может быть отрицательным.',
            'payments.required' => 'Добавь способ возврата денег.',
            'payments.min' => 'Добавь хотя бы один способ возврата денег.',
            'payments.*.payment_method_id.required' => 'Выбери способ оплаты.',
            'payments.*.payment_method_id.exists' => 'Способ оплаты не существует.',
            'payments.*.payment_method_id.distinct' =>
                'Один способ оплаты нельзя добавлять несколько раз.',
            'payments.*.amount.required' => 'Укажи сумму возврата.',
            'payments.*.amount.numeric' => 'Сумма должна быть числом.',
            'payments.*.amount.gt' => 'Сумма должна быть больше нуля.',
        ]);

        $requestedItems = collect($validated['items'])
            ->mapWithKeys(fn ($quantity, $id) => [
                (int) $id => (int) ($quantity ?? 0),
            ])
            ->filter(fn ($quantity) => $quantity > 0);

        if ($requestedItems->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Укажи количество хотя бы для одного товара.',
            ]);
        }

        $orderItems = OrderItem::query()
            ->where('order_id', $order->id)
            ->get()
            ->keyBy('id');

        $allocationsForCalculation = FifoAllocation::query()
            ->whereIn('id', $requestedItems->keys())
            ->with('movement')
            ->get()
            ->keyBy('id');

        $itemsTotal = 0;
        $requestedByOrderItem = [];

        foreach ($requestedItems as $allocationId => $quantity) {
            $allocation = $allocationsForCalculation->get($allocationId);

            if (!$allocation || !$allocation->movement) {
                throw ValidationException::withMessages([
                    'items' => 'Не удалось определить позицию возврата.',
                ]);
            }

            $orderItem = $orderItems->get(
                $allocation->movement->source_id
            );

            if (!$orderItem) {
                throw ValidationException::withMessages([
                    'items' => 'Позиция не относится к этому заказу.',
                ]);
            }

            $itemsTotal += (float) $orderItem->price * $quantity;

            $orderItemId = (int) $orderItem->id;

            $requestedByOrderItem[$orderItemId] =
                ($requestedByOrderItem[$orderItemId] ?? 0) + $quantity;
        }

        $originalItemsTotal = (float) $orderItems->sum(
            fn ($item) => (float) $item->price * (int) $item->quantity
        );

        $orderNetTotal = max(
            0,
            round($originalItemsTotal - (float) ($order->discount ?? 0), 2)
        );

        $returnState = $this->getOrderReturnState($order);

        $completesOrder = $this->doesReturnCompleteOrder(
            $orderItems,
            $returnState['returned_quantities'],
            $requestedByOrderItem
        );

        $remainingRefundable = max(
            0,
            round(
                $orderNetTotal - $returnState['previous_refund_total'],
                2
            )
        );

        $expectedRefund = $completesOrder
            ? $remainingRefundable
            : min(round($itemsTotal, 2), $remainingRefundable);

        $expectedRefund = round($expectedRefund, 2);

        if ($expectedRefund <= 0) {
            throw ValidationException::withMessages([
                'payments' => 'По этому заказу больше нет суммы, доступной к возврату.',
            ]);
        }

        $paymentsTotal = round(
            collect($validated['payments'])
                ->sum(fn ($payment) => (float) $payment['amount']),
            2
        );

        if (abs($paymentsTotal - $expectedRefund) > 0.01) {
            throw ValidationException::withMessages([
                'payments' => sprintf(
                    'Сумма платежей %.2f ₸ должна совпадать с суммой возврата %.2f ₸.',
                    $paymentsTotal,
                    $expectedRefund
                ),
            ]);
        }

        $customerReturn = DB::transaction(function () use (
            $order,
            $requestedItems,
            $validated,
            $expectedRefund
        ) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $orderItems = OrderItem::query()
                ->where('order_id', $lockedOrder->id)
                ->get()
                ->keyBy('id');

            if ($orderItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'В заказе не найдены товары.',
                ]);
            }

            $allocations = FifoAllocation::query()
                ->whereIn('stock_movement_id', function ($query) use ($orderItems) {
                    $query->select('id')
                        ->from('stock_movements')
                        ->where('type', 'sale')
                        ->whereIn('source_id', $orderItems->keys());
                })
                ->with(['movement', 'layer.batch', 'layer.warehouse'])
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $groups = [];

            foreach ($allocations as $allocation) {
                $movement = $allocation->movement;
                $layer = $allocation->layer;

                if (
                    !$movement ||
                    !$layer ||
                    !$layer->batch ||
                    $movement->type !== 'sale'
                ) {
                    continue;
                }

                $key = $this->allocationGroupKey(
                    (int) $movement->source_id,
                    $layer
                );

                $groups[$key][] = $allocation;
            }

            foreach ($groups as &$groupAllocations) {
                usort(
                    $groupAllocations,
                    fn ($a, $b) => $b->id <=> $a->id
                );
            }
            unset($groupAllocations);

            $prepared = [];
            $processedGroups = [];

            foreach ($requestedItems as $allocationId => $quantity) {
                $selectedAllocation = $allocations->get($allocationId);

                if (!$selectedAllocation) {
                    throw ValidationException::withMessages([
                        'items' => 'Позиция не относится к этому заказу.',
                    ]);
                }

                $movement = $selectedAllocation->movement;
                $layer = $selectedAllocation->layer;

                if (
                    !$movement ||
                    !$layer ||
                    !$layer->batch ||
                    $movement->type !== 'sale'
                ) {
                    throw ValidationException::withMessages([
                        'items' => 'Не удалось определить исходную партию.',
                    ]);
                }

                $orderItem = $orderItems->get($movement->source_id);

                if (!$orderItem) {
                    throw ValidationException::withMessages([
                        'items' => 'Не найдена позиция исходного заказа.',
                    ]);
                }

                $groupKey = $this->allocationGroupKey(
                    (int) $movement->source_id,
                    $layer
                );

                if (isset($processedGroups[$groupKey])) {
                    throw ValidationException::withMessages([
                        'items' => 'Группа товара передана повторно. Обнови страницу.',
                    ]);
                }

                $processedGroups[$groupKey] = true;
                $groupAllocations = $groups[$groupKey] ?? [];

                $canonicalAllocation = null;
                $groupAvailable = 0;

                foreach ($groupAllocations as $groupAllocation) {
                    $alreadyReturned = (int) CustomerReturnItem::query()
                        ->where(
                            'original_fifo_allocation_id',
                            $groupAllocation->id
                        )
                        ->sum('quantity');

                    $available = max(
                        0,
                        (int) $groupAllocation->quantity - $alreadyReturned
                    );

                    $groupAvailable += $available;

                    if ($available > 0 && $canonicalAllocation === null) {
                        $canonicalAllocation = $groupAllocation;
                    }
                }

                if (
                    !$canonicalAllocation ||
                    (int) $canonicalAllocation->id !== (int) $allocationId
                ) {
                    throw ValidationException::withMessages([
                        'items' => 'FIFO-списания изменились. Обнови страницу.',
                    ]);
                }

                if ($quantity > $groupAvailable) {
                    throw ValidationException::withMessages([
                        'items' => sprintf(
                            'Доступно вернуть %d шт., запрошено %d шт.',
                            $groupAvailable,
                            $quantity
                        ),
                    ]);
                }

                $remaining = (int) $quantity;

                foreach ($groupAllocations as $groupAllocation) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $allocationLayer = $groupAllocation->layer;

                    if (!$allocationLayer || !$allocationLayer->batch) {
                        continue;
                    }

                    $alreadyReturned = (int) CustomerReturnItem::query()
                        ->where(
                            'original_fifo_allocation_id',
                            $groupAllocation->id
                        )
                        ->sum('quantity');

                    $available = max(
                        0,
                        (int) $groupAllocation->quantity - $alreadyReturned
                    );

                    if ($available <= 0) {
                        continue;
                    }

                    $quantityForAllocation = min($remaining, $available);

                    $prepared[] = [
                        'allocation' => $groupAllocation,
                        'order_item' => $orderItem,
                        'layer' => $allocationLayer,
                        'quantity' => $quantityForAllocation,
                        'unit_cost' => $this->allocationUnitCost(
                            $groupAllocation,
                            $allocationLayer
                        ),
                    ];

                    $remaining -= $quantityForAllocation;
                }

                if ($remaining > 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Не удалось распределить количество по FIFO.',
                    ]);
                }
            }

            $actualItemsTotal = 0;
            $actualRequestedByOrderItem = [];

            foreach ($prepared as $item) {
                $orderItemId = (int) $item['order_item']->id;
                $quantity = (int) $item['quantity'];

                $actualItemsTotal +=
                    (float) $item['order_item']->price * $quantity;

                $actualRequestedByOrderItem[$orderItemId] =
                    ($actualRequestedByOrderItem[$orderItemId] ?? 0) + $quantity;
            }

            $originalItemsTotal = (float) $orderItems->sum(
                fn ($item) => (float) $item->price * (int) $item->quantity
            );

            $orderNetTotal = max(
                0,
                round(
                    $originalItemsTotal - (float) ($lockedOrder->discount ?? 0),
                    2
                )
            );

            $returnState = $this->getOrderReturnState($lockedOrder);

            $completesOrder = $this->doesReturnCompleteOrder(
                $orderItems,
                $returnState['returned_quantities'],
                $actualRequestedByOrderItem
            );

            $remainingRefundable = max(
                0,
                round(
                    $orderNetTotal - $returnState['previous_refund_total'],
                    2
                )
            );

            $actualRefund = $completesOrder
                ? $remainingRefundable
                : min(round($actualItemsTotal, 2), $remainingRefundable);

            $actualRefund = round($actualRefund, 2);

            if (
                $actualRefund <= 0 ||
                abs($actualRefund - $expectedRefund) > 0.01
            ) {
                throw ValidationException::withMessages([
                    'items' => 'Сумма возврата изменилась. Обнови страницу и проверь сумму.',
                ]);
            }

            $returnDate = now('Asia/Almaty');

            $customerReturn = CustomerReturn::create([
                'order_id' => $lockedOrder->id,
                'user_id' => auth()->id(),
                'return_date' => $returnDate,
                'comment' => $validated['comment'] ?? null,
            ]);

            foreach ($validated['payments'] as $payment) {
                CustomerReturnPayment::create([
                    'customer_return_id' => $customerReturn->id,
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount' => $payment['amount'],
                ]);
            }

            $fifoService = app(FifoService::class);

            foreach ($prepared as $preparedItem) {
                $allocation = $preparedItem['allocation'];
                $orderItem = $preparedItem['order_item'];
                $layer = $preparedItem['layer'];
                $quantity = (int) $preparedItem['quantity'];
                $unitCost = (float) $preparedItem['unit_cost'];

                $returnItem = CustomerReturnItem::create([
                    'customer_return_id' => $customerReturn->id,
                    'order_item_id' => $orderItem->id,
                    'original_fifo_allocation_id' => $allocation->id,
                    'warehouse_id' => $layer->warehouse_id,
                    'variant_id' => $layer->variant_id,
                    'batch_id' => $layer->batch_id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                ]);

                $fifoService->changeWarehouseQuantity(
                    $layer->batch,
                    (int) $layer->warehouse_id,
                    $quantity
                );

                StockMovement::create([
                    'warehouse_id' => $layer->warehouse_id,
                    'variant_id' => $layer->variant_id,
                    'batch_id' => $layer->batch_id,
                    'type' => 'return',
                    'source_id' => $returnItem->id,
                    'quantity' => $quantity,
                    'direction' => 1,
                    'movement_date' => $returnDate,
                ]);

                InventoryLayer::create([
                    'warehouse_id' => $layer->warehouse_id,
                    'variant_id' => $layer->variant_id,
                    'batch_id' => $layer->batch_id,
                    'source_type' => 'return',
                    'source_id' => $returnItem->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'layer_date' => $returnDate,
                ]);
            }

            return $customerReturn;
        }, 3);

        return redirect()
            ->route('admin.orders.returns.show', $customerReturn->id)
            ->with(
                'success',
                'Возврат №' . $customerReturn->id . ' успешно оформлен.'
            );
    }

    /**
     * Просмотр возврата и подготовка данных для редактирования.
     */
    public function show(CustomerReturn $customerReturn)
    {
        $customerReturn->load([
            'order.pointOfSale',
            'order.items',
            'items.orderItem',
            'items.variant.product',
            'items.batch',
            'items.warehouse',
            'payments.paymentMethod',
        ]);

        $grouped = [];

        foreach ($customerReturn->items as $item) {
            $allocation = FifoAllocation::query()
                ->with([
                    'movement',
                    'layer.batch',
                    'layer.warehouse',
                    'layer.variant.product',
                ])
                ->find($item->original_fifo_allocation_id);

            $layer = $allocation?->layer;

            $batchId = $layer?->batch_id ?? $item->batch_id;
            $warehouseId = $layer?->warehouse_id ?? $item->warehouse_id;
            $variantId = $layer?->variant_id ?? $item->variant_id;

            $key = implode('_', [
                $item->order_item_id,
                $variantId,
                $batchId,
                $warehouseId,
            ]);

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'key' => $key,
                    'item' => $item,
                    'items' => [],
                    'batch' => $layer?->batch ?? $item->batch,
                    'warehouse' => $layer?->warehouse ?? $item->warehouse,
                    'quantity' => 0,
                    'available_quantity' => 0,
                    'allocation_ids' => [],
                ];
            }

            $grouped[$key]['items'][] = $item;
            $grouped[$key]['quantity'] += (int) $item->quantity;

            if ($allocation) {
                $grouped[$key]['allocation_ids'][] = $allocation->id;
            }
        }

        $orderItemIds = $customerReturn->items
            ->pluck('order_item_id')
            ->filter()
            ->unique()
            ->values();

        if ($orderItemIds->isNotEmpty()) {
            $allocations = FifoAllocation::query()
                ->with([
                    'movement',
                    'layer.batch',
                    'layer.warehouse',
                    'layer.variant.product',
                ])
                ->whereHas('movement', function ($query) use ($orderItemIds) {
                    $query->where('type', 'sale')
                        ->whereIn('source_id', $orderItemIds);
                })
                ->get();

            $allocationIds = $allocations
                ->pluck('id')
                ->unique()
                ->values();

            $otherReturned = collect();

            if ($allocationIds->isNotEmpty()) {
                $otherReturned = CustomerReturnItem::query()
                    ->whereIn('original_fifo_allocation_id', $allocationIds)
                    ->where('customer_return_id', '!=', $customerReturn->id)
                    ->selectRaw(
                        'original_fifo_allocation_id, SUM(quantity) as total_quantity'
                    )
                    ->groupBy('original_fifo_allocation_id')
                    ->get()
                    ->keyBy('original_fifo_allocation_id');
            }

            foreach ($allocations as $allocation) {
                $layer = $allocation->layer;
                $movement = $allocation->movement;

                if (!$layer || !$movement) {
                    continue;
                }

                $orderItemId = (int) $movement->source_id;
                $variantId = (int) $layer->variant_id;
                $batchId = (int) $layer->batch_id;
                $warehouseId = (int) $layer->warehouse_id;

                $key = implode('_', [
                    $orderItemId,
                    $variantId,
                    $batchId,
                    $warehouseId,
                ]);

                if (!isset($grouped[$key])) {
                    continue;
                }

                $returnedElsewhere = (int) (
                    $otherReturned->get($allocation->id)?->total_quantity ?? 0
                );

                $availableFromAllocation = max(
                    0,
                    (int) $allocation->quantity - $returnedElsewhere
                );

                $grouped[$key]['available_quantity'] += $availableFromAllocation;

                if (!in_array(
                    $allocation->id,
                    $grouped[$key]['allocation_ids'],
                    true
                )) {
                    $grouped[$key]['allocation_ids'][] = $allocation->id;
                }
            }
        }

        $rows = array_values($grouped);

        $paymentMethods = PaymentMethod::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.orders.returns.show',
            compact('customerReturn', 'rows', 'paymentMethods')
        );
    }

    /**
     * Обновление существующего возврата.
     */
    public function update(Request $request, CustomerReturn $customerReturn)
    {
        $paymentsInput = collect($request->input('payments', []))
            ->map(function ($payment) {
                if (is_array($payment) && isset($payment['amount'])) {
                    $payment['amount'] = str_replace(
                        ',',
                        '.',
                        str_replace(' ', '', trim((string) $payment['amount']))
                    );
                }

                return $payment;
            })
            ->all();

        $request->merge(['payments' => $paymentsInput]);

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.key' => ['required', 'string', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],

            'payments' => ['required', 'array', 'min:1'],
            'payments.*.payment_method_id' => [
                'required',
                'integer',
                'exists:payment_methods,id',
            ],
            'payments.*.amount' => ['required', 'numeric', 'gt:0'],

            'return_date' => ['required', 'date'],
            'comment' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            DB::transaction(function () use ($validated, $customerReturn) {
                $return = CustomerReturn::query()
                    ->whereKey($customerReturn->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $order = Order::query()
                    ->whereKey($return->order_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $returnItems = CustomerReturnItem::query()
                    ->where('customer_return_id', $return->id)
                    ->lockForUpdate()
                    ->get();

                $returnPayments = CustomerReturnPayment::query()
                    ->where('customer_return_id', $return->id)
                    ->lockForUpdate()
                    ->get();

                $makeKey = static function (
                    $orderItemId,
                    $variantId,
                    $batchId,
                    $warehouseId
                ) {
                    return implode('_', [
                        (int) $orderItemId,
                        (int) $variantId,
                        (int) $batchId,
                        (int) $warehouseId,
                    ]);
                };

                $groups = [];

                foreach ($returnItems as $item) {
                    $allocation = FifoAllocation::query()
                        ->with('layer')
                        ->find($item->original_fifo_allocation_id);

                    $layer = $allocation?->layer;

                    $variantId = (int) ($item->variant_id ?? $layer?->variant_id);
                    $batchId = (int) ($item->batch_id ?? $layer?->batch_id);
                    $warehouseId = (int) ($item->warehouse_id ?? $layer?->warehouse_id);
                    $orderItemId = (int) $item->order_item_id;

                    if (!$variantId || !$batchId || !$warehouseId || !$orderItemId) {
                        throw ValidationException::withMessages([
                            'items' => 'У позиции возврата отсутствуют данные товара, партии или склада.',
                        ]);
                    }

                    $key = $makeKey(
                        $orderItemId,
                        $variantId,
                        $batchId,
                        $warehouseId
                    );

                    $groups[$key] = [
                        'key' => $key,
                        'order_item_id' => $orderItemId,
                        'variant_id' => $variantId,
                        'batch_id' => $batchId,
                        'warehouse_id' => $warehouseId,
                    ];
                }

                if (!$groups) {
                    throw ValidationException::withMessages([
                        'items' => 'В возврате нет позиций для редактирования.',
                    ]);
                }

                $requested = [];

                foreach ($validated['items'] as $submitted) {
                    $key = (string) $submitted['key'];

                    if (!isset($groups[$key])) {
                        throw ValidationException::withMessages([
                            'items' => 'Список позиций изменился. Обновите страницу.',
                        ]);
                    }

                    if (isset($requested[$key])) {
                        throw ValidationException::withMessages([
                            'items' => 'Группа товара передана несколько раз.',
                        ]);
                    }

                    $requested[$key] = (int) $submitted['quantity'];
                }

                $groupKeys = array_keys($groups);
                $requestedKeys = array_keys($requested);

                sort($groupKeys);
                sort($requestedKeys);

                if ($groupKeys !== $requestedKeys) {
                    throw ValidationException::withMessages([
                        'items' => 'Необходимо передать все группы товаров.',
                    ]);
                }

                $orderItems = OrderItem::query()
                    ->where('order_id', $order->id)
                    ->get()
                    ->keyBy('id');

                if ($orderItems->isEmpty()) {
                    throw ValidationException::withMessages([
                        'items' => 'Не найдены товары исходного заказа.',
                    ]);
                }

                $saleMovements = StockMovement::query()
                    ->where('type', 'sale')
                    ->whereIn('source_id', $orderItems->keys())
                    ->lockForUpdate()
                    ->get();

                $allocationsByGroup = [];
                $allAllocations = collect();

                foreach ($saleMovements as $movement) {
                    $orderItemId = (int) $movement->source_id;

                    if (!$orderItems->has($orderItemId)) {
                        continue;
                    }

                    $allocations = $movement->allocations()
                        ->with('layer')
                        ->lockForUpdate()
                        ->get();

                    foreach ($allocations as $allocation) {
                        $layer = $allocation->layer;

                        if (
                            !$layer ||
                            !$layer->variant_id ||
                            !$layer->batch_id ||
                            !$layer->warehouse_id
                        ) {
                            continue;
                        }

                        $key = $makeKey(
                            $orderItemId,
                            $layer->variant_id,
                            $layer->batch_id,
                            $layer->warehouse_id
                        );

                        if (!isset($groups[$key])) {
                            continue;
                        }

                        $allocationsByGroup[$key][] = $allocation;
                        $allAllocations->put($allocation->id, $allocation);
                    }
                }

                $allocationIds = $allAllocations->keys()->all();

                $otherReturned = collect();

                if ($allocationIds) {
                    $otherReturned = CustomerReturnItem::query()
                        ->whereIn('original_fifo_allocation_id', $allocationIds)
                        ->where('customer_return_id', '!=', $return->id)
                        ->selectRaw(
                            'original_fifo_allocation_id, SUM(quantity) as returned_quantity'
                        )
                        ->groupBy('original_fifo_allocation_id')
                        ->pluck(
                            'returned_quantity',
                            'original_fifo_allocation_id'
                        );
                }

                $desiredByAllocation = [];
                $grossReturnTotal = 0;
                $requestedByOrderItem = [];

                foreach ($groups as $key => $group) {
                    $requestedQuantity = $requested[$key];

                    $allocations = collect(
                        $allocationsByGroup[$key] ?? []
                    )->sortByDesc('id')->values();

                    if ($allocations->isEmpty() && $requestedQuantity > 0) {
                        throw ValidationException::withMessages([
                            'items' => 'Не найдены исходные FIFO-списания для одной из групп.',
                        ]);
                    }

                    $availableTotal = 0;
                    $capacities = [];

                    foreach ($allocations as $allocation) {
                        $alreadyReturned = (int) (
                            $otherReturned[$allocation->id] ?? 0
                        );

                        $capacity = max(
                            0,
                            (int) $allocation->quantity - $alreadyReturned
                        );

                        $capacities[$allocation->id] = $capacity;
                        $availableTotal += $capacity;
                    }

                    if ($requestedQuantity > $availableTotal) {
                        throw ValidationException::withMessages([
                            'items' => sprintf(
                                'Для группы %s доступно %d шт., запрошено %d шт.',
                                $key,
                                $availableTotal,
                                $requestedQuantity
                            ),
                        ]);
                    }

                    $remaining = $requestedQuantity;

                    foreach ($allocations as $allocation) {
                        $take = min(
                            $remaining,
                            $capacities[$allocation->id] ?? 0
                        );

                        $desiredByAllocation[$allocation->id] = [
                            'allocation' => $allocation,
                            'group' => $group,
                            'quantity' => $take,
                        ];

                        $remaining -= $take;

                        if ($remaining <= 0) {
                            break;
                        }
                    }

                    if ($remaining > 0) {
                        throw ValidationException::withMessages([
                            'items' => 'Не удалось распределить количество по FIFO.',
                        ]);
                    }

                    $orderItem = $orderItems->get($group['order_item_id']);

                    if (!$orderItem) {
                        throw ValidationException::withMessages([
                            'items' => 'Не найдена позиция исходного заказа.',
                        ]);
                    }

                    $grossReturnTotal +=
                        (float) $orderItem->price * $requestedQuantity;

                    $orderItemId = (int) $group['order_item_id'];

                    $requestedByOrderItem[$orderItemId] =
                        ($requestedByOrderItem[$orderItemId] ?? 0)
                        + $requestedQuantity;
                }

                $submittedPayments = $validated['payments'];

                $existingPaymentIds = $returnPayments
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->all();

                $submittedPaymentIds = collect(array_keys($submittedPayments))
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->all();

                if ($existingPaymentIds !== $submittedPaymentIds) {
                    throw ValidationException::withMessages([
                        'payments' => 'Список платежей изменился. Обновите страницу.',
                    ]);
                }

                $paymentTotal = round(
                    collect($submittedPayments)->sum(
                        fn ($payment) => (float) $payment['amount']
                    ),
                    2
                );

                $originalItemsTotal = (float) $orderItems->sum(
                    fn ($item) =>
                        (float) $item->price * (int) $item->quantity
                );

                $orderNetTotal = max(
                    0,
                    round(
                        $originalItemsTotal - (float) ($order->discount ?? 0),
                        2
                    )
                );

                // Исключаем редактируемый возврат из подсчёта предыдущих возвратов.
                $returnState = $this->getOrderReturnState(
                    $order,
                    (int) $return->id
                );

                $completesOrder = $this->doesReturnCompleteOrder(
                    $orderItems,
                    $returnState['returned_quantities'],
                    $requestedByOrderItem
                );

                $remainingRefundable = max(
                    0,
                    round(
                        $orderNetTotal - $returnState['previous_refund_total'],
                        2
                    )
                );

                $expectedRefund = $completesOrder
                    ? $remainingRefundable
                    : min(round($grossReturnTotal, 2), $remainingRefundable);

                $expectedRefund = round($expectedRefund, 2);

                if ($expectedRefund <= 0) {
                    throw ValidationException::withMessages([
                        'payments' => 'По этому заказу больше нет суммы, доступной к возврату.',
                    ]);
                }

                if (abs($paymentTotal - $expectedRefund) > 0.01) {
                    throw ValidationException::withMessages([
                        'payments' => sprintf(
                            'Сумма возврата должна составлять %s ₸. Указано %s ₸.',
                            number_format($expectedRefund, 2, '.', ' '),
                            number_format($paymentTotal, 2, '.', ' ')
                        ),
                    ]);
                }

                $existingByAllocation = [];

                foreach ($returnItems as $item) {
                    $allocationId = (int) $item->original_fifo_allocation_id;

                    if ($allocationId <= 0) {
                        throw ValidationException::withMessages([
                            'items' => 'У позиции отсутствует исходное FIFO-списание.',
                        ]);
                    }

                    if (isset($existingByAllocation[$allocationId])) {
                        throw ValidationException::withMessages([
                            'items' => 'Найдены повторные строки для одного FIFO-списания.',
                        ]);
                    }

                    $existingByAllocation[$allocationId] = $item;
                }

                $affectedIds = array_unique(array_merge(
                    array_keys($existingByAllocation),
                    array_keys($desiredByAllocation)
                ));

                $fifoService = app(FifoService::class);

                foreach ($affectedIds as $allocationId) {
                    $allocationId = (int) $allocationId;

                    $returnItem = $existingByAllocation[$allocationId] ?? null;
                    $desired = $desiredByAllocation[$allocationId] ?? null;

                    $newQuantity = (int) ($desired['quantity'] ?? 0);
                    $oldQuantity = (int) ($returnItem?->quantity ?? 0);

                    if (!$returnItem && $newQuantity === 0) {
                        continue;
                    }

                    if ($returnItem) {
                        $returnLayer = InventoryLayer::query()
                            ->where('source_type', 'return')
                            ->where('source_id', $returnItem->id)
                            ->lockForUpdate()
                            ->first();

                        $returnMovement = StockMovement::query()
                            ->where('type', 'return')
                            ->where('source_id', $returnItem->id)
                            ->lockForUpdate()
                            ->first();

                        if (!$returnLayer || !$returnMovement) {
                            throw ValidationException::withMessages([
                                'items' => 'Не найдены складской слой или движение возврата №' . $returnItem->id . '.',
                            ]);
                        }

                        if (
                            (int) $returnLayer->quantity !== $oldQuantity ||
                            (int) $returnMovement->quantity !== $oldQuantity
                        ) {
                            throw ValidationException::withMessages([
                                'items' => 'Количество складской записи не совпадает с позицией возврата №' . $returnItem->id . '.',
                            ]);
                        }

                        $downstreamQuantity = (int) FifoAllocation::query()
                            ->where('inventory_layer_id', $returnLayer->id)
                            ->sum('quantity');

                        if ($newQuantity < $downstreamQuantity) {
                            throw ValidationException::withMessages([
                                'items' => sprintf(
                                    'Нельзя установить %d шт.: из этой позиции уже использовано %d шт.',
                                    $newQuantity,
                                    $downstreamQuantity
                                ),
                            ]);
                        }

                        $batch = \App\Models\Batch::query()
                            ->whereKey($returnItem->batch_id)
                            ->lockForUpdate()
                            ->first();

                        if (!$batch) {
                            throw ValidationException::withMessages([
                                'items' => 'Не найдена партия для возврата №' . $returnItem->id . '.',
                            ]);
                        }

                        if ($newQuantity === 0) {
                            $fifoService->changeWarehouseQuantity(
                                $batch,
                                (int) $returnItem->warehouse_id,
                                -$oldQuantity
                            );

                            $returnMovement->delete();
                            $returnLayer->delete();
                            $returnItem->delete();

                            continue;
                        }

                        $difference = $newQuantity - $oldQuantity;

                        if ($difference !== 0) {
                            $fifoService->changeWarehouseQuantity(
                                $batch,
                                (int) $returnItem->warehouse_id,
                                $difference
                            );
                        }

                        $allocation = $desired['allocation'] ?? null;

                        if (!$allocation) {
                            throw ValidationException::withMessages([
                                'items' => 'Не найдено исходное FIFO-списание.',
                            ]);
                        }

                        $allocation->loadMissing('layer');

                        if (!$allocation->layer) {
                            throw ValidationException::withMessages([
                                'items' => 'Не найден исходный FIFO-слой.',
                            ]);
                        }

                        $unitCost = $this->allocationUnitCost(
                            $allocation,
                            $allocation->layer
                        );

                        $returnItem->quantity = $newQuantity;
                        $returnItem->unit_cost = $unitCost;
                        $returnItem->save();

                        $returnLayer->quantity = $newQuantity;
                        $returnLayer->unit_cost = $unitCost;
                        $returnLayer->layer_date = $validated['return_date'];
                        $returnLayer->save();

                        $returnMovement->quantity = $newQuantity;
                        $returnMovement->movement_date = $validated['return_date'];
                        $returnMovement->save();

                        continue;
                    }

                    if (!$desired || $newQuantity <= 0) {
                        continue;
                    }

                    $allocation = $desired['allocation'];
                    $group = $desired['group'];

                    $allocation->loadMissing('layer');

                    if (!$allocation->layer) {
                        throw ValidationException::withMessages([
                            'items' => 'Не найден исходный FIFO-слой.',
                        ]);
                    }

                    $unitCost = $this->allocationUnitCost(
                        $allocation,
                        $allocation->layer
                    );

                    $batch = \App\Models\Batch::query()
                        ->whereKey($group['batch_id'])
                        ->lockForUpdate()
                        ->first();

                    if (!$batch) {
                        throw ValidationException::withMessages([
                            'items' => 'Не найдена партия для новой позиции возврата.',
                        ]);
                    }

                    $newItem = CustomerReturnItem::create([
                        'customer_return_id' => $return->id,
                        'order_item_id' => $group['order_item_id'],
                        'original_fifo_allocation_id' => $allocation->id,
                        'warehouse_id' => $group['warehouse_id'],
                        'variant_id' => $group['variant_id'],
                        'batch_id' => $group['batch_id'],
                        'quantity' => $newQuantity,
                        'unit_cost' => $unitCost,
                    ]);

                    $fifoService->changeWarehouseQuantity(
                        $batch,
                        (int) $group['warehouse_id'],
                        $newQuantity
                    );

                    StockMovement::create([
                        'warehouse_id' => $group['warehouse_id'],
                        'variant_id' => $group['variant_id'],
                        'batch_id' => $group['batch_id'],
                        'type' => 'return',
                        'source_id' => $newItem->id,
                        'quantity' => $newQuantity,
                        'direction' => 1,
                        'movement_date' => $validated['return_date'],
                    ]);

                    InventoryLayer::create([
                        'warehouse_id' => $group['warehouse_id'],
                        'variant_id' => $group['variant_id'],
                        'batch_id' => $group['batch_id'],
                        'source_type' => 'return',
                        'source_id' => $newItem->id,
                        'quantity' => $newQuantity,
                        'unit_cost' => $unitCost,
                        'layer_date' => $validated['return_date'],
                    ]);
                }

                foreach ($returnPayments as $payment) {
                    $data = $submittedPayments[$payment->id]
                        ?? $submittedPayments[(string) $payment->id]
                        ?? null;

                    if (!$data) {
                        throw ValidationException::withMessages([
                            'payments' => 'Не найдены данные платежа №' . $payment->id . '.',
                        ]);
                    }

                    $payment->payment_method_id = (int) $data['payment_method_id'];
                    $payment->amount = round((float) $data['amount'], 2);
                    $payment->save();
                }

                $return->return_date = $validated['return_date'];
                $return->comment = $validated['comment'] ?? null;
                $return->save();
            }, 3);

            return redirect()
                ->route('admin.orders.returns.show', $customerReturn->id)
                ->with('success', 'Возврат успешно обновлён.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Не удалось обновить возврат: ' . $e->getMessage());
        }
    }

    /**
     * Удаление возврата с отменой складских операций.
     */
    public function destroy(CustomerReturn $customerReturn)
    {
        try {
            $orderId = DB::transaction(function () use ($customerReturn) {
                $return = CustomerReturn::query()
                    ->whereKey($customerReturn->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $orderId = $return->order_id;

                $items = CustomerReturnItem::query()
                    ->where('customer_return_id', $return->id)
                    ->lockForUpdate()
                    ->get();

                $fifoService = app(FifoService::class);

                foreach ($items as $item) {
                    $movement = StockMovement::query()
                        ->where('type', 'return')
                        ->where('source_id', $item->id)
                        ->lockForUpdate()
                        ->first();

                    $layer = InventoryLayer::query()
                        ->where('source_type', 'return')
                        ->where('source_id', $item->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$movement || !$layer) {
                        throw new RuntimeException(
                            "Не найдены складские записи возврата №{$return->id}, "
                                . "позиция №{$item->id}. Удаление отменено."
                        );
                    }

                    if (
                        (int) $layer->batch_id !== (int) $item->batch_id ||
                        (int) $layer->warehouse_id !== (int) $item->warehouse_id ||
                        (int) $layer->variant_id !== (int) $item->variant_id
                    ) {
                        throw new RuntimeException(
                            "Партия или склад позиции №{$item->id} не совпадают "
                                . "с данными возврата. Удаление отменено."
                        );
                    }

                    if ((int) $layer->quantity !== (int) $item->quantity) {
                        throw new RuntimeException(
                            "Количество в FIFO-слое не совпадает с возвратом №{$return->id}. "
                                . "Удаление отменено."
                        );
                    }

                    $allocatedFromLayer = (int) FifoAllocation::query()
                        ->where('inventory_layer_id', $layer->id)
                        ->sum('quantity');

                    if ($allocatedFromLayer > 0) {
                        throw new RuntimeException(
                            "Товар из возврата №{$return->id} использовался "
                                . "в последующих продажах. Сначала удалите или отмените "
                                . "эти продажи корректно."
                        );
                    }

                    $batch = \App\Models\Batch::query()
                        ->whereKey($layer->batch_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$batch) {
                        throw new RuntimeException(
                            "Не найдена исходная партия для позиции №{$item->id}."
                        );
                    }

                    $fifoService->changeWarehouseQuantity(
                        $batch,
                        (int) $layer->warehouse_id,
                        -(int) $item->quantity
                    );

                    $movement->delete();
                    $layer->delete();
                    $item->delete();
                }

                CustomerReturnPayment::query()
                    ->where('customer_return_id', $return->id)
                    ->delete();

                $return->delete();

                return $orderId;
            }, 3);

            return redirect()
                ->route('admin.orders.returns.create', $orderId)
                ->with(
                    'success',
                    'Возврат удалён. Товар списан с исходного склада и из исходной партии.'
                );
        } catch (RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Возвращает сумму денег, выплаченную по другим возвратам заказа,
     * и количество товара, возвращённое по другим возвратам.
     *
     * $excludeReturnId используется при редактировании возврата,
     * чтобы не учитывать его старые платежи и количества.
     */
    private function getOrderReturnState(
        Order $order,
        ?int $excludeReturnId = null
    ): array {
        $returnsQuery = CustomerReturn::query()
            ->where('order_id', $order->id);

        if ($excludeReturnId !== null) {
            $returnsQuery->where('id', '!=', $excludeReturnId);
        }

        $returnIds = $returnsQuery->pluck('id');

        if ($returnIds->isEmpty()) {
            return [
                'previous_refund_total' => 0.0,
                'returned_quantities' => [],
            ];
        }

        $previousRefundTotal = (float) CustomerReturnPayment::query()
            ->whereIn('customer_return_id', $returnIds)
            ->sum('amount');

        $returnedQuantities = CustomerReturnItem::query()
            ->whereIn('customer_return_id', $returnIds)
            ->selectRaw('order_item_id, SUM(quantity) as total_quantity')
            ->groupBy('order_item_id')
            ->pluck('total_quantity', 'order_item_id')
            ->map(fn ($quantity) => (int) $quantity)
            ->all();

        return [
            'previous_refund_total' => round($previousRefundTotal, 2),
            'returned_quantities' => $returnedQuantities,
        ];
    }

    /**
     * Проверяет, возвращены ли все товары заказа с учётом
     * предыдущих возвратов и количества в текущем возврате.
     */
    private function doesReturnCompleteOrder(
        $orderItems,
        array $previousReturnedQuantities,
        array $requestedByOrderItem
    ): bool {
        foreach ($orderItems as $orderItem) {
            $orderItemId = (int) $orderItem->id;

            $alreadyReturned = (int) (
                $previousReturnedQuantities[$orderItemId] ?? 0
            );

            $requestedNow = (int) (
                $requestedByOrderItem[$orderItemId] ?? 0
            );

            $totalReturned = $alreadyReturned + $requestedNow;
            $orderedQuantity = (int) $orderItem->quantity;

            if ($totalReturned < $orderedQuantity) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ключ группы FIFO: исходная позиция + вариант + партия + склад.
     */
    private function allocationGroupKey(
        int $orderItemId,
        InventoryLayer $layer
    ): string {
        return implode('|', [
            $orderItemId,
            $layer->variant_id,
            $layer->batch_id,
            $layer->warehouse_id,
        ]);
    }

    /**
     * Себестоимость исходного FIFO-списания.
     */
    private function allocationUnitCost(
        FifoAllocation $allocation,
        InventoryLayer $layer
    ): float {
        if ($allocation->unit_cost !== null) {
            return (float) $allocation->unit_cost;
        }

        if ($layer->unit_cost !== null) {
            return (float) $layer->unit_cost;
        }

        if ($layer->source_type === 'initial') {
            $layer->loadMissing('variant.product');

            $purchasePrice = $layer->variant?->product?->purchase_price;

            if ($purchasePrice !== null) {
                return (float) $purchasePrice;
            }
        }

        throw new RuntimeException(
            'Не удалось определить историческую себестоимость возврата. '
                . 'FIFO allocation #' . $allocation->id
                . ', слой #' . $layer->id . '.'
        );
    }
}
