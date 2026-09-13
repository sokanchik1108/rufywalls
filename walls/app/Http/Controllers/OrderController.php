<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PaymentMethod;
use App\Models\PointOfSale;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = json_decode(Cookie::get('cart', '{}'), true);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Ваша корзина пуста.');
        }

        $variantIds = array_keys($cart);

        $variants = Variant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

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
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\s\-]+$/u'
            ],
            'phone' => [
                'required',
                'string',
                'min:9',
                'max:30',
                'regex:/^\+?[0-9\s\-\(\)]+$/'
            ],
            'comment' => ['nullable', 'string', 'max:500'],
        ], [
            'name.regex' => 'Имя должно содержать только буквы, пробелы и дефисы.',
            'phone.regex' => 'Телефон может содержать только цифры, пробелы, скобки, тире и может начинаться с +.',
            'phone.min' => 'Телефон слишком короткий. Укажите не менее 10 символов.',
        ]);

        $cart = json_decode(Cookie::get('cart', '{}'), true);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Корзина пуста.');
        }

        $order = Order::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'comment' => $request->comment,
            'status' => 'Новый',
            'is_website' => true,
        ]);

        foreach ($cart as $variantId => $item) {
            $variant = Variant::with('product')->find($variantId);

            if (!$variant) {
                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $variantId,
                'quantity' => $item['quantity'] ?? 1,
                'price' => $item['price'] ?? 0,
                'image' => $item['image'] ?? (json_decode($variant->images)[0] ?? null),
            ]);
        }

        Cookie::queue(Cookie::forget('cart'));

        $whatsappLink = 'https://wa.me/77773555704?text=Я%20подтверждаю%20заказ%20на%20сайте';

        $message = 'Спасибо за заказ!<br>Чтобы мы начали обработку, пожалуйста, подтвердите его в WhatsApp.<br>Это займёт всего пару секунд.<br><br>';

        $message .= '<a href="' . $whatsappLink . '" class="btn btn-success btn-sm mt-2" target="_blank">🔗 Подтвердить заказ в WhatsApp</a>';

        return redirect()
            ->route('cart')
            ->with('success_html', $message);
    }

    public function indexWebsite()
    {
        $orders = Order::with('payments')
            ->where('is_website', true)
            ->latest()
            ->get();

        return view('admin.orders.orders_website', compact('orders'));
    }

    // Заказы продавцов
    public function create()
    {
        $warehouses = \App\Models\Warehouse::all();
        $pointsOfSale = \App\Models\PointOfSale::orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        return view('admin.orders.create', compact(
            'warehouses',
            'pointsOfSale',
            'paymentMethods'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'comment' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',

            'order_date' => 'required|date',
            'point_of_sale_id' => 'required|exists:points_of_sale,id',

            'items' => 'required|array|min:1',
            'items.*.sku' => 'required|exists:variants,sku',
            'items.*.batch_id' => 'required|integer|exists:batches,id',
            'items.*.warehouse_id' => 'nullable|integer|exists:warehouses,id',
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.batch_code' => 'required|string',
            'items.*.warehouse_name' => 'nullable|string',

            'payments' => 'nullable|array',

            /*
             * Способ оплаты больше не ограничен
             * cash / qr / transfer.
             *
             * Теперь можно использовать любое количество
             * способов оплаты.
             */
            'payments.*.payment_method' => [
                'required',
                'string',
                'max:255',
            ],

            'payments.*.amount' => [
                'required',
                'numeric',
            ],
        ], [
            'payments.*.payment_method.required' => 'Выберите способ оплаты.',
            'payments.*.amount.required' => 'Введите сумму оплаты.',
        ]);

        /*
         * Сначала рассчитываем сумму заказа.
         */
        $itemsTotal = 0;

        foreach ($request->items as $item) {
            $quantity = (int) $item['quantity'];
            $price = (float) $item['price'];

            $itemsTotal += $quantity * $price;
        }

        $discount = (float) ($request->discount ?? 0);

        $orderTotal = $itemsTotal - $discount;

        if ($orderTotal < 0) {
            $orderTotal = 0;
        }

        /*
         * Считаем сумму всех способов оплаты.
         */
        $paymentsTotal = collect($request->input('payments', []))
            ->sum(function ($payment) {
                return (float) ($payment['amount'] ?? 0);
            });

        /*
         * Если заказ положительный, оплаты должны полностью
         * соответствовать сумме заказа.
         */
        if ($orderTotal > 0) {

            if (empty($request->payments)) {
                return back()
                    ->withInput()
                    ->with('error', 'Добавьте способ оплаты.');
            }

            if (abs($paymentsTotal - $orderTotal) > 0.01) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Сумма оплат (' .
                            number_format($paymentsTotal, 2, '.', ' ') .
                            ' ₸) не соответствует сумме заказа (' .
                            number_format($orderTotal, 2, '.', ' ') .
                            ' ₸).'
                    );
            }
        }

        $order = Order::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'comment' => $request->comment,
            'discount' => $request->discount ?? 0,
            'status' => 'Новый',
            'is_website' => false,
            'point_of_sale_id' => $request->point_of_sale_id,

            'order_date' => $request->order_date
                ? Carbon::parse($request->order_date, 'Asia/Almaty')
                : now('Asia/Almaty'),
        ]);

        foreach ($request->items as $item) {

            if (!isset($item['warehouse_id']) || !isset($item['quantity'])) {
                continue;
            }

            $batchId = $item['batch_id'];
            $warehouseId = $item['warehouse_id'];
            $quantity = (int) $item['quantity'];
            $price = (float) $item['price'];

            $batch = Batch::findOrFail($batchId);

            $variant = Variant::where('sku', $item['sku'])->firstOrFail();

            if ($batch->variant_id !== $variant->id) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "Партия {$batch->batch_code} не принадлежит артикулу {$variant->sku}"
                    );
            }

            $pivot = $batch->warehouses()
                ->where('warehouse_id', $warehouseId)
                ->first();

            if (!$pivot) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "Склад не найден для SKU {$variant->sku}"
                    );
            }

            $currentStock = $pivot->pivot->quantity;

            if ($quantity > 0 && $currentStock < $quantity) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "Недостаточно товара на складе для SKU {$variant->sku}"
                    );
            }

            $batch->warehouses()->updateExistingPivot($warehouseId, [
                'quantity' => $currentStock - $quantity
            ]);

            $order->items()->create([
                'variant_id' => $variant->id,
                'batch_id' => $batchId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'price' => $price,
                'image' => $variant->image ?? '',
                'batch_code' => $item['batch_code'],
                'warehouse_name' => $item['warehouse_name'] ?? '',
                'order_date' => $request->order_date,
            ]);
        }

        /*
         * Сохраняем каждую оплату отдельно.
         *
         * Способ оплаты теперь не ограничен тремя вариантами.
         */
        foreach ($request->input('payments', []) as $payment) {

            $order->payments()->create([
                'payment_method' => trim($payment['payment_method']),
                'amount' => $payment['amount'],
            ]);
        }

        return redirect()
            ->route('admin.orders.seller', [
                'date' => $request->order_date
            ])
            ->with('success', 'Заказ успешно создан');
    }

    public function indexSeller(Request $request)
    {
        $date = $request->get('date');

        if (!$date) {
            $date = now('Asia/Almaty');
        } else {
            $date = Carbon::parse($date, 'Asia/Almaty');
        }

        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $orders = Order::with([
            'items.variant.product',
            'pointOfSale',
            'payments'
        ])
            ->whereBetween(
                DB::raw('COALESCE(order_date, created_at)'),
                [$start, $end]
            )
            ->latest()
            ->get();

        /*
         * Общая сумма по ВСЕМ способам оплаты за день.
         *
         * Никаких cash / qr / transfer здесь больше нет.
         */
        $paymentTotals = [];

        foreach ($orders as $order) {

            foreach ($order->payments as $payment) {

                $method = trim($payment->payment_method);

                if ($method === '') {
                    $method = 'Без способа оплаты';
                }

                if (!isset($paymentTotals[$method])) {
                    $paymentTotals[$method] = 0;
                }

                $paymentTotals[$method] += (float) $payment->amount;
            }
        }

        return view('admin.orders.orders_seller', compact(
            'orders',
            'paymentTotals'
        ));
    }

    public function search(Request $request)
    {
        $q = trim($request->q);

        $orders = Order::with([
            'items',
            'items.variant',
            'pointOfSale',
            'payments'
        ])
            ->where(function ($query) use ($q) {

                $query->where('id', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhereHas('items.variant', function ($variantQuery) use ($q) {
                        $variantQuery->where('sku', 'like', "%{$q}%");
                    });
            })
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function edit($id)
    {
        $order = Order::with([
            'items.variant.product',
            'items.batch.warehouses',
            'payments'
        ])->findOrFail($id);

        $warehouses = \App\Models\Warehouse::all();

        $pointsOfSale = PointOfSale::orderBy('name')->get();

        $paymentMethods = PaymentMethod::orderBy('name')->get();

        return view(
            'admin.orders.edit',
            compact(
                'order',
                'warehouses',
                'pointsOfSale',
                'paymentMethods'
            )
        );
    }


    public function update(Request $request, $id)
    {
        $order = Order::with('items')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'comment' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'point_of_sale_id' => 'nullable|integer|exists:points_of_sale,id',

            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:order_items,id',
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|numeric|min:0',

            'payments' => 'nullable|array',

            /*
         * Способ оплаты может быть пустым.
         * Это нужно для старых заказов,
         * где способ оплаты не был указан.
         */
            'payments.*.payment_method' => [
                'nullable',
                'string',
                'max:255',
            ],

            'payments.*.amount' => [
                'required',
                'numeric',
            ],
        ], [
            'payments.*.amount.required' => 'Введите сумму оплаты.',
        ]);


        $order->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'comment' => $request->comment,
            'discount' => $request->discount ?? 0,
            'point_of_sale_id' => $request->point_of_sale_id ?: null,
        ]);


        foreach ($request->items as $itemData) {

            $orderItem = $order->items()->find($itemData['id']);

            if ($orderItem) {

                $orderItem->update([
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                ]);
            }
        }


        /*
     * Обновляем оплаты только если поле payments
     * действительно было передано.
     */
        if ($request->has('payments')) {

            $order->payments()->delete();

            foreach ($request->input('payments', []) as $payment) {

                $order->payments()->create([
                    /*
                 * Если способ не выбран —
                 * сохраняем пустую строку, а не NULL.
                 *
                 * В базе payment_method имеет NOT NULL.
                 */
                    'payment_method' => !empty($payment['payment_method'])
                        ? trim($payment['payment_method'])
                        : '',

                    'amount' => $payment['amount'],
                ]);
            }
        }


        return redirect()
            ->route('admin.orders.seller', [
                'date' => $order->order_date
                    ? Carbon::parse(
                        $order->order_date,
                        'Asia/Almaty'
                    )->format('Y-m-d')
                    : Carbon::parse(
                        $order->created_at,
                        'Asia/Almaty'
                    )->format('Y-m-d'),
            ])
            ->with('success', 'Заказ успешно обновлён');
    }

    
    public function paymentMethods()
    {
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        return view(
            'admin.orders.payment-methods',
            compact('paymentMethods')
        );
    }

    public function storePaymentMethod(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
        ], [
            'name.required' => 'Введите название способа оплаты.',
            'name.unique' => 'Такой способ оплаты уже существует.',
            'name.max' => 'Название слишком длинное.',
        ]);

        PaymentMethod::create([
            'name' => trim($request->name),
        ]);

        return redirect()
            ->route('admin.payment-methods')
            ->with('success', 'Способ оплаты добавлен.');
    }

    public function destroyPaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        if ($paymentMethod->payments()->exists()) {
            return redirect()
                ->route('admin.payment-methods')
                ->with(
                    'error',
                    'Нельзя удалить способ оплаты, который уже используется в заказах.'
                );
        }

        $paymentMethod->delete();

        return redirect()
            ->route('admin.payment-methods')
            ->with('success', 'Способ оплаты удалён.');
    }
}
