<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\{Batch, Category, Product, Room, Variant};
use Illuminate\Support\Facades\{DB, Storage};
use App\Models\User;
use Illuminate\Support\Facades\Hash;




class AdminController extends Controller
{

    public function create()
    {
        $categories = Category::all();
        $rooms = Room::all();
        $allVariants = Variant::with('product')->get();

        return view('admin.form', compact('categories', 'rooms', 'allVariants'));
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
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'description' => 'required|string',
            'detailed' => 'required|string',

            // ⚠️ теперь компаньоны не глобально, а внутри variants
            'variants' => 'required|array|min:1',
            'variants.*.color' => 'required|string',
            'variants.*.sku' => 'required|string|distinct|unique:variants,sku',
            'variants.*.images' => 'required|array|min:1',
            'variants.*.images.*' => 'image',
            'variants.*.companions' => 'nullable|array',
            'variants.*.companions.*' => 'exists:variants,id',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $validated['name'],
                'country' => $validated['country'],
                'sticking' => $validated['sticking'] ?? null,
                'material' => $validated['material'],
                'purchase_price' => $validated['purchase_price'],
                'sale_price' => $validated['sale_price'],
                'brand' => $validated['brand'],
                'description' => $validated['description'],
                'detailed' => $validated['detailed'],
            ]);

            $product->rooms()->attach($validated['room_ids']);
            $product->categories()->attach($validated['category_ids']);

            // создаём варианты
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

                // компаньоны именно для этого варианта
                if (!empty($variantData['companions'])) {
                    $variant->companions()->sync($variantData['companions']);

                    // двусторонняя связь
                    foreach ($variantData['companions'] as $companionId) {
                        $companion = Variant::find($companionId);
                        if ($companion) {
                            $companion->companions()->syncWithoutDetaching([$variant->id]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.form')->with('success', 'Товар успешно добавлен');
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

        // Список карточек вариантов (подгружаем product и companions, чтобы работали selected)
        $variants = Variant::with([
            'product.categories',
            'product.rooms',
            'companions',        // чтобы selected работал без доп. запросов
        ])
            ->when($sku, fn($q) => $q->where('sku', 'like', "%{$sku}%"))
            ->orderByDesc('created_at')
            ->paginate(20);

        $categories = Category::all();
        $rooms      = Room::all();

        // Список всех вариантов для селекта (с продуктом для подписи)
        $allVariants = Variant::with('product')->get();

        if ($request->ajax()) {
            $html = view('admin.partials.variant-cards', compact('variants', 'categories', 'rooms', 'allVariants'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.database', compact('variants', 'categories', 'rooms', 'allVariants'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::with('variants')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string',
            'sticking' => 'required|string',
            'material' => 'required|string',
            'purchase_price' => 'required',
            'sale_price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'brand' => 'required|string',
            'status' => 'nullable|in:новинка,распродажа,хит продаж',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'description' => 'required|string',
            'detailed' => 'required|string',

            // Существующие варианты
            'variants' => 'nullable|array',
            'variants.*.sku' => 'nullable|string|max:255',
            'variants.*.color' => 'nullable|string|max:255',
            'variants.*.companion_variant_ids' => 'nullable|array',
            'variants.*.companion_variant_ids.*' => 'exists:variants,id',

            // Новые варианты
            'new_variants' => 'nullable|array',
            'new_variants.*.sku' => 'nullable|string|max:255',
            'new_variants.*.color' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 Обновляем сам товар
            $product->update([
                'name' => $validated['name'],
                'country' => $validated['country'],
                'sticking' => $validated['sticking'],
                'material' => $validated['material'],
                'purchase_price' => $validated['purchase_price'],
                'sale_price' => $validated['sale_price'],
                'discount_price' => $validated['discount_price'] ?? null,
                'status' => $validated['status'] ?? null,
                'brand' => $validated['brand'],
                'description' => $validated['description'],
                'detailed' => $validated['detailed'],
            ]);

            $product->rooms()->sync($validated['room_ids']);
            $product->categories()->sync($validated['category_ids']);

            // 🔹 Обновляем существующие варианты
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variantId => $variantData) {
                    $variant = $product->variants()->find($variantId);
                    if (!$variant) continue;

                    $variant->update([
                        'sku'   => $variantData['sku'] ?? $variant->sku,
                        'color' => $variantData['color'] ?? $variant->color,
                    ]);

                    // === Обновляем картинки ===
                    if ($request->hasFile("variants.$variantId.images")) {
                        $paths = [];
                        foreach ($request->file("variants.$variantId.images") as $img) {
                            $paths[] = $img->store('variant_images', 'public');
                        }
                        $variant->images = json_encode($paths);
                        $variant->save();
                    }

                    // === Компаньоны ===
                    $companionIds = $variantData['companion_variant_ids'] ?? [];
                    $companionIds = array_filter($companionIds, fn($id) => $id != $variant->id);

                    $variant->companions()->sync($companionIds);

                    foreach ($companionIds as $companionId) {
                        $companion = Variant::find($companionId);
                        if ($companion) {
                            $companion->companions()->syncWithoutDetaching([$variant->id]);
                        }
                    }

                    $oldCompanions = Variant::whereHas('companions', function ($q) use ($variant) {
                        $q->where('companion_variant_id', $variant->id);
                    })->get();

                    foreach ($oldCompanions as $oldCompanion) {
                        if (!in_array($oldCompanion->id, $companionIds)) {
                            $oldCompanion->companions()->detach($variant->id);
                        }
                    }
                }
            }

            // 🔹 Добавляем новые оттенки (варианты)
            if (!empty($validated['new_variants'])) {
                foreach ($validated['new_variants'] as $newVariantData) {
                    if (!empty($newVariantData['sku']) || !empty($newVariantData['color'])) {
                        $variant = $product->variants()->create([
                            'sku'   => $newVariantData['sku'] ?? null,
                            'color' => $newVariantData['color'] ?? null,
                            'images' => json_encode([]), // новые оттенки без картинок
                        ]);
                    }
                }
            }

            // 🔹 Обновляем изображения самого товара
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('product_images', 'public');
                }
                $product->images = json_encode($imagePaths);
                $product->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Товар успешно обновлён');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ошибка при обновлении: ' . $e->getMessage()]);
        }
    }










    public function delete($id)
    {
        $product = Product::findOrFail($id);

        // Удаляем изображения товара
        if ($product->images) {
            $images = json_decode($product->images, true);
            foreach ($images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // Удаляем варианты и их изображения
        foreach ($product->variants as $variant) {
            if ($variant->images) {
                $variantImages = json_decode($variant->images, true);
                foreach ($variantImages as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            // Удаляем связи компаньонов варианта
            $variant->companions()->detach();
            $variant->delete();
        }

        // Удаляем связи товара
        $product->rooms()->detach();
        $product->companions()->detach();

        // Удаляем сам товар
        $product->delete();

        return redirect()->route('admin.database')->with('success', 'Товар и все его варианты успешно удалены');
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

        $variants = Variant::with('product')
            ->where('sku', 'LIKE', '%' . $term . '%')
            ->orWhereHas('product', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%' . $term . '%');
            })
            ->limit(10)
            ->get();

        $results = $variants->map(function ($variant) {
            return [
                'label' => $variant->sku . ' — ' . ($variant->product->name ?? ''),
                'value' => $variant->sku
            ];
        });

        return response()->json($results);
    }





    public function makeMeAdmin(Request $request)
    {
        $user = auth()->user();

        if (
            $user->email === 'kurbanov.abdurrohman2010@mail.ru' &&
            Hash::check('adil1986', $user->password)
        ) {
            $user->is_admin = true;
            $user->save();

            return redirect()->route('home')->with('status', 'Вы стали админом.');
        }

        abort(403, 'Доступ запрещён.');
    }


    public function toggleHidden(Request $request, Product $product)
    {
        $product->is_hidden = $request->has('is_hidden');
        $product->save();

        return back()->with('success', 'Статус отображения товара обновлён.');
    }





    public function storeHidden(Request $request)
    {
        $validated = $request->validate([
            'variants' => 'required|array|min:1',
            'variants.*.color' => 'required|string',
            'variants.*.sku' => 'required|string|distinct|unique:variants,sku',
            'variants.*.images.*' => 'nullable|image',
        ], [
            'variants.*.sku.unique' => 'Уже есть такой артикул',
            'variants.*.sku.distinct' => 'Артикулы должны быть разными',
        ]);


        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => 'Cкрытый товар',
                'country' => '—',
                'sticking' => '—',
                'material' => '—',
                'purchase_price' => 0,
                'sale_price' => 0,
                'brand' => '—',
                'description' => '—',
                'detailed' => '—',
                'is_hidden' => true,
            ]);

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
                    'images' => json_encode($imagePaths),
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Скрытый товар успешно добавлен');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ошибка: ' . $e->getMessage()]);
        }
    }

    public function selectCreateForm()
    {
        return view('admin.select-form');
    }
}
