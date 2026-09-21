<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    >

    <title>Списания</title>

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
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .top {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .top-left {
            grid-column: 1;
            justify-self: start;
        }

        .title {
            grid-column: 2;
            margin: 0;
            text-align: center;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 700;
        }

        .top-right {
            grid-column: 3;
            justify-self: end;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            padding: 0 15px;
            border-radius: 9px;
            border: 0;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            transition: .15s ease;
        }

        .btn-dark {
            background: #111827;
            color: #fff;
        }

        .btn-dark:hover {
            background: #000;
        }

        .btn-light {
            background: #fff;
            color: #111827;
            border: 1px solid #dfe3e8;
        }

        .btn-light:hover {
            background: #f8f9fa;
        }

        /* =========================================================
           ALERTS
        ========================================================= */

        .success,
        .error {
            padding: 11px 13px;
            margin-bottom: 15px;
            border-radius: 9px;
            font-size: 13px;
        }

        .success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* =========================================================
           CARD
        ========================================================= */

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
        }

        /* =========================================================
           FILTERS
        ========================================================= */

        .filters {
            display: grid;
            grid-template-columns: minmax(180px, 1fr) minmax(0, 1fr) auto;
            gap: 10px;
            align-items: end;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .field label {
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
        }

        input,
        select {
            width: 100%;
            height: 39px;
            padding: 0 10px;
            border: 1px solid #d7dbe0;
            border-radius: 8px;
            background: #fff;
            color: #111827;
            font-size: 13px;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: #111827;
        }

        /* =========================================================
           DATE FIELDS
        ========================================================= */

        .date-fields {
            width: 100%;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 10px;
            min-width: 0;
        }

        .date-fields .field {
            min-width: 0;
        }

        .date-fields input {
            width: 100%;
            min-width: 0;
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
            min-width: 700px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px 11px;
            border-bottom: 1px solid #edf0f2;
            text-align: left;
            vertical-align: middle;
            font-size: 13px;
        }

        th {
            background: #f8f9fa;
            color: #4b5563;
            font-weight: 700;
            white-space: nowrap;
            font-size: 12px;
        }

        tbody tr {
            transition: background .12s ease;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .number {
            width: 65px;
            color: #6b7280;
            white-space: nowrap;
            font-weight: 600;
        }

        .date {
            width: 105px;
            white-space: nowrap;
            color: #4b5563;
        }

        .warehouse {
            font-weight: 600;
            white-space: nowrap;
        }

        .quantity {
            width: 110px;
            white-space: nowrap;
            font-weight: 700;
        }

        .comment {
            max-width: 300px;
            color: #555;
            word-break: break-word;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            padding: 0 9px;
            border-radius: 7px;
            border: 0;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: .15s ease;
        }

        .action-view {
            background: #eef2ff;
            color: #3730a3;
        }

        .action-view:hover {
            background: #e0e7ff;
        }

        .action-delete {
            background: #fee2e2;
            color: #b91c1c;
        }

        .action-delete:hover {
            background: #fecaca;
        }

        /* =========================================================
           EMPTY
        ========================================================= */

        .empty {
            padding: 45px 20px;
            text-align: center;
            color: #777;
            font-size: 13px;
        }

        /* =========================================================
           PAGINATION
        ========================================================= */

        .pagination {
            margin-top: 15px;
        }

        .pagination nav {
            width: 100%;
        }

        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 800px) {

            .page {
                padding: 14px;
            }

            .top {
                grid-template-columns: 40px 1fr 40px;
                gap: 8px;
                margin-bottom: 15px;
            }

            .top-left {
                grid-column: 1;
                justify-self: start;
            }

            .title {
                grid-column: 2;
                font-size: 21px;
            }

            .top-right {
                grid-column: 3;
                justify-self: end;
            }

            /* Стрелка */

            .top-left .btn {
                width: 40px;
                height: 40px;
                padding: 0;
                font-size: 0;
            }

            .top-left .btn::before {
                content: "←";
                font-size: 19px;
                line-height: 1;
            }

            /* Плюс */

            .top-right .btn {
                width: 40px;
                height: 40px;
                padding: 0;
                font-size: 0;
            }

            .top-right .btn::before {
                content: "+";
                font-size: 25px;
                line-height: 1;
                font-weight: 400;
            }

            .card {
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 12px;
            }

            /* Склад отдельно */
            .filters {
                display: grid;
                grid-template-columns: 1fr;
                gap: 9px;
            }

            /*
             * От и По ВСЕГДА
             * в одной строке
             */
            .date-fields {
                width: 100%;
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 8px;
            }

            .date-fields .field {
                min-width: 0;
            }

            .date-fields input {
                width: 100%;
                min-width: 0;
            }

            .filters > .btn {
                width: 100%;
            }

            table {
                min-width: 700px;
            }

            th,
            td {
                padding: 9px 8px;
                font-size: 12px;
            }

            th {
                font-size: 11px;
            }

            .action {
                height: 30px;
                padding: 0 8px;
                font-size: 11px;
            }
        }

        @media (max-width: 500px) {

            .page {
                padding: 10px;
            }

            .top {
                grid-template-columns: 36px 1fr 36px;
                gap: 7px;
            }

            .title {
                font-size: 19px;
            }

            .top-left .btn,
            .top-right .btn {
                width: 36px;
                height: 36px;
            }

            .top-left .btn::before {
                font-size: 18px;
            }

            .top-right .btn::before {
                font-size: 23px;
            }

            .card {
                padding: 10px;
            }

            /*
             * От / По остаются
             * строго в одной строке
             */
            .date-fields {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 7px;
            }

            .date-fields input {
                min-width: 0;
                height: 38px;
                padding-left: 7px;
                padding-right: 7px;
                font-size: 12px;
            }

            .field label {
                font-size: 11px;
            }

            .table-wrap {
                margin-left: -1px;
                width: calc(100% + 2px);
            }

            .empty {
                padding: 35px 15px;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <!-- =========================================================
         HEADER
    ========================================================= -->

    <div class="top">

        <div class="top-left">
            <a
                href="{{ url('/admin') }}"
                class="btn btn-light"
                title="Назад"
            >
                ← Назад
            </a>
        </div>

        <h1 class="title">
            Списания
        </h1>

        <div class="top-right">
            <a
                href="{{ route('admin.writeoffs.create') }}"
                class="btn btn-dark"
                title="Новое списание"
            >
                + Новое списание
            </a>
        </div>

    </div>


    <!-- =========================================================
         SUCCESS
    ========================================================= -->

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif


    <!-- =========================================================
         ERROR
    ========================================================= -->

    @if(session('error'))
        <div class="error">
            {{ session('error') }}
        </div>
    @endif


    @if(session('writeoff_error'))
        <div class="error">
            {{ session('writeoff_error') }}
        </div>
    @endif


    <!-- =========================================================
         FILTERS
    ========================================================= -->

    <div class="card">

        <form
            method="GET"
            action="{{ route('admin.writeoffs.index') }}"
        >

            <div class="filters">

                <!-- СКЛАД -->

                <div class="field">

                    <label for="warehouse_id">
                        Склад
                    </label>

                    <select
                        name="warehouse_id"
                        id="warehouse_id"
                    >

                        <option value="">
                            Все склады
                        </option>

                        @foreach($warehouses as $warehouse)

                            <option
                                value="{{ $warehouse->id }}"
                                @selected(
                                    request('warehouse_id') == $warehouse->id
                                )
                            >
                                {{ $warehouse->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <!-- =================================================
                     ДАТЫ
                ================================================== -->

                <div class="date-fields">

                    <!-- ОТ -->

                    <div class="field">

                        <label for="from">
                            От
                        </label>

                        <input
                            type="date"
                            name="from"
                            id="from"
                            value="{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}"
                        >

                    </div>


                    <!-- ПО -->

                    <div class="field">

                        <label for="to">
                            По
                        </label>

                        <input
                            type="date"
                            name="to"
                            id="to"
                            value="{{ request('to', now()->format('Y-m-d')) }}"
                        >

                    </div>

                </div>


                <!-- ФИЛЬТР -->

                <button
                    type="submit"
                    class="btn btn-dark"
                >
                    Фильтр
                </button>

            </div>

        </form>

    </div>


    <!-- =========================================================
         LIST
    ========================================================= -->

    <div class="card">

        @if($writeOffs->count())

            <div class="table-wrap">

                <table>

                    <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Дата
                        </th>

                        <th>
                            Склад
                        </th>

                        <th>
                            Количество
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

                    @foreach($writeOffs as $writeOff)

                        <tr>

                            <!-- ID -->

                            <td class="number">
                                #{{ $writeOff->id }}
                            </td>


                            <!-- DATE -->

                            <td class="date">

                                {{ $writeOff->writeoff_date?->format('d.m.Y') }}

                            </td>


                            <!-- WAREHOUSE -->

                            <td class="warehouse">

                                {{ $writeOff->warehouse?->name ?? '—' }}

                            </td>


                            <!-- QUANTITY -->

                            <td class="quantity">

                                {{ $writeOff->items->sum('quantity') }} шт.

                            </td>


                            <!-- COMMENT -->

                            <td class="comment">

                                {{ $writeOff->comment ?: '—' }}

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <div class="actions">

                                    <!-- VIEW -->

                                    <a
                                        href="{{ route('admin.writeoffs.show', $writeOff) }}"
                                        class="action action-view"
                                    >
                                        Открыть
                                    </a>


                                    <!-- DELETE -->

                                    <form
                                        action="{{ route('admin.writeoffs.destroy', $writeOff) }}"
                                        method="POST"
                                        onsubmit="return confirm('Удалить это списание? Товар будет возвращён на склад.')"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="action action-delete"
                                        >
                                            Удалить
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>


            <!-- =====================================================
                 PAGINATION
            ====================================================== -->

            <div class="pagination">

                {{ $writeOffs->links() }}

            </div>

        @else

            <div class="empty">

                Списаний за выбранный период нет.

            </div>

        @endif

    </div>

</div>

</body>
</html>
