@extends('layouts.app')

@section('title', 'Редактировать заказ')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --page-bg:#f7f8fa;
    --surface:#fff;
    --text:#30353b;
    --text-secondary:#737b85;
    --text-muted:#a1a8b1;
    --border:#e8ebef;
    --border-light:#f0f2f4;
    --blue:#2f6fed;
    --blue-hover:#1f56d1;
    --blue-light:#f1f6fd;
    --blue-border:#dce8f7;
    --red:#dc747b;
    --red-light:#fff6f6;
    --red-border:#f2dddd;
    --green:#63a98c;
    --green-light:#f0f8f4;
    --radius:9px;
    --radius-small:7px;
}

* { box-sizing:border-box; }

body {
    background:var(--page-bg);
    color:var(--text);
    font-family:'Inter','Segoe UI',Arial,sans-serif;
    font-size:13px;
    -webkit-font-smoothing:antialiased;
}

.edit-page {
    max-width:680px;
    padding-top:20px;
    padding-bottom:45px;
}

.edit-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:15px;
    margin-bottom:16px;
}

.edit-heading { min-width:0; }

.edit-title {
    margin:0;
    color:var(--text);
    font-size:20px;
    line-height:1.2;
    font-weight:700;
    letter-spacing:-.025em;
}

.edit-subtitle {
    margin-top:4px;
    color:var(--text-muted);
    font-size:11px;
}

.cancel-order,
.finish-order {
    height:34px;
    padding:0 11px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:5px;
    border-radius:var(--radius-small);
    font-family:inherit;
    font-size:11px;
    font-weight:600;
    cursor:pointer;
    transition:background .15s ease,border-color .15s ease;
    white-space:nowrap;
}

.cancel-order {
    background:var(--red-light);
    border:1px solid var(--red-border);
    color:var(--red);
}

.cancel-order:hover {
    background:#ffeded;
    border-color:#efc5c8;
    color:#d6535c;
}

.finish-order {
    padding:0 14px;
    background:var(--blue);
    border:1px solid var(--blue);
    color:#fff;
}

.finish-order:hover {
    background:var(--blue-hover);
    border-color:var(--blue-hover);
}

.order-controls {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    margin-bottom:10px;
}

/* RETURN STATUS */

.return-toolbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:10px;
    padding:11px 13px;
    margin-bottom:8px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:var(--radius);
}

.return-status {
    display:flex;
    flex-direction:column;
    gap:4px;
    min-width:0;
}

.return-status-title {
    color:var(--text);
    font-size:12px;
    font-weight:600;
}

.return-status-details {
    color:var(--text-secondary);
    font-size:11px;
    line-height:1.5;
}

.return-status-badge {
    display:inline-flex;
    align-items:center;
    gap:5px;
    width:fit-content;
    padding:4px 7px;
    border-radius:5px;
    background:var(--green-light);
    color:#43896d;
    font-size:10px;
    font-weight:600;
}

.return-status-badge.none {
    background:#f3f5f7;
    color:#858d97;
}

.return-button {
    min-height:34px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    padding:0 11px;
    border:1px solid var(--blue-border);
    border-radius:var(--radius-small);
    background:var(--blue-light);
    color:var(--blue);
    font-family:inherit;
    font-size:11px;
    font-weight:600;
    text-decoration:none;
    white-space:nowrap;
}

.return-button:hover {
    background:#e6efff;
    color:var(--blue-hover);
}

/* SECTIONS */

.edit-section {
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:var(--radius);
    padding:12px 13px;
    margin-bottom:8px;
}

.section-title {
    display:flex;
    align-items:center;
    gap:6px;
    margin-bottom:9px;
    color:#858d97;
    font-size:10.5px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.025em;
}

.section-title i {
    color:#aeb6bf;
    font-size:11px;
}

/* FORM */

.form-label {
    display:block;
    margin-bottom:4px;
    color:#9299a2;
    font-size:10.5px;
    font-weight:500;
}

.form-control,
.form-select {
    min-height:35px;
    border:1px solid var(--border)!important;
    border-radius:var(--radius-small)!important;
    background:#fff!important;
    color:#41474e!important;
    font-family:inherit;
    font-size:12px;
    box-shadow:none!important;
}

.form-control::placeholder { color:#adb4bc; }

.form-control:focus,
.form-select:focus {
    border-color:#c8dbf1!important;
    box-shadow:0 0 0 3px var(--blue-light)!important;
}

.form-control:hover,
.form-select:hover {
    border-color:#dce1e6!important;
}

.customer-grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:8px;
}

.customer-bottom-grid {
    display:grid;
    grid-template-columns:minmax(0,1fr) 180px;
    gap:8px;
    margin-top:8px;
}

.comment-input {
    resize:vertical;
    min-height:35px;
    max-height:120px;
}

/* PRODUCTS */

.order-item {
    position:relative;
    background:#fff;
    border:1px solid var(--border);
    border-radius:8px;
    padding:9px 10px;
    margin-bottom:6px;
    transition:border-color .15s ease;
}

.order-item:hover { border-color:#dce1e6; }
.order-item:last-child { margin-bottom:0; }
.item-main { min-width:0; }

.item-header {
    display:flex;
    align-items:center;
    gap:7px;
    min-width:0;
}

.item-sku {
    min-width:0;
    color:#3d444b;
    font-size:12px;
    font-weight:600;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.item-batch {
    flex:0 0 auto;
    color:#a1a8b0;
    font-size:10px;
    white-space:nowrap;
}

.item-edit-row {
    display:flex;
    align-items:center;
    gap:8px;
    margin-top:7px;
}

.item-quantity {
    color:#7c848d;
    font-size:10.5px;
    white-space:nowrap;
}

.price-wrapper {
    display:flex;
    align-items:center;
    gap:4px;
}

.price-input {
    width:105px!important;
    min-height:31px!important;
    padding:4px 8px;
    border:1px solid var(--border)!important;
    border-radius:6px!important;
    background:#fff;
    color:#3e444b;
    font-family:inherit;
    font-size:11.5px;
    font-weight:600;
    text-align:right;
    outline:none;
    box-shadow:none!important;
}

.price-input:focus {
    border-color:#c8dbf1!important;
    box-shadow:0 0 0 3px var(--blue-light)!important;
}

.price-currency {
    color:#a1a8b0;
    font-size:10px;
}

.item-total {
    margin-left:auto;
    color:#3e444b;
    font-size:11.5px;
    font-weight:700;
    white-space:nowrap;
}

/* TOTAL AND DISCOUNT */

.order-total-box {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-top:8px;
    padding:10px 12px;
    background:#fafbfc;
    border:1px solid var(--border-light);
    border-radius:8px;
}

.order-total-left {
    display:flex;
    align-items:center;
    gap:8px;
    min-width:0;
}

.order-total-label {
    color:#8b939c;
    font-size:11px;
    font-weight:500;
    white-space:nowrap;
}

.discount-inline {
    display:flex;
    align-items:center;
    gap:4px;
}

.discount-inline-label {
    color:#9299a2;
    font-size:10.5px;
    white-space:nowrap;
}

.discount-input {
    width:95px!important;
    min-height:31px!important;
    text-align:right;
    font-weight:600;
}

.discount-currency {
    color:#a1a8b0;
    font-size:10px;
}

.order-total-right {
    display:flex;
    align-items:baseline;
    gap:5px;
    white-space:nowrap;
}

.order-total-value {
    color:#30353b;
    font-size:17px;
    font-weight:700;
    letter-spacing:-.015em;
}

.order-total-currency {
    color:#9299a2;
    font-size:10.5px;
    font-weight:500;
}

/* PAYMENTS */

.payments-wrapper { margin-top:8px; }

.payment-item {
    background:#fff;
    border:1px solid var(--border);
    border-radius:8px;
    padding:8px;
    margin-bottom:6px;
}

.payment-item:last-child { margin-bottom:0; }

.payment-row {
    display:flex;
    align-items:center;
    gap:7px;
}

.payment-method {
    flex:1 1 auto;
    min-width:0;
}

.payment-amount {
    width:125px!important;
    flex:0 0 125px;
    text-align:right;
    font-weight:600;
}

.remove-payment {
    width:28px;
    height:32px;
    flex:0 0 28px;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:0;
    background:var(--red-light);
    border:1px solid var(--red-border);
    border-radius:6px;
    color:var(--red);
    font-size:14px;
    cursor:pointer;
}

.remove-payment:hover {
    background:#ffeded;
    border-color:#efc5c8;
    color:#d6535c;
}

.add-payment {
    height:32px;
    display:inline-flex;
    align-items:center;
    gap:5px;
    margin-top:7px;
    padding:0 10px;
    background:var(--blue-light);
    border:1px solid var(--blue-border);
    border-radius:6px;
    color:var(--blue);
    font-family:inherit;
    font-size:10.5px;
    font-weight:600;
    cursor:pointer;
}

.add-payment:hover {
    background:#eaf2ff;
    border-color:#cbdcf0;
}

/* PAYMENT SUMMARY */

.payment-summary {
    margin-top:8px;
    background:#fafbfc;
    border:1px solid var(--border-light);
    border-radius:8px;
    padding:9px 10px;
}

.payment-summary-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    min-height:23px;
    color:#858d97;
    font-size:10.5px;
}

.payment-summary-row strong {
    color:#4a5159;
    font-size:11px;
    font-weight:600;
}

.payment-difference { font-weight:700!important; }

.payment-error,
.payment-success {
    display:none;
    margin-top:7px;
    padding:8px 9px;
    border-radius:6px;
    font-size:10.5px;
    font-weight:600;
}

.payment-error {
    background:var(--red-light);
    border:1px solid var(--red-border);
    color:#d65d65;
}

.payment-success {
    background:var(--green-light);
    border:1px solid #dcefe6;
    color:var(--green);
}

/* MOBILE */

@media(max-width:600px) {
    .edit-page { padding:14px 12px 35px; }
    .edit-title { font-size:18px; }
    .edit-subtitle { font-size:10px; }
    .customer-grid { grid-template-columns:1fr 1fr; gap:6px; }
    .customer-bottom-grid { grid-template-columns:1fr 1fr; gap:6px; }
    .order-controls { margin-bottom:9px; }
    .cancel-order,.finish-order { height:33px; }
    .edit-section { padding:10px; }
    .payment-row { gap:5px; }
    .payment-amount { width:105px!important; flex-basis:105px; }
    .item-edit-row { gap:6px; }
    .price-input { width:95px!important; }
    .order-total-box { gap:8px; }
    .discount-input { width:85px!important; }

    input.form-control,
    select.form-select,
    textarea.form-control,
    .price-input,
    .payment-amount,
    .discount-input {
        font-size:16px!important;
        -webkit-text-size-adjust:100%;
        text-size-adjust:100%;
    }
}

@media(max-width:420px) {
    .customer-bottom-grid { grid-template-columns:1fr 1fr; }
    .order-total-box { align-items:center; }
    .order-total-left { gap:5px; }
    .discount-inline-label { font-size:10px; }
    .discount-input { width:78px!important; }
    .order-total-value { font-size:15px; }
    .return-toolbar { align-items:flex-start; }
    .return-button { width:100%; }
}
</style>

<style>
    .return-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-left: 10px;
        color: #0d6efd;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .return-link:hover {
        color: #084298;
        text-decoration: underline;
    }
</style>

<div class="container edit-page">

<form method="POST"
      action="{{ route('admin.orders.update', $order->id) }}"
      id="orderForm">
    @csrf
    @method('PUT')

    {{-- HEADER --}}

    <div class="order-controls">

<a href="{{ route('admin.orders.seller') }}" class="cancel-order">
    <i class="bi bi-x-lg"></i>
    ОТМЕНА
</a>

        <div class="edit-heading">
            <h1 class="edit-title">Заказ #{{ $order->id }}</h1>
            <div class="edit-subtitle">Редактирование заказа</div>
        </div>

        <button type="submit" id="finishOrder" class="finish-order">
            <i class="bi bi-check-lg"></i>
            СОХРАНИТЬ
        </button>

    </div>

    {{-- CUSTOMER RETURN STATUS --}}

<div class="return-toolbar">

    <div class="return-status">

        @if(isset($customerReturns) && $customerReturns->isNotEmpty())


            @foreach($customerReturns as $customerReturn)
                <div class="return-status-details">
                    Возврат №{{ $customerReturn->id }}

                    @if(!empty($customerReturn->return_date))
                        · {{ \Carbon\Carbon::parse($customerReturn->return_date)->format('d.m.Y') }}
                    @endif

                    <a href="{{ route('admin.orders.returns.show', $customerReturn->id) }}"
                       class="return-link">
                        <i class="bi bi-box-arrow-up-right"></i>
                        Открыть возврат
                    </a>
                </div>
            @endforeach

        @else

            <div class="return-status-badge none">
                <i class="bi bi-check-circle"></i>
                Возвратов пока нет
            </div>

            <div class="return-status-details">
                По этому заказу ещё не оформляли возврат.
            </div>

        @endif

    </div>

    <a href="{{ route('admin.orders.returns.create', $order->id) }}"
       class="return-button">
        <i class="bi bi-arrow-counterclockwise"></i>
         Создать возврат покупателя
    </a>

</div>



    {{-- CUSTOMER --}}

    <div class="edit-section">

        <div class="section-title">
            <i class="bi bi-person"></i>
            Покупатель
        </div>

        <div class="customer-grid">

            <div>
                <label class="form-label">Имя</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $order->name) }}"
                       placeholder="Имя"
                       required>
            </div>

            <div>
                <label class="form-label">Телефон</label>
                <input type="text"
                       name="phone"
                       class="form-control"
                       value="{{ old('phone', $order->phone) }}"
                       placeholder="Телефон"
                       required>
            </div>

        </div>

        <div class="customer-bottom-grid">

            <div>
                <label class="form-label">Комментарий</label>
                <textarea name="comment"
                          class="form-control comment-input"
                          placeholder="Комментарий"
                          rows="1">{{ old('comment', $order->comment) }}</textarea>
            </div>

            <div>
                <label class="form-label">Точка продаж</label>
                <select name="point_of_sale_id" class="form-select">
                    <option value="">Не выбрана</option>

                    @foreach($pointsOfSale as $point)
                        <option value="{{ $point->id }}"
                            {{ old('point_of_sale_id', $order->point_of_sale_id) == $point->id ? 'selected' : '' }}>
                            {{ $point->name }}
                        </option>
                    @endforeach

                </select>
            </div>

        </div>

    </div>

    {{-- PRODUCTS --}}

    <div class="edit-section">

        <div class="section-title">
            <i class="bi bi-box-seam"></i>
            Товары
        </div>

        <div id="itemsWrapper">

            @foreach($order->items as $i => $item)

                <div class="order-item">

                    <div class="item-main">

                        <div class="item-header">
                            <div class="item-sku">
                                {{ $item->variant->sku ?? 'Без артикула' }}
                            </div>

                            @if($item->batch_code)
                                <div class="item-batch">
                                    Партия: {{ $item->batch_code }}
                                </div>
                            @endif
                        </div>

                        <div class="item-edit-row">

                            <div class="item-quantity">
                                {{ $item->quantity }} ×
                            </div>

                            <div class="price-wrapper">
                                <input type="text"
                                       name="items[{{ $i }}][price]"
                                       class="price-input"
                                       value="{{ old("items.$i.price", number_format((float) $item->price, 0, '.', '')) }}"
                                       inputmode="numeric"
                                       autocomplete="off"
                                       placeholder="Цена">

                                <span class="price-currency">₸</span>
                            </div>

                            <span class="item-total"></span>

                        </div>

                    </div>

                    <input type="hidden"
                           name="items[{{ $i }}][id]"
                           value="{{ $item->id }}">

                    <input type="hidden"
                           name="items[{{ $i }}][quantity]"
                           value="{{ $item->quantity }}">

                </div>

            @endforeach

        </div>

        {{-- TOTAL AND DISCOUNT --}}

        <div class="order-total-box">

            <div class="order-total-left">


                <div class="order-total-label">Итого:</div>

            </div>

            <div class="order-total-right">
                <span class="order-total-value" id="orderTotal">0</span>
                <span class="order-total-currency">₸</span>
            </div>

        </div>

    </div>

    {{-- PAYMENTS --}}

    <div class="edit-section payments-wrapper">

        <div class="section-title">
            <i class="bi bi-credit-card"></i>
            Оплата
        </div>

        <div id="paymentsWrapper">

            @if($order->payments->count())

                @foreach($order->payments as $payment)

                    <div class="payment-item">

                        <div class="payment-row">

                            <select name="payments[{{ $loop->index }}][payment_method]"
                                    class="form-select payment-method">

                                <option value="">Не указано</option>

                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->name }}"
                                        {{ $payment->payment_method === $method->name ? 'selected' : '' }}>
                                        {{ $method->name }}
                                    </option>
                                @endforeach

                            </select>

                            <input type="text"
                                   name="payments[{{ $loop->index }}][amount]"
                                   class="form-control payment-amount"
                                   value="{{ number_format((float) $payment->amount, 0, '.', '') }}"
                                   inputmode="numeric"
                                   autocomplete="off"
                                   placeholder="Сумма">

                            <button type="button"
                                    class="remove-payment"
                                    title="Удалить оплату">
                                <i class="bi bi-trash3"></i>
                            </button>

                        </div>

                    </div>

                @endforeach

            @else

                <div class="payment-item">
                    <div class="payment-row">

                        <select name="payments[0][payment_method]"
                                class="form-select payment-method">
                            <option value="">Не указано</option>

                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->name }}">
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>

                        <input type="text"
                               name="payments[0][amount]"
                               class="form-control payment-amount"
                               value="0"
                               inputmode="numeric"
                               autocomplete="off"
                               placeholder="Сумма">

                        <button type="button"
                                class="remove-payment"
                                title="Удалить оплату">
                            <i class="bi bi-trash3"></i>
                        </button>

                    </div>
                </div>

            @endif

        </div>

        <button type="button" class="add-payment" id="addPayment">
            <i class="bi bi-plus-lg"></i>
            Добавить оплату
        </button>

        <div class="payment-summary">

            <div class="payment-summary-row">
                <span>Сумма заказа</span>
                <strong><span id="paymentOrderTotal">0</span> ₸</strong>
            </div>

            <div class="payment-summary-row">
                <span>Сумма оплаты</span>
                <strong><span id="paymentTotal">0</span> ₸</strong>
            </div>

            <div class="payment-summary-row">
                <span>Разница</span>
                <strong class="payment-difference">
                    <span id="paymentDifference">0</span> ₸
                </strong>
            </div>

            <div id="paymentError" class="payment-error"></div>

            <div id="paymentSuccess" class="payment-success">
                Сумма заказа и сумма оплаты совпадают.
            </div>

        </div>

    </div>

</form>
</div>

<script>
const paymentMethods = @json($paymentMethods->pluck('name')->values());

function formatAmount(amount) {
    amount = Math.round(Number(amount) || 0);
    const negative = amount < 0;
    amount = Math.abs(amount);

    const formatted = amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

    return negative ? '-' + formatted : formatted;
}

function parseAmount(value) {
    let string = String(value ?? '').trim().replace(/\s/g, '');

    if (string === '' || string === '-') {
        return 0;
    }

    // В этом интерфейсе точка используется как разделитель тысяч.
    string = string.replace(/\./g, '').replace(',', '.');

    return Number(string) || 0;
}

function formatInputAmount(input) {
    let value = String(input.val() ?? '').trim();
    const negative = value.startsWith('-');
    const raw = value.replace(/\D/g, '');

    if (raw === '') {
        input.val(negative ? '-' : '');
        return;
    }

    let number = parseInt(raw, 10);

    if (negative) {
        number = -number;
    }

    input.val(formatAmount(number));
}

function recalc() {
    let sum = 0;

    $('.order-item').each(function() {
        const qty = parseInt($(this).find('input[name$="[quantity]"]').val(), 10) || 0;
        const price = parseAmount($(this).find('.price-input').val());
        const total = qty * price;

        $(this).find('.item-total').text(formatAmount(total) + ' ₸');

        sum += total;
    });

    const discount = parseAmount($('#discountInput').val());
    const orderTotal = Math.round(sum - discount);

    $('#orderTotal').text(formatAmount(orderTotal));
    $('#paymentOrderTotal').text(formatAmount(orderTotal));

    recalcPayments(orderTotal);
}

function getPaymentsTotal() {
    let total = 0;

    $('.payment-amount').each(function() {
        total += parseAmount($(this).val());
    });

    return Math.round(total);
}

function recalcPayments(orderTotal) {
    const paymentTotal = getPaymentsTotal();

    $('#paymentTotal').text(formatAmount(paymentTotal));

    const difference = Math.round(orderTotal - paymentTotal);

    $('#paymentDifference').text(formatAmount(difference));

    if (difference === 0) {
        $('#paymentSuccess').show();
        $('#paymentError').hide();
    } else {
        $('#paymentSuccess').hide();
        $('#paymentError').show();

        if (difference > 0) {
            $('#paymentError').text(
                'Не хватает ' + formatAmount(difference) + ' ₸ для полной оплаты заказа.'
            );
        } else {
            $('#paymentError').text(
                'Сумма оплаты больше суммы заказа на ' +
                formatAmount(Math.abs(difference)) + ' ₸.'
            );
        }
    }
}

$(document).on('input', '.price-input, #discountInput', function() {
    formatInputAmount($(this));
    recalc();
});

$(document).on('input', '.payment-amount', function() {
    formatInputAmount($(this));
    recalc();
});

let paymentIndex = {{ max($order->payments->count(), 1) }};

$('#addPayment').on('click', function() {
    const orderTotal = parseAmount($('#orderTotal').text());
    const remaining = Math.max(0, orderTotal - getPaymentsTotal());

    let options = '<option value="">Не указано</option>';

    paymentMethods.forEach(function(method) {
        const safeMethod = $('<div>').text(method).html();

        options += '<option value="' + safeMethod + '">' + safeMethod + '</option>';
    });

    const html = `
        <div class="payment-item">
            <div class="payment-row">
                <select name="payments[${paymentIndex}][payment_method]"
                        class="form-select payment-method">
                    ${options}
                </select>

                <input type="text"
                       name="payments[${paymentIndex}][amount]"
                       class="form-control payment-amount"
                       value="${formatAmount(remaining)}"
                       inputmode="numeric"
                       autocomplete="off"
                       placeholder="Сумма">

                <button type="button" class="remove-payment" title="Удалить оплату">
                    <i class="bi bi-trash3"></i>
                </button>
            </div>
        </div>
    `;

    $('#paymentsWrapper').append(html);
    paymentIndex++;

    recalc();
});

$(document).on('click', '.remove-payment', function() {
    $(this).closest('.payment-item').remove();
    recalc();
});

$('#orderForm').on('submit', function(e) {
    let missingMethod = false;
    let firstEmpty = null;

    $('.payment-method').each(function() {
        if (!$(this).val()) {
            missingMethod = true;

            if (!firstEmpty) {
                firstEmpty = $(this);
            }
        }
    });

    if (missingMethod) {
        e.preventDefault();

        $('#paymentError')
            .text('Выберите способ оплаты перед сохранением заказа.')
            .show();

        $('#paymentSuccess').hide();

        alert('Выберите способ оплаты перед сохранением заказа.');

        if (firstEmpty) {
            firstEmpty.trigger('focus');
        }

        return false;
    }

    const orderTotal = Math.round(parseAmount($('#orderTotal').text()));
    const paymentTotal = getPaymentsTotal();
    const difference = orderTotal - paymentTotal;

    if (difference !== 0) {
        e.preventDefault();

        $('#paymentSuccess').hide();
        $('#paymentError').show();

        if (difference > 0) {
            $('#paymentError').text(
                'Нельзя сохранить заказ. Не хватает ' +
                formatAmount(difference) + ' ₸ для полной оплаты.'
            );
        } else {
            $('#paymentError').text(
                'Нельзя сохранить заказ. Сумма оплаты больше суммы заказа на ' +
                formatAmount(Math.abs(difference)) + ' ₸.'
            );
        }

        alert($('#paymentError').text());

        return false;
    }

    $('.price-input').each(function() {
        $(this).val(parseAmount($(this).val()));
    });

    $('#discountInput').val(parseAmount($('#discountInput').val()));

    $('.payment-amount').each(function() {
        $(this).val(parseAmount($(this).val()));
    });
});

$('.price-input, #discountInput, .payment-amount').each(function() {
    formatInputAmount($(this));
});

recalc();

$('#cancelOrder').on('click', function() {
    window.history.back();
});
</script>

@endsection
