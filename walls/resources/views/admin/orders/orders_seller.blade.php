@extends('layouts.app')

@section('title', 'Заказы продавцов')

@section('content')

@php

$selectedDate = request()->get('date')
? \Carbon\Carbon::parse(request()->get('date'))->format('Y-m-d')
: now()->format('Y-m-d');

$prevDate = \Carbon\Carbon::parse($selectedDate)->subDay()->format('Y-m-d');
$nextDate = \Carbon\Carbon::parse($selectedDate)->addDay()->format('Y-m-d');

$totalDaySum = 0;

$methodNames = [
'cash' => 'Наличные',
'qr' => 'QR',
'transfer' => 'Перевод',
'card' => 'Карта',
];

@endphp

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
    rel="stylesheet">

<style>
    :root {
        --page-bg: #f7f8fa;
        --surface: #ffffff;
        --text: #25292f;
        --text-secondary: #727b87;
        --text-muted: #a1a8b1;
        --border: #e8ebef;
        --border-light: #eef0f3;

        --blue: #2f6fed;
        --blue-hover: #1f56d1;
        --blue-light: #f1f6fd;
        --blue-light-hover: #eaf2fc;
        --blue-border: #dce8f7;

        --red: #dc747b;
        --red-light: #fff6f6;
        --red-hover: #ffeded;
        --red-border: #f2dddd;

        --green: #63a98c;
        --green-light: #f0f8f4;

        --radius: 9px;
        --radius-small: 7px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: var(--page-bg);
        color: var(--text);
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        font-size: 14px;
        -webkit-font-smoothing: antialiased;
    }

    .container {
        max-width: 680px;
    }

    .orders-page {
        padding-top: 10px;
        padding-bottom: 45px;
    }

    /* =========================================================
       HEADER
    ========================================================= */

    .orders-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 17px;
    }

    .orders-title {
        margin: 0;
        font-size: 20px;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: -0.025em;
        color: var(--text);
    }

    .orders-subtitle {
        margin-top: 4px;
        font-size: 11.5px;
        color: var(--text-muted);
    }

    /* =========================================================
       CREATE
    ========================================================= */

    .btn-create {
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 15px;

        background: var(--blue);
        color: #fff;

        border: 1px solid var(--blue);
        border-radius: var(--radius-small);

        font-family: inherit;
        font-size: 13px;
        font-weight: 600;

        text-decoration: none;

        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease;
    }

    .btn-create:hover {
        background: var(--blue-hover);
        border-color: var(--blue-hover);
        color: #fff;
    }

    /* =========================================================
       DATE
    ========================================================= */

    .orders-toolbar {
        display: flex;
        gap: 6px;
        margin-bottom: 10px;
    }

    .date-picker {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .date-arrow {
        width: 35px;
        height: 35px;
        flex: 0 0 35px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-small);

        color: #89919b;
        text-decoration: none;

        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease;
    }

    .date-arrow:hover {
        background: var(--blue-light);
        border-color: var(--blue-border);
        color: var(--blue);
    }

    .date-input {
        width: 100%;
        height: 35px;

        background: #fff;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-small) !important;

        color: var(--text);
        text-align: center;

        font-family: inherit;
        font-size: 13px;

        box-shadow: none !important;
        outline: none;
    }

    .date-input:focus {
        border-color: #c8dbf1 !important;
        box-shadow: 0 0 0 3px var(--blue-light) !important;
    }

    /* =========================================================
       SEARCH
    ========================================================= */

    .search-wrapper {
        position: relative;
        margin-bottom: 13px;
    }

    .search-icon {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);

        color: #adb4bc;
        font-size: 13px;

        pointer-events: none;
    }

    #searchInput {
        width: 100%;
        height: 39px;

        padding-left: 36px;

        background: #fff;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius) !important;

        color: var(--text);

        font-family: inherit;
        font-size: 13px;

        box-shadow: none !important;
        outline: none;
    }

    #searchInput::placeholder {
        color: #adb4bc;
    }

    #searchInput:focus {
        border-color: #c8dbf1 !important;
        box-shadow: 0 0 0 3px var(--blue-light) !important;
    }

    /* =========================================================
       DAY SUMMARY
    ========================================================= */

    .day-summary {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);

        margin-bottom: 13px;
        overflow: hidden;
    }

    .day-summary-main {
        display: flex;
        align-items: center;
        justify-content: space-between;

        padding: 13px 15px;
    }

    .summary-label {
        font-size: 12px;
        color: var(--text-secondary);
        margin-bottom: 3px;
    }

    .summary-date {
        font-size: 11px;
        color: var(--text-muted);
    }

    .summary-total {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--text);
    }

    /* =========================================================
       PAYMENT SUMMARY
    ========================================================= */

    .payment-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;

        padding: 9px 15px;

        border-top: 1px solid var(--border-light);
        background: #fcfcfd;
    }

    .payment-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        padding: 5px 8px;

        border: 1px solid #e9edf1;
        border-radius: 6px;

        background: #fff;
        color: var(--text-secondary);

        font-size: 11px;
    }

    .payment-chip strong {
        color: #4e5660;
        font-weight: 600;
    }

    /* =========================================================
       ORDERS
    ========================================================= */

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* =========================================================
       ORDER CARD
    ========================================================= */

    .order-card {
        background: #fff;

        border: 1px solid var(--border);
        border-radius: var(--radius);

        padding: 13px 14px;

        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    .order-card:hover {
        border-color: #dde2e8;
        box-shadow: 0 2px 10px rgba(40, 50, 65, .025);
    }

    /* =========================================================
       TOP
    ========================================================= */

    .order-top {
        width: 100%;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 12px;
        margin-bottom: 9px;
    }

    .order-number {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        color: #424951;
        font-size: 13px;
        font-weight: 700;

        min-width: 0;
    }

    .order-number-icon {
        color: #a7afb8;
        font-size: 12px;
    }

    .order-date {
        flex: 0 0 auto;

        color: var(--text-muted);
        font-size: 11px;

        text-align: right;
        white-space: nowrap;
    }

    /* =========================================================
       CUSTOMER ROW
    ========================================================= */

    .customer-row {
        width: 100%;

        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 12px;
    }

    .customer-block {
        flex: 1 1 auto;
        min-width: 0;
    }

    .customer-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;

        color: #343940;
        font-size: 13.5px;
        font-weight: 600;
    }

    .customer-phone {
        margin-top: 3px;

        color: var(--text-secondary);
        font-size: 11.5px;
    }

    .customer-payments {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;

        margin-top: 6px;
    }

    .order-point {
        flex: 0 0 auto;
        max-width: 48%;

        display: flex;
        align-items: center;
        justify-content: flex-end;

        gap: 5px;

        color: #8a929c;
        font-size: 10.5px;
        line-height: 1.3;

        text-align: right;
        white-space: nowrap;

        overflow: hidden;
        text-overflow: ellipsis;
    }

    .order-point i {
        color: #aeb6bf;
        font-size: 10px;
        flex: 0 0 auto;
    }

    /* =========================================================
       PAYMENT BADGE
    ========================================================= */

    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;

        padding: 4px 7px;

        border-radius: 6px;

        background: var(--green-light);
        color: var(--green);

        font-size: 10.5px;
        font-weight: 500;

        white-space: nowrap;
    }

    .payment-badge i {
        font-size: 9px;
    }

    .payment-badge.negative {
        background: var(--red-light);
        color: var(--red);
    }

    /* =========================================================
       ORDER BOTTOM
    ========================================================= */

    .order-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;

        margin-top: 12px;
        padding-top: 10px;

        border-top: 1px solid var(--border-light);
    }

    .order-finance {
        min-width: 0;
    }

    .discount-line {
        margin-bottom: 2px;

        color: #a4aab1;
        font-size: 10.5px;
    }

    .order-total {
        color: #30353b;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -0.015em;
    }

    /* =========================================================
       ACTIONS
    ========================================================= */

    .order-actions {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .action-btn {
        width: 25px;
        height: 25px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 7px;
        border: 1px solid var(--blue);

        background: #fff;
        color: var(--blue);

        text-decoration: none;
        font-size: 11px;

        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease;
    }

    .action-btn:not(.action-delete):hover {
        background: var(--blue-hover);
        border-color: var(--blue-hover);
        color: #fff;
    }

    .action-delete {
        background: var(--red-light);
        border-color: var(--red-border);
        color: var(--red);

        cursor: pointer;
    }

    .action-delete:hover {
        background: var(--red-hover);
        border-color: #efc5c8;
        color: #d6535c;
    }

    .delete-form {
        display: inline-flex;
        margin: 0;
    }

    /* =========================================================
       DETAILS
    ========================================================= */

    .details-btn {
        width: 100%;
        height: 33px;

        margin-top: 9px;

        border: 1px solid var(--blue);
        border-radius: 7px;

        background: var(--blue);
        color: #fff;

        font-family: inherit;
        font-size: 12px;
        font-weight: 500;

        cursor: pointer;

        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease;
    }

    .details-btn:hover {
        background: var(--blue-hover);
        border-color: var(--blue-hover);
        color: #fff;
    }

    /* =========================================================
       MODAL
    ========================================================= */

    .modal-content {
        border: none;
        border-radius: 0;

        background: var(--page-bg);

        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .modal-header {
        padding: 12px 16px;

        background: #fff;

        border-bottom: 1px solid var(--border);
    }

    .modal-title {
        color: #30353b;
        font-size: 14px;
        font-weight: 700;
    }

    .btn-close {
        opacity: .4;
        transform: scale(.85);
    }

    .btn-close:hover {
        opacity: .75;
    }

    .modal-body {
        padding: 12px 16px 25px;
    }

    /* =========================================================
       MODAL SECTION
    ========================================================= */

    .modal-section {
        background: #fff;

        border: 1px solid var(--border);
        border-radius: 8px;

        padding: 10px 12px;
        margin-bottom: 7px;
    }

    .modal-section-title {
        margin-bottom: 6px;

        color: #9299a2;
        font-size: 10px;
        font-weight: 600;

        text-transform: uppercase;
        letter-spacing: .02em;
    }

    /* =========================================================
       MODAL INFO
    ========================================================= */

    .info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        min-height: 27px;
        padding: 4px 0;

        border-bottom: 1px solid #f2f3f5;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        flex: 0 0 auto;

        color: #9ba2aa;
        font-size: 10.5px;
    }

    .info-value {
        min-width: 0;

        color: #3e444b;
        font-size: 11.5px;
        font-weight: 600;

        text-align: right;
        overflow-wrap: anywhere;
    }

    /* =========================================================
       INLINE CUSTOMER
    ========================================================= */

    .customer-info-inline {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;
        min-width: 0;
    }

    .customer-info-name {
        min-width: 0;

        color: #3e444b;
        font-size: 11.5px;
        font-weight: 600;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .customer-info-phone {
        flex: 0 0 auto;

        color: #737b85;
        font-size: 11px;

        white-space: nowrap;
    }

    /* =========================================================
       ORDER INFO INLINE
    ========================================================= */

    .order-info-inline {
        display: flex;
        align-items: center;
        justify-content: flex-end;

        gap: 14px;
        min-width: 0;
    }

    .order-info-value {
        color: #3e444b;
        font-size: 11.5px;
        font-weight: 600;

        white-space: nowrap;
    }

    .order-info-date {
        color: #737b85;
        font-size: 11px;

        white-space: nowrap;
    }

    /* =========================================================
       FINANCE INLINE
    ========================================================= */

    .finance-inline {
        display: flex;
        align-items: center;
        justify-content: flex-end;

        gap: 15px;
    }

    .finance-discount {
        color: var(--red);
        font-size: 11px;
        font-weight: 500;

        white-space: nowrap;
    }

    .finance-total {
        color: #30353b;
        font-size: 12px;
        font-weight: 700;

        white-space: nowrap;
    }

    /* =========================================================
       COMMENT
    ========================================================= */

    .comment-value {
        color: #555d66;
        font-size: 11.5px;
        font-weight: 500;

        text-align: right;
        white-space: pre-wrap;
    }

    /* =========================================================
       PAYMENTS MODAL
    ========================================================= */

    .modal-payments {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;

        gap: 5px;
    }

    .modal-payment {
        display: inline-flex;
        align-items: center;
        gap: 4px;

        padding: 4px 7px;

        border-radius: 5px;

        background: var(--green-light);
        color: var(--green);

        font-size: 10.5px;
        font-weight: 500;

        white-space: nowrap;
    }

    .modal-payment.negative {
        background: var(--red-light);
        color: var(--red);
    }

    .modal-payment i {
        font-size: 9px;
    }

    /* =========================================================
       MODAL PRODUCTS
    ========================================================= */

    .modal-item {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 12px;

        padding: 7px 0;

        border-bottom: 1px solid #f1f2f4;
    }

    .modal-item:last-child {
        border-bottom: none;
    }

    .item-left {
        min-width: 0;
        flex: 1 1 auto;
    }

    .item-main-line {
        display: flex;
        align-items: center;

        gap: 7px;
        min-width: 0;
    }

    .item-sku {
        min-width: 0;

        color: #41474e;
        font-size: 11.5px;
        font-weight: 600;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .item-batch-inline {
        flex: 0 0 auto;

        color: #a1a8b0;
        font-size: 10px;

        white-space: nowrap;
    }

    .item-qty {
        margin-top: 3px;

        color: #7c848d;
        font-size: 10.5px;
    }

    .item-sum {
        flex: 0 0 auto;

        color: #3e444b;
        font-size: 11.5px;
        font-weight: 700;

        white-space: nowrap;
    }

    /* =========================================================
       EMPTY
    ========================================================= */

    .orders-empty {
        padding: 36px 20px;

        text-align: center;

        background: #fff;

        border: 1px solid var(--border);
        border-radius: var(--radius);

        color: var(--text-muted);
        font-size: 12.5px;
    }

    .orders-empty i {
        color: #c0c6cd;
    }

    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 575px) {

        .container {
            max-width: 100%;

            padding-left: 12px;
            padding-right: 12px;
        }

        .orders-page {
            padding-top: 14px;
        }

        .orders-title {
            font-size: 18px;
        }

        .orders-subtitle {
            font-size: 10.5px;
        }

        .btn-create {
            height: 34px;

            padding: 0 11px;

            font-size: 12px;
        }

        .date-arrow {
            width: 34px;
            height: 34px;
            flex-basis: 34px;
        }

        .date-input {
            height: 34px;

            /*
             * iOS Safari не увеличивает страницу
             * при фокусе на input с font-size >= 16px.
             *
             * Остальной размер самого поля
             * остаётся прежним.
             */
            font-size: 16px !important;
            -webkit-text-size-adjust: 100%;
        }

        /*
         * Убираем автоматический zoom Safari
         * при нажатии на поиск.
         */
        #searchInput {
            font-size: 16px !important;
            -webkit-text-size-adjust: 100%;
        }

        .order-card {
            padding: 12px;
        }

        .order-total {
            font-size: 15px;
        }

        .order-point {
            max-width: 50%;
        }

        .modal-header {
            padding: 11px 13px;
        }

        .modal-body {
            padding: 9px 10px 20px;
        }

        .modal-section {
            padding: 9px 10px;
            margin-bottom: 6px;
        }

        .customer-info-inline {
            gap: 8px;
        }

        .customer-info-name {
            font-size: 11px;
        }

        .customer-info-phone {
            font-size: 10px;
        }

        .order-info-inline {
            gap: 9px;
        }

        .order-info-value,
        .order-info-date {
            font-size: 10.5px;
        }

        .finance-inline {
            gap: 10px;
        }

        .info-label {
            font-size: 10px;
        }

        .info-value {
            font-size: 11px;
        }

        .payment-summary {
            padding: 8px 12px;
        }

        .modal-payments {
            gap: 4px;
        }

        .modal-payment {
            padding: 4px 6px;
            font-size: 10px;
        }
    }
</style>

<div class="container orders-page">

    {{-- =====================================================
     HEADER
====================================================== --}}

    <div class="orders-header">

        <div>
            <h1 class="orders-title">
                Заказы
            </h1>
        </div>

        <a
            href="{{ route('admin.orders.create') }}"
            class="btn-create">

            <i class="bi bi-plus-lg"></i>

            Создать

        </a>

    </div>


    {{-- =====================================================
     DATE
====================================================== --}}

    <div class="orders-toolbar">

        <div class="date-picker">

            <a
                href="{{ route('admin.orders.seller', ['date' => $prevDate]) }}"
                class="date-arrow"
                title="Предыдущий день">

                <i class="bi bi-chevron-left"></i>

            </a>

            <form method="GET" style="flex:1">

                <input
                    type="date"
                    name="date"
                    value="{{ $selectedDate }}"
                    onchange="this.form.submit()"
                    class="date-input">

            </form>

            <a
                href="{{ route('admin.orders.seller', ['date' => $nextDate]) }}"
                class="date-arrow"
                title="Следующий день">

                <i class="bi bi-chevron-right"></i>

            </a>

        </div>

    </div>


    {{-- =====================================================
     SEARCH
====================================================== --}}

    <div class="search-wrapper">

        <i class="bi bi-search search-icon"></i>

        <input
            type="text"
            id="searchInput"
            class="form-control"
            placeholder="Поиск по заказам, имени, телефону или артикулу"
            autocomplete="off">

    </div>


    {{-- =====================================================
     DAY TOTAL
====================================================== --}}

    @foreach($orders as $order)

    @php

    $totalDaySum +=
    $order->items->sum(
    fn($i) =>
    ($i->price ?? 0) * $i->quantity
    )
    -
    ($order->discount ?? 0);

    @endphp

    @endforeach


    <div class="day-summary">

        <div class="day-summary-main">

            <div>

                <div class="summary-label">
                    Продажи за день
                </div>

                <div class="summary-date">
                    {{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}
                </div>

            </div>

            <div class="summary-total">
                {{ number_format($totalDaySum, 0, '.', ' ') }} ₸
            </div>

        </div>


        @if(!empty($paymentTotals))

        <div class="payment-summary">

            @foreach($paymentTotals as $method => $amount)

            @php
            $methodName = $methodNames[$method] ?? $method;
            @endphp

            <div class="payment-chip">

                <span>
                    {{ $methodName }}
                </span>

                <strong>
                    {{ number_format($amount, 0, '.', ' ') }} ₸
                </strong>

            </div>

            @endforeach

        </div>

        @endif

    </div>


    {{-- =====================================================
     ORDERS
====================================================== --}}

    <div
        id="mobileOrders"
        class="orders-list">

        @forelse($orders as $order)

        @php

        $orderSum =
        $order->items->sum(
        fn($i) =>
        ($i->price ?? 0) * $i->quantity
        );

        $finalSum =
        $orderSum -
        ($order->discount ?? 0);

        @endphp


        <div class="order-card">

            {{-- TOP --}}

            <div class="order-top">

                <div class="order-number">
                    #{{ $order->id }}
                </div>

                <div class="order-date">
                    {{ $order->order_date?->format('d.m.Y H:i') ?? '—' }}
                </div>

            </div>


            {{-- CUSTOMER --}}

            <div class="customer-row">

                <div class="customer-block">

                    <div class="customer-name">
                        {{ $order->name ?: 'Без имени' }}
                    </div>

                    <div class="customer-phone">
                        {{ $order->phone ?: 'Телефон не указан' }}
                    </div>


                    <div class="customer-payments">

                        @if($order->payments->count())

                        @foreach($order->payments as $payment)

                        @php

                        $methodName =
                        $methodNames[
                        $payment->payment_method
                        ]
                        ??
                        $payment->payment_method;

                        $isNegative =
                        $payment->amount < 0;

                            @endphp


                            <div
                            class="payment-badge {{ $isNegative ? 'negative' : '' }}">

                            {{ $methodName }}

                            {{ number_format(
                                        $payment->amount,
                                        0,
                                        '.',
                                        ' '
                                    ) }} ₸

                    </div>

                    @endforeach

                    @else

                    <div class="payment-badge negative">

                        <i class="bi bi-exclamation-circle"></i>

                        Оплата не указана

                    </div>

                    @endif

                </div>

            </div>


            <div class="order-point">

                {{ $order->pointOfSale->name ?? 'Точка не указана' }}

            </div>

        </div>


        {{-- BOTTOM --}}

        <div class="order-bottom">

            <div class="order-finance">

                @if(($order->discount ?? 0) > 0)

                <div class="discount-line">

                    Скидка:

                    {{ number_format(
                                $order->discount,
                                0,
                                '.',
                                ' '
                            ) }} ₸

                </div>

                @endif


                <div class="order-total">

                    {{ number_format(
                            $finalSum,
                            0,
                            '.',
                            ' '
                        ) }} ₸

                </div>

            </div>


            <div class="order-actions">

                {{-- EDIT --}}

                <a
                    href="{{ route('admin.orders.edit', $order->id) }}"
                    class="action-btn"
                    title="Редактировать">

                    <i class="bi bi-pencil"></i>

                </a>


                {{-- DELETE --}}

                <form
                    action="{{ route('admin.orders.destroy', $order->id) }}"
                    method="POST"
                    class="delete-form"
                    onsubmit="return confirm('Удалить заказ?')">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="action-btn action-delete"
                        title="Удалить">

                        <i class="bi bi-trash3"></i>

                    </button>

                </form>

            </div>

        </div>


        {{-- DETAILS --}}

        <button
            type="button"
            class="details-btn"
            data-bs-toggle="modal"
            data-bs-target="#orderModal{{ $order->id }}">

            Подробнее

        </button>

    </div>

    @empty

    <div class="orders-empty">

        <i
            class="bi bi-receipt"
            style="
                    font-size:24px;
                    display:block;
                    margin-bottom:8px;
                ">
        </i>

        За этот день заказов нет

    </div>

    @endforelse

</div>
```

</div>

{{-- =====================================================
NORMAL MODALS
====================================================== --}}

@foreach($orders as $order)

```
@php

$orderSum =
$order->items->sum(
fn($i) =>
($i->price ?? 0) * $i->quantity
);

$finalSum =
$orderSum -
($order->discount ?? 0);

@endphp


<div
    class="modal fade"
    id="orderModal{{ $order->id }}"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-fullscreen">

        <div class="modal-content">


            {{-- MODAL HEADER --}}

            <div class="modal-header">

                <h6 class="modal-title">
                    Заказ #{{ $order->id }}
                </h6>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">


                {{-- CUSTOMER --}}

                <div class="modal-section">

                    <div class="modal-section-title">
                        Покупатель
                    </div>


                    <div class="info-row">

                        <div class="customer-info-inline">

                            <div class="customer-info-name">
                                {{ $order->name ?: 'Без имени' }}
                            </div>

                            <div class="customer-info-phone">
                                {{ $order->phone ?: 'Телефон не указан' }}
                            </div>

                        </div>

                    </div>

                </div>


                {{-- ORDER INFO --}}

                <div class="modal-section">

                    <div class="modal-section-title">
                        Информация о заказе
                    </div>


                    <div class="info-row">

                        <div class="info-label">
                            Точка продаж
                        </div>

                        <div class="order-info-inline">

                            <div class="order-info-value">

                                <i
                                    class="bi bi-shop"
                                    style="color:#aeb6bf; margin-right:4px;">
                                </i>

                                {{ $order->pointOfSale->name ?? '—' }}

                            </div>


                            <div class="order-info-date">

                                {{ $order->order_date?->format('d.m.Y H:i') ?? '—' }}

                            </div>

                        </div>

                    </div>


                    @if($order->comment)

                    <div class="info-row">

                        <div class="info-label">
                            Комментарий
                        </div>

                        <div class="comment-value">
                            {{ $order->comment }}
                        </div>

                    </div>

                    @endif

                </div>


                {{-- FINANCE --}}

                <div class="modal-section">

                    <div class="modal-section-title">
                        Оплата
                    </div>


                    <div class="info-row">

                        <div class="info-label">
                            Сумма
                        </div>

                        <div class="finance-inline">

                            @if(($order->discount ?? 0) > 0)

                            <div class="finance-discount">

                                Скидка:

                                {{ number_format(
                                        $order->discount,
                                        0,
                                        '.',
                                        ' '
                                    ) }} ₸

                            </div>

                            @endif


                            <div class="finance-total">

                                {{ number_format(
                                    $finalSum,
                                    0,
                                    '.',
                                    ' '
                                ) }} ₸

                            </div>

                        </div>

                    </div>


                    <div class="info-row">

                        <div class="info-label">
                            Оплата
                        </div>


                        <div class="modal-payments">

                            @if($order->payments->count())

                            @foreach($order->payments as $payment)

                            @php

                            $methodName =
                            $methodNames[
                            $payment->payment_method
                            ]
                            ??
                            $payment->payment_method;

                            $isNegative =
                            $payment->amount < 0;

                                @endphp


                                <div
                                class="modal-payment {{ $isNegative ? 'negative' : '' }}">

                                {{ $methodName }}

                                {{ number_format(
                                            $payment->amount,
                                            0,
                                            '.',
                                            ' '
                                        ) }} ₸

                        </div>

                        @endforeach

                        @else

                        <span style="color:var(--red); font-size:11px;">
                            Не указана
                        </span>

                        @endif

                    </div>

                </div>

            </div>


            {{-- PRODUCTS --}}

            <div class="modal-section">

                <div class="modal-section-title">
                    Товары
                </div>


                @forelse($order->items as $item)

                <div class="modal-item">

                    <div class="item-left">

                        <div class="item-main-line">

                            <div class="item-sku">
                                {{ $item->variant->sku ?? '—' }}
                            </div>

                            <div class="item-batch-inline">

                                Партия:
                                {{ $item->batch_code ?? '—' }}

                            </div>

                        </div>


                        <div class="item-qty">

                            {{ $item->quantity }}

                            ×

                            {{ number_format(
                                        $item->price ?? 0,
                                        0,
                                        '.',
                                        ' '
                                    ) }} ₸

                        </div>

                    </div>


                    <div class="item-sum">

                        {{ number_format(
                                    ($item->price ?? 0) *
                                    $item->quantity,
                                    0,
                                    '.',
                                    ' '
                                ) }} ₸

                    </div>

                </div>

                @empty

                <div
                    style="
                                color:var(--text-muted);
                                font-size:11px;
                                padding:7px 0;
                            ">

                    Товаров нет

                </div>

                @endforelse

            </div>

        </div>

    </div>

</div>

</div>
```

@endforeach

{{-- =========================================================
SEARCH JAVASCRIPT
========================================================= --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const search =
            document.getElementById('searchInput');

        const container =
            document.getElementById('mobileOrders');

        if (!search || !container) {
            return;
        }

        let timer = null;


        /* =====================================================
           ESCAPE
        ===================================================== */

        function escapeHtml(value) {

            if (value === null || value === undefined) {
                return '';
            }

            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

        }


        /* =====================================================
           MONEY
        ===================================================== */

        function formatMoney(value) {

            const number =
                Number(value) || 0;

            return number.toLocaleString('ru-RU');

        }


        /* =====================================================
           DATE
        ===================================================== */

        function formatDate(value) {

            if (!value) {
                return '—';
            }

            let date = String(value)
                .replace('T', ' ')
                .trim();

            const match = date.match(
                /^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{2}):(\d{2})(?::\d{2})?)?/
            );

            if (!match) {
                return '—';
            }

            const year = match[1];
            const month = match[2];
            const day = match[3];

            const hours =
                match[4] || '00';

            const minutes =
                match[5] || '00';

            return `${day}.${month}.${year} ${hours}:${minutes}`;

        }


        /* =====================================================
           PAYMENTS
        ===================================================== */

        function buildPayments(payments) {

            if (!Array.isArray(payments) || payments.length === 0) {

                return `
                <div class="payment-badge negative">

                    <i class="bi bi-exclamation-circle"></i>

                    Оплата не указана

                </div>
            `;

            }


            const paymentNames = {

                cash: 'Наличные',
                qr: 'QR',
                transfer: 'Перевод',
                card: 'Карта'

            };


            return payments.map(function(payment) {

                const methodName =
                    paymentNames[payment.payment_method] ||
                    payment.payment_method ||
                    'Оплата';

                const amount =
                    Number(payment.amount) || 0;

                const negative =
                    amount < 0;


                return `
                <div class="payment-badge ${negative ? 'negative' : ''}">

                    ${escapeHtml(methodName)}

                    ${formatMoney(amount)} ₸

                </div>
            `;

            }).join('');

        }


        /* =====================================================
           SEARCH ITEMS
        ===================================================== */

        function buildItems(items) {

            if (!Array.isArray(items) || items.length === 0) {

                return `
                <div
                    style="
                        padding:7px 0;
                        color:var(--text-muted);
                        font-size:11px;
                    "
                >
                    Товаров нет
                </div>
            `;

            }


            return items.map(function(item) {

                const sku =
                    item.variant &&
                    item.variant.sku ?
                    item.variant.sku :
                    '—';


                const quantity =
                    Number(item.quantity) || 0;


                const price =
                    Number(item.price) || 0;


                const itemTotal =
                    quantity * price;


                const batch =
                    item.batch_code ??
                    '—';


                return `
                <div class="modal-item">

                    <div class="item-left">

                        <div class="item-main-line">

                            <div class="item-sku">

                                ${escapeHtml(sku)}

                            </div>


                            <div class="item-batch-inline">

                                Партия:
                                ${escapeHtml(batch)}

                            </div>

                        </div>


                        <div class="item-qty">

                            ${quantity}

                            ×

                            ${formatMoney(price)} ₸

                        </div>

                    </div>


                    <div class="item-sum">

                        ${formatMoney(itemTotal)} ₸

                    </div>

                </div>
            `;

            }).join('');

        }


        /* =====================================================
           SEARCH CARD
        ===================================================== */

        function buildSearchCard(order) {

            let orderSum = 0;


            if (Array.isArray(order.items)) {

                orderSum =
                    order.items.reduce(
                        function(sum, item) {

                            return sum +
                                (
                                    (Number(item.price) || 0) *
                                    (Number(item.quantity) || 0)
                                );

                        },
                        0
                    );

            }


            const discount =
                Number(order.discount) || 0;


            const finalSum =
                orderSum - discount;


            const rawDate =
                order.order_date ||
                order.created_at ||
                '';


            const date =
                formatDate(rawDate);


            const customerName =
                order.name ?
                escapeHtml(order.name) :
                'Без имени';


            const customerPhone =
                order.phone ?
                escapeHtml(order.phone) :
                'Телефон не указан';


            const pointOfSale =
                order.point_of_sale &&
                order.point_of_sale.name ?
                escapeHtml(order.point_of_sale.name) :
                'Точка не указана';


            const paymentsHtml =
                buildPayments(order.payments);


            return `
            <div class="order-card">

                <div class="order-top">

                    <div class="order-number">

                        #${escapeHtml(order.id)}

                    </div>


                    <div class="order-date">

                        ${escapeHtml(date)}

                    </div>

                </div>


                <div class="customer-row">

                    <div class="customer-block">

                        <div class="customer-name">

                            ${customerName}

                        </div>


                        <div class="customer-phone">

                            ${customerPhone}

                        </div>


                        <div class="customer-payments">

                            ${paymentsHtml}

                        </div>

                    </div>


                    <div class="order-point">

                        ${pointOfSale}

                    </div>

                </div>


                <div class="order-bottom">

                    <div class="order-finance">

                        ${
                            discount > 0
                                ? `
                                    <div class="discount-line">

                                        Скидка:
                                        ${formatMoney(discount)} ₸

                                    </div>
                                  `
                                : ''
                        }


                        <div class="order-total">

                            ${formatMoney(finalSum)} ₸

                        </div>

                    </div>


                    <div class="order-actions">

                        <a
                            href="/admin/orders/${encodeURIComponent(order.id)}/edit"
                            class="action-btn"
                            title="Редактировать">

                            <i class="bi bi-pencil"></i>

                        </a>


                        <form
                            action="/admin/orders/${encodeURIComponent(order.id)}"
                            method="POST"
                            class="delete-form"
                            onsubmit="return confirm('Удалить заказ?')">

                            <input
                                type="hidden"
                                name="_token"
                                value="{{ csrf_token() }}">


                            <input
                                type="hidden"
                                name="_method"
                                value="DELETE">


                            <button
                                type="submit"
                                class="action-btn action-delete"
                                title="Удалить">

                                <i class="bi bi-trash3"></i>

                            </button>

                        </form>

                    </div>

                </div>


                <button
                    type="button"
                    class="details-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#searchOrderModal${escapeHtml(order.id)}">

                    Подробнее

                </button>

            </div>
        `;

        }


        /* =====================================================
           SEARCH MODAL
        ===================================================== */

        function buildSearchModal(order) {

            const orderSum =
                Array.isArray(order.items) ?
                order.items.reduce(
                    function(sum, item) {

                        return sum +
                            (
                                (Number(item.price) || 0) *
                                (Number(item.quantity) || 0)
                            );

                    },
                    0
                ) :
                0;


            const discount =
                Number(order.discount) || 0;


            const finalSum =
                orderSum - discount;


            const customerName =
                order.name ?
                escapeHtml(order.name) :
                '—';


            const customerPhone =
                order.phone ?
                escapeHtml(order.phone) :
                '—';


            const pointOfSale =
                order.point_of_sale &&
                order.point_of_sale.name ?
                escapeHtml(order.point_of_sale.name) :
                '—';


            const date =
                formatDate(
                    order.order_date ||
                    order.created_at ||
                    ''
                );


            const comment =
                order.comment ?
                escapeHtml(order.comment) :
                '—';


            const payments =
                Array.isArray(order.payments) ?
                order.payments : [];


            let paymentsModalHtml = '';


            if (payments.length) {

                const paymentNames = {

                    cash: 'Наличные',
                    qr: 'QR',
                    transfer: 'Перевод',
                    card: 'Карта'

                };


                paymentsModalHtml =
                    payments.map(function(payment) {

                        const methodName =
                            paymentNames[payment.payment_method] ||
                            payment.payment_method ||
                            'Оплата';


                        const amount =
                            Number(payment.amount) || 0;


                        const negative =
                            amount < 0;


                        return `
                        <div class="modal-payment ${negative ? 'negative' : ''}">

                            ${escapeHtml(methodName)}

                            ${formatMoney(amount)} ₸

                        </div>
                    `;

                    }).join('');

            } else {

                paymentsModalHtml = `
                <span style="color:var(--red); font-size:11px;">
                    Не указана
                </span>
            `;

            }


            const itemsHtml =
                buildItems(order.items);


            return `
            <div
                class="modal fade search-order-modal"
                id="searchOrderModal${escapeHtml(order.id)}"
                tabindex="-1"
                aria-hidden="true">

                <div class="modal-dialog modal-fullscreen">

                    <div class="modal-content">


                        <div class="modal-header">

                            <h6 class="modal-title">

                                Заказ #${escapeHtml(order.id)}

                            </h6>


                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>

                        </div>


                        <div class="modal-body">


                            <div class="modal-section">

                                <div class="modal-section-title">
                                    Покупатель
                                </div>


                                <div class="info-row">

                                    <div class="customer-info-inline">

                                        <div class="customer-info-name">

                                            ${customerName}

                                        </div>


                                        <div class="customer-info-phone">

                                            ${customerPhone}

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div class="modal-section">

                                <div class="modal-section-title">
                                    Информация о заказе
                                </div>


                                <div class="info-row">

                                    <div class="info-label">
                                        Точка продаж
                                    </div>


                                    <div class="order-info-inline">

                                        <div class="order-info-value">

                                            <i
                                                class="bi bi-shop"
                                                style="color:#aeb6bf; margin-right:4px;">
                                            </i>

                                            ${pointOfSale}

                                        </div>


                                        <div class="order-info-date">

                                            ${escapeHtml(date)}

                                        </div>

                                    </div>

                                </div>


                                ${
                                    comment !== '—'
                                        ? `
                                            <div class="info-row">

                                                <div class="info-label">
                                                    Комментарий
                                                </div>

                                                <div class="comment-value">

                                                    ${comment}

                                                </div>

                                            </div>
                                          `
                                        : ''
                                }

                            </div>


                            <div class="modal-section">

                                <div class="modal-section-title">
                                    Оплата
                                </div>


                                <div class="info-row">

                                    <div class="info-label">
                                        Сумма
                                    </div>


                                    <div class="finance-inline">

                                        ${
                                            discount > 0
                                                ? `
                                                    <div class="finance-discount">

                                                        Скидка:
                                                        ${formatMoney(discount)} ₸

                                                    </div>
                                                  `
                                                : ''
                                        }


                                        <div class="finance-total">

                                            ${formatMoney(finalSum)} ₸

                                        </div>

                                    </div>

                                </div>


                                <div class="info-row">

                                    <div class="info-label">
                                        Оплата
                                    </div>


                                    <div class="modal-payments">

                                        ${paymentsModalHtml}

                                    </div>

                                </div>

                            </div>


                            <div class="modal-section">

                                <div class="modal-section-title">
                                    Товары
                                </div>


                                ${itemsHtml}

                            </div>


                        </div>

                    </div>

                </div>

            </div>
        `;

        }


        /* =====================================================
           SEARCH EVENT
        ===================================================== */

        search.addEventListener('input', function() {

            clearTimeout(timer);


            timer = setTimeout(function() {

                const q =
                    search.value.trim();


                /* Очистка поиска */

                if (!q.length) {

                    location.reload();

                    return;

                }


                fetch(
                        `/admin/orders/search?q=${encodeURIComponent(q)}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    )

                    .then(function(response) {

                        if (!response.ok) {

                            throw new Error(
                                'Ошибка поиска: ' +
                                response.status
                            );

                        }

                        return response.json();

                    })


                    .then(function(data) {


                        /* Удаляем старые поисковые модалки */

                        document
                            .querySelectorAll('.search-order-modal')
                            .forEach(function(modal) {

                                modal.remove();

                            });


                        /* Очищаем список */

                        container.innerHTML = '';


                        /* Нет результатов */

                        if (
                            !Array.isArray(data) ||
                            data.length === 0
                        ) {

                            container.innerHTML = `

                        <div class="orders-empty">

                            <i
                                class="bi bi-search"
                                style="
                                    font-size:22px;
                                    display:block;
                                    margin-bottom:8px;
                                ">
                            </i>

                            Заказы не найдены

                        </div>

                    `;

                            return;

                        }


                        /* Создаём результаты */

                        data.forEach(function(order) {

                            container.insertAdjacentHTML(
                                'beforeend',
                                buildSearchCard(order)
                            );


                            document.body.insertAdjacentHTML(
                                'beforeend',
                                buildSearchModal(order)
                            );

                        });

                    })


                    .catch(function(error) {

                        console.error(
                            'Search error:',
                            error
                        );


                        container.innerHTML = `

                    <div class="orders-empty">

                        <i
                            class="bi bi-exclamation-circle"
                            style="
                                font-size:22px;
                                display:block;
                                margin-bottom:8px;
                            ">
                        </i>

                        Ошибка поиска

                    </div>

                `;

                    });


            }, 300);

        });

    });
</script>

@endsection