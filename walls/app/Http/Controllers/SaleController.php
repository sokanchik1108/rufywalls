<?php

// app/Http/Controllers/SaleController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', now()->format('Y-m-d'));

        $sales = Sale::orderBy('sale_date')
            ->get()
            ->groupBy('sale_date');

        return view('sales.index', [
            'salesByDate' => $sales,
            'selectedDate' => $selectedDate,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:qr,нал,перевод,halyk',
        ]);

        $validated['total'] = $validated['price'] * $validated['quantity'];

        Sale::create($validated);


        return redirect()->route('admin.sales.index')->with('success', 'Продажа добавлена!');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return back()->with('success', 'Продажа удалена');
    }
}
