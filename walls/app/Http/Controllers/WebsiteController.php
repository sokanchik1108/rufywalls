<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\Variant;

class WebsiteController extends Controller
{
    public function website()
    {
        $products = Product::with('category', 'rooms')->take(3)->get();
        $categories = Category::all();
        $rooms = Room::all();

        return view('website', compact('products', 'categories', 'rooms'));
    }


    public function catalog(Request $request)
    {
        $categories = Category::all();
        $rooms = Room::all();

        // Базовый запрос вариантов с загрузкой связанных товаров
        $variants = Variant::with('product');

        // 🔎 Фильтрация по категории товара
        if ($request->filled('category_id')) {
            $variants->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // 🔎 Фильтрация по комнате
        if ($request->filled('room_id')) {
            $variants->whereHas('product.rooms', function ($q) use ($request) {
                $q->where('rooms.id', $request->room_id);
            });
        }


        // 🔎 По бренду
        if ($request->filled('brand')) {
            $brands = (array) $request->brand;
            $variants->whereHas('product', function ($q) use ($brands) {
                $q->whereIn('brand', $brands);
            });
        }

        // 🔎 По материалу
        if ($request->filled('material')) {
            $materials = (array) $request->material;
            $variants->whereHas('product', function ($q) use ($materials) {
                $q->whereIn('material', $materials);
            });
        }

        // 🔎 По наличию
        if ($request->filled('in_stock')) {
            $variants->where('stock', '>', 0);
        }

        // 🔎 По цене
        if ($request->filled('price_min')) {
            $variants->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $variants->where('price', '<=', $request->price_max);
        }

        // 🔎 По цвету (если ты сохраняешь в JSON или строку)
        if ($request->filled('color')) {
            $colors = (array) $request->color;
            $variants->whereIn('color', $colors);
        }


        // 🔍 Поиск по названию
        if ($request->filled('search')) {
            $search = $request->search;
            $variants->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // 🔃 Сортировка
        switch ($request->input('sort')) {
            case 'price_asc':
                $variants->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $variants->orderBy('price', 'desc');
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

        // 🔢 Пагинация
        $variants = $variants->paginate(7)->withQueryString();

        if ($request->ajax()) {
            return view('partials.products', compact('variants'))->render();
        }

        // 🔄 Сбор всех брендов/материалов из Product
        $brands = Product::distinct()->pluck('brand');
        $materials = Product::distinct()->pluck('material');
        $colors = Variant::whereNotNull('color')
            ->distinct()
            ->pluck('color')
            ->values();


        return view('catalog', [
            'variants' => $variants,
            'categories' => $categories,
            'rooms' => $rooms,
            'brands' => $brands,
            'materials' => $materials,
            'colors' => $colors,
        ]);
    }



    public function show($id)
    {
        // Загружаем варианты, без with('color')
        $product = Product::with(['category', 'rooms'])->findOrFail($id);
        $variants = $product->variants()->get();
        $activeVariant = $variants->first(); // первый по умолчанию

        return view('product-page', compact('product', 'variants', 'activeVariant'));
    }

    public function variantData($id)
    {
        // Просто находим вариант без with('color')
        $variant = Variant::findOrFail($id);

        return response()->json([
            'id' => $variant->id,
            'sku' => $variant->sku,
            'stock' => $variant->stock,
            'color' => $variant->color, // просто строка
            'images' => json_decode($variant->images),
        ]);
    }



    private function getCartData()
    {
        return json_decode(Cookie::get('cart', '{}'), true) ?? [];
    }

    private function saveCartData(array $cart)
    {
        return Cookie::make('cart', json_encode($cart), 60 * 24 * 7); // 7 дней
    }

    public function cart()
    {
        $cart = $this->getCartData();
        $products = Product::whereIn('id', array_keys($cart))->get();

        $cartItems = $products->map(function ($product) use ($cart) {
            $quantity = $cart[$product->id]['quantity'];

            // Получаем первое изображение из JSON-поля images
            $images = json_decode($product->images, true);
            $image = $images[0] ?? null;

            return [
                'product' => $product,
                'quantity' => $quantity,
                'total' => $product->sale_price * $quantity,
                'image' => $image,
            ];
        });

        $total = $cartItems->sum('total');

        return view('index', compact('cartItems', 'total'));
    }


    public function addToCart(Request $request, $id)
    {
        $cart = $this->getCartData();
        $quantity = max(1, (int) $request->input('quantity', 1));

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = ['quantity' => $quantity];
        }

        $totalCount = array_sum(array_column($cart, 'quantity'));

        Cookie::queue($this->saveCartData($cart));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Товар добавлен в корзину',
                'cart_count' => $totalCount,
            ]);
        }

        return redirect()->back()->with('success', 'Товар добавлен в корзину');
    }

    public function updateCart(Request $request, $id)
    {
        $cart = $this->getCartData();
        $quantity = max(1, (int) $request->input('quantity', 1));

        if (!isset($cart[$id])) {
            return response()->json(['error' => 'Товар не найден в корзине'], 404);
        }

        // Обновляем количество
        $cart[$id]['quantity'] = $quantity;

        // Получаем товар из базы
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Товар не найден в базе'], 404);
        }

        $itemTotal = $product->sale_price * $quantity;

        // Сумма всей корзины
        $cartTotal = 0;
        foreach ($cart as $cartId => $cartItem) {
            $p = Product::find($cartId);
            if ($p) {
                $cartTotal += $p->sale_price * $cartItem['quantity'];
            }
        }

        return response()->json([
            'success' => true,
            'quantity' => $quantity,
            'itemTotal' => number_format($itemTotal, 2),
            'cartTotal' => number_format($cartTotal, 2),
        ])->withCookie($this->saveCartData($cart));
    }




    public function removeFromCart($id)
    {
        $cart = $this->getCartData();
        unset($cart[$id]);

        return redirect()->back()->withCookie($this->saveCartData($cart));
    }

    public function clearCart()
    {
        return redirect()->back()->withCookie(Cookie::forget('cart'));
    }

    public function updateAjax(Request $request, $id)
    {
        $quantity = max((int) $request->input('quantity', 1), 1);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $quantity;
            $cart[$id]['total'] = $quantity * $cart[$id]['product']->sale_price;
            session()->put('cart', $cart);

            // Пересчитать общую сумму
            $total = collect($cart)->sum('total');

            return response()->json([
                'success' => true,
                'quantity' => $quantity,
                'itemTotal' => number_format($cart[$id]['total'], 2),
                'cartTotal' => number_format($total, 2),
            ]);
        }

        return response()->json(['success' => false], 404);
    }
}
