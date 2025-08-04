<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Variant;
use App\Models\Warehouse;
use App\Models\Batch;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('date');
        $selectedWarehouseId = $request->input('warehouse_id'); // важно: именно warehouse_id

        // Получаем список всех складов
        $warehouses = Warehouse::all();

        // Проверка, если склад не выбран — можно вернуть с ошибкой или показать пусто
        if (!$selectedWarehouseId) {
            return view('sales.index', [
                'salesByDate' => collect(),
                'selectedDate' => $selectedDate,
                'warehouses' => $warehouses,
                'selectedWarehouseId' => null,
            ])->with('error', 'Выберите склад');
        }

        // Формируем запрос к продажам
        $query = Sale::query()
            ->where('warehouse_id', $selectedWarehouseId)
            ->with(['warehouse', 'batch.variant']);

        // Если дата выбрана — фильтруем по дате
        if ($selectedDate) {
            $query->whereDate('sale_date', $selectedDate);
        }

        // Получаем все продажи по выбранным условиям
        $sales = $query->orderByDesc('sale_date')->get();

        // Группировка по дате
        $salesByDate = $sales->groupBy(function ($sale) {
            return $sale->sale_date->format('Y-m-d');
        });

        return view('sales.index', [
            'salesByDate' => $salesByDate,
            'selectedDate' => $selectedDate,
            'warehouses' => $warehouses,
            'selectedWarehouseId' => $selectedWarehouseId,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|exists:variants,sku',
            'batch_id' => 'required|exists:batches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|not_in:0',
            'price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'sale_date' => 'required|date',
        ]);

        $warehouseId = $request->warehouse_id;
        $batchId = $request->batch_id;
        $sku = $request->sku;
        $quantity = (int) $request->quantity;
        $price = (float) $request->price;
        $total = $price * $quantity;
        $saleDate = $request->sale_date;
        $paymentMethod = $request->payment_method;

        $variant = Variant::where('sku', $sku)->firstOrFail();
        $batch = Batch::findOrFail($batchId);

        if ($batch->variant_id !== $variant->id) {
            return back()->with('error', 'Партия не принадлежит выбранному артикулу.');
        }

        $pivot = $batch->warehouses()->where('warehouse_id', $warehouseId)->first();

        if (!$pivot) {
            return back()->with('error', 'Выбранная партия не привязана к этому складу.');
        }

        $currentQty = $pivot->pivot->quantity;

        if ($quantity > 0) {
            // Продажа
            if ($currentQty < $quantity) {
                return back()->with('error', 'Недостаточно товара на складе.');
            }

            $batch->warehouses()->updateExistingPivot($warehouseId, [
                'quantity' => $currentQty - $quantity
            ]);
        } else {
            // Возврат
            $batch->warehouses()->updateExistingPivot($warehouseId, [
                'quantity' => $currentQty + abs($quantity)
            ]);
        }

        Sale::create([
            'sku' => $sku,
            'batch_id' => $batchId, // ✅ добавлено
            'price' => $price,
            'quantity' => $quantity,
            'total' => $total,
            'sale_date' => $saleDate,
            'payment_method' => $paymentMethod,
            'warehouse_id' => $warehouseId,
        ]);


        return redirect()->route('admin.sales.index', [
            'date' => $saleDate,
            'warehouse_id' => $warehouseId,
        ])->with('success', $quantity > 0 ? 'Продажа успешно сохранена' : 'Возврат успешно сохранён');
    }


    public function selectWarehouse()
    {
        $warehouses = Warehouse::all();
        return view('sales.select_warehouse', compact('warehouses'));
    }

    public function bySku($sku)
    {
        $variant = Variant::where('sku', $sku)->firstOrFail();

        return $variant->batches()
            ->with(['warehouses' => function ($q) {
                $q->select('warehouses.id', 'name')->withPivot('quantity');
            }])
            ->orderBy('created_at')
            ->get(['id', 'batch_code']);
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return back()->with('success', 'Продажа удалена');
    }
}
