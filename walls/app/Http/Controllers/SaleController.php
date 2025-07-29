<?php

// app/Http/Controllers/SaleController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Variant;
use App\Models\Batch;

class SaleController extends Controller
{

    public function index(Request $request)
    {
        $selectedDate = $request->input('date'); // nullable — если не выбрана

        $query = Sale::query();

        // Фильтр по дате, если выбрана
        if ($selectedDate) {
            $query->whereDate('sale_date', $selectedDate);
        }

        $sales = $query->orderByDesc('sale_date')->get();

        // Группировка по дате
        $salesByDate = $sales->groupBy(function ($sale) {
            return $sale->sale_date->format('Y-m-d');
        });

        return view('sales.index', [
            'salesByDate' => $salesByDate,
            'selectedDate' => $selectedDate,
        ]);
    }





    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|exists:variants,sku',
            'batch_id' => 'required|exists:batches,id',
            'quantity' => 'required|integer|not_in:0',
            'price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'sale_date' => 'required|date',
        ]);

        $sku = $request->sku;
        $batchId = $request->batch_id;
        $quantity = (int) $request->quantity;
        $price = (float) $request->price;
        $total = $price * $quantity;
        $saleDate = $request->sale_date;
        $paymentMethod = $request->payment_method;

        // Найти партию и убедиться, что она принадлежит нужному артикулу
        $batch = Batch::findOrFail($batchId);
        $variant = Variant::where('sku', $sku)->firstOrFail();

        if ($batch->variant_id !== $variant->id) {
            return redirect()->back()->with('error', 'Партия не принадлежит выбранному артикулу.');
        }

        if ($quantity > 0) {
            // ✅ Продажа — списываем
            if ($batch->stock < $quantity) {
                return redirect()->back()->with('error', 'Недостаточно товара в выбранной партии.');
            }

            $batch->stock -= $quantity;
            $batch->save();
        } else {
            // 🔁 Возврат — прибавляем обратно
            $batch->stock += abs($quantity);
            $batch->save();
        }

        // ✅ Сохраняем продажу
        Sale::create([
            'sku' => $sku,
            'price' => $price,
            'quantity' => $quantity,
            'total' => $total,
            'sale_date' => $saleDate,
            'payment_method' => $paymentMethod,
        ]);

        return redirect()->route('admin.sales.index', ['date' => $saleDate])
            ->with('success', 'Продажа успешно сохранена');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return back()->with('success', 'Продажа удалена');
    }




    public function bySku($sku)
    {
        $variant = Variant::where('sku', $sku)->firstOrFail();
        return $variant->batches()->orderBy('created_at')->get(['id', 'batch_code', 'stock']);
    }
}
