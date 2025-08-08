<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\{Batch, Category, Product, Room, Variant};


class WarehouseController extends Controller
{
    public function editWarehouse(Request $request, Warehouse $warehouse)
    {
        $variants = Variant::with(['product', 'batches' => function ($q) use ($warehouse) {
            $q->whereHas('warehouses', fn($q) => $q->where('warehouse_id', $warehouse->id));
        }, 'batches.warehouses'])
            ->when($request->filled('sku'), fn($q) => $q->where('sku', 'like', '%' . $request->sku . '%'))
            ->paginate(25);

        return view('admin.stocks.edit-warehouse', compact('warehouse', 'variants'));
    }

    public function updateWarehouse(Request $request, Warehouse $warehouse)
    {
        $errors = [];

        // 🟡 Обновление существующих партий
        if ($request->has('batch')) {
            foreach ($request->batch as $batchId => $quantity) {
                $batch = Batch::find($batchId);
                if ($batch) {
                    $batch->warehouses()->syncWithoutDetaching([
                        $warehouse->id => ['quantity' => $quantity]
                    ]);
                }
            }
        }

        // 🟢 Добавление новых партий
        if ($request->has('new_batch')) {
            foreach ($request->new_batch as $variantId => $data) {
                $code = isset($data['code']) ? trim($data['code']) : null;
                $quantity = isset($data['quantity']) ? intval($data['quantity']) : 0;

                if ($code && $quantity > 0) {
                    $exists = \App\Models\Batch::where('variant_id', $variantId)
                        ->where('batch_code', $code)
                        ->exists();

                    if ($exists) {
                        $errors[] = 'Партия «' . e($code) . '» уже существует и не была добавлена.';
                    } else {
                        $batch = \App\Models\Batch::create([
                            'variant_id' => $variantId,
                            'batch_code' => $code,
                        ]);
                        $batch->warehouses()->attach($warehouse->id, ['quantity' => $quantity]);
                    }
                }
            }
        }


        if (!empty($errors)) {
            return redirect()->back()->with('error_list', $errors);
        }

        return redirect()->back()->with('success', 'Остатки обновлены');
    }



    public function listWarehouses()
    {
        $warehouses = Warehouse::all();
        return view('admin.stocks.warehouses', compact('warehouses'));
    }


    public function viewAllBatches(Request $request)
    {
        // Склады
        $warehouses = Warehouse::orderBy('name')->get();

        // Загружаем варианты с партиями и складами
        $query = Variant::with(['product', 'batches.warehouses']);

        if ($request->filled('sku')) {
            $query->where('sku', 'like', '%' . $request->sku . '%');
        }

        // Сначала получаем без пагинации, чтобы посчитать stock_balance
        $variants = $query->get();

        // Считаем остатки
        $variants->transform(function ($variant) {
            $total = 0;
            $warehouseTotals = [];

            foreach ($variant->batches as $batch) {
                foreach ($batch->warehouses as $warehouse) {
                    $qty = $warehouse->pivot->quantity ?? 0;
                    $total += $qty;

                    $warehouseTotals[$warehouse->id] = [
                        'name' => $warehouse->name,
                        'quantity' => ($warehouseTotals[$warehouse->id]['quantity'] ?? 0) + $qty,
                    ];
                }
            }

            $variant->stock_balance = $total;
            $variant->stock_per_warehouse = $warehouseTotals;

            return $variant;
        });

        // Сортировка по количеству
        if ($request->filled('sort')) {
            $direction = strtolower($request->sort) === 'asc' ? 'asc' : 'desc';
            $variants = $direction === 'asc'
                ? $variants->sortBy('stock_balance')->values()
                : $variants->sortByDesc('stock_balance')->values();
        }

        // Ручная пагинация после сортировки
        $perPage = 25;
        $page = $request->input('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $variants->forPage($page, $perPage),
            $variants->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('admin.stocks.view_all_batches', [
            'variants' => $paginated,
            'warehouses' => $warehouses,
        ]);
    }




    public function addBatchToWarehouse(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        $batch = Batch::firstOrCreate([
            'variant_id' => $data['variant_id'],
            'batch_code' => $data['code'],
        ]);

        $batch->warehouses()->syncWithoutDetaching([
            $data['warehouse_id'] => ['quantity' => $data['quantity']]
        ]);

        return response()->json([
            'success' => true,
            'batch_id' => $batch->id,
            'code' => $batch->batch_code,
            'quantity' => $data['quantity']
        ]);
    }



    public function updateQuantity(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $batch = Batch::findOrFail($request->batch_id);
        $batch->warehouses()->updateExistingPivot($request->warehouse_id, [
            'quantity' => $request->quantity
        ]);

        return response()->json(['success' => true]);
    }

    public function removeBatch(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $batch = Batch::findOrFail($request->batch_id);

        // Удаляем связь партия-склад
        $batch->warehouses()->detach($request->warehouse_id);

        // Если партия больше не связана ни с одним складом — удаляем полностью
        if ($batch->warehouses()->count() === 0) {
            $batch->delete();
        }

        return response()->json(['success' => true]);
    }


    public function storeWarehouse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Warehouse::create(['name' => $request->name]);

        return redirect()->route('admin.stocks.warehouses')->with('success', 'Склад успешно добавлен!');
    }

    public function destroyWarehouse($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->delete();

            return redirect()->route('admin.stocks.warehouses')->with('success', 'Склад удалён');
        } catch (\Exception $e) {
            return redirect()->route('admin.stocks.warehouses')->with('error', 'Ошибка при удалении');
        }
    }

    public function batchOverview(Request $request, $warehouseId)
    {
        $warehouse = Warehouse::findOrFail($warehouseId);

        $query = \App\Models\Batch::whereHas('warehouses', function ($q) use ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        })
            ->with([
                'variant.product',
                'warehouses' => function ($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId);
                }
            ]);

        // Eager-load все партии, потом вручную сгруппируем
        $batches = $query->get();

        // Группируем по variant_id
        $grouped = $batches->groupBy('variant_id');

        // Фильтрация
        if ($request->filled('search')) {
            $grouped = $grouped->filter(function ($group) use ($request) {
                return str_contains(strtolower($group->first()->variant->sku), strtolower($request->search));
            });
        }

        // Сортировка
        if ($request->filled('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $grouped = $grouped->sortBy(function ($group) use ($warehouseId) {
                return $group->sum(function ($batch) use ($warehouseId) {
                    return optional($batch->warehouses->firstWhere('id', $warehouseId)?->pivot)->quantity ?? 0;
                });
            }, SORT_REGULAR, $request->sort === 'desc');
        }

        // Пагинация вручную
        $perPage = 25;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $grouped->values(); // переиндексация
        $paginated = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.stocks.batch_overview', [
            'warehouse' => $warehouse,
            'batches' => $batches, // если где-то ещё нужен полный список
            'grouped' => $paginated,
        ]);
    }

    public function stockWarehousesPage()
    {
        $warehouses = \App\Models\Warehouse::all();
        return view('admin.stocks.warehouse_list', compact('warehouses'));
    }
}
