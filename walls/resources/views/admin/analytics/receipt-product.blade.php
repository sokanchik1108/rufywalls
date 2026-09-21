<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Аналитика товаров по приёмкам</title>

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #f4f5f7;
            color: #17191c;
            font-family: Arial, sans-serif;
        }

        .page {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 14px;
        }


        /* =========================================================
           Шапка
        ========================================================= */

        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }

        .title-block h1 {
            margin: 0;
            font-size: 19px;
            font-weight: 700;
        }

        .title-block p {
            margin: 3px 0 0;
            color: #777e87;
            font-size: 12px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border-radius: 8px;
            background: #ffffff;
            color: #17191c;
            border: 1px solid #e0e3e7;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            transition: 0.15s;
        }

        .back-btn:hover {
            background: #f0f1f3;
        }


        /* =========================================================
           Фильтры
        ========================================================= */

        .filters {
            display: grid;

            grid-template-columns:
                minmax(200px, 1.4fr)
                minmax(200px, 1.2fr)
                minmax(160px, 1fr)
                auto;

            gap: 8px;

            padding: 10px;

            background: #ffffff;

            border: 1px solid #e3e5e8;

            border-radius: 10px;

            margin-bottom: 12px;
        }

        /*
         * ОТ / ПО
         *
         * Два поля строго 50% / 50%.
         */

        .date-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
            min-width: 0;
        }

        .date-row .field {
            width: 100%;
            min-width: 0;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .field label {
            font-size: 11px;
            font-weight: 600;
            color: #737981;
        }

        .field input,
        .field select {
            width: 100%;
            height: 34px;

            padding: 0 8px;

            border: 1px solid #d9dde2;

            border-radius: 7px;

            background: #ffffff;

            color: #17191c;

            outline: none;

            font-size: 12px;
        }

        .field input:focus,
        .field select:focus {
            border-color: #9ba2aa;
        }

        .filter-btn {
            align-self: end;

            height: 34px;

            padding: 0 14px;

            border: 0;

            border-radius: 7px;

            background: #01142f;

            color: #ffffff;

            cursor: pointer;

            font-size: 12px;

            font-weight: 600;
        }

        .filter-btn:hover {
            background: #02214b;
        }


        /* =========================================================
           Общая карточка
        ========================================================= */

        .card {
            background: #ffffff;

            border: 1px solid #e3e5e8;

            border-radius: 10px;

            overflow: hidden;
        }

        .card-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 12px 14px;

            border-bottom: 1px solid #e9ebee;
        }

        .card-header h2 {
            margin: 0;

            font-size: 14px;

            font-weight: 700;
        }

        .card-header span {
            color: #777e87;

            font-size: 11px;
        }


        /* =========================================================
           Таблица
        ========================================================= */

        #products-table-container {
            width: 100%;

            transition: opacity 0.15s ease;
        }

        .table-loading {
            opacity: 0.45;

            pointer-events: none;
        }

        .table-wrapper {
            width: 100%;

            overflow-x: auto;
        }

        .products-table {
            width: 100%;

            border-collapse: collapse;

            min-width: 600px;
        }

        .products-table th {
            height: 36px;

            padding: 6px 10px;

            background: #fafafa;

            border-bottom: 1px solid #e5e7ea;

            color: #727881;

            font-size: 10px;

            font-weight: 700;

            text-align: left;

            white-space: nowrap;
        }

        .products-table td {
            padding: 9px 10px;

            border-bottom: 1px solid #edf0f2;

            font-size: 12px;

            vertical-align: middle;
        }

        .products-table tbody tr:hover {
            background: #fafbfc;
        }

        .products-table th:first-child,
        .products-table td:first-child {
            width: 40px;

            text-align: center;
        }


        /*
         * Убираем колонку "Товар".
         *
         * Сейчас структура таблицы:
         *
         * 1 — №
         * 2 — SKU
         * 3 — Товар
         * 4 — Приёмок
         * 5 — Количество
         * 6 — Средний завоз
         */

        .products-table th:nth-child(3),
        .products-table td:nth-child(3) {
            display: none;
        }


        .products-table th:nth-child(4),
        .products-table td:nth-child(4),
        .products-table th:nth-child(5),
        .products-table td:nth-child(5),
        .products-table th:nth-child(6),
        .products-table td:nth-child(6) {
            text-align: right;
        }


        /* =========================================================
           Сортировка
        ========================================================= */

        .th-sort {
            display: inline-flex;

            align-items: center;

            gap: 6px;
        }

        .sort-arrows {
            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            line-height: 8px;

            gap: 1px;
        }

        .sort-arrow {
            display: block;

            width: 12px;

            height: 8px;

            margin: 0;

            padding: 0;

            border: 0;

            background: transparent;

            color: #cfd3d8;

            cursor: pointer;

            font-size: 7px;

            line-height: 8px;

            transition:
                color 0.15s ease,
                transform 0.15s ease;
        }

        .sort-arrow:hover {
            color: #7a8189;
        }

        .sort-arrow.active {
            color: #111827;

            font-weight: 900;
        }

        .sort-arrow.active:hover {
            color: #000000;
        }


        /* =========================================================
           SKU
        ========================================================= */

        .sku-link {
            color: #111827;

            text-decoration: none;

            font-weight: 700;
        }

        .sku-link:hover {
            text-decoration: underline;
        }


        /* =========================================================
           Пустая таблица
        ========================================================= */

        .empty-row {
            padding: 24px !important;

            text-align: center !important;

            color: #858b93;

            font-size: 12px !important;
        }


        /* =========================================================
           Аналитика конкретного товара
        ========================================================= */

        .product-detail-card {
            margin-top: 0;

            background: #ffffff;

            border: 1px solid #e3e5e8;

            border-radius: 10px;

            overflow: hidden;
        }

        .product-detail-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            padding: 12px 14px;

            border-bottom: 1px solid #e8eaed;
        }

        .product-detail-title {
            font-size: 15px;

            font-weight: 700;

            color: #17191c;
        }

        .product-detail-sku {
            margin-top: 3px;

            color: #777e87;

            font-size: 11px;
        }

        .back-to-products {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-height: 30px;

            padding: 0 10px;

            border-radius: 7px;

            background: #f3f4f6;

            color: #17191c;

            text-decoration: none;

            font-size: 11px;

            font-weight: 600;

            white-space: nowrap;

            transition: 0.15s;
        }

        .back-to-products:hover {
            background: #e8eaed;
        }


        /* =========================================================
           Карточки статистики товара
        ========================================================= */

        .product-detail-stats {
            display: grid;

            grid-template-columns:
                repeat(5, minmax(0, 1fr));

            gap: 1px;

            background: #e5e7ea;

            border-bottom: 1px solid #e5e7ea;
        }

        .detail-stat {
            background: #ffffff;

            padding: 10px;
        }

        .detail-stat-label {
            color: #777e87;

            font-size: 10px;

            margin-bottom: 4px;
        }

        .detail-stat-value {
            color: #17191c;

            font-size: 14px;

            font-weight: 700;
        }


        /* =========================================================
           Таблица конкретного товара
        ========================================================= */

        .detail-table-wrapper {
            width: 100%;

            overflow-x: auto;
        }

        .detail-table {
            width: 100%;

            min-width: 750px;

            border-collapse: collapse;
        }

        .detail-table th {
            padding: 8px 10px;

            background: #fafafa;

            border-bottom: 1px solid #e5e7ea;

            color: #727881;

            font-size: 10px;

            font-weight: 700;

            text-align: left;

            white-space: nowrap;
        }

        .detail-table td {
            padding: 8px 10px;

            border-bottom: 1px solid #edf0f2;

            font-size: 12px;

            white-space: nowrap;
        }

        .detail-table tbody tr:hover {
            background: #fafbfc;
        }


        /* =========================================================
           Адаптив
        ========================================================= */

        @media (max-width: 900px) {

            .page {
                padding: 12px;
            }

            .filters {
                grid-template-columns: 1fr 1fr;
            }

            .search-field {
                grid-column: 1 / -1;
            }

            .filter-btn {
                width: 100%;
            }

            .product-detail-stats {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }
        }


        @media (max-width: 600px) {

            .page {
                padding: 10px;
            }

            .title-block h1 {
                font-size: 17px;
            }

            .filters {
                grid-template-columns: 1fr;

                padding: 10px;

                gap: 8px;
            }

            .search-field {
                grid-column: auto;
            }

            .card-header {
                padding: 10px 12px;
            }

            .card-header h2 {
                font-size: 13px;
            }

            .products-table th,
            .products-table td {
                padding: 8px 8px;
            }

            .product-detail-header {
                padding: 10px 12px;
            }

            .product-detail-stats {
                grid-template-columns: 1fr 1fr;
            }

            .detail-stat {
                padding: 8px;
            }

            .detail-stat-value {
                font-size: 13px;
            }
        }

    </style>
</head>


<body>

<div class="page">


    <!-- =========================================================
         Шапка
    ========================================================== -->

    <div class="top">

        <div class="title-block">

            <h1>
                Аналитика товаров по приёмкам
            </h1>

            <p>
                Полная статистика завоза товаров за выбранный период
            </p>

        </div>


        <a
            href="{{ route('admin.analytics.receipts') }}"
            class="back-btn"
            title="Назад к аналитике приёмок"
        >
            ←
        </a>

    </div>


    <!-- =========================================================
         Фильтры
    ========================================================== -->

    <form
        method="GET"
        action="{{ route('admin.analytics.receipts.product') }}"
        class="filters"
        id="product-filters"
    >

        <!-- Артикул -->

        <div class="field search-field">

            <label>
                Поиск по артикулу
            </label>

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Введите артикул"
                autocomplete="off"
            >

        </div>


        <!-- От / По в одну строку -->

        <div class="date-row">

            <div class="field">

                <label>
                    От
                </label>

                <input
                    type="date"
                    name="from"
                    value="{{ $from->format('Y-m-d') }}"
                >

            </div>


            <div class="field">

                <label>
                    По
                </label>

                <input
                    type="date"
                    name="to"
                    value="{{ $to->format('Y-m-d') }}"
                >

            </div>

        </div>


        <!-- Склад -->

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
                        @selected(
                            (int) $selectedWarehouse ===
                            (int) $warehouse->id
                        )
                    >
                        {{ $warehouse->name }}
                    </option>

                @endforeach

            </select>

        </div>


        <!-- Применить -->

        <button
            type="submit"
            class="filter-btn"
        >
            Применить
        </button>

    </form>


    <!-- =========================================================
         Список товаров
    ========================================================== -->

    @if(!$variantId || !$selectedVariant)

        <div class="card">

            <div class="card-header">

                <h2>
                    Все товары
                </h2>

                <span id="products-count">

                    {{ $allProductStats->count() }}

                    товаров

                </span>

            </div>


            <div
                id="products-table-container"
                data-table-url="{{ route(
                    'admin.analytics.receipts.product.table'
                ) }}"
            >

                @include(
                    'admin.analytics.partials.receipt-product-table',
                    [
                        'allProductStats' =>
                            $allProductStats,

                        'sortBy' =>
                            $sortBy,

                        'sortDirection' =>
                            $sortDirection,
                    ]
                )

            </div>

        </div>

    @endif


    <!-- =========================================================
         Аналитика выбранного товара
    ========================================================== -->

    @if($variantId && $selectedVariant)

        <div class="product-detail-card">


            <div class="product-detail-header">

                <div>

                    <div class="product-detail-title">

                        {{ $selectedVariant->product?->name
                            ?? 'Товар' }}

                    </div>


                    <div class="product-detail-sku">

                        Артикул:

                        {{ $selectedVariant->sku ?? '—' }}

                    </div>

                </div>


                <a
                    href="{{ route(
                        'admin.analytics.receipts.product',
                        request()->except(
                            'variant_id'
                        )
                    ) }}"
                    class="back-to-products"
                >
                    ← Все товары
                </a>

            </div>


            <!-- =====================================================
                 Статистика
            ====================================================== -->

            <div class="product-detail-stats">


                <div class="detail-stat">

                    <div class="detail-stat-label">
                        Всего завезено
                    </div>

                    <div class="detail-stat-value">

                        {{ number_format(
                            $totalQuantity,
                            0,
                            ',',
                            ' '
                        ) }}

                        шт.

                    </div>

                </div>


                <div class="detail-stat">

                    <div class="detail-stat-label">
                        Приёмок
                    </div>

                    <div class="detail-stat-value">
                        {{ $totalReceiptCount }}
                    </div>

                </div>


                <div class="detail-stat">

                    <div class="detail-stat-label">
                        Средний завоз
                    </div>

                    <div class="detail-stat-value">

                        {{ number_format(
                            $totalReceiptCount > 0
                                ? $totalQuantity / $totalReceiptCount
                                : 0,
                            2,
                            ',',
                            ' '
                        ) }}

                        шт.

                    </div>

                </div>


                <div class="detail-stat">

                    <div class="detail-stat-label">
                        Средняя цена закупки
                    </div>

                    <div class="detail-stat-value">

                        {{ number_format(
                            $averagePurchasePrice,
                            2,
                            ',',
                            ' '
                        ) }}

                        ₸

                    </div>

                </div>


                <div class="detail-stat">

                    <div class="detail-stat-label">
                        Общая стоимость
                    </div>

                    <div class="detail-stat-value">

                        {{ number_format(
                            $totalCost,
                            2,
                            ',',
                            ' '
                        ) }}

                        ₸

                    </div>

                </div>


            </div>


            <!-- =====================================================
                 Приёмки конкретного товара
            ====================================================== -->

            <div class="detail-table-wrapper">

                <table class="detail-table">

                    <thead>

                        <tr>

                            <th>
                                Дата
                            </th>

                            <th>
                                № приёмки
                            </th>

                            <th>
                                Склад
                            </th>

                            <th>
                                Количество
                            </th>

                            <th>
                                Цена закупки
                            </th>

                            <th>
                                Сумма
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse(
                            $receiptItems
                            as $item
                        )

                            <tr>

                                <td>

                                    {{ optional(
                                        $item->receipt
                                    )->receipt_date
                                        ?->format(
                                            'd.m.Y'
                                        )
                                        ?? '—' }}

                                </td>


                                <td>

                                    #{{ $item->receipt_id }}

                                </td>


                                <td>

                                    {{ $item->receipt?->warehouse?->name
                                        ?? '—' }}

                                </td>


                                <td>

                                    {{ number_format(
                                        $item->quantity,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    шт.

                                </td>


                                <td>

                                    {{ number_format(
                                        (float)
                                        $item->purchase_price,
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                    ₸

                                </td>


                                <td>

                                    {{ number_format(
                                        (float)
                                        $item->quantity
                                        *
                                        (float)
                                        $item->purchase_price,
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                    ₸

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="empty-row"
                                >
                                    По этому товару приёмок
                                    за выбранный период нет
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    @endif

</div>


<!-- =============================================================
     JavaScript
============================================================= -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const tableContainer =
            document.getElementById(
                'products-table-container'
            );

        const filters =
            document.getElementById(
                'product-filters'
            );

        const productsCount =
            document.getElementById(
                'products-count'
            );


        /*
         * Если открыт конкретный товар,
         * таблицы всех товаров нет.
         */

        if (
            !tableContainer ||
            !filters
        ) {
            return;
        }


        const tableUrl =
            tableContainer.dataset.tableUrl;


        let currentRequest = null;

        let searchTimer = null;


        /*
         * =========================================================
         * Получаем фильтры
         * =========================================================
         *
         * Здесь всегда берутся текущие значения
         * дат и склада.
         *
         * Но сами они отправляются только при
         * нажатии "Применить".
         */

        function getFilters() {

            const params =
                new URLSearchParams();


            const search =
                filters.querySelector(
                    '[name="search"]'
                );


            const from =
                filters.querySelector(
                    '[name="from"]'
                );


            const to =
                filters.querySelector(
                    '[name="to"]'
                );


            const warehouse =
                filters.querySelector(
                    '[name="warehouse_id"]'
                );


            if (
                search &&
                search.value.trim() !== ''
            ) {

                params.set(
                    'search',
                    search.value.trim()
                );

            }


            if (
                from &&
                from.value
            ) {

                params.set(
                    'from',
                    from.value
                );

            }


            if (
                to &&
                to.value
            ) {

                params.set(
                    'to',
                    to.value
                );

            }


            if (
                warehouse &&
                warehouse.value
            ) {

                params.set(
                    'warehouse_id',
                    warehouse.value
                );

            }


            return params;

        }


        /*
         * =========================================================
         * Обновление таблицы
         * =========================================================
         */

        async function updateTable(
            sortBy = null,
            sortDirection = null
        ) {

            const params =
                getFilters();


            if (sortBy) {

                params.set(
                    'sort_by',
                    sortBy
                );

            }


            if (sortDirection) {

                params.set(
                    'sort_direction',
                    sortDirection
                );

            }


            /*
             * Отменяем предыдущий запрос.
             */

            if (currentRequest) {

                currentRequest.abort();

            }


            currentRequest =
                new AbortController();


            tableContainer.classList.add(
                'table-loading'
            );


            try {

                const response =
                    await fetch(
                        tableUrl +
                        '?' +
                        params.toString(),
                        {
                            method: 'GET',

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'text/html'
                            },

                            signal:
                                currentRequest.signal
                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Ошибка загрузки таблицы'
                    );

                }


                const html =
                    await response.text();


                /*
                 * Заменяем только таблицу.
                 */

                tableContainer.innerHTML =
                    html;


                /*
                 * Обновляем количество товаров.
                 */

                updateProductsCount();


                /*
                 * После замены таблицы
                 * снова подключаем сортировку.
                 */

                bindSortButtons();


            } catch (error) {

                if (
                    error.name !==
                    'AbortError'
                ) {

                    console.error(
                        error
                    );

                }

            } finally {

                tableContainer.classList.remove(
                    'table-loading'
                );

            }

        }


        /*
         * =========================================================
         * Правильное количество товаров
         * =========================================================
         */

        function updateProductsCount() {

            if (!productsCount) {
                return;
            }


            const rows =
                tableContainer.querySelectorAll(
                    '.products-table tbody tr'
                );


            let count = 0;


            rows.forEach(
                function (row) {

                    /*
                     * Строку "Ничего не найдено"
                     * не считаем товаром.
                     */

                    if (
                        row.querySelector(
                            '.sku-link'
                        )
                    ) {

                        count++;

                    }

                }
            );


            productsCount.textContent =
                count +
                ' ' +
                getProductsWord(count);

        }


        /*
         * Правильное склонение слова "товар".
         */

        function getProductsWord(count) {

            const lastTwo =
                count % 100;

            const last =
                count % 10;


            if (
                lastTwo >= 11 &&
                lastTwo <= 14
            ) {

                return 'товаров';

            }


            if (last === 1) {

                return 'товар';

            }


            if (
                last >= 2 &&
                last <= 4
            ) {

                return 'товара';

            }


            return 'товаров';

        }


        /*
         * =========================================================
         * Сортировка
         * =========================================================
         */

        function bindSortButtons() {

            const buttons =
                tableContainer.querySelectorAll(
                    '.sort-arrow'
                );


            buttons.forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            const sortBy =
                                this.dataset.sort;


                            const direction =
                                this.dataset.direction;


                            updateTable(
                                sortBy,
                                direction
                            );

                        }
                    );

                }
            );

        }


        /*
         * Первичная привязка сортировки.
         */

        bindSortButtons();


        /*
         * =========================================================
         * Поиск по артикулу
         * =========================================================
         *
         * Поиск работает автоматически.
         */

        const searchInput =
            filters.querySelector(
                '[name="search"]'
            );


        if (searchInput) {

            searchInput.addEventListener(
                'input',
                function () {

                    clearTimeout(
                        searchTimer
                    );


                    searchTimer =
                        setTimeout(
                            function () {

                                updateTable();

                            },
                            350
                        );

                }
            );

        }


        /*
         * =========================================================
         * Даты
         * =========================================================
         *
         * ВАЖНО:
         * здесь больше НЕТ updateTable().
         *
         * Поэтому изменение даты само по себе
         * ничего не отправляет.
         */

        const dateInputs =
            filters.querySelectorAll(
                '[name="from"], [name="to"]'
            );


        dateInputs.forEach(
            function (input) {

                input.addEventListener(
                    'change',
                    function () {

                        /*
                         * Ничего не делаем.
                         *
                         * Дата применится после
                         * нажатия "Применить".
                         */

                    }
                );

            }
        );


        /*
         * =========================================================
         * Склад
         * =========================================================
         *
         * ВАЖНО:
         * здесь тоже больше НЕТ updateTable().
         */

        const warehouseInput =
            filters.querySelector(
                '[name="warehouse_id"]'
            );


        if (warehouseInput) {

            warehouseInput.addEventListener(
                'change',
                function () {

                    /*
                     * Ничего не делаем.
                     *
                     * Склад применится только
                     * после кнопки "Применить".
                     */

                }
            );

        }


        /*
         * =========================================================
         * Кнопка "Применить"
         * =========================================================
         */

        filters.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();


                /*
                 * Здесь уже отправляем:
                 *
                 * - поиск
                 * - дату ОТ
                 * - дату ПО
                 * - склад
                 */

                updateTable();

            }
        );


        /*
         * Первоначально правильно показываем
         * количество товаров.
         */

        updateProductsCount();

    }

);

</script>

</body>

</html>
