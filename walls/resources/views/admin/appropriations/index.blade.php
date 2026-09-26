<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            font-family: inherit;
        }

        .page {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 24px;
        }


        /* =========================================================
           HEADER
        ========================================================= */

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
            border: 1px solid #e5e8ed;

            background: #ffffff;
            color: #01142f;

            text-decoration: none;
            font-size: 21px;

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
            line-height: 1.4;
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
            font-size: 20px;
            line-height: 1;
            font-weight: 400;
        }


        /* =========================================================
           ALERTS
        ========================================================= */

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

            font-size: 13px;
            font-weight: 700;
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


        /* =========================================================
           FILTER CARD
        ========================================================= */

        .filter-card {
            width: 100%;

            margin-bottom: 18px;
            padding: 18px;

            background: #ffffff;

            border: 1px solid #e5e8ed;
            border-radius: 15px;
        }

        /*
         * DESKTOP:
         *
         * От       До       Склад       Кнопки
         * 1fr      1fr      1.3fr       auto
         *
         * От и До одинаковой ширины.
         */

        .filters {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr)
                minmax(0, 1fr)
                minmax(0, 1.3fr)
                auto;

            gap: 12px;

            align-items: end;

            width: 100%;
        }

        .field {
            width: 100%;
            min-width: 0;
        }

        .field label {
            display: block;

            width: 100%;

            margin-bottom: 7px;

            color: #555e69;

            font-size: 12px;
            line-height: 1.2;
            font-weight: 600;
        }

        /*
         * Все поля имеют одинаковую высоту
         * и занимают всю ширину своей grid-ячейки.
         */

        .field input,
        .field select {
            display: block;

            width: 100%;
            max-width: 100%;
            min-width: 0;

            height: 44px;

            padding: 0 12px;

            border: 1px solid #dfe3e8;
            border-radius: 10px;

            background: #ffffff;
            color: #1a1d21;

            font-family: inherit;
            font-size: 14px;

            outline: none;

            transition:
                border-color .2s,
                box-shadow .2s,
                background .2s;
        }

        .field input:focus,
        .field select:focus {
            border-color: #7b8795;

            box-shadow:
                0 0 0 3px rgba(1, 20, 47, .06);
        }

        /*
         * ВАЖНО:
         * оба date input получают одинаковые параметры.
         */

        .field input[type="date"] {
            width: 100%;
            max-width: 100%;
            min-width: 0;

            height: 44px;

            padding: 0 10px;

            box-sizing: border-box;

            font-size: 14px;
        }


        /* =========================================================
           FILTER BUTTONS
        ========================================================= */

        .filter-actions {
            display: flex;
            align-items: stretch;

            gap: 8px;

            width: 100%;
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

            font-family: inherit;
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


        /* =========================================================
           TABLE CARD
        ========================================================= */

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

            white-space: nowrap;
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

            font-size: 13px;

            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover {
            background: #fafbfc;
        }


        /* =========================================================
           TABLE CONTENT
        ========================================================= */

        .number {
            color: #01142f;

            font-weight: 700;

            white-space: nowrap;
        }

        .date {
            color: #444b54;

            white-space: nowrap;
        }

        .warehouse {
            font-weight: 600;

            white-space: nowrap;
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


        /* =========================================================
           ACTIONS
        ========================================================= */

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


        /* =========================================================
           EMPTY
        ========================================================= */

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


        /* =========================================================
           PAGINATION
        ========================================================= */

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


        /* =========================================================
           TABLET
        ========================================================= */

        @media (max-width: 900px) {

            .filters {
                grid-template-columns:
                    minmax(0, 1fr)
                    minmax(0, 1fr);
            }

            /*
             * Склад занимает всю строку.
             */
            .filters .field:nth-child(3) {
                grid-column: 1 / -1;
            }

            /*
             * Кнопки занимают всю строку.
             */
            .filter-actions {
                grid-column: 1 / -1;
            }

            .filter-actions .btn {
                flex: 1 1 0;
            }
        }


        /* =========================================================
           PHONE
        ========================================================= */

        @media (max-width: 700px) {

            .page {
                padding: 16px;
            }

            .top {
                align-items: center;

                gap: 10px;

                margin-bottom: 20px;
            }

            .top-left {
                align-items: center;
            }

            .title {
                font-size: 22px;
            }

            .subtitle {
                font-size: 12px;
            }


            /* -----------------------------------------
               Header buttons
            ----------------------------------------- */

            .back-btn,
            .create-btn {
                width: 44px;
                height: 44px;

                min-height: 44px;

                flex: 0 0 44px;

                padding: 0;

                border-radius: 11px;

                font-size: 0;
            }

            .back-btn {
                font-size: 21px;
            }

            .create-btn .plus {
                font-size: 22px;
            }


            /* -----------------------------------------
               Filters
            ----------------------------------------- */

            .filter-card {
                padding: 15px;
            }

            .filters {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    minmax(0, 1fr);

                gap: 10px;

                width: 100%;
            }

            .filters .field {
                width: 100%;
                min-width: 0;

                grid-column: span 1;
            }

            /*
             * Склад на всю ширину.
             */
            .filters .field:nth-child(3) {
                grid-column: 1 / -1;
            }

            .field {
                width: 100%;
                min-width: 0;
            }

            .field label {
                display: block;

                width: 100%;

                margin-bottom: 7px;

                font-size: 11px;
            }

            /*
             * Все поля одинаковой высоты и ширины.
             */
            .field input,
            .field select {
                display: block;

                width: 100%;
                max-width: 100%;
                min-width: 0;

                height: 44px;

                box-sizing: border-box;

                padding: 0 8px;

                border-radius: 10px;

                font-size: 12px;
            }

            /*
             * Обе даты абсолютно одинаковые.
             */
            .field input[type="date"] {
                display: block;

                width: 100%;
                max-width: 100%;
                min-width: 0;

                height: 44px;

                box-sizing: border-box;

                padding: 0 6px;

                font-size: 12px;

                line-height: normal;

                overflow: hidden;
            }

            /*
             * Иконка календаря не должна ломать размеры.
             */
            .field input[type="date"]::-webkit-calendar-picker-indicator {
                width: 16px;
                height: 16px;

                margin: 0;
                padding: 0;

                flex-shrink: 0;
            }

            /*
             * Склад.
             */
            .field select {
                width: 100%;
                max-width: 100%;
                min-width: 0;

                padding-left: 8px;
                padding-right: 8px;

                font-size: 12px;
            }


            /* -----------------------------------------
               Filter buttons
            ----------------------------------------- */

            .filter-actions {
                grid-column: 1 / -1;

                display: flex;

                width: 100%;

                gap: 8px;

                flex-direction: row;
            }

            .filter-actions .btn {
                width: 100%;
                min-width: 0;

                flex: 1 1 0;
            }


            /* -----------------------------------------
               Table
            ----------------------------------------- */

            .table-head {
                padding: 15px;
            }
        }


        /* =========================================================
           SMALL PHONE
        ========================================================= */

        @media (max-width: 480px) {

            .page {
                padding: 12px;
            }

            .top {
                margin-bottom: 18px;

                gap: 8px;
            }


            /* -----------------------------------------
               Header
            ----------------------------------------- */

            .back-btn,
            .create-btn {
                width: 40px;
                height: 40px;

                min-height: 40px;

                flex: 0 0 40px;

                padding: 0;

                border-radius: 10px;

                font-size: 0;
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


            /* -----------------------------------------
               Alerts
            ----------------------------------------- */

            .alert {
                padding: 12px;

                font-size: 13px;
            }


            /* -----------------------------------------
               Filter
            ----------------------------------------- */

            .filter-card {
                padding: 13px;

                border-radius: 13px;
            }

            .filters {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr)
                    minmax(0, 1fr);

                gap: 8px;

                width: 100%;
            }

            .filters .field {
                width: 100%;
                min-width: 0;

                grid-column: span 1;
            }

            /*
             * Склад — вся ширина.
             */
            .filters .field:nth-child(3) {
                grid-column: 1 / -1;
            }

            .field {
                width: 100%;
                min-width: 0;
            }

            .field label {
                display: block;

                width: 100%;

                margin-bottom: 6px;

                font-size: 11px;
            }


            /* -----------------------------------------
               All inputs
            ----------------------------------------- */

            .field input,
            .field select {
                display: block;

                width: 100%;
                max-width: 100%;
                min-width: 0;

                height: 44px;

                box-sizing: border-box;

                padding: 0 8px;

                border: 1px solid #dfe3e8;
                border-radius: 10px;

                background: #ffffff;

                font-family: inherit;
                font-size: 12px;

                overflow: hidden;
            }


            /* -----------------------------------------
               Date inputs
            ----------------------------------------- */

            .field input[type="date"] {
                display: block;

                width: 100%;
                max-width: 100%;
                min-width: 0;

                height: 44px;

                box-sizing: border-box;

                padding: 0 6px;

                font-size: 11px;

                line-height: normal;

                overflow: hidden;
            }

            .field input[type="date"]::-webkit-calendar-picker-indicator {
                width: 15px;
                height: 15px;

                margin: 0;
                padding: 0;
            }


            /* -----------------------------------------
               Warehouse select
            ----------------------------------------- */

            .field select {
                width: 100%;
                max-width: 100%;
                min-width: 0;

                height: 44px;

                padding: 0 8px;

                font-size: 12px;
            }


            /* -----------------------------------------
               Buttons
            ----------------------------------------- */

            .filter-actions {
                grid-column: 1 / -1;

                display: flex;

                width: 100%;

                gap: 8px;

                flex-direction: row;
            }

            .filter-actions .btn {
                width: 100%;
                min-width: 0;

                flex: 1 1 0;
            }


            /* -----------------------------------------
               Table
            ----------------------------------------- */

            .table-card {
                border-radius: 13px;
            }

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


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="top">

        <div class="top-left">

            <a
                href="{{ route('home') }}"
                class="back-btn"
                title="Назад"
                aria-label="Назад">
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
            class="create-btn"
            title="Новое оприходование">

            <span class="plus">
                +
            </span>

        </a>

    </div>



    {{-- =========================================================
         ALERTS
    ========================================================== --}}

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

                    <ul style="margin: 5px 0 0 18px; padding: 0;">

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



    {{-- =========================================================
         FILTERS
    ========================================================== --}}

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

                    <input
                        type="date"
                        id="from"
                        name="from"
                        value="{{ $from }}">

                </div>



                {{-- ДО --}}

                <div class="field">

                    <label for="to">
                        До
                    </label>

                    <input
                        type="date"
                        id="to"
                        name="to"
                        value="{{ $to }}">

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
                                (string) request('warehouse_id') === (string) $warehouse->id
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



    {{-- =========================================================
         TABLE
    ========================================================== --}}

    <div class="table-card">


        {{-- TABLE HEADER --}}

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


        {{-- =====================================================
             TABLE
        ====================================================== --}}

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

                                {{ $appropriation->appropriation_date?->format('d.m.Y') }}

                            </span>

                        </td>



                        {{-- Склад --}}

                        <td>

                            <span class="warehouse">

                                {{ $appropriation->warehouse->name ?? '—' }}

                            </span>

                        </td>



                        {{-- Позиций --}}

                        <td>

                            <span class="items-count">

                                {{ $appropriation->items->count() }}

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
                                    href="{{ route('admin.appropriations.show', $appropriation) }}"
                                    class="action"
                                    title="Просмотреть"
                                    aria-label="Просмотреть">

                                    👁

                                </a>



                                {{-- Редактирование --}}

                                <a
                                    href="{{ route('admin.appropriations.edit', $appropriation) }}"
                                    class="action"
                                    title="Редактировать"
                                    aria-label="Редактировать">

                                    ✎

                                </a>



                                {{-- Удаление --}}

                                <form
                                    action="{{ route('admin.appropriations.destroy', $appropriation) }}"
                                    method="POST"
                                    style="margin:0;"
                                    onsubmit="return confirm(
                                        'Удалить оприходование #{{ $appropriation->id }}?\n\n' +
                                        'Если товар из этого оприходования уже использован в продажах или списаниях, удаление будет запрещено.'
                                    );">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="action action-delete"
                                        title="Удалить"
                                        aria-label="Удалить">

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



        {{-- =====================================================
             PAGINATION
        ====================================================== --}}

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
                    )
                    as $page => $url
                )


                    @if($page == $appropriations->currentPage())

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


        {{-- =====================================================
             EMPTY
        ====================================================== --}}

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

</body>

</html>
