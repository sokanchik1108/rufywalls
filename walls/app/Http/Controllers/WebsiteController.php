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

        $variants = Variant::with(['product', 'batches']);

        if ($request->filled('category_id')) {
            $variants->whereHas('product.categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }



        if ($request->filled('room_id')) {
            $variants->whereHas('product.rooms', fn($q) => $q->where('rooms.id', $request->room_id));
        }

        if ($request->filled('brand')) {
            $brands = (array) $request->brand;
            $variants->whereHas('product', fn($q) => $q->whereIn('brand', $brands));
        }

        if ($request->filled('material')) {
            $materials = (array) $request->material;
            $variants->whereHas('product', fn($q) => $q->whereIn('material', $materials));
        }

        if ($request->filled('in_stock')) {
            $variants->whereHas('batches', fn($q) => $q->where('stock', '>', 0));
        }

        if ($request->filled('sticking')) {
            $variants->whereHas('product', function ($q) use ($request) {
                if ($request->sticking === 'yes') {
                    $q->whereRaw("LOWER(sticking) != 'Нет'");
                } elseif ($request->sticking === 'no') {
                    $q->whereRaw("LOWER(sticking) = 'Нет'");
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
            $colors = (array) $request->color;
            $variants->whereIn('color', $colors);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $variants->where(function ($query) use ($searchTerm) {
                $query->where('sku', 'like', "%$searchTerm%")
                    ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%$searchTerm%"));
            });
        }


        switch ($request->input('sort')) {
            case 'price_asc':
                $variants->join('products', 'variants.product_id', '=', 'products.id')
                    ->orderBy('products.sale_price', 'asc');
                break;
            case 'price_desc':
                $variants->join('products', 'variants.product_id', '=', 'products.id')
                    ->orderBy('products.sale_price', 'desc');
                break;
            case 'name_asc':
                $variants->join('products', 'variants.product_id', '=', 'products.id')
                    ->orderBy('products.name', 'asc');
                break;
            case 'name_desc':
                $variants->join('products', 'variants.product_id', '=', 'products.id')
                    ->orderBy('products.name', 'desc');
                break;
            default:
                $variants->latest();
        }

        $variants = $variants->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return view('partials.products', compact('variants'))->render();
        }

        $brands = Product::distinct()->pluck('brand');
        $materials = Product::distinct()->pluck('material');
        $colors = Variant::whereNotNull('color')->distinct()->pluck('color');

        return view('catalog', compact('variants', 'categories', 'rooms', 'brands', 'materials', 'colors'));
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
        $stock = $variant->batches->sum('stock');
        $quantity = max(1, (int) $request->input('quantity', 1));

        if ($quantity > $stock) {
            return response()->json([
                'message' => 'Недостаточно товара на складе',
            ], 400);
        }

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
        $stock = $variant->batches->sum('stock');

        if ($quantity > $stock) {
            return response()->json(['error' => 'Недостаточно на складе'], 400);
        }

        $cart[$variantId]['quantity'] = $quantity;

        Cookie::queue($this->saveCartData($cart));

        $itemTotal = $variant->product->sale_price * $quantity;

        $variantIds = array_keys($cart);
        $variants = Variant::with('product')->findMany($variantIds)->keyBy('id');

        $cartTotal = 0;
        foreach ($cart as $id => $item) {
            $v = $variants->get((int) $id); // Явно приводим к int
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
}
