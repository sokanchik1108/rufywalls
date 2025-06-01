<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteController extends Controller
{
    public function website()
    {
        $products = Product::with('category', 'rooms')->take(3)->get(); // только 3 товара

        $categories = Category::all();
        $rooms = Room::all();

        return view('website', compact('products', 'categories', 'rooms'));
    }



    public function catalog(Request $request)
{
    $query = Product::query();

    // 🔍 Фильтрация
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->input('category_id'));
    }

    if ($request->filled('brand')) {
        $query->whereIn('brand', (array) $request->input('brand'));
    }

    if ($request->filled('material')) {
        $query->whereIn('material', (array) $request->input('material'));
    }

    if ($request->filled('color')) {
        $query->whereIn('color', (array) $request->input('color'));
    }

    if ($request->boolean('in_stock')) {
        $query->where('quantity', '>', 0);
    }

    if ($request->filled('room_id')) {
        $query->whereHas('rooms', function ($q) use ($request) {
            $q->whereIn('rooms.id', (array) $request->input('room_id'));
        });
    }

    if ($request->filled('price_min')) {
        $query->where('sale_price', '>=', $request->input('price_min'));
    }

    if ($request->filled('price_max')) {
        $query->where('sale_price', '<=', $request->input('price_max'));
    }

    // 🔍 Поиск по названию и описанию
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->input('search') . '%')
              ->orWhere('description', 'like', '%' . $request->input('search') . '%');
        });
    }

    // 📊 Сортировка
    if ($request->filled('sort')) {
        switch ($request->input('sort')) {
            case 'price-asc':
                $query->orderBy('sale_price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('sale_price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
        }
    }

    // 🔁 Пагинация
    $products = $query->paginate(9)->appends($request->query());

    // 📡 AJAX-запрос — только часть с товарами
    if ($request->ajax()) {
        return view('partials.products', compact('products'))->render();
    }

    // 🧱 Полная страница
    return view('catalog', [
        'products' => $products,
        'rooms' => Room::all(),
        'categories' => Category::all(),
        'brands' => Product::distinct()->pluck('brand')->filter()->values(),
        'materials' => Product::distinct()->pluck('material')->filter()->values(),
        'colors' => Product::distinct()->pluck('color')->filter()->values(),
    ]);
}

}
