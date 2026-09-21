<!DOCTYPE html>

<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>Приёмки</title>

    <style>

        * {
            box-sizing: border-box;
        }

        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            background: #f6f7f9;
            color: #111827;
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Arial,
                sans-serif;
        }

        a {
            color: inherit;
        }

        button,
        input,
        select {
            font: inherit;
        }

        .page {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 18px 20px 40px;
        }


        /* =========================
           HEADER
        ========================= */

        .header {
            position: relative;

            display: grid;
            grid-template-columns: 42px 1fr 42px;

            align-items: center;

            height: 52px;
            margin-bottom: 18px;
        }

        .header-title {
            text-align: center;

            font-size: 21px;
            font-weight: 700;

            letter-spacing: -0.3px;
        }

        .header-btn {
            width: 42px;
            height: 42px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #e5e7eb;
            border-radius: 12px;

            background: #fff;
            text-decoration: none;
            color: #111827;

            transition: .15s ease;
        }

        .header-btn:hover {
            border-color: #cfd3d8;
            transform: translateY(-1px);
        }

        .header-btn svg {
            width: 19px;
            height: 19px;

            stroke: currentColor;
            fill: none;

            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .add-btn {
            background: #111827;
            color: #fff;
            border-color: #111827;
        }

        .add-btn:hover {
            background: #1f2937;
            border-color: #1f2937;
        }

        .add-btn svg {
            width: 20px;
            height: 20px;
        }


        /* =========================
           FILTERS
        ========================= */

        .filters {
            display: grid;

            /*
             * ВАЖНО:
             * первая строка — склад,
             * вторая — От / До,
             * третья — кнопка.
             */
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);

            gap: 8px;

            width: 100%;

            padding: 9px;
            margin-bottom: 14px;

            background: #fff;

            border: 1px solid #e7e9ed;
            border-radius: 14px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .025);
        }

        .filter {
            position: relative;

            width: 100%;
            min-width: 0;
        }

        /*
         * Склад занимает всю ширину.
         */
        .filter:first-child {
            grid-column: 1 / -1;
        }

        /*
         * От и До занимают строго
         * по 50% доступного пространства.
         */
        .filter:nth-child(2),
        .filter:nth-child(3) {
            grid-column: auto;

            width: 100%;
            min-width: 0;
        }

        .filter-label {
            position: absolute;

            left: 11px;
            top: 5px;

            z-index: 2;

            font-size: 9px;
            line-height: 1;

            color: #9ca3af;

            pointer-events: none;
        }

        .filter input,
        .filter select {
            display: block;

            width: 100%;
            min-width: 0;

            height: 45px;

            margin: 0;

            padding: 15px 10px 3px;

            border: 1px solid #e5e7eb;
            border-radius: 10px;

            background: #fafafa;
            color: #111827;

            outline: none;

            font-size: 13px;
            line-height: 1.2;

            appearance: auto;
        }

        .filter input:focus,
        .filter select:focus {
            background: #fff;
            border-color: #9ca3af;
        }

        /*
         * Кнопка занимает всю строку.
         */
        .filter-button {
            grid-column: 1 / -1;

            width: 100%;
            min-width: 0;

            height: 45px;

            padding: 0 18px;

            border: 0;
            border-radius: 10px;

            background: #111827;
            color: #fff;

            cursor: pointer;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 13px;
            font-weight: 600;
        }

        .filter-button:hover {
            background: #1f2937;
        }


        /* =========================
           ALERTS
        ========================= */

        .alert {
            padding: 12px 14px;

            margin-bottom: 12px;

            border-radius: 11px;

            font-size: 13px;
        }

        .alert-success {
            background: #ecfdf3;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }


        /* =========================
           DESKTOP TABLE
        ========================= */

        .table-wrapper {
            width: 100%;

            overflow-x: auto;

            background: #fff;

            border: 1px solid #e7e9ed;
            border-radius: 14px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .025);
        }

        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 760px;
        }

        thead {
            background: #f8f9fa;
        }

        th {
            padding: 13px 15px;

            text-align: left;

            color: #6b7280;

            font-size: 11px;
            font-weight: 600;

            border-bottom: 1px solid #e7e9ed;

            white-space: nowrap;
        }

        td {
            padding: 14px 15px;

            font-size: 13px;

            border-bottom: 1px solid #f0f1f3;

            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        .id-cell {
            font-weight: 700;
            white-space: nowrap;
        }

        .date-cell {
            white-space: nowrap;
            color: #4b5563;
        }

        .warehouse-cell {
            font-weight: 600;
        }

        .products-cell {
            min-width: 230px;
        }

        .product-line {
            margin-bottom: 4px;

            font-size: 12px;
            line-height: 1.4;
        }

        .product-line:last-child {
            margin-bottom: 0;
        }

        .product-sku {
            font-weight: 600;
        }

        .product-batch {
            color: #9ca3af;
        }

        .product-qty {
            color: #374151;
        }

        .comment-cell {
            max-width: 220px;

            color: #6b7280;

            font-size: 12px;
        }

        .no-comment {
            color: #d1d5db;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;

            gap: 6px;

            white-space: nowrap;
        }

        .action-btn {
            width: 34px;
            height: 34px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;
            border: 1px solid #e5e7eb;

            background: #fff;

            text-decoration: none;

            color: #4b5563;

            transition: .15s ease;
        }

        .action-btn:hover {
            background: #f8f9fa;
            border-color: #d1d5db;
        }

        .action-btn svg {
            width: 16px;
            height: 16px;

            fill: none;
            stroke: currentColor;

            stroke-width: 1.8;

            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .action-delete {
            color: #dc2626;
        }

        .action-delete:hover {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .show-btn {
            color: #111827;
        }


        /* =========================
           EMPTY
        ========================= */

        .empty {
            padding: 55px 20px;

            text-align: center;

            background: #fff;

            border: 1px solid #e7e9ed;
            border-radius: 14px;

            color: #9ca3af;

            font-size: 14px;
        }

        .table-empty {
            padding: 45px 20px;

            text-align: center;

            color: #9ca3af;

            font-size: 13px;
        }


        /* =========================
           MOBILE CARDS
        ========================= */

        .receipts {
            display: none;
        }


        /* =========================
           PAGINATION
        ========================= */

        .pagination {
            display: flex;

            justify-content: center;

            margin-top: 18px;
        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 700px) {

            .page {
                width: 100%;

                padding: 10px 10px 30px;
            }


            /* =====================
               HEADER
            ===================== */

            .header {
                margin-bottom: 12px;
            }

            .header-title {
                font-size: 19px;
            }

            .header-btn {
                width: 40px;
                height: 40px;

                border-radius: 11px;
            }


            /* =====================
               FILTERS
            ===================== */

            .filters {

                /*
                 * Две абсолютно одинаковые колонки.
                 */
                grid-template-columns:
                    minmax(0, 1fr)
                    minmax(0, 1fr);

                gap: 7px;

                width: 100%;

                padding: 7px;

                border-radius: 14px;
            }

            /*
             * Склад.
             */
            .filter:first-child {
                grid-column: 1 / -1;
            }

            /*
             * От.
             */
            .filter:nth-child(2) {
                grid-column: 1;
                width: 100%;
            }

            /*
             * До.
             */
            .filter:nth-child(3) {
                grid-column: 2;
                width: 100%;
            }

            /*
             * Инпуты обязательно
             * остаются внутри своих колонок.
             */
            .filter input,
            .filter select {
                width: 100%;
                min-width: 0;

                height: 45px;

                padding-left: 10px;
                padding-right: 8px;

                font-size: 13px;
            }

            /*
             * Кнопка.
             */
            .filter-button {
                grid-column: 1 / -1;

                width: 100%;

                height: 45px;

                font-size: 13px;
            }


            /* =====================
               TABLE HIDDEN
            ===================== */

            .table-wrapper {
                display: none;
            }


            /* =====================
               CARDS
            ===================== */

            .receipts {
                display: flex;

                flex-direction: column;

                gap: 7px;
            }

            .receipt {
                display: grid;

                grid-template-columns: minmax(0, 1fr) auto;

                gap: 5px 10px;

                padding: 13px;

                background: #fff;

                border: 1px solid #e7e9ed;

                border-radius: 13px;

                text-decoration: none;

                transition: .15s ease;
            }

            .receipt:active {
                transform: scale(.99);
            }

            .receipt-id {
                grid-column: 1;
                grid-row: 1;

                font-size: 13px;

                font-weight: 700;
            }

            .receipt-date {
                grid-column: 2;
                grid-row: 1;

                text-align: right;

                color: #6b7280;

                font-size: 12px;
            }

            .receipt-main {
                grid-column: 1;
                grid-row: 2;

                min-width: 0;
            }

            .receipt-warehouse {
                font-size: 13px;

                font-weight: 600;

                white-space: nowrap;

                overflow: hidden;

                text-overflow: ellipsis;
            }

            .receipt-meta {
                margin-top: 3px;

                color: #9ca3af;

                font-size: 11px;
            }

            .receipt-arrow {
                grid-column: 2;
                grid-row: 2;

                width: 34px;
                height: 34px;

                display: flex;

                align-items: center;
                justify-content: center;

                color: #9ca3af;

                border-radius: 9px;

                background: #f8f9fa;
            }

            .receipt-arrow svg {
                width: 16px;
                height: 16px;

                fill: none;

                stroke: currentColor;

                stroke-width: 2;

                stroke-linecap: round;
                stroke-linejoin: round;
            }

            .empty {
                padding: 45px 15px;

                border-radius: 13px;

                font-size: 13px;
            }

        }

    </style>

</head>


<body>


    <div class="page">


        {{-- =========================
             HEADER
        ========================= --}}

        <div class="header">

            <a
                href="{{ auth()->user()->role === 'analyst'
                    ? route('admin.analytics.receipts')
                    : url('/admin') }}"
                class="header-btn"
                title="Назад">

                <svg viewBox="0 0 24 24">

                    <path d="M19 12H5"></path>

                    <path d="M12 19l-7-7 7-7"></path>

                </svg>

            </a>


            <div class="header-title">

                Приёмки

            </div>


            <a
                href="{{ route('admin.receipts.create') }}"
                class="header-btn add-btn"
                title="Новая приёмка">

                <svg viewBox="0 0 24 24">

                    <path d="M12 5v14"></path>

                    <path d="M5 12h14"></path>

                </svg>

            </a>

        </div>


        {{-- =========================
             ALERTS
        ========================= --}}

        @if(session('success'))

            <div class="alert alert-success">

                {{ session('success') }}

            </div>

        @endif


        @if(session('error'))

            <div class="alert alert-error">

                {{ session('error') }}

            </div>

        @endif


        @if($errors->any())

            <div class="alert alert-error">

                @foreach($errors->all() as $error)

                    <div>

                        {{ $error }}

                    </div>

                @endforeach

            </div>

        @endif


        {{-- =========================
             FILTERS
        ========================= --}}

        <form
            method="GET"
            action="{{ route('admin.receipts.index') }}"
            class="filters">


            {{-- Склад --}}

            <div class="filter">

                <span class="filter-label">

                    Склад

                </span>


                <select name="warehouse_id">

                    <option value="">

                        Все склады

                    </option>


                    @foreach($warehouses as $warehouse)

                        <option
                            value="{{ $warehouse->id }}"
                            @selected(request('warehouse_id') == $warehouse->id)
                        >

                            {{ $warehouse->name }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- От --}}

            <div class="filter">

                <span class="filter-label">

                    От

                </span>


                <input
                    type="date"
                    name="from"
                    value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}"
                >

            </div>


            {{-- До --}}

            <div class="filter">

                <span class="filter-label">

                    До

                </span>


                <input
                    type="date"
                    name="to"
                    value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}"
                >

            </div>


            {{-- Применить --}}

            <button
                type="submit"
                class="filter-button">

                Применить

            </button>


        </form>


        {{-- ==================================================
             DESKTOP TABLE
        ================================================== --}}

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Дата
                        </th>

                        <th>
                            Склад
                        </th>

                        <th>
                            Комментарий
                        </th>

                        <th>
                            Действия
                        </th>

                    </tr>

                </thead>


                <tbody>


                    @forelse($receipts as $receipt)


                        <tr>


                            {{-- ID --}}

                            <td class="id-cell">

                                #{{ $receipt->id }}

                            </td>


                            {{-- Дата --}}

                            <td class="date-cell">

                                {{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d.m.Y') }}

                            </td>


                            {{-- Склад --}}

                            <td class="warehouse-cell">

                                {{ $receipt->warehouse->name }}

                            </td>


                            {{-- Комментарий --}}

                            <td class="comment-cell">


                                @if($receipt->comment)

                                    {{ $receipt->comment }}

                                @else

                                    <span class="no-comment">

                                        —

                                    </span>

                                @endif


                            </td>


                            {{-- Действия --}}

                            <td>

                                <div class="actions">


                                    {{-- Подробнее --}}

                                    <a
                                        href="{{ route('admin.receipts.show', $receipt) }}"
                                        class="action-btn show-btn"
                                        title="Подробнее">

                                        <svg viewBox="0 0 24 24">

                                            <path d="M9 18l6-6-6-6"></path>

                                        </svg>

                                    </a>


                                    {{-- Изменить --}}

                                    <a
                                        href="{{ route('admin.receipts.edit', $receipt) }}"
                                        class="action-btn"
                                        title="Изменить">

                                        <svg viewBox="0 0 24 24">

                                            <path d="M12 20h9"></path>

                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>

                                        </svg>

                                    </a>


                                    {{-- Удалить --}}

                                    <form
                                        action="{{ route('admin.receipts.destroy', $receipt) }}"
                                        method="POST"
                                        style="display:inline"
                                        onsubmit="return confirm('Удалить эту приёмку? Неиспользованный остаток будет убран со склада.');"
                                    >

                                        @csrf

                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            class="action-btn action-delete"
                                            title="Удалить">

                                            <svg viewBox="0 0 24 24">

                                                <path d="M3 6h18"></path>

                                                <path d="M8 6V4h8v2"></path>

                                                <path d="M19 6l-1 14H6L5 6"></path>

                                                <path d="M10 11v5"></path>

                                                <path d="M14 11v5"></path>

                                            </svg>

                                        </button>

                                    </form>


                                </div>

                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td
                                colspan="6"
                                class="table-empty">

                                За выбранный период приёмок нет

                            </td>

                        </tr>


                    @endforelse


                </tbody>

            </table>

        </div>


        {{-- ==================================================
             MOBILE CARDS
        ================================================== --}}

        <div class="receipts">


            @forelse($receipts as $receipt)


                <a
                    href="{{ route('admin.receipts.show', $receipt) }}"
                    class="receipt">


                    <div class="receipt-id">

                        #{{ $receipt->id }}

                    </div>


                    <div class="receipt-date">

                        {{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d.m.Y') }}

                    </div>


                    <div class="receipt-main">


                        <div class="receipt-warehouse">

                            {{ $receipt->warehouse->name }}

                        </div>


                        <div class="receipt-meta">

                            {{ $receipt->items->count() }}

                            {{ $receipt->items->count() == 1 ? 'позиция' : 'позиций' }}

                            ·

                            {{ $receipt->items->sum('quantity') }}

                            шт.


                            @if($receipt->comment)

                                · есть комментарий

                            @endif

                        </div>


                    </div>


                    <div class="receipt-arrow">

                        <svg viewBox="0 0 24 24">

                            <path d="M9 18l6-6-6-6"></path>

                        </svg>

                    </div>


                </a>


            @empty


                <div class="empty">

                    За выбранный период приёмок нет

                </div>


            @endforelse


        </div>


        {{-- =========================
             PAGINATION
        ========================= --}}

        @if($receipts->hasPages())

            <div class="pagination">

                {{ $receipts->links() }}

            </div>

        @endif


    </div>


</body>

</html>
<!DOCTYPE html>

<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>Приёмки</title>

    <style>

        * {
            box-sizing: border-box;
        }

        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            background: #f6f7f9;
            color: #111827;
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Arial,
                sans-serif;
        }

        a {
            color: inherit;
        }

        button,
        input,
        select {
            font: inherit;
        }

        .page {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 18px 20px 40px;
        }


        /* =========================
           HEADER
        ========================= */

        .header {
            position: relative;

            display: grid;
            grid-template-columns: 42px 1fr 42px;

            align-items: center;

            height: 52px;
            margin-bottom: 18px;
        }

        .header-title {
            text-align: center;

            font-size: 21px;
            font-weight: 700;

            letter-spacing: -0.3px;
        }

        .header-btn {
            width: 42px;
            height: 42px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #e5e7eb;
            border-radius: 12px;

            background: #fff;
            text-decoration: none;
            color: #111827;

            transition: .15s ease;
        }

        .header-btn:hover {
            border-color: #cfd3d8;
            transform: translateY(-1px);
        }

        .header-btn svg {
            width: 19px;
            height: 19px;

            stroke: currentColor;
            fill: none;

            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .add-btn {
            background: #111827;
            color: #fff;
            border-color: #111827;
        }

        .add-btn:hover {
            background: #1f2937;
            border-color: #1f2937;
        }

        .add-btn svg {
            width: 20px;
            height: 20px;
        }


        /* =========================
           FILTERS
        ========================= */

        .filters {
            display: grid;

            /*
             * ВАЖНО:
             * первая строка — склад,
             * вторая — От / До,
             * третья — кнопка.
             */
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);

            gap: 8px;

            width: 100%;

            padding: 9px;
            margin-bottom: 14px;

            background: #fff;

            border: 1px solid #e7e9ed;
            border-radius: 14px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .025);
        }

        .filter {
            position: relative;

            width: 100%;
            min-width: 0;
        }

        /*
         * Склад занимает всю ширину.
         */
        .filter:first-child {
            grid-column: 1 / -1;
        }

        /*
         * От и До занимают строго
         * по 50% доступного пространства.
         */
        .filter:nth-child(2),
        .filter:nth-child(3) {
            grid-column: auto;

            width: 100%;
            min-width: 0;
        }

        .filter-label {
            position: absolute;

            left: 11px;
            top: 5px;

            z-index: 2;

            font-size: 9px;
            line-height: 1;

            color: #9ca3af;

            pointer-events: none;
        }

        .filter input,
        .filter select {
            display: block;

            width: 100%;
            min-width: 0;

            height: 45px;

            margin: 0;

            padding: 15px 10px 3px;

            border: 1px solid #e5e7eb;
            border-radius: 10px;

            background: #fafafa;
            color: #111827;

            outline: none;

            font-size: 13px;
            line-height: 1.2;

            appearance: auto;
        }

        .filter input:focus,
        .filter select:focus {
            background: #fff;
            border-color: #9ca3af;
        }

        /*
         * Кнопка занимает всю строку.
         */
        .filter-button {
            grid-column: 1 / -1;

            width: 100%;
            min-width: 0;

            height: 45px;

            padding: 0 18px;

            border: 0;
            border-radius: 10px;

            background: #111827;
            color: #fff;

            cursor: pointer;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 13px;
            font-weight: 600;
        }

        .filter-button:hover {
            background: #1f2937;
        }


        /* =========================
           ALERTS
        ========================= */

        .alert {
            padding: 12px 14px;

            margin-bottom: 12px;

            border-radius: 11px;

            font-size: 13px;
        }

        .alert-success {
            background: #ecfdf3;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }


        /* =========================
           DESKTOP TABLE
        ========================= */

        .table-wrapper {
            width: 100%;

            overflow-x: auto;

            background: #fff;

            border: 1px solid #e7e9ed;
            border-radius: 14px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .025);
        }

        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 760px;
        }

        thead {
            background: #f8f9fa;
        }

        th {
            padding: 13px 15px;

            text-align: left;

            color: #6b7280;

            font-size: 11px;
            font-weight: 600;

            border-bottom: 1px solid #e7e9ed;

            white-space: nowrap;
        }

        td {
            padding: 14px 15px;

            font-size: 13px;

            border-bottom: 1px solid #f0f1f3;

            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        .id-cell {
            font-weight: 700;
            white-space: nowrap;
        }

        .date-cell {
            white-space: nowrap;
            color: #4b5563;
        }

        .warehouse-cell {
            font-weight: 600;
        }

        .products-cell {
            min-width: 230px;
        }

        .product-line {
            margin-bottom: 4px;

            font-size: 12px;
            line-height: 1.4;
        }

        .product-line:last-child {
            margin-bottom: 0;
        }

        .product-sku {
            font-weight: 600;
        }

        .product-batch {
            color: #9ca3af;
        }

        .product-qty {
            color: #374151;
        }

        .comment-cell {
            max-width: 220px;

            color: #6b7280;

            font-size: 12px;
        }

        .no-comment {
            color: #d1d5db;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;

            gap: 6px;

            white-space: nowrap;
        }

        .action-btn {
            width: 34px;
            height: 34px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;
            border: 1px solid #e5e7eb;

            background: #fff;

            text-decoration: none;

            color: #4b5563;

            transition: .15s ease;
        }

        .action-btn:hover {
            background: #f8f9fa;
            border-color: #d1d5db;
        }

        .action-btn svg {
            width: 16px;
            height: 16px;

            fill: none;
            stroke: currentColor;

            stroke-width: 1.8;

            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .action-delete {
            color: #dc2626;
        }

        .action-delete:hover {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .show-btn {
            color: #111827;
        }


        /* =========================
           EMPTY
        ========================= */

        .empty {
            padding: 55px 20px;

            text-align: center;

            background: #fff;

            border: 1px solid #e7e9ed;
            border-radius: 14px;

            color: #9ca3af;

            font-size: 14px;
        }

        .table-empty {
            padding: 45px 20px;

            text-align: center;

            color: #9ca3af;

            font-size: 13px;
        }


        /* =========================
           MOBILE CARDS
        ========================= */

        .receipts {
            display: none;
        }


        /* =========================
           PAGINATION
        ========================= */

        .pagination {
            display: flex;

            justify-content: center;

            margin-top: 18px;
        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 700px) {

            .page {
                width: 100%;

                padding: 10px 10px 30px;
            }


            /* =====================
               HEADER
            ===================== */

            .header {
                margin-bottom: 12px;
            }

            .header-title {
                font-size: 19px;
            }

            .header-btn {
                width: 40px;
                height: 40px;

                border-radius: 11px;
            }


            /* =====================
               FILTERS
            ===================== */

            .filters {

                /*
                 * Две абсолютно одинаковые колонки.
                 */
                grid-template-columns:
                    minmax(0, 1fr)
                    minmax(0, 1fr);

                gap: 7px;

                width: 100%;

                padding: 7px;

                border-radius: 14px;
            }

            /*
             * Склад.
             */
            .filter:first-child {
                grid-column: 1 / -1;
            }

            /*
             * От.
             */
            .filter:nth-child(2) {
                grid-column: 1;
                width: 100%;
            }

            /*
             * До.
             */
            .filter:nth-child(3) {
                grid-column: 2;
                width: 100%;
            }

            /*
             * Инпуты обязательно
             * остаются внутри своих колонок.
             */
            .filter input,
            .filter select {
                width: 100%;
                min-width: 0;

                height: 45px;

                padding-left: 10px;
                padding-right: 8px;

                font-size: 13px;
            }

            /*
             * Кнопка.
             */
            .filter-button {
                grid-column: 1 / -1;

                width: 100%;

                height: 45px;

                font-size: 13px;
            }


            /* =====================
               TABLE HIDDEN
            ===================== */

            .table-wrapper {
                display: none;
            }


            /* =====================
               CARDS
            ===================== */

            .receipts {
                display: flex;

                flex-direction: column;

                gap: 7px;
            }

            .receipt {
                display: grid;

                grid-template-columns: minmax(0, 1fr) auto;

                gap: 5px 10px;

                padding: 13px;

                background: #fff;

                border: 1px solid #e7e9ed;

                border-radius: 13px;

                text-decoration: none;

                transition: .15s ease;
            }

            .receipt:active {
                transform: scale(.99);
            }

            .receipt-id {
                grid-column: 1;
                grid-row: 1;

                font-size: 13px;

                font-weight: 700;
            }

            .receipt-date {
                grid-column: 2;
                grid-row: 1;

                text-align: right;

                color: #6b7280;

                font-size: 12px;
            }

            .receipt-main {
                grid-column: 1;
                grid-row: 2;

                min-width: 0;
            }

            .receipt-warehouse {
                font-size: 13px;

                font-weight: 600;

                white-space: nowrap;

                overflow: hidden;

                text-overflow: ellipsis;
            }

            .receipt-meta {
                margin-top: 3px;

                color: #9ca3af;

                font-size: 11px;
            }

            .receipt-arrow {
                grid-column: 2;
                grid-row: 2;

                width: 34px;
                height: 34px;

                display: flex;

                align-items: center;
                justify-content: center;

                color: #9ca3af;

                border-radius: 9px;

                background: #f8f9fa;
            }

            .receipt-arrow svg {
                width: 16px;
                height: 16px;

                fill: none;

                stroke: currentColor;

                stroke-width: 2;

                stroke-linecap: round;
                stroke-linejoin: round;
            }

            .empty {
                padding: 45px 15px;

                border-radius: 13px;

                font-size: 13px;
            }

        }

    </style>

</head>


<body>


    <div class="page">


        {{-- =========================
             HEADER
        ========================= --}}

        <div class="header">

            <a
                href="{{ auth()->user()->role === 'analyst'
                    ? route('admin.analytics.receipts')
                    : url('/admin') }}"
                class="header-btn"
                title="Назад">

                <svg viewBox="0 0 24 24">

                    <path d="M19 12H5"></path>

                    <path d="M12 19l-7-7 7-7"></path>

                </svg>

            </a>


            <div class="header-title">

                Приёмки

            </div>


            <a
                href="{{ route('admin.receipts.create') }}"
                class="header-btn add-btn"
                title="Новая приёмка">

                <svg viewBox="0 0 24 24">

                    <path d="M12 5v14"></path>

                    <path d="M5 12h14"></path>

                </svg>

            </a>

        </div>


        {{-- =========================
             ALERTS
        ========================= --}}

        @if(session('success'))

            <div class="alert alert-success">

                {{ session('success') }}

            </div>

        @endif


        @if(session('error'))

            <div class="alert alert-error">

                {{ session('error') }}

            </div>

        @endif


        @if($errors->any())

            <div class="alert alert-error">

                @foreach($errors->all() as $error)

                    <div>

                        {{ $error }}

                    </div>

                @endforeach

            </div>

        @endif


        {{-- =========================
             FILTERS
        ========================= --}}

        <form
            method="GET"
            action="{{ route('admin.receipts.index') }}"
            class="filters">


            {{-- Склад --}}

            <div class="filter">

                <span class="filter-label">

                    Склад

                </span>


                <select name="warehouse_id">

                    <option value="">

                        Все склады

                    </option>


                    @foreach($warehouses as $warehouse)

                        <option
                            value="{{ $warehouse->id }}"
                            @selected(request('warehouse_id') == $warehouse->id)
                        >

                            {{ $warehouse->name }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- От --}}

            <div class="filter">

                <span class="filter-label">

                    От

                </span>


                <input
                    type="date"
                    name="from"
                    value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}"
                >

            </div>


            {{-- До --}}

            <div class="filter">

                <span class="filter-label">

                    До

                </span>


                <input
                    type="date"
                    name="to"
                    value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}"
                >

            </div>


            {{-- Применить --}}

            <button
                type="submit"
                class="filter-button">

                Применить

            </button>


        </form>


        {{-- ==================================================
             DESKTOP TABLE
        ================================================== --}}

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Дата
                        </th>

                        <th>
                            Склад
                        </th>

                        <th>
                            Комментарий
                        </th>

                        <th>
                            Действия
                        </th>

                    </tr>

                </thead>


                <tbody>


                    @forelse($receipts as $receipt)


                        <tr>


                            {{-- ID --}}

                            <td class="id-cell">

                                #{{ $receipt->id }}

                            </td>


                            {{-- Дата --}}

                            <td class="date-cell">

                                {{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d.m.Y') }}

                            </td>


                            {{-- Склад --}}

                            <td class="warehouse-cell">

                                {{ $receipt->warehouse->name }}

                            </td>


                            {{-- Комментарий --}}

                            <td class="comment-cell">


                                @if($receipt->comment)

                                    {{ $receipt->comment }}

                                @else

                                    <span class="no-comment">

                                        —

                                    </span>

                                @endif


                            </td>


                            {{-- Действия --}}

                            <td>

                                <div class="actions">


                                    {{-- Подробнее --}}

                                    <a
                                        href="{{ route('admin.receipts.show', $receipt) }}"
                                        class="action-btn show-btn"
                                        title="Подробнее">

                                        <svg viewBox="0 0 24 24">

                                            <path d="M9 18l6-6-6-6"></path>

                                        </svg>

                                    </a>


                                    {{-- Изменить --}}

                                    <a
                                        href="{{ route('admin.receipts.edit', $receipt) }}"
                                        class="action-btn"
                                        title="Изменить">

                                        <svg viewBox="0 0 24 24">

                                            <path d="M12 20h9"></path>

                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>

                                        </svg>

                                    </a>


                                    {{-- Удалить --}}

                                    <form
                                        action="{{ route('admin.receipts.destroy', $receipt) }}"
                                        method="POST"
                                        style="display:inline"
                                        onsubmit="return confirm('Удалить эту приёмку? Неиспользованный остаток будет убран со склада.');"
                                    >

                                        @csrf

                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            class="action-btn action-delete"
                                            title="Удалить">

                                            <svg viewBox="0 0 24 24">

                                                <path d="M3 6h18"></path>

                                                <path d="M8 6V4h8v2"></path>

                                                <path d="M19 6l-1 14H6L5 6"></path>

                                                <path d="M10 11v5"></path>

                                                <path d="M14 11v5"></path>

                                            </svg>

                                        </button>

                                    </form>


                                </div>

                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td
                                colspan="6"
                                class="table-empty">

                                За выбранный период приёмок нет

                            </td>

                        </tr>


                    @endforelse


                </tbody>

            </table>

        </div>


        {{-- ==================================================
             MOBILE CARDS
        ================================================== --}}

        <div class="receipts">


            @forelse($receipts as $receipt)


                <a
                    href="{{ route('admin.receipts.show', $receipt) }}"
                    class="receipt">


                    <div class="receipt-id">

                        #{{ $receipt->id }}

                    </div>


                    <div class="receipt-date">

                        {{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d.m.Y') }}

                    </div>


                    <div class="receipt-main">


                        <div class="receipt-warehouse">

                            {{ $receipt->warehouse->name }}

                        </div>


                        <div class="receipt-meta">

                            {{ $receipt->items->count() }}

                            {{ $receipt->items->count() == 1 ? 'позиция' : 'позиций' }}

                            ·

                            {{ $receipt->items->sum('quantity') }}

                            шт.


                            @if($receipt->comment)

                                · есть комментарий

                            @endif

                        </div>


                    </div>


                    <div class="receipt-arrow">

                        <svg viewBox="0 0 24 24">

                            <path d="M9 18l6-6-6-6"></path>

                        </svg>

                    </div>


                </a>


            @empty


                <div class="empty">

                    За выбранный период приёмок нет

                </div>


            @endforelse


        </div>


        {{-- =========================
             PAGINATION
        ========================= --}}

        @if($receipts->hasPages())

            <div class="pagination">

                {{ $receipts->links() }}

            </div>

        @endif


    </div>


</body>

</html>
