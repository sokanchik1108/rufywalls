<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\{Batch, Category, Product, Room, Variant};
use Illuminate\Support\Facades\{DB, Storage};
use App\Models\User;




class AdminController extends Controller
{

    public function create()
    {
        $categories = Category::all();
        $rooms = Room::all();
        $allProducts = Product::with('variants')->get();

        return view('admin.form', compact('categories', 'rooms', 'allProducts'));
    }

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
            'companions' => 'nullable|array',
            'companions.*' => 'exists:products,id',
            'variants' => 'required|array|min:1',
            'variants.*.color' => 'required|string',
            'variants.*.sku' => 'required|string|distinct|unique:variants,sku',
            'variants.*.images' => 'required|array|min:1',
            'variants.*.images.*' => 'image|max:2048',
            'variants.*.batches' => 'required|array|min:1',
            'variants.*.batches.*.batch_code' => 'required|string',
            'variants.*.batches.*.stock' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
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

            $product->rooms()->attach($validated['room_ids']);

            // Сохраняем связи с компаньонами в обе стороны
            if (!empty($validated['companions'])) {
                // Связь текущего товара с компаньонами
                $product->companions()->sync($validated['companions']);

                // Обратная связь: связываем каждого компаньона с текущим товаром
                foreach ($validated['companions'] as $companionId) {
                    $companion = Product::find($companionId);
                    if ($companion) {
                        $companion->companions()->syncWithoutDetaching([$product->id]);
                    }
                }
            }

            // Сохраняем варианты и партии
            foreach ($validated['variants'] as $variantIndex => $variantData) {
                $imagePaths = [];
                if ($request->hasFile("variants.$variantIndex.images")) {
                    foreach ($request->file("variants.$variantIndex.images") as $image) {
                        $imagePaths[] = $image->store('variants', 'public');
                    }
                }

                $variant = $product->variants()->create([
                    'color' => $variantData['color'],
                    'sku' => $variantData['sku'],
                    'images' => json_encode($imagePaths),
                ]);

                foreach ($variantData['batches'] as $batch) {
                    $variant->batches()->create([
                        'batch_code' => $batch['batch_code'],
                        'stock' => $batch['stock'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.form')->with('success', 'Товар и партии успешно добавлены');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ошибка при сохранении: ' . $e->getMessage()]);
        }
    }



    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Доступ запрещён');
        }

        $sku = $request->get('sku');

        // Пагинация по Variant
        $variants = Variant::with(['product.category', 'product.rooms', 'batches'])
            ->when($sku, fn($q) => $q->where('sku', 'like', "%$sku%"))
            ->paginate(1);

        $categories = Category::all();
        $rooms = Room::all();
        $allProducts = Product::with('variants')->get();

        if ($request->ajax()) {
            $html = view('admin.partials.variant-cards', compact('variants', 'categories', 'rooms', 'allProducts'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.database', compact('variants', 'categories', 'rooms', 'allProducts'));
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
            'description' => 'required|string',
            'detailed' => 'required|string',

            // 👇 добавляем companion_variant_ids
            'companion_variant_ids' => 'nullable|array',
            'companion_variant_ids.*' => 'exists:variants,id',
        ]);

        DB::beginTransaction();

        try {
            // Обновление основного товара
            $product->update($validated);
            $product->rooms()->sync($validated['room_ids']);

            // 🔁 Компаньоны по variant_id → product_id
            if (!empty($validated['companion_variant_ids'])) {
                // Получаем ID товаров по variant_id
                $companionProductIds = Variant::whereIn('id', $validated['companion_variant_ids'])
                    ->pluck('product_id')
                    ->unique()
                    ->toArray();

                // Исключаем текущий товар
                $companionProductIds = array_diff($companionProductIds, [$product->id]);

                // Обновляем связи
                $product->companions()->sync($companionProductIds);

                // Обратные связи
                foreach ($companionProductIds as $companionId) {
                    $companion = Product::find($companionId);
                    if ($companion) {
                        $companion->companions()->syncWithoutDetaching([$product->id]);
                    }
                }

                // Удалим устаревшие обратные связи
                $oldCompanions = Product::whereHas('companions', function ($q) use ($product) {
                    $q->where('companion_id', $product->id);
                })->get();

                foreach ($oldCompanions as $oldCompanion) {
                    if (!in_array($oldCompanion->id, $companionProductIds)) {
                        $oldCompanion->companions()->detach($product->id);
                    }
                }
            } else {
                // Удаляем все связи, если ничего не пришло
                foreach ($product->companions as $companion) {
                    $companion->companions()->detach($product->id);
                }
                $product->companions()->detach();
            }

            // Обновляем изображения товара
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('product_images', 'public');
                }
                $product->images = json_encode($imagePaths);
                $product->save();
            }

            // Обновляем варианты и партии
            if ($request->has('variants')) {
                foreach ($request->input('variants') as $variantId => $variantData) {
                    $variant = $product->variants()->find($variantId);
                    if (!$variant) continue;

                    $variant->sku = $variantData['sku'] ?? $variant->sku;
                    $variant->color = $variantData['color'] ?? $variant->color;

                    if ($request->hasFile("variants.$variantId.images")) {
                        $paths = [];
                        foreach ($request->file("variants.$variantId.images") as $img) {
                            $paths[] = $img->store('variant_images', 'public');
                        }
                        $variant->images = json_encode($paths);
                    }

                    $variant->save();

                    // Обновляем партии
                    if (isset($variantData['batches'])) {
                        foreach ($variantData['batches'] as $batchId => $batchData) {
                            $batch = $variant->batches()->find($batchId);
                            if (!$batch) continue;

                            $batch->batch_code = $batchData['batch_code'] ?? $batch->batch_code;
                            $batch->stock = $batchData['stock'] ?? $batch->stock;
                            $batch->save();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.database')->with('success', 'Товар успешно обновлён');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ошибка при обновлении: ' . $e->getMessage()]);
        }
    }


    public function delete($id)
    {
        $product = Product::findOrFail($id);

        if ($product->images) {
            $images = json_decode($product->images, true);
            foreach ($images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $product->rooms()->detach();
        $product->companions()->detach();

        $product->delete();

        return redirect()->route('admin.database')->with('success', 'Товар успешно удалён');
    }

    public function deleteVariant($id)
    {
        $variant = Variant::findOrFail($id);

        // Удаляем изображения
        if ($variant->images) {
            $images = json_decode($variant->images, true);
            foreach ($images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // Удаляем партии
        $variant->batches()->delete();

        // Удаляем сам вариант
        $variant->delete();

        return redirect()->back()->with('success', 'Вариант успешно удалён');
    }


    public function adminIndex()
    {

        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Доступ запрещён');
        }

        $orders = Order::with(['items.variant.product'])->latest()->get();
        return view('admin.orders', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {

        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Доступ запрещён');
        }

        $request->validate([
            'status' => 'required|string|in:Новый,Подтвержден,Завершен,Отменен',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Статус заказа обновлен.');
    }

    public function destroy($id)
    {

        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Доступ запрещён');
        }

        $order = Order::findOrFail($id);
        $order->delete();

        return back()->with('success', 'Заказ удален.');
    }

    public function clearAll()
    {


        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Доступ запрещён');
        }

        Order::truncate(); // Удалит все заказы (и их items если настроено каскадное удаление)
        return redirect()->back()->with('success', 'Все заказы удалены.');
    }




    public function users()
    {
        if (!auth()->user()?->is_admin) {
            abort(403, 'Доступ запрещён');
        }

        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function toggleAdmin(User $user)
    {
        if (!auth()->user()?->is_admin) {
            abort(403, 'Доступ запрещён');
        }

        // Нельзя изменить самого себя
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['error' => 'Нельзя изменить себя.']);
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return redirect()->back()->with('success', 'Права пользователя обновлены.');
    }



    public function autocomplete(Request $request)
    {
        $term = $request->get('term');

        $skus = Variant::where('sku', 'LIKE', '%' . $term . '%')
            ->pluck('sku')
            ->take(10);

        return response()->json($skus);
    }



    public function editStock(Request $request)
    {
        $query = Variant::with(['product', 'batches']);

        if ($request->filled('sku')) {
            $query->where('sku', 'like', '%' . $request->sku . '%');
        }

        $variants = $query->paginate(1)->withQueryString(); // пагинация

        return view('admin.stock-edit', compact('variants'));
    }



    public function updateStockAjax(Request $request)
    {
        $stocks = $request->stocks;

        foreach ($stocks as $batchId => $stock) {
            \App\Models\Batch::where('id', $batchId)->update(['stock' => (int) $stock]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Остатки успешно обновлены.'
        ]);
    }

    public function deleteBatch($id)
    {
        $batch = \App\Models\Batch::findOrFail($id);
        $batch->delete();

        return response()->json(['success' => true]);
    }
}
