<?php

namespace App\Http\Controllers;

use App\Models\Appropriation;
use App\Models\AppropriationItem;
use App\Models\Batch;
use App\Models\FifoAllocation;
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

class AppropriationController extends Controller
{
    /**
     * ============================================================
     * СПИСОК ОПРИХОДОВАНИЙ
     * ============================================================
     */
    public function index(Request $request)
    {
        $today = now('Asia/Almaty');

        $from = $request->filled('from')
            ? $request->input('from')
            : $today->copy()
            ->startOfMonth()
            ->format('Y-m-d');

        $to = $request->filled('to')
            ? $request->input('to')
            : $today->copy()
            ->format('Y-m-d');

        $appropriations = Appropriation::with([
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
            ->whereDate(
                'appropriation_date',
                '>=',
                $from
            )
            ->whereDate(
                'appropriation_date',
                '<=',
                $to
            )
            ->orderByDesc('appropriation_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get();

        return view(
            'admin.appropriations.index',
            compact(
                'appropriations',
                'warehouses',
                'from',
                'to'
            )
        );
    }

    /**
     * ============================================================
     * СОЗДАНИЕ
     * ============================================================
     */
    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();

        $variants = $this->getVariantsWithLatestReceiptPrice();

        return view(
            'admin.appropriations.create',
            compact(
                'warehouses',
                'variants'
            )
        );
    }

    /**
     * ============================================================
     * СОХРАНЕНИЕ
     * ============================================================
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

            'appropriation_date' => [
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
        ]);

        try {
            DB::transaction(function () use (
                $data,
                $fifoService
            ) {
                $appropriation = Appropriation::create([
                    'warehouse_id' =>
                    $data['warehouse_id'],

                    'appropriation_date' =>
                    Carbon::parse(
                        $data['appropriation_date']
                    ),

                    'comment' =>
                    $data['comment'] ?? null,
                ]);

                foreach (
                    $data['items']
                    as $item
                ) {
                    $variant = Variant::with(
                        'product'
                    )->findOrFail(
                        $item['variant_id']
                    );

                    $batch = Batch::findOrFail(
                        $item['batch_id']
                    );

                    /**
                     * Партия должна принадлежать SKU.
                     */
                    if (
                        (int) $batch->variant_id
                        !==
                        (int) $variant->id
                    ) {
                        throw new RuntimeException(
                            "Партия {$batch->batch_code} "
                                . "не принадлежит товару "
                                . "{$variant->sku}."
                        );
                    }

                    /**
                     * Получаем себестоимость.
                     */
                    $purchasePrice =
                        $this->getAppropriationPurchasePrice(
                            $variant
                        );

                    if ($purchasePrice === null) {
                        throw new RuntimeException(
                            "Для SKU {$variant->sku} "
                                . "не удалось определить себестоимость."
                        );
                    }

                    /**
                     * Создаём позицию.
                     */
                    $appropriationItem =
                        AppropriationItem::create([
                            'appropriation_id' =>
                            $appropriation->id,

                            'variant_id' =>
                            $variant->id,

                            'batch_id' =>
                            $batch->id,

                            'quantity' =>
                            $item['quantity'],

                            'purchase_price' =>
                            $purchasePrice,
                        ]);

                    /**
                     * Увеличиваем физический остаток.
                     */
                    $fifoService->changeWarehouseQuantity(
                        $batch,
                        $appropriation->warehouse_id,
                        (int) $item['quantity']
                    );

                    /**
                     * Создаём FIFO-слой.
                     */
                    InventoryLayer::create([
                        'warehouse_id' =>
                        $appropriation->warehouse_id,

                        'variant_id' =>
                        $variant->id,

                        'batch_id' =>
                        $batch->id,

                        'source_type' =>
                        'appropriation',

                        'source_id' =>
                        $appropriationItem->id,

                        'quantity' =>
                        $item['quantity'],

                        'unit_cost' =>
                        $purchasePrice,

                        'layer_date' =>
                        $appropriation
                            ->appropriation_date
                            ->copy()
                            ->startOfDay(),
                    ]);
                }
            });
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'error' =>
                    'Не удалось создать оприходование: '
                        . $e->getMessage(),
                ]);
        }

        return redirect()
            ->route(
                'admin.appropriations.index'
            )
            ->with(
                'success',
                'Оприходование успешно создано.'
            );
    }

    /**
     * ============================================================
     * ПРОСМОТР
     * ============================================================
     */
    public function show(
        Appropriation $appropriation
    ) {
        $appropriation->load([
            'warehouse',
            'items.variant.product',
            'items.batch',
        ]);

        return view(
            'admin.appropriations.show',
            compact('appropriation')
        );
    }

    /**
     * ============================================================
     * РЕДАКТИРОВАНИЕ
     * ============================================================
     */
    public function edit(
        Appropriation $appropriation
    ) {
        $appropriation->load([
            'warehouse',
            'items.variant.product',
            'items.batch',
        ]);

        $variants =
            $this->getVariantsWithLatestReceiptPrice();

        return view(
            'admin.appropriations.edit',
            compact(
                'appropriation',
                'variants'
            )
        );
    }

    /**
     * ============================================================
     * UPDATE
     * ============================================================
     *
     * Правила:
     *
     * 1. Количество можно увеличить.
     * 2. Количество можно уменьшить только до значения,
     *    которое ещё не было использовано.
     *
     *    Например:
     *    было 5
     *    использовано 3
     *
     *    5 -> 6   можно
     *    5 -> 5   можно
     *    5 -> 4   можно
     *    5 -> 3   можно
     *    5 -> 2   нельзя
     *
     * 3. Если использовано хотя бы 1 шт.,
     *    менять партию нельзя.
     *
     * 4. SKU существующей позиции менять нельзя.
     *
     * 5. Новую позицию добавить можно.
     */
    public function update(
        Request $request,
        Appropriation $appropriation,
        FifoService $fifoService
    ) {
        $data = $request->validate([
            'comment' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'items' => [
                'nullable',
                'array',
            ],

            'items.*.id' => [
                'nullable',
                'integer',
                'exists:appropriation_items,id',
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
        ]);

        try {
            DB::transaction(function () use (
                $data,
                $appropriation,
                $fifoService
            ) {
                /**
                 * Обновляем комментарий.
                 */
                $appropriation->update([
                    'comment' =>
                    $data['comment'] ?? null,
                ]);

                /**
                 * Обрабатываем все позиции формы.
                 */
                foreach (
                    $data['items'] ?? []
                    as $itemData
                ) {
                    /**
                     * ==================================================
                     * СУЩЕСТВУЮЩАЯ ПОЗИЦИЯ
                     * ==================================================
                     */
                    if (
                        !empty($itemData['id'])
                    ) {
                        $appropriationItem =
                            AppropriationItem::where(
                                'appropriation_id',
                                $appropriation->id
                            )
                            ->findOrFail(
                                $itemData['id']
                            );

                        /**
                         * Ищем FIFO-слой именно этой позиции.
                         */
                        $layer =
                            InventoryLayer::where(
                                'source_type',
                                'appropriation'
                            )
                            ->where(
                                'source_id',
                                $appropriationItem->id
                            )
                            ->first();

                        $oldQuantity =
                            (int) $appropriationItem->quantity;

                        $newQuantity =
                            (int) $itemData['quantity'];

                        $oldBatch =
                            Batch::findOrFail(
                                $appropriationItem->batch_id
                            );

                        $newBatch =
                            Batch::findOrFail(
                                $itemData['batch_id']
                            );

                        /**
                         * Получаем SKU.
                         */
                        $variant =
                            Variant::with(
                                'product'
                            )->findOrFail(
                                $itemData['variant_id']
                            );

                        /**
                         * ==================================================
                         * ПРОВЕРКА ПАРТИИ
                         * ==================================================
                         */
                        if (
                            (int) $newBatch->variant_id
                            !==
                            (int) $variant->id
                        ) {
                            throw new RuntimeException(
                                "Партия {$newBatch->batch_code} "
                                    . "не принадлежит SKU "
                                    . "{$variant->sku}."
                            );
                        }

                        /**
                         * ==================================================
                         * ПРОВЕРКА SKU
                         * ==================================================
                         */
                        if (
                            (int) $appropriationItem->variant_id
                            !==
                            (int) $variant->id
                        ) {
                            throw new RuntimeException(
                                "Нельзя изменить SKU существующей "
                                    . "позиции #{$appropriationItem->id}. "
                                    . "Удалите старую позицию и "
                                    . "добавьте новую."
                            );
                        }

                        /**
                         * ==================================================
                         * СКОЛЬКО УЖЕ ИСПОЛЬЗОВАНО FIFO
                         * ==================================================
                         *
                         * Например:
                         *
                         * Оприходовано = 5
                         * Использовано  = 3
                         * Осталось      = 2
                         */
                        $consumed = 0;

                        if ($layer) {
                            $consumed =
                                (int) $fifoService
                                    ->getConsumedQuantity(
                                        $layer
                                    );
                        }

                        /**
                         * ==================================================
                         * ПРОВЕРКА КОЛИЧЕСТВА
                         * ==================================================
                         *
                         * Нельзя поставить количество
                         * меньше уже использованного.
                         *
                         * Было 5, использовано 3:
                         *
                         * 6 -> можно
                         * 5 -> можно
                         * 4 -> можно
                         * 3 -> можно
                         * 2 -> нельзя
                         */
                        if (
                            $newQuantity < $consumed
                        ) {
                            throw new RuntimeException(
                                "Нельзя установить количество "
                                    . "{$newQuantity} шт. для SKU "
                                    . "{$appropriationItem->variant->sku}. "
                                    . "Из этой позиции уже использовано "
                                    . "{$consumed} шт. в заказах."
                            );
                        }

                        /**
                         * ==================================================
                         * ПРОВЕРКА СМЕНЫ ПАРТИИ
                         * ==================================================
                         */
                        $batchChanged =
                            (int) $oldBatch->id
                            !==
                            (int) $newBatch->id;

                        /**
                         * Если хотя бы одна штука уже ушла
                         * в заказ — партию менять нельзя.
                         */
                        if (
                            $batchChanged &&
                            $consumed > 0
                        ) {
                            throw new RuntimeException(
                                "Нельзя изменить партию SKU "
                                    . "{$appropriationItem->variant->sku}. "
                                    . "Из этой позиции уже использовано "
                                    . "{$consumed} шт. в заказах."
                            );
                        }

                        /**
                         * ==================================================
                         * СМЕНА ПАРТИИ
                         * ==================================================
                         */
                        if ($batchChanged) {
                            /**
                             * Убираем старое количество
                             * со старой партии.
                             */
                            if ($oldQuantity > 0) {
                                $fifoService
                                    ->changeWarehouseQuantity(
                                        $oldBatch,
                                        $appropriation->warehouse_id,
                                        -$oldQuantity
                                    );
                            }

                            /**
                             * Добавляем новое количество
                             * на новую партию.
                             */
                            if ($newQuantity > 0) {
                                $fifoService
                                    ->changeWarehouseQuantity(
                                        $newBatch,
                                        $appropriation->warehouse_id,
                                        $newQuantity
                                    );
                            }

                            /**
                             * Обновляем позицию.
                             */
                            $appropriationItem->update([
                                'batch_id' =>
                                $newBatch->id,

                                'quantity' =>
                                $newQuantity,
                            ]);

                            /**
                             * Обновляем FIFO-слой.
                             */
                            if ($layer) {
                                $layer->update([
                                    'batch_id' =>
                                    $newBatch->id,

                                    'quantity' =>
                                    $newQuantity,
                                ]);
                            } else {
                                /**
                                 * Если слоя нет —
                                 * восстанавливаем его.
                                 */
                                InventoryLayer::create([
                                    'warehouse_id' =>
                                    $appropriation
                                        ->warehouse_id,

                                    'variant_id' =>
                                    $appropriationItem
                                        ->variant_id,

                                    'batch_id' =>
                                    $newBatch->id,

                                    'source_type' =>
                                    'appropriation',

                                    'source_id' =>
                                    $appropriationItem->id,

                                    'quantity' =>
                                    $newQuantity,

                                    'unit_cost' =>
                                    $appropriationItem
                                        ->purchase_price,

                                    'layer_date' =>
                                    $appropriation
                                        ->appropriation_date
                                        ->copy()
                                        ->startOfDay(),
                                ]);
                            }

                            continue;
                        }

                        /**
                         * ==================================================
                         * ПАРТИЯ НЕ МЕНЯЛАСЬ
                         * ==================================================
                         */

                        $difference =
                            $newQuantity -
                            $oldQuantity;

                        /**
                         * Меняем физический остаток.
                         *
                         * Например:
                         *
                         * было 5
                         * стало 7
                         *
                         * difference = +2
                         *
                         * или:
                         *
                         * было 5
                         * стало 4
                         *
                         * difference = -1
                         */
                        if ($difference !== 0) {
                            $fifoService
                                ->changeWarehouseQuantity(
                                    $oldBatch,
                                    $appropriation->warehouse_id,
                                    $difference
                                );
                        }

                        /**
                         * Сохраняем количество.
                         *
                         * purchase_price НЕ меняем.
                         * Это историческая себестоимость
                         * конкретного оприходования.
                         */
                        $appropriationItem->update([
                            'quantity' =>
                            $newQuantity,
                        ]);

                        /**
                         * Обновляем FIFO-слой.
                         */
                        if ($layer) {
                            $layer->update([
                                'quantity' =>
                                $newQuantity,
                            ]);
                        } else {
                            /**
                             * Если слоя нет —
                             * восстанавливаем его.
                             */
                            InventoryLayer::create([
                                'warehouse_id' =>
                                $appropriation
                                    ->warehouse_id,

                                'variant_id' =>
                                $appropriationItem
                                    ->variant_id,

                                'batch_id' =>
                                $appropriationItem
                                    ->batch_id,

                                'source_type' =>
                                'appropriation',

                                'source_id' =>
                                $appropriationItem
                                    ->id,

                                'quantity' =>
                                $newQuantity,

                                'unit_cost' =>
                                $appropriationItem
                                    ->purchase_price,

                                'layer_date' =>
                                $appropriation
                                    ->appropriation_date
                                    ->copy()
                                    ->startOfDay(),
                            ]);
                        }

                        continue;
                    }

                    /**
                     * ==================================================
                     * НОВАЯ ПОЗИЦИЯ
                     * ==================================================
                     */

                    $variant =
                        Variant::with(
                            'product'
                        )->findOrFail(
                            $itemData['variant_id']
                        );

                    $batch =
                        Batch::findOrFail(
                            $itemData['batch_id']
                        );

                    /**
                     * Партия должна принадлежать SKU.
                     */
                    if (
                        (int) $batch->variant_id
                        !==
                        (int) $variant->id
                    ) {
                        throw new RuntimeException(
                            "Партия {$batch->batch_code} "
                                . "не принадлежит SKU "
                                . "{$variant->sku}."
                        );
                    }

                    /**
                     * Получаем себестоимость.
                     */
                    $purchasePrice =
                        $this->getAppropriationPurchasePrice(
                            $variant
                        );

                    if ($purchasePrice === null) {
                        throw new RuntimeException(
                            "Для SKU {$variant->sku} "
                                . "не удалось определить себестоимость."
                        );
                    }

                    /**
                     * Создаём новую позицию.
                     */
                    $appropriationItem =
                        AppropriationItem::create([
                            'appropriation_id' =>
                            $appropriation->id,

                            'variant_id' =>
                            $variant->id,

                            'batch_id' =>
                            $batch->id,

                            'quantity' =>
                            $itemData['quantity'],

                            'purchase_price' =>
                            $purchasePrice,
                        ]);

                    /**
                     * Увеличиваем склад.
                     */
                    $fifoService
                        ->changeWarehouseQuantity(
                            $batch,
                            $appropriation->warehouse_id,
                            (int) $itemData['quantity']
                        );

                    /**
                     * Создаём FIFO-слой.
                     */
                    InventoryLayer::create([
                        'warehouse_id' =>
                        $appropriation->warehouse_id,

                        'variant_id' =>
                        $variant->id,

                        'batch_id' =>
                        $batch->id,

                        'source_type' =>
                        'appropriation',

                        'source_id' =>
                        $appropriationItem->id,

                        'quantity' =>
                        $itemData['quantity'],

                        'unit_cost' =>
                        $purchasePrice,

                        'layer_date' =>
                        $appropriation
                            ->appropriation_date
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
                    'appropriation_error',
                    $e->getMessage()
                );
        }

        return redirect()
            ->route(
                'admin.appropriations.edit',
                $appropriation
            )
            ->with(
                'success',
                'Оприходование успешно обновлено.'
            );
    }

    /**
     * ============================================================
     * УДАЛЕНИЕ ВСЕГО ДОКУМЕНТА
     * ============================================================
     */
    public function destroy(
        Appropriation $appropriation,
        FifoService $fifoService
    ) {
        try {
            DB::transaction(function () use ($appropriation, $fifoService) {

                $appropriation->load([
                    'items.batch',
                    'items.variant',
                ]);

                /*
             * Сначала проверяем ВСЕ позиции.
             *
             * Если хотя бы часть товара из оприходования
             * уже была использована в продаже/списании,
             * полностью удалить документ нельзя.
             */
                foreach ($appropriation->items as $appropriationItem) {

                    $layer = InventoryLayer::where(
                        'source_type',
                        'appropriation'
                    )
                        ->where(
                            'source_id',
                            $appropriationItem->id
                        )
                        ->first();

                    if (!$layer) {
                        continue;
                    }

                    $consumed = $fifoService->getConsumedQuantity($layer);

                    if ($consumed > 0) {

                        $sku = $appropriationItem->variant
                            ? $appropriationItem->variant->sku
                            : $appropriationItem->variant_id;

                        throw new RuntimeException(
                            "Нельзя удалить оприходование #{$appropriation->id}. " .
                                "Из SKU {$sku} уже использовано {$consumed} шт. " .
                                "в продажах или списаниях."
                        );
                    }
                }

                /*
             * Если ничего не было использовано —
             * полностью возвращаем товар со склада
             * и удаляем слои.
             */
                foreach ($appropriation->items as $appropriationItem) {

                    $layer = InventoryLayer::where(
                        'source_type',
                        'appropriation'
                    )
                        ->where(
                            'source_id',
                            $appropriationItem->id
                        )
                        ->first();

                    if (!$layer) {
                        continue;
                    }

                    $quantity = (int) $appropriationItem->quantity;

                    if ($quantity > 0) {
                        $fifoService->changeWarehouseQuantity(
                            $appropriationItem->batch,
                            $appropriation->warehouse_id,
                            -$quantity
                        );
                    }

                    FifoAllocation::where(
                        'inventory_layer_id',
                        $layer->id
                    )->delete();

                    $layer->delete();
                }

                $appropriation->delete();
            });
        } catch (RuntimeException $e) {

            return redirect()
                ->back()
                ->with('appropriation_error', $e->getMessage());
        }

        return redirect()
            ->route('admin.appropriations.index')
            ->with(
                'success',
                'Оприходование успешно удалено. Товар возвращён со склада.'
            );
    }

    /**
     * ============================================================
     * УДАЛЕНИЕ ОТДЕЛЬНОЙ ПОЗИЦИИ
     * ============================================================
     */
    public function destroyItem(
        Appropriation $appropriation,
        AppropriationItem $appropriationItem,
        FifoService $fifoService
    ) {
        try {
            DB::transaction(function () use (
                $appropriation,
                $appropriationItem,
                $fifoService
            ) {
                /**
                 * Проверяем принадлежность позиции.
                 */
                if (
                    (int) $appropriationItem->appropriation_id
                    !==
                    (int) $appropriation->id
                ) {
                    throw new RuntimeException(
                        'Эта позиция не принадлежит '
                            . 'выбранному оприходованию.'
                    );
                }

                /**
                 * Находим FIFO-слой.
                 */
                $layer =
                    InventoryLayer::where(
                        'source_type',
                        'appropriation'
                    )
                    ->where(
                        'source_id',
                        $appropriationItem->id
                    )
                    ->first();

                /**
                 * Сколько уже использовано.
                 */
                $consumed = $layer
                    ? (int) $fifoService
                        ->getConsumedQuantity(
                            $layer
                        )
                    : 0;

                /**
                 * Если уже использована хотя бы
                 * одна штука — удалить позицию нельзя.
                 */
                if ($consumed > 0) {
                    $sku =
                        $appropriationItem->variant
                        ? $appropriationItem
                        ->variant
                        ->sku
                        : $appropriationItem
                        ->variant_id;

                    throw new RuntimeException(
                        "Нельзя удалить SKU {$sku}. "
                            . "Из этой позиции уже использовано "
                            . "{$consumed} шт. в заказах."
                    );
                }

                $quantity =
                    (int) $appropriationItem->quantity;

                $batch =
                    Batch::findOrFail(
                        $appropriationItem->batch_id
                    );

                /**
                 * Убираем количество со склада.
                 */
                if ($quantity > 0) {
                    $fifoService
                        ->changeWarehouseQuantity(
                            $batch,
                            $appropriation->warehouse_id,
                            -$quantity
                        );
                }

                /**
                 * Удаляем FIFO-слой.
                 */
                if ($layer) {
                    FifoAllocation::where(
                        'inventory_layer_id',
                        $layer->id
                    )->delete();

                    $layer->delete();
                }

                /**
                 * Удаляем позицию.
                 */
                $appropriationItem->delete();
            });
        } catch (RuntimeException $e) {
            return redirect()
                ->route(
                    'admin.appropriations.edit',
                    $appropriation
                )
                ->with(
                    'appropriation_error',
                    $e->getMessage()
                );
        }

        return redirect()
            ->route(
                'admin.appropriations.edit',
                $appropriation
            )
            ->with(
                'success',
                'Артикул удалён из оприходования.'
            );
    }

    /**
     * ============================================================
     * ВАРИАНТЫ + ЦЕНА
     * ============================================================
     */
    private function getVariantsWithLatestReceiptPrice()
    {
        return Variant::with([
            'product',
            'batches',
        ])
            ->orderBy('sku')
            ->get()
            ->map(function ($variant) {

                $purchasePrice =
                    $this->getAppropriationPurchasePrice(
                        $variant
                    );

                return [
                    'id' =>
                    $variant->id,

                    'sku' =>
                    $variant->sku,

                    'name' =>
                    $variant->product->name ?? '',

                    'purchase_price' =>
                    $purchasePrice,

                    'batches' =>
                    $variant->batches
                        ->map(function ($batch) {
                            return [
                                'id' =>
                                $batch->id,

                                'batch_code' =>
                                $batch->batch_code,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * ============================================================
     * ЦЕНА ОПРИХОДОВАНИЯ
     * ============================================================
     *
     * Приоритет:
     *
     * 1. Последняя фактическая приёмка.
     * 2. Начальный FIFO-слой.
     * 3. purchase_price товара.
     */
    private function getAppropriationPurchasePrice(
        Variant $variant
    ): ?float {
        /**
         * ========================================================
         * 1. ПОСЛЕДНЯЯ ФАКТИЧЕСКАЯ ПРИЁМКА
         * ========================================================
         */
        $lastReceiptItem =
            ReceiptItem::query()
            ->where(
                'variant_id',
                $variant->id
            )
            ->whereHas(
                'receipt'
            )
            ->with(
                'receipt'
            )
            ->orderByDesc(
                Receipt::select(
                    'receipt_date'
                )
                    ->whereColumn(
                        'receipts.id',
                        'receipt_items.receipt_id'
                    )
            )
            ->orderByDesc('id')
            ->first();

        if ($lastReceiptItem) {
            return (float) $lastReceiptItem
                ->purchase_price;
        }

        /**
         * ========================================================
         * 2. НАЧАЛЬНЫЙ FIFO-СЛОЙ
         * ========================================================
         */
        $initialLayer =
            InventoryLayer::query()
            ->where(
                'variant_id',
                $variant->id
            )
            ->where(
                'source_type',
                'initial'
            )
            ->orderBy('id')
            ->first();

        if ($initialLayer) {
            /**
             * Если unit_cost уже записан,
             * используем именно его.
             */
            if (
                $initialLayer->unit_cost !== null
            ) {
                return (float) $initialLayer
                    ->unit_cost;
            }

            /**
             * Если unit_cost старого
             * начального слоя пустой —
             * используем accessor.
             */
            return (float) $initialLayer
                ->current_unit_cost;
        }

        /**
         * ========================================================
         * 3. PURCHASE PRICE ТОВАРА
         * ========================================================
         */
        if (
            $variant->product &&
            $variant->product->purchase_price !== null
        ) {
            return (float) $variant
                ->product
                ->purchase_price;
        }

        return null;
    }

    /**
     * ============================================================
     * СОЗДАНИЕ НОВОЙ ПАРТИИ
     * ============================================================
     */
    public function storeBatch(Request $request)
    {
        $data = $request->validate([
            'variant_id' => [
                'required',
                'exists:variants,id',
            ],

            'batch_code' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $variant =
            Variant::findOrFail(
                $data['variant_id']
            );

        $batchCode =
            trim(
                $data['batch_code']
            );

        /**
         * Проверяем, нет ли такой партии
         * у этого SKU.
         */
        $exists =
            Batch::where(
                'variant_id',
                $variant->id
            )
            ->where(
                'batch_code',
                $batchCode
            )
            ->exists();

        if ($exists) {
            return response()->json([
                'message' =>
                'Такая партия уже существует у этого SKU.',
            ], 422);
        }

        /**
         * Создаём партию.
         *
         * ВАЖНО:
         * сама партия не увеличивает остаток.
         *
         * Остаток увеличится только после
         * сохранения оприходования.
         */
        $batch = Batch::create([
            'variant_id' =>
            $variant->id,

            'batch_code' =>
            $batchCode,
        ]);

        return response()->json([
            'success' => true,

            'batch' => [
                'id' =>
                $batch->id,

                'batch_code' =>
                $batch->batch_code,
            ],
        ]);
    }
}
