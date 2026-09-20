<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Warehouse;
use App\Models\Variant;
use App\Models\Batch;
use App\Models\InventoryLayer;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use RuntimeException;

class ReceiptController extends Controller
{
    /**
     * Список приёмок
     */
    public function index(Request $request)
    {
        /*
|--------------------------------------------------------------------------
| Период по умолчанию
|--------------------------------------------------------------------------
| От — первое число текущего месяца
| До — сегодняшний день
*/

        $today = now('Asia/Almaty');

        $from = $request->filled('from')
            ? $request->input('from')
            : $today->copy()->startOfMonth()->format('Y-m-d');

        $to = $request->filled('to')
            ? $request->input('to')
            : $today->copy()->format('Y-m-d');


        /*
|--------------------------------------------------------------------------
| Приёмки
|--------------------------------------------------------------------------
*/

        $receipts = Receipt::with([
            'warehouse',
            'items.variant.product',
            'items.batch',
        ])

            /*
    |--------------------------------------------------------------------------
    | Фильтр склада
    |--------------------------------------------------------------------------
    */

            ->when(
                $request->filled('warehouse_id'),
                function ($query) use ($request) {

                    $query->where(
                        'warehouse_id',
                        $request->warehouse_id
                    );
                }
            )

            /*
    |--------------------------------------------------------------------------
    | От
    |--------------------------------------------------------------------------
    */

            ->whereDate(
                'receipt_date',
                '>=',
                $from
            )

            /*
    |--------------------------------------------------------------------------
    | До
    |--------------------------------------------------------------------------
    | whereDate() специально используется здесь,
    | поэтому весь выбранный день включается полностью.
    |--------------------------------------------------------------------------
    */

            ->whereDate(
                'receipt_date',
                '<=',
                $to
            )

            ->orderByDesc('receipt_date')
            ->orderByDesc('id')

            ->paginate(25)

            ->withQueryString();


        /*
|--------------------------------------------------------------------------
| Склады
|--------------------------------------------------------------------------
*/

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
     * Страница создания приёмки
     */
    /**
     * Страница создания приёмки
     */
    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();

        $variants = Variant::with([
            'product',
            'batches',
        ])
            ->whereHas('product', function ($query) {
                $query->where('is_hidden', false);
            })
            ->orderBy('sku')
            ->get()
            ->map(function ($variant) {

                /*
             * =====================================================
             * ПОСЛЕДНЯЯ ЗАКУПОЧНАЯ ЦЕНА ИЗ ПРИЁМКИ
             * =====================================================
             *
             * Ищем последнюю приёмку именно этого SKU.
             *
             * Сначала сортируем по дате приёмки,
             * затем по ID — это гарантирует получение
             * самой последней записи при одинаковой дате.
             */

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


                /*
             * =====================================================
             * ОПРЕДЕЛЯЕМ ЗАКУПОЧНУЮ ЦЕНУ
             * =====================================================
             */

                if ($lastReceiptItem) {

                    /*
                 * Есть предыдущая приёмка.
                 * Берём закупочную цену именно из неё.
                 */

                    $purchasePrice =
                        (float) $lastReceiptItem->purchase_price;
                } else {

                    /*
                 * Приёмок ещё нет.
                 *
                 * Используем цену продажи товара из базы
                 * как первоначальную закупочную цену.
                 *
                 * Основное поле — price.
                 *
                 * Дополнительные варианты оставлены как
                 * безопасный fallback, если в твоей модели
                 * используется другое название поля.
                 */

                    $product = $variant->product;


                    $purchasePrice = 0;


                    if ($product) {

                        if (
                            isset($product->price) &&
                            $product->price !== null
                        ) {

                            $purchasePrice =
                                (float) $product->price;
                        } elseif (
                            isset($product->sale_price) &&
                            $product->sale_price !== null
                        ) {

                            $purchasePrice =
                                (float) $product->sale_price;
                        } elseif (
                            isset($product->selling_price) &&
                            $product->selling_price !== null
                        ) {

                            $purchasePrice =
                                (float) $product->selling_price;
                        } elseif (
                            isset($product->purchase_price) &&
                            $product->purchase_price !== null
                        ) {

                            /*
                         * Последний fallback — старое поле,
                         * если цена продажи в проекте называется
                         * purchase_price.
                         */

                            $purchasePrice =
                                (float) $product->purchase_price;
                        }
                    }
                }


                /*
             * =====================================================
             * ДАННЫЕ ДЛЯ BLADE / AUTOCOMPLETE
             * =====================================================
             */

                return [

                    'id' =>
                    $variant->id,

                    'sku' =>
                    $variant->sku,

                    'name' =>
                    $variant->product->name ?? '',

                    /*
                 * Именно эта цена будет автоматически
                 * подставляться в поле "Закупочная цена".
                 */

                    'purchase_price' =>
                    $purchasePrice,

                    'batches' =>
                    $variant->batches
                        ->map(function ($batch) {

                            return [

                                'id' =>
                                $batch->id,

                                'code' =>
                                $batch->batch_code,

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
     * Создание приёмки
     */
    public function store(
        Request $request,
        FifoService $fifoService
    ) {
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

        DB::transaction(function () use (
            $data,
            $fifoService
        ) {

            $receipt = Receipt::create([
                'warehouse_id' => $data['warehouse_id'],
                'receipt_date' => $data['receipt_date'],
                'comment' => $data['comment'] ?? null,
            ]);

            foreach ($data['items'] as $item) {

                $variant = Variant::findOrFail(
                    $item['variant_id']
                );

                $batch = Batch::findOrFail(
                    $item['batch_id']
                );

                /*
                 * Проверяем, что партия действительно
                 * принадлежит выбранному SKU.
                 */
                if ($batch->variant_id !== $variant->id) {
                    throw new RuntimeException(
                        "Партия {$batch->batch_code} не принадлежит SKU {$variant->sku}."
                    );
                }

                /*
                 * Создаём строку приёмки.
                 */
                $receiptItem = ReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'variant_id' => $variant->id,
                    'batch_id' => $batch->id,
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                ]);

                /*
                 * Увеличиваем физический остаток
                 * партии на складе.
                 */
                $pivot = $batch->warehouses()
                    ->where('warehouse_id', $receipt->warehouse_id)
                    ->first();

                if ($pivot) {

                    $currentQuantity = (int) (
                        $pivot->pivot->quantity ?? 0
                    );

                    $batch->warehouses()->updateExistingPivot(
                        $receipt->warehouse_id,
                        [
                            'quantity' =>
                            $currentQuantity +
                                (int) $item['quantity'],
                        ]
                    );
                } else {

                    $batch->warehouses()->attach(
                        $receipt->warehouse_id,
                        [
                            'quantity' => $item['quantity'],
                        ]
                    );
                }

                /*
                 * Создаём FIFO layer.
                 *
                 * Цена приёмки фиксируется здесь.
                 */
                InventoryLayer::create([
                    'warehouse_id' => $receipt->warehouse_id,
                    'variant_id' => $variant->id,
                    'batch_id' => $batch->id,
                    'source_type' => 'receipt',
                    'source_id' => $receiptItem->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['purchase_price'],
                    'layer_date' =>
                    $receipt->receipt_date
                        ->copy()
                        ->startOfDay(),
                ]);
            }
        });

        return redirect()
            ->route('admin.receipts.index')
            ->with(
                'success',
                'Приёмка успешно создана.'
            );
    }


    /**
     * Просмотр / редактирование приёмки
     */
public function edit(Receipt $receipt)
{
    $receipt->load([
        'warehouse',
        'items.variant.product',
        'items.batch',
        'items.inventoryLayer',
    ]);

    $variants = Variant::with([
        'product',
        'batches',
    ])
        ->whereHas('product', function ($query) {
            $query->where('is_hidden', false);
        })
        ->orderBy('sku')
        ->get()
        ->map(function ($variant) {

            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'name' => $variant->product->name ?? '',

                /*
                 * Здесь можешь оставить нужное поле
                 * цены товара из твоей БД.
                 */
                'purchase_price' => $variant->product->purchase_price ?? 0,

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
     * Дату намеренно не изменяем.
    /**
 * Обновление приёмки.
 *
 * Можно:
 * - изменять количество существующих товаров;
 * - изменять закупочную цену;
 * - добавлять новые товары;
 * - добавлять новые партии;
 *
 * Дату намеренно не изменяем.
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
         * Существующие товары
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
         * Новые товары
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

        DB::transaction(function () use (
            $data,
            $receipt,
            $fifoService
        ) {

            /*
             * Обновляем комментарий
             */
            $receipt->update([
                'comment' => $data['comment'] ?? null,
            ]);


            /*
             * ==========================================
             * ОБНОВЛЕНИЕ СУЩЕСТВУЮЩИХ ТОВАРОВ
             * ==========================================
             */

            foreach ($data['items'] ?? [] as $itemData) {

                $receiptItem = ReceiptItem::where(
                    'receipt_id',
                    $receipt->id
                )->findOrFail($itemData['id']);


                $layer = InventoryLayer::where(
                    'source_type',
                    'receipt'
                )
                    ->where(
                        'source_id',
                        $receiptItem->id
                    )
                    ->first();


                $oldQuantity = (int) $receiptItem->quantity;

                $newQuantity = (int) $itemData['quantity'];


                /*
                 * Сколько уже было использовано
                 * в продажах FIFO.
                 */
                $consumed = $layer
                    ? $fifoService->getConsumedQuantity($layer)
                    : 0;


                /*
                 * Нельзя уменьшить количество ниже
                 * уже проданного.
                 */
                if ($newQuantity < $consumed) {

                    throw new RuntimeException(
                        "Нельзя уменьшить количество товара «{$receiptItem->variant->sku}» " .
                        "ниже {$consumed} шт., потому что это количество уже используется в продажах."
                    );
                }


                $difference = $newQuantity - $oldQuantity;


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
                 * Обновляем ReceiptItem.
                 */
                $receiptItem->update([
                    'quantity' => $newQuantity,
                    'purchase_price' => $itemData['purchase_price'],
                ]);


                /*
                 * Обновляем FIFO layer.
                 */
                if ($layer) {

                    $layer->update([
                        'quantity' => $newQuantity,
                        'unit_cost' => $itemData['purchase_price'],
                    ]);

                } else {

                    /*
                     * На всякий случай создаём слой,
                     * если старой записи почему-то нет.
                     */
                    InventoryLayer::create([
                        'warehouse_id' => $receipt->warehouse_id,
                        'variant_id' => $receiptItem->variant_id,
                        'batch_id' => $receiptItem->batch_id,
                        'source_type' => 'receipt',
                        'source_id' => $receiptItem->id,
                        'quantity' => $newQuantity,
                        'unit_cost' => $itemData['purchase_price'],
                        'layer_date' => $receipt->receipt_date
                            ->copy()
                            ->startOfDay(),
                    ]);
                }
            }


            /*
             * ==========================================
             * ДОБАВЛЕНИЕ НОВЫХ ТОВАРОВ
             * ==========================================
             */

            foreach ($data['new_items'] ?? [] as $newItem) {

                $variant = Variant::findOrFail(
                    $newItem['variant_id']
                );

                $batch = Batch::findOrFail(
                    $newItem['batch_id']
                );


                /*
                 * Проверяем принадлежность партии SKU.
                 */
                if ((int) $batch->variant_id !== (int) $variant->id) {

                    throw new RuntimeException(
                        "Партия {$batch->batch_code} не принадлежит SKU {$variant->sku}."
                    );
                }


                /*
                 * Создаём позицию приёмки.
                 */
                $receiptItem = ReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'variant_id' => $variant->id,
                    'batch_id' => $batch->id,
                    'quantity' => $newItem['quantity'],
                    'purchase_price' => $newItem['purchase_price'],
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
                    'warehouse_id' => $receipt->warehouse_id,
                    'variant_id' => $variant->id,
                    'batch_id' => $batch->id,
                    'source_type' => 'receipt',
                    'source_id' => $receiptItem->id,
                    'quantity' => $newItem['quantity'],
                    'unit_cost' => $newItem['purchase_price'],
                    'layer_date' => $receipt->receipt_date
                        ->copy()
                        ->startOfDay(),
                ]);
            }
        });

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

                $layer = InventoryLayer::where(
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

                $consumed = $fifoService->getConsumedQuantity($layer);

                $remaining = max(
                    0,
                    (int) $layer->quantity - $consumed
                );

                if ($remaining > 0) {

                    $this->changeWarehouseQuantity(
                        $receiptItem->batch,
                        $receipt->warehouse_id,
                        -$remaining
                    );
                }

                \App\Models\FifoAllocation::where(
                    'inventory_layer_id',
                    $layer->id
                )->delete();

                $layer->delete();
            }

            /*
             * receipt_items удалятся каскадно
             * вместе с receipt.
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
            ->where('warehouse_id', $warehouseId)
            ->first();

        $current = $pivot
            ? (int) ($pivot->pivot->quantity ?? 0)
            : 0;

        $newQuantity = $current + $difference;

        if ($newQuantity < 0) {
            throw new RuntimeException(
                "Невозможно изменить остаток партии {$batch->batch_code}. " .
                    "Остаток на складе недостаточен."
            );
        }

        if ($pivot) {

            $batch->warehouses()->updateExistingPivot(
                $warehouseId,
                [
                    'quantity' => $newQuantity,
                ]
            );
        } else {

            if ($difference < 0) {
                throw new RuntimeException(
                    'Партия отсутствует на выбранном складе.'
                );
            }

            $batch->warehouses()->attach(
                $warehouseId,
                [
                    'quantity' => $difference,
                ]
            );
        }
    }


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
            // Проверяем, что позиция действительно принадлежит этой приёмке
            if ((int) $receiptItem->receipt_id !== (int) $receipt->id) {
                throw new RuntimeException(
                    'Эта позиция не принадлежит выбранной приёмке.'
                );
            }

            // Ищем FIFO-слой этой позиции
            $layer = InventoryLayer::where(
                'source_type',
                'receipt'
            )
                ->where(
                    'source_id',
                    $receiptItem->id
                )
                ->first();

            // Сколько уже ушло в продажи
            $consumed = $layer
                ? $fifoService->getConsumedQuantity($layer)
                : 0;

            // Если товар уже участвовал в продаже —
            // полностью удалять позицию нельзя
            if ($consumed > 0) {
                $sku = $receiptItem->variant
                    ? $receiptItem->variant->sku
                    : $receiptItem->variant_id;

                throw new RuntimeException(
                    "Нельзя удалить SKU {$sku}. " .
                    "Из этой позиции уже использовано {$consumed} шт. в продажах."
                );
            }

            $quantity = (int) $receiptItem->quantity;

            // Загружаем партию
            $batch = Batch::findOrFail(
                $receiptItem->batch_id
            );

            // Списываем количество со склада
            if ($quantity > 0) {
                $this->changeWarehouseQuantity(
                    $batch,
                    $receipt->warehouse_id,
                    -$quantity
                );
            }

            // Удаляем FIFO allocations, если они есть
            if ($layer) {
                \App\Models\FifoAllocation::where(
                    'inventory_layer_id',
                    $layer->id
                )->delete();

                // Удаляем FIFO-слой
                $layer->delete();
            }

            // Удаляем позицию из приёмки
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
            'Артикул удалён из приёмки, остаток списан со склада.'
        );
}
}
