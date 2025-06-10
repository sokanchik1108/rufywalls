<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Variant;

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
            'country' => 'required|string',
            'party' => 'nullable|string',
            'sticking' => 'required|string',
            'material' => 'required|string',
            'purchase_price' => 'required',
            'sale_price' => 'required|numeric',
            'brand' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'description' => 'required|string',
            'detailed' => 'required|string',

            // Оттенки
            'variants' => 'required|array|min:1',
            'variants.*.color' => 'required|string',
            'variants.*.sku' => 'required|string|distinct|unique:variants,sku',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.images' => 'required|array|min:1',
            'variants.*.images.*' => 'image|max:2048',
        ]);

        // Сохраняем сам товар
        $product = Product::create([
            'name' => $validated['name'],
            'country' => $validated['country'],
            'party' => $validated['party'] ?? '',
            'sticking' => $validated['sticking'],
            'material' => $validated['material'],
            'purchase_price' => $validated['purchase_price'],
            'sale_price' => $validated['sale_price'],
            'brand' => $validated['brand'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'detailed' => $validated['detailed'],
        ]);

        // Привязываем комнаты
        $product->rooms()->attach($validated['room_ids']);

        // Сохраняем оттенки
        foreach ($validated['variants'] as $index => $variantData) {
            $imagePaths = [];

            if ($request->hasFile("variants.$index.images")) {
                foreach ($request->file("variants.$index.images") as $image) {
                    $imagePaths[] = $image->store('variants', 'public');
                }
            }

            $product->variants()->create([
                'color' => $variantData['color'],
                'sku' => $variantData['sku'],
                'stock' => $variantData['stock'],
                'images' => json_encode($imagePaths), // ← исправлено
            ]);
        }

        return redirect()->route('form')->with('success', 'Товар с оттенками успешно добавлен');
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
            'detailed' => 'required',
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
