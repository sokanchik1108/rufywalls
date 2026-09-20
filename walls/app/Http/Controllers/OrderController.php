<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use App\Models\Batch;
use App\Models\PaymentMethod;
use App\Models\PointOfSale;
use App\Models\Warehouse;
use App\Models\StockMovement;
use App\Services\FifoService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $cart = json_decode(
            Cookie::get('cart', '{}'),
            true
        );

        if (empty($cart)) {
            return redirect()
                ->route('cart')
                ->with(
                    'error',
                    'Ваша корзина пуста.'
                );
        }

        $variantIds = array_keys($cart);

        $variants = Variant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $cartItems = collect($cart)->map(
            function ($item, $id) use ($variants) {

                $variant = $variants[$id];

                return [
                    'variant' => $variant,
                    'product' => $variant->product,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' =>
                    $item['price'] *
                        $item['quantity'],
                    'image' =>
                    $item['image']
                        ?? (
                            json_decode(
                                $variant->images
                            )[0] ?? null
                        ),
                ];
            }
        );

        $total = $cartItems->sum('total');

        return view(
            'checkout',
            compact(
                'cartItems',
                'total'
            )
        );
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
                'regex:/^[\p{L}\s\-]+$/u'
            ],

            'phone' => [
                'required',
                'string',
                'min:9',
                'max:30',
                'regex:/^\+?[0-9\s\-\(\)]+$/'
            ],

            'comment' => [
                'nullable',
                'string',
                'max:500'
            ],
        ], [
            'name.regex' =>
            'Имя должно содержать только буквы, пробелы и дефисы.',

            'phone.regex' =>
            'Телефон может содержать только цифры, пробелы, скобки, тире и может начинаться с +.',

            'phone.min' =>
            'Телефон слишком короткий. Укажите не менее 10 символов.',
        ]);

        $cart = json_decode(
            Cookie::get('cart', '{}'),
            true
        );

        if (empty($cart)) {
            return redirect()
                ->route('cart')
                ->with(
                    'error',
                    'Корзина пуста.'
                );
        }

        $order = Order::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'comment' => $request->comment,
            'status' => 'Новый',
            'is_website' => true,
        ]);

        foreach ($cart as $variantId => $item) {

            $variant = Variant::with('product')
                ->find($variantId);

            if (!$variant) {
                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $variantId,
                'quantity' =>
                $item['quantity'] ?? 1,
                'price' =>
                $item['price'] ?? 0,
                'image' =>
                $item['image']
                    ?? (
                        json_decode(
                            $variant->images
                        )[0] ?? null
                    ),
            ]);
        }

        Cookie::queue(
            Cookie::forget('cart')
        );

        $whatsappLink =
            'https://wa.me/77773555704?text=Я%20подтверждаю%20заказ%20на%20сайте';

        $message =
            'Спасибо за заказ!<br>' .
            'Чтобы мы начали обработку, пожалуйста, ' .
            'подтвердите его в WhatsApp.<br>' .
            'Это займёт всего пару секунд.<br><br>';

        $message .=
            '<a href="' .
            $whatsappLink .
            '" class="btn btn-success btn-sm mt-2" ' .
            'target="_blank">' .
            '🔗 Подтвердить заказ в WhatsApp' .
            '</a>';

        return redirect()
            ->route('cart')
            ->with(
                'success_html',
                $message
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ЗАКАЗЫ С САЙТА
    |--------------------------------------------------------------------------
    */

    public function indexWebsite()
    {
        $orders = Order::with('payments')
            ->where(
                'is_website',
                true
            )
            ->latest()
            ->get();

        return view(
            'admin.orders.orders_website',
            compact('orders')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | СОЗДАНИЕ ПРОДАЖИ ПРОДАВЦОМ
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $warehouses = Warehouse::all();

        $pointsOfSale =
            PointOfSale::orderBy('name')
            ->get();

        $paymentMethods =
            PaymentMethod::orderBy('name')
            ->get();

        $defaultPointOfSaleId =
            auth()->user()->point_of_sale_id ?? null;

        return view(
            'admin.orders.create',
            compact(
                'warehouses',
                'pointsOfSale',
                'paymentMethods',
                'defaultPointOfSaleId'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | СОХРАНЕНИЕ ПРОДАЖИ
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        FifoService $fifoService
    ) {

        /*
    |--------------------------------------------------------------------------
    | ТОЧКА ПРОДАЖ ПО УМОЛЧАНИЮ ПОЛЬЗОВАТЕЛЯ
    |--------------------------------------------------------------------------
    |
    | Если пользователь не выбрал точку вручную,
    | автоматически используем назначенную ему точку продаж.
    |
    */

        if (
            !$request->filled('point_of_sale_id') &&
            auth()->check() &&
            auth()->user()->point_of_sale_id
        ) {
            $request->merge([
                'point_of_sale_id' =>
                auth()->user()->point_of_sale_id,
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | НОРМАЛИЗАЦИЯ ОПЛАТ
    |--------------------------------------------------------------------------
    |
    | Например:
    | -20.000 -> -20000
    | 20.000  -> 20000
    |
    */

        if ($request->has('payments')) {

            $payments = $request->input(
                'payments',
                []
            );

            foreach ($payments as $index => $payment) {

                if (
                    isset($payment['amount']) &&
                    is_string($payment['amount'])
                ) {

                    $amount = trim(
                        $payment['amount']
                    );

                    $negative =
                        str_starts_with(
                            $amount,
                            '-'
                        );

                    $amount =
                        str_replace(
                            ' ',
                            '',
                            $amount
                        );

                    $amount =
                        str_replace(
                            '.',
                            '',
                            $amount
                        );

                    $amount =
                        str_replace(
                            ',',
                            '.',
                            $amount
                        );

                    $amount =
                        preg_replace(
                            '/[^\d.]/',
                            '',
                            $amount
                        );

                    if ($negative) {

                        $amount =
                            '-' . $amount;
                    }

                    $payments[$index]['amount'] =
                        $amount;
                }
            }

            $request->merge([
                'payments' => $payments
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | ВАЛИДАЦИЯ
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'name' =>
            'required|string|max:255',

            'phone' =>
            'required|string|max:20',

            'comment' =>
            'nullable|string',

            'discount' =>
            'nullable|numeric|min:0',

            'order_date' =>
            'required|date',

            'point_of_sale_id' =>
            'required|exists:points_of_sale,id',

            'items' =>
            'required|array|min:1',

            'items.*.sku' =>
            'required|exists:variants,sku',

            'items.*.batch_id' =>
            'required|integer|exists:batches,id',

            'items.*.warehouse_id' =>
            'required|integer|exists:warehouses,id',

            /*
        |--------------------------------------------------------------------------
        | ВАЖНО:
        | теперь разрешены отрицательные количества.
        |--------------------------------------------------------------------------
        */

            'items.*.quantity' =>
            'required|integer|not_in:0',

            'items.*.price' =>
            'required|numeric|min:0',

            'items.*.batch_code' =>
            'required|string',

            'items.*.warehouse_name' =>
            'nullable|string',

            'payments' =>
            'nullable|array',

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

            'payments.*.payment_method.required' =>
            'Выберите способ оплаты.',

            'payments.*.amount.required' =>
            'Введите сумму оплаты.',

            'items.*.quantity.not_in' =>
            'Количество товара не может быть равно нулю.',
        ]);

        /*
    |--------------------------------------------------------------------------
    | СУММА ЗАКАЗА
    |--------------------------------------------------------------------------
    */

        $itemsTotal = 0;

        foreach ($request->items as $item) {

            $quantity =
                (int) $item['quantity'];

            $price =
                (float) $item['price'];

            /*
        |--------------------------------------------------------------------------
        | Отрицательное количество автоматически
        | делает сумму отрицательной.
        |--------------------------------------------------------------------------
        */

            $itemsTotal +=
                $quantity * $price;
        }

        $discount =
            (float) (
                $request->discount ?? 0
            );

        $orderTotal =
            $itemsTotal - $discount;

        /*
    |--------------------------------------------------------------------------
    | СУММА ОПЛАТ
    |--------------------------------------------------------------------------
    */

        $paymentsTotal =
            collect(
                $request->input(
                    'payments',
                    []
                )
            )->sum(function ($payment) {

                return (float) (
                    $payment['amount'] ?? 0
                );
            });

        /*
    |--------------------------------------------------------------------------
    | ПРОВЕРКА ОПЛАТ
    |--------------------------------------------------------------------------
    |
    | Для продажи:
    | 100000 = 100000
    |
    | Для возврата:
    | -20000 = -20000
    |
    */

        if (
            abs(
                $paymentsTotal -
                    $orderTotal
            ) > 0.01
        ) {

            if (
                empty($request->payments)
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Добавьте способ оплаты.'
                    );
            }

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Сумма оплат (' .
                        number_format(
                            $paymentsTotal,
                            2,
                            '.',
                            ' '
                        ) .
                        ' ₸) не соответствует сумме заказа (' .
                        number_format(
                            $orderTotal,
                            2,
                            '.',
                            ' '
                        ) .
                        ' ₸).'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | СОЗДАНИЕ ЗАКАЗА
    |--------------------------------------------------------------------------
    */

        try {

            DB::transaction(
                function () use (
                    $request,
                    $fifoService
                ) {

                    $order = Order::create([
                        'name' =>
                        $request->name,

                        'phone' =>
                        $request->phone,

                        'comment' =>
                        $request->comment,

                        'discount' =>
                        $request->discount ?? 0,

                        'status' =>
                        'Новый',

                        'is_website' =>
                        false,

                        /*
                    |--------------------------------------------------------------------------
                    | Здесь уже будет:
                    | - выбранная вручную точка
                    | ИЛИ
                    | - точка, назначенная пользователю
                    |--------------------------------------------------------------------------
                    */

                        'point_of_sale_id' =>
                        $request->point_of_sale_id,

                        'order_date' =>
                        $request->order_date
                            ? Carbon::parse(
                                $request->order_date,
                                'Asia/Almaty'
                            )
                            : now('Asia/Almaty'),
                    ]);

                    /*
                |--------------------------------------------------------------------------
                | ПОЗИЦИИ
                |--------------------------------------------------------------------------
                */

                    foreach (
                        $request->items
                        as $item
                    ) {

                        $variant =
                            Variant::where(
                                'sku',
                                $item['sku']
                            )->firstOrFail();

                        $batch =
                            Batch::findOrFail(
                                $item['batch_id']
                            );

                        /*
                    |--------------------------------------------------------------------------
                    | Проверяем, что партия принадлежит
                    | выбранному варианту.
                    |--------------------------------------------------------------------------
                    */

                        if (
                            $batch->variant_id !==
                            $variant->id
                        ) {

                            throw new RuntimeException(
                                "Партия {$batch->batch_code} " .
                                    "не принадлежит артикулу {$variant->sku}."
                            );
                        }

                        $quantity =
                            (int) $item['quantity'];

                        /*
                    |--------------------------------------------------------------------------
                    | ОБЫЧНАЯ ПРОДАЖА
                    |--------------------------------------------------------------------------
                    */

                        if ($quantity > 0) {

                            /*
                        |--------------------------------------------------------------------------
                        | Сначала проверяем FIFO.
                        |--------------------------------------------------------------------------
                        */

                            $fifoService->calculateFifo(
                                (int) $item['warehouse_id'],
                                (int) $variant->id,
                                $quantity
                            );

                            /*
                        |--------------------------------------------------------------------------
                        | Реально списываем товар.
                        |--------------------------------------------------------------------------
                        */

                            $fifoService->consume(
                                (int) $item['warehouse_id'],
                                (int) $variant->id,
                                $quantity,
                                'sale',
                                $order->id,
                                Carbon::parse(
                                    $request->order_date,
                                    'Asia/Almaty'
                                )
                            );
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | ВОЗВРАТ
                    |--------------------------------------------------------------------------
                    */

                        if ($quantity < 0) {

                            /*
                        |--------------------------------------------------------------------------
                        | Превращаем -1 в 1,
                        | потому что returnToWarehouse()
                        | принимает положительное количество.
                        |--------------------------------------------------------------------------
                        */

                            $returnQuantity =
                                abs($quantity);

                            /*
                        |--------------------------------------------------------------------------
                        | Возвращаем товар именно
                        | в выбранную партию.
                        |--------------------------------------------------------------------------
                        */

                            $fifoService->returnToWarehouse(
                                (int) $item['warehouse_id'],
                                (int) $variant->id,
                                $returnQuantity,
                                (int) $item['batch_id'],
                                $order->id,
                                Carbon::parse(
                                    $request->order_date,
                                    'Asia/Almaty'
                                )
                            );
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | ИЗОБРАЖЕНИЕ
                    |--------------------------------------------------------------------------
                    */

                        $image = '';

                        if (
                            !empty($variant->images)
                        ) {

                            $images =
                                json_decode(
                                    $variant->images,
                                    true
                                );

                            if (
                                is_array($images) &&
                                !empty($images[0])
                            ) {

                                $image =
                                    $images[0];
                            }
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | СОХРАНЯЕМ ПОЗИЦИЮ
                    |--------------------------------------------------------------------------
                    */

                        $order->items()->create([
                            'variant_id' =>
                            $variant->id,

                            'quantity' =>
                            $quantity,

                            'price' =>
                            (float) $item['price'],

                            'image' =>
                            $image,

                            'batch_code' =>
                            $item['batch_code'],

                            'warehouse_name' =>
                            $item['warehouse_name']
                                ?? '',
                        ]);
                    }

                    /*
                |--------------------------------------------------------------------------
                | ОПЛАТЫ
                |--------------------------------------------------------------------------
                */

                    foreach (
                        $request->input(
                            'payments',
                            []
                        ) as $payment
                    ) {

                        $order->payments()->create([
                            'payment_method' =>
                            trim(
                                $payment['payment_method']
                            ),

                            'amount' =>
                            $payment['amount'],
                        ]);
                    }
                }
            );
        } catch (RuntimeException $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }

        /*
    |--------------------------------------------------------------------------
    | ПОСЛЕ СОЗДАНИЯ
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route(
                'admin.orders.seller',
                [
                    'date' =>
                    $request->order_date
                ]
            )
            ->with(
                'success',
                'Заказ успешно создан'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ЗАКАЗЫ ПРОДАВЦОВ
    |--------------------------------------------------------------------------
    */

    public function indexSeller(
        Request $request
    ) {

        $date =
            $request->get('date');

        if (!$date) {

            $date =
                now('Asia/Almaty');
        } else {

            $date =
                Carbon::parse(
                    $date,
                    'Asia/Almaty'
                );
        }

        $start =
            $date->copy()
            ->startOfDay();

        $end =
            $date->copy()
            ->endOfDay();

        $orders =
            Order::with([
                'items.variant.product',
                'pointOfSale',
                'payments'
            ])
            ->whereBetween(
                DB::raw(
                    'COALESCE(order_date, created_at)'
                ),
                [
                    $start,
                    $end
                ]
            )
            ->latest()
            ->get();

        /*
         * Итоги по способам оплаты.
         */

        $paymentTotals = [];

        foreach ($orders as $order) {

            foreach (
                $order->payments
                as $payment
            ) {

                $method =
                    trim(
                        $payment->payment_method
                    );

                if ($method === '') {

                    $method =
                        'Без способа оплаты';
                }

                if (
                    !isset(
                        $paymentTotals[$method]
                    )
                ) {

                    $paymentTotals[$method] =
                        0;
                }

                $paymentTotals[$method] +=
                    (float) $payment->amount;
            }
        }

        return view(
            'admin.orders.orders_seller',
            compact(
                'orders',
                'paymentTotals'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ПОИСК
    |--------------------------------------------------------------------------
    */

    public function search(
        Request $request
    ) {

        $q =
            trim(
                $request->q
            );

        $orders =
            Order::with([
                'items',
                'items.variant',
                'pointOfSale',
                'payments'
            ])
            ->where(function ($query) use ($q) {

                $query
                    ->where(
                        'id',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhere(
                        'name',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhereHas(
                        'items.variant',
                        function (
                            $variantQuery
                        ) use ($q) {

                            $variantQuery->where(
                                'sku',
                                'like',
                                "%{$q}%"
                            );
                        }
                    );
            })
            ->latest()
            ->get();

        return response()->json(
            $orders
        );
    }

    /*
    |--------------------------------------------------------------------------
    | РЕДАКТИРОВАНИЕ
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $order =
            Order::with([
                'items.variant.product',
                'items',
                'payments'
            ])
            ->findOrFail($id);

        $warehouses =
            Warehouse::all();

        $pointsOfSale =
            PointOfSale::orderBy('name')
            ->get();

        $paymentMethods =
            PaymentMethod::orderBy('name')
            ->get();

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

    /*
    |--------------------------------------------------------------------------
    | ОБНОВЛЕНИЕ
    |--------------------------------------------------------------------------
    |
    | Сейчас:
    |
    | - имя можно менять
    | - телефон можно менять
    | - комментарий можно менять
    | - скидку можно менять
    | - точку продаж можно менять
    | - цену товара можно менять
    | - оплаты можно менять
    |
    | Количество FIFO-продажи менять нельзя.
    |
    | Это временная защита от рассинхронизации склада.
    | Позже добавим полноценный пересчёт движений.
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $order =
            Order::with([
                'items',
                'payments'
            ])
            ->findOrFail($id);

        /*
         * Такое же исправление для редактирования заказа,
         * чтобы отрицательная сумма вида -20.000
         * нормально проходила валидацию.
         */
        if ($request->has('payments')) {

            $payments = $request->input(
                'payments',
                []
            );

            foreach ($payments as $index => $payment) {

                if (
                    isset($payment['amount']) &&
                    is_string($payment['amount'])
                ) {

                    $amount = trim(
                        $payment['amount']
                    );

                    $negative =
                        str_starts_with(
                            $amount,
                            '-'
                        );

                    $amount =
                        str_replace(
                            ' ',
                            '',
                            $amount
                        );

                    $amount =
                        str_replace(
                            '.',
                            '',
                            $amount
                        );

                    $amount =
                        str_replace(
                            ',',
                            '.',
                            $amount
                        );

                    if ($negative) {
                        $amount =
                            '-' . ltrim(
                                $amount,
                                '-'
                            );
                    }

                    $payments[$index]['amount'] =
                        $amount;
                }
            }

            $request->merge([
                'payments' => $payments
            ]);
        }

        $request->validate([
            'name' =>
            'required|string|max:255',

            'phone' =>
            'required|string|max:20',

            'comment' =>
            'nullable|string',

            'discount' =>
            'nullable|numeric|min:0',

            'point_of_sale_id' =>
            'nullable|integer|exists:points_of_sale,id',

            'items' =>
            'required|array|min:1',

            'items.*.id' =>
            'required|integer|exists:order_items,id',

            'items.*.quantity' =>
            'required|integer|min:1',

            'items.*.price' =>
            'required|numeric|min:0',

            'payments' =>
            'nullable|array',

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
            'payments.*.amount.required' =>
            'Введите сумму оплаты.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ПРОВЕРЯЕМ FIFO-МОТИВАЦИЮ
        |--------------------------------------------------------------------------
        */

        foreach (
            $request->items
            as $itemData
        ) {

            $orderItem =
                $order->items()
                ->find(
                    $itemData['id']
                );

            if (!$orderItem) {
                continue;
            }

            /*
             * Если количество изменилось,
             * проверяем, есть ли FIFO-движение.
             */

            if (
                (int) $orderItem->quantity !==
                (int) $itemData['quantity']
            ) {

                $movementExists =
                    StockMovement::where(
                        'type',
                        'sale'
                    )
                    ->where(
                        'source_id',
                        $order->id
                    )
                    ->where(
                        'variant_id',
                        $orderItem->variant_id
                    )
                    ->exists();

                if ($movementExists) {

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Количество товара в уже проведённой продаже нельзя изменить, потому что продажа уже списана по FIFO.'
                        );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ОБНОВЛЯЕМ ЗАКАЗ
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $request,
                $order
            ) {

                $order->update([
                    'name' =>
                    $request->name,

                    'phone' =>
                    $request->phone,

                    'comment' =>
                    $request->comment,

                    'discount' =>
                    $request->discount ?? 0,

                    'point_of_sale_id' =>
                    $request->point_of_sale_id
                        ?: null,
                ]);

                /*
                 * Обновляем позиции.
                 *
                 * Количество уже проверено выше.
                 */

                foreach (
                    $request->items
                    as $itemData
                ) {

                    $orderItem =
                        $order->items()
                        ->find(
                            $itemData['id']
                        );

                    if ($orderItem) {

                        $orderItem->update([
                            'quantity' =>
                            $itemData['quantity'],

                            'price' =>
                            $itemData['price'],
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | ОПЛАТЫ
                |--------------------------------------------------------------------------
                */

                if (
                    $request->has(
                        'payments'
                    )
                ) {

                    $order->payments()
                        ->delete();

                    foreach (
                        $request->input(
                            'payments',
                            []
                        ) as $payment
                    ) {

                        $order->payments()
                            ->create([
                                'payment_method' =>
                                !empty($payment['payment_method'])
                                    ? trim(
                                        $payment['payment_method']
                                    )
                                    : '',

                                'amount' =>
                                $payment['amount'],
                            ]);
                    }
                }
            }
        );

        return redirect()
            ->route(
                'admin.orders.seller',
                [
                    'date' =>
                    $order->order_date
                        ? Carbon::parse(
                            $order->order_date,
                            'Asia/Almaty'
                        )->format('Y-m-d')
                        : Carbon::parse(
                            $order->created_at,
                            'Asia/Almaty'
                        )->format('Y-m-d'),
                ]
            )
            ->with(
                'success',
                'Заказ успешно обновлён'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | СПОСОБЫ ОПЛАТЫ
    |--------------------------------------------------------------------------
    */

    public function paymentMethods()
    {
        $paymentMethods =
            PaymentMethod::orderBy('name')
            ->get();

        return view(
            'admin.orders.payment-methods',
            compact(
                'paymentMethods'
            )
        );
    }

    public function storePaymentMethod(
        Request $request
    ) {

        $request->validate([
            'name' =>
            'required|string|max:255|unique:payment_methods,name',
        ], [
            'name.required' =>
            'Введите название способа оплаты.',

            'name.unique' =>
            'Такой способ оплаты уже существует.',

            'name.max' =>
            'Название слишком длинное.',
        ]);

        PaymentMethod::create([
            'name' =>
            trim(
                $request->name
            ),
        ]);

        return redirect()
            ->route(
                'admin.payment-methods'
            )
            ->with(
                'success',
                'Способ оплаты добавлен.'
            );
    }

    public function destroyPaymentMethod(
        $id
    ) {

        $paymentMethod =
            PaymentMethod::findOrFail(
                $id
            );

        if (
            $paymentMethod
            ->payments()
            ->exists()
        ) {

            return redirect()
                ->route(
                    'admin.payment-methods'
                )
                ->with(
                    'error',
                    'Нельзя удалить способ оплаты, который уже используется в заказах.'
                );
        }

        $paymentMethod->delete();

        return redirect()
            ->route(
                'admin.payment-methods'
            )
            ->with(
                'success',
                'Способ оплаты удалён.'
            );
    }
}
