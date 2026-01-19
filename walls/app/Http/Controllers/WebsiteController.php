<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class WebsiteController extends Controller
{
    public function website()
    {
        $categories = Category::all();
        $rooms = Room::all();

        // Случайные новинки
        $products = Product::with('variants.batches')
            ->where('status', 'новинка')
            ->whereHas('variants')
            ->inRandomOrder()
            ->take(8)
            ->get();

        $variants = $products->map(function ($product) {
            return $product->variants->random();
        });

        // Варианты по категориям: только по одному уникальному продукту на категорию
        $allVariants = Variant::with('product.categories')->get();
        $usedProductIds = []; // ключевое изменение: теперь исключаем не варианты, а продукты
        $categoryVariants = [];

        foreach ($categories as $category) {
            $variant = $allVariants->first(function ($v) use ($category, $usedProductIds) {
                $images = json_decode($v->images, true) ?? [];

                return $v->product
                    && $v->product->categories->contains('id', $category->id)
                    && !in_array($v->product_id, $usedProductIds) // исключаем продукты, не варианты
                    && count($images) >= 7;
            });

            if (!$variant) continue;

            $images = json_decode($variant->images, true) ?? [];
            $variant->image7 = $images[6];

            $categoryVariants[$category->id] = $variant;
            $usedProductIds[] = $variant->product_id; // теперь помечаем продукт
        }

        return view('website', compact('products', 'categories', 'rooms', 'variants', 'categoryVariants'));
    }





    public function howToOrder()
    {
        return view('navigations.ordermake');
    }

    public function Calculator()
    {
        return view('navigations.calculator');
    }



    public function catalog(Request $request)
    {
        /* ============================================================
       Загружаем справочники
    ============================================================ */
        $categories = Category::whereHas('products', fn($q) => $q->where('is_hidden', false))->get();
        $rooms = Room::whereHas('products', fn($q) => $q->where('is_hidden', false))->get();

        $brands = Product::where('is_hidden', false)
            ->whereNotNull('brand')
            ->distinct()
            ->pluck('brand');

        $materials = Product::where('is_hidden', false)
            ->whereNotNull('material')
            ->distinct()
            ->pluck('material');

        $colors = Variant::whereNotNull('color')
            ->whereHas('product', fn($q) => $q->where('is_hidden', false))
            ->distinct()
            ->pluck('color');



        /* ============================================================
       ✅ РЕЖИМ ВАРИАНТОВ: ТОЛЬКО ПРИ ПОИСКЕ
       (ФИЛЬТР ЦВЕТ НЕ ВКЛЮЧАЕТ РЕЖИМ ВАРИАНТОВ)
    ============================================================ */
        $showVariants = $request->filled('search');



        /* ============================================================
       ✅ РЕЖИМ ВАРИАНТОВ
    ============================================================ */
        if ($showVariants) {

            $variants = Variant::with(['product', 'batches'])
                ->whereHas('product', fn($q) => $q->where('is_hidden', false));

            /* ---------- Фильтры ---------- */
            if ($request->filled('category_id')) {
                $variants->whereHas('product.categories', fn($q) =>
                $q->whereIn('categories.id', (array)$request->category_id));
            }

            if ($request->filled('room_id')) {
                $variants->whereHas('product.rooms', fn($q) =>
                $q->whereIn('rooms.id', (array)$request->room_id));
            }

            if ($request->filled('brand')) {
                $variants->whereHas('product', fn($q) =>
                $q->whereIn('brand', (array)$request->brand));
            }

            if ($request->filled('material')) {
                $variants->whereHas('product', fn($q) =>
                $q->whereIn('material', (array)$request->material));
            }

            if ($request->filled('in_stock')) {
                $variants->whereHas('batches', fn($q) =>
                $q->where('stock', '>', 0));
            }

            if ($request->filled('sticking')) {
                $variants->whereHas('product', function ($q) use ($request) {
                    if ($request->sticking === 'yes') {
                        $q->whereRaw("sticking != 'Нет'");
                    } elseif ($request->sticking === 'no') {
                        $q->whereRaw("sticking = 'Нет'");
                    }
                });
            }

            if ($request->filled('price_min')) {
                $variants->whereHas('product', fn($q) =>
                $q->where('sale_price', '>=', $request->price_min));
            }

            if ($request->filled('price_max')) {
                $variants->whereHas('product', fn($q) =>
                $q->where('sale_price', '<=', $request->price_max));
            }

            if ($request->filled('color')) {
                $variants->whereIn('color', (array)$request->color);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $variants->where(function ($query) use ($search) {
                    $query->where('sku', 'like', "%$search%")
                        ->orWhereHas('product', fn($q) =>
                        $q->where('name', 'like', "%$search%"));
                });
            }

            if ($request->filled('status')) {
                $variants->whereHas('product', fn($q) =>
                $q->whereIn('status', (array)$request->status));
            }

            if ($request->filled('on_sale') && $request->on_sale == '1') {
                $variants->whereHas('product', fn($q) =>
                $q->whereColumn('discount_price', '>', 'sale_price'));
            }

            /* ---------- Сортировка ---------- */
            switch ($request->input('sort')) {
                case 'price_asc':
                    $variants->orderBy(
                        Product::select('sale_price')
                            ->whereColumn('products.id', 'variants.product_id')
                            ->limit(1),
                        'asc'
                    );
                    break;

                case 'price_desc':
                    $variants->orderBy(
                        Product::select('sale_price')
                            ->whereColumn('products.id', 'variants.product_id')
                            ->limit(1),
                        'desc'
                    );
                    break;

                case 'name_asc':
                    $variants->orderBy(
                        Product::select('name')
                            ->whereColumn('products.id', 'variants.product_id')
                            ->limit(1),
                        'asc'
                    );
                    break;

                case 'name_desc':
                    $variants->orderBy(
                        Product::select('name')
                            ->whereColumn('products.id', 'variants.product_id')
                            ->limit(1),
                        'desc'
                    );
                    break;

                default:
                    $variants->join('products', 'variants.product_id', '=', 'products.id')
                        ->select('variants.*')
                        ->orderByRaw("
                        CASE
                        WHEN products.status = 'новинка' THEN 1
                        ELSE 2
                        END
                    ")
                        ->orderBy('products.created_at', 'desc'); // новее — выше
            }

            /* ---------- Пагинация ---------- */
            $variants = $variants->paginate(20)->withQueryString();

            if ($request->ajax()) {
                return view('partials.products', compact('variants'))->render();
            }

            return view('catalog', compact('variants', 'categories', 'rooms', 'brands', 'materials', 'colors'));
        }



        /* ============================================================
       ✅ РЕЖИМ ПРОДУКТОВ (включая фильтр цвета)
    ============================================================ */
        $products = Product::where('is_hidden', false);

        if ($request->filled('category_id')) {
            $products->whereHas('categories', fn($q) =>
            $q->whereIn('categories.id', (array)$request->category_id));
        }

        if ($request->filled('room_id')) {
            $products->whereHas('rooms', fn($q) =>
            $q->whereIn('rooms.id', (array)$request->room_id));
        }

        if ($request->filled('brand')) {
            $products->whereIn('brand', (array)$request->brand);
        }

        if ($request->filled('material')) {
            $products->whereIn('material', (array)$request->material);
        }

        /* ✅ ФИЛЬТР ПО ЦВЕТУ — ГЛАВНОЕ ИСПРАВЛЕНИЕ */
        if ($request->filled('color')) {
            $products->whereHas('variants', fn($q) =>
            $q->whereIn('color', (array)$request->color));
        }

        if ($request->filled('in_stock')) {
            $products->whereHas('variants.batches', fn($q) =>
            $q->where('stock', '>', 0));
        }

        if ($request->filled('sticking')) {
            if ($request->sticking === 'yes') {
                $products->whereRaw("sticking != 'Нет'");
            } elseif ($request->sticking === 'no') {
                $products->whereRaw("sticking = 'Нет'");
            }
        }

        if ($request->filled('price_min')) {
            $products->where('sale_price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $products->where('sale_price', '<=', $request->price_max);
        }

        if ($request->filled('status')) {
            $products->whereIn('status', (array)$request->status);
        }

        if ($request->filled('on_sale') && $request->on_sale == '1') {
            $products->whereColumn('discount_price', '>', 'sale_price');
        }


        /* ---------- Сортировка ---------- */
        switch ($request->input('sort')) {
            case 'price_asc':
                $products->orderBy('sale_price', 'asc');
                break;

            case 'price_desc':
                $products->orderBy('sale_price', 'desc');
                break;

            case 'name_asc':
                $products->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $products->orderBy('name', 'desc');
                break;

            default:
                $products->orderByRaw("
                CASE
                     WHEN status = 'новинка' THEN 1
                     ELSE 2
                 END
                ")->orderBy('created_at', 'desc'); // новее — выше
        }


        /* ---------- Пагинация ---------- */
        $products = $products->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return view('partials.products', ['variants' => $products])->render();
        }

        return view('catalog', [
            'variants'   => $products,
            'categories' => $categories,
            'rooms'      => $rooms,
            'brands'     => $brands,
            'materials'  => $materials,
            'colors'     => $colors
        ]);
    }









    public function show($id)
    {
        $product = Product::with([
            'categories',
            'rooms',
            'variants.batches.warehouses',
            'companions.variants'
        ])->findOrFail($id);

        $variants = $product->variants;

        // Получаем variant из query-параметра
        $activeVariantId = request('variant');

        // Находим активный вариант
        $activeVariant = $activeVariantId
            ? $variants->firstWhere('id', $activeVariantId)
            : $variants->first(); // fallback

        // Если не нашли, просто первый
        if (!$activeVariant) {
            $activeVariant = $variants->first();
        }

        $variantStock = $activeVariant
            ? $activeVariant->batches->flatMap(fn($b) => $b->warehouses)->sum('pivot.quantity')
            : 0;

        $firstCompanion = $product->companions->first();
        $firstVariant = $firstCompanion?->variants->first();

        return view('product-page', compact(
            'product',
            'variants',
            'activeVariant',
            'variantStock',
            'firstCompanion',
            'firstVariant'
        ));
    }




    public function variantData($id)
    {
        $variant = Variant::with(['batches', 'companions.product', 'companionOf.product'])->findOrFail($id);
        $stock = $variant->batches->sum('stock');

        // Все компаньоны (двусторонние связи)
        $companions = $variant->companions->merge($variant->companionOf)->unique('id');

        $companionSkus = $companions->map(fn($comp) => $comp->sku ?: '')->filter()->values()->all();

        $firstCompanion = $companions->first();

        $companionData = null;
        if ($firstCompanion && $firstCompanion->product) {
            $companionImages = json_decode($firstCompanion->images, true);

            $companionData = [
                'id' => $firstCompanion->product->id,
                'variant_id' => $firstCompanion->id, // <-- добавляем ID варианта
                'sku' => $firstCompanion->sku,
                'title' => $firstCompanion->product->title
                    ?? $firstCompanion->product->name
                    ?? $firstCompanion->name
                    ?? '',
                'image' => $companionImages[0] ?? null,
            ];
        }

        return response()->json([
            'id' => $variant->id,
            'sku' => $variant->sku,
            'stock' => $stock,
            'color' => $variant->color,
            'images' => json_decode($variant->images),
            'companions' => $companionSkus,
            'companion' => $companionData,
        ]);
    }







    // ---------------- КОРЗИНА ----------------

    private function getCartData()
    {
        return json_decode(Cookie::get('cart', '{}'), true) ?? [];
    }

    private function saveCartData(array $cart)
    {
        return Cookie::make('cart', json_encode($cart), 60 * 24 * 7);
    }

    public function addToCart(Request $request)
    {
        $variantId = $request->input('variant_id');

        if (!$variantId) {
            return response()->json([
                'message' => 'Не указан вариант товара.',
            ], 422);
        }

        $variant = Variant::with(['product', 'batches'])->findOrFail($variantId);
        $quantity = max(1, (int) $request->input('quantity', 1));

        // ❌ Удалена проверка на наличие stock

        $cart = $this->getCartData();

        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] += $quantity;
        } else {
            $cart[$variantId] = [
                'quantity' => $quantity,
                'sku' => $variant->sku,
                'price' => $variant->product->sale_price,
                'image' => json_decode($variant->images)[0] ?? null,
                'product_name' => $variant->product->name,
            ];
        }

        Cookie::queue($this->saveCartData($cart));

        return response()->json([
            'message' => 'Товар добавлен в корзину',
            'cart_count' => array_sum(array_column($cart, 'quantity')),
        ]);
    }

    public function cart()
    {
        $cart = $this->getCartData();
        $variantIds = array_keys($cart);

        if (empty($variantIds)) {
            return view('index', [
                'cartItems' => collect(),
                'total' => 0,
            ]);
        }

        $variants = Variant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $cartItems = collect($cart)->map(function ($item, $variantId) use ($variants) {
            $variant = $variants[$variantId] ?? null;
            if (!$variant) return null;

            $product = $variant->product;

            return [
                'variant_id' => $variantId,
                'variant' => $variant,
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $product->sale_price,
                'total' => $product->sale_price * $item['quantity'],
                'image' => $item['image'] ?? (json_decode($variant->images)[0] ?? null),
            ];
        })->filter();

        $total = $cartItems->sum('total');

        return view('index', compact('cartItems', 'total'));
    }

    public function updateCart(Request $request, $variantId)
    {
        $cart = $this->getCartData();
        $quantity = max(1, (int) $request->input('quantity', 1));

        if (!isset($cart[$variantId])) {
            return response()->json(['error' => 'Товар не найден в корзине'], 404);
        }

        $variant = Variant::with('product')->findOrFail($variantId);

        // обновляем количество
        $cart[$variantId]['quantity'] = $quantity;
        Cookie::queue($this->saveCartData($cart));

        // цена товара
        $itemPrice = $variant->product->sale_price ?? 0;

        // сумма по позиции
        $itemTotal = $itemPrice > 0 ? $itemPrice * $quantity : 0;

        // пересчитываем корзину
        $variantIds = array_keys($cart);
        $variants = Variant::with('product')->findMany($variantIds)->keyBy('id');

        $cartTotal = 0;
        $hasZeroPrices = false;

        foreach ($cart as $id => $item) {
            $v = $variants->get((int) $id);

            if ($v && $v->product) {
                $price = $v->product->sale_price ?? 0;

                if ($price == 0) {
                    $hasZeroPrices = true;
                }

                $cartTotal += $price * $item['quantity'];
            }
        }

        return response()->json([
            'success' => true,
            'quantity' => $quantity,
            'itemPrice' => $itemPrice,   // ← для проверки в JS
            'itemTotal' => $itemTotal,
            'cartTotal' => $cartTotal,
            'hasZeroPrices' => $hasZeroPrices, // ← флаг для итога
        ]);
    }




    public function removeFromCart($variantId)
    {
        $cart = $this->getCartData();
        unset($cart[$variantId]);

        return redirect()->back()
            ->withCookie($this->saveCartData($cart))
            ->with('success', 'Товар удалён из корзины.');
    }

    public function clearCart()
    {
        return redirect()->back()
            ->withCookie(Cookie::forget('cart'))
            ->with('success', 'Корзина очищена.');
    }

    public function count()
    {
        $cart = json_decode(Cookie::get('cart', '[]'), true);
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'] ?? 0;
        }

        return response()->json(['count' => $count]);
    }

    public function catalogAutocomplete(Request $request)
    {
        $term = $request->get('term');

        $variants = Variant::with('product')
            ->where(function ($query) use ($term) {
                $query->where('sku', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('product', function ($subQuery) use ($term) {
                        $subQuery->where('name', 'LIKE', '%' . $term . '%')
                            ->where('is_hidden', false); // исключаем скрытые товары
                    });
            })
            ->whereHas('product', function ($query) {
                $query->where('is_hidden', false); // гарантируем только видимые
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

    public function images($id)
    {
        $product = Product::with('variants')->findOrFail($id);

        $images = [];

        // Картинки продукта
        if (!empty($product->images)) {
            $decoded = is_string($product->images) ? json_decode($product->images, true) : $product->images;
            if (is_array($decoded)) {
                $images = array_merge($images, $decoded);
            }
        }

        // Картинки вариантов
        foreach ($product->variants as $variant) {
            if (!empty($variant->images)) {
                $decoded = is_string($variant->images) ? json_decode($variant->images, true) : $variant->images;
                if (is_array($decoded)) {
                    $images = array_merge($images, $decoded);
                }
            }
        }

        $images = array_map(function ($img) {
            return asset('storage/' . ltrim($img, '/'));
        }, array_unique($images));

        return response()->json([
            'images' => array_values($images)
        ]);
    }
}
