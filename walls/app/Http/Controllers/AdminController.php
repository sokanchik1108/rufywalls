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
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
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
            'variants.*.images.*' => 'image',
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
                'description' => $validated['description'],
                'detailed' => $validated['detailed'],
            ]);

            $product->rooms()->attach($validated['room_ids']);
            $product->categories()->attach($validated['category_ids']);

            if (!empty($validated['companions'])) {
                $product->companions()->sync($validated['companions']);
                foreach ($validated['companions'] as $companionId) {
                    $companion = Product::find($companionId);
                    if ($companion) {
                        $companion->companions()->syncWithoutDetaching([$product->id]);
                    }
                }
            }

            foreach ($validated['variants'] as $variantIndex => $variantData) {
                $imagePaths = [];
                if ($request->hasFile("variants.$variantIndex.images")) {
                    foreach ($request->file("variants.$variantIndex.images") as $image) {
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

        // Пагинация по Variant
        $variants = Variant::with(['product.categories', 'product.rooms'])
            ->when($sku, fn($q) => $q->where('sku', 'like', "%$sku%"))
            ->orderByDesc('created_at') // или updated_at, если новизна по обновлению
            ->paginate(12);

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
        'discount_price' => 'nullable|numeric', // скидочная цена
        'brand' => 'required|string',
        'status' => 'nullable|in:новинка,распродажа,хит продаж', // статус
        'category_ids' => 'required|array',
        'category_ids.*' => 'exists:categories,id',
        'room_ids' => 'required|array',
        'room_ids.*' => 'exists:rooms,id',
        'description' => 'required|string',
        'detailed' => 'required|string',
        'companion_variant_ids' => 'nullable|array',
        'companion_variant_ids.*' => 'exists:variants,id',
    ]);

    // Если распродажа — скидочная цена обязательна
    if ($validated['status'] === 'распродажа' && empty($validated['discount_price'])) {
        return redirect()->back()
            ->withErrors(['discount_price' => 'Для распродажи нужно указать скидочную цену'])
            ->withInput();
    }

    DB::beginTransaction();

    try {
        // Обновление товара
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

        // Компаньоны
        if (!empty($validated['companion_variant_ids'])) {
            $companionProductIds = Variant::whereIn('id', $validated['companion_variant_ids'])
                ->pluck('product_id')
                ->unique()
                ->toArray();

            $companionProductIds = array_diff($companionProductIds, [$product->id]);

            $product->companions()->sync($companionProductIds);

            foreach ($companionProductIds as $companionId) {
                $companion = Product::find($companionId);
                if ($companion) {
                    $companion->companions()->syncWithoutDetaching([$product->id]);
                }
            }

            // Удаляем устаревшие обратные связи
            $oldCompanions = Product::whereHas('companions', function ($q) use ($product) {
                $q->where('companion_id', $product->id);
            })->get();

            foreach ($oldCompanions as $oldCompanion) {
                if (!in_array($oldCompanion->id, $companionProductIds)) {
                    $oldCompanion->companions()->detach($product->id);
                }
            }
        } else {
            foreach ($product->companions as $companion) {
                $companion->companions()->detach($product->id);
            }
            $product->companions()->detach();
        }

        // Изображения товара
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('product_images', 'public');
            }
            $product->images = json_encode($imagePaths);
            $product->save();
        }

        // Обновление вариантов (без партий)
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
            }
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
