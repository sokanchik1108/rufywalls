@extends('layouts.app')



@section('title', 'Создание возврата')



@section('content')

@php

$orderDate = $order->order_date

? \Carbon\Carbon::parse($order->order_date)->format('d.m.Y')

: $order->created_at?->format('d.m.Y');



$shopName = $order->pointOfSale->name

?? $order->point_of_sale_name

?? '—';


// Скидка применяется только при полном возврате всех оставшихся товаров заказа.
$discountAmount = max(0, (float) ($order->discount ?? 0));



$paymentMethodsData = $paymentMethods->map(fn ($method) => [

'id' => $method->id,

'name' => $method->name,

])->values();



$oldPayments = old('payments', [

['payment_method_id' => '', 'amount' => ''],

]);

@endphp



<style>
    .return-page {

        --blue: #0878e8;

        --text: #172033;

        --muted: #758094;

        --line: #e4e8ee;

        --soft: #f6f8fa;

        max-width: 1000px;

        margin: 0 auto;

        padding: 12px 16px 30px;

        color: var(--text);

        font-size: 14px;

        -webkit-text-size-adjust: 100%;

    }



    .return-page * {

        box-sizing: border-box;

    }



    .return-page .return-header {

        display: flex;

        align-items: center;

        gap: 10px;

        margin-bottom: 16px;

    }



    .return-page .back-button {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        width: 32px;

        height: 32px;

        flex-shrink: 0;

        border: 1px solid var(--line);

        border-radius: 8px;

        background: #fff;

        color: var(--text);

        cursor: pointer;

        font-size: 20px;

        text-decoration: none;

    }



    .return-page .back-button::before,

    .return-page .back-button::after {

        content: none !important;

        display: none !important;

    }



    .return-page .page-title {

        margin: 0;

        font-size: 20px;

        line-height: 1.3;

        font-weight: 650;

    }



    .return-page .info-grid {

        display: grid;

        grid-template-columns: 1.2fr 1fr 1fr;

        gap: 12px;

        padding: 11px 0;

        border-top: 1px solid var(--line);

        border-bottom: 1px solid var(--line);

        margin-bottom: 18px;

    }



    .return-page .info-item {

        min-width: 0;

    }



    .return-page .info-label {

        display: block;

        margin-bottom: 3px;

        color: var(--muted);

        font-size: 11px;

        line-height: 1.3;

    }



    .return-page .info-value {

        color: var(--text);

        font-size: 12px;

        line-height: 1.4;

        font-weight: 600;

        overflow-wrap: anywhere;

    }



    .return-page .customer-phone {

        display: block;

        margin-top: 2px;

        color: var(--muted);

        font-size: 10px;

        font-weight: 400;

    }



    /* Сообщения об успехе и ошибках */

    .return-page .notice {

        padding: 10px 12px;

        margin-bottom: 12px;

        border: 1px solid transparent;

        border-radius: 7px;

        font-size: 12px;

        line-height: 1.5;

        overflow-wrap: anywhere;

    }



    .return-page .notice-success {

        color: #16834a;

        background: #edf9f1;

        border-color: #ccebd6;

    }



    .return-page .notice-error {

        color: #c53030;

        background: #fff1f1;

        border-color: #f5cccc;

    }



    .return-page .notice strong {

        display: block;

        margin-bottom: 3px;

    }



    .return-page .notice ul {

        margin: 5px 0 0;

        padding-left: 18px;

    }



    .return-page .section-heading {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 10px;

        margin: 0 0 9px;

        color: var(--muted);

        font-size: 11px;

        font-weight: 700;

        letter-spacing: .4px;

        text-transform: uppercase;

    }



    .return-page .product-list {

        display: flex;

        flex-direction: column;

        gap: 8px;

    }



    .return-page .product-card {

        min-width: 0;

        padding: 10px 12px;

        border: 1px solid var(--line);

        border-radius: 8px;

        background: #fff;

    }



    .return-page .product-top {

        display: grid;

        grid-template-columns: minmax(0, 1fr) auto;

        align-items: center;

        gap: 8px;

        margin-bottom: 9px;

    }



    .return-page .product-sku {

        min-width: 0;

        color: var(--text);

        font-size: 13px;

        line-height: 1.4;

        font-weight: 650;

        overflow-wrap: anywhere;

    }



    .return-page .product-meta {

        display: flex;

        flex-direction: column;

        align-items: flex-end;

        gap: 4px;

        min-width: 0;

    }



    .return-page .product-warehouse {

        color: var(--muted);

        font-size: 12px;

        line-height: 1.4;

        text-align: right;

        white-space: nowrap;

    }



    .return-page .product-available {

        color: #16834a;

        font-size: 11px;

        line-height: 1.4;

        font-weight: 600;

        white-space: nowrap;

    }



    .return-page .product-bottom {

        display: grid;

        grid-template-columns: max-content 58px minmax(0, 1fr);

        align-items: center;

        column-gap: 8px;

        padding-top: 8px;

        border-top: 1px solid #f0f2f5;

    }



    .return-page .price-quantity {

        display: flex;

        align-items: center;

        gap: 5px;

        min-width: 0;

    }



    .return-page .price-value {

        color: var(--text);

        font-size: 13px;

        font-weight: 600;

        white-space: nowrap;

    }



    .return-page .multiply {

        color: var(--muted);

        font-size: 13px;

    }



    .return-page .qty-input {

        display: block;

        width: 58px;

        height: 27px;

        padding: 2px 3px 4px;

        border: 0;

        border-bottom: 1px solid #aab3c0;

        border-radius: 0;

        outline: none;

        background: transparent;

        color: var(--text);

        font: inherit;

        font-size: 13px;

        font-weight: 600;

        text-align: center;

        appearance: textfield;

        -moz-appearance: textfield;

    }



    .return-page .qty-input::-webkit-inner-spin-button,

    .return-page .qty-input::-webkit-outer-spin-button {

        margin: 0;

        -webkit-appearance: none;

    }



    .return-page .qty-input:focus {

        border-bottom: 2px solid var(--blue);

    }



    .return-page .line-total {

        min-width: 0;

        color: var(--text);

        font-size: 13px;

        font-weight: 650;

        text-align: right;

        white-space: nowrap;

    }



    .return-page .total-panel {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 12px;

        margin-top: 16px;

        padding: 12px 13px;

        border: 1px solid var(--line);

        border-radius: 8px;

        background: var(--soft);

    }



    .return-page .total-label {

        color: var(--muted);

        font-size: 12px;

    }



    .return-page .total-value {

        color: var(--text);

        font-size: 21px;

        font-weight: 700;

        white-space: nowrap;

    }

    .return-page .total-value {

        color: var(--text);

        font-size: 21px;

        font-weight: 700;

        white-space: nowrap;

    }

    .return-page .total-panel-breakdown {
        display: block;
    }

    .return-page .total-breakdown-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 4px 0;
    }

    .return-page .total-breakdown-value {
        color: var(--text);
        font-size: 13px;
        font-weight: 650;
        white-space: nowrap;
    }

    .return-page .discount-row .total-breakdown-value {
        color: #c53030;
    }

    .return-page .total-final-row {
        margin-top: 7px;
        padding-top: 10px;
        border-top: 1px solid var(--line);
    }





    .return-page .payment-section {

        margin-top: 21px;

    }



    .return-page .payment-list {

        display: flex;

        flex-direction: column;

        gap: 8px;

    }



    .return-page .payment-row {

        display: grid;

        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) 30px;

        align-items: center;

        gap: 8px;

    }



    .return-page .form-control,

    .return-page .form-select {

        width: 100%;

        min-width: 0;

        min-height: 37px;

        padding: 7px 9px;

        border: 1px solid #dce1e8;

        border-radius: 7px;

        background: #fff;

        color: var(--text);

        font: inherit;

        font-size: 12px;

        box-shadow: none;

    }



    .return-page .form-control:focus,

    .return-page .form-select:focus {

        border-color: var(--blue);

        outline: none;

    }



    .return-page .remove-payment {

        display: flex;

        align-items: center;

        justify-content: center;

        width: 30px;

        height: 34px;

        border: 0;

        border-radius: 6px;

        background: transparent;

        color: #8993a2;

        font-size: 20px;

        cursor: pointer;

    }



    .return-page .remove-payment:hover {

        background: #fff0f0;

        color: #d63b3b;

    }



    .return-page .add-payment {

        display: inline-flex;

        align-items: center;

        gap: 5px;

        margin-top: 10px;

        padding: 4px 0;

        border: 0;

        background: transparent;

        color: var(--blue);

        font: inherit;

        font-size: 13px;

        font-weight: 600;

        cursor: pointer;

    }



    .return-page .payment-summary {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 12px;

        margin-top: 12px;

        color: var(--muted);

        font-size: 12px;

    }



    .return-page .payment-summary strong {

        color: var(--text);

        font-size: 13px;

    }



    .return-page .payment-summary .is-valid {

        color: #16834a;

    }



    .return-page .payment-summary .is-invalid {

        color: #cf3333;

    }



    .return-page .form-error {

        margin-top: 6px;

        color: #cf3333;

        font-size: 12px;

    }



    .return-page .bottom-actions {

        display: flex;

        justify-content: flex-end;

        gap: 8px;

        margin-top: 22px;

    }



    .return-page .btn-cancel,

    .return-page .btn-submit {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        min-height: 39px;

        padding: 9px 15px;

        border-radius: 7px;

        font: inherit;

        font-size: 13px;

        font-weight: 600;

        cursor: pointer;

        text-decoration: none;

    }



    .return-page .btn-cancel {

        border: 1px solid var(--line);

        background: #fff;

        color: #536074;

    }



    .return-page .btn-submit {

        border: 1px solid var(--blue);

        background: var(--blue);

        color: #fff;

    }



    .return-page .btn-submit:disabled {

        opacity: .55;

        cursor: not-allowed;

    }



    @media (max-width: 600px) {

        .return-page {

            padding: 9px 10px 22px;

        }



        .return-page .page-title {

            font-size: 18px;

        }



        .return-page .info-grid {

            grid-template-columns: 1.2fr .9fr .9fr;

            gap: 7px;

            padding: 10px 0;

            margin-bottom: 15px;

        }



        .return-page .info-label {

            font-size: 10px;

        }



        .return-page .info-value {

            font-size: 11px;

        }



        .return-page .customer-phone {

            font-size: 9px;

        }



        .return-page .product-card {

            padding: 9px 10px;

        }



        .return-page .product-top {

            margin-bottom: 8px;

        }



        .return-page .product-sku {

            font-size: 12px;

        }



        .return-page .product-warehouse {

            font-size: 11px;

        }



        .return-page .product-available {

            font-size: 10px;

        }



        .return-page .product-bottom {

            grid-template-columns: max-content 48px minmax(0, 1fr);

            column-gap: 6px;

            padding-top: 7px;

        }



        .return-page .price-quantity {

            gap: 4px;

        }



        .return-page .price-value,

        .return-page .line-total {

            font-size: 12px;

        }



        .return-page .qty-input {

            width: 48px;

            height: 26px;

            font-size: 12px;

        }



        .return-page .total-panel {

            margin-top: 13px;

            padding: 10px;

        }



        .return-page .total-value {

            font-size: 19px;

        }



        .return-page .payment-row {

            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) 25px;

            gap: 6px;

        }



        .return-page .form-control,

        .return-page .form-select {

            min-height: 36px;

            padding: 6px;

            font-size: 11px;

        }



        .return-page .remove-payment {

            width: 25px;

        }



        .return-page .bottom-actions {

            display: grid;

            grid-template-columns: 1fr 1.5fr;

        }



        .return-page .btn-cancel,

        .return-page .btn-submit {

            padding: 9px 6px;

            font-size: 12px;

        }

    }



    @media (max-width: 370px) {

        .return-page .product-bottom {

            grid-template-columns: max-content 42px minmax(0, 1fr);

            column-gap: 5px;

        }



        .return-page .price-quantity {

            gap: 3px;

        }



        .return-page .price-value,

        .return-page .line-total {

            font-size: 11px;

        }



        .return-page .qty-input {

            width: 42px;

        }

    }


    @media (max-width: 600px) {

        .return-page input,
        .return-page select,
        .return-page textarea {
            font-size: 16px !important;
        }
    }
</style>



<div class="return-page" data-discount-amount="{{ $discountAmount }}">

    <div class="return-header">

        <a href="{{ route('admin.orders.edit', $order->id) }}"

            class="back-button"

            aria-label="Назад">←</a>



        <h1 class="page-title">Создание возврата</h1>

    </div>



    {{-- Сообщение об успешном возврате --}}

    @if (session('success'))

    <div class="notice notice-success" role="status">

        <strong>Возврат успешно оформлен</strong>

        {{ session('success') }}

    </div>

    @endif



    {{-- Сообщение об общей ошибке --}}

    @if (session('error'))

    <div class="notice notice-error" role="alert">

        <strong>Не удалось оформить возврат</strong>

        {{ session('error') }}

    </div>

    @endif



    {{-- Ошибки валидации --}}

    @if ($errors->any())

    <div class="notice notice-error" role="alert">

        <strong>Проверьте данные</strong>

        <ul>

            @foreach ($errors->all() as $error)

            <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif



    <div class="info-grid">

        <div class="info-item">

            <span class="info-label">Покупатель</span>

            <div class="info-value">

                {{ $order->name ?: '—' }}

                @if($order->phone)

                <span class="customer-phone">{{ $order->phone }}</span>

                @endif

            </div>

        </div>



        <div class="info-item">

            <span class="info-label">Дата</span>

            <div class="info-value">{{ $orderDate ?: '—' }}</div>

        </div>



        <div class="info-item">

            <span class="info-label">Магазин</span>

            <div class="info-value">{{ $shopName }}</div>

        </div>

    </div>







    <form

        action="{{ route('admin.orders.returns.store', $order->id) }}"

        method="POST"

        id="returnForm">

        @csrf



        <div class="section-heading">

            <span>Товары</span>

            <span>{{ count($rows) }} поз.</span>

        </div>



        <div class="product-list">

            @forelse($rows as $row)

            @php

            $orderItem = $row['order_item'];

            $allocation = $row['allocation'];

            $layer = $row['layer'] ?? null;

            $batch = $row['batch'] ?? null;

            $warehouse = $row['warehouse'] ?? null;



            $sku = $orderItem->variant->sku

            ?? $orderItem->sku

            ?? $layer?->variant?->sku

            ?? '—';



            $batchCode = $batch->batch_code

            ?? $allocation->batch_code

            ?? $layer?->batch_code

            ?? '—';



            $warehouseName = $warehouse->name

            ?? $allocation->warehouse_name

            ?? $layer?->warehouse?->name

            ?? '—';



            $unitPrice = (float) ($orderItem->price ?? 0);

            $available = max(0, (float) ($row['available_quantity'] ?? 0));



            $inputKey = (string) $allocation->id;

            $oldQty = old("items.$inputKey", 0);

            $qty = min($available, max(0, (float) $oldQty));

            @endphp



            <div

                class="product-card"

                data-product-card

                data-order-item-id="{{ $orderItem->id }}"

                data-available="{{ $available }}"

                data-price="{{ $unitPrice }}">

                <div class="product-top">

                    <div class="product-sku">

                        {{ $sku }} ({{ $batchCode }})

                    </div>



                    <div class="product-meta">

                        <div class="product-warehouse">

                            {{ $warehouseName }}

                        </div>



                        <div class="product-available">

                            Доступно: {{ $available }} шт.

                        </div>

                    </div>

                </div>



                <div class="product-bottom">

                    <div class="price-quantity">

                        <span class="price-value">

                            {{ number_format($unitPrice, 0, '.', ' ') }} ₸

                        </span>

                        <span class="multiply">×</span>

                    </div>



                    <div>

                        <input

                            type="number"

                            class="qty-input"

                            id="qty-{{ $allocation->id }}"

                            name="items[{{ $allocation->id }}]"

                            value="{{ $qty }}"

                            min="0"

                            max="{{ $available }}"

                            step="1"

                            inputmode="numeric"

                            data-quantity

                            aria-label="Количество для возврата">

                    </div>



                    <div class="line-total" data-line-total>0 ₸</div>

                </div>

            </div>

            @empty

            <div class="text-muted py-3">

                Нет товаров, доступных для возврата.

            </div>

            @endforelse

        </div>



        <div class="total-panel total-panel-breakdown">
            <div class="total-breakdown-row">
                <span class="total-label">Стоимость выбранных товаров</span>
                <strong class="total-breakdown-value" id="returnSubtotal">0 ₸</strong>
            </div>
            <div class="total-breakdown-row discount-row">
                <span class="total-label">Скидка, учтённая в возврате</span>
                <strong class="total-breakdown-value" id="returnDiscount">−0 ₸</strong>
            </div>
            <div class="total-breakdown-row total-final-row">
                <span class="total-label">Итого к возврату</span>
                <div class="total-value" id="returnTotal">0 ₸</div>
            </div>
        </div>

        <div class="payment-section">

            <div class="section-heading">

                <span>Возврат денег</span>

            </div>



            <div class="payment-list" id="paymentList">

                @foreach($oldPayments as $index => $payment)

                <div class="payment-row" data-payment-row>

                    <select

                        class="form-select"

                        name="payments[{{ $index }}][payment_method_id]"

                        aria-label="Способ возврата">

                        <option value="">Способ возврата</option>



                        @foreach($paymentMethods as $method)

                        <option

                            value="{{ $method->id }}"

                            @selected((string) ($payment['payment_method_id'] ?? '' )===(string) $method->id)

                            >

                            {{ $method->name }}

                        </option>

                        @endforeach

                    </select>



                    <input

                        type="number"

                        class="form-control payment-amount"

                        name="payments[{{ $index }}][amount]"

                        value="{{ $payment['amount'] ?? '' }}"

                        min="0"

                        step="0.01"

                        inputmode="decimal"

                        placeholder="Сумма ₸"

                        aria-label="Сумма возврата">



                    <button

                        type="button"

                        class="remove-payment"

                        data-remove-payment

                        aria-label="Удалить способ возврата"

                        title="Удалить">&times;</button>

                </div>

                @endforeach

            </div>



            <button type="button" class="add-payment" id="addPayment">

                <span style="font-size:18px;line-height:1">+</span>

                Добавить способ возврата

            </button>



            <div class="payment-summary">

                <span>Распределено</span>

                <strong id="paymentTotal">0 ₸</strong>

            </div>



            <div class="payment-summary">

                <span>Остаток</span>

                <strong id="paymentDifference">0 ₸</strong>

            </div>



            <div class="form-error" id="paymentError" hidden>

                Сумма способов возврата должна совпадать с итоговой суммой возврата.

            </div>

        </div>



        <div class="bottom-actions">

            <button type="button" class="btn-cancel" id="cancelReturn">

                Отмена

            </button>



            <button type="submit" class="btn-submit" id="submitReturn">

                Оформить возврат

            </button>

        </div>

    </form>

</div>



<script>
    (function() {

        'use strict';



        const root = document.querySelector('.return-page');

        if (!root) return;



        const form = root.querySelector('#returnForm');

        const productCards = [...root.querySelectorAll('[data-product-card]')];

        const paymentList = root.querySelector('#paymentList');

        const addPaymentButton = root.querySelector('#addPayment');

        const returnSubtotalElement = root.querySelector('#returnSubtotal');

        const returnDiscountElement = root.querySelector('#returnDiscount');

        const returnTotalElement = root.querySelector('#returnTotal');

        const paymentTotalElement = root.querySelector('#paymentTotal');

        const paymentDifferenceElement = root.querySelector('#paymentDifference');

        const paymentError = root.querySelector('#paymentError');

        const submitButton = root.querySelector('#submitReturn');



        const paymentMethods = @json($paymentMethodsData);

        const discountAmount = Math.max(0, Number(root.dataset.discountAmount) || 0);



        let nextPaymentIndex = Math.max(

            -1,

            ...[...paymentList.querySelectorAll('[name]')].map(el => {

                const match = el.name.match(/^payments\[(\d+)\]/);

                return match ? Number(match[1]) : -1;

            })

        ) + 1;



        const money = value =>

            new Intl.NumberFormat('ru-RU', {

                maximumFractionDigits: 2

            }).format(value) + ' ₸';



        const parseNumber = value => {

            const number = Number(value);

            return Number.isFinite(number) ? number : 0;

        };



        function calculateProducts() {

            let total = 0;



            productCards.forEach(card => {

                const price = parseNumber(card.dataset.price);

                const input = card.querySelector('[data-quantity]');

                const max = parseNumber(input.max);



                let quantity = Math.max(0, Math.floor(parseNumber(input.value)));



                if (quantity > max) {

                    quantity = max;

                    input.value = String(max);

                }



                const lineTotal = price * quantity;

                total += lineTotal;



                card.querySelector('[data-line-total]').textContent = money(lineTotal);

            });



            return Math.round((total + Number.EPSILON) * 100) / 100;

        }



        function calculatePayments() {

            let total = 0;



            paymentList.querySelectorAll('.payment-amount').forEach(input => {

                total += Math.max(0, parseNumber(input.value));

            });



            return Math.round((total + Number.EPSILON) * 100) / 100;

        }



        /*

         * При изменении количества автоматически устанавливаем

         * сумму возврата в первый способ оплаты.

         * Остальные поля очищаем, чтобы не задвоить сумму.

         */

        function isFullReturnSelected() {
            if (productCards.length === 0) return false;

            const quantitiesByOrderItem = new Map();
            const availableByOrderItem = new Map();

            productCards.forEach(card => {
                const orderItemId = String(card.dataset.orderItemId || '');
                const input = card.querySelector('[data-quantity]');
                if (!orderItemId || !input) return;

                const selectedQuantity = Math.max(0, Math.floor(parseNumber(input.value)));
                const availableQuantity = Math.max(0, parseNumber(card.dataset.available));

                quantitiesByOrderItem.set(
                    orderItemId,
                    (quantitiesByOrderItem.get(orderItemId) || 0) + selectedQuantity
                );
                availableByOrderItem.set(
                    orderItemId,
                    (availableByOrderItem.get(orderItemId) || 0) + availableQuantity
                );
            });

            if (availableByOrderItem.size === 0) return false;

            for (const [orderItemId, availableQuantity] of availableByOrderItem.entries()) {
                const selectedQuantity = quantitiesByOrderItem.get(orderItemId) || 0;
                if (selectedQuantity < availableQuantity) return false;
            }

            return true;
        }

        function recalculate() {
            // Цены рулонов остаются исходными. Скидка вычитается только
            // когда выбран полный остаток всех товаров заказа.
            const productsTotal = calculateProducts();
            const fullReturn = isFullReturnSelected();
            const discount = fullReturn ? Math.min(productsTotal, discountAmount) : 0;
            const total = Math.max(0, Math.round((productsTotal - discount + Number.EPSILON) * 100) / 100);

            returnSubtotalElement.textContent = money(productsTotal);
            returnDiscountElement.textContent = '−' + money(discount);

            const paymentRows = [

                ...paymentList.querySelectorAll('[data-payment-row]')

            ];



            paymentRows.forEach((row, index) => {

                const amountInput = row.querySelector('.payment-amount');



                if (index === 0) {

                    amountInput.value = total > 0 ? String(total) : '';

                } else {

                    amountInput.value = '';

                }

            });



            const paid = calculatePayments();

            const difference = Math.round(

                (total - paid + Number.EPSILON) * 100

            ) / 100;



            const isBalanced = Math.abs(difference) < 0.01;



            returnTotalElement.textContent = money(total);

            paymentTotalElement.textContent = money(paid);

            paymentDifferenceElement.textContent = money(difference);



            paymentDifferenceElement.classList.toggle('is-valid', isBalanced);

            paymentDifferenceElement.classList.toggle('is-invalid', !isBalanced);



            paymentError.hidden = isBalanced;

            submitButton.disabled = total <= 0 || !isBalanced;



            return {

                total,

                paid,

                difference

            };

        }



        function createPaymentRow(index) {

            const row = document.createElement('div');

            row.className = 'payment-row';

            row.setAttribute('data-payment-row', '');



            const select = document.createElement('select');

            select.className = 'form-select';

            select.name = `payments[${index}][payment_method_id]`;

            select.setAttribute('aria-label', 'Способ возврата');



            const placeholder = document.createElement('option');

            placeholder.value = '';

            placeholder.textContent = 'Способ возврата';

            select.appendChild(placeholder);



            paymentMethods.forEach(method => {

                const option = document.createElement('option');

                option.value = method.id;

                option.textContent = method.name;

                select.appendChild(option);

            });



            const amount = document.createElement('input');

            amount.type = 'number';

            amount.className = 'form-control payment-amount';

            amount.name = `payments[${index}][amount]`;

            amount.min = '0';

            amount.step = '0.01';

            amount.inputMode = 'decimal';

            amount.placeholder = 'Сумма ₸';

            amount.setAttribute('aria-label', 'Сумма возврата');



            const remove = document.createElement('button');

            remove.type = 'button';

            remove.className = 'remove-payment';

            remove.setAttribute('data-remove-payment', '');

            remove.setAttribute('aria-label', 'Удалить способ возврата');

            remove.title = 'Удалить';

            remove.textContent = '×';



            row.append(select, amount, remove);



            return row;

        }



        root.addEventListener('input', function(event) {

            if (event.target.matches('[data-quantity]')) {

                recalculate();

            }

        });



        root.addEventListener('change', function(event) {

            if (!event.target.matches('[data-quantity]')) return;



            const input = event.target;

            const max = parseNumber(input.max);

            let quantity = Math.max(0, Math.floor(parseNumber(input.value)));



            if (quantity > max) quantity = max;



            input.value = String(quantity);

            recalculate();

        });



        addPaymentButton.addEventListener('click', function() {

            paymentList.appendChild(createPaymentRow(nextPaymentIndex++));

            recalculate();

        });



        paymentList.addEventListener('click', function(event) {

            const removeButton = event.target.closest('[data-remove-payment]');

            if (!removeButton) return;



            const rows = paymentList.querySelectorAll('[data-payment-row]');

            const row = removeButton.closest('[data-payment-row]');



            if (rows.length <= 1) {

                row.querySelector('select').value = '';

                row.querySelector('.payment-amount').value = '';

            } else {

                row.remove();

            }



            recalculate();

        });



        form.addEventListener('submit', function(event) {

            const {

                total,

                difference

            } = recalculate();



            if (total <= 0) {

                event.preventDefault();

                alert('Укажите количество товара для возврата.');

                return;

            }



            if (Math.abs(difference) >= 0.01) {

                event.preventDefault();

                alert('Сумма способов возврата должна совпадать с итоговой суммой.');

                return;

            }



            const hasInvalidPayment = [

                ...paymentList.querySelectorAll('[data-payment-row]')

            ].some(row => {

                const amount = parseNumber(

                    row.querySelector('.payment-amount').value

                );

                const method = row.querySelector('select').value;



                return amount > 0 && !method;

            });



            if (hasInvalidPayment) {

                event.preventDefault();

                alert('Выберите способ возврата для каждой указанной суммы.');

            }

        });



        function goBack() {

            if (window.history.length > 1) {

                window.history.back();

            } else {

                window.location.href = @json(route('admin.orders.seller'));

            }

        }



        root.querySelector('.back-button').addEventListener('click', goBack);

        root.querySelector('#cancelReturn').addEventListener('click', goBack);



        recalculate();

    })();
</script>

@endsection