<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\InventoryLayer;
use App\Models\StockMovement;
use App\Models\FifoAllocation;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FifoService
{
    /**
     * Создание начальных FIFO-слоёв.
     */
    public function createInitialLayers(): int
    {
        $created = 0;

        DB::transaction(function () use (&$created) {
            Batch::with([
                'variant.product',
                'warehouses',
            ])->chunkById(100, function ($batches) use (&$created) {
                foreach ($batches as $batch) {
                    foreach ($batch->warehouses as $warehouse) {
                        $quantity = (int) (
                            $warehouse->pivot->quantity ?? 0
                        );

                        if ($quantity <= 0) {
                            continue;
                        }

                        $exists = InventoryLayer::where(
                            'warehouse_id',
                            $warehouse->id
                        )
                            ->where('variant_id', $batch->variant_id)
                            ->where('batch_id', $batch->id)
                            ->where('source_type', 'initial')
                            ->exists();

                        if ($exists) {
                            continue;
                        }

                        InventoryLayer::create([
                            'warehouse_id' => $warehouse->id,
                            'variant_id' => $batch->variant_id,
                            'batch_id' => $batch->id,
                            'source_type' => 'initial',
                            'source_id' => null,
                            'quantity' => $quantity,
                            'unit_cost' => null,
                            'layer_date' => now('Asia/Almaty'),
                        ]);

                        $created++;
                    }
                }
            });
        });

        return $created;
    }

    /**
     * Получить себестоимость FIFO-слоя.
     */
    public function getLayerUnitCost(InventoryLayer $layer): float
    {
        if ($layer->unit_cost !== null) {
            return (float) $layer->unit_cost;
        }

        // Начальные слои могут не содержать историческую себестоимость.
        if ($layer->source_type === 'initial') {
            $layer->loadMissing('variant.product');

            return (float) (
                $layer->variant?->product?->purchase_price ?? 0
            );
        }

        return 0.0;
    }

    /**
     * Себестоимость конкретного FIFO-распределения.
     */
    public function getAllocationUnitCost(
        FifoAllocation $allocation
    ): float {
        if ($allocation->unit_cost !== null) {
            return (float) $allocation->unit_cost;
        }

        if (!$allocation->layer) {
            return 0.0;
        }

        return $this->getLayerUnitCost($allocation->layer);
    }

    /**
     * FIFO-слои товара на складе.
     */
    public function getAvailableLayers(
        int $warehouseId,
        int $variantId
    ) {
        return InventoryLayer::with([
            'variant.product',
            'batch',
        ])
            ->where('warehouse_id', $warehouseId)
            ->where('variant_id', $variantId)
            ->orderByRaw("
                CASE
                    WHEN source_type = 'initial' THEN 0
                    ELSE 1
                END
            ")
            ->orderBy('layer_date')
            ->orderBy('id')
            ->get();
    }

    /**
     * FIFO-слои конкретной партии.
     */
    public function getAvailableBatchLayers(
        int $warehouseId,
        int $variantId,
        int $batchId
    ) {
        return InventoryLayer::with([
            'variant.product',
            'batch',
        ])
            ->where('warehouse_id', $warehouseId)
            ->where('variant_id', $variantId)
            ->where('batch_id', $batchId)
            ->orderByRaw("
                CASE
                    WHEN source_type = 'initial' THEN 0
                    ELSE 1
                END
            ")
            ->orderBy('layer_date')
            ->orderBy('id')
            ->get();
    }

    /**
     * Количество, уже распределённое из FIFO-слоя.
     */
    public function getConsumedQuantity(
        InventoryLayer $layer
    ): int {
        return (int) FifoAllocation::where(
            'inventory_layer_id',
            $layer->id
        )->sum('quantity');
    }

    /**
     * Остаток FIFO-слоя.
     */
    public function getRemainingQuantity(
        InventoryLayer $layer
    ): int {
        return max(
            0,
            (int) $layer->quantity - $this->getConsumedQuantity($layer)
        );
    }

    /**
     * Общий FIFO-расчёт по всем партиям товара на складе.
     */
    public function calculateFifo(
        int $warehouseId,
        int $variantId,
        int $quantity
    ): array {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'Количество расхода должно быть больше нуля.'
            );
        }

        $layers = $this->getAvailableLayers(
            $warehouseId,
            $variantId
        );

        $remainingToConsume = $quantity;
        $result = [];
        $totalCost = 0.0;

        foreach ($layers as $layer) {
            if ($remainingToConsume <= 0) {
                break;
            }

            $available = $this->getRemainingQuantity($layer);

            if ($available <= 0) {
                continue;
            }

            $take = min($available, $remainingToConsume);
            $unitCost = $this->getLayerUnitCost($layer);
            $cost = $take * $unitCost;

            $result[] = [
                'layer_id' => $layer->id,
                'batch_id' => $layer->batch_id,
                'quantity' => $take,
                'unit_cost' => $unitCost,
                'cost' => $cost,
                'source_type' => $layer->source_type,
                'source_id' => $layer->source_id,
            ];

            $totalCost += $cost;
            $remainingToConsume -= $take;
        }

        if ($remainingToConsume > 0) {
            throw new RuntimeException(
                'Недостаточно FIFO-остатка для списания ' .
                $quantity .
                ' шт. Не хватает ' .
                $remainingToConsume .
                ' шт.'
            );
        }

        return [
            'items' => $result,
            'total_cost' => $totalCost,
            'quantity' => $quantity,
        ];
    }

    /**
     * FIFO-расчёт внутри конкретной партии.
     */
    public function calculateBatchFifo(
        int $warehouseId,
        int $variantId,
        int $batchId,
        int $quantity
    ): array {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'Количество расхода должно быть больше нуля.'
            );
        }

        $batch = Batch::findOrFail($batchId);

        if ((int) $batch->variant_id !== $variantId) {
            throw new RuntimeException(
                "Партия {$batch->batch_code} не принадлежит выбранному товару."
            );
        }

        $layers = $this->getAvailableBatchLayers(
            $warehouseId,
            $variantId,
            $batchId
        );

        $remainingToConsume = $quantity;
        $result = [];
        $totalCost = 0.0;

        foreach ($layers as $layer) {
            if ($remainingToConsume <= 0) {
                break;
            }

            $available = $this->getRemainingQuantity($layer);

            if ($available <= 0) {
                continue;
            }

            $take = min($available, $remainingToConsume);
            $unitCost = $this->getLayerUnitCost($layer);
            $cost = $take * $unitCost;

            $result[] = [
                'layer_id' => $layer->id,
                'batch_id' => $layer->batch_id,
                'quantity' => $take,
                'unit_cost' => $unitCost,
                'cost' => $cost,
                'source_type' => $layer->source_type,
                'source_id' => $layer->source_id,
            ];

            $totalCost += $cost;
            $remainingToConsume -= $take;
        }

        if ($remainingToConsume > 0) {
            throw new RuntimeException(
                "Недостаточно FIFO-остатка в партии " .
                "{$batch->batch_code}. Не хватает {$remainingToConsume} шт."
            );
        }

        return [
            'items' => $result,
            'total_cost' => $totalCost,
            'quantity' => $quantity,
        ];
    }

    /**
     * FIFO-списание продажи или списания.
     *
     * Если передан $batchId, списываем только из выбранной партии.
     * Если $batchId не передан, сохраняется прежнее общее FIFO-поведение.
     */
    public function consume(
        int $warehouseId,
        int $variantId,
        int $quantity,
        string $type,
        ?int $sourceId = null,
        ?string $movementDate = null,
        ?int $batchId = null
    ): StockMovement {
        if (!in_array($type, ['sale', 'writeoff'], true)) {
            throw new RuntimeException(
                'Недопустимый тип FIFO-списания.'
            );
        }

        if ($quantity <= 0) {
            throw new RuntimeException(
                'Количество списания должно быть больше нуля.'
            );
        }

        return DB::transaction(function () use (
            $warehouseId,
            $variantId,
            $quantity,
            $type,
            $sourceId,
            $movementDate,
            $batchId
        ) {
            if ($batchId !== null) {
                $batch = Batch::findOrFail($batchId);

                if ((int) $batch->variant_id !== $variantId) {
                    throw new RuntimeException(
                        "Партия {$batch->batch_code} не принадлежит выбранному товару."
                    );
                }

                $warehousePivot = $batch->warehouses()
                    ->where('warehouse_id', $warehouseId)
                    ->first();

                if (!$warehousePivot) {
                    throw new RuntimeException(
                        "Партия {$batch->batch_code} отсутствует на выбранном складе."
                    );
                }

                $physicalQuantity = (int) (
                    $warehousePivot->pivot->quantity ?? 0
                );

                if ($quantity > $physicalQuantity) {
                    throw new RuntimeException(
                        "Недостаточно товара партии {$batch->batch_code}. " .
                        "Доступно: {$physicalQuantity} шт."
                    );
                }

                $fifo = $this->calculateBatchFifo(
                    $warehouseId,
                    $variantId,
                    $batchId,
                    $quantity
                );
            } else {
                $fifo = $this->calculateFifo(
                    $warehouseId,
                    $variantId,
                    $quantity
                );
            }

            $movement = StockMovement::create([
                'warehouse_id' => $warehouseId,
                'variant_id' => $variantId,
                'batch_id' => $batchId
                    ?? $fifo['items'][0]['batch_id'],
                'type' => $type,
                'source_id' => $sourceId,
                'quantity' => $quantity,
                'direction' => -1,
                'movement_date' => $movementDate
                    ? $movementDate
                    : now('Asia/Almaty'),
            ]);

            foreach ($fifo['items'] as $item) {
                $batch = Batch::findOrFail($item['batch_id']);

                $this->changeWarehouseQuantity(
                    $batch,
                    $warehouseId,
                    -(int) $item['quantity']
                );

                FifoAllocation::create([
                    'stock_movement_id' => $movement->id,
                    'inventory_layer_id' => $item['layer_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

            return $movement;
        }, 3);
    }

    /**
     * Списание из конкретной партии.
     *
     * Метод оставлен для совместимости с существующими списаниями.
     */
    public function consumeBatch(
        int $warehouseId,
        int $variantId,
        int $batchId,
        int $quantity,
        string $type = 'writeoff',
        ?int $sourceId = null,
        ?string $movementDate = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'Количество списания должно быть больше нуля.'
            );
        }

        if ($type !== 'writeoff') {
            throw new RuntimeException(
                'consumeBatch() предназначен для списаний.'
            );
        }

        return DB::transaction(function () use (
            $warehouseId,
            $variantId,
            $batchId,
            $quantity,
            $type,
            $sourceId,
            $movementDate
        ) {
            $batch = Batch::findOrFail($batchId);

            if ((int) $batch->variant_id !== $variantId) {
                throw new RuntimeException(
                    "Партия {$batch->batch_code} не принадлежит выбранному товару."
                );
            }

            $warehouse = $batch->warehouses()
                ->where('warehouse_id', $warehouseId)
                ->first();

            if (!$warehouse) {
                throw new RuntimeException(
                    "Партия {$batch->batch_code} отсутствует на выбранном складе."
                );
            }

            $physicalQuantity = (int) (
                $warehouse->pivot->quantity ?? 0
            );

            if ($quantity > $physicalQuantity) {
                throw new RuntimeException(
                    "Недостаточно товара партии {$batch->batch_code}. " .
                    "Доступно: {$physicalQuantity} шт."
                );
            }

            $fifo = $this->calculateBatchFifo(
                $warehouseId,
                $variantId,
                $batchId,
                $quantity
            );

            $movement = StockMovement::create([
                'warehouse_id' => $warehouseId,
                'variant_id' => $variantId,
                'batch_id' => $batchId,
                'type' => $type,
                'source_id' => $sourceId,
                'quantity' => $quantity,
                'direction' => -1,
                'movement_date' => $movementDate
                    ? $movementDate
                    : now('Asia/Almaty'),
            ]);

            $this->changeWarehouseQuantity(
                $batch,
                $warehouseId,
                -$quantity
            );

            foreach ($fifo['items'] as $item) {
                FifoAllocation::create([
                    'stock_movement_id' => $movement->id,
                    'inventory_layer_id' => $item['layer_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

            return $movement;
        }, 3);
    }

    /**
     * Изменение физического остатка партии на складе.
     */
    public function changeWarehouseQuantity(
        Batch $batch,
        int $warehouseId,
        int $difference
    ): void {
        $pivot = $batch->warehouses()
            ->where('warehouse_id', $warehouseId)
            ->first();

        $current = $pivot
            ? (int) ($pivot->pivot->quantity ?? 0)
            : 0;

        $newQuantity = $current + $difference;

        if ($newQuantity < 0) {
            throw new RuntimeException(
                "Недостаточно товара партии {$batch->batch_code} на выбранном складе."
            );
        }

        if ($pivot) {
            $batch->warehouses()->updateExistingPivot(
                $warehouseId,
                ['quantity' => $newQuantity]
            );
        } else {
            if ($difference < 0) {
                throw new RuntimeException(
                    'Партия отсутствует на выбранном складе.'
                );
            }

            $batch->warehouses()->attach(
                $warehouseId,
                ['quantity' => $difference]
            );
        }
    }

    /**
     * Перерасчёт FIFO-распределений.
     *
     * Внимание: метод удаляет и создаёт заново FIFO-аллокации
     * продаж и списаний. Не запускай без резервной копии.
     */
    public function rebuildAllocations(
        ?int $warehouseId = null,
        ?int $variantId = null
    ): void {
        DB::transaction(function () use ($warehouseId, $variantId) {
            $movementQuery = StockMovement::query()
                ->whereIn('type', ['sale', 'writeoff'])
                ->orderBy('movement_date')
                ->orderBy('id');

            if ($warehouseId !== null) {
                $movementQuery->where('warehouse_id', $warehouseId);
            }

            if ($variantId !== null) {
                $movementQuery->where('variant_id', $variantId);
            }

            $movements = $movementQuery->get();

            foreach ($movements as $movement) {
                FifoAllocation::where(
                    'stock_movement_id',
                    $movement->id
                )->delete();
            }

            $consumed = [];

            foreach ($movements as $movement) {
                if (
                    $movement->type === 'writeoff' &&
                    $movement->batch_id
                ) {
                    $layers = InventoryLayer::where(
                        'warehouse_id',
                        $movement->warehouse_id
                    )
                        ->where('variant_id', $movement->variant_id)
                        ->where('batch_id', $movement->batch_id)
                        ->orderByRaw("
                            CASE
                                WHEN source_type = 'initial' THEN 0
                                ELSE 1
                            END
                        ")
                        ->orderBy('layer_date')
                        ->orderBy('id')
                        ->get();
                } else {
                    $layers = InventoryLayer::where(
                        'warehouse_id',
                        $movement->warehouse_id
                    )
                        ->where('variant_id', $movement->variant_id)
                        ->orderByRaw("
                            CASE
                                WHEN source_type = 'initial' THEN 0
                                ELSE 1
                            END
                        ")
                        ->orderBy('layer_date')
                        ->orderBy('id')
                        ->get();
                }

                $remaining = (int) $movement->quantity;

                foreach ($layers as $layer) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $alreadyConsumed = $consumed[$layer->id] ?? 0;

                    $available = max(
                        0,
                        (int) $layer->quantity - $alreadyConsumed
                    );

                    if ($available <= 0) {
                        continue;
                    }

                    $take = min($available, $remaining);
                    $unitCost = $this->getLayerUnitCost($layer);

                    FifoAllocation::create([
                        'stock_movement_id' => $movement->id,
                        'inventory_layer_id' => $layer->id,
                        'quantity' => $take,
                        'unit_cost' => $unitCost,
                    ]);

                    $consumed[$layer->id] = $alreadyConsumed + $take;
                    $remaining -= $take;
                }

                if ($remaining > 0) {
                    if (
                        $movement->type === 'writeoff' &&
                        $movement->batch_id
                    ) {
                        $batch = Batch::find($movement->batch_id);

                        $batchCode = $batch
                            ? $batch->batch_code
                            : $movement->batch_id;

                        throw new RuntimeException(
                            "После перерасчёта FIFO недостаточно товара " .
                            "в партии {$batchCode} для движения #{$movement->id}. " .
                            "Не хватает {$remaining} шт."
                        );
                    }

                    throw new RuntimeException(
                        "После перерасчёта FIFO недостаточно товара " .
                        "для движения #{$movement->id}. " .
                        "Не хватает {$remaining} шт."
                    );
                }
            }
        });
    }

    /**
     * Себестоимость движения по FIFO-аллокациям.
     */
    public function getMovementCost(StockMovement $movement): float
    {
        $movement->loadMissing([
            'allocations.layer.variant.product',
        ]);

        $total = 0.0;

        foreach ($movement->allocations as $allocation) {
            $total +=
                (int) $allocation->quantity *
                $this->getAllocationUnitCost($allocation);
        }

        return $total;
    }

    /**
     * Откат движения.
     *
     * Для sale/writeoff восстанавливает остаток по аллокациям.
     */
    public function reverseMovement(
        StockMovement $movement
    ): void {
        DB::transaction(function () use ($movement) {
            $movement->load(['allocations.layer']);

            foreach ($movement->allocations as $allocation) {
                $layer = $allocation->layer;

                if (!$layer) {
                    continue;
                }

                $batch = Batch::find($layer->batch_id);

                if (!$batch) {
                    continue;
                }

                $this->changeWarehouseQuantity(
                    $batch,
                    $movement->warehouse_id,
                    (int) $allocation->quantity
                );
            }

            FifoAllocation::where(
                'stock_movement_id',
                $movement->id
            )->delete();

            $movement->delete();
        });
    }

    /**
     * Старый механизм возврата товара на склад.
     *
     * Оставлен для совместимости с существующим кодом.
     * Для возврата покупателя с точной исходной себестоимостью
     * используй CustomerReturnController.
     */
    public function returnToWarehouse(
        int $warehouseId,
        int $variantId,
        int $quantity,
        int $batchId,
        ?int $sourceId = null,
        ?string $movementDate = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'Количество возврата должно быть больше нуля.'
            );
        }

        return DB::transaction(function () use (
            $warehouseId,
            $variantId,
            $quantity,
            $batchId,
            $sourceId,
            $movementDate
        ) {
            $batch = Batch::findOrFail($batchId);

            if ((int) $batch->variant_id !== $variantId) {
                throw new RuntimeException(
                    "Партия {$batch->batch_code} не принадлежит выбранному товару."
                );
            }

            $date = $movementDate
                ? $movementDate
                : now('Asia/Almaty');

            $this->changeWarehouseQuantity(
                $batch,
                $warehouseId,
                $quantity
            );

            $movement = StockMovement::create([
                'warehouse_id' => $warehouseId,
                'variant_id' => $variantId,
                'batch_id' => $batchId,
                'type' => 'return',
                'source_id' => $sourceId,
                'quantity' => $quantity,
                'direction' => 1,
                'movement_date' => $date,
            ]);

            // Для совместимости со старым кодом оставлен прежний
            // способ определения стоимости. Он не гарантирует
            // историческую себестоимость проданного товара.
            $receiptLayer = InventoryLayer::where(
                'warehouse_id',
                $warehouseId
            )
                ->where('variant_id', $variantId)
                ->where('batch_id', $batchId)
                ->where('source_type', 'receipt')
                ->orderByDesc('layer_date')
                ->orderByDesc('id')
                ->first();

            if ($receiptLayer && $receiptLayer->unit_cost !== null) {
                $unitCost = (float) $receiptLayer->unit_cost;
            } else {
                $batch->loadMissing('variant.product');

                $unitCost = (float) (
                    $batch->variant?->product?->purchase_price ?? 0
                );
            }

            InventoryLayer::create([
                'warehouse_id' => $warehouseId,
                'variant_id' => $variantId,
                'batch_id' => $batchId,
                'source_type' => 'return',
                'source_id' => $sourceId,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'layer_date' => $date,
            ]);

            return $movement;
        }, 3);
    }
}
