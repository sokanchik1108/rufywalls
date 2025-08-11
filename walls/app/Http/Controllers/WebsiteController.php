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
        $products = Product::with('categories', 'rooms')->take(3)->get();
        $categories = Category::all();
        $rooms = Room::all();

        $variants = Variant::with(['product', 'batches'])->take(3)->get();

        return view('website', compact('products', 'categories', 'rooms', 'variants'));
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
        $categories = Category::all();
        $rooms = Room::all();
        $brands = Product::where('is_hidden', false)->distinct()->pluck('brand');
        $materials = Product::where('is_hidden', false)->distinct()->pluck('material');
        $colors = Variant::whereNotNull('color')->distinct()->pluck('color');

        // Условие: показывать варианты только если есть поиск или выбран цвет
        $showVariants = $request->filled('color') || $request->filled('search');

        if ($showVariants) {
            $variants = Variant::with(['product', 'batches'])
                ->whereHas('product', fn($q) => $q->where('is_hidden', false));

            if ($request->filled('category_id')) {
                $variants->whereHas('product.categories', fn($q) => $q->where('categories.id', $request->category_id));
            }

            if ($request->filled('room_id')) {
                $variants->whereHas('product.rooms', fn($q) => $q->where('rooms.id', $request->room_id));
            }

            if ($request->filled('brand')) {
                $variants->whereHas('product', fn($q) => $q->whereIn('brand', (array) $request->brand));
            }

            if ($request->filled('material')) {
                $variants->whereHas('product', fn($q) => $q->whereIn('material', (array) $request->material));
            }

            if ($request->filled('in_stock')) {
                $variants->whereHas('batches', fn($q) => $q->where('stock', '>', 0));
            }

            if ($request->filled('sticking')) {
                $variants->whereHas('product', function ($q) use ($request) {
                    if ($request->sticking === 'yes') {
                        $q->whereRaw("LOWER(sticking) != 'нет'");
                    } elseif ($request->sticking === 'no') {
                        $q->whereRaw("LOWER(sticking) = 'нет'");
                    }
                });
            }

            if ($request->filled('price_min')) {
                $variants->whereHas('product', fn($q) => $q->where('sale_price', '>=', $request->price_min));
            }

            if ($request->filled('price_max')) {
                $variants->whereHas('product', fn($q) => $q->where('sale_price', '<=', $request->price_max));
            }

            if ($request->filled('color')) {
                $variants->whereIn('color', (array) $request->color);
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $variants->where(function ($query) use ($searchTerm) {
                    $query->where('sku', 'like', "%$searchTerm%")
                        ->orWhereHas(
                            'product',
                            fn($q) =>
                            $q->where('name', 'like', "%$searchTerm%")
                        );
                });
            }

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
                    $variants->oldest();
            }

            $variants = $variants->paginate(12)->withQueryString();

            if ($request->ajax()) {
                return view('partials.products', compact('variants'))->render();
            }

            return view('catalog', compact('variants', 'categories', 'rooms', 'brands', 'materials', 'colors'));
        }

        // Без поиска и цвета — показываем продукты
        $products = Product::where('is_hidden', false);

        if ($request->filled('category_id')) {
            $products->whereHas('categories', fn($q) => $q->where('categories.id', $request->category_id));
        }

        if ($request->filled('room_id')) {
            $products->whereHas('rooms', fn($q) => $q->where('rooms.id', $request->room_id));
        }

        if ($request->filled('brand')) {
            $products->whereIn('brand', (array) $request->brand);
        }

        if ($request->filled('material')) {
            $products->whereIn('material', (array) $request->material);
        }

        if ($request->filled('in_stock')) {
            $products->whereHas('variants.batches', fn($q) => $q->where('stock', '>', 0));
        }

        if ($request->filled('sticking')) {
            if ($request->sticking === 'yes') {
                $products->whereRaw("LOWER(sticking) != 'нет'");
            } elseif ($request->sticking === 'no') {
                $products->whereRaw("LOWER(sticking) = 'нет'");
            }
        }

        if ($request->filled('price_min')) {
            $products->where('sale_price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $products->where('sale_price', '<=', $request->price_max);
        }

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
                $products->oldest();
        }

        $products = $products->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return view('partials.products', ['variants' => $products])->render();
        }

        return view('catalog', [
            'variants' => $products,
            'categories' => $categories,
            'rooms' => $rooms,
            'brands' => $brands,
            'materials' => $materials,
            'colors' => $colors
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
        $activeVariant = $variants->first();

        $variantStock = 0;

        if ($activeVariant) {
            $variantStock = $activeVariant->batches->flatMap(function ($batch) {
                return $batch->warehouses;
            })->sum('pivot.quantity'); // <- тут используем quantity
        }

        return view('product-page', compact('product', 'variants', 'activeVariant', 'variantStock'));
    }




    public function variantData($id)
    {
        $variant = Variant::with('batches')->findOrFail($id);
        $stock = $variant->batches->sum('stock');

        return response()->json([
            'id' => $variant->id,
            'sku' => $variant->sku,
            'stock' => $stock,
            'color' => $variant->color,
            'images' => json_decode($variant->images),
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

        $variant = Variant::with('product', 'batches')->findOrFail($variantId);



        $cart[$variantId]['quantity'] = $quantity;

        Cookie::queue($this->saveCartData($cart));

        $itemTotal = $variant->product->sale_price * $quantity;

        $variantIds = array_keys($cart);
        $variants = Variant::with('product')->findMany($variantIds)->keyBy('id');

        $cartTotal = 0;
        foreach ($cart as $id => $item) {
            $v = $variants->get((int) $id);
            if ($v && $v->product) {
                $cartTotal += $v->product->sale_price * $item['quantity'];
            }
        }

        return response()->json([
            'success' => true,
            'quantity' => $quantity,
            'itemTotal' => $itemTotal,
            'cartTotal' => $cartTotal,
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
}
