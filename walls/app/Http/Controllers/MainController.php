<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{

    // Показать форму
    public function create()
    {
        $categories = Category::all();
        $rooms = Room::all();

        return view('form', compact('categories', 'rooms'));
    }

    // Сохранить товар

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'required|string|unique:products,' . ($product->id ?? 'NULL') . ',id',
            'country' => 'required|string',
            'color' => 'required|string',
            'party' => 'nullable|string',
            'sticking' => 'required|string',
            'material' => 'required|string',
            'purchase_price' => 'required',
            'sale_price' => 'required|numeric',
            'brand' => 'required|string',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'description' => 'required',
        ]);

        $product = Product::create($validated);

        // Связываем комнаты (many-to-many)
        $product->rooms()->attach($validated['room_ids']);

        // Сохранение изображений
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('product_images', 'public');
            }
            $product->images = json_encode($imagePaths);
            $product->save();
        }

        return redirect()->route('form')->with('success', 'Товар успешно добавлен');
    }

    // Показать все товары
    public function index()
    {
        // Подгружаем связи: категорию и комнаты (many-to-many)
        $products = Product::with('category', 'rooms')->get();

        $categories = Category::all();
        $rooms = Room::all();

        return view('database', compact('products', 'categories', 'rooms'));
    }


    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'required|string|unique:products,' . ($product->id ?? 'NULL') . ',id',
            'country' => 'required|string',
            'color' => 'required|string',
            'party' => 'nullable|string',
            'sticking' => 'required|string',
            'material' => 'required|string',
            'purchase_price' => 'required',
            'sale_price' => 'required|numeric',
            'brand' => 'required|string',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'description' => 'required',
        ]);


        $product->update($validated);
        $product->rooms()->sync($request->input('room_ids', []));






        // Обновление изображений (если новые загружены)
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('product_images', 'public');
            }
            $product->images = json_encode($imagePaths);
            $product->save();
        }

        return redirect()->route('database')->with('success', 'Товар успешно обновлён');
    }

    public function destroy($id)
{
    $product = Product::findOrFail($id);

    // Опционально: удалить связанные изображения из storage
    if ($product->images) {
        $images = json_decode($product->images, true);
        foreach ($images as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    // Удаляем связи many-to-many с комнатами
    $product->rooms()->detach();

    // Удаляем сам товар
    $product->delete();

    return redirect()->route('database')->with('success', 'Товар успешно удалён');
}

}
