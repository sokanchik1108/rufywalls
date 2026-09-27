<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('receipts')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'comment' => ['nullable', 'string'],
        ]);

        Supplier::create($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Поставщик успешно добавлен.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'comment' => ['nullable', 'string'],
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Поставщик успешно изменён.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->receipts()->exists()) {
            return redirect()
                ->route('admin.suppliers.index')
                ->with('error', 'Нельзя удалить поставщика, у которого есть приёмки.');
        }

        if ($supplier->payments()->exists()) {
            return redirect()
                ->route('admin.suppliers.index')
                ->with('error', 'Нельзя удалить поставщика, у которого есть оплаты.');
        }

        $supplier->delete();

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Поставщик удалён.');
    }
}

