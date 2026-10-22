<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use App\Models\Batch;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentMethod;
use App\Models\PointOfSale;
use App\Models\Warehouse;
use App\Models\StockMovement;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use RuntimeException;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | САЙТ — ОФОРМЛЕНИЕ
    |--------------------------------------------------------------------------
    */

    public function checkout(Request $request)
    {
        $cart = json_decode(Cookie::get('cart', '{}'), true);

        if (empty($cart)) {
            return redirect()->route('cart')
                ->with('error', 'Ваша корзина пуста.');
        }

        $variantIds = array_keys($cart);

        $variants = Variant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $cartItems = collect($cart)->map(function ($item, $id) use ($variants) {
            $variant = $variants->get($id);

            if (!$variant) {
                return null;
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $price = max(0, (float) ($item['price'] ?? 0));

            return [
                'variant' => $variant,
                'product' => $variant->product,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $price * $quantity,
                'image' => $item['image']
                    ?? (json_decode($variant->images, true)[0] ?? null),
            ];
        })->filter();

        $total = $cartItems->sum('total');

        return view('checkout', compact('cartItems', 'total'));
    }

    /*
    |--------------------------------------------------------------------------
    | САЙТ — СОЗДАНИЕ ЗАКАЗА
    |--------------------------------------------------------------------------
    */

    public function submit(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\s\-]+$/u',
            ],
            'phone' => [
                'required',
                'string',
                'min:9',
                'max:30',
                'regex:/^\+?[0-9\s\-\(\)]+$/',
            ],
            'comment' => ['nullable', 'string', 'max:500'],
        ], [
            'name.regex' => 'Имя должно содержать только буквы, пробелы и дефисы.',
            'phone.regex' => 'Телефон может содержать только цифры, пробелы, скобки, тире и может начинаться с +.',
            'phone.min' => 'Телефон слишком короткий. Укажите не менее 10 символов.',
        ]);

        $cart = json_decode(Cookie::get('cart', '{}'), true);

        if (empty($cart)) {
            return redirect()->route('cart')
                ->with('error', 'Корзина пуста.');
        }

        $order = DB::transaction(function () use ($request, $cart) {
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

                $quantity = filter_var(
                    $item['quantity'] ?? 1,
                    FILTER_VALIDATE_INT
                );

                if ($quantity === false || $quantity < 1) {
                    throw new RuntimeException(
                        'Количество товара должно быть целым числом не меньше 1.'
                    );
                }

                $price = (float) ($item['price'] ?? 0);

                if ($price < 0) {
                    throw new RuntimeException(
                        'Цена товара не может быть отрицательной.'
                    );
                }

                $images = json_decode($variant->images, true);

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'image' => $item['image']
                        ?? (is_array($images) ? ($images[0] ?? null) : null),
                ]);
            }

            return $order;
        });

        Cookie::queue(Cookie::forget('cart'));

        $whatsappLink = 'https://wa.me/77773555704?text=Я%20подтверждаю%20заказ%20на%20сайте';

        $message = 'Спасибо за заказ!<br>' .
            'Чтобы мы начали обработку, пожалуйста, ' .
            'подтвердите его в WhatsApp.<br>' .
            'Это займёт всего пару секунд.<br><br>';

        $message .= '<a href="' . $whatsappLink .
            '" class="btn btn-success btn-sm mt-2" target="_blank">' .
            '🔗 Подтвердить заказ в WhatsApp</a>';

        return redirect()->route('cart')
            ->with('success_html', $message);
    }

    /*
    |--------------------------------------------------------------------------
    | ЗАКАЗЫ С САЙТА
    |--------------------------------------------------------------------------
    */

    public function indexWebsite()
    {
        $orders = Order::with('payments')
            ->where('is_website', true)
            ->latest()
            ->get();

        return view('admin.orders.orders_website', compact('orders'));
    }

    /*
    |--------------------------------------------------------------------------
    | СОЗДАНИЕ ПРОДАЖИ ПРОДАВЦОМ
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $warehouses = Warehouse::all();
        $pointsOfSale = PointOfSale::orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        $defaultPointOfSaleId = auth()->user()->point_of_sale_id ?? null;

        return view('admin.orders.create', compact(
            'warehouses',
            'pointsOfSale',
            'paymentMethods',
            'defaultPointOfSaleId'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | СОХРАНЕНИЕ ПРОДАЖИ
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, FifoService $fifoService)
    {
        if (
            !$request->filled('point_of_sale_id') &&
            auth()->check() &&
            auth()->user()->point_of_sale_id
        ) {
            $request->merge([
                'point_of_sale_id' => auth()->user()->point_of_sale_id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | НОРМАЛИЗАЦИЯ СУММ ОПЛАТ
        |--------------------------------------------------------------------------
        | Отрицательные суммы оплат также запрещены.
        */

        if ($request->has('payments')) {
            $payments = $request->input('payments', []);

            foreach ($payments as $index => $payment) {
                if (isset($payment['amount']) && is_string($payment['amount'])) {
                    $amount = trim($payment['amount']);
                    $amount = str_replace(' ', '', $amount);
                    $amount = str_replace(',', '.', $amount);

                    $payments[$index]['amount'] = $amount;
                }
            }

            $request->merge(['payments' => $payments]);
        }

        /*
        |--------------------------------------------------------------------------
        | ВАЛИДАЦИЯ
        |--------------------------------------------------------------------------
        */

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
            'items.*.warehouse_id' => 'required|integer|exists:warehouses,id',
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.batch_code' => 'required|string',
            'items.*.warehouse_name' => 'nullable|string',

            'payments' => 'nullable|array',
            'payments.*.payment_method' => [
                'required',
                'string',
                'max:255',
            ],
            'payments.*.amount' => [
                'required',
                'numeric',
                'min:0',
            ],
        ], [
            'items.*.quantity.required' => 'Укажите количество товара.',
            'items.*.quantity.integer' => 'Количество должно быть целым числом.',
            'items.*.quantity.min' => 'Количество товара должно быть не меньше 1.',
            'items.*.price.min' => 'Цена не может быть отрицательной.',
            'payments.*.amount.min' => 'Сумма оплаты не может быть отрицательной.',
            'payments.*.payment_method.required' => 'Выберите способ оплаты.',
            'payments.*.amount.required' => 'Введите сумму оплаты.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | СУММА ЗАКАЗА
        |--------------------------------------------------------------------------
        */

        $itemsTotal = 0;

        foreach ($request->items as $item) {
            $quantity = (int) $item['quantity'];

            if ($quantity < 1) {
                return back()->withInput()->with(
                    'error',
                    'Количество товара должно быть не меньше 1.'
                );
            }

            $itemsTotal += $quantity * (float) $item['price'];
        }

        $discount = (float) ($request->discount ?? 0);
        $orderTotal = $itemsTotal - $discount;

        if ($orderTotal < 0) {
            return back()->withInput()->with(
                'error',
                'Скидка не может превышать сумму заказа.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | СУММА ОПЛАТ
        |--------------------------------------------------------------------------
        */

        $paymentsTotal = collect($request->input('payments', []))
            ->sum(fn($payment) => (float) ($payment['amount'] ?? 0));

        if (abs($paymentsTotal - $orderTotal) > 0.01) {
            if (empty($request->payments)) {
                return back()->withInput()->with(
                    'error',
                    'Добавьте способ оплаты.'
                );
            }

            return back()->withInput()->with(
                'error',
                'Сумма оплат (' .
                    number_format($paymentsTotal, 2, '.', ' ') .
                    ' ₸) не соответствует сумме заказа (' .
                    number_format($orderTotal, 2, '.', ' ') .
                    ' ₸).'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | СОЗДАНИЕ ЗАКАЗА И СПИСАНИЕ ТОВАРА
        |--------------------------------------------------------------------------
        */

        try {
            DB::transaction(function () use ($request, $fifoService) {
                $orderDate = Carbon::parse(
                    $request->order_date,
                    'Asia/Almaty'
                );

                $order = Order::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'comment' => $request->comment,
                    'discount' => $request->discount ?? 0,
                    'status' => 'Новый',
                    'is_website' => false,
                    'point_of_sale_id' => $request->point_of_sale_id,
                    'order_date' => $orderDate,
                ]);

                foreach ($request->items as $item) {
                    $variant = Variant::with('product')
                        ->where('sku', $item['sku'])
                        ->firstOrFail();

                    $batch = Batch::findOrFail($item['batch_id']);

                    if ((int) $batch->variant_id !== (int) $variant->id) {
                        throw new RuntimeException(
                            "Партия {$batch->batch_code} не принадлежит артикулу {$variant->sku}."
                        );
                    }

                    $quantity = (int) $item['quantity'];

                    if ($quantity < 1) {
                        throw new RuntimeException(
                            'Количество товара должно быть не меньше 1.'
                        );
                    }

                    $image = '';

                    if (!empty($variant->images)) {
                        $images = json_decode($variant->images, true);

                        if (is_array($images) && !empty($images[0])) {
                            $image = $images[0];
                        }
                    }

                    $orderItem = $order->items()->create([
                        'variant_id' => $variant->id,
                        'quantity' => $quantity,
                        'price' => (float) $item['price'],
                        'image' => $image,
                        'batch_code' => $item['batch_code'],
                        'warehouse_name' => $item['warehouse_name'] ?? '',
                    ]);

                    // Только обычная продажа. Возвратов в этом методе нет.
                    $fifoService->consume(
                        (int) $item['warehouse_id'],
                        (int) $variant->id,
                        $quantity,
                        'sale',
                        $orderItem->id,
                        $orderDate,
                        (int) $item['batch_id']
                    );
                }

                foreach ($request->input('payments', []) as $payment) {
                    $order->payments()->create([
                        'payment_method' => trim($payment['payment_method']),
                        'amount' => $payment['amount'],
                    ]);
                }
            });
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.orders.seller', [
                'date' => $request->order_date,
            ])
            ->with('success', 'Заказ успешно создан');
    }

    /*
    |--------------------------------------------------------------------------
    | ЗАКАЗЫ ПРОДАВЦОВ
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Доступ запрещён');
        }

        try {
            DB::transaction(function () use ($id) {
                $order = Order::query()
                    ->whereKey($id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Получаем позиции заказа до его удаления.
                $items = OrderItem::query()
                    ->where('order_id', $order->id)
                    ->lockForUpdate()
                    ->get();

                foreach ($items as $item) {
                    // Находим FIFO-списания конкретной позиции заказа.
                    $movements = StockMovement::query()
                        ->where('type', 'sale')
                        ->where('source_id', $item->id)
                        ->lockForUpdate()
                        ->get();

                    foreach ($movements as $movement) {
                        app(\App\Services\FifoService::class)
                            ->reverseMovement($movement);
                    }
                }

                // Удаляем заказ только после отмены списаний.
                $order->delete();
            });

            return back()->with('success', 'Заказ удален.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Не удалось удалить заказ. Проверьте связанные списания и платежи.'
            );
        }
    }



    public function indexSeller(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'), 'Asia/Almaty')
            : now('Asia/Almaty');

        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $sku = trim((string) $request->input('sku', ''));
        $search = trim((string) $request->input('q', ''));

        $isSearching = $sku !== '' || $search !== '';

        $ordersQuery = Order::with([
            'items.variant.product',
            'pointOfSale',
            'payments',
        ]);

        if ($isSearching) {
            $monthStart = now('Asia/Almaty')->subDays(29)->startOfDay();
            $monthEnd = now('Asia/Almaty')->endOfDay();

            $ordersQuery->whereBetween(
                DB::raw('COALESCE(order_date, created_at)'),
                [$monthStart, $monthEnd]
            );

            if ($sku !== '') {
                // Артикул ищется строго, без частичных совпадений.
                $ordersQuery->whereHas('items.variant', function ($query) use ($sku) {
                    $query->where('sku', $sku);
                });
            } else {
                $orderNumber = trim(
                    preg_replace('/^\s*#\s*/', '', $search)
                );

                $phoneDigits = preg_replace('/\D+/', '', $search);

                $isNumericSearch = (bool) preg_match(
                    '/^#?\s*\d+\s*$/',
                    $search
                );

                $isOrderNumber = $isNumericSearch
                    && $orderNumber !== ''
                    && ctype_digit($orderNumber);

                $ordersQuery->where(function ($query) use (
                    $search,
                    $orderNumber,
                    $phoneDigits,
                    $isOrderNumber,
                    $isNumericSearch
                ) {
                    $hasCondition = false;

                    // Число или #число — точный номер заказа.
                    if ($isOrderNumber) {
                        $query->where('id', (int) $orderNumber);
                        $hasCondition = true;
                    }

                    // Текстовый поиск по имени.
                    // Для запроса только из цифр имя не проверяется.
                    if (!$isNumericSearch && $search !== '') {
                        if ($hasCondition) {
                            $query->orWhere(
                                'name',
                                'like',
                                '%' . $search . '%'
                            );
                        } else {
                            $query->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            );
                        }

                        $hasCondition = true;
                    }

                    // Телефон проверяется только по полному номеру.
                    if (strlen($phoneDigits) >= 7) {
                        $phoneCondition = function ($phoneQuery) use ($phoneDigits) {
                            $phoneQuery->whereRaw(
                                "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(phone, ''), ' ', ''), '-', ''), '+', ''), '(', ''), ')', ''), '.', ''), '/', '') = ?",
                                [$phoneDigits]
                            );
                        };

                        if ($hasCondition) {
                            $query->orWhere($phoneCondition);
                        } else {
                            $phoneCondition($query);
                        }
                    }
                });
            }
        } else {
            $ordersQuery->whereBetween(
                DB::raw('COALESCE(order_date, created_at)'),
                [$start, $end]
            );
        }

        $orders = $ordersQuery->latest()->get();

        $orderIds = $orders->pluck('id');

        $returnDetails = \App\Models\CustomerReturn::with([
            'items.variant',
            'items.orderItem.variant',
            'payments.paymentMethod',
        ])
            ->whereIn('order_id', $orderIds)
            ->get()
            ->groupBy('order_id');

        $ordersWithReturns = $returnDetails
            ->keys()
            ->map(fn($id) => (int) $id)
            ->toArray();

        $paymentTotals = [];

        // Дневные итоги считаются только при просмотре дня без поиска.
        if (!$isSearching) {
            foreach ($orders as $order) {
                foreach ($order->payments as $payment) {
                    $method = trim((string) $payment->payment_method);

                    if ($method === '') {
                        $method = 'Без способа оплаты';
                    }

                    $paymentTotals[$method] =
                        ($paymentTotals[$method] ?? 0)
                        + (float) $payment->amount;
                }

                foreach ($returnDetails->get($order->id, collect()) as $return) {
                    foreach ($return->payments as $payment) {
                        $method = trim((string) (
                            $payment->paymentMethod?->name
                            ?? $payment->payment_method
                            ?? ''
                        ));

                        if ($method === '') {
                            $method = 'Без способа оплаты';
                        }

                        $paymentTotals[$method] =
                            ($paymentTotals[$method] ?? 0)
                            - (float) $payment->amount;
                    }
                }
            }

            $paymentTotals = array_filter(
                $paymentTotals,
                fn($amount) => abs($amount) > 0.001
            );
        }

        return view('admin.orders.orders_seller', compact(
            'orders',
            'paymentTotals',
            'ordersWithReturns',
            'returnDetails'
        ));
    }


    /**
     * Автодополнение артикулов по заказам за последние 30 дней.
     */
    public function sellerSkuSuggestions(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $monthStart = now('Asia/Almaty')->subDays(29)->startOfDay();
        $monthEnd = now('Asia/Almaty')->endOfDay();

        $orders = Order::with('items.variant')
            ->whereBetween(
                DB::raw('COALESCE(order_date, created_at)'),
                [$monthStart, $monthEnd]
            )
            ->whereHas('items.variant', function ($query) use ($term) {
                $query->where('sku', 'like', '%' . $term . '%');
            })
            ->get();

        $skus = $orders
            ->flatMap(fn($order) => $order->items)
            ->map(fn($item) => $item->variant?->sku)
            ->filter()
            ->unique()
            ->filter(fn($value) => stripos((string) $value, $term) !== false)
            ->sort()
            ->take(10)
            ->values();

        return response()->json($skus);
    }



    /*
    |--------------------------------------------------------------------------
    | ПОИСК
    |--------------------------------------------------------------------------
    */

    public function search(Request $request)
    {
        $q = trim($request->q);

        $orders = Order::with([
            'items',
            'items.variant',
            'pointOfSale',
            'payments',
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

    /*
    |--------------------------------------------------------------------------
    | РЕДАКТИРОВАНИЕ
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $order = Order::with([
            'items.variant.product',
            'items',
            'payments',
        ])->findOrFail($id);

        $warehouses = Warehouse::all();
        $pointsOfSale = PointOfSale::orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        $customerReturns = \App\Models\CustomerReturn::where(
            'order_id',
            $order->id
        )
            ->orderByDesc('id')
            ->get();

        $returnSummary = DB::table('customer_return_items as cri')
            ->join(
                'customer_returns as cr',
                'cr.id',
                '=',
                'cri.customer_return_id'
            )
            ->leftJoin(
                'order_items as oi',
                'oi.id',
                '=',
                'cri.order_item_id'
            )
            ->where('cr.order_id', $order->id)
            ->selectRaw('
                COALESCE(SUM(cri.quantity), 0) as quantity,
                COALESCE(SUM(cri.quantity * COALESCE(oi.price, 0)), 0) as amount
            ')
            ->first();

        return view('admin.orders.edit', compact(
            'order',
            'warehouses',
            'pointsOfSale',
            'paymentMethods',
            'customerReturns',
            'returnSummary'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ОБНОВЛЕНИЕ
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $order = Order::with(['items', 'payments'])->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | НОРМАЛИЗАЦИЯ СУММ ОПЛАТ
        |--------------------------------------------------------------------------
        */

        if ($request->has('payments')) {
            $payments = $request->input('payments', []);

            foreach ($payments as $index => $payment) {
                if (isset($payment['amount']) && is_string($payment['amount'])) {
                    $amount = trim($payment['amount']);
                    $amount = str_replace(' ', '', $amount);
                    $amount = str_replace(',', '.', $amount);

                    $payments[$index]['amount'] = $amount;
                }
            }

            $request->merge(['payments' => $payments]);
        }

        /*
        |--------------------------------------------------------------------------
        | ВАЛИДАЦИЯ
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'comment' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'point_of_sale_id' => 'nullable|integer|exists:points_of_sale,id',

            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:order_items,id',
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],

            'payments' => 'nullable|array',
            'payments.*.payment_method' => [
                'nullable',
                'string',
                'max:255',
            ],
            'payments.*.amount' => [
                'required',
                'numeric',
                'min:0',
            ],
        ], [
            'items.*.quantity.required' => 'Укажите количество товара.',
            'items.*.quantity.integer' => 'Количество должно быть целым числом.',
            'items.*.quantity.min' => 'Количество товара должно быть не меньше 1.',
            'items.*.price.min' => 'Цена не может быть отрицательной.',
            'payments.*.amount.required' => 'Введите сумму оплаты.',
            'payments.*.amount.min' => 'Сумма оплаты не может быть отрицательной.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ПРОВЕРКА ПОЗИЦИЙ ЗАКАЗА
        |--------------------------------------------------------------------------
        */

        foreach ($request->items as $itemData) {
            $orderItem = $order->items()->find($itemData['id']);

            if (!$orderItem) {
                continue;
            }

            if ((int) $orderItem->quantity !== (int) $itemData['quantity']) {
                $movementExists = StockMovement::where('type', 'sale')
                    ->where('source_id', $orderItem->id)
                    ->where('variant_id', $orderItem->variant_id)
                    ->exists();

                if ($movementExists) {
                    return back()->withInput()->with(
                        'error',
                        'Количество товара в уже проведённой продаже нельзя изменить, потому что продажа уже списана по FIFO.'
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ОБНОВЛЕНИЕ ЗАКАЗА
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($request, $order) {
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

            if ($request->has('payments')) {
                $order->payments()->delete();

                foreach ($request->input('payments', []) as $payment) {
                    $order->payments()->create([
                        'payment_method' => !empty($payment['payment_method'])
                            ? trim($payment['payment_method'])
                            : '',
                        'amount' => $payment['amount'],
                    ]);
                }
            }
        });

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

    /*
    |--------------------------------------------------------------------------
    | СПОСОБЫ ОПЛАТЫ
    |--------------------------------------------------------------------------
    */

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
