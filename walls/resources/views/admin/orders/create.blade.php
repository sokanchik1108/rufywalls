@extends('layouts.app')

@section('title', 'Создать заказ')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">


<style>
    :root {
        --bg: #f4f5f7;
        --surface: #ffffff;
        --ink: #1a1d21;
        --ink-soft: #6e7580;
        --ink-faint: #9aa0a8;
        --border: #dfe3e8;
        --border-strong: #c9ced6;
        --primary: #2f6fed;
        --primary-hover: #1f56d1;
        --primary-soft: #eaf1fe;
        --danger: #e5484d;
        --danger-soft: #fdeceb;
        --radius: 6px;
        --radius-lg: 8px;
    }


    * {
        box-sizing: border-box
    }


    body {
        background: var(--bg);
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        color: var(--ink);
        margin: 0;
        padding: 0;
        font-size: 14px;
        -webkit-font-smoothing: antialiased
    }


    .container {
        padding: 0 0 40px;
        max-width: 600px;
        margin: 0 auto
    }


    #orderControls {
        position: sticky;
        margin-top: -18px;
        margin-bottom: 10px;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
    }


    #cancelOrder {
        color: var(--ink-soft);
        font-weight: 500;
        font-size: 13.5px;
        cursor: pointer;
        padding: 7px 10px;
        border-radius: var(--radius);
        transition: background .12s, color .12s
    }


    #cancelOrder:hover {
        background: var(--bg);
        color: var(--ink)
    }


    #finishOrder {
        background: var(--primary);
        color: #fff;
        border: none;
        font-weight: 600;
        font-size: 13.5px;
        cursor: pointer;
        border-radius: var(--radius);
        padding: 8px 18px;
        transition: background .12s
    }


    #finishOrder:hover {
        background: var(--primary-hover)
    }


    .panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        margin: 0 16px 14px;
        padding: 14px 16px 16px;
    }


    .section-label {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--ink-soft);
        margin: 0 0 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }


    .section-label .count {
        font-weight: 500;
        color: var(--ink-faint);
    }


    .field {
        margin-bottom: 12px
    }


    .field:last-child {
        margin-bottom: 0
    }


    .field-row {
        display: flex;
        gap: 10px
    }


    .field-row .field {
        flex: 1;
        min-width: 0
    }


    label.field-label {
        display: block;
        font-size: 12px;
        color: var(--ink-soft);
        margin-bottom: 5px;
        font-weight: 500;
    }


    select.form-select {
        padding: 6px 10px;
    }


    input.form-control,
    select.form-select {
        display: block;
        width: 100%;
        height: 40px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0 10px;
        margin-bottom: 0;
        background: var(--surface);
        font-family: inherit;
        font-size: 16px;
        line-height: 38px;
        color: var(--ink);
        box-sizing: border-box;
        transition: border-color .12s, box-shadow .12s;
        appearance: none;
        -webkit-appearance: none;
    }


    input.form-control::placeholder {
        color: var(--ink-faint)
    }


    select.form-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='9' viewBox='0 0 14 9' fill='none'%3E%3Cpath d='M1 1L7 7L13 1' stroke='%236e7580' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
    }


    input:focus,
    select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-soft);
    }


    input[type="datetime-local"],
    input[type="date"] {
        -webkit-appearance: none;
        appearance: none;
        height: 40px;
        line-height: 38px;
        color: var(--ink);
        background: var(--surface);
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
        width: 16px;
        height: 16px;
        opacity: .6;
        cursor: pointer;
    }


    input[type="datetime-local"]::-webkit-inner-spin-button,
    input[type="date"]::-webkit-inner-spin-button {
        display: none;
    }


    #itemsWrapper:empty {
        display: block;
        text-align: center;
        color: var(--ink-faint);
        font-size: 13px;
        padding: 16px 10px 4px;
    }


    #itemsWrapper:empty::before {
        content: "Пока нет добавленных позиций";
    }


    .order-item {
        border: 1px solid var(--border);
        padding: 10px 12px;
        margin-bottom: 8px;
        position: relative;
        border-radius: var(--radius);
    }


    .order-item .item-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding-right: 20px;
    }


    .order-item .item-title {
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.4;
    }


    .order-item .item-title .meta {
        color: var(--ink-faint);
        font-weight: 500;
    }


    .order-item .warehouse {
        display: block;
        font-size: 12px;
        color: var(--ink-soft);
        margin-top: 2px;
    }


    .return-label {
        display: inline-block;
        color: var(--danger);
        background: var(--danger-soft);
        font-weight: 600;
        font-size: 11px;
        margin-left: 6px;
        padding: 1px 6px;
        border-radius: 4px;
        vertical-align: middle;
    }


    .remove-item {
        position: absolute;
        right: 8px;
        top: 8px;
        border: none;
        background: none;
        width: 22px;
        height: 22px;
        line-height: 1;
        font-size: 17px;
        color: var(--ink-faint);
        cursor: pointer;
        border-radius: 4px;
        transition: background .12s, color .12s;
    }


    .remove-item:hover {
        background: var(--danger-soft);
        color: var(--danger);
    }


    .order-item-bottom {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        font-size: 13px;
    }


    .qty-times {
        color: var(--ink-soft);
        display: flex;
        align-items: center;
        gap: 6px;
    }


    .price-field {
        position: relative;
        display: inline-flex;
        align-items: center;
    }


    .price-input {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        width: 100px;
        padding: 5px 24px 5px 7px;
        background: var(--surface);
        font-family: inherit;
        font-size: 16px;
        color: var(--ink);
        transition: border-color .12s;
    }


    .price-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-soft);
    }


    .price-field::after {
        content: "тг";
        position: absolute;
        right: 8px;
        font-size: 11.5px;
        color: var(--ink-faint);
        pointer-events: none;
    }


    .item-total {
        font-weight: 600;
    }


    #openProductModal {
        width: 100%;
        margin: 0 0 12px;
        padding: 9px;
        background: #2f6fed;
        color: white;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background .12s, border-color .12s;
    }


    #openProductModal:hover {
        background: var(--primary-hover);
        border-color: var(--primary);
    }


    .discount-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 2px 0 4px;
    }


    .discount-row label.field-label {
        margin-bottom: 0;
    }


    #discountInput {
        width: 120px;
        text-align: right;
        margin-bottom: 0;
    }


    .total-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        border-top: 1px solid var(--border);
        margin: 10px -16px 0;
        padding: 12px 16px 0;
    }


    .total-row .total-label {
        font-size: 13px;
        color: var(--ink-soft);
        font-weight: 500;
    }


    #orderTotal {
        font-weight: 700;
        font-size: 19px;
        letter-spacing: -.005em;
        color: var(--ink);
    }


    /* ---------- PAYMENT ---------- */

    .payment-row {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 8px;
    }


    .payment-row select {
        flex: 1;
        min-width: 0;
    }


    .payment-amount {
        width: 145px !important;
        flex: none !important;
        text-align: right;
    }


    .remove-payment {
        width: 32px;
        height: 32px;
        flex: none;
        border: none;
        background: transparent;
        color: var(--ink-faint);
        font-size: 20px;
        cursor: pointer;
        border-radius: 4px;
    }


    .remove-payment:hover {
        background: var(--danger-soft);
        color: var(--danger);
    }


    #addPayment {
        width: 100%;
        height: 38px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--primary);
        border-radius: var(--radius);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 4px;
    }


    #addPayment:hover {
        background: var(--primary-soft);
        border-color: var(--primary);
    }


    .payment-summary {
        border-top: 1px solid var(--border);
        margin-top: 12px;
        padding-top: 12px;
    }


    .payment-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        margin-bottom: 6px;
    }


    .payment-summary-row .label {
        color: var(--ink-soft);
    }


    .payment-summary-row .value {
        font-weight: 600;
    }


    #paymentRemaining {
        font-weight: 700;
    }


    .payment-ok {
        color: #198754;
    }


    .payment-error {
        color: var(--danger);
    }


    /* ---------- MODALS ---------- */

    .modal-fullscreen {
        max-width: 100%;
        width: 100%;
        height: 100%;
        margin: 0
    }


    .modal-fullscreen .modal-content {
        height: 100%;
        border-radius: 0;
        border: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        background: var(--bg);
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }


    .modal-header {
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        padding: 14px 18px;
    }


    .modal-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
    }


    .btn-close {
        opacity: .5;
    }


    .btn-close:hover {
        opacity: 1;
    }


    .modal-body {
        padding: 16px 18px 20px;
        overflow-y: auto;
    }


    #modalSkuInput {
        margin-bottom: 12px;
    }


    .batch-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 11px 13px;
        margin-bottom: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        font-size: 13.5px;
        transition: border-color .12s, background .12s;
    }


    .batch-card:hover {
        border-color: var(--primary);
        background: var(--primary-soft);
    }


    .batch-card .batch-info {
        flex: 1;
        min-width: 0;
    }


    .batch-card .batch-sku {
        font-weight: 600;
        line-height: 1.35;
    }


    .batch-card .batch-code {
        font-size: 12px;
        color: var(--ink-soft);
    }


    .batch-card .batch-qty {
        flex: none;
        font-size: 12px;
        font-weight: 600;
        color: var(--ink-soft);
        white-space: nowrap;
    }


    #batchDetailTable {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 4px;
    }


    #batchDetailTable thead th {
        text-align: left;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--ink-soft);
        padding: 0 8px 8px;
        border-bottom: 1px solid var(--border);
    }


    #batchDetailTable tbody td {
        padding: 10px 8px;
        border-bottom: 1px solid var(--border);
        font-size: 13.5px;
        vertical-align: middle;
    }


    #batchDetailTable tbody tr:last-child td {
        border-bottom: none;
    }


    .qty-input {
        width: 100%;
        font-size: 16px;
        padding: 6px 8px;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        font-weight: 600;
        font-family: inherit;
        background: var(--surface);
    }


    .qty-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-soft);
    }


    #addBatchToOrder {
        width: 100%;
        background: var(--primary);
        color: #fff;
        border: none;
        font-weight: 600;
        font-size: 13.5px;
        cursor: pointer;
        border-radius: var(--radius);
        padding: 11px;
        transition: background .12s;
    }


    #addBatchToOrder:hover {
        background: var(--primary-hover);
    }


    .ui-autocomplete {
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-lg) !important;
        box-shadow: 0 4px 14px rgba(20, 24, 30, .08) !important;
        padding: 4px !important;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        z-index: 3000 !important;
    }


    .ui-menu-item-wrapper {
        border-radius: var(--radius) !important;
        padding: 8px 9px !important;
        border: none !important;
        font-size: 13.5px;
    }


    .ui-menu-item-wrapper.ui-state-active {
        background: var(--primary-soft) !important;
        color: var(--primary) !important;
        border: none !important;
        margin: 0 !important;
    }


    @media (max-width: 600px) {

        .payment-row {
            gap: 6px;
        }

        .payment-amount {
            width: 125px !important;
        }

        .price-input {
            width: 100px;
        }
    }
</style>


<div class="container">

    <form method="POST"
        action="{{ route('admin.orders.store') }}"
        id="orderForm">

        @csrf


        <div id="orderControls">

            <span id="cancelOrder">
                Отмена
            </span>

            <button
                type="submit"
                id="finishOrder">
                Готово
            </button>

        </div>


        {{-- КЛИЕНТ --}}

        <div class="panel">

            <div class="field">

                <label class="field-label">
                    Имя клиента
                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    required>

            </div>


            <div class="field">

                <label class="field-label">
                    Телефон клиента
                </label>

                <input
                    type="text"
                    name="phone"
                    class="form-control"
                    placeholder="+7 (___) ___-__-__"
                    required>

            </div>


            <div class="field">

                <label class="field-label">
                    Комментарий
                </label>

                <input
                    type="text"
                    name="comment"
                    class="form-control"
                    placeholder="Необязательно">

            </div>


            <div class="field-row">

                <div class="field">

                    <label class="field-label">
                        Дата и время
                    </label>

                    <input
                        type="datetime-local"
                        name="order_date"
                        class="form-control"
                        value="{{ now('Asia/Almaty')->format('Y-m-d\TH\:i') }}"
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

                        <option value="{{ $point->id }}">
                            {{ $point->name }}
                        </option>

                        @endforeach

                    </select>

                </div>

            </div>


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


        {{-- ПОЗИЦИИ --}}

        <div class="panel">

            <div class="section-label">

                Позиции

                <span
                    class="count"
                    id="positionsCount">
                    (0)
                </span>

            </div>


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


            <div id="itemsWrapper"></div>

        </div>


        {{-- ИТОГ --}}

        <div class="panel">

            <div class="section-label">
                Итог
            </div>


            <div class="discount-row">

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
                    value="0"
                    inputmode="numeric"
                    autocomplete="off">

            </div>


            <div class="total-row">

                <span class="total-label">
                    Итого к оплате
                </span>

                <div id="orderTotal">
                    0 тг
                </div>

            </div>

        </div>


        {{-- ОПЛАТА --}}

        <div class="panel">

            <div class="section-label">
                Оплата
            </div>


            <div id="paymentsWrapper">
                <div class="payment-row">
                    <select
                        name="payments[0][payment_method]"
                        class="form-select payment-method">
                        <option value="" selected>
                            Не указано
                        </option>
                        @foreach($paymentMethods as $method)
                        <option value="{{ $method->name }}">
                            {{ $method->name }}
                        </option>
                        @endforeach
                    </select>
                    <input type="text" name="payments[0][amount]" class="form-control payment-amount" inputmode="numeric" autocomplete="off" placeholder="Сумма" data-auto="1">
                    <button type="button" class="remove-payment" title="Удалить"> &times; </button>
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
                        Итого заказа
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


{{-- МОДАЛЬНОЕ ОКНО ТОВАРОВ --}}

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


{{-- МОДАЛЬНОЕ ОКНО ПАРТИИ --}}

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

                            <th>
                                Склад
                            </th>

                            <th>
                                Доступно
                            </th>

                            <th>
                                Количество
                            </th>

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


    /*
     * ФОРМАТ СУММЫ
     *
     * 120000 -> 120.000
     * 1500000 -> 1.500.000
     * 7000 -> 7.000
     */

    function formatAmount(amount) {

        amount = Math.round(Number(amount) || 0);

        return amount
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }


    /*
     * ПОЛУЧИТЬ ЧИСЛО ИЗ ФОРМАТИРОВАННОЙ СУММЫ
     *
     * 120.000 -> 120000
     * 1.500.000 -> 1500000
     */

    function parseAmount(value) {

        return parseFloat(
            String(value || '')
            .replace(/\./g, '')
            .replace(',', '.')
        ) || 0;

    }


    /*
     * ФОРМАТИРОВАНИЕ ПОЛЯ СУММЫ
     */

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


    /*
     * ДОБАВЛЕНИЕ ТОВАРА
     */

    $('#openProductModal').click(() => {

        $('#modalSkuInput').val('');

        $('#modalBatchesSimple').empty();

        bootstrap.Modal
            .getOrCreateInstance(
                document.getElementById('productModal')
            )
            .show();

    });


    $('#modalSkuInput').autocomplete({

        source: '{{ route("admin.variants.autocomplete") }}',

        minLength: 1,

        appendTo: '#productModal',

        select: function(e, ui) {

            loadBatchesSimple(ui.item.value);

        }

    });


    function loadBatchesSimple(sku) {

        $.get(
            '/admin/batches/by-sku/' + sku,
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
                            data-sku='${sku}'>

                            <div class="batch-info">

                                <div class="batch-sku">
                                    ${sku}
                                </div>

                                <div class="batch-code">
                                    Партия ${batch.batch_code}
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
                                placeholder="Кол-во">

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


    $('#addBatchToOrder').click(function() {

        let hasError = false;


        $('#batchDetailTable tbody tr').each(

            function() {

                const $input =
                    $(this).find('.qty-input');


                let qty =
                    parseInt($input.val());


                if (isNaN(qty) || qty === 0) {
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


                const warehouseName =
                    currentBatch.warehouses.find(
                        w => w.id == warehouseId
                    ).name;


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

                                    Склад:
                                    ${warehouseName}

                                </span>

                            </div>


                            <button
                                type="button"
                                class="remove-item">

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
                                        placeholder="цена">

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
                            value="${currentBatch.sku}">


                        <input
                            type="hidden"
                            name="items[${itemIndex}][batch_id]"
                            value="${currentBatch.id}">


                        <input
                            type="hidden"
                            name="items[${itemIndex}][warehouse_id]"
                            value="${warehouseId}">


                        <input
                            type="hidden"
                            name="items[${itemIndex}][quantity]"
                            value="${qty}">


                        <input
                            type="hidden"
                            name="items[${itemIndex}][warehouse_name]"
                            value="${warehouseName}">


                        <input
                            type="hidden"
                            name="items[${itemIndex}][batch_code]"
                            value="${currentBatch.batch_code}">

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


    /*
     * РАСЧЁТ ПОЗИЦИИ
     */

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


    /*
     * ОБЩАЯ СУММА ЗАКАЗА
     */

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


        if (final < 0) {
            final = 0;
        }


        $('#orderTotal')
            .text(
                formatAmount(final) +
                ' тг'
            );


        updatePaymentSummary();

    }


    /*
     * ИЗМЕНЕНИЕ ЦЕНЫ
     */

    $(document).on(
        'input',
        '.price-input',
        function() {

            formatInputAmount($(this));

            recalcOrderTotal();

        }
    );


    /*
     * ИЗМЕНЕНИЕ СКИДКИ
     */

    $(document).on(
        'input',
        '#discountInput',
        function() {

            formatInputAmount($(this));

            recalcOrderTotal();

        }
    );


    /*
     * УДАЛЕНИЕ ТОВАРА
     */

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


    /*
     * ОПЛАТЫ
     */

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


        if (total < 0) {
            total = 0;
        }


        return Math.round(total);

    }


    /*
     * ОБЩАЯ СУММА ОПЛАТ
     */

    function getPaymentsTotal() {

        let total = 0;


        $('.payment-amount').each(

            function() {

                total +=
                    parseAmount(
                        $(this).val()
                    );

            }

        );


        return Math.round(total);

    }


    /*
     * ОБНОВЛЕНИЕ ОПЛАТЫ
     */

    function updatePaymentSummary() {

        const orderTotal =
            getOrderTotal();


        /*
         * Если осталась только одна
         * автоматическая строка оплаты,
         * ставим туда весь заказ.
         */

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


        /*
         * ИТОГО ЗАКАЗА
         */

        $('#paymentOrderTotal')
            .text(
                formatAmount(orderTotal) +
                ' тг'
            );


        /*
         * ОПЛАЧЕНО
         */

        $('#paymentPaidTotal')
            .text(
                formatAmount(paidTotal) +
                ' тг'
            );


        /*
         * ОСТАЛОСЬ
         */

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


    /*
     * РУЧНОЕ ИЗМЕНЕНИЕ ОПЛАТЫ
     */

    $(document).on(
        'input',
        '.payment-amount',
        function() {

            $(this)
                .attr(
                    'data-auto',
                    '0'
                );


            formatInputAmount($(this));


            updatePaymentSummary();

        }
    );


    /*
     * ДОБАВИТЬ СПОСОБ ОПЛАТЫ
     */

    $('#addPayment').click(function() {

        const orderTotal =
            getOrderTotal();


        const paidTotal =
            getPaymentsTotal();


        let remaining =
            orderTotal - paidTotal;


        if (remaining < 0) {
            remaining = 0;
        }


        const paymentMethods = @json($paymentMethods);


        $('#paymentsWrapper').append(`
    <div class="payment-row">

<select
    name="payments[${paymentIndex}][payment_method]"
    class="form-select payment-method">
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
            data-auto="1">

        <button
            type="button"
            class="remove-payment"
            title="Удалить">
            &times;
        </button>

    </div>
`);


        paymentIndex++;


        updatePaymentSummary();

    });


    /*
     * УДАЛИТЬ СПОСОБ ОПЛАТЫ
     */

    $(document).on(
        'click',
        '.remove-payment',
        function() {

            const rows =
                $('.payment-row').length;


            /*
             * Если осталась одна строка —
             * просто обнуляем её.
             */

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


    /*
     * ПРОВЕРКА ПЕРЕД ОТПРАВКОЙ
     */

    $('#orderForm').on(
        'submit',
        function(e) {

            let valid = true;


            /*
             * Проверяем цены.
             */

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


            /*
             * Проверяем наличие позиций.
             */

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


            /*
             * Проверяем оплату.
             */

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
                orderTotal > 0 &&
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


            /*
             * ПЕРЕД ОТПРАВКОЙ УБИРАЕМ ТОЧКИ
             *
             * 120.000 -> 120000
             * 1.500.000 -> 1500000
             */

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
                        parseAmount(
                            $(this).val()
                        )
                    );

                }

            );

        }
    );


    /*
     * ОТМЕНА
     */

    $('#cancelOrder').click(
        () => {

            window.history.back();

        }
    );


    /*
     * ПЕРВИЧНЫЙ РАСЧЁТ
     */

    recalcOrderTotal();
</script>

@endsection