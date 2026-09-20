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
     * ============================================================
     * СОЗДАНИЕ INITIAL-СЛОЁВ
     * ============================================================
     *
     * Создаёт начальный FIFO-слой для текущего остатка партии
     * на конкретном складе.
     */
    public function createInitialLayers(): int
    {
        $created = 0;

        DB::transaction(function () use (&$created) {

            Batch::with([
                'variant.product',
                'warehouses',
            ])
                ->chunkById(100, function ($batches) use (&$created) {

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
                                ->where(
                                    'variant_id',
                                    $batch->variant_id
                                )
                                ->where(
                                    'batch_id',
                                    $batch->id
                                )
                                ->where(
                                    'source_type',
                                    'initial'
                                )
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
     * ============================================================
     * СЕБЕСТОИМОСТЬ СЛОЯ
     * ============================================================
     */
    public function getLayerUnitCost(
        InventoryLayer $layer
    ): float {

        /*
         * INITIAL
         *
         * Для начального слоя используем purchase_price
         * товара.
         */
        if ($layer->source_type === 'initial') {

            $layer->loadMissing(
                'variant.product'
            );

            return (float) (
                $layer->variant
                    ->product
                    ->purchase_price ?? 0
            );
        }

        /*
         * RECEIPT / RETURN и другие слои
         *
         * используют собственную себестоимость слоя.
         */
        return (float) (
            $layer->unit_cost ?? 0
        );
    }


    /**
     * ============================================================
     * ОБЫЧНЫЕ FIFO-СЛОИ ТОВАРА
     * ============================================================
     *
     * Используется обычным FIFO, например продажами.
     */
    public function getAvailableLayers(
        int $warehouseId,
        int $variantId
    ) {

        return InventoryLayer::with([
            'variant.product',
            'batch',
        ])
            ->where(
                'warehouse_id',
                $warehouseId
            )
            ->where(
                'variant_id',
                $variantId
            )
            ->orderByRaw("
                CASE
                    WHEN source_type = 'initial' THEN 0
                    ELSE 1
                END
            ")
            ->orderBy(
                'layer_date'
            )
            ->orderBy(
                'id'
            )
            ->get();
    }


    /**
     * ============================================================
     * FIFO-СЛОИ КОНКРЕТНОЙ ПАРТИИ
     * ============================================================
     *
     * Используется списаниями.
     *
     * Порядок:
     *
     * 1. initial
     * 2. самая старая приёмка
     * 3. следующая приёмка
     * 4. ...
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
            ->where(
                'warehouse_id',
                $warehouseId
            )
            ->where(
                'variant_id',
                $variantId
            )
            ->where(
                'batch_id',
                $batchId
            )
            ->orderByRaw("
                CASE
                    WHEN source_type = 'initial' THEN 0
                    ELSE 1
                END
            ")
            ->orderBy(
                'layer_date'
            )
            ->orderBy(
                'id'
            )
            ->get();
    }


    /**
     * ============================================================
     * СКОЛЬКО УЖЕ ПОТРЕБИЛИ ИЗ СЛОЯ
     * ============================================================
     */
    public function getConsumedQuantity(
        InventoryLayer $layer
    ): int {

        return (int) FifoAllocation::where(
            'inventory_layer_id',
            $layer->id
        )->sum(
            'quantity'
        );
    }


    /**
     * ============================================================
     * ОСТАТОК КОНКРЕТНОГО FIFO-СЛОЯ
     * ============================================================
     */
    public function getRemainingQuantity(
        InventoryLayer $layer
    ): int {

        $consumed =
            $this->getConsumedQuantity(
                $layer
            );

        return max(
            0,
            (int) $layer->quantity - $consumed
        );
    }


    /**
     * ============================================================
     * ОБЫЧНЫЙ FIFO-РАСЧЁТ
     * ============================================================
     *
     * Используется продажами и старой логикой.
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

        $layers =
            $this->getAvailableLayers(
                $warehouseId,
                $variantId
            );

        $remainingToConsume =
            $quantity;

        $result = [];

        $totalCost = 0;

        foreach ($layers as $layer) {

            if ($remainingToConsume <= 0) {
                break;
            }

            $available =
                $this->getRemainingQuantity(
                    $layer
                );

            if ($available <= 0) {
                continue;
            }

            $take =
                min(
                    $available,
                    $remainingToConsume
                );

            $unitCost =
                $this->getLayerUnitCost(
                    $layer
                );

            $cost =
                $take * $unitCost;

            $result[] = [
                'layer_id' =>
                    $layer->id,

                'batch_id' =>
                    $layer->batch_id,

                'quantity' =>
                    $take,

                'unit_cost' =>
                    $unitCost,

                'cost' =>
                    $cost,

                'source_type' =>
                    $layer->source_type,

                'source_id' =>
                    $layer->source_id,
            ];

            $totalCost +=
                $cost;

            $remainingToConsume -=
                $take;
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
            'items' =>
                $result,

            'total_cost' =>
                $totalCost,

            'quantity' =>
                $quantity,
        ];
    }


    /**
     * ============================================================
     * РАСЧЁТ FIFO ТОЛЬКО ПО КОНКРЕТНОЙ ПАРТИИ
     * ============================================================
     *
     * ВАЖНО:
     *
     * Другие партии никогда не используются.
     *
     * Порядок:
     *
     * initial
     * ↓
     * первая приёмка
     * ↓
     * вторая приёмка
     * ↓
     * ...
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

        $batch =
            Batch::findOrFail(
                $batchId
            );

        if (
            (int) $batch->variant_id !==
            (int) $variantId
        ) {

            throw new RuntimeException(
                "Партия {$batch->batch_code} " .
                "не принадлежит выбранному товару."
            );
        }

        $layers =
            $this->getAvailableBatchLayers(
                $warehouseId,
                $variantId,
                $batchId
            );

        $remainingToConsume =
            $quantity;

        $result = [];

        $totalCost = 0;

        foreach ($layers as $layer) {

            if ($remainingToConsume <= 0) {
                break;
            }

            $available =
                $this->getRemainingQuantity(
                    $layer
                );

            if ($available <= 0) {
                continue;
            }

            $take =
                min(
                    $available,
                    $remainingToConsume
                );

            $unitCost =
                $this->getLayerUnitCost(
                    $layer
                );

            $cost =
                $take * $unitCost;

            $result[] = [
                'layer_id' =>
                    $layer->id,

                'batch_id' =>
                    $layer->batch_id,

                'quantity' =>
                    $take,

                'unit_cost' =>
                    $unitCost,

                'cost' =>
                    $cost,

                'source_type' =>
                    $layer->source_type,

                'source_id' =>
                    $layer->source_id,
            ];

            $totalCost +=
                $cost;

            $remainingToConsume -=
                $take;
        }

        if ($remainingToConsume > 0) {

            throw new RuntimeException(
                "Недостаточно FIFO-остатка в партии " .
                "{$batch->batch_code}. " .
                "Не хватает {$remainingToConsume} шт."
            );
        }

        return [
            'items' =>
                $result,

            'total_cost' =>
                $totalCost,

            'quantity' =>
                $quantity,
        ];
    }


    /**
     * ============================================================
     * ОБЫЧНОЕ СПИСАНИЕ FIFO
     * ============================================================
     *
     * Используется:
     *
     * sale
     * writeoff старого типа
     */
    public function consume(
        int $warehouseId,
        int $variantId,
        int $quantity,
        string $type,
        ?int $sourceId = null,
        ?string $movementDate = null
    ): StockMovement {

        if (!in_array(
            $type,
            [
                'sale',
                'writeoff',
            ],
            true
        )) {

            throw new RuntimeException(
                'Недопустимый тип FIFO-списания.'
            );
        }

        if ($quantity <= 0) {

            throw new RuntimeException(
                'Количество списания должно быть больше нуля.'
            );
        }

        return DB::transaction(
            function () use (
                $warehouseId,
                $variantId,
                $quantity,
                $type,
                $sourceId,
                $movementDate
            ) {

                $fifo =
                    $this->calculateFifo(
                        $warehouseId,
                        $variantId,
                        $quantity
                    );

                $movement =
                    StockMovement::create([
                        'warehouse_id' =>
                            $warehouseId,

                        'variant_id' =>
                            $variantId,

                        'batch_id' =>
                            $fifo['items'][0]['batch_id'],

                        'type' =>
                            $type,

                        'source_id' =>
                            $sourceId,

                        'quantity' =>
                            $quantity,

                        'direction' =>
                            -1,

                        'movement_date' =>
                            $movementDate
                            ? $movementDate
                            : now('Asia/Almaty'),
                    ]);

                foreach (
                    $fifo['items']
                    as $item
                ) {

                    $batch =
                        Batch::findOrFail(
                            $item['batch_id']
                        );

                    $this->changeWarehouseQuantity(
                        $batch,
                        $warehouseId,
                        -$item['quantity']
                    );

                    FifoAllocation::create([
                        'stock_movement_id' =>
                            $movement->id,

                        'inventory_layer_id' =>
                            $item['layer_id'],

                        'quantity' =>
                            $item['quantity'],
                    ]);
                }

                return $movement;
            }
        );
    }


    /**
     * ============================================================
     * СПИСАНИЕ КОНКРЕТНОЙ ПАРТИИ
     * ============================================================
     *
     * ВАЖНО:
     *
     * batchId здесь является обязательным.
     *
     * Система НЕ имеет права взять товар из другой партии.
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

        return DB::transaction(
            function () use (
                $warehouseId,
                $variantId,
                $batchId,
                $quantity,
                $type,
                $sourceId,
                $movementDate
            ) {

                $batch =
                    Batch::findOrFail(
                        $batchId
                    );

                if (
                    (int) $batch->variant_id !==
                    (int) $variantId
                ) {

                    throw new RuntimeException(
                        "Партия {$batch->batch_code} " .
                        "не принадлежит выбранному товару."
                    );
                }

                /*
                 * Сначала проверяем физический остаток
                 * конкретной партии.
                 */
                $warehouse =
                    $batch->warehouses()
                        ->where(
                            'warehouse_id',
                            $warehouseId
                        )
                        ->first();

                if (!$warehouse) {

                    throw new RuntimeException(
                        "Партия {$batch->batch_code} " .
                        "отсутствует на выбранном складе."
                    );
                }

                $physicalQuantity =
                    (int) (
                        $warehouse
                            ->pivot
                            ->quantity ?? 0
                    );

                if (
                    $quantity >
                    $physicalQuantity
                ) {

                    throw new RuntimeException(
                        "Недостаточно товара партии " .
                        "{$batch->batch_code}. " .
                        "Доступно: {$physicalQuantity} шт."
                    );
                }

                /*
                 * FIFO только внутри выбранной партии.
                 */
                $fifo =
                    $this->calculateBatchFifo(
                        $warehouseId,
                        $variantId,
                        $batchId,
                        $quantity
                    );

                /*
                 * Создаём движение.
                 */
                $movement =
                    StockMovement::create([
                        'warehouse_id' =>
                            $warehouseId,

                        'variant_id' =>
                            $variantId,

                        'batch_id' =>
                            $batchId,

                        'type' =>
                            $type,

                        'source_id' =>
                            $sourceId,

                        'quantity' =>
                            $quantity,

                        'direction' =>
                            -1,

                        'movement_date' =>
                            $movementDate
                            ? $movementDate
                            : now('Asia/Almaty'),
                    ]);

                /*
                 * Физически уменьшаем именно выбранную партию.
                 */
                $this->changeWarehouseQuantity(
                    $batch,
                    $warehouseId,
                    -$quantity
                );

                /*
                 * Фиксируем, из каких InventoryLayer
                 * реально была взята себестоимость.
                 */
                foreach (
                    $fifo['items']
                    as $item
                ) {

                    FifoAllocation::create([
                        'stock_movement_id' =>
                            $movement->id,

                        'inventory_layer_id' =>
                            $item['layer_id'],

                        'quantity' =>
                            $item['quantity'],
                    ]);
                }

                return $movement;
            }
        );
    }


    /**
     * ============================================================
     * ИЗМЕНЕНИЕ ФИЗИЧЕСКОГО ОСТАТКА ПАРТИИ
     * ============================================================
     */
    public function changeWarehouseQuantity(
        Batch $batch,
        int $warehouseId,
        int $difference
    ): void {

        $pivot =
            $batch->warehouses()
                ->where(
                    'warehouse_id',
                    $warehouseId
                )
                ->first();

        $current =
            $pivot
            ? (int) (
                $pivot->pivot->quantity ?? 0
            )
            : 0;

        $newQuantity =
            $current + $difference;

        if ($newQuantity < 0) {

            throw new RuntimeException(
                "Недостаточно товара партии {$batch->batch_code} " .
                "на выбранном складе."
            );
        }

        if ($pivot) {

            $batch->warehouses()
                ->updateExistingPivot(
                    $warehouseId,
                    [
                        'quantity' =>
                            $newQuantity,
                    ]
                );

        } else {

            if ($difference < 0) {

                throw new RuntimeException(
                    'Партия отсутствует на выбранном складе.'
                );
            }

            $batch->warehouses()
                ->attach(
                    $warehouseId,
                    [
                        'quantity' =>
                            $difference,
                    ]
                );
        }
    }


    /**
     * ============================================================
     * ПЕРЕРАСЧЁТ FIFO
     * ============================================================
     *
     * Ключевая логика:
     *
     * SALE:
     *     общий FIFO по товару + складу.
     *
     * WRITEOFF:
     *     только конкретный batch_id.
     *
     * Для writeoff:
     *
     *     initial
     *     ↓
     *     receipt #1
     *     ↓
     *     receipt #2
     *     ↓
     *     ...
     *
     * ВАЖНО:
     *
     * Перерасчёт идёт в хронологическом порядке.
     * Поэтому более раннее движение имеет приоритет
     * над более поздним.
     */
    public function rebuildAllocations(
        ?int $warehouseId = null,
        ?int $variantId = null
    ): void {

        DB::transaction(
            function () use (
                $warehouseId,
                $variantId
            ) {

                /*
                 * Получаем движения, которые используют FIFO.
                 */
                $movementQuery =
                    StockMovement::query()
                        ->whereIn(
                            'type',
                            [
                                'sale',
                                'writeoff',
                            ]
                        )
                        ->orderBy(
                            'movement_date'
                        )
                        ->orderBy(
                            'id'
                        );

                if ($warehouseId !== null) {

                    $movementQuery->where(
                        'warehouse_id',
                        $warehouseId
                    );
                }

                if ($variantId !== null) {

                    $movementQuery->where(
                        'variant_id',
                        $variantId
                    );
                }

                $movements =
                    $movementQuery->get();


                /*
                 * Сначала полностью удаляем старые allocations
                 * только для выбранных движений.
                 */
                foreach ($movements as $movement) {

                    FifoAllocation::where(
                        'stock_movement_id',
                        $movement->id
                    )->delete();
                }


                /*
                 * Здесь храним, сколько уже забрали
                 * из каждого InventoryLayer.
                 *
                 * Пример:
                 *
                 * layer #10 => 7
                 * layer #11 => 3
                 */
                $consumed = [];


                foreach ($movements as $movement) {

                    /*
                     * ====================================================
                     * 1. ОПРЕДЕЛЯЕМ FIFO-СЛОИ
                     * ====================================================
                     */

                    if (
                        $movement->type === 'writeoff'
                        &&
                        !empty($movement->batch_id)
                    ) {

                        /*
                         * СПИСАНИЕ
                         *
                         * Только конкретная партия.
                         */
                        $layers =
                            InventoryLayer::where(
                                'warehouse_id',
                                $movement->warehouse_id
                            )
                                ->where(
                                    'variant_id',
                                    $movement->variant_id
                                )
                                ->where(
                                    'batch_id',
                                    $movement->batch_id
                                )
                                ->orderByRaw("
                                    CASE
                                        WHEN source_type = 'initial'
                                        THEN 0
                                        ELSE 1
                                    END
                                ")
                                ->orderBy(
                                    'layer_date'
                                )
                                ->orderBy(
                                    'id'
                                )
                                ->get();

                    } else {

                        /*
                         * ПРОДАЖА
                         *
                         * Обычный FIFO по товару и складу.
                         */
                        $layers =
                            InventoryLayer::where(
                                'warehouse_id',
                                $movement->warehouse_id
                            )
                                ->where(
                                    'variant_id',
                                    $movement->variant_id
                                )
                                ->orderByRaw("
                                    CASE
                                        WHEN source_type = 'initial'
                                        THEN 0
                                        ELSE 1
                                    END
                                ")
                                ->orderBy(
                                    'layer_date'
                                )
                                ->orderBy(
                                    'id'
                                )
                                ->get();
                    }


                    /*
                     * Сколько нужно распределить.
                     */
                    $remaining =
                        (int) $movement->quantity;


                    /*
                     * ====================================================
                     * 2. РАСПРЕДЕЛЯЕМ ПО СЛОЯМ
                     * ====================================================
                     */
                    foreach ($layers as $layer) {

                        if ($remaining <= 0) {
                            break;
                        }


                        /*
                         * Сколько уже было использовано
                         * из этого слоя предыдущими движениями.
                         */
                        $alreadyConsumed =
                            $consumed[$layer->id]
                            ?? 0;


                        /*
                         * Реально доступный остаток слоя
                         * на момент перерасчёта.
                         */
                        $available =
                            max(
                                0,
                                (int) $layer->quantity -
                                $alreadyConsumed
                            );


                        if ($available <= 0) {
                            continue;
                        }


                        /*
                         * Сколько берём из этого слоя.
                         */
                        $take =
                            min(
                                $available,
                                $remaining
                            );


                        /*
                         * Создаём allocation.
                         */
                        FifoAllocation::create([
                            'stock_movement_id' =>
                                $movement->id,

                            'inventory_layer_id' =>
                                $layer->id,

                            'quantity' =>
                                $take,
                        ]);


                        /*
                         * Запоминаем расход слоя.
                         */
                        $consumed[$layer->id] =
                            $alreadyConsumed +
                            $take;


                        /*
                         * Уменьшаем оставшееся количество
                         * текущего движения.
                         */
                        $remaining -=
                            $take;
                    }


                    /*
                     * ====================================================
                     * 3. ПРОВЕРКА
                     * ====================================================
                     */
                    if ($remaining > 0) {

                        if (
                            $movement->type === 'writeoff'
                            &&
                            $movement->batch_id
                        ) {

                            $batch =
                                Batch::find(
                                    $movement->batch_id
                                );

                            $batchCode =
                                $batch
                                ? $batch->batch_code
                                : $movement->batch_id;

                            throw new RuntimeException(
                                "После перерасчёта FIFO недостаточно " .
                                "товара в партии {$batchCode} " .
                                "для движения #{$movement->id}. " .
                                "Не хватает {$remaining} шт."
                            );
                        }

                        throw new RuntimeException(
                            "После перерасчёта FIFO недостаточно " .
                            "товара для движения #{$movement->id}. " .
                            "Не хватает {$remaining} шт."
                        );
                    }
                }
            }
        );
    }


    /**
     * ============================================================
     * СЕБЕСТОИМОСТЬ ДВИЖЕНИЯ
     * ============================================================
     */
    public function getMovementCost(
        StockMovement $movement
    ): float {

        $movement->loadMissing(
            'allocations.layer.variant.product'
        );

        $total = 0;

        foreach (
            $movement->allocations
            as $allocation
        ) {

            if (!$allocation->layer) {
                continue;
            }

            $unitCost =
                $this->getLayerUnitCost(
                    $allocation->layer
                );

            $total +=
                $allocation->quantity *
                $unitCost;
        }

        return $total;
    }


    /**
     * ============================================================
     * ОТКАТ ДВИЖЕНИЯ
     * ============================================================
     */
    public function reverseMovement(
        StockMovement $movement
    ): void {

        DB::transaction(
            function () use ($movement) {

                $movement->load([
                    'allocations.layer',
                ]);

                foreach (
                    $movement->allocations
                    as $allocation
                ) {

                    $layer =
                        $allocation->layer;

                    if (!$layer) {
                        continue;
                    }

                    $batch =
                        Batch::find(
                            $layer->batch_id
                        );

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
            }
        );
    }


    /**
     * ============================================================
     * ВОЗВРАТ НА СКЛАД
     * ============================================================
     *
     * Возврат всегда идёт в конкретную партию.
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

        return DB::transaction(
            function () use (
                $warehouseId,
                $variantId,
                $quantity,
                $batchId,
                $sourceId,
                $movementDate
            ) {

                $batch =
                    Batch::findOrFail(
                        $batchId
                    );

                if (
                    (int) $batch->variant_id !==
                    (int) $variantId
                ) {

                    throw new RuntimeException(
                        "Партия {$batch->batch_code} " .
                        "не принадлежит выбранному товару."
                    );
                }

                /*
                 * Увеличиваем физический остаток
                 * конкретной партии.
                 */
                $this->changeWarehouseQuantity(
                    $batch,
                    $warehouseId,
                    $quantity
                );


                /*
                 * Создаём движение возврата.
                 */
                $movement =
                    StockMovement::create([
                        'warehouse_id' =>
                            $warehouseId,

                        'variant_id' =>
                            $variantId,

                        'batch_id' =>
                            $batchId,

                        'type' =>
                            'return',

                        'source_id' =>
                            $sourceId,

                        'quantity' =>
                            $quantity,

                        'direction' =>
                            1,

                        'movement_date' =>
                            $movementDate
                            ? $movementDate
                            : now('Asia/Almaty'),
                    ]);


                /*
                 * Определяем себестоимость возврата.
                 *
                 * Берём последнюю receipt-себестоимость
                 * этого товара на складе.
                 *
                 * Если её нет — purchase_price.
                 */
                $receiptLayer =
                    InventoryLayer::where(
                        'warehouse_id',
                        $warehouseId
                    )
                        ->where(
                            'variant_id',
                            $variantId
                        )
                        ->where(
                            'source_type',
                            'receipt'
                        )
                        ->orderByDesc(
                            'layer_date'
                        )
                        ->orderByDesc(
                            'id'
                        )
                        ->first();

                if ($receiptLayer) {

                    $unitCost =
                        (float) (
                            $receiptLayer->unit_cost
                            ?? 0
                        );

                } else {

                    $batch->loadMissing(
                        'variant.product'
                    );

                    $unitCost =
                        (float) (
                            $batch
                                ->variant
                                ->product
                                ->purchase_price
                            ?? 0
                        );
                }


                /*
                 * Возврат создаёт новый FIFO-слой.
                 *
                 * Он попадёт в FIFO после initial,
                 * по своей дате.
                 */
                InventoryLayer::create([
                    'warehouse_id' =>
                        $warehouseId,

                    'variant_id' =>
                        $variantId,

                    'batch_id' =>
                        $batchId,

                    'source_type' =>
                        'return',

                    'source_id' =>
                        $sourceId,

                    'quantity' =>
                        $quantity,

                    'unit_cost' =>
                        $unitCost,

                    'layer_date' =>
                        $movementDate
                        ? $movementDate
                        : now('Asia/Almaty'),
                ]);

                return $movement;
            }
        );
    }
}

