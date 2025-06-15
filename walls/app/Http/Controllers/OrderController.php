<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

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
    $whatsappLink = 'https://wa.me/77077121255?text=Я%20подтверждаю%20заказ%20на%20сайте';
    $message = 'Спасибо за заказ!<br>Чтобы мы начали обработку, пожалуйста, подтвердите его в WhatsApp.<br>Это займёт всего пару секунд.<br><br>';
    $message .= '<a href="' . $whatsappLink . '" class="btn btn-success btn-sm mt-2" target="_blank">🔗 Подтвердить заказ в WhatsApp</a>';

    return redirect()
        ->route('cart')
        ->with('success_html', $message);
}





    public function adminIndex()
    {
        $orders = Order::with(['items.variant.product'])->latest()->get();
        return view('admin.orders', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
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
        $order = Order::findOrFail($id);
        $order->delete();

        return back()->with('success', 'Заказ удален.');
    }

    public function clearAll()
    {
        Order::truncate(); // Удалит все заказы (и их items если настроено каскадное удаление)
        return redirect()->back()->with('success', 'Все заказы удалены.');
    }
}
