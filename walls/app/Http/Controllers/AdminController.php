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

            if (!empty($validated['companions'])) {
                $product->companions()->sync($validated['companions']);
            }

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


    public function index()
    {

        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Доступ запрещён');
        }

        $products = Product::with([
            'category',
            'rooms',
            'variants.batches',
            'companions.variants.batches',
        ])->get();

        $categories = Category::all();
        $rooms = Room::all();

        // Все варианты всех товаров
        $allProducts = Product::with('variants')->get();
        return view('admin.database', compact('products', 'categories', 'rooms', 'allProducts'));
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
            'description' => 'required',
            'detailed' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // Обновляем товар
            $product->update($validated);
            $product->rooms()->sync($validated['room_ids']);

            // Обновление компаньонов через variant_id → product_id
            if ($request->has('companion_variant_ids')) {
                $variantIds = $request->input('companion_variant_ids', []);

                $companionProductIds = Variant::whereIn('id', $variantIds)
                    ->pluck('product_id')
                    ->unique()
                    ->toArray();

                // Исключаем сам товар
                $companionProductIds = array_diff($companionProductIds, [$product->id]);

                $product->companions()->sync($companionProductIds);
            }

            // Обновление изображений товара
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('product_images', 'public');
                }
                $product->images = json_encode($imagePaths);
                $product->save();
            }

            // Обновление вариантов и партий
            if ($request->has('variants')) {
                foreach ($request->input('variants') as $variantId => $variantData) {
                    $variant = $product->variants()->find($variantId);
                    if (!$variant) continue;

                    $variant->sku = $variantData['sku'] ?? $variant->sku;
                    $variant->color = $variantData['color'] ?? $variant->color;

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
} 