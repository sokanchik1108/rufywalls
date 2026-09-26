<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Оприходования</title>

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
            background: #f4f6f8;
            color: #1a1d21;
            font-family: Georgia, "Times New Roman", serif;
        }

        a,
        button,
        input,
        select {
            font-family: inherit;
        }

        /* =========================
           PAGE
        ========================= */

        .page {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 24px;
        }

        /* =========================
           HEADER
        ========================= */

        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        .top-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .back-btn {
            width: 42px;
            height: 42px;

            flex: 0 0 42px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            background: #ffffff;
            color: #01142f;

            border: 1px solid #e5e8ed;

            text-decoration: none;

            font-family: Arial, sans-serif;
            font-size: 21px;
            line-height: 1;

            transition: .2s;
        }

        .back-btn:hover {
            background: #f0f3f7;
        }

        .title-wrap {
            min-width: 0;
        }

        .title {
            margin: 0;

            color: #01142f;

            font-size: 26px;
            line-height: 1.2;
            font-weight: 700;
        }

        .subtitle {
            margin-top: 5px;

            color: #7a828d;

            font-size: 13px;
            line-height: 1.3;
        }

        .create-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            min-height: 44px;

            padding: 0 17px;

            border-radius: 11px;

            background: #01142f;
            color: #ffffff;

            text-decoration: none;

            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: 600;

            white-space: nowrap;

            transition: .2s;

            flex-shrink: 0;
        }

        .create-btn:hover {
            background: #02214b;
        }

        .plus {
            font-family: Arial, sans-serif;
            font-size: 20px;
            line-height: 1;
            font-weight: 400;
        }

        /* =========================
           ALERTS
        ========================= */

        .alerts {
            display: flex;
            flex-direction: column;
            gap: 10px;

            margin-bottom: 18px;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;

            padding: 14px 16px;

            border-radius: 12px;

            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-icon {
            width: 25px;
            height: 25px;

            flex: 0 0 25px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            font-weight: 700;
            font-size: 13px;
        }

        .alert-content {
            min-width: 0;
        }

        .alert-title {
            margin-bottom: 2px;
            font-weight: 700;
        }

        .alert-success {
            background: #eaf8ef;
            border: 1px solid #ccebd7;
            color: #176b36;
        }

        .alert-success .alert-icon {
            background: #ccebd7;
            color: #176b36;
        }

        .alert-error {
            background: #fff0f0;
            border: 1px solid #f1cccc;
            color: #a72d2d;
        }

        .alert-error .alert-icon {
            background: #f1cccc;
            color: #a72d2d;
        }

        /* =========================
           FILTER CARD
        ========================= */

        .filter-card {
            width: 100%;

            background: #ffffff;

            border: 1px solid #e5e8ed;
            border-radius: 15px;

            padding: 18px;

            margin-bottom: 18px;
        }

        .filters {
            display: grid;

            grid-template-columns:
                minmax(180px, 1fr)
                minmax(180px, 1fr)
                minmax(180px, 1fr)
                auto;

            gap: 12px;

            align-items: end;
        }

        .field {
            min-width: 0;
            width: 100%;
        }

        .field label {
            display: block;

            margin-bottom: 7px;

            color: #555e69;

            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: 600;
        }

        /* =========================
           ОДИНАКОВЫЕ INPUT / SELECT
        ========================= */

        .field input,
        .field select {
            width: 100%;
            height: 44px;

            padding: 0 12px;

            border: 1px solid #dfe3e8;
            border-radius: 10px;

            background: #ffffff;
            color: #1a1d21;

            font-family: Georgia, "Times New Roman", serif;
            font-size: 14px;

            outline: none;

            transition: .2s;
        }

        .field input:focus,
        .field select:focus {
            border-color: #7b8795;

            box-shadow:
                0 0 0 3px rgba(1, 20, 47, .06);
        }

        /* =========================
           DATE CONTROL
           ТУТ ИСПРАВЛЕНО
        ========================= */

        .date-control {
            position: relative;

            width: 100%;
            height: 44px;

            min-width: 0;
            max-width: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            overflow: hidden;

            border: 1px solid #dfe3e8;
            border-radius: 10px;

            background: #ffffff;

            transition: .2s;
        }

        .date-control:focus-within {
            border-color: #7b8795;

            box-shadow:
                0 0 0 3px rgba(1, 20, 47, .06);
        }

        /*
         * Визуальная дата.
         * Именно она гарантирует идеальное
         * центрирование текста.
         */

        .date-display {
            position: absolute;

            inset: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 0 36px 0 10px;

            color: #1a1d21;

            font-family: Georgia, "Times New Roman", serif;
            font-size: 14px;

            line-height: 1;

            text-align: center;

            white-space: nowrap;

            pointer-events: none;

            z-index: 1;
        }

        /*
         * Настоящий date input.
         * Он остается кликабельным,
         * но его собственный текст скрыт.
         */

        .date-control input[type="date"] {
            position: absolute;

            inset: 0;

            width: 100%;
            height: 100%;

            margin: 0;
            padding: 0;

            border: 0 !important;
            outline: none !important;

            background: transparent;

            opacity: 0;

            cursor: pointer;

            z-index: 2;

            box-shadow: none !important;

            appearance: auto;
            -webkit-appearance: auto;
        }

        .date-calendar-icon {
            position: absolute;

            right: 10px;
            top: 50%;

            width: 17px;
            height: 17px;

            transform: translateY(-50%);

            color: #555e69;

            pointer-events: none;

            z-index: 3;
        }

        .date-calendar-icon svg {
            display: block;

            width: 17px;
            height: 17px;
        }

        /* =========================
           FILTER BUTTONS
        ========================= */

        .filter-actions {
            display: flex;
            align-items: center;

            gap: 8px;

            height: 44px;
        }

        .btn {
            height: 44px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 0 15px;

            border: 0;
            border-radius: 10px;

            text-decoration: none;

            cursor: pointer;

            font-family: Georgia, "Times New Roman", serif;
            font-size: 13px;
            font-weight: 600;

            white-space: nowrap;

            transition: .2s;
        }

        .btn-primary {
            background: #01142f;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #02214b;
        }

        .btn-light {
            background: #f1f3f5;
            color: #343a40;
        }

        .btn-light:hover {
            background: #e6e9ed;
        }

        /* =========================
           TABLE CARD
        ========================= */

        .table-card {
            overflow: hidden;

            background: #ffffff;

            border: 1px solid #e5e8ed;
            border-radius: 15px;
        }

        .table-head {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;

            padding: 17px 18px;

            border-bottom: 1px solid #edf0f3;
        }

        .table-title {
            color: #01142f;

            font-size: 15px;
            font-weight: 700;
        }

        .table-count {
            color: #7a828d;

            font-size: 12px;
        }

        .table-wrap {
            width: 100%;

            overflow-x: auto;

            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;

            min-width: 780px;

            border-collapse: collapse;
        }

        th {
            padding: 12px 16px;

            background: #fafbfc;

            border-bottom: 1px solid #edf0f3;

            color: #737b86;

            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: 700;

            text-align: left;

            text-transform: uppercase;
            letter-spacing: .03em;

            white-space: nowrap;
        }

        td {
            padding: 14px 16px;

            border-bottom: 1px solid #f0f2f4;

            color: #30353b;

            font-family: Arial, sans-serif;
            font-size: 13px;

            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover {
            background: #fafbfc;
        }

        .number {
            color: #01142f;

            font-weight: 700;

            white-space: nowrap;
        }

        .date {
            white-space: nowrap;
            color: #444b54;
        }

        .warehouse {
            white-space: nowrap;
            font-weight: 600;
        }

        .items-count {
            color: #5f6873;
            white-space: nowrap;
        }

        .comment {
            max-width: 300px;

            color: #69727d;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* =========================
           ACTIONS
        ========================= */

        .actions {
            display: flex;
            align-items: center;

            gap: 6px;

            white-space: nowrap;
        }

        .action {
            width: 34px;
            height: 34px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            border: 1px solid #e1e5e9;

            background: #ffffff;
            color: #4d5661;

            text-decoration: none;

            cursor: pointer;

            font-family: Arial, sans-serif;
            font-size: 15px;

            transition: .2s;
        }

        .action:hover {
            background: #f3f5f7;
        }

        .action-delete {
            color: #b33a3a;
        }

        .action-delete:hover {
            background: #fff0f0;
            border-color: #f0cccc;
        }

        /* =========================
           EMPTY
        ========================= */

        .empty {
            padding: 55px 20px;

            text-align: center;
        }

        .empty-icon {
            width: 48px;
            height: 48px;

            margin: 0 auto 13px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 13px;

            background: #f1f3f5;
            color: #69727d;

            font-family: Arial, sans-serif;
            font-size: 21px;
        }

        .empty-title {
            margin-bottom: 5px;

            color: #252a30;

            font-size: 15px;
            font-weight: 700;
        }

        .empty-text {
            color: #858d97;

            font-size: 13px;
        }

        /* =========================
           PAGINATION
        ========================= */

        .pagination-wrap {
            padding: 15px 18px;

            border-top: 1px solid #edf0f3;
        }

        .pagination {
            display: flex;
            align-items: center;

            gap: 5px;

            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            min-width: 34px;
            height: 34px;

            padding: 0 9px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #e1e5e9;
            border-radius: 8px;

            background: #ffffff;
            color: #505861;

            font-family: Arial, sans-serif;
            font-size: 12px;

            text-decoration: none;
        }

        .pagination a:hover {
            background: #f3f5f7;
        }

        .pagination .active span {
            background: #01142f;
            border-color: #01142f;
            color: #ffffff;
        }

        .pagination .disabled span {
            opacity: .45;
        }

        /* =========================
           TABLET
        ========================= */

        @media (max-width: 900px) {

            .filters {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            /*
             * От
             * До
             */

            .filters .field:nth-child(1),
            .filters .field:nth-child(2) {
                grid-column: span 1;
            }

            /*
             * Склад
             */

            .filters .field:nth-child(3) {
                grid-column: 1 / -1;
            }

            /*
             * Кнопки
             */

            .filter-actions {
                grid-column: 1 / -1;

                width: 100%;
            }

            .filter-actions .btn {
                flex: 1;
            }
        }

        /* =========================
           PHONE
        ========================= */

        @media (max-width: 700px) {

            .page {
                padding: 16px;
            }

            .top {
                align-items: center;

                gap: 10px;

                margin-bottom: 18px;
            }

            .top-left {
                align-items: center;

                gap: 10px;
            }

            .title {
                font-size: 22px;
            }

            .subtitle {
                font-size: 12px;
            }

            /*
             * Кнопки одинакового размера
             */

            .back-btn,
            .create-btn {
                width: 44px;
                height: 44px;

                min-height: 44px;

                flex: 0 0 44px;

                padding: 0;

                border-radius: 11px;
            }

            .back-btn {
                font-size: 21px;
            }

            .create-btn {
                font-size: 0;
            }

            .create-btn .plus {
                font-size: 22px;
            }

            /*
             * Фильтры:
             * От | До
             * Склад
             * Кнопки
             */

            .filters {
                display: grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 8px;
            }

            .filters .field {
                width: 100%;
                min-width: 0;
            }

            .filters .field:nth-child(1),
            .filters .field:nth-child(2) {
                grid-column: span 1;
            }

            .filters .field:nth-child(3) {
                grid-column: 1 / -1;
            }

            /*
             * Даты всегда одинаковой ширины
             */

            .date-control {
                width: 100%;
                min-width: 0;
                max-width: 100%;

                height: 44px;
            }

            .date-display {
                font-size: 12px;

                padding-left: 8px;
                padding-right: 34px;
            }

            .date-calendar-icon {
                right: 9px;
            }

            /*
             * Склад
             */

            .field select {
                width: 100%;
                min-width: 0;

                height: 44px;

                padding: 0 8px;

                font-size: 13px;
            }

            /*
             * Кнопки
             */

            .filter-actions {
                grid-column: 1 / -1;

                width: 100%;

                display: flex;

                flex-direction: row;

                gap: 8px;
            }

            .filter-actions .btn {
                width: auto;

                flex: 1;

                min-width: 0;
            }

            .filter-card {
                padding: 15px;

                border-radius: 15px;
            }

            .table-head {
                padding: 15px;
            }
        }

        /* =========================
           SMALL PHONE
        ========================= */

        @media (max-width: 480px) {

            .page {
                padding: 12px;
            }

            .top {
                margin-bottom: 18px;

                gap: 8px;
            }

            .top-left {
                gap: 8px;
            }

            .back-btn,
            .create-btn {
                width: 40px;
                height: 40px;

                min-height: 40px;

                flex: 0 0 40px;

                border-radius: 10px;

                padding: 0;
            }

            .back-btn {
                font-size: 18px;
            }

            .create-btn .plus {
                font-size: 20px;
            }

            .title {
                font-size: 20px;
            }

            .subtitle {
                margin-top: 3px;

                font-size: 11px;
            }

            /*
             * ALERT
             */

            .alert {
                padding: 12px;

                font-size: 13px;
            }

            /*
             * FILTER
             */

            .filter-card {
                padding: 13px;

                border-radius: 13px;
            }

            /*
             * ОТ / ДО
             */

            .filters {
                display: grid;

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 6px;
            }

            .filters .field {
                width: 100%;
                min-width: 0;
            }

            .filters .field:nth-child(1),
            .filters .field:nth-child(2) {
                grid-column: span 1;
            }

            .filters .field:nth-child(3) {
                grid-column: 1 / -1;
            }

            /*
             * Дата
             */

            .date-control {
                width: 100%;
                height: 44px;

                min-width: 0;
                max-width: 100%;

                border-radius: 10px;
            }

            .date-display {
                font-size: 11px;

                padding-left: 5px;
                padding-right: 30px;
            }

            .date-calendar-icon {
                right: 8px;

                width: 16px;
                height: 16px;
            }

            .date-calendar-icon svg {
                width: 16px;
                height: 16px;
            }

            /*
             * LABEL
             */

            .field label {
                margin-bottom: 6px;

                font-size: 11px;
            }

            /*
             * СКЛАД
             */

            .field select {
                width: 100%;
                height: 44px;

                min-width: 0;

                padding: 0 8px;

                font-size: 12px;
            }

            /*
             * КНОПКИ
             */

            .filter-actions {
                grid-column: 1 / -1;

                width: 100%;

                display: flex;
                flex-direction: row;

                gap: 6px;
            }

            .filter-actions .btn {
                width: auto;

                flex: 1;

                min-width: 0;

                padding: 0 8px;
            }

            /*
             * TABLE
             */

            .table-card {
                border-radius: 13px;
            }

            .table-head {
                padding: 15px;
            }

            /*
             * PAGINATION
             */

            .pagination-wrap {
                padding: 13px;

                overflow-x: auto;
            }

            .pagination {
                flex-wrap: nowrap;

                width: max-content;
            }
        }
    </style>
</head>

<body>

<div class="page">

    {{-- =========================
         HEADER
    ========================== --}}

    <div class="top">

        <div class="top-left">

            <a
                href="{{ route('home') }}"
                class="back-btn"
                title="Назад">
                ←
            </a>

            <div class="title-wrap">

                <h1 class="title">
                    Оприходования
                </h1>

                <div class="subtitle">
                    Учет поступлений товара без приемки
                </div>

            </div>

        </div>

        <a
            href="{{ route('admin.appropriations.create') }}"
            class="create-btn">

            <span class="plus">
                +
            </span>

        </a>

    </div>


    {{-- =========================
         ALERTS
    ========================== --}}

    <div class="alerts">

        @if(session('success'))

            <div class="alert alert-success">

                <div class="alert-icon">
                    ✓
                </div>

                <div class="alert-content">

                    <div class="alert-title">
                        Успешно
                    </div>

                    <div>
                        {{ session('success') }}
                    </div>

                </div>

            </div>

        @endif


        @if(session('appropriation_error'))

            <div class="alert alert-error">

                <div class="alert-icon">
                    !
                </div>

                <div class="alert-content">

                    <div class="alert-title">
                        Операция запрещена
                    </div>

                    <div>
                        {{ session('appropriation_error') }}
                    </div>

                </div>

            </div>

        @endif


        @if(session('error'))

            <div class="alert alert-error">

                <div class="alert-icon">
                    !
                </div>

                <div class="alert-content">

                    <div class="alert-title">
                        Ошибка
                    </div>

                    <div>
                        {{ session('error') }}
                    </div>

                </div>

            </div>

        @endif


        @if($errors->any())

            <div class="alert alert-error">

                <div class="alert-icon">
                    !
                </div>

                <div class="alert-content">

                    <div class="alert-title">
                        Проверьте данные
                    </div>

                    <div>

                        <ul style="margin:5px 0 0 18px; padding:0;">

                            @foreach($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif

    </div>


    {{-- =========================
         FILTERS
    ========================== --}}

    <div class="filter-card">

        <form
            method="GET"
            action="{{ route('admin.appropriations.index') }}">

            <div class="filters">

                {{-- ОТ --}}

                <div class="field">

                    <label for="from">
                        От
                    </label>

                    <div class="date-control">

                        <span
                            class="date-display"
                            id="from-display">
                        </span>

                        <input
                            type="date"
                            id="from"
                            name="from"
                            value="{{ $from }}">

                        <span class="date-calendar-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="17"
                                    rx="2">
                                </rect>

                                <line
                                    x1="16"
                                    y1="2"
                                    x2="16"
                                    y2="6">
                                </line>

                                <line
                                    x1="8"
                                    y1="2"
                                    x2="8"
                                    y2="6">
                                </line>

                                <line
                                    x1="3"
                                    y1="9"
                                    x2="21"
                                    y2="9">
                                </line>

                            </svg>

                        </span>

                    </div>

                </div>


                {{-- ДО --}}

                <div class="field">

                    <label for="to">
                        До
                    </label>

                    <div class="date-control">

                        <span
                            class="date-display"
                            id="to-display">
                        </span>

                        <input
                            type="date"
                            id="to"
                            name="to"
                            value="{{ $to }}">

                        <span class="date-calendar-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="17"
                                    rx="2">
                                </rect>

                                <line
                                    x1="16"
                                    y1="2"
                                    x2="16"
                                    y2="6">
                                </line>

                                <line
                                    x1="8"
                                    y1="2"
                                    x2="8"
                                    y2="6">
                                </line>

                                <line
                                    x1="3"
                                    y1="9"
                                    x2="21"
                                    y2="9">
                                </line>

                            </svg>

                        </span>

                    </div>

                </div>


                {{-- СКЛАД --}}

                <div class="field">

                    <label for="warehouse_id">
                        Склад
                    </label>

                    <select
                        id="warehouse_id"
                        name="warehouse_id">

                        <option value="">
                            Все склады
                        </option>

                        @foreach($warehouses as $warehouse)

                            <option
                                value="{{ $warehouse->id }}"
                                @selected(
                                    (string) request('warehouse_id') ===
                                    (string) $warehouse->id
                                )>

                                {{ $warehouse->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- КНОПКИ --}}

                <div class="filter-actions">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Применить

                    </button>

                    <a
                        href="{{ route('admin.appropriations.index') }}"
                        class="btn btn-light">

                        Сбросить

                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- =========================
         TABLE
    ========================== --}}

    <div class="table-card">

        <div class="table-head">

            <div class="table-title">
                Список оприходований
            </div>

            <div class="table-count">

                {{ $appropriations->total() }}

                {{ $appropriations->total() == 1
                    ? 'документ'
                    : 'документов'
                }}

            </div>

        </div>


        @if($appropriations->count())

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                №
                            </th>

                            <th>
                                Дата
                            </th>

                            <th>
                                Склад
                            </th>

                            <th>
                                Позиций
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

                        @foreach($appropriations as $appropriation)

                            <tr>

                                {{-- № --}}

                                <td>

                                    <span class="number">
                                        #{{ $appropriation->id }}
                                    </span>

                                </td>


                                {{-- Дата --}}

                                <td>

                                    <span class="date">

                                        {{
                                            $appropriation
                                                ->appropriation_date
                                                ?->format('d.m.Y')
                                        }}

                                    </span>

                                </td>


                                {{-- Склад --}}

                                <td>

                                    <span class="warehouse">

                                        {{
                                            $appropriation->warehouse->name
                                            ?? '—'
                                        }}

                                    </span>

                                </td>


                                {{-- Позиций --}}

                                <td>

                                    <span class="items-count">

                                        {{
                                            $appropriation
                                                ->items
                                                ->count()
                                        }}

                                    </span>

                                </td>


                                {{-- Комментарий --}}

                                <td>

                                    @if($appropriation->comment)

                                        <div
                                            class="comment"
                                            title="{{ $appropriation->comment }}">

                                            {{ $appropriation->comment }}

                                        </div>

                                    @else

                                        <span style="color:#a1a8b0;">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Действия --}}

                                <td>

                                    <div class="actions">

                                        {{-- Просмотр --}}

                                        <a
                                            href="{{
                                                route(
                                                    'admin.appropriations.show',
                                                    $appropriation
                                                )
                                            }}"
                                            class="action"
                                            title="Просмотреть">

                                            👁

                                        </a>


                                        {{-- Редактирование --}}

                                        <a
                                            href="{{
                                                route(
                                                    'admin.appropriations.edit',
                                                    $appropriation
                                                )
                                            }}"
                                            class="action"
                                            title="Редактировать">

                                            ✎

                                        </a>


                                        {{-- Удаление --}}

                                        <form
                                            action="{{
                                                route(
                                                    'admin.appropriations.destroy',
                                                    $appropriation
                                                )
                                            }}"
                                            method="POST"
                                            onsubmit="return confirm(
                                                'Удалить оприходование #{{ $appropriation->id }}?\n\n' +
                                                'Если товар из этого оприходования уже использован в продажах или списаниях, удаление будет запрещено.'
                                            );"
                                            style="margin:0;">

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="action action-delete"
                                                title="Удалить">

                                                🗑

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- =========================
                 PAGINATION
            ========================== --}}

            @if($appropriations->hasPages())

                <div class="pagination-wrap">

                    <div class="pagination">

                        {{-- Previous --}}

                        @if($appropriations->onFirstPage())

                            <div class="disabled">

                                <span>
                                    ‹
                                </span>

                            </div>

                        @else

                            <a
                                href="{{ $appropriations->previousPageUrl() }}">

                                ‹

                            </a>

                        @endif


                        {{-- Pages --}}

                        @foreach(
                            $appropriations->getUrlRange(
                                max(
                                    1,
                                    $appropriations->currentPage() - 2
                                ),
                                min(
                                    $appropriations->lastPage(),
                                    $appropriations->currentPage() + 2
                                )
                            ) as $page => $url
                        )

                            @if(
                                $page ==
                                $appropriations->currentPage()
                            )

                                <div class="active">

                                    <span>
                                        {{ $page }}
                                    </span>

                                </div>

                            @else

                                <a href="{{ $url }}">
                                    {{ $page }}
                                </a>

                            @endif

                        @endforeach


                        {{-- Next --}}

                        @if($appropriations->hasMorePages())

                            <a
                                href="{{ $appropriations->nextPageUrl() }}">

                                ›

                            </a>

                        @else

                            <div class="disabled">

                                <span>
                                    ›
                                </span>

                            </div>

                        @endif

                    </div>

                </div>

            @endif


        @else

            {{-- =========================
                 EMPTY
            ========================== --}}

            <div class="empty">

                <div class="empty-icon">
                    +
                </div>

                <div class="empty-title">
                    Оприходований пока нет
                </div>

                <div class="empty-text">
                    За выбранный период документы не найдены.
                </div>

            </div>

        @endif

    </div>

</div>


{{-- =========================
     DATE DISPLAY SCRIPT
========================== --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {

        function formatDate(value) {

            if (!value) {
                return '';
            }

            const parts = value.split('-');

            if (parts.length !== 3) {
                return '';
            }

            const year = Number(parts[0]);
            const month = Number(parts[1]);
            const day = Number(parts[2]);

            if (!year || !month || !day) {
                return '';
            }

            const date = new Date(
                year,
                month - 1,
                day
            );

            const formatted = new Intl.DateTimeFormat(
                'ru-RU',
                {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                }
            ).format(date);

            return formatted
                .replace(' г.', ' г.')
                .replace(/\.$/, '.');
        }


        function updateDateDisplay(input) {

            const display = document.getElementById(
                input.id + '-display'
            );

            if (!display) {
                return;
            }

            if (!input.value) {

                display.textContent = '';

                return;
            }

            display.textContent = formatDate(
                input.value
            );
        }


        const dateInputs = document.querySelectorAll(
            '.date-control input[type="date"]'
        );


        dateInputs.forEach(function (input) {

            updateDateDisplay(input);

            input.addEventListener(
                'change',
                function () {
                    updateDateDisplay(input);
                }
            );

            input.addEventListener(
                'input',
                function () {
                    updateDateDisplay(input);
                }
            );

        });

    });
</script>

</body>
</html>
