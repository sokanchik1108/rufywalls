<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\Batch;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = json_decode(Cookie::get('cart', '{}'), true);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Ваша корзина пуста.');
        }

        $variantIds = array_keys($cart);
        $variants = Variant::with('product')->whereIn('id', $variantIds)->get()->keyBy('id');

        $cartItems = collect($cart)->map(function ($item, $id) use ($variants) {
            $variant = $variants[$id];
            return [
                'variant' => $variant,
                'product' => $variant->product,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
                'image' => $item['image'] ?? (json_decode($variant->images)[0] ?? null),
            ];
        });


        $total = $cartItems->sum('total');

        return view('checkout', compact('cartItems', 'total'));
    }


    public function submit(Request $request)
    {
        // Валидация имени и телефона
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\s\-]+$/u' // Только буквы, пробелы и дефисы
            ],
            'phone' => [
                'required',
                'string',
                'min:9', // Минимум 10 символов
                'max:30',
                'regex:/^\+?[0-9\s\-\(\)]+$/' // Только цифры, +, скобки, пробелы, тире
            ],
            'comment' => ['nullable', 'string', 'max:500'],
        ], [
            'name.regex' => 'Имя должно содержать только буквы, пробелы и дефисы.',
            'phone.regex' => 'Телефон может содержать только цифры, пробелы, скобки, тире и может начинаться с +.',
            'phone.min' => 'Телефон слишком короткий. Укажите не менее 10 символов.',
        ]);

        // Получение корзины
        $cart = json_decode(Cookie::get('cart', '{}'), true);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Корзина пуста.');
        }

        // Создание заказа
        $order = Order::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'comment' => $request->comment,
            'status' => 'Новый',
            'is_website' => true, // это заказ с сайта
        ]);

        foreach ($cart as $variantId => $item) {
            $variant = Variant::with('product')->find($variantId);
            if (!$variant) continue;

            OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $variantId,
                'quantity' => $item['quantity'] ?? 1,
                'price' => $item['price'] ?? 0,
                'image' => $item['image'] ?? (json_decode($variant->images)[0] ?? null),
            ]);
        }

        // Очистка корзины
        Cookie::queue(Cookie::forget('cart'));

        // WhatsApp-ссылка
        $whatsappLink = 'https://wa.me/77773555704?text=Я%20подтверждаю%20заказ%20на%20сайте';
        $message = 'Спасибо за заказ!<br>Чтобы мы начали обработку, пожалуйста, подтвердите его в WhatsApp.<br>Это займёт всего пару секунд.<br><br>';
        $message .= '<a href="' . $whatsappLink . '" class="btn btn-success btn-sm mt-2" target="_blank">🔗 Подтвердить заказ в WhatsApp</a>';

        return redirect()
            ->route('cart')
            ->with('success_html', $message);
    }

    public function create()
    {
        $warehouses = \App\Models\Warehouse::all();
        return view('admin.orders.create', compact('warehouses'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'comment' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0', // ✅ добавлено поле скидки
            'items' => 'required|array|min:1',
            'items.*.sku' => 'required|exists:variants,sku',
            'items.*.batch_id' => 'required|integer|exists:batches,id',
            'items.*.warehouse_id' => 'nullable|integer|exists:warehouses,id',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.batch_code' => 'required|string',
            'items.*.warehouse_name' => 'nullable|string',
        ]);

        $order = Order::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'comment' => $request->comment,
            'discount' => $request->discount ?? 0,
            'status' => 'Новый',
            'is_website' => false,
        ]);

        foreach ($request->items as $item) {

            if (empty($item['warehouse_id']) || empty($item['quantity'])) {
                continue;
            }

            $batchId = $item['batch_id'];
            $warehouseId = $item['warehouse_id'];
            $quantity = (int)$item['quantity'];
            $price = (float)$item['price'];

            $batch = Batch::findOrFail($batchId);
            $variant = Variant::where('sku', $item['sku'])->firstOrFail();

            if ($batch->variant_id !== $variant->id) {
                return back()->with('error', "Партия {$batch->batch_code} не принадлежит артикулу {$variant->sku}");
            }

            $pivot = $batch->warehouses()->where('warehouse_id', $warehouseId)->first();

            if (!$pivot || $pivot->pivot->quantity < $quantity) {
                return back()->with('error', "Недостаточно товара на складе для SKU {$variant->sku}");
            }

            // уменьшаем количество на складе
            $batch->warehouses()->updateExistingPivot($warehouseId, [
                'quantity' => $pivot->pivot->quantity - $quantity
            ]);

            // создаём позицию заказа
            $order->items()->create([
                'variant_id' => $variant->id,
                'batch_id' => $batchId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'price' => $price,
                'image' => $variant->image ?? '',
                'batch_code' => $item['batch_code'],
                'warehouse_name' => $item['warehouse_name'] ?? '',
            ]);
        }

        return redirect()->route('admin.orders.seller')->with('success', 'Заказ успешно создан');
    }

    // Заказы с сайта
    public function indexWebsite()
    {
        $orders = Order::where('is_website', true)->latest()->get();
        return view('admin.orders.orders_website', compact('orders'));
    }

    // Заказы продавцов
    public function indexSeller(Request $request)
    {
        $date = $request->get('date') ?? now()->format('Y-m-d');

        $orders = Order::with(['items.variant.product'])
            ->whereBetween('created_at', [
                Carbon::parse($date)->startOfDay(),
                Carbon::parse($date)->endOfDay()
            ])
            ->latest()
            ->get();

        return view('admin.orders.orders_seller', compact('orders'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q');

        $orders = Order::with('items')
            ->where('id', 'like', "%$q%")
            ->orWhere('phone', 'like', "%$q%")
            ->orWhere('name', 'like', "%$q%")
            ->latest()
            ->limit(20)
            ->get();

        return response()->json($orders);
    }
}
