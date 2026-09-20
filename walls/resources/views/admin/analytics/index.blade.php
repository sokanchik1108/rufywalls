<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Аналитика</title>
</head>

<body>

    <style>
        :root {
            --bg: #f4f5f7;
            --surface: #ffffff;
            --ink: #1a1d21;
            --ink-soft: #6e7580;
            --ink-faint: #9aa0a8;
            --border: #dfe3e8;

            --primary: #01142f;
            --primary-hover: #02214b;
            --primary-soft: #eaf1fe;

            --danger: #e5484d;
            --danger-soft: #fdeceb;

            --success: #1c9a6c;
            --success-soft: #e6f6ef;

            --radius: 6px;
            --radius-lg: 8px;
        }

        .filter-group:nth-child(3) {
            grid-column: 1 / -1;
        }


        /* =========================
   RESET
========================= */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }


        html {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }


        body {
            margin: 0;
            width: 100%;
            max-width: 100%;
            background: var(--bg);
            font-family: sans-serif;
            color: var(--ink);
            font-size: 14px;
            overflow-x: hidden;
        }


        /* =========================
   MAIN CONTAINER
========================= */

        .analytics-container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }


        /* =========================
   NAVIGATION
========================= */

        .analytics-navigation {
            margin-bottom: 18px;
        }


        .analytics-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            height: 36px;
            padding: 0 13px;

            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;

            color: #374151;
            text-decoration: none;

            font-size: 13px;
            font-weight: 600;

            transition: .15s ease;
        }


        .analytics-nav-btn:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            color: #111827;
        }


        /* =========================
   HEADER
========================= */

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }


        .page-header h5 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }


        /* =========================
   FILTER
========================= */

        .filter-card {
            width: 100%;
            max-width: 100%;

            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);

            padding: 14px;
            margin-bottom: 16px;

            overflow: hidden;
        }


        .filter-form {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr) minmax(0, 1fr);

            gap: 8px;

            width: 100%;
            max-width: 100%;
        }


        .filter-group {
            flex: 1 1 0;
            min-width: 0;
            width: 100%;
        }


        .filter-label {
            display: block;

            font-size: 11px;
            color: var(--ink-soft);

            margin-bottom: 5px;
        }


        /* =========================
   INPUTS
========================= */

        .form-control {
            display: block;

            width: 100%;
            max-width: 100%;
            min-width: 0;

            height: 36px;

            padding: 0 10px;

            background: #fff;

            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;

            font-family: inherit;
            font-size: 13px;

            box-shadow: none !important;
            outline: none;

            box-sizing: border-box;
        }


        .form-control:focus {
            border-color: var(--primary) !important;

            box-shadow:
                0 0 0 3px var(--primary-soft) !important;

            outline: none;
        }


        /* =========================
   DATE INPUT
========================= */

        input[type="date"] {
            -webkit-appearance: none;
            appearance: none;

            box-sizing: border-box;

            width: 100%;
            max-width: 100%;
            min-width: 0;

            height: 36px;
            line-height: 34px;

            padding: 0 8px;

            font-family: inherit;
            font-size: 13px;

            color: var(--ink);

            background: #fff;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;

            outline: none;
        }


        input[type="date"]:focus {
            border-color: var(--primary) !important;

            box-shadow:
                0 0 0 3px var(--primary-soft) !important;

            outline: none;
        }


        input[type="date"]::-webkit-date-and-time-value {
            text-align: left;
            margin: 0;
            padding: 0;
        }


        input[type="date"]::-webkit-calendar-picker-indicator {
            margin-left: auto;
            padding: 0;

            width: 16px;
            height: 16px;

            opacity: .6;

            cursor: pointer;
        }


        input[type="date"]::-webkit-inner-spin-button {
            display: none;
        }


        /* =========================
   PRIMARY BUTTON
========================= */

        .btn-primary {
            flex: 0 0 auto;

            height: 36px;

            padding: 0 18px;

            background: var(--primary);
            border: none;

            color: white;

            font-weight: 600;
            font-size: 13px;

            border-radius: var(--radius) !important;

            cursor: pointer;

            white-space: nowrap;
        }


        .btn-primary:hover {
            background: var(--primary-hover);
            color: white;
        }


        /* =========================
   STAT CARDS
========================= */

        .stats-grid {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 10px;

            margin-bottom: 16px;

            width: 100%;
        }


        .stat-card {
            min-width: 0;

            background: var(--surface);

            border: 1px solid var(--border);
            border-radius: var(--radius-lg);

            padding: 16px;
        }


        .stat-label {
            font-size: 12px;
            color: var(--ink-soft);

            margin-bottom: 7px;
        }


        .stat-value {
            font-size: 20px;

            font-weight: 700;

            letter-spacing: -0.02em;

            overflow-wrap: anywhere;
        }


        .stat-small {
            margin-top: 5px;

            font-size: 11px;

            color: var(--ink-faint);
        }


        .stat-sales .stat-value {
            color: var(--success);
        }


        .stat-returns .stat-value {
            color: var(--danger);
        }


        .stat-profit .stat-value {
            color: var(--primary);
        }


        /* =========================
   SECONDARY
========================= */

        .secondary-grid {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 10px;

            margin-bottom: 16px;

            width: 100%;
        }


        .secondary-card {
            min-width: 0;

            background: var(--surface);

            border: 1px solid var(--border);
            border-radius: var(--radius-lg);

            padding: 16px;
        }


        .secondary-title {
            font-size: 12px;

            color: var(--ink-soft);

            margin-bottom: 7px;
        }


        .secondary-value {
            font-size: 18px;

            font-weight: 700;

            color: var(--primary);
        }


        /* =========================
   SECTION
========================= */

        .section-card {
            width: 100%;
            max-width: 100%;

            background: var(--surface);

            border: 1px solid var(--border);
            border-radius: var(--radius-lg);

            overflow: hidden;

            margin-bottom: 16px;
        }


        .section-header {
            display: flex;

            justify-content: space-between;
            align-items: center;

            gap: 15px;

            padding: 14px 16px;

            border-bottom: 1px solid var(--border);
        }


        .section-title {
            font-size: 14px;

            font-weight: 700;
        }


        .section-description {
            font-size: 11px;

            color: var(--ink-faint);
        }


        /* =========================
   CHART
========================= */

        .chart-container {
            position: relative;

            width: 100%;

            height: 340px;

            padding: 20px;
        }


        /* =========================
   DAYS SUMMARY
========================= */

        .days-summary {
            display: flex;

            align-items: center;
            justify-content: space-between;

            gap: 20px;

            padding: 18px;

            width: 100%;
        }


        .days-summary-info {
            display: flex;

            align-items: center;

            gap: 30px;

            flex-wrap: wrap;

            min-width: 0;
        }


        .days-summary-item {
            display: flex;

            flex-direction: column;

            gap: 4px;

            min-width: 0;
        }


        .days-summary-label {
            font-size: 11px;

            color: var(--ink-faint);
        }


        .days-summary-value {
            font-size: 15px;

            font-weight: 700;

            white-space: nowrap;
        }


        .details-btn {
            flex: 0 0 auto;

            border: 1px solid var(--border);

            background: white;

            color: var(--ink);

            border-radius: 6px;

            padding: 9px 14px;

            font-size: 12px;

            font-weight: 600;

            cursor: pointer;

            transition: .15s;

            white-space: nowrap;
        }


        .details-btn:hover {
            border-color: var(--primary);

            color: var(--primary);

            background: var(--primary-soft);
        }


        /* =========================
   PRODUCTS / VARIANTS
========================= */

        .product-rank {
            width: 28px;
            height: 28px;

            flex: 0 0 28px;

            border-radius: 50%;

            background: #f4f5f7;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 11px;

            font-weight: 700;

            color: var(--ink-soft);
        }


        .product-rank.first {
            background: var(--primary-soft);

            color: var(--primary);
        }


        .product-name {
            font-weight: 600;

            color: var(--ink);
        }


        .product-product-name {
            margin-top: 3px;

            font-size: 11px;

            color: var(--ink-faint);

            font-weight: 400;
        }


        .product-color {
            margin-top: 3px;

            font-size: 11px;

            color: var(--ink-soft);

            font-weight: 400;
        }


        .product-quantity {
            font-weight: 700;
        }


        /* =========================
   MODAL
========================= */

        .analytics-modal {
            display: none;

            position: fixed;

            z-index: 9999;

            inset: 0;

            width: 100%;
            height: 100%;

            background: rgba(15, 20, 28, .45);

            padding: 30px 15px;

            overflow-y: auto;

            overflow-x: hidden;
        }


        .analytics-modal.active {
            display: flex;

            align-items: flex-start;

            justify-content: center;
        }


        .analytics-modal-content {
            width: 100%;
            max-width: 1100px;

            background: white;

            border-radius: 10px;

            box-shadow:
                0 20px 60px rgba(0, 0, 0, .2);

            overflow: hidden;

            animation: modalOpen .18s ease-out;
        }


        @keyframes modalOpen {

            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }


        .modal-header-custom {
            display: flex;

            align-items: center;
            justify-content: space-between;

            gap: 15px;

            padding: 16px 18px;

            border-bottom: 1px solid var(--border);
        }


        .modal-title-custom {
            font-size: 15px;

            font-weight: 700;
        }


        .modal-close {
            flex: 0 0 auto;

            border: none;

            background: transparent;

            font-size: 22px;

            line-height: 1;

            color: var(--ink-soft);

            cursor: pointer;

            padding: 0 4px;
        }


        .modal-close:hover {
            color: var(--ink);
        }


        .modal-body-custom {
            max-height: 70vh;

            overflow-y: auto;
            overflow-x: auto;
        }


        /* =========================
   TABLE
========================= */

        .table-wrap {
            width: 100%;

            overflow-x: auto;

            -webkit-overflow-scrolling: touch;
        }


        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 700px;
        }


        th {
            text-align: left;

            padding: 10px 16px;

            font-size: 11px;

            color: var(--ink-faint);

            font-weight: 600;

            background: #fafbfc;

            border-bottom: 1px solid var(--border);

            white-space: nowrap;
        }


        td {
            padding: 11px 16px;

            font-size: 13px;

            border-bottom: 1px solid var(--border);

            white-space: nowrap;
        }


        tr:last-child td {
            border-bottom: none;
        }


        .text-right {
            text-align: right;
        }


        .sales-value {
            color: var(--success);

            font-weight: 700;
        }


        .return-value {
            color: var(--danger);

            font-weight: 600;
        }


        .profit-value {
            color: var(--primary);

            font-weight: 700;
        }


        .order-count {
            color: var(--ink-soft);
        }


        /* =========================
   TABLET
========================= */

        @media (max-width: 800px) {

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }


        /* =========================
   MOBILE
========================= */

        @media (max-width: 600px) {

            .analytics-container {
                width: 100%;
                max-width: 100%;

                padding: 16px 10px 30px;
            }


            .analytics-navigation {
                margin-bottom: 14px;
            }


            .analytics-nav-btn {
                height: 34px;

                font-size: 12px;

                padding: 0 11px;
            }


            /* =========================
       FILTER MOBILE
    ========================= */

            .filter-card {
                width: 100%;
                max-width: 100%;

                padding: 12px;

                overflow: hidden;
            }


            .filter-form {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr) minmax(0, 1fr);

                gap: 8px;

                width: 100%;
                max-width: 100%;
            }


            .filter-group {
                width: 100%;
                min-width: 0;
                max-width: 100%;
            }


            .filter-label {
                font-size: 11px;

                margin-bottom: 5px;
            }


            /* Инпуты даты и текстовые — одинаковый ровный вид */
            .filter-form .form-control,
            .filter-form input[type="date"] {
                display: block;

                width: 100%;
                max-width: 100%;
                min-width: 0;

                height: 40px;
                line-height: 38px;

                box-sizing: border-box;

                padding-left: 8px;
                padding-right: 8px;

                font-size: 13px;

                overflow: hidden;
            }


            /* Кнопка на всю ширину */
            .filter-form .btn-primary {
                grid-column: 1 / -1;

                width: 100%;
                max-width: 100%;
                min-width: 0;

                height: 40px;

                padding: 0 12px;
            }


            /* =========================
       STATS
    ========================= */

            .stats-grid {
                grid-template-columns: 1fr 1fr;

                gap: 8px;
            }


            .stat-card {
                min-width: 0;

                padding: 13px;
            }


            .stat-value {
                font-size: 17px;

                word-break: break-word;
            }


            .stat-label {
                font-size: 11px;
            }


            .stat-small {
                font-size: 10px;

                line-height: 1.4;
            }


            /* =========================
       SECONDARY
    ========================= */

            .secondary-grid {
                grid-template-columns: 1fr;

                gap: 8px;
            }


            .secondary-card {
                padding: 13px;

                min-width: 0;
            }


            .secondary-value {
                font-size: 17px;
            }


            /* =========================
       CHART
    ========================= */

            .chart-container {
                width: 100%;

                height: 330px;

                padding: 10px 4px 20px;
            }


            /* =========================
       DAYS SUMMARY
    ========================= */

            .days-summary {
                align-items: flex-start;

                flex-direction: column;

                gap: 15px;

                padding: 15px;
            }


            .days-summary-info {
                width: 100%;

                gap: 18px;
            }


            .days-summary-item {
                min-width: 70px;
            }


            .days-summary-value {
                font-size: 14px;
            }


            .details-btn {
                width: 100%;

                max-width: 100%;
            }


            /* =========================
       SECTION HEADER
    ========================= */

            .section-header {
                align-items: flex-start;

                padding: 13px;

                gap: 10px;
            }


            .section-title {
                font-size: 13px;
            }


            .section-description {
                font-size: 10px;

                line-height: 1.4;
            }


            .section-header .details-btn {
                width: auto;

                flex: 0 0 auto;

                padding: 8px 10px;
            }


            /* =========================
       MODAL
    ========================= */

            .analytics-modal {
                padding: 10px;
            }


            .analytics-modal-content {
                width: 100%;
                max-width: 100%;

                border-radius: 8px;
            }


            .modal-header-custom {
                padding: 14px;

                gap: 10px;
            }


            .modal-title-custom {
                font-size: 14px;
            }


            .modal-body-custom {
                max-height: 75vh;

                overflow-x: auto;
            }


            /* =========================
       TABLE MOBILE
    ========================= */

            .table-wrap {
                width: 100%;

                overflow-x: auto;
            }


            th,
            td {
                padding: 10px 12px;
            }

        }


        /* =========================
   VERY SMALL PHONES
========================= */

        @media (max-width: 380px) {

            .analytics-container {
                padding-left: 8px;
                padding-right: 8px;
            }


            .filter-card {
                padding: 10px;
            }


            .filter-form {
                gap: 6px;
            }


            .filter-form input[type="date"] {
                padding-left: 6px;
                padding-right: 6px;

                font-size: 12px;
            }


            .stats-grid {
                gap: 6px;
            }


            .stat-card {
                padding: 11px;
            }


            .stat-value {
                font-size: 16px;
            }


            .days-summary {
                padding: 13px;
            }

        }
    </style>


    <div class="analytics-container">


        {{-- =========================
         НАВИГАЦИЯ
    ========================== --}}

        <div class="analytics-navigation">

            <a
                href="{{ route('admin.analytics.menu') }}"
                class="analytics-nav-btn">

                ← В меню аналитики

            </a>

        </div>


        {{-- =========================
         HEADER
    ========================== --}}

        <div class="page-header">

            <h5>
                Аналитика
            </h5>

        </div>


        {{-- =========================
         ФИЛЬТР
    ========================== --}}

        <div class="filter-card">

            <form
                method="GET"
                action="{{ route('admin.analytics.index') }}"
                class="filter-form">


                <div class="filter-group">

                    <label class="filter-label">
                        От
                    </label>

                    <input
                        type="date"
                        name="from"
                        value="{{ $from->format('Y-m-d') }}"
                        class="form-control">

                </div>


                <div class="filter-group">

                    <label class="filter-label">
                        По
                    </label>

                    <input
                        type="date"
                        name="to"
                        value="{{ $to->format('Y-m-d') }}"
                        class="form-control">

                </div>


                <div class="filter-group">

                    <label class="filter-label">
                        Точка продаж
                    </label>

                    <select
                        name="point_of_sale_id"
                        class="form-control">

                        <option value="">
                            Все точки
                        </option>

                        @foreach($pointsOfSale as $point)

                        <option
                            value="{{ $point->id }}"
                            @selected($selectedPointOfSale==$point->id)>

                            {{ $point->name }}

                        </option>

                        @endforeach

                    </select>

                </div>


                <button
                    type="submit"
                    class="btn-primary">

                    <i class="bi bi-funnel"></i>

                    Показать

                </button>

            </form>

        </div>


        {{-- =========================
         ОСНОВНЫЕ ПОКАЗАТЕЛИ
    ========================== --}}

        <div class="stats-grid">


            <div class="stat-card stat-sales">

                <div class="stat-label">
                    Продажи
                </div>

                <div class="stat-value">
                    {{ number_format($totalSales, 0, '.', ' ') }} ₸
                </div>

                <div class="stat-small">
                    общая сумма продаж
                </div>

            </div>


            <div class="stat-card stat-returns">

                <div class="stat-label">
                    Возвраты
                </div>

                <div class="stat-value">
                    {{ number_format($totalReturns, 0, '.', ' ') }} ₸
                </div>

                <div class="stat-small">
                    общая сумма возвратов
                </div>

            </div>


            <div class="stat-card stat-profit">

                <div class="stat-label">
                    Итог
                </div>

                <div class="stat-value">
                    {{ number_format($totalProfit, 0, '.', ' ') }} ₸
                </div>

                <div class="stat-small">
                    продажи − возвраты
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-label">
                    Заказы
                </div>

                <div class="stat-value">
                    {{ number_format($totalOrders, 0, '.', ' ') }}
                </div>

                <div class="stat-small">
                    количество заказов
                </div>

            </div>


        </div>


        {{-- =========================
         СЕГОДНЯ / МЕСЯЦ
    ========================== --}}

        <div class="secondary-grid">


            <div class="secondary-card">

                <div class="secondary-title">
                    Сегодня
                </div>

                <div class="secondary-value">
                    {{ number_format($todayProfit, 0, '.', ' ') }} ₸
                </div>

                <div class="stat-small">

                    Продажи:
                    {{ number_format($todaySales, 0, '.', ' ') }} ₸

                    &nbsp; · &nbsp;

                    Возвраты:
                    {{ number_format($todayReturns, 0, '.', ' ') }} ₸

                    &nbsp; · &nbsp;

                    {{ $todayOrders->count() }} заказов

                </div>

            </div>


            <div class="secondary-card">

                <div class="secondary-title">
                    Текущий месяц
                </div>

                <div class="secondary-value">
                    {{ number_format($monthProfit, 0, '.', ' ') }} ₸
                </div>

                <div class="stat-small">

                    Продажи:
                    {{ number_format($monthSales, 0, '.', ' ') }} ₸

                    &nbsp; · &nbsp;

                    Возвраты:
                    {{ number_format($monthReturns, 0, '.', ' ') }} ₸

                    &nbsp; · &nbsp;

                    {{ $monthOrders->count() }} заказов

                </div>

            </div>


        </div>


        {{-- =========================
         ГРАФИК
    ========================== --}}

        <div class="section-card">


            <div class="section-header">

                <span class="section-title">
                    Торговля по дням недели
                </span>

                <span class="section-description">
                    Средний итог за день
                </span>

            </div>


            <div class="chart-container">

                <canvas id="weeklySalesChart"></canvas>

            </div>


        </div>


        {{-- =========================
         ПРОДАЖИ ПО ДНЯМ
    ========================== --}}

        <div class="section-card">


            <div class="section-header">

                <span class="section-title">
                    Продажи по дням
                </span>

            </div>


            @php

            $daysWithSales = count($salesByDay);

            $periodSales = collect($salesByDay)->sum('sales');

            $periodReturns = collect($salesByDay)->sum('returns');

            $periodProfit = collect($salesByDay)->sum('profit');

            @endphp


            <div class="days-summary">


                <div class="days-summary-info">


                    <div class="days-summary-item">

                        <span class="days-summary-label">
                            Дней с заказами
                        </span>

                        <span class="days-summary-value">
                            {{ $daysWithSales }}
                        </span>

                    </div>


                    <div class="days-summary-item">

                        <span class="days-summary-label">
                            Продажи
                        </span>

                        <span class="days-summary-value sales-value">
                            {{ number_format($periodSales, 0, '.', ' ') }} ₸
                        </span>

                    </div>


                    <div class="days-summary-item">

                        <span class="days-summary-label">
                            Возвраты
                        </span>

                        <span class="days-summary-value return-value">
                            {{ number_format($periodReturns, 0, '.', ' ') }} ₸
                        </span>

                    </div>


                    <div class="days-summary-item">

                        <span class="days-summary-label">
                            Итог
                        </span>

                        <span class="days-summary-value profit-value">
                            {{ number_format($periodProfit, 0, '.', ' ') }} ₸
                        </span>

                    </div>


                </div>


                <button
                    type="button"
                    class="details-btn"
                    onclick="openSalesDaysModal()">

                    Посмотреть дни

                    <i class="bi bi-arrow-right"></i>

                </button>


            </div>


        </div>


        {{-- =========================================================
         САМЫЕ ПРОДАВАЕМЫЕ ВАРИАНТЫ
    ========================================================== --}}

        <div class="section-card">


            <div class="section-header">


                <div>

                    <div class="section-title">
                        Самые продаваемые варианты
                    </div>

                    <div class="section-description">
                        Варианты с наибольшим количеством проданных единиц за выбранный период
                    </div>

                </div>


                @if(isset($productStats) && $productStats->count() > 10)

                <button
                    type="button"
                    class="details-btn"
                    onclick="openProductsModal()">

                    <i class="bi bi-list"></i>

                    Подробнее

                </button>

                @endif


            </div>


            @if(isset($topProducts) && $topProducts->count())


            <div class="table-wrap">

                <table>


                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Вариант / SKU
                            </th>

                            <th class="text-right">
                                Продано
                            </th>

                            <th class="text-right">
                                Возврат
                            </th>

                            <th class="text-right">
                                Продажи
                            </th>

                            {{-- ДОБАВЛЕНО --}}

                            <th class="text-right">
                                Сумма возвратов
                            </th>

                            <th class="text-right">
                                Итог
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        @foreach($topProducts as $index => $variant)


                        <tr>


                            {{-- № --}}

                            <td>

                                <div
                                    class="product-rank {{ $index === 0 ? 'first' : '' }}">

                                    {{ $index + 1 }}

                                </div>

                            </td>


                            {{-- SKU --}}

                            <td>

                                <div class="product-name">

                                    {{ $variant['sku'] ?? '—' }}

                                </div>

                            </td>


                            {{-- ПРОДАНО --}}

                            <td class="text-right product-quantity">

                                {{ number_format(
                                        $variant['sold_quantity'] ?? 0,
                                        0,
                                        '.',
                                        ' '
                                    ) }}

                            </td>


                            {{-- ВОЗВРАТ ШТУК --}}

                            <td class="text-right return-value">

                                {{ number_format(
                                        $variant['return_quantity'] ?? 0,
                                        0,
                                        '.',
                                        ' '
                                    ) }}

                            </td>


                            {{-- ПРОДАЖИ --}}

                            <td class="text-right sales-value">

                                {{ number_format(
                                        $variant['sales'] ?? 0,
                                        0,
                                        '.',
                                        ' '
                                    ) }} ₸

                            </td>


                            {{-- СУММА ВОЗВРАТОВ --}}

                            <td class="text-right return-value">

                                {{ number_format(
                                        $variant['returns'] ?? 0,
                                        0,
                                        '.',
                                        ' '
                                    ) }} ₸

                            </td>


                            {{-- ИТОГ --}}

                            <td class="text-right profit-value">

                                {{ number_format(
                                        $variant['profit'] ?? 0,
                                        0,
                                        '.',
                                        ' '
                                    ) }} ₸

                            </td>


                        </tr>


                        @endforeach


                    </tbody>


                </table>

            </div>


            @else


            <div style="
                padding:30px;
                text-align:center;
                color:var(--ink-faint);
            ">

                За выбранный период продаж вариантов нет.

            </div>


            @endif


        </div>


    </div>


    {{-- =========================================================
     MODAL: ПРОДАЖИ ПО ДНЯМ
========================================================= --}}

    <div
        id="salesDaysModal"
        class="analytics-modal"
        onclick="closeSalesDaysModal(event)">


        <div
            class="analytics-modal-content"
            onclick="event.stopPropagation()">


            <div class="modal-header-custom">


                <div>

                    <div class="modal-title-custom">
                        Продажи по дням
                    </div>

                    <div
                        class="section-description"
                        style="margin-top:4px;">

                        {{ $from->format('d.m.Y') }}

                        —

                        {{ $to->format('d.m.Y') }}

                    </div>

                </div>


                <button
                    type="button"
                    class="modal-close"
                    onclick="closeSalesDaysModal()">

                    &times;

                </button>


            </div>


            <div class="modal-body-custom">


                <div class="table-wrap">


                    <table>


                        <thead>

                            <tr>

                                <th>
                                    Дата
                                </th>

                                <th>
                                    День недели
                                </th>

                                <th>
                                    Заказы
                                </th>

                                <th class="text-right">
                                    Продажи
                                </th>

                                <th class="text-right">
                                    Возвраты
                                </th>

                                <th class="text-right">
                                    Итог
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            @forelse($salesByDay as $day)


                            @php

                            $carbonDate = \Carbon\Carbon::parse(
                            $day['date'],
                            'Asia/Almaty'
                            );

                            @endphp


                            <tr>


                                <td>
                                    {{ $carbonDate->format('d.m.Y') }}
                                </td>


                                <td>
                                    {{ $carbonDate->locale('ru')->translatedFormat('l') }}
                                </td>


                                <td class="order-count">
                                    {{ $day['orders'] }}
                                </td>


                                <td class="text-right sales-value">

                                    {{ number_format(
                                        $day['sales'],
                                        0,
                                        '.',
                                        ' '
                                    ) }} ₸

                                </td>


                                <td class="text-right return-value">

                                    {{ number_format(
                                        $day['returns'],
                                        0,
                                        '.',
                                        ' '
                                    ) }} ₸

                                </td>


                                <td class="text-right profit-value">

                                    {{ number_format(
                                        $day['profit'],
                                        0,
                                        '.',
                                        ' '
                                    ) }} ₸

                                </td>


                            </tr>


                            @empty


                            <tr>

                                <td
                                    colspan="6"
                                    style="
                                        text-align:center;
                                        color:var(--ink-faint);
                                        padding:30px;
                                    ">

                                    За выбранный период заказов нет

                                </td>

                            </tr>


                            @endforelse


                        </tbody>


                    </table>


                </div>


            </div>


        </div>


    </div>


    {{-- =========================================================
     MODAL: ВСЕ ВАРИАНТЫ
========================================================= --}}

    <div
        id="productsModal"
        class="analytics-modal"
        onclick="closeProductsModal(event)">


        <div
            class="analytics-modal-content"
            onclick="event.stopPropagation()">


            <div class="modal-header-custom">


                <div>

                    <div class="modal-title-custom">
                        Продажи по вариантам
                    </div>


                    <div
                        class="section-description"
                        style="margin-top:4px;">

                        {{ $from->format('d.m.Y') }}

                        —

                        {{ $to->format('d.m.Y') }}

                        @if(isset($productStats))

                        &nbsp; · &nbsp;

                        {{ $productStats->count() }}

                        вариантов

                        @endif

                    </div>


                </div>


                <button
                    type="button"
                    class="modal-close"
                    onclick="closeProductsModal()">

                    &times;

                </button>


            </div>


            <div class="modal-body-custom">


                @if(isset($productStats) && $productStats->count())


                <div class="table-wrap">


                    <table>


                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Вариант / SKU
                                </th>

                                <th class="text-right">
                                    Продано
                                </th>

                                <th class="text-right">
                                    Возврат
                                </th>

                                <th class="text-right">
                                    Продажи
                                </th>

                                <th class="text-right">
                                    Сумма возвратов
                                </th>

                                <th class="text-right">
                                    Итог
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            @foreach($productStats as $index => $variant)


                            <tr>


                                {{-- № --}}

                                <td>

                                    <div
                                        class="product-rank {{ $index === 0 ? 'first' : '' }}">

                                        {{ $index + 1 }}

                                    </div>

                                </td>


                                {{-- SKU --}}

                                <td>

                                    <div class="product-name">

                                        {{ $variant['sku'] ?? '—' }}

                                    </div>

                                </td>


                                {{-- ПРОДАНО, ШТ --}}

                                <td class="text-right product-quantity">

                                    {{ number_format(
                                            $variant['sold_quantity'] ?? 0,
                                            0,
                                            '.',
                                            ' '
                                        ) }}

                                </td>


                                {{-- ВОЗВРАТ, ШТ --}}

                                <td class="text-right return-value">

                                    {{ number_format(
                                            $variant['return_quantity'] ?? 0,
                                            0,
                                            '.',
                                            ' '
                                        ) }}

                                </td>


                                {{-- ПРОДАЖИ --}}

                                <td class="text-right sales-value">

                                    {{ number_format(
                                            $variant['sales'] ?? 0,
                                            0,
                                            '.',
                                            ' '
                                        ) }} ₸

                                </td>


                                {{-- ОБЩАЯ СУММА ВОЗВРАТОВ --}}

                                <td class="text-right return-value">

                                    {{ number_format(
                                            $variant['returns'] ?? 0,
                                            0,
                                            '.',
                                            ' '
                                        ) }} ₸

                                </td>


                                {{-- ИТОГ --}}

                                <td class="text-right profit-value">

                                    {{ number_format(
                                            $variant['profit'] ?? 0,
                                            0,
                                            '.',
                                            ' '
                                        ) }} ₸

                                </td>


                            </tr>


                            @endforeach


                        </tbody>


                    </table>


                </div>


                @else


                <div style="
                    padding:40px;
                    text-align:center;
                    color:var(--ink-faint);
                ">

                    За выбранный период продаж вариантов нет.

                </div>


                @endif


            </div>


        </div>


    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        /*
    |--------------------------------------------------------------------------
    | ГРАФИК
    |--------------------------------------------------------------------------
    */

        document.addEventListener('DOMContentLoaded', function() {

            const weeklyData =
                @json(array_values($weeklyStats));


            const labels =
                weeklyData.map(function(item) {

                    return item.day +
                        ' (' +
                        item.days_count +
                        ' ' +
                        (
                            item.days_count === 1 ?
                            'день' :
                            (
                                item.days_count >= 2 &&
                                item.days_count <= 4 ?
                                'дня' :
                                'дней'
                            )
                        ) +
                        ')';

                });


            const averageProfit =
                weeklyData.map(function(item) {

                    return Math.round(
                        item.average_profit
                    );

                });


            const chartElement =
                document.getElementById(
                    'weeklySalesChart'
                );


            if (!chartElement) {
                return;
            }


            new Chart(
                chartElement, {

                    type: 'bar',

                    data: {

                        labels: labels,

                        datasets: [

                            {

                                label: 'Средний итог',

                                data: averageProfit,

                                borderWidth: 0,

                                borderRadius: 6,

                                borderSkipped: false

                            }

                        ]

                    },


                    options: {

                        responsive: true,

                        maintainAspectRatio: false,


                        interaction: {

                            intersect: false,

                            mode: 'index'

                        },


                        plugins: {

                            legend: {

                                display: false

                            },


                            tooltip: {

                                callbacks: {


                                    title: function(context) {

                                        if (!context.length) {
                                            return '';
                                        }


                                        const index =
                                            context[0].dataIndex;


                                        const item =
                                            weeklyData[index];


                                        let countText;


                                        if (
                                            item.days_count === 1
                                        ) {

                                            countText = 'день';

                                        } else if (
                                            item.days_count >= 2 &&
                                            item.days_count <= 4
                                        ) {

                                            countText = 'дня';

                                        } else {

                                            countText = 'дней';

                                        }


                                        return item.day +
                                            ' — ' +
                                            item.days_count +
                                            ' ' +
                                            countText;

                                    },


                                    label: function(context) {

                                        return 'Средний итог: ' +
                                            new Intl.NumberFormat(
                                                'ru-RU'
                                            ).format(
                                                context.raw
                                            ) +
                                            ' ₸';

                                    }

                                }

                            }

                        },


                        scales: {

                            x: {

                                grid: {
                                    display: false
                                },


                                ticks: {

                                    font: {

                                        size: 13,

                                        weight: '700'

                                    },

                                    autoSkip: false,

                                    maxRotation: 90,

                                    minRotation: 90,


                                    callback: function(value) {

                                        return this.getLabelForValue(
                                            value
                                        );

                                    }

                                }

                            },


                            y: {

                                beginAtZero: true,


                                ticks: {

                                    callback: function(value) {

                                        return new Intl.NumberFormat(
                                                'ru-RU'
                                            ).format(value) +
                                            ' ₸';

                                    },


                                    font: {

                                        size: 12

                                    }

                                }

                            }

                        }

                    }

                }
            );

        });


        /*
        |--------------------------------------------------------------------------
        | MODAL: ПРОДАЖИ ПО ДНЯМ
        |--------------------------------------------------------------------------
        */

        function openSalesDaysModal() {

            const modal =
                document.getElementById(
                    'salesDaysModal'
                );


            if (!modal) {
                return;
            }


            modal.classList.add('active');


            document.body.style.overflow =
                'hidden';

        }


        function closeSalesDaysModal(event) {

            const modal =
                document.getElementById(
                    'salesDaysModal'
                );


            if (!modal) {
                return;
            }


            if (
                event &&
                event.target !== modal
            ) {

                return;

            }


            modal.classList.remove(
                'active'
            );


            document.body.style.overflow =
                '';

        }


        /*
        |--------------------------------------------------------------------------
        | MODAL: ВАРИАНТЫ
        |--------------------------------------------------------------------------
        */

        function openProductsModal() {

            const modal =
                document.getElementById(
                    'productsModal'
                );


            if (!modal) {
                return;
            }


            modal.classList.add(
                'active'
            );


            document.body.style.overflow =
                'hidden';

        }


        function closeProductsModal(event) {

            const modal =
                document.getElementById(
                    'productsModal'
                );


            if (!modal) {
                return;
            }


            if (
                event &&
                event.target !== modal
            ) {

                return;

            }


            modal.classList.remove(
                'active'
            );


            document.body.style.overflow =
                '';

        }


        /*
        |--------------------------------------------------------------------------
        | ESC
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function(event) {

                if (event.key !== 'Escape') {
                    return;
                }


                const salesModal =
                    document.getElementById(
                        'salesDaysModal'
                    );


                const productsModal =
                    document.getElementById(
                        'productsModal'
                    );


                if (
                    salesModal &&
                    salesModal.classList.contains(
                        'active'
                    )
                ) {

                    salesModal.classList.remove(
                        'active'
                    );

                }


                if (
                    productsModal &&
                    productsModal.classList.contains(
                        'active'
                    )
                ) {

                    productsModal.classList.remove(
                        'active'
                    );

                }


                document.body.style.overflow =
                    '';

            }
        );
    </script>
</body>

</html>