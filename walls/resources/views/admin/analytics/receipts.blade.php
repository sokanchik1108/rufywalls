<!DOCTYPE html>

<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Аналитика приёмок</title>

    <style>

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --accent: #01142f;
            --accent-dark: #01142f;
            --accent-soft: #eef2ff;
            --text: black;
            --text-muted: #6b7280;
            --text-faint: #9ca3af;
            --border: #e5e7eb;
            --border-soft: #edf0f2;
            --bg: #f4f5f8;
            --card-bg: #ffffff;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow: 0 1px 2px rgba(17, 24, 39, .04), 0 1px 8px rgba(17, 24, 39, .04);
        }

        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .page {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px;
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--card-bg);
            color: #374151;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            transition: .15s ease;
        }

        .back-btn:hover {
            border-color: var(--accent);
            color: var(--accent-dark);
        }

        h1 {
            margin: 0;
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .subtitle {
            margin-top: 2px;
            color: var(--text-muted);
            font-size: 12px;
        }

        /* =========================================================
           FILTERS
        ========================================================= */

        .filters {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(180px, 220px) auto auto;
            align-items: end;
            gap: 8px;
            padding: 10px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            margin-bottom: 12px;
            box-shadow: var(--shadow);
        }

        /*
         * От и По всегда одинаковой ширины.
         */
        .date-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
            min-width: 0;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            width: 100%;
        }

        .field label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            line-height: 1.2;
        }

        input,
        select {
            height: 34px;
            padding: 0 8px;
            border: 1px solid #d7dbe0;
            border-radius: var(--radius-sm);
            background: var(--card-bg);
            font: inherit;
            font-size: 12px;
            outline: none;
            color: var(--text);
            transition: border-color .15s ease, box-shadow .15s ease;
            width: 100%;
            min-width: 0;
            max-width: 100%;
        }

        /*
         * Даты
         */
        input[type="date"] {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 100%;
            min-width: 0;
            max-width: 100%;

            height: 34px;
            min-height: 34px;

            box-sizing: border-box;

            -webkit-appearance: auto;
            appearance: auto;

            padding-left: 6px;
            padding-right: 6px;

            font-size: 12px;
            line-height: 34px;
            text-align: center;
            vertical-align: middle;
        }

        /*
         * Вертикальное выравнивание даты внутри input
         * для Chrome / Android / WebKit.
         */
        input[type="date"]::-webkit-datetime-edit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            line-height: normal;
        }

        input[type="date"]::-webkit-datetime-edit-fields-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            margin-left: 2px;
            padding: 0;
        }

        input:focus,
        select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        /*
         * Склад
         */
        .filters > .field {
            width: 100%;
            min-width: 0;
        }

        .filter-btn {
            height: 34px;
            padding: 0 14px;
            border: 0;
            border-radius: var(--radius-sm);
            background: var(--accent);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s ease, transform .1s ease;
            white-space: nowrap;
        }

        .filter-btn:hover {
            background: var(--accent-dark);
        }

        .filter-btn:active {
            transform: scale(.98);
        }

        /* =========================================================
           RECEIPTS BUTTON
        ========================================================= */

        .receipts-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 34px;
            padding: 0 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--card-bg);
            color: #374151;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            transition: .15s ease;
            white-space: nowrap;
        }

        .receipts-btn:hover {
            border-color: var(--accent);
            color: var(--accent-dark);
        }

        /* =========================================================
           CARDS
        ========================================================= */

        .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 12px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 11px;
            box-shadow: var(--shadow);
        }

        .card-label {
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .card-value {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .card-small {
            margin-top: 3px;
            color: var(--text-faint);
            font-size: 9px;
        }

        /* =========================================================
           SECTION
        ========================================================= */

        .section {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px;
            margin-bottom: 12px;
            box-shadow: var(--shadow);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 10px;
        }

        .section-title {
            margin: 0;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .section-note {
            margin-bottom: 10px;
            color: var(--text-muted);
            font-size: 11px;
        }

        /* =========================================================
           WEEKDAYS
        ========================================================= */

        .weekday-scroller {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0;
            padding: 0;
        }

        .weekday-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
        }

        .weekday-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-width: 0;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-sm);
            padding: 8px 4px;
            text-align: center;
            background: #fafbfc;
        }

        .weekday-name {
            width: 100%;
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 5px;
            text-transform: uppercase;
            text-align: center;
        }

        .weekday-value {
            width: 100%;
            font-size: 15px;
            font-weight: 800;
            color: var(--accent-dark);
            text-align: center;
            line-height: 1.2;
        }

        .weekday-label {
            width: 100%;
            margin-top: 2px;
            color: var(--text-faint);
            font-size: 9px;
            text-align: center;
            line-height: 1.2;
        }

        .weekday-products {
            width: 100%;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid var(--border-soft);
            font-size: 10px;
            font-weight: 700;
            color: #374151;
            text-align: center;
            line-height: 1.2;
        }

        /* =========================================================
           TABLE
        ========================================================= */

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 9px 8px;
            border-bottom: 1px solid var(--border-soft);
            text-align: left;
            vertical-align: middle;
        }

        th {
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .02em;
            white-space: nowrap;
        }

        td {
            font-size: 12px;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .sku {
            font-weight: 700;
        }

        .sku a {
            color: var(--text);
            text-decoration: none;
        }

        .sku a:hover {
            color: var(--accent-dark);
        }

        /*
         * Числовые колонки
         */
        .number {
            text-align: center;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        th.number,
        td.number {
            text-align: center;
        }

        .quantity {
            font-weight: 700;
        }

        .empty {
            padding: 35px 10px;
            text-align: center;
            color: var(--text-muted);
        }

        .rank {
            width: 34px;
            color: var(--text-faint);
            font-weight: 800;
            text-align: center;
        }

        /* =========================================================
           MOBILE TABLE
        ========================================================= */

        @media (max-width: 600px) {

            th,
            td {
                padding: 8px 6px;
                font-size: 12px;
            }

            th {
                font-size: 10px;
            }

            .rank {
                width: 22px;
                text-align: center;
            }
        }

        /* =========================================================
           TABLET / MOBILE
        ========================================================= */

        @media (max-width: 900px) {

            .filters {
                grid-template-columns: minmax(0, 1fr) minmax(160px, 200px);
            }

            .date-row {
                grid-column: 1 / -1;
            }

            .filters > .field {
                grid-column: 1;
            }

            .filter-btn {
                grid-column: 2;
            }

            .filters > .receipts-btn {
                grid-column: 1 / -1;
                width: 100%;
            }
        }

        @media (max-width: 700px) {

            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .weekday-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 600px) {

            .page {
                padding: 10px 10px 28px;
            }

            .filters {
                display: grid;
                grid-template-columns: 1fr;
                gap: 9px;
                align-items: stretch;
            }

            /*
             * От и По строго в одной строке.
             * Оба поля всегда одинаковой ширины.
             */
            .date-row {
                grid-column: 1;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                width: 100%;
                min-width: 0;
                gap: 8px;
            }

            .date-row .field {
                width: 100%;
                min-width: 0;
            }

            .date-row input[type="date"] {
                display: flex;
                align-items: center;
                justify-content: center;

                width: 100%;
                min-width: 0;
                max-width: 100%;

                height: 34px;
                min-height: 34px;

                padding-left: 4px;
                padding-right: 4px;

                font-size: 12px;
                line-height: 34px;
                text-align: center;
            }

            .date-row input[type="date"]::-webkit-datetime-edit {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                height: 100%;
                line-height: normal;
            }

            .date-row input[type="date"]::-webkit-datetime-edit-fields-wrapper {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                height: 100%;
            }

            .filters > .field {
                grid-column: 1;
                width: 100%;
            }

            .filters > .filter-btn,
            .filters > .receipts-btn {
                grid-column: 1;
                width: 100%;
            }
        }

        /* =========================================================
           SMALL MOBILE
        ========================================================= */

        @media (max-width: 420px) {

            .weekday-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 5px;
            }

            .date-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 7px;
            }

            .date-row input[type="date"] {
                padding-left: 3px;
                padding-right: 3px;
                font-size: 11px;
                line-height: 34px;
                text-align: center;
            }

            .weekday-card {
                padding-left: 3px;
                padding-right: 3px;
            }

            .weekday-value {
                text-align: center;
            }

            .weekday-products {
                text-align: center;
            }
        }

    </style>

</head>

<body>

    <div class="page">

        {{-- HEADER --}}

        <div class="header">

            <div class="header-left">

                <a
                    href="{{ route('admin.analytics.menu') }}"
                    class="back-btn"
                    title="В меню аналитики">

                    ←

                </a>

                <div>

                    <h1>
                        Аналитика приёмок
                    </h1>

                    <div class="subtitle">
                        Анализ поступления товаров на склады
                    </div>

                </div>

            </div>

        </div>

        {{-- FILTERS --}}

        <form
            method="GET"
            action="{{ route('admin.analytics.receipts') }}"
            class="filters">

            <div class="date-row">

                <div class="field">

                    <label>
                        От
                    </label>

                    <input
                        type="date"
                        name="from"
                        value="{{ $from->format('Y-m-d') }}">

                </div>

                <div class="field">

                    <label>
                        По
                    </label>

                    <input
                        type="date"
                        name="to"
                        value="{{ $to->format('Y-m-d') }}">

                </div>

            </div>

            <div class="field">

                <label>
                    Склад
                </label>

                <select name="warehouse_id">

                    <option value="">
                        Все склады
                    </option>

                    @foreach($warehouses as $warehouse)

                        <option
                            value="{{ $warehouse->id }}"
                            @selected($selectedWarehouse == $warehouse->id)
                        >
                            {{ $warehouse->name }}
                        </option>

                    @endforeach

                </select>

            </div>

            <button
                type="submit"
                class="filter-btn">

                Показать

            </button>

            <a
                href="{{ url('/admin/receipts') }}"
                class="receipts-btn">

                Все приёмки

            </a>

        </form>

        {{-- MAIN CARDS --}}

        <div class="cards">

            <div class="card">

                <div class="card-label">
                    Всего приёмок
                </div>

                <div class="card-value">
                    {{ number_format($totalReceipts, 0, '.', ' ') }}
                </div>

                <div class="card-small">
                    за выбранный период
                </div>

            </div>

            <div class="card">

                <div class="card-label">
                    Завезено товаров
                </div>

                <div class="card-value">
                    {{ number_format($totalQuantity, 0, '.', ' ') }} шт.
                </div>

                <div class="card-small">
                    по всем приёмкам
                </div>

            </div>

            <div class="card">

                <div class="card-label">
                    В среднем за приёмку
                </div>

                <div class="card-value">
                    {{ number_format($averageQuantityPerReceipt, 1, '.', ' ') }}
                    шт.
                </div>

                <div class="card-small">
                    товаров на одну приёмку
                </div>

            </div>

            <div class="card">

                <div class="card-label">
                    Дней с приёмками
                </div>

                <div class="card-value">
                    {{ number_format($receiptDaysCount, 0, '.', ' ') }}
                </div>

                <div class="card-small">
                    дней за выбранный период
                </div>

            </div>

        </div>

        {{-- WEEKDAYS --}}

        @php

            $weekdayNames = [
                1 => 'Пн',
                2 => 'Вт',
                3 => 'Ср',
                4 => 'Чт',
                5 => 'Пт',
                6 => 'Сб',
                7 => 'Вс',
            ];

            $weekdayStats = [];

            for ($i = 1; $i <= 7; $i++) {

                $weekdayStats[$i] = [
                    'receipts' => 0,
                    'quantity' => 0,
                ];

            }

            foreach ($dailyStats as $day) {

                $date = \Carbon\Carbon::parse($day['date']);

                $weekday = $date->dayOfWeekIso;

                $weekdayStats[$weekday]['receipts'] += (int) $day['receipts'];

                $weekdayStats[$weekday]['quantity'] += (int) $day['quantity'];

            }

        @endphp

        <div class="section">

            <div class="section-header">

                <h2 class="section-title">
                    Приёмки по дням недели
                </h2>

            </div>

            <div class="weekday-scroller">

                <div class="weekday-grid">

                    @foreach($weekdayNames as $dayNumber => $dayName)

                        <div class="weekday-card">

                            <div class="weekday-name">
                                {{ $dayName }}
                            </div>

                            <div class="weekday-value">
                                {{ number_format($weekdayStats[$dayNumber]['receipts'], 0, '.', ' ') }}
                            </div>

                            <div class="weekday-label">
                                приёмок
                            </div>

                            <div class="weekday-products">
                                {{ number_format($weekdayStats[$dayNumber]['quantity'], 0, '.', ' ') }}
                                шт.
                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

        {{-- TOP 10 PRODUCTS --}}

        <div class="section">

            <div class="section-header">

                <h2 class="section-title">
                    ТОП-10 товаров по среднему завозу
                </h2>

                <a
                    href="{{ route('admin.analytics.receipts.product', request()->query()) }}"
                    class="receipts-btn">

                    Подробнее

                </a>

            </div>

            <div class="section-note">
                Средний завоз = общее количество завезённого товара ÷ количество приёмок
            </div>

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Артикул
                            </th>

                            <th class="number">
                                Приёмок
                            </th>

                            <th class="number">
                                Завезено
                            </th>

                            <th class="number">
                                Средний завоз
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @php

                            $topProducts = collect($productStats)
                                ->map(function ($product) {

                                    $receipts = (int) $product['receipts'];
                                    $quantity = (int) $product['quantity'];

                                    $product['average_quantity'] = $receipts > 0
                                        ? $quantity / $receipts
                                        : 0;

                                    return $product;

                                })
                                ->sortByDesc('average_quantity')
                                ->take(10)
                                ->values();

                        @endphp

                        @forelse($topProducts as $index => $product)

                            <tr>

                                <td class="rank">
                                    {{ $index + 1 }}
                                </td>

                                <td class="sku">

                                    <a
                                        href="{{ route('admin.analytics.receipts.product', array_merge(
                                            request()->query(),
                                            ['variant_id' => $product['variant_id']]
                                        )) }}">

                                        {{ $product['sku'] }}

                                    </a>

                                </td>

                                <td
                                    class="number"
                                    data-label="Приёмок">

                                    {{ number_format($product['receipts'], 0, '.', ' ') }}

                                </td>

                                <td
                                    class="number quantity"
                                    data-label="Завезено">

                                    {{ number_format($product['quantity'], 0, '.', ' ') }}
                                    шт.

                                </td>

                                <td
                                    class="number quantity"
                                    data-label="Средний завоз">

                                    {{ number_format($product['average_quantity'], 1, '.', ' ') }}
                                    шт.

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="empty">

                                    За выбранный период данных нет

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</body>

</html>