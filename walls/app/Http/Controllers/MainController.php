<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use App\Models\Batch;

class MainController extends Controller
{

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

            'variants' => 'required|array|min:1',
            'variants.*.color' => 'required|string',
            'variants.*.sku' => 'required|string|distinct|unique:variants,sku',
            'variants.*.images' => 'required|array|min:1',
            'variants.*.images.*' => 'image|max:2048',

            // партии внутри каждого варианта
            'variants.*.batches' => 'required|array|min:1',
            'variants.*.batches.*.batch_code' => 'required|string',
            'variants.*.batches.*.stock' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 1. Сохраняем товар
            $product = Product::create([
                'name' => $validated['name'],
                'country' => $validated['country'],
                'sticking' => $validated['sticking'],
                'material' => $validated['material'],
                'purchase_price' => $validated['purchase_price'],
                'sale_price' => $validated['sale_price'],
                'brand' => $validated['brand'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'detailed' => $validated['detailed'],
            ]);

            // 2. Привязываем комнаты
            $product->rooms()->attach($validated['room_ids']);

            // 3. Сохраняем варианты и вложенные партии
            foreach ($validated['variants'] as $variantIndex => $variantData) {
                // 3.1 Сохраняем изображения
                $imagePaths = [];
                if ($request->hasFile("variants.$variantIndex.images")) {
                    foreach ($request->file("variants.$variantIndex.images") as $image) {
                        $imagePaths[] = $image->store('variants', 'public');
                    }
                }

                // 3.2 Сохраняем вариант
                $variant = $product->variants()->create([
                    'color' => $variantData['color'],
                    'sku' => $variantData['sku'],
                    'images' => json_encode($imagePaths),
                ]);

                // 3.3 Сохраняем партии для этого варианта
                foreach ($variantData['batches'] as $batch) {
                    $variant->batches()->create([
                        'batch_code' => $batch['batch_code'],
                        'stock' => $batch['stock'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('form')->with('success', 'Товар и партии успешно добавлены');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ошибка при сохранении: ' . $e->getMessage()]);
        }
    }

    // Показать все товары
    public function index()
    {
        $products = Product::with(['category', 'rooms', 'variants.batches', 'variants'])->get();
        $categories = Category::all();
        $rooms = Room::all();

        return view('database', compact('products', 'categories', 'rooms'));
    }


    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string',
            'sticking' => 'required|string',
            'material' => 'required|string',
            'purchase_price' => 'required',
            'sale_price' => 'required|numeric',
            'brand' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'description' => 'required',
            'detailed' => 'required',
        ]);

        // Обновление самого продукта
        $product->update($validated);
        $product->rooms()->sync($request->input('room_ids', []));

        // Обновление изображений продукта (если загружены)
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('product_images', 'public');
            }
            $product->images = json_encode($imagePaths);
            $product->save();
        }

        // Обновление вариантов (оттенков)
        if ($request->has('variants')) {
            foreach ($request->input('variants') as $variantId => $variantData) {
                $variant = $product->variants()->find($variantId);
                if (!$variant) continue;

                // Обновление sku и color
                $variant->sku = $variantData['sku'] ?? $variant->sku;
                $variant->color = $variantData['color'] ?? $variant->color;

                // Обработка изображений варианта
                if ($request->hasFile("variants.$variantId.images")) {
                    $images = $request->file("variants.$variantId.images");
                    $paths = [];
                    foreach ($images as $img) {
                        $paths[] = $img->store('variant_images', 'public');
                    }
                    $variant->images = json_encode($paths);
                }

                $variant->save();

                // Обновление партий
                if (isset($variantData['batches'])) {
                    foreach ($variantData['batches'] as $batchId => $batchData) {
                        $batch = $variant->batches()->find($batchId);
                        if (!$batch) continue;

                        $batch->batch_code = $batchData['batch_code'] ?? null;
                        $batch->stock = $batchData['stock'] ?? 0;
                        $batch->save();
                    }
                }
            }
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
