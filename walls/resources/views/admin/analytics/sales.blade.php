<!DOCTYPE html>

<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    >

    <title>Продажи по товарам</title>

    <style>

        * {
            box-sizing: border-box;
        }

        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f4f6f9;
            color: #111827;
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Arial,
                sans-serif;
        }

        button,
        input,
        select {
            font: inherit;
        }

        /* ==========================================================
           PAGE
        ========================================================== */

        .page {
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px;
        }

        /* ==========================================================
           HEADER
        ========================================================== */

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .page-title {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
            font-weight: 750;
            letter-spacing: -0.4px;
        }

        .page-subtitle {
            margin-top: 6px;
            color: #6b7280;
            font-size: 13px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 42px;
            padding: 0 15px;
            border-radius: 9px;
            background: #01142f;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            transition:
                background .15s ease,
                transform .15s ease;
        }

        .back-button:hover {
            background: #02214b;
        }

        .back-button:active {
            transform: scale(.98);
        }

        /* ==========================================================
           FILTERS
        ========================================================== */

        .filters {
            display: grid;
            grid-template-columns:
                minmax(150px, 180px)
                minmax(150px, 180px)
                minmax(200px, 260px)
                minmax(220px, 1fr)
                auto;
            align-items: end;
            gap: 12px;
            padding: 16px;
            margin-bottom: 18px;
            background: #fff;
            border: 1px solid #e7eaf0;
            border-radius: 13px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .filter-group label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 650;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            height: 42px;
            padding: 0 11px;
            border: 1px solid #d7dce4;
            border-radius: 8px;
            background: #fff;
            color: #111827;
            font-size: 14px;
            outline: none;
            transition:
                border-color .15s ease,
                box-shadow .15s ease;
        }

        /* Дата: убираем "распирание" инпута нативным пикером,
           чтобы он всегда помещался в свою колонку грида */
        .filter-group input[type="date"] {
            display: block;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            padding-right: 6px;
            box-sizing: border-box;
            -webkit-appearance: auto;
            appearance: auto;
        }

        .filter-group input[type="date"]::-webkit-calendar-picker-indicator {
            margin-left: 2px;
            padding: 0;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border-color: #01142f;
            box-shadow: 0 0 0 3px rgba(1, 20, 47, .07);
        }

        /* ==========================================================
           SKU SEARCH
        ========================================================== */

        .sku-search-wrapper {
            position: relative;
            width: 100%;
            min-width: 0;
        }

        .sku-search-wrapper input {
            padding-right: 42px;
        }

        .clear-sku-button {
            position: absolute;
            top: 50%;
            right: 7px;
            width: 28px;
            height: 28px;
            transform: translateY(-50%);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            cursor: pointer;
            font-size: 21px;
            line-height: 1;
            font-weight: 400;
            transition:
                background .15s ease,
                color .15s ease,
                transform .15s ease;
        }

        .clear-sku-button.visible {
            display: flex;
        }

        .clear-sku-button:hover {
            background: #d1d5db;
            color: #111827;
        }

        .clear-sku-button:active {
            transform: translateY(-50%) scale(.92);
        }

        .filter-button {
            height: 42px;
            padding: 0 20px;
            border: 0;
            border-radius: 8px;
            background: #01142f;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
            font-weight: 650;
            transition:
                background .15s ease,
                transform .15s ease;
        }

        .filter-button:hover {
            background: #02214b;
        }

        .filter-button:active {
            transform: scale(.98);
        }

        /* ==========================================================
           CARD
        ========================================================== */

        .card {
            background: #fff;
            border: 1px solid #e7eaf0;
            border-radius: 13px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 17px 20px;
            border-bottom: 1px solid #e7eaf0;
        }

        .card-title {
            margin: 0;
            font-size: 18px;
            line-height: 1.3;
            font-weight: 700;
        }

        .card-period {
            margin-top: 4px;
            color: #6b7280;
            font-size: 13px;
        }

        .products-count {
            color: #6b7280;
            font-size: 13px;
            white-space: nowrap;
        }

        .products-count strong {
            color: #111827;
        }

        /* ==========================================================
           TABLE CONTAINER
        ========================================================== */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .table-wrapper::-webkit-scrollbar {
            height: 7px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: #f1f3f6;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: #cbd1d9;
            border-radius: 10px;
        }

        table {
            width: 100%;
            min-width: 900px;
            border-collapse: separate;
            border-spacing: 0;
        }

        th,
        td {
            padding: 13px 15px;
            border-bottom: 1px solid #e8ebef;
            text-align: left;
            white-space: nowrap;
            font-size: 14px;
        }

        th {
            background: #f8fafc;
            color: #374151;
            font-weight: 700;
            position: relative;
        }

        td {
            color: #111827;
            background: #fff;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover td {
            background: #f8fafc;
        }

        /* ==========================================================
           SORT
        ========================================================== */

        th.sortable {
            cursor: pointer;
            user-select: none;
            transition: background .15s ease;
        }

        th.sortable:hover {
            background: #eef2f7;
        }

        th.sortable:active {
            background: #e5e7eb;
        }

        .sortable-content {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .sort-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 14px;
            color: #01142f;
            font-size: 14px;
            font-weight: 800;
        }

        .sort-arrow.inactive {
            color: #b7bec8;
            font-weight: 500;
        }

        /* ==========================================================
           SKU
        ========================================================== */

        .sku-link {
            color: #01142f;
            font-weight: 700;
            text-decoration: none;
        }

        .sku-link:hover {
            text-decoration: underline;
        }

        /* ==========================================================
           NUMBERS
        ========================================================== */

        .number {
            font-variant-numeric: tabular-nums;
        }

        .positive {
            color: #15803d;
            font-weight: 650;
        }

        .negative {
            color: #b91c1c;
            font-weight: 650;
        }

        /* ==========================================================
           EMPTY
        ========================================================== */

        .empty {
            padding: 45px 20px !important;
            text-align: center !important;
            color: #6b7280 !important;
            background: #fff !important;
        }

        /* ==========================================================
           DETAIL STATS
        ========================================================== */

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
            padding: 16px;
        }

        .stat {
            min-width: 0;
            padding: 14px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #f8fafc;
        }

        .stat-label {
            margin-bottom: 7px;
            color: #6b7280;
            font-size: 11px;
            line-height: 1.3;
        }

        .stat-value {
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 17px;
            line-height: 1.25;
            font-weight: 750;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        /* ==========================================================
           AJAX
        ========================================================== */

        #sales-products-table {
            transition: opacity .15s ease;
        }

        #sales-products-table.ajax-loading {
            opacity: .45;
            pointer-events: none;
        }

        /* ==========================================================
           MOBILE
        ========================================================== */

        @media (max-width: 1100px) {

            .filters {
                grid-template-columns:
                    minmax(140px, 180px)
                    minmax(140px, 180px)
                    minmax(200px, 260px)
                    1fr;
            }

            .filter-button {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 900px) {

            .page {
                padding: 15px;
            }

            .page-header {
                align-items: flex-start;
            }

            .filters {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-group:nth-child(3),
            .filter-group:nth-child(4) {
                grid-column: 1 / -1;
            }

            .filter-button {
                width: 100%;
            }

            .detail-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 650px) {

            body {
                background: #f4f6f9;
            }

            .page {
                padding: 10px;
            }

            .page-header {
                margin-bottom: 12px;
            }

            .page-title {
                font-size: 22px;
                letter-spacing: -.25px;
            }

            .page-subtitle {
                font-size: 12px;
            }

            .back-button {
                min-height: 38px;
                padding: 0 12px;
                font-size: 13px;
            }

            .filters {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
                padding: 11px;
                margin-bottom: 12px;
                border-radius: 11px;
                width: 100%;
                min-width: 0;
            }

            .filters > .filter-group {
                width: 100%;
                min-width: 0;
                max-width: 100%;
            }

            .filters > .filter-group:nth-child(1),
            .filters > .filter-group:nth-child(2) {
                grid-column: span 1;
            }

            .filters > .filter-group:nth-child(1) input[type="date"],
            .filters > .filter-group:nth-child(2) input[type="date"] {
                width: 100%;
                min-width: 0;
                max-width: 100%;
            }

            .filter-group:nth-child(3),
            .filter-group:nth-child(4) {
                grid-column: 1 / -1;
            }

            .filter-group input[type="date"] {
                display: block;
                width: 100%;
                min-width: 0;
                max-width: 100%;
                height: 40px;
                padding: 0 4px;
                font-size: 12px;
                line-height: 40px;
            }

            .filter-group {
                gap: 5px;
            }

            .filter-group label {
                font-size: 11px;
            }

            .filter-group input,
            .filter-group select {
                height: 40px;
                padding: 0 9px;
                font-size: 13px;
                border-radius: 7px;
            }

            .sku-search-wrapper input {
                padding-right: 40px;
            }

            .clear-sku-button {
                width: 27px;
                height: 27px;
                right: 6px;
            }

            .filter-button {
                grid-column: 1 / -1;
                height: 40px;
                font-size: 13px;
                border-radius: 7px;
            }

            .card {
                border-radius: 11px;
            }

            .card-header {
                align-items: flex-start;
                padding: 13px 12px;
            }

            .card-title {
                font-size: 16px;
            }

            .card-period {
                font-size: 11px;
            }

            .products-count {
                font-size: 11px;
            }

            .table-wrapper {
                width: 100%;
                overflow-x: auto;
            }

            table {
                min-width: 790px;
            }

            th,
            td {
                padding: 11px 12px;
                font-size: 12px;
            }

            th {
                font-size: 11px;
            }

            .sort-arrow {
                font-size: 12px;
            }

            .detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
                padding: 10px;
            }

            .stat {
                padding: 11px;
                border-radius: 8px;
            }

            .stat-label {
                margin-bottom: 5px;
                font-size: 10px;
            }

            .stat-value {
                font-size: 14px;
            }
        }

        @media (max-width: 390px) {

            .page {
                padding: 8px;
            }

            .page-header {
                gap: 8px;
            }

            .page-title {
                font-size: 20px;
            }

            .back-button {
                min-height: 36px;
                padding: 0 9px;
                font-size: 12px;
            }

            .filters {
                width: 100%;
                min-width: 0;
                padding: 9px;
                gap: 7px;
            }

            .filters > .filter-group,
            .filters > .filter-group input[type="date"] {
                min-width: 0;
                max-width: 100%;
                width: 100%;
            }

            .filter-group input,
            .filter-group select,
            .filter-button {
                height: 38px;
                font-size: 12px;
            }

            .card-header {
                padding: 11px 10px;
            }

            .card-title {
                font-size: 15px;
            }

            table {
                min-width: 760px;
            }

            th,
            td {
                padding: 10px;
                font-size: 11px;
            }

            .detail-grid {
                gap: 6px;
                padding: 8px;
            }

            .stat {
                padding: 9px;
            }

            .stat-value {
                font-size: 13px;
            }
        }

    </style>

</head>

<body>

<div class="page">

    {{-- ==========================================================
         HEADER
    ========================================================== --}}

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Продажи по товарам
            </h1>

            <div class="page-subtitle">
                Аналитика продаж и прибыли
            </div>

        </div>

        @if(isset($sku) && $sku !== null)

            <a
                href="{{ route('admin.analytics.sales', [
                    'from' => $from->format('Y-m-d'),
                    'to' => $to->format('Y-m-d'),
                    'point_of_sale_id' => $pointOfSaleId ?? null,
                ]) }}"
                class="back-button"
            >
                ← Все товары
            </a>

        @else

            <a
                href="{{ route('admin.analytics.profit') }}"
                class="back-button"
            >
                ← Назад
            </a>

        @endif

    </div>


    {{-- ==========================================================
         FILTERS
    ========================================================== --}}

    <form
        action="{{ isset($sku) && $sku !== null
            ? route('admin.analytics.sales.product', ['sku' => $sku])
            : route('admin.analytics.sales') }}"
        method="GET"
        class="filters"
        id="sales-filters"
    >

        {{-- ОТ --}}

        <div class="filter-group">

            <label for="from">
                От
            </label>

            <input
                type="date"
                name="from"
                id="from"
                value="{{ $from->format('Y-m-d') }}"
            >

        </div>


        {{-- ПО --}}

        <div class="filter-group">

            <label for="to">
                По
            </label>

            <input
                type="date"
                name="to"
                id="to"
                value="{{ $to->format('Y-m-d') }}"
            >

        </div>


        {{-- ТОЧКА ПРОДАЖ --}}

        <div class="filter-group">

            <label for="point_of_sale_id">
                Точка продаж
            </label>

            <select
                name="point_of_sale_id"
                id="point_of_sale_id"
            >

                <option value="">
                    Все точки продаж
                </option>

                @foreach($pointsOfSale ?? [] as $point)

                    <option
                        value="{{ $point->id }}"
                        @selected(
                            (int) ($pointOfSaleId ?? 0) === (int) $point->id
                        )
                    >
                        {{ $point->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- ======================================================
             ПОИСК ПО АРТИКУЛУ
        ======================================================= --}}

        <div class="filter-group">

            <label for="sku">
                Поиск по артикулу
            </label>

            <div class="sku-search-wrapper">

                <input
                    type="text"
                    id="sku"
                    value=""
                    placeholder="Например: 11526"
                    autocomplete="off"
                >

                <button
                    type="button"
                    id="clearSkuSearch"
                    class="clear-sku-button"
                    aria-label="Очистить поиск"
                    title="Очистить поиск"
                >
                    ×
                </button>

            </div>

        </div>


        {{-- ======================================================
             ПРИМЕНИТЬ
        ======================================================= --}}

        <button
            type="submit"
            class="filter-button"
        >
            Применить
        </button>

    </form>


    {{-- ==========================================================
         PRODUCT DETAIL
    ========================================================== --}}

    @if(isset($sku) && $sku !== null)

        <div class="card">

            <div class="card-header">

                <div>

                    <h2 class="card-title">
                        {{ $variant->sku }}
                    </h2>

                </div>

            </div>


            <div class="detail-grid">

                <div class="stat">

                    <div class="stat-label">
                        Продано
                    </div>

                    <div class="stat-value">

                        {{ number_format(
                            $totalQuantity,
                            0,
                            ',',
                            ' '
                        ) }}

                    </div>

                </div>


                <div class="stat">

                    <div class="stat-label">
                        Сумма продаж
                    </div>

                    <div class="stat-value">

                        {{ number_format(
                            $totalSales,
                            2,
                            ',',
                            ' '
                        ) }} ₸

                    </div>

                </div>


                <div class="stat">

                    <div class="stat-label">
                        Себестоимость
                    </div>

                    <div class="stat-value">

                        {{ number_format(
                            $totalCost,
                            2,
                            ',',
                            ' '
                        ) }} ₸

                    </div>

                </div>


                <div class="stat">

                    <div class="stat-label">
                        Средняя цена продажи
                    </div>

                    <div class="stat-value">

                        {{ number_format(
                            $averageSalePrice,
                            2,
                            ',',
                            ' '
                        ) }} ₸

                    </div>

                </div>


                <div class="stat">

                    <div class="stat-label">
                        Средняя себестоимость
                    </div>

                    <div class="stat-value">

                        {{ number_format(
                            $averageCost,
                            2,
                            ',',
                            ' '
                        ) }} ₸

                    </div>

                </div>


                <div class="stat">

                    <div class="stat-label">
                        Прибыль
                    </div>

                    <div
                        class="stat-value {{ $profit >= 0 ? 'positive' : 'negative' }}"
                    >

                        {{ number_format(
                            $profit,
                            2,
                            ',',
                            ' '
                        ) }} ₸

                    </div>

                </div>

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Заказ
                            </th>

                            <th>
                                Дата
                            </th>

                            <th>
                                Время
                            </th>

                            <th>
                                Количество
                            </th>

                            <th>
                                Цена продажи
                            </th>

                            <th>
                                Средняя себестоимость
                            </th>

                            <th>
                                Сумма продажи
                            </th>

                            <th>
                                Себестоимость
                            </th>

                            <th>
                                Прибыль
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($rows as $row)

                            <tr>

                                <td>
                                    #{{ $row['order_id'] }}
                                </td>

                                <td>
                                    {{ $row['date'] }}
                                </td>

                                <td>
                                    {{ $row['time'] }}
                                </td>

                                <td class="number">

                                    {{ number_format(
                                        $row['quantity'],
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                </td>

                                <td class="number">

                                    {{ number_format(
                                        $row['sale_price'],
                                        2,
                                        ',',
                                        ' '
                                    ) }} ₸

                                </td>

                                <td class="number">

                                    {{ number_format(
                                        $row['average_cost'],
                                        2,
                                        ',',
                                        ' '
                                    ) }} ₸

                                </td>

                                <td class="number">

                                    {{ number_format(
                                        $row['sales_amount'],
                                        2,
                                        ',',
                                        ' '
                                    ) }} ₸

                                </td>

                                <td class="number">

                                    {{ number_format(
                                        $row['cost_amount'],
                                        2,
                                        ',',
                                        ' '
                                    ) }} ₸

                                </td>

                                <td
                                    class="number {{ $row['profit'] >= 0
                                        ? 'positive'
                                        : 'negative' }}"
                                >

                                    {{ number_format(
                                        $row['profit'],
                                        2,
                                        ',',
                                        ' '
                                    ) }} ₸

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="9"
                                    class="empty"
                                >
                                    Продаж за выбранный период нет
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


    @else


        {{-- ======================================================
             ALL PRODUCTS
        ======================================================= --}}

        <div class="card">

            <div class="card-header">

                <div>

                    <h2 class="card-title">
                        Все товары
                    </h2>

                    <div class="card-period">

                        Период:
                        {{ $from->format('d.m.Y') }}
                        —
                        {{ $to->format('d.m.Y') }}

                    </div>

                </div>




            </div>


            {{-- ==================================================
                 AJAX TABLE
            ================================================== --}}

            <div
                id="sales-products-table"
                class="table-wrapper"
            >

                @include(
                    'admin.analytics.partials.sales-products-table',
                    [
                        'products' => $products,
                        'sortBy' => 'sales',
                        'sortDirection' => 'desc',
                    ]
                )

            </div>

        </div>

    @endif

</div>


{{-- ==============================================================
     AJAX SORTING + SEARCH
================================================================= --}}

@if(!isset($sku) || $sku === null)

<script>
document.addEventListener('DOMContentLoaded', function () {

    const tableContainer =
        document.getElementById('sales-products-table');

    if (!tableContainer) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | СОСТОЯНИЕ СОРТИРОВКИ
    |--------------------------------------------------------------------------
    */

    let currentSort = 'sales';
    let currentDirection = 'desc';


    /*
    |--------------------------------------------------------------------------
    | ЭЛЕМЕНТЫ
    |--------------------------------------------------------------------------
    */

    const skuInput =
        document.getElementById('sku');

    const clearSkuButton =
        document.getElementById('clearSkuSearch');

    const filters =
        document.getElementById('sales-filters');

    const fromInput =
        document.getElementById('from');

    const toInput =
        document.getElementById('to');

    const pointOfSaleInput =
        document.getElementById('point_of_sale_id');


    /*
    |--------------------------------------------------------------------------
    | TIMER ПОИСКА
    |--------------------------------------------------------------------------
    */

    let skuSearchTimer = null;


    /*
    |--------------------------------------------------------------------------
    | ПОКАЗАТЬ / СКРЫТЬ КРЕСТИК
    |--------------------------------------------------------------------------
    */

    function updateClearSkuButton() {

        if (!skuInput || !clearSkuButton) {
            return;
        }

        clearSkuButton.classList.toggle(
            'visible',
            skuInput.value.trim() !== ''
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX ЗАГРУЗКА ТАБЛИЦЫ
    |--------------------------------------------------------------------------
    */

    async function loadProductsTable(
        sortBy = currentSort,
        sortDirection = currentDirection
    ) {

        const params =
            new URLSearchParams();


        /*
        |--------------------------------------------------------------------------
        | ДАТА ОТ
        |--------------------------------------------------------------------------
        */

        if (
            fromInput &&
            fromInput.value
        ) {

            params.set(
                'from',
                fromInput.value
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ДАТА ДО
        |--------------------------------------------------------------------------
        */

        if (
            toInput &&
            toInput.value
        ) {

            params.set(
                'to',
                toInput.value
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ТОЧКА ПРОДАЖ
        |--------------------------------------------------------------------------
        */

        if (
            pointOfSaleInput &&
            pointOfSaleInput.value
        ) {

            params.set(
                'point_of_sale_id',
                pointOfSaleInput.value
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ПОИСК ПО АРТИКУЛУ
        |--------------------------------------------------------------------------
        */

        if (
            skuInput &&
            skuInput.value.trim() !== ''
        ) {

            params.set(
                'sku',
                skuInput.value.trim()
            );
        }


        /*
        |--------------------------------------------------------------------------
        | СОРТИРОВКА
        |--------------------------------------------------------------------------
        */

        params.set(
            'sort_by',
            sortBy
        );

        params.set(
            'sort_direction',
            sortDirection
        );


        /*
        |--------------------------------------------------------------------------
        | LOADING
        |--------------------------------------------------------------------------
        */

        tableContainer.classList.add(
            'ajax-loading'
        );


        try {

            const url =
                '{{ route("admin.analytics.sales.product.table") }}'
                + '?'
                + params.toString();


            const response =
                await fetch(
                    url,
                    {
                        method: 'GET',

                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'text/html'
                        }
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'HTTP ' +
                    response.status
                );
            }


            const html =
                await response.text();


            /*
            |--------------------------------------------------------------------------
            | ЗАМЕНЯЕМ ТАБЛИЦУ
            |--------------------------------------------------------------------------
            */

            tableContainer.innerHTML =
                html;


            /*
            |--------------------------------------------------------------------------
            | СОХРАНЯЕМ СОРТИРОВКУ
            |--------------------------------------------------------------------------
            */

            currentSort =
                sortBy;

            currentDirection =
                sortDirection;


        } catch (error) {

            console.error(
                'AJAX ошибка:',
                error
            );

            alert(
                'Не удалось обновить таблицу.\n\n' +
                error.message
            );


        } finally {

            tableContainer.classList.remove(
                'ajax-loading'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ПОИСК ПО АРТИКУЛУ
    |--------------------------------------------------------------------------
    |
    | При вводе SKU таблица обновляется автоматически.
    | Кнопка "Применить" здесь не нужна.
    |
    */

    if (skuInput) {

        skuInput.addEventListener(
            'input',
            function () {

                updateClearSkuButton();


                clearTimeout(
                    skuSearchTimer
                );


                skuSearchTimer =
                    setTimeout(
                        function () {

                            loadProductsTable(
                                currentSort,
                                currentDirection
                            );

                        },
                        300
                    );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | КРЕСТИК — ОЧИСТИТЬ ПОИСК
    |--------------------------------------------------------------------------
    */

    if (
        skuInput &&
        clearSkuButton
    ) {

        clearSkuButton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();
                event.stopPropagation();


                clearTimeout(
                    skuSearchTimer
                );


                skuInput.value = '';


                updateClearSkuButton();


                loadProductsTable(
                    currentSort,
                    currentDirection
                );


                skuInput.focus();
            }
        );


        updateClearSkuButton();
    }


    /*
    |--------------------------------------------------------------------------
    | КНОПКА "ПРИМЕНИТЬ"
    |--------------------------------------------------------------------------
    |
    | ВАЖНО:
    |
    | Здесь теперь event.preventDefault().
    |
    | Поэтому обычная GET-форма НЕ отправляется.
    | Страница НЕ перезагружается.
    | URL НЕ меняется.
    |
    | AJAX получает:
    |
    | from
    | to
    | point_of_sale_id
    | sku
    | sort_by
    | sort_direction
    |
    */

    if (filters) {

        filters.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();


                /*
                |--------------------------------------------------------------------------
                | Если пользователь быстро печатал SKU,
                | отменяем отложенный поиск.
                |--------------------------------------------------------------------------
                */

                clearTimeout(
                    skuSearchTimer
                );


                /*
                |--------------------------------------------------------------------------
                | Применяем период + точку продаж
                | через AJAX.
                |
                | Текущий SKU тоже сохраняется.
                |--------------------------------------------------------------------------
                */

                loadProductsTable(
                    currentSort,
                    currentDirection
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | СОРТИРОВКА ПО ЗАГОЛОВКУ
    |--------------------------------------------------------------------------
    */

    tableContainer.addEventListener(
        'click',
        function (event) {

            const header =
                event.target.closest(
                    '.sortable'
                );


            if (!header) {
                return;
            }


            const sortBy =
                header.dataset.sort;


            if (!sortBy) {
                return;
            }


            let direction =
                'desc';


            /*
            |--------------------------------------------------------------------------
            | Если нажали на ту же колонку —
            | меняем направление
            |--------------------------------------------------------------------------
            */

            if (
                currentSort === sortBy
            ) {

                direction =
                    currentDirection === 'asc'
                        ? 'desc'
                        : 'asc';
            }


            /*
            |--------------------------------------------------------------------------
            | Сортировка AJAX
            |--------------------------------------------------------------------------
            */

            loadProductsTable(
                sortBy,
                direction
            );
        }
    );

});
</script>
@endif

</body>

</html>