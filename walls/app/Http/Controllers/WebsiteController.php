<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

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
        $query = Product::query();

        // Фильтрация
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('brand')) {
            $query->whereIn('brand', (array) $request->input('brand'));
        }
        if ($request->filled('material')) {
            $query->whereIn('material', (array) $request->input('material'));
        }
        if ($request->filled('color')) {
            $query->whereIn('color', (array) $request->input('color'));
        }
        if ($request->boolean('in_stock')) {
            $query->where('quantity', '>', 0);
        }
        if ($request->filled('room_id')) {
            $query->whereHas('rooms', function ($q) use ($request) {
                $q->whereIn('rooms.id', (array) $request->input('room_id'));
            });
        }
        if ($request->filled('price_min')) {
            $query->where('sale_price', '>=', $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('sale_price', '<=', $request->input('price_max'));
        }

        // Поиск
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('article', 'like', '%' . $search . '%');
            });
        }

        // Сортировка
        if ($request->filled('sort')) {
            switch ($request->input('sort')) {
                case 'price_asc':
                    $query->orderBy('sale_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('sale_price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
            }
        }

        $products = $query->paginate(9)->appends($request->query());

        if ($request->ajax()) {
            return view('partials.products', compact('products'))->render();
        }

        return view('catalog', [
            'products' => $products,
            'rooms' => Room::all(),
            'categories' => Category::all(),
            'brands' => Product::distinct()->pluck('brand')->filter()->values(),
            'materials' => Product::distinct()->pluck('material')->filter()->values(),
            'colors' => Product::distinct()->pluck('color')->filter()->values(),
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'rooms'])->findOrFail($id);
        return view('product-page', compact('product'));
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
