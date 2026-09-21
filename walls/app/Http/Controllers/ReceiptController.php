<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\InventoryLayer;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Variant;
use App\Models\Warehouse;
use App\Services\FifoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReceiptController extends Controller
{
    /**
     * Список приёмок.
     */
    public function index(Request $request)
    {
        $today = now('Asia/Almaty');

        $from = $request->filled('from')
            ? $request->input('from')
            : $today->copy()->startOfMonth()->format('Y-m-d');

        $to = $request->filled('to')
            ? $request->input('to')
            : $today->copy()->format('Y-m-d');

        $receipts = Receipt::with([
            'warehouse',
            'items.variant.product',
            'items.batch',
        ])
            ->when(
                $request->filled('warehouse_id'),
                function ($query) use ($request) {
                    $query->where(
                        'warehouse_id',
                        $request->warehouse_id
                    );
                }
            )
            ->whereDate('receipt_date', '>=', $from)
            ->whereDate('receipt_date', '<=', $to)
            ->orderByDesc('receipt_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get();

        return view(
            'admin.receipts.index',
            compact(
                'receipts',
                'warehouses',
                'from',
                'to'
            )
        );
    }

    /**
     * Страница создания приёмки.
     *
     * Показываем ВСЕ товары:
     * - скрытые и обычные;
     * - с партией и без партии;
     * - с остатком и без остатка.
     *
     * Если у товара нет партии, её можно создать
     * через кнопку в интерфейсе.
     */
    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();

        $variants = Variant::with([
            'product',
            'batches',
        ])
            ->orderBy('sku')
            ->get()
            ->map(function ($variant) {

                // Последняя цена прихода по этому варианту.
                $lastReceiptItem = ReceiptItem::query()
                    ->where('variant_id', $variant->id)
                    ->whereHas('receipt')
                    ->with('receipt')
                    ->orderByDesc(
                        Receipt::select('receipt_date')
                            ->whereColumn(
                                'receipts.id',
                                'receipt_items.receipt_id'
                            )
                    )
                    ->orderByDesc('id')
                    ->first();

                // Если товар уже приходил —
                // берём последнюю цену прихода.
                //
                // Если товар ещё ни разу не приходил —
                // берём purchase_price товара.
                $purchasePrice = $lastReceiptItem
                    ? (float) $lastReceiptItem->purchase_price
                    : (float) (
                        $variant->product->purchase_price ?? 0
                    );

                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'name' => $variant->product->name ?? '',
                    'purchase_price' => $purchasePrice,

                    'batches' => $variant->batches
                        ->map(function ($batch) {
                            return [
                                'id' => $batch->id,
                                'code' => $batch->batch_code,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return view(
            'admin.receipts.create',
            compact(
                'warehouses',
                'variants'
            )
        );
    }

    /**
     * Создание приёмки.
     *
     * Партия ОБЯЗАТЕЛЬНА.
     *
     * Если партии нет, пользователь должен
     * сначала создать её через интерфейс.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => [
                'required',
                'exists:warehouses,id',
            ],

            'receipt_date' => [
                'required',
                'date',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.variant_id' => [
                'required',
                'exists:variants,id',
            ],

            'items.*.batch_id' => [
                'required',
                'exists:batches,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        DB::beginTransaction();

        try {
            $receipt = Receipt::create([
                'warehouse_id' => $data['warehouse_id'],
                'receipt_date' => Carbon::parse(
                    $data['receipt_date']
                ),
                'comment' => $data['comment'] ?? null,
            ]);

            foreach ($data['items'] as $item) {

                $variant = Variant::findOrFail(
                    $item['variant_id']
                );

                /*
                 * Партия теперь обязательна.
                 *
                 * Автоматического создания партии здесь нет.
                 */
                $batch = Batch::findOrFail(
                    $item['batch_id']
                );

                /*
                 * Защита от ситуации, когда
                 * пользователь отправил партию
                 * другого артикула.
                 */
                if (
                    (int) $batch->variant_id
                    !==
                    (int) $variant->id
                ) {
                    throw new RuntimeException(
                        "Партия {$batch->batch_code} " .
                            "не принадлежит товару {$variant->sku}."
                    );
                }

                /*
                 * Создаём позицию приёмки.
                 */
                $receiptItem = ReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'variant_id' => $variant->id,
                    'batch_id' => $batch->id,
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                ]);

                /*
                 * Создаём FIFO-слой.
                 */
                InventoryLayer::create([
                    'warehouse_id' => $receipt->warehouse_id,
                    'variant_id' => $variant->id,
                    'batch_id' => $batch->id,
                    'source_type' => 'receipt',
                    'source_id' => $receiptItem->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['purchase_price'],
                    'layer_date' => $receipt->receipt_date
                        ->copy()
                        ->startOfDay(),
                ]);

                /*
                 * Добавляем количество партии
                 * на выбранный склад.
                 */
                $this->changeWarehouseQuantity(
                    $batch,
                    $receipt->warehouse_id,
                    (int) $item['quantity']
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.receipts.index')
                ->with(
                    'success',
                    'Приёмка успешно создана.'
                );
        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'error' =>
                    'Не удалось создать приёмку: '
                        . $e->getMessage(),
                ]);
        }
    }

    /**
     * Просмотр / редактирование приёмки.
     */
    public function edit(Receipt $receipt)
    {
        $receipt->load([
            'warehouse',
            'items.variant.product',
            'items.batch',
            'items.inventoryLayer',
        ]);

        /*
         * Показываем ВСЕ товары.
         *
         * В том числе скрытые товары.
         */
        $variants = Variant::with([
            'product',
            'batches',
        ])
            ->orderBy('sku')
            ->get()
            ->map(function ($variant) {

                $lastReceiptItem = ReceiptItem::query()
                    ->where(
                        'variant_id',
                        $variant->id
                    )
                    ->whereHas('receipt')
                    ->with('receipt')
                    ->orderByDesc(
                        Receipt::select('receipt_date')
                            ->whereColumn(
                                'receipts.id',
                                'receipt_items.receipt_id'
                            )
                    )
                    ->orderByDesc('id')
                    ->first();

                $purchasePrice = $lastReceiptItem
                    ? (float) $lastReceiptItem->purchase_price
                    : (float) (
                        $variant->product->purchase_price ?? 0
                    );

                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'name' => $variant->product->name ?? '',
                    'purchase_price' => $purchasePrice,

                    'batches' => $variant->batches
                        ->map(function ($batch) {
                            return [
                                'id' => $batch->id,
                                'code' => $batch->batch_code,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return view(
            'admin.receipts.edit',
            compact(
                'receipt',
                'variants'
            )
        );
    }

    /**
     * Обновление приёмки.
     *
     * Дата и склад не изменяются.
     */
    public function update(
        Request $request,
        Receipt $receipt,
        FifoService $fifoService
    ) {
        $data = $request->validate([
            'comment' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
             * Уже существующие позиции.
             */
            'items' => [
                'nullable',
                'array',
            ],

            'items.*.id' => [
                'required',
                'integer',
                'exists:receipt_items,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            /*
             * Новые позиции.
             *
             * Партия ОБЯЗАТЕЛЬНА.
             */
            'new_items' => [
                'nullable',
                'array',
            ],

            'new_items.*.variant_id' => [
                'required',
                'exists:variants,id',
            ],

            'new_items.*.batch_id' => [
                'required',
                'exists:batches,id',
            ],

            'new_items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'new_items.*.purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        try {

            DB::transaction(
                function () use (
                    $data,
                    $receipt,
                    $fifoService
                ) {

                    /*
                     * Обновляем комментарий.
                     */
                    $receipt->update([
                        'comment' =>
                        $data['comment'] ?? null,
                    ]);

                    /*
                     * Обновляем существующие позиции.
                     */
                    foreach (
                        $data['items'] ?? []
                        as $itemData
                    ) {

                        $receiptItem =
                            ReceiptItem::where(
                                'receipt_id',
                                $receipt->id
                            )->findOrFail(
                                $itemData['id']
                            );

                        $layer =
                            InventoryLayer::where(
                                'source_type',
                                'receipt'
                            )
                            ->where(
                                'source_id',
                                $receiptItem->id
                            )
                            ->first();

                        $oldQuantity =
                            (int) $receiptItem->quantity;

                        $newQuantity =
                            (int) $itemData['quantity'];

                        /*
                         * Сколько уже было продано
                         * из этого FIFO-слоя.
                         */
                        $consumed = $layer
                            ? $fifoService
                            ->getConsumedQuantity(
                                $layer
                            )
                            : 0;

                        /*
                         * Нельзя уменьшить приход
                         * ниже уже проданного количества.
                         */
                        if ($newQuantity < $consumed) {

                            throw new RuntimeException(
                                "Нельзя уменьшить количество товара " .
                                    "«{$receiptItem->variant->sku}» " .
                                    "ниже {$consumed} шт., " .
                                    "потому что это количество " .
                                    "уже используется в продажах."
                            );
                        }

                        $difference =
                            $newQuantity - $oldQuantity;

                        /*
                         * Меняем остаток на складе.
                         */
                        if ($difference !== 0) {

                            $this->changeWarehouseQuantity(
                                $receiptItem->batch,
                                $receipt->warehouse_id,
                                $difference
                            );
                        }

                        /*
                         * Обновляем позицию приёмки.
                         */
                        $receiptItem->update([
                            'quantity' => $newQuantity,
                            'purchase_price' =>
                            $itemData['purchase_price'],
                        ]);

                        /*
                         * Обновляем FIFO-слой.
                         */
                        if ($layer) {

                            $layer->update([
                                'quantity' => $newQuantity,
                                'unit_cost' =>
                                $itemData['purchase_price'],
                            ]);
                        } else {

                            /*
                             * Если слоя почему-то нет —
                             * создаём его.
                             */
                            InventoryLayer::create([
                                'warehouse_id' =>
                                $receipt->warehouse_id,

                                'variant_id' =>
                                $receiptItem->variant_id,

                                'batch_id' =>
                                $receiptItem->batch_id,

                                'source_type' => 'receipt',

                                'source_id' =>
                                $receiptItem->id,

                                'quantity' =>
                                $newQuantity,

                                'unit_cost' =>
                                $itemData['purchase_price'],

                                'layer_date' =>
                                $receipt->receipt_date
                                    ->copy()
                                    ->startOfDay(),
                            ]);
                        }
                    }

                    /*
                     * Добавляем новые позиции.
                     */
                    foreach (
                        $data['new_items'] ?? []
                        as $newItem
                    ) {

                        $variant = Variant::findOrFail(
                            $newItem['variant_id']
                        );

                        /*
                         * Партия ОБЯЗАТЕЛЬНА.
                         *
                         * Никакого AUTO создания.
                         */
                        $batch = Batch::findOrFail(
                            $newItem['batch_id']
                        );

                        /*
                         * Проверяем принадлежность
                         * партии к выбранному SKU.
                         */
                        if (
                            (int) $batch->variant_id
                            !==
                            (int) $variant->id
                        ) {

                            throw new RuntimeException(
                                "Партия {$batch->batch_code} " .
                                    "не принадлежит SKU " .
                                    "{$variant->sku}."
                            );
                        }

                        /*
                         * Создаём позицию.
                         */
                        $receiptItem =
                            ReceiptItem::create([
                                'receipt_id' =>
                                $receipt->id,

                                'variant_id' =>
                                $variant->id,

                                'batch_id' =>
                                $batch->id,

                                'quantity' =>
                                $newItem['quantity'],

                                'purchase_price' =>
                                $newItem['purchase_price'],
                            ]);

                        /*
                         * Добавляем количество на склад.
                         */
                        $this->changeWarehouseQuantity(
                            $batch,
                            $receipt->warehouse_id,
                            (int) $newItem['quantity']
                        );

                        /*
                         * Создаём FIFO-слой.
                         */
                        InventoryLayer::create([
                            'warehouse_id' =>
                            $receipt->warehouse_id,

                            'variant_id' =>
                            $variant->id,

                            'batch_id' =>
                            $batch->id,

                            'source_type' =>
                            'receipt',

                            'source_id' =>
                            $receiptItem->id,

                            'quantity' =>
                            $newItem['quantity'],

                            'unit_cost' =>
                            $newItem['purchase_price'],

                            'layer_date' =>
                            $receipt->receipt_date
                                ->copy()
                                ->startOfDay(),
                        ]);
                    }
                }
            );
        } catch (RuntimeException $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'receipt_error',
                    $e->getMessage()
                );
        }

        return redirect()
            ->route(
                'admin.receipts.edit',
                $receipt
            )
            ->with(
                'success',
                'Приёмка успешно обновлена.'
            );
    }

    /**
     * Удаление приёмки.
     */
    public function destroy(
        Receipt $receipt,
        FifoService $fifoService
    ) {
        DB::transaction(function () use (
            $receipt,
            $fifoService
        ) {

            $receipt->load([
                'items.batch',
            ]);

            foreach ($receipt->items as $receiptItem) {

                $layer =
                    InventoryLayer::where(
                        'source_type',
                        'receipt'
                    )
                    ->where(
                        'source_id',
                        $receiptItem->id
                    )
                    ->first();

                if (!$layer) {
                    continue;
                }

                /*
                 * Сколько уже использовано
                 * в продажах.
                 */
                $consumed =
                    $fifoService->getConsumedQuantity(
                        $layer
                    );

                /*
                 * Остаток, который можно вернуть
                 * со склада.
                 */
                $remaining = max(
                    0,
                    (int) $layer->quantity
                        - $consumed
                );

                if ($remaining > 0) {

                    $this->changeWarehouseQuantity(
                        $receiptItem->batch,
                        $receipt->warehouse_id,
                        -$remaining
                    );
                }

                /*
                 * Удаляем FIFO-распределения.
                 */
                \App\Models\FifoAllocation::where(
                    'inventory_layer_id',
                    $layer->id
                )->delete();

                /*
                 * Удаляем FIFO-слой.
                 */
                $layer->delete();
            }

            /*
             * Удаляем саму приёмку.
             */
            $receipt->delete();
        });

        return redirect()
            ->route('admin.receipts.index')
            ->with(
                'success',
                'Приёмка удалена.'
            );
    }

    /**
     * Изменение количества товара на складе.
     */
    private function changeWarehouseQuantity(
        Batch $batch,
        int $warehouseId,
        int $difference
    ): void {

        $pivot = $batch->warehouses()
            ->where(
                'warehouse_id',
                $warehouseId
            )
            ->first();

        $current = $pivot
            ? (int) (
                $pivot->pivot->quantity ?? 0
            )
            : 0;

        $newQuantity =
            $current + $difference;

        /*
         * Нельзя получить отрицательный остаток.
         */
        if ($newQuantity < 0) {

            throw new RuntimeException(
                "Невозможно изменить остаток партии " .
                    "{$batch->batch_code}. " .
                    "Остаток на складе недостаточен."
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

            /*
             * Если записи на складе ещё нет,
             * нельзя списывать из неё.
             */
            if ($difference < 0) {

                throw new RuntimeException(
                    'Партия отсутствует на выбранном складе.'
                );
            }

            /*
             * Если это приход — создаём
             * запись в pivot.
             */
            $batch->warehouses()->attach(
                $warehouseId,
                [
                    'quantity' =>
                    $difference,
                ]
            );
        }
    }

    /**
     * Просмотр приёмки.
     */
    public function show(Receipt $receipt)
    {
        $receipt->load([
            'warehouse',
            'items.variant.product',
            'items.batch',
        ]);

        return view(
            'admin.receipts.show',
            compact('receipt')
        );
    }

    /**
     * Удаление отдельной позиции из приёмки.
     */
    public function destroyItem(
        Receipt $receipt,
        ReceiptItem $receiptItem,
        FifoService $fifoService
    ) {

        try {

            DB::transaction(function () use (
                $receipt,
                $receiptItem,
                $fifoService
            ) {

                /*
                 * Проверяем, что позиция принадлежит
                 * именно этой приёмке.
                 */
                if (
                    (int) $receiptItem->receipt_id
                    !==
                    (int) $receipt->id
                ) {

                    throw new RuntimeException(
                        'Эта позиция не принадлежит ' .
                            'выбранной приёмке.'
                    );
                }

                /*
                 * Находим FIFO-слой.
                 */
                $layer =
                    InventoryLayer::where(
                        'source_type',
                        'receipt'
                    )
                    ->where(
                        'source_id',
                        $receiptItem->id
                    )
                    ->first();

                /*
                 * Проверяем, использовался ли товар
                 * в продажах.
                 */
                $consumed = $layer
                    ? $fifoService
                    ->getConsumedQuantity(
                        $layer
                    )
                    : 0;

                if ($consumed > 0) {

                    $sku = $receiptItem->variant
                        ? $receiptItem->variant->sku
                        : $receiptItem->variant_id;

                    throw new RuntimeException(
                        "Нельзя удалить SKU {$sku}. " .
                            "Из этой позиции уже использовано " .
                            "{$consumed} шт. в продажах."
                    );
                }

                $quantity =
                    (int) $receiptItem->quantity;

                /*
                 * Получаем партию.
                 */
                $batch = Batch::findOrFail(
                    $receiptItem->batch_id
                );

                /*
                 * Списываем количество со склада.
                 */
                if ($quantity > 0) {

                    $this->changeWarehouseQuantity(
                        $batch,
                        $receipt->warehouse_id,
                        -$quantity
                    );
                }

                /*
                 * Удаляем FIFO allocation и слой.
                 */
                if ($layer) {

                    \App\Models\FifoAllocation::where(
                        'inventory_layer_id',
                        $layer->id
                    )->delete();

                    $layer->delete();
                }

                /*
                 * Удаляем позицию приёмки.
                 */
                $receiptItem->delete();
            });
        } catch (RuntimeException $e) {

            return redirect()
                ->route(
                    'admin.receipts.edit',
                    $receipt
                )
                ->with(
                    'receipt_error',
                    $e->getMessage()
                );
        }

        return redirect()
            ->route(
                'admin.receipts.edit',
                $receipt
            )
            ->with(
                'success',
                'Артикул удалён из приёмки, ' .
                    'остаток списан со склада.'
            );
    }
}
