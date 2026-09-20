<?php

namespace App\Http\Controllers;

use App\Models\WriteOff;
use App\Models\WriteOffItem;
use App\Models\Warehouse;
use App\Models\Variant;
use App\Models\StockMovement;
use App\Models\Batch;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WriteOffController extends Controller
{
    public function index(Request $request)
    {
        $writeOffs = WriteOff::with([
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
            ->when(
                $request->filled('from'),
                function ($query) use ($request) {
                    $query->whereDate(
                        'writeoff_date',
                        '>=',
                        $request->from
                    );
                }
            )
            ->when(
                $request->filled('to'),
                function ($query) use ($request) {
                    $query->whereDate(
                        'writeoff_date',
                        '<=',
                        $request->to
                    );
                }
            )
            ->orderByDesc('writeoff_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get();

        return view(
            'admin.writeoffs.index',
            compact(
                'writeOffs',
                'warehouses'
            )
        );
    }


    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();

        /*
         * Склад, назначенный текущему пользователю.
         *
         * Ожидается, что в таблице users есть:
         *
         * warehouse_id
         */
        $defaultWarehouseId =
            Auth::user()?->warehouse_id;

        return view(
            'admin.writeoffs.create',
            compact(
                'warehouses',
                'defaultWarehouseId'
            )
        );
    }


    /**
     * Поиск товаров и партий на выбранном складе.
     */
    public function searchVariants(Request $request)
    {
        $request->validate([
            'warehouse_id' => [
                'required',
                'exists:warehouses,id',
            ],

            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $warehouseId =
            (int) $request->warehouse_id;

        $search =
            trim(
                $request->search ?? ''
            );

        $variants = Variant::with([
            'product',
            'batches.warehouses',
        ])
            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        'sku',
                        'like',
                        '%' . $search . '%'
                    );
                }
            )
            ->orderBy('sku')
            ->limit(30)
            ->get();

        $result = [];

        foreach ($variants as $variant) {

            $batches = [];

            foreach ($variant->batches as $batch) {

                $warehouse =
                    $batch->warehouses
                        ->firstWhere(
                            'id',
                            $warehouseId
                        );

                if (!$warehouse) {
                    continue;
                }

                $quantity =
                    (int) (
                        $warehouse
                            ->pivot
                            ->quantity ?? 0
                    );

                if ($quantity <= 0) {
                    continue;
                }

                $batches[] = [
                    'id' =>
                        $batch->id,

                    'batch_code' =>
                        $batch->batch_code,

                    'quantity' =>
                        $quantity,
                ];
            }

            /*
             * Если на выбранном складе
             * нет ни одной партии с остатком,
             * товар не показываем.
             */
            if (empty($batches)) {
                continue;
            }

            $totalStock =
                array_sum(
                    array_column(
                        $batches,
                        'quantity'
                    )
                );

            $result[] = [
                'id' =>
                    $variant->id,

                'sku' =>
                    $variant->sku,

                'product' =>
                    $variant->product?->name,

                'stock' =>
                    $totalStock,

                'batches' =>
                    $batches,
            ];
        }

        return response()->json(
            $result
        );
    }


    /**
     * Создание списания.
     *
     * Пользователь выбирает конкретную партию,
     * и количество списывается именно из неё.
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

            'writeoff_date' => [
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
                'integer',
                'exists:variants,id',
            ],

            'items.*.batch_id' => [
                'required',
                'integer',
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

                $warehouseId =
                    (int) $data['warehouse_id'];


                $writeOff = WriteOff::create([
                    'warehouse_id' =>
                        $warehouseId,

                    'writeoff_date' =>
                        $data['writeoff_date'],

                    'comment' =>
                        $data['comment'] ?? null,
                ]);


                foreach ($data['items'] as $itemData) {

                    $variantId =
                        (int) $itemData['variant_id'];

                    $batchId =
                        (int) $itemData['batch_id'];

                    $quantity =
                        (int) $itemData['quantity'];


                    /*
                     * Проверяем партию.
                     */
                    $batch =
                        Batch::with('warehouses')
                            ->findOrFail(
                                $batchId
                            );


                    /*
                     * Партия должна принадлежать
                     * выбранному товару.
                     */
                    if (
                        (int) $batch->variant_id !==
                        $variantId
                    ) {

                        throw new RuntimeException(
                            "Партия {$batch->batch_code} " .
                            "не принадлежит выбранному товару."
                        );
                    }


                    /*
                     * Проверяем наличие партии
                     * на выбранном складе.
                     */
                    $warehouse =
                        $batch->warehouses
                            ->firstWhere(
                                'id',
                                $warehouseId
                            );


                    if (!$warehouse) {

                        throw new RuntimeException(
                            "Партия {$batch->batch_code} " .
                            "отсутствует на выбранном складе."
                        );
                    }


                    $available =
                        (int) (
                            $warehouse
                                ->pivot
                                ->quantity ?? 0
                        );


                    /*
                     * Проверяем остаток
                     * именно выбранной партии.
                     */
                    if ($quantity > $available) {

                        throw new RuntimeException(
                            "Недостаточно товара в партии " .
                            "{$batch->batch_code}. " .
                            "Доступно: {$available} шт."
                        );
                    }


                    /*
                     * Создаём строку списания.
                     */
                    $item = WriteOffItem::create([
                        'write_off_id' =>
                            $writeOff->id,

                        'variant_id' =>
                            $variantId,

                        'batch_id' =>
                            $batchId,

                        'quantity' =>
                            $quantity,
                    ]);


                    /*
                     * Списываем именно выбранную партию.
                     *
                     * Внутри партии FIFO:
                     * сначала initial,
                     * затем receipt,
                     * затем следующие слои.
                     */
                    $fifoService->consumeBatch(
                        $warehouseId,
                        $variantId,
                        $batchId,
                        $quantity,
                        'writeoff',
                        $item->id,
                        $data['writeoff_date']
                    );
                }
            });

        } catch (RuntimeException $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'writeoff_error',
                    $e->getMessage()
                );
        }


        return redirect()
            ->route('admin.writeoffs.index')
            ->with(
                'success',
                'Списание успешно создано.'
            );
    }


    public function show(
        WriteOff $writeOff
    ) {

        $writeOff->load([
            'warehouse',
            'items.variant.product',
            'items.batch',
        ]);


        $movements =
            StockMovement::with([
                'allocations.layer.batch',
            ])
                ->whereIn(
                    'source_id',
                    $writeOff
                        ->items
                        ->pluck('id')
                )
                ->where(
                    'type',
                    'writeoff'
                )
                ->get();


        return view(
            'admin.writeoffs.show',
            compact(
                'writeOff',
                'movements'
            )
        );
    }


    public function destroy(
        WriteOff $writeOff,
        FifoService $fifoService
    ) {

        try {

            DB::transaction(function () use (
                $writeOff,
                $fifoService
            ) {

                $items =
                    $writeOff
                        ->items()
                        ->get();


                foreach ($items as $item) {

                    $movement =
                        StockMovement::where(
                            'type',
                            'writeoff'
                        )
                            ->where(
                                'source_id',
                                $item->id
                            )
                            ->first();


                    if ($movement) {

                        /*
                         * Восстанавливаем именно те партии,
                         * которые были затронуты движением.
                         */
                        $fifoService->reverseMovement(
                            $movement
                        );
                    }
                }


                $writeOff->delete();


                /*
                 * Пересчитываем FIFO после удаления.
                 */
                $fifoService->rebuildAllocations();
            });

        } catch (RuntimeException $e) {

            return redirect()
                ->back()
                ->with(
                    'writeoff_error',
                    $e->getMessage()
                );
        }


        return redirect()
            ->route('admin.writeoffs.index')
            ->with(
                'success',
                'Списание удалено, остатки восстановлены.'
            );
    }
}