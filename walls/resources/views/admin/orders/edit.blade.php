@extends('layouts.app')

@section('title', 'Редактировать заказ')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>

    body {
        background: #f4f6fb;
        font-family: 'Segoe UI', sans-serif;
    }

    .container {
        padding: 20px;
    }

    #orderControls {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        margin-bottom: 20px;
    }

    #cancelOrder {
        color: #ef4444;
        cursor: pointer;
    }

    #finishOrder {
        background: #3b82f6;
        color: #fff;
        border: none;
        font-weight: 600;
        padding: 8px 16px;
    }

    .order-item {
        background: #fff;
        padding: 12px;
        margin-bottom: 12px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .05);
    }

    .price-input {
        border: none;
        border-bottom: 1px solid #ef4444;
        width: 100px;
    }

    .item-total {
        font-weight: 600;
        margin-left: 10px;
    }

    .remove-item {
        float: right;
        border: none;
        background: none;
        color: #ef4444;
        font-size: 20px;
    }

    /* ========================= */
    /* ОПЛАТА */
    /* ========================= */

    .payments-wrapper {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .payment-item {
        background: #fff;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .05);
    }

    .payment-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .payment-method {
        flex: 1;
    }

    .payment-amount {
        width: 130px;
    }

    .remove-payment {
        border: none;
        background: none;
        color: #ef4444;
        font-size: 22px;
        cursor: pointer;
    }

    .add-payment {
        border: none;
        background: #111827;
        color: #fff;
        padding: 8px 14px;
        border-radius: 8px;
        font-weight: 600;
        margin-top: 5px;
    }

    .payment-summary {
        margin-top: 10px;
        padding: 10px;
        background: #f8fafc;
        border-radius: 8px;
        font-size: 14px;
    }

    .payment-difference {
        font-weight: 700;
    }

    .payment-error {
        display: none;
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        background: #fef2f2;
        color: #dc2626;
        font-weight: 600;
    }

    .payment-success {
        display: none;
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        background: #f0fdf4;
        color: #16a34a;
        font-weight: 600;
    }

    @media (max-width: 600px) {

        .payment-row {
            align-items: stretch;
        }

        .payment-method {
            width: 100%;
        }

        .payment-amount {
            width: 120px;
        }

    }

</style>

<div class="container">

<form
    method="POST"
    action="{{ route('admin.orders.update', $order->id) }}"
    id="orderForm"
>

    @csrf
    @method('PUT')


    {{-- ========================= --}}
    {{-- КНОПКИ --}}
    {{-- ========================= --}}

    <div id="orderControls">

        <span id="cancelOrder">
            ОТМЕНА
        </span>

        <button
            type="submit"
            id="finishOrder"
        >
            СОХРАНИТЬ
        </button>

    </div>


    {{-- ========================= --}}
    {{-- ДАННЫЕ КЛИЕНТА --}}
    {{-- ========================= --}}

    <input
        type="text"
        name="name"
        class="form-control mb-2"
        value="{{ $order->name }}"
        placeholder="Имя"
    >

    <input
        type="text"
        name="phone"
        class="form-control mb-2"
        value="{{ $order->phone }}"
        placeholder="Телефон"
    >

    <input
        type="text"
        name="comment"
        class="form-control mb-2"
        value="{{ $order->comment }}"
        placeholder="Комментарий"
    >


    {{-- ========================= --}}
    {{-- ТОЧКА ПРОДАЖ --}}
    {{-- ========================= --}}

    <label class="mb-1">
        Точка продаж
    </label>

    <select
        name="point_of_sale_id"
        class="form-control mb-3"
    >

        <option value="">
            Не выбрана
        </option>

        @foreach($pointsOfSale as $point)

            <option
                value="{{ $point->id }}"
                {{ $order->point_of_sale_id == $point->id ? 'selected' : '' }}
            >
                {{ $point->name }}
            </option>

        @endforeach

    </select>


    {{-- ========================= --}}
    {{-- СКИДКА --}}
    {{-- ========================= --}}

    <label>
        Скидка
    </label>

    <input
        type="text"
        id="discountInput"
        name="discount"
        class="form-control mb-3"
        value="{{ $order->discount ?? 0 }}"
        inputmode="numeric"
        autocomplete="off"
    >


    {{-- ========================= --}}
    {{-- ТОВАРЫ --}}
    {{-- ========================= --}}

    <div id="itemsWrapper">

        @foreach($order->items as $i => $item)

            <div class="order-item">

                <button
                    type="button"
                    class="remove-item"
                >
                    &times;
                </button>


                <div>

                    <b>
                        {{ $item->variant->sku }}
                    </b>

                    @if($item->batch_code)
                        ({{ $item->batch_code }})
                    @endif

                </div>


                <div style="margin-top:10px">

                    {{ $item->quantity }} ×

                    <input
                        type="text"
                        name="items[{{ $i }}][price]"
                        class="price-input"
                        value="{{ number_format((float) $item->price, 0, '.', '') }}"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="цена"
                    >

                    <span class="item-total"></span>

                </div>


                <input
                    type="hidden"
                    name="items[{{ $i }}][id]"
                    value="{{ $item->id }}"
                >


                <input
                    type="hidden"
                    name="items[{{ $i }}][quantity]"
                    value="{{ $item->quantity }}"
                >

            </div>

        @endforeach

    </div>


    {{-- ========================= --}}
    {{-- ИТОГО ЗАКАЗА --}}
    {{-- ========================= --}}

    <div
        style="
            text-align:right;
            font-weight:700;
            font-size:18px;
            margin-top:15px;
        "
    >

        ИТОГО:

        <span id="orderTotal">
            0
        </span>

        ₸

    </div>


    {{-- ========================= --}}
    {{-- ОПЛАТЫ --}}
    {{-- ========================= --}}

    <div class="payments-wrapper">

        <label
            class="mb-2"
            style="font-weight:600;"
        >
            Оплата
        </label>


        <div id="paymentsWrapper">

            @if($order->payments->count())


                @foreach($order->payments as $payment)

                    <div class="payment-item">

                        <div class="payment-row">


                            {{-- СПОСОБ ОПЛАТЫ --}}

                            <select
                                name="payments[{{ $loop->index }}][payment_method]"
                                class="form-control payment-method"
                            >

                                {{-- Если у существующей оплаты способ отсутствует --}}

                                @if(empty($payment->payment_method))

                                    <option
                                        value=""
                                        selected
                                    >
                                        Не указано
                                    </option>

                                @else

                                    {{-- Пустой вариант --}}

                                    <option value="">
                                        Не указано
                                    </option>

                                @endif


                                {{-- Все способы оплаты из базы --}}

                                @foreach($paymentMethods as $method)

                                    <option
                                        value="{{ $method->name }}"
                                        {{ $payment->payment_method === $method->name ? 'selected' : '' }}
                                    >
                                        {{ $method->name }}
                                    </option>

                                @endforeach

                            </select>


                            {{-- СУММА --}}

                            <input
                                type="text"
                                name="payments[{{ $loop->index }}][amount]"
                                class="form-control payment-amount"
                                value="{{ number_format((float) $payment->amount, 0, '.', '') }}"
                                inputmode="numeric"
                                autocomplete="off"
                                placeholder="Сумма"
                            >


                            {{-- УДАЛИТЬ --}}

                            <button
                                type="button"
                                class="remove-payment"
                            >
                                &times;
                            </button>

                        </div>

                    </div>

                @endforeach


            @else


                {{-- ЕСЛИ ОПЛАТ ЕЩЁ НЕТ --}}

                <div class="payment-item">

                    <div class="payment-row">


                        <select
                            name="payments[0][payment_method]"
                            class="form-control payment-method"
                        >

                            <option
                                value=""
                                selected
                            >
                                Не указано
                            </option>


                            @foreach($paymentMethods as $method)

                                <option value="{{ $method->name }}">
                                    {{ $method->name }}
                                </option>

                            @endforeach

                        </select>


                        <input
                            type="text"
                            name="payments[0][amount]"
                            class="form-control payment-amount"
                            value="0"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="Сумма"
                        >


                        <button
                            type="button"
                            class="remove-payment"
                        >
                            &times;
                        </button>

                    </div>

                </div>

            @endif

        </div>


        {{-- ДОБАВИТЬ ОПЛАТУ --}}

        <button
            type="button"
            class="add-payment"
            id="addPayment"
        >
            + Добавить оплату
        </button>


        {{-- ========================= --}}
        {{-- ИНФОРМАЦИЯ ПО ОПЛАТЕ --}}
        {{-- ========================= --}}

        <div class="payment-summary">

            <div>

                Сумма заказа:

                <strong>
                    <span id="paymentOrderTotal">0</span> ₸
                </strong>

            </div>


            <div>

                Сумма оплаты:

                <strong>
                    <span id="paymentTotal">0</span> ₸
                </strong>

            </div>


            <div>

                Разница:

                <strong class="payment-difference">
                    <span id="paymentDifference">0</span> ₸
                </strong>

            </div>


            <div
                id="paymentError"
                class="payment-error"
            >
                {{-- Текст устанавливается через JS --}}
            </div>


            <div
                id="paymentSuccess"
                class="payment-success"
            >
                Сумма заказа и сумма оплаты совпадают.
            </div>

        </div>

    </div>


</form>


</div>

<script>


    /* ========================= */
    /* СПОСОБЫ ОПЛАТЫ */
    /* ========================= */

    const paymentMethods = @json(
        $paymentMethods->pluck('name')->values()
    );


    /* ========================= */
    /* ФОРМАТИРОВАНИЕ */
    /* ========================= */

function formatAmount(amount) {

    amount = Math.round(Number(amount) || 0);

    let isNegative = amount < 0;

    amount = Math.abs(amount);

    let formatted = amount
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return isNegative
        ? '-' + formatted
        : formatted;
}


    function parseAmount(value) {

        return parseFloat(
            String(value || '')
                .replace(/\./g, '')
                .replace(',', '.')
        ) || 0;

    }


function formatInputAmount(input) {

    let value = String(input.val() || '').trim();

    let isNegative = value.startsWith('-');

    let raw = value.replace(/\D/g, '');

    if (raw === '') {

        input.val(isNegative ? '-' : '');

        return;
    }

    let number = parseInt(raw, 10);

    if (isNegative) {
        number = -number;
    }

    input.val(
        formatAmount(number)
    );
}


    /* ========================= */
    /* ПЕРЕСЧЁТ ЗАКАЗА */
    /* ========================= */

    function recalc() {

        let sum = 0;


        $('.order-item').each(function() {

            let qty =
                parseInt(
                    $(this)
                        .find(
                            'input[name$="[quantity]"]'
                        )
                        .val()
                ) || 0;


            let price =
                parseAmount(
                    $(this)
                        .find('.price-input')
                        .val()
                );


            let total =
                qty * price;


            $(this)
                .find('.item-total')
                .text(
                    formatAmount(total) + ' ₸'
                );


            sum += total;

        });


        let discount =
            parseAmount(
                $('#discountInput').val()
            );


        let orderTotal =
        sum - discount;

        orderTotal =
        Math.round(orderTotal);


        $('#orderTotal').text(
            formatAmount(orderTotal)
        );


        $('#paymentOrderTotal').text(
            formatAmount(orderTotal)
        );


        recalcPayments(orderTotal);

    }


    /* ========================= */
    /* ПЕРЕСЧЁТ ОПЛАТ */
    /* ========================= */

    function recalcPayments(orderTotal) {

        let paymentTotal = 0;


        $('.payment-amount').each(function() {

            paymentTotal +=
                parseAmount(
                    $(this).val()
                );

        });


        paymentTotal =
            Math.round(paymentTotal);


        $('#paymentTotal').text(
            formatAmount(paymentTotal)
        );


        let difference =
            Math.round(
                orderTotal - paymentTotal
            );


        $('#paymentDifference').text(
            formatAmount(difference)
        );


        /* ========================= */
        /* ПРОВЕРКА СУММ */
        /* ========================= */

        if (difference === 0) {

            $('#paymentSuccess').show();

            $('#paymentError').hide();

        } else {

            $('#paymentSuccess').hide();

            $('#paymentError').show();


            if (difference > 0) {

                $('#paymentError').text(
                    'Не хватает ' +
                    formatAmount(difference) +
                    ' ₸ для полной оплаты заказа.'
                );

            } else {

                $('#paymentError').text(
                    'Сумма оплаты больше суммы заказа на ' +
                    formatAmount(
                        Math.abs(difference)
                    ) +
                    ' ₸.'
                );

            }

        }

    }


    /* ========================= */
    /* ИЗМЕНЕНИЕ ЦЕНЫ */
    /* ========================= */

    $(document).on(
        'input',
        '.price-input',
        function() {

            formatInputAmount(
                $(this)
            );

            recalc();

        }
    );


    /* ========================= */
    /* ИЗМЕНЕНИЕ СКИДКИ */
    /* ========================= */

    $(document).on(
        'input',
        '#discountInput',
        function() {

            formatInputAmount(
                $(this)
            );

            recalc();

        }
    );


    /* ========================= */
    /* ИЗМЕНЕНИЕ СУММЫ ОПЛАТЫ */
    /* ========================= */

    $(document).on(
        'input',
        '.payment-amount',
        function() {

            formatInputAmount(
                $(this)
            );


            let orderTotal =
                parseAmount(
                    $('#orderTotal').text()
                );


            recalcPayments(
                orderTotal
            );

        }
    );


    /* ========================= */
    /* ДОБАВЛЕНИЕ ОПЛАТЫ */
    /* ========================= */

    let paymentIndex =
        {{ $order->payments->count() ?: 1 }};


    $('#addPayment').click(function() {

        let orderTotal =
            parseAmount(
                $('#orderTotal').text()
            );


        let paymentTotal =
            getPaymentsTotal();


        let remaining =
            orderTotal - paymentTotal;


        if (remaining < 0) {
            remaining = 0;
        }


        let methodsHtml = '';


        /*
         * Если способов оплаты нет,
         * оставляем вариант "Не указано".
         */

        if (paymentMethods.length === 0) {

            methodsHtml = `
                <option value="" selected>
                    Не указано
                </option>
            `;

        } else {

            /*
             * При добавлении новой оплаты
             * первым показываем "Не указано".
             *
             * Пользователь должен выбрать способ.
             */

            methodsHtml = `
                <option value="" selected>
                    Не указано
                </option>
            `;


            paymentMethods.forEach(function(method) {

                methodsHtml += `
                    <option value="${method}">
                        ${method}
                    </option>
                `;

            });

        }


        let html = `

            <div class="payment-item">

                <div class="payment-row">

                    <select
                        name="payments[${paymentIndex}][payment_method]"
                        class="form-control payment-method"
                    >

                        ${methodsHtml}

                    </select>


                    <input
                        type="text"
                        name="payments[${paymentIndex}][amount]"
                        class="form-control payment-amount"
                        value="${formatAmount(remaining)}"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="Сумма"
                    >


                    <button
                        type="button"
                        class="remove-payment"
                    >
                        &times;
                    </button>

                </div>

            </div>

        `;


        $('#paymentsWrapper').append(
            html
        );


        paymentIndex++;


        recalc();

    });


    /* ========================= */
    /* ПОЛУЧИТЬ СУММУ ОПЛАТ */
    /* ========================= */

    function getPaymentsTotal() {

        let total = 0;


        $('.payment-amount').each(function() {

            total +=
                parseAmount(
                    $(this).val()
                );

        });


        return Math.round(total);

    }


    /* ========================= */
    /* УДАЛЕНИЕ ОПЛАТЫ */
    /* ========================= */

    $(document).on(
        'click',
        '.remove-payment',
        function() {

            $(this)
                .closest('.payment-item')
                .remove();


            recalc();

        }
    );


    /* ========================= */
    /* УДАЛЕНИЕ ТОВАРА */
    /* ========================= */

    $(document).on(
        'click',
        '.remove-item',
        function() {

            $(this)
                .closest('.order-item')
                .remove();


            recalc();

        }
    );


    /* ========================= */
    /* ПРОВЕРКА ПЕРЕД СОХРАНЕНИЕМ */
    /* ========================= */

    $('#orderForm').on(
        'submit',
        function(e) {


            /* ========================= */
            /* 1. ПРОВЕРЯЕМ СПОСОБ ОПЛАТЫ */
            /* ========================= */

            let paymentMethodMissing = false;

            let firstEmptyPayment = null;


            $('.payment-method').each(function() {

                if (!$(this).val()) {

                    paymentMethodMissing = true;


                    if (!firstEmptyPayment) {

                        firstEmptyPayment =
                            $(this);

                    }

                }

            });


            if (paymentMethodMissing) {

                e.preventDefault();


                $('#paymentError')
                    .text(
                        'Выберите способ оплаты перед сохранением заказа.'
                    )
                    .show();


                $('#paymentSuccess').hide();


                alert(
                    'Выберите способ оплаты перед сохранением заказа.'
                );


                firstEmptyPayment.focus();


                return false;

            }


            /* ========================= */
            /* 2. ПРОВЕРЯЕМ СУММЫ */
            /* ========================= */

            let orderTotal =
                parseAmount(
                    $('#orderTotal').text()
                );


            let paymentTotal =
                getPaymentsTotal();


            orderTotal =
                Math.round(orderTotal);


            paymentTotal =
                Math.round(paymentTotal);


            let difference =
                orderTotal - paymentTotal;


            if (difference !== 0) {

                e.preventDefault();


                $('#paymentSuccess').hide();

                $('#paymentError')
                    .show();


                if (difference > 0) {

                    $('#paymentError').text(
                        'Нельзя сохранить заказ. Не хватает ' +
                        formatAmount(difference) +
                        ' ₸ для полной оплаты.'
                    );


                    alert(
                        'Сумма оплаты меньше суммы заказа на ' +
                        formatAmount(difference) +
                        ' ₸.'
                    );

                } else {

                    $('#paymentError').text(
                        'Нельзя сохранить заказ. Сумма оплаты больше суммы заказа на ' +
                        formatAmount(
                            Math.abs(difference)
                        ) +
                        ' ₸.'
                    );


                    alert(
                        'Сумма оплаты больше суммы заказа на ' +
                        formatAmount(
                            Math.abs(difference)
                        ) +
                        ' ₸.'
                    );

                }


                return false;

            }


            /* ========================= */
            /* 3. НОРМАЛИЗАЦИЯ ЦЕН */
            /* ========================= */

            $('.price-input').each(function() {

                $(this).val(
                    parseAmount(
                        $(this).val()
                    )
                );

            });


            /* ========================= */
            /* 4. НОРМАЛИЗАЦИЯ СКИДКИ */
            /* ========================= */

            $('#discountInput').val(
                parseAmount(
                    $('#discountInput').val()
                )
            );


            /* ========================= */
            /* 5. НОРМАЛИЗАЦИЯ ОПЛАТ */
            /* ========================= */

            $('.payment-amount').each(function() {

                $(this).val(
                    parseAmount(
                        $(this).val()
                    )
                );

            });

        }
    );


    /* ========================= */
    /* НАЧАЛЬНОЕ ФОРМАТИРОВАНИЕ */
    /* ========================= */

    $('.price-input').each(function() {

        formatInputAmount(
            $(this)
        );

    });


    formatInputAmount(
        $('#discountInput')
    );


    $('.payment-amount').each(function() {

        formatInputAmount(
            $(this)
        );

    });


    /* ========================= */
    /* ПЕРВИЧНЫЙ РАСЧЁТ */
    /* ========================= */

    recalc();


    /* ========================= */
    /* ОТМЕНА */
    /* ========================= */

    $('#cancelOrder').click(
        () => history.back()
    );

</script>

@endsection
