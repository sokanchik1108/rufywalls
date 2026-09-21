@extends('layouts.app')

@section('title', 'Создать заказ')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">

<style>
    :root {
        --bg: #ffffff;
        --group-bg: #f4f4f6;
        --surface: #ffffff;
        --ink: #1c1c1e;
        --ink-soft: #8e8e93;
        --ink-faint: #c2c2c7;
        --border: #e2e2e6;
        --label-blue: #3ea0e6;
        --accent-blue: #4fb1e8;
        --accent-blue-hover: #33a0de;
        --check-blue: #0a84ff;
        --danger: #ff3b30;
        --danger-soft: #ffe8e6;
        --success: #34c759;
        --radius-sm: 8px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: var(--bg);
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        color: var(--ink);
        margin: 0;
        padding: 0;
        font-size: 14px;
        -webkit-font-smoothing: antialiased;

        /* Защита от автоматического изменения масштаба текста iOS */
        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }

    .container {
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
        padding: 0 0 30px;
    }

    /* =========================================================
       TOP
    ========================================================= */

    #orderControls {
        position: sticky;
        top: 0;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 52px;
        padding: 8px 16px;
        margin: 0;
        background: rgba(255, 255, 255, .96);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--border);
    }

    #cancelOrder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--group-bg);
        color: var(--ink-soft);
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: .12s ease;
    }

    #cancelOrder:hover {
        background: #e8e8ec;
        color: var(--ink);
    }

    #finishOrder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        padding: 0;
        background: var(--check-blue);
        color: #fff;
        border: none;
        border-radius: 50%;
        font-size: 17px;
        cursor: pointer;
        transition: .12s ease;
        box-shadow: 0 2px 6px rgba(10, 132, 255, .35);
    }

    #finishOrder:hover {
        background: #0074e6;
    }

    /* =========================================================
       SECTIONS
    ========================================================= */

    .section {
        margin: 0;
        padding: 10px 16px 12px;
        background: var(--surface);
        border-bottom: 8px solid var(--group-bg);
    }

    .section-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 2px -16px 8px;
        padding: 7px 16px;
        background: var(--group-bg);
        color: var(--ink-soft);
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .section-label .count {
        color: var(--ink-faint);
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0;
    }

    /* =========================================================
       FIELDS
    ========================================================= */

    .field {
        padding: 9px 0;
        border-bottom: 1px solid var(--border);
    }

    .field:last-child {
        border-bottom: none;
    }

    .field-row {
        display: flex;
        gap: 16px;
    }

    .field-row .field {
        flex: 1;
        min-width: 0;
    }

    label.field-label {
        display: block;
        margin-bottom: 4px;
        color: var(--label-blue);
        font-size: 12.5px;
        font-weight: 600;
    }

    input.form-control,
    select.form-select {
        display: block;
        width: 100%;
        height: 26px;
        padding: 0;
        border: none;
        border-radius: 0;
        background: transparent;
        color: var(--ink);
        font-family: inherit;
        font-size: 15px;
        font-weight: 600;
        line-height: 26px;
        outline: none;

        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }

    input.form-control::placeholder {
        color: var(--ink-faint);
        font-weight: 500;
    }

    select.form-select {
        padding-right: 22px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='9' viewBox='0 0 14 9' fill='none'%3E%3Cpath d='M1 1L7 7L13 1' stroke='%238e8e93' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 2px center;
        appearance: none;
        -webkit-appearance: none;
    }

    input:focus,
    select:focus {
        outline: none;
    }

    .field:has(input:focus),
    .field:has(select:focus) {
        border-bottom-color: var(--check-blue);
    }

    input[type="datetime-local"],
    input[type="date"] {
        -webkit-appearance: none;
        appearance: none;
        height: 26px;
        line-height: 26px;
    }

    input[type="datetime-local"]::-webkit-date-and-time-value,
    input[type="date"]::-webkit-date-and-time-value {
        text-align: left;
        margin: 0;
        padding: 0;
    }

    input[type="datetime-local"]::-webkit-datetime-edit,
    input[type="date"]::-webkit-datetime-edit {
        padding: 0;
    }

    input[type="datetime-local"]::-webkit-datetime-edit-fields-wrapper,
    input[type="date"]::-webkit-datetime-edit-fields-wrapper {
        padding: 0;
    }

    input[type="datetime-local"]::-webkit-calendar-picker-indicator,
    input[type="date"]::-webkit-calendar-picker-indicator {
        margin-left: 6px;
        padding: 0;
        width: 15px;
        height: 15px;
        opacity: .55;
        cursor: pointer;
    }

    /* =========================================================
       ITEMS
    ========================================================= */

    #itemsWrapper:empty {
        display: block;
        padding: 16px 0 4px;
        text-align: center;
        color: var(--ink-faint);
        font-size: 13px;
    }

    #itemsWrapper:empty::before {
        content: "Пока нет добавленных позиций";
    }

    .order-item {
        position: relative;
        padding: 10px 0 9px;
        border-bottom: 1px solid var(--border);
        background: transparent;
    }

    #itemsWrapper .order-item:last-child {
        border-bottom: none;
    }

    .order-item .item-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        padding-right: 24px;
    }

    .order-item .item-title {
        font-size: 14.5px;
        font-weight: 700;
        line-height: 1.35;
    }

    .order-item .item-title .meta {
        color: var(--ink-faint);
        font-weight: 500;
    }

    .order-item .warehouse {
        display: block;
        margin-top: 2px;
        color: var(--ink-soft);
        font-size: 11.5px;
    }

    .return-label {
        display: inline-block;
        margin-left: 4px;
        padding: 1px 6px;
        color: var(--danger);
        background: var(--danger-soft);
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 700;
        vertical-align: middle;
    }

    .remove-item {
        position: absolute;
        right: 0;
        top: 8px;
        width: 24px;
        height: 24px;
        border: none;
        background: var(--group-bg);
        color: var(--ink-soft);
        font-size: 17px;
        line-height: 1;
        cursor: pointer;
        border-radius: 50%;
        transition: .12s ease;
    }

    .remove-item:hover {
        background: var(--danger-soft);
        color: var(--danger);
    }

    .order-item-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 8px;
        font-size: 13px;
    }

    .qty-times {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--ink-soft);
        font-weight: 600;
    }

    .price-field {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .price-input {
        width: 92px !important;
        height: 30px !important;
        padding: 0 22px 0 0 !important;
        border: none;
        border-bottom: 1px solid var(--border);
        border-radius: 0;
        background: transparent;
        font-family: inherit;
        font-size: 14px !important;
        font-weight: 700;
        color: var(--ink);
        text-align: right;

        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }

    .price-input:focus {
        outline: none;
        border-bottom-color: var(--check-blue);
    }

    .price-field::after {
        content: "тг";
        position: absolute;
        right: 0;
        color: var(--ink-faint);
        font-size: 10.5px;
        pointer-events: none;
    }

    .item-total {
        font-weight: 700;
        color: var(--ink);
    }

    #openProductModal {
        width: 100%;
        height: 42px;
        margin: 4px 0 2px;
        padding: 0 12px;
        background: var(--accent-blue);
        color: #fff;
        border: 0;
        border-radius: var(--radius-sm);
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: .12s ease;
    }

    #openProductModal:hover {
        background: var(--accent-blue-hover);
    }

    /* =========================================================
       TOTAL
    ========================================================= */

    .total-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .discount-part {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .discount-part label {
        margin: 0;
        color: var(--label-blue);
        font-size: 12.5px;
        font-weight: 600;
        white-space: nowrap;
    }

    #discountInput {
        width: 90px !important;
        height: 30px !important;
        text-align: right;
        margin: 0;
        border-bottom: 1px solid var(--border) !important;
    }

    .total-part {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-left: auto;
    }

    .total-label {
        color: var(--ink-soft);
        font-size: 15px;
        font-weight: 700;
        white-space: nowrap;
    }

    #orderTotal {
        color: var(--ink);
        font-size: 22px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* =========================================================
       PAYMENTS
    ========================================================= */

    .payment-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
    }

    #paymentsWrapper .payment-row:last-child {
        border-bottom: none;
    }

    .payment-row select {
        flex: 1;
        min-width: 0;
        border-bottom: 1px solid var(--border) !important;
    }

    .payment-amount {
        width: 120px !important;
        flex: none !important;
        text-align: right;
        font-weight: 700;
        border-bottom: 1px solid var(--border) !important;

        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }

    .remove-payment {
        width: 26px;
        height: 26px;
        flex: none;
        border: none;
        background: transparent;
        color: var(--ink-faint);
        font-size: 18px;
        cursor: pointer;
        border-radius: 50%;
    }

    .remove-payment:hover {
        background: var(--danger-soft);
        color: var(--danger);
    }

    #addPayment {
        width: 100%;
        height: 36px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--label-blue);
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 6px;
    }

    #addPayment:hover {
        background: var(--group-bg);
    }

    .payment-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border);
    }

    .payment-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 5px;
        margin: 0;
        font-size: 11.5px;
    }

    .payment-summary-row .label {
        color: var(--ink-soft);
    }

    .payment-summary-row .value {
        font-weight: 700;
        white-space: nowrap;
    }

    #paymentRemaining {
        font-weight: 800;
    }

    .payment-ok {
        color: var(--success);
    }

    .payment-error {
        color: var(--danger);
    }

    /* =========================================================
       MODALS
    ========================================================= */

    .modal-fullscreen {
        max-width: 100%;
        width: 100%;
        height: 100%;
        margin: 0;
    }

    .modal-fullscreen .modal-content {
        height: 100%;
        border-radius: 0;
        border: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        background: var(--group-bg);
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .modal-header {
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        padding: 13px 16px;
    }

    .modal-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--ink);
    }

    .btn-close {
        opacity: .5;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 14px 15px 18px;
        overflow-y: auto;
    }

    #modalSkuInput {
        margin-bottom: 10px;
        height: 42px;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-sm) !important;
        padding: 0 10px !important;
        background: var(--surface) !important;

        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }

    .batch-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 12px 14px;
        margin-bottom: 6px;
        background: var(--surface);
        border: none;
        border-bottom: 1px solid var(--border);
        border-radius: 0;
        cursor: pointer;
        font-size: 13px;
        transition: .12s ease;
    }

    .batch-card:hover {
        background: #eef8fd;
    }

    .batch-card .batch-info {
        flex: 1;
        min-width: 0;
    }

    .batch-card .batch-sku {
        font-weight: 700;
        line-height: 1.35;
    }

    .batch-card .batch-code {
        margin-top: 2px;
        color: var(--ink-soft);
        font-size: 11px;
    }

    .batch-card .batch-qty {
        flex: none;
        color: var(--label-blue);
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    #batchDetailTable {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
        background: var(--surface);
    }

    #batchDetailTable thead th {
        padding: 8px 8px 6px;
        border-bottom: 1px solid var(--border);
        color: var(--ink-soft);
        font-size: 10.5px;
        font-weight: 700;
        text-align: left;
    }

    #batchDetailTable tbody td {
        padding: 10px 8px;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        vertical-align: middle;
    }

    #batchDetailTable tbody tr:last-child td {
        border-bottom: none;
    }

    .qty-input {
        width: 100%;
        font-size: 14px;
        padding: 6px 7px;
        border-radius: 6px;
        border: 1px solid var(--border);
        font-weight: 700;
        font-family: inherit;
        background: var(--group-bg);

        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }

    .qty-input:focus {
        outline: none;
        border-color: var(--check-blue);
    }

    #addBatchToOrder {
        width: 100%;
        background: var(--check-blue);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        padding: 12px;
    }

    #addBatchToOrder:hover {
        background: #0074e6;
    }

    /* =========================================================
       AUTOCOMPLETE
    ========================================================= */

    .ui-autocomplete {
        border: 1px solid var(--border) !important;
        border-radius: 10px !important;
        box-shadow: 0 8px 24px rgba(20, 22, 26, .12) !important;
        padding: 5px !important;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        z-index: 3000 !important;
    }

    .ui-menu-item-wrapper {
        border-radius: 6px !important;
        padding: 8px 9px !important;
        border: none !important;
        font-size: 13px;
    }

    .ui-menu-item-wrapper.ui-state-active {
        background: #eef8fd !important;
        color: var(--check-blue) !important;
        border: none !important;
        margin: 0 !important;
    }

    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 600px) {

        .section {
            padding: 9px 14px 11px;
        }

        .section-label {
            margin: 2px -14px 8px;
            padding: 7px 14px;
        }

        .field-row {
            gap: 12px;
        }

        .price-input {
            width: 82px !important;
        }

        .discount-part {
            gap: 6px;
        }

        #discountInput {
            width: 78px !important;
        }

        .total-label {
            font-size: 13px;
        }

        #orderTotal {
            font-size: 19px;
        }

        .payment-amount {
            width: 106px !important;
        }

        .payment-summary {
            gap: 5px;
        }

        .payment-summary-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
        }

        .payment-summary-row .value {
            font-size: 11px;
        }

        /*
         * =====================================================
         * FIX MOBILE INPUT ZOOM
         * =====================================================
         *
         * iOS Safari увеличивает страницу, если input имеет
         * маленький размер шрифта.
         *
         * Остальной дизайн и размеры текста НЕ меняем.
         */

        input,
        select,
        textarea,
        button {
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        input.form-control,
        select.form-select,
        input[type="datetime-local"],
        input[type="date"],
        input[type="tel"],
        input[type="text"],
        input[type="number"],
        .price-input,
        .payment-amount,
        #discountInput,
        #modalSkuInput,
        .qty-input {
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        /*
         * Оставляем исходные размеры шрифтов полей.
         */

        input.form-control,
        select.form-select {
            font-size: 15px;
        }

        .price-input {
            font-size: 14px !important;
        }

        .payment-amount {
            font-size: 15px !important;
        }

        #discountInput {
            font-size: 15px !important;
        }

        #modalSkuInput {
            font-size: 15px !important;
        }

        .qty-input {
            font-size: 14px !important;
        }
    }

    @media (max-width: 400px) {

        .total-label {
            display: none;
        }

        #discountInput {
            width: 72px !important;
        }

        #orderTotal {
            font-size: 18px;
        }

        .payment-amount {
            width: 96px !important;
        }
    }
</style>

<div class="container">

    <form
        method="POST"
        action="{{ route('admin.orders.store') }}"
        id="orderForm">

        @csrf

        {{-- =====================================================
             ВЕРХНЯЯ ПАНЕЛЬ
        ====================================================== --}}

        <div id="orderControls">

            <span id="cancelOrder">
                &times;
            </span>

            Создание заказа

            <button
                type="submit"
                id="finishOrder">

                <svg
                    width="17"
                    height="13"
                    viewBox="0 0 17 13"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg">

                    <path
                        d="M1 6.5L6 11.5L16 1.5"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round" />

                </svg>

            </button>

        </div>

        {{-- =====================================================
             ДАТА + ТОЧКА ПРОДАЖ
        ====================================================== --}}

        <div class="section">

            <div class="field-row">

                <div class="field">

                    <label class="field-label">
                        Дата и время
                    </label>

                    <input
                        type="datetime-local"
                        name="order_date"
                        class="form-control"
                        value="{{ old('order_date', now('Asia/Almaty')->format('Y-m-d\TH\:i')) }}"
                        required>

                </div>

                <div class="field">

                    <label class="field-label">
                        Точка продаж
                    </label>

                    <select
                        name="point_of_sale_id"
                        id="pointOfSaleSelect"
                        class="form-select"
                        required>

                        <option value="">
                            Выберите точку
                        </option>

                        @foreach($pointsOfSale as $point)

                        <option
                            value="{{ $point->id }}"
                            {{ (int) old(
                                    'point_of_sale_id',
                                    $defaultPointOfSaleId ?? null
                                ) === (int) $point->id ? 'selected' : '' }}>

                            {{ $point->name }}

                        </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </div>

        {{-- =====================================================
             ОСНОВНАЯ ИНФОРМАЦИЯ
        ====================================================== --}}

        <div class="section">

            <div class="section-label">
                Покупатель
            </div>

            {{-- Имя клиента --}}

            <div class="field">

                <label class="field-label">
                    Имя клиента
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}"
                    placeholder="Введите имя клиента"
                    required>

            </div>

            {{-- Телефон --}}

            <div class="field">

                <label class="field-label">
                    Телефон
                </label>

                <input
                    type="tel"
                    name="phone"
                    class="form-control"
                    value="{{ old('phone') }}"
                    placeholder="+7 700 000 00 00"
                    autocomplete="tel"
                    required>

            </div>

            {{-- Склад --}}

            <div class="field">

                <label class="field-label">
                    Склад
                </label>

                <select
                    id="warehouseSelect"
                    class="form-select">

                    <option value="">
                        Все склады
                    </option>

                    @foreach($warehouses as $warehouse)

                    <option value="{{ $warehouse->id }}">
                        {{ $warehouse->name }}
                    </option>

                    @endforeach

                </select>

            </div>

        </div>

        {{-- =====================================================
             ПОЗИЦИИ
        ====================================================== --}}

        <div class="section">

            <div class="section-label">

                <span>
                    Позиции
                </span>

                <span
                    class="count"
                    id="positionsCount">

                    (0)

                </span>

            </div>

            <div id="itemsWrapper"></div>

            <button
                type="button"
                id="openProductModal">

                <svg
                    width="14"
                    height="14"
                    viewBox="0 0 16 16"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg">

                    <path
                        d="M8 2.5V13.5M2.5 8H13.5"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round" />

                </svg>

                Добавить позицию

            </button>

        </div>

        {{-- =====================================================
             ИТОГ
        ====================================================== --}}

        <div class="section">

            <div class="total-line">

                <div class="discount-part">

                    <label
                        class="field-label"
                        for="discountInput">

                        Скидка

                    </label>

                    <input
                        type="text"
                        id="discountInput"
                        name="discount"
                        class="form-control"
                        value="{{ old('discount', 0) }}"
                        inputmode="numeric"
                        autocomplete="off">

                </div>

                <div class="total-part">

                    <span class="total-label">
                        Итого
                    </span>

                    <div id="orderTotal">
                        0 тг
                    </div>

                </div>

            </div>

        </div>

        {{-- =====================================================
             ОПЛАТА
        ====================================================== --}}

        <div class="section">

            <div class="section-label">
                Оплата
            </div>

            <div id="paymentsWrapper">

                <div class="payment-row">

                    <select
                        name="payments[0][payment_method]"
                        class="form-select payment-method">

                        <option
                            value=""
                            selected>

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
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="Сумма"
                        data-auto="1">

                    <button
                        type="button"
                        class="remove-payment"
                        title="Удалить">

                        &times;

                    </button>

                </div>

            </div>

            <button
                type="button"
                id="addPayment">

                + Добавить оплату

            </button>

            <div class="payment-summary">

                <div class="payment-summary-row">

                    <span class="label">
                        Итого
                    </span>

                    <span
                        class="value"
                        id="paymentOrderTotal">

                        0 тг

                    </span>

                </div>

                <div class="payment-summary-row">

                    <span class="label">
                        Оплачено
                    </span>

                    <span
                        class="value"
                        id="paymentPaidTotal">

                        0 тг

                    </span>

                </div>

                <div class="payment-summary-row">

                    <span class="label">
                        Осталось
                    </span>

                    <span
                        class="value"
                        id="paymentRemaining">

                        0 тг

                    </span>

                </div>

            </div>

        </div>

    </form>

</div>

{{-- =========================================================
     МОДАЛЬНОЕ ОКНО ТОВАРОВ
========================================================= --}}

<div
    class="modal fade modal-fullscreen"
    id="productModal">

    <div class="modal-dialog modal-dialog-centered modal-fullscreen">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Выберите партию
                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <input
                    type="text"
                    id="modalSkuInput"
                    class="form-control mb-2"
                    placeholder="Введите SKU">

                <div id="modalBatchesSimple"></div>

            </div>

        </div>

    </div>

</div>

{{-- =========================================================
     МОДАЛЬНОЕ ОКНО ПАРТИИ
========================================================= --}}

<div
    class="modal fade modal-fullscreen"
    id="batchDetailModal">

    <div class="modal-dialog modal-dialog-centered modal-fullscreen">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Введите количество (минус = возврат)
                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <table
                    class="table table-sm"
                    id="batchDetailTable">

                    <thead>

                        <tr>
                            <th>Склад</th>
                            <th>Доступно</th>
                            <th>Количество</th>
                        </tr>

                    </thead>

                    <tbody></tbody>

                </table>

                <button
                    class="btn w-100"
                    id="addBatchToOrder">

                    Добавить

                </button>

            </div>

        </div>

    </div>

</div>

<script>
    let itemIndex = 0;
    let paymentIndex = 1;
    let currentBatch = {};
    let batchesState = {};

    /* =========================================================
       ФОРМАТ СУММЫ
    ========================================================= */

    function formatAmount(amount) {

        amount = Math.round(Number(amount) || 0);

        return amount
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    /* =========================================================
       ПОЛУЧИТЬ ЧИСЛО
    ========================================================= */

    function parseAmount(value) {

        return parseFloat(
            String(value || '')
            .replace(/\./g, '')
            .replace(',', '.')
        ) || 0;
    }

    /* =========================================================
       ФОРМАТИРОВАНИЕ СУММЫ
    ========================================================= */

    function formatInputAmount(input) {

        let raw = String(input.val() || '')
            .replace(/\D/g, '');

        if (raw === '') {
            input.val('');
            return;
        }

        input.val(
            formatAmount(
                parseInt(raw, 10)
            )
        );
    }

    /* =========================================================
       ОПЛАТА С ОТРИЦАТЕЛЬНЫМ ЗНАКОМ
    ========================================================= */

    function parsePaymentAmount(value) {

        let str = String(value || '').trim();

        if (str === '') {
            return 0;
        }

        const isNegative = str.startsWith('-');

        str = str.replace(/-/g, '');
        str = str.replace(/\./g, '');
        str = str.replace(',', '.');

        const number = parseFloat(str) || 0;

        return isNegative ? -number : number;
    }

    /* =========================================================
       ФОРМАТ ОПЛАТЫ
    ========================================================= */

    function formatPaymentInputAmount(input) {

        let value = String(input.val() || '').trim();

        if (value === '') {
            input.val('');
            return;
        }

        const isNegative = value.startsWith('-');

        let raw = value.replace(/\D/g, '');

        if (raw === '') {
            input.val(isNegative ? '-' : '');
            return;
        }

        let formatted = formatAmount(
            parseInt(raw, 10)
        );

        if (isNegative) {
            formatted = '-' + formatted;
        }

        input.val(formatted);
    }

    /* =========================================================
       ОТКРЫТЬ ТОВАРЫ
    ========================================================= */

    $('#openProductModal').click(function() {

        $('#modalSkuInput').val('');
        $('#modalBatchesSimple').empty();

        bootstrap.Modal
            .getOrCreateInstance(
                document.getElementById('productModal')
            )
            .show();
    });

    /* =========================================================
       ПОИСК SKU
    ========================================================= */

    $('#modalSkuInput').autocomplete({

        source: '{{ route("admin.variants.autocomplete") }}',

        minLength: 1,

        appendTo: '#productModal',

        select: function(e, ui) {

            loadBatchesSimple(
                ui.item.value
            );
        }

    });

    /* =========================================================
       ЗАГРУЗКА ПАРТИЙ
    ========================================================= */

    function loadBatchesSimple(sku) {

        $.get(
            '/admin/batches/by-sku/' +
            encodeURIComponent(sku),

            function(data) {

                let $list =
                    $('#modalBatchesSimple').empty();

                data.forEach(batch => {

                    if (!batchesState[batch.id]) {

                        batchesState[batch.id] = {};

                        batch.warehouses.forEach(w => {

                            batchesState[batch.id][w.id] =
                                w.pivot.quantity;

                        });

                    }

                    const totalQty =
                        batch.warehouses.reduce(
                            (sum, w) =>
                            sum +
                            batchesState[batch.id][w.id],
                            0
                        );

                    $list.append(`
                        <div
                            class="batch-card batch-select"
                            data-batch='${JSON.stringify(batch)}'
                            data-sku="${sku}"
                        >

                            <div class="batch-info">

                                <div class="batch-sku">
                                    ${sku}
                                </div>

                                <div class="batch-code">
                                    ( Партия ${batch.batch_code} )
                                </div>

                            </div>

                            <div class="batch-qty">
                                Доступно ${totalQty}
                            </div>

                        </div>
                    `);

                });

            }
        );
    }

    /* =========================================================
       ВЫБОР ПАРТИИ
    ========================================================= */

    $(document).on(
        'click',
        '.batch-select',
        function() {

            currentBatch =
                JSON.parse(
                    $(this).attr('data-batch')
                );

            currentBatch.sku =
                $(this).attr('data-sku');

            const selectedWarehouse =
                $('#warehouseSelect').val();

            const batchWarehouses =
                selectedWarehouse ?
                currentBatch.warehouses.filter(
                    w => w.id == selectedWarehouse
                ) :
                currentBatch.warehouses;

            const $tbody =
                $('#batchDetailTable tbody').empty();

            batchWarehouses.forEach(w => {

                const available =
                    batchesState[
                        currentBatch.id
                    ][w.id];

                $tbody.append(`
                    <tr>

                        <td>
                            ${w.name}
                        </td>

                        <td>
                            ${available}
                        </td>

                        <td>

                            <input
                                type="number"
                                class="form-control qty-input"
                                data-warehouse="${w.id}"
                                min="-${available}"
                                max="${available}"
                                placeholder="Кол-во"
                            >

                        </td>

                    </tr>
                `);

            });

            bootstrap.Modal
                .getOrCreateInstance(
                    document.getElementById('productModal')
                )
                .hide();

            bootstrap.Modal
                .getOrCreateInstance(
                    document.getElementById('batchDetailModal')
                )
                .show();

        }
    );

    /* =========================================================
       ДОБАВИТЬ ПАРТИЮ
    ========================================================= */

    $('#addBatchToOrder').click(function() {

        let hasError = false;

        $('#batchDetailTable tbody tr').each(
            function() {

                const $input =
                    $(this).find('.qty-input');

                let qty =
                    parseInt($input.val());

                if (
                    isNaN(qty) ||
                    qty === 0
                ) {
                    return;
                }

                const warehouseId =
                    $input.data('warehouse');

                const available =
                    batchesState[
                        currentBatch.id
                    ][warehouseId];

                if (qty > available) {

                    alert(
                        'Недостаточно товара'
                    );

                    hasError = true;

                    return;
                }

                const warehouse =
                    currentBatch.warehouses.find(
                        w => w.id == warehouseId
                    );

                const warehouseName =
                    warehouse.name;

                const label =
                    qty < 0 ?
                    '<span class="return-label">ВОЗВРАТ</span>' :
                    '';

                $('#itemsWrapper').append(`

                    <div class="order-item">

                        <div class="item-head">

                            <div>

                                <div class="item-title">

                                    ${currentBatch.sku}

                                    <span class="meta">
                                        · Партия
                                        ${currentBatch.batch_code}
                                    </span>

                                    ${label}

                                </div>

                                <span class="warehouse">
                                    ${warehouseName}
                                </span>

                            </div>

                            <button
                                type="button"
                                class="remove-item"
                            >
                                &times;
                            </button>

                        </div>

                        <div class="order-item-bottom">

                            <span class="qty-times">

                                ${qty} ×

                                <span class="price-field">

                                    <input
                                        type="text"
                                        name="items[${itemIndex}][price]"
                                        class="price-input"
                                        inputmode="numeric"
                                        autocomplete="off"
                                        placeholder="цена"
                                    >

                                </span>

                            </span>

                            <span>

                                Сумма:

                                <span class="item-total">
                                    0 тг
                                </span>

                            </span>

                        </div>

                        <input
                            type="hidden"
                            name="items[${itemIndex}][sku]"
                            value="${currentBatch.sku}"
                        >

                        <input
                            type="hidden"
                            name="items[${itemIndex}][batch_id]"
                            value="${currentBatch.id}"
                        >

                        <input
                            type="hidden"
                            name="items[${itemIndex}][warehouse_id]"
                            value="${warehouseId}"
                        >

                        <input
                            type="hidden"
                            name="items[${itemIndex}][quantity]"
                            value="${qty}"
                        >

                        <input
                            type="hidden"
                            name="items[${itemIndex}][warehouse_name]"
                            value="${warehouseName}"
                        >

                        <input
                            type="hidden"
                            name="items[${itemIndex}][batch_code]"
                            value="${currentBatch.batch_code}"
                        >

                    </div>

                `);

                if (qty > 0) {

                    batchesState[
                        currentBatch.id
                    ][warehouseId] -= qty;

                }

                itemIndex++;

            }
        );

        if (hasError) {
            return;
        }

        bootstrap.Modal
            .getOrCreateInstance(
                document.getElementById('batchDetailModal')
            )
            .hide();

        recalcOrderTotal();

        $('#positionsCount')
            .text(
                '(' +
                $('.order-item').length +
                ')'
            );

    });

    /* =========================================================
       РАСЧЁТ ПОЗИЦИИ
    ========================================================= */

    function recalcItemTotal($item) {

        const price =
            parseAmount(
                $item.find('.price-input').val()
            );

        const qty =
            parseInt(
                $item.find(
                    'input[name$="[quantity]"]'
                ).val()
            ) || 0;

        const total =
            price * qty;

        $item
            .find('.item-total')
            .text(
                formatAmount(total) +
                ' тг'
            );

        return total;
    }

    /* =========================================================
       ОБЩАЯ СУММА
    ========================================================= */

    function recalcOrderTotal() {

        let sum = 0;

        $('.order-item').each(
            function() {

                sum +=
                    recalcItemTotal(
                        $(this)
                    );

            }
        );

        const discount =
            parseAmount(
                $('#discountInput').val()
            );

        let final =
            sum - discount;

        if (
            final < 0 &&
            sum >= 0
        ) {
            final = 0;
        }

        $('#orderTotal')
            .text(
                formatAmount(final) +
                ' тг'
            );

        updatePaymentSummary();
    }

    /* =========================================================
       ИЗМЕНЕНИЕ ЦЕНЫ
    ========================================================= */

    $(document).on(
        'input',
        '.price-input',
        function() {

            formatInputAmount(
                $(this)
            );

            recalcOrderTotal();

        }
    );

    /* =========================================================
       ИЗМЕНЕНИЕ СКИДКИ
    ========================================================= */

    $(document).on(
        'input',
        '#discountInput',
        function() {

            formatInputAmount(
                $(this)
            );

            recalcOrderTotal();

        }
    );

    /* =========================================================
       УДАЛЕНИЕ ТОВАРА
    ========================================================= */

    $(document).on(
        'click',
        '.remove-item',
        function() {

            $(this)
                .closest('.order-item')
                .remove();

            recalcOrderTotal();

            $('#positionsCount')
                .text(
                    '(' +
                    $('.order-item').length +
                    ')'
                );

        }
    );

    /* =========================================================
       ИТОГО ЗАКАЗА
    ========================================================= */

    function getOrderTotal() {

        let sum = 0;

        $('.order-item').each(
            function() {

                const price =
                    parseAmount(
                        $(this)
                        .find('.price-input')
                        .val()
                    );

                const qty =
                    parseInt(
                        $(this)
                        .find(
                            'input[name$="[quantity]"]'
                        )
                        .val()
                    ) || 0;

                sum += price * qty;

            }
        );

        const discount =
            parseAmount(
                $('#discountInput').val()
            );

        let total =
            sum - discount;

        if (
            total < 0 &&
            sum >= 0
        ) {
            total = 0;
        }

        return Math.round(total);
    }

    /* =========================================================
       СУММА ОПЛАТ
    ========================================================= */

    function getPaymentsTotal() {

        let total = 0;

        $('.payment-amount').each(
            function() {

                total +=
                    parsePaymentAmount(
                        $(this).val()
                    );

            }
        );

        return Math.round(total);
    }

    /* =========================================================
       ОБНОВЛЕНИЕ ОПЛАТЫ
    ========================================================= */

    function updatePaymentSummary() {

        const orderTotal =
            getOrderTotal();

        const $firstPayment =
            $('.payment-row:first .payment-amount');

        if (
            $('.payment-row').length === 1 &&
            $firstPayment.length &&
            $firstPayment.attr('data-auto') === '1'
        ) {

            $firstPayment.val(
                formatAmount(orderTotal)
            );

        }

        const paidTotal =
            getPaymentsTotal();

        let remaining =
            orderTotal - paidTotal;

        if (Math.abs(remaining) < 1) {
            remaining = 0;
        }

        $('#paymentOrderTotal')
            .text(
                formatAmount(orderTotal) +
                ' тг'
            );

        $('#paymentPaidTotal')
            .text(
                formatAmount(paidTotal) +
                ' тг'
            );

        $('#paymentRemaining')
            .text(
                formatAmount(remaining) +
                ' тг'
            );

        $('#paymentRemaining')
            .removeClass(
                'payment-ok payment-error'
            );

        if (remaining === 0) {

            $('#paymentRemaining')
                .addClass(
                    'payment-ok'
                );

        } else {

            $('#paymentRemaining')
                .addClass(
                    'payment-error'
                );

        }
    }

    /* =========================================================
       РУЧНАЯ ОПЛАТА
    ========================================================= */

    $(document).on(
        'input',
        '.payment-amount',
        function() {

            $(this)
                .attr(
                    'data-auto',
                    '0'
                );

            formatPaymentInputAmount(
                $(this)
            );

            updatePaymentSummary();

        }
    );

    /* =========================================================
       ДОБАВИТЬ ОПЛАТУ
    ========================================================= */

    $('#addPayment').click(function() {

        const orderTotal =
            getOrderTotal();

        const paidTotal =
            getPaymentsTotal();

        let remaining =
            orderTotal - paidTotal;

        const paymentMethods =
            @json($paymentMethods);

        $('#paymentsWrapper').append(`

            <div class="payment-row">

                <select
                    name="payments[${paymentIndex}][payment_method]"
                    class="form-select payment-method"
                >

                    <option value="" selected>
                        Не указано
                    </option>

                    ${paymentMethods.map(method => `

                        <option value="${method.name}">
                            ${method.name}
                        </option>

                    `).join('')}

                </select>

                <input
                    type="text"
                    name="payments[${paymentIndex}][amount]"
                    class="form-control payment-amount"
                    inputmode="numeric"
                    autocomplete="off"
                    value="${formatAmount(remaining)}"
                    placeholder="Сумма"
                    data-auto="1"
                >

                <button
                    type="button"
                    class="remove-payment"
                    title="Удалить"
                >
                    &times;
                </button>

            </div>

        `);

        paymentIndex++;

        updatePaymentSummary();

    });

    /* =========================================================
       УДАЛИТЬ ОПЛАТУ
    ========================================================= */

    $(document).on(
        'click',
        '.remove-payment',
        function() {

            const rows =
                $('.payment-row').length;

            if (rows <= 1) {

                const $input =
                    $(this)
                    .closest('.payment-row')
                    .find('.payment-amount');

                $input
                    .val('0')
                    .attr(
                        'data-auto',
                        '1'
                    );

                updatePaymentSummary();

                return;
            }

            $(this)
                .closest('.payment-row')
                .remove();

            updatePaymentSummary();

        }
    );

    /* =========================================================
       ПРОВЕРКА ПЕРЕД ОТПРАВКОЙ
    ========================================================= */

    $('#orderForm').on(
        'submit',
        function(e) {

            let valid = true;

            $('.price-input').each(
                function() {

                    const val =
                        parseAmount(
                            $(this).val()
                        );

                    if (val <= 0) {

                        valid = false;

                        $(this)
                            .css(
                                'border-color',
                                '#e5484d'
                            );

                    } else {

                        $(this)
                            .css(
                                'border-color',
                                'var(--border)'
                            );

                    }

                }
            );

            if (!valid) {

                e.preventDefault();

                alert(
                    'Заполните цену у всех позиций'
                );

                return;
            }

            if (
                $('.order-item').length === 0
            ) {

                e.preventDefault();

                alert(
                    'Добавьте хотя бы одну позицию'
                );

                return;
            }

            let paymentMethodMissing = false;

            $('.payment-method').each(
                function() {

                    if (!$(this).val()) {

                        paymentMethodMissing = true;

                        $(this).css(
                            'border-color',
                            '#e5484d'
                        );

                    } else {

                        $(this).css(
                            'border-color',
                            'var(--border)'
                        );

                    }

                }
            );

            if (paymentMethodMissing) {

                e.preventDefault();

                alert(
                    'Выберите способ оплаты перед сохранением заказа'
                );

                return;
            }

            const orderTotal =
                getOrderTotal();

            const paidTotal =
                getPaymentsTotal();

            const difference =
                Math.abs(
                    orderTotal -
                    paidTotal
                );

            if (
                difference > 0
            ) {

                e.preventDefault();

                alert(
                    'Сумма оплат должна полностью совпадать с итогом заказа.\n\n' +

                    'Итого: ' +
                    formatAmount(orderTotal) +
                    ' тг\n' +

                    'Оплачено: ' +
                    formatAmount(paidTotal) +
                    ' тг\n' +

                    'Осталось: ' +
                    formatAmount(
                        orderTotal -
                        paidTotal
                    ) +
                    ' тг'
                );

                return;
            }

            /* Убираем форматирование перед отправкой */

            $('.price-input').each(
                function() {

                    $(this).val(
                        parseAmount(
                            $(this).val()
                        )
                    );

                }
            );

            $('#discountInput').val(
                parseAmount(
                    $('#discountInput').val()
                )
            );

            $('.payment-amount').each(
                function() {

                    $(this).val(
                        parsePaymentAmount(
                            $(this).val()
                        )
                    );

                }
            );

        }
    );

    /* =========================================================
       ОТМЕНА
    ========================================================= */

    $('#cancelOrder').click(
        function() {

            window.history.back();

        }
    );

    /* =========================================================
       ПЕРВИЧНЫЙ РАСЧЁТ
    ========================================================= */

    recalcOrderTotal();
</script>

@endsection