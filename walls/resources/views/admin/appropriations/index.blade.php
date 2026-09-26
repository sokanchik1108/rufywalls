<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>


    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f4f6f8;
        }

        .page {
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
            text-decoration: none;
            border: 1px solid #e5e8ed;
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
            background: #ffffff;
            border: 1px solid #e5e8ed;
            border-radius: 15px;
            padding: 18px;
            margin-bottom: 18px;
        }

        .filters {
            display: grid;
            grid-template-columns: minmax(180px, 1.6fr) minmax(140px, 1fr) minmax(140px, 1fr) minmax(170px, 1.2fr) auto;
            gap: 12px;
            align-items: end;
        }

        .field {
            min-width: 0;
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            color: #555e69;
            font-size: 12px;
            font-weight: 600;
        }

        .field input,
        .field select {
            width: 100%;
            height: 44px;
            padding: 0 12px;
            border: 1px solid #dfe3e8;
            border-radius: 10px;
            background: #ffffff;
            color: #1a1d21;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: .2s;
        }

        .field input:focus,
        .field select:focus {
            border-color: #7b8795;
            box-shadow: 0 0 0 3px rgba(1, 20, 47, .06);
        }

        .filter-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 15px;
            border-radius: 10px;
            border: 0;
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

        /* =========================
       TABLE
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
       MOBILE (TABLET)
    ========================= */

        @media (max-width: 900px) {
            .filters {
                grid-template-columns: repeat(3, 1fr);
            }

            .filters .field {
                grid-column: span 1;
            }

            .filter-actions {
                grid-column: 1 / -1;
            }
        }

        /* =========================
       MOBILE (PHONE)
    ========================= */

        @media (max-width: 700px) {
            .page {
                padding: 16px;
            }

            .top {
                align-items: center;
                gap: 10px;
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

            /* Плюсик и кнопка "назад" — одинаковый размер и выравнивание */
            .back-btn,
            .create-btn {
                width: 44px;
                height: 44px;
                min-height: 44px;
                flex: 0 0 44px;
                padding: 0;
                font-size: 0;
                border-radius: 11px;
            }

            .back-btn {
                font-size: 21px;
            }

            .create-btn .plus {
                font-size: 22px;
            }

            /* Дата (От/До) и Склад — три равные по ширине колонки */
            .filters {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .filters .field {
                grid-column: span 1;
                min-width: 0;
            }

            .filter-actions {
                grid-column: 1 / -1;
            }

            .filter-actions .btn {
                flex: 1;
            }

            .filter-card {
                padding: 15px;
            }

            .table-head {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .page {
                padding: 12px;
            }

            .top {
                margin-bottom: 18px;
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

            .alert {
                padding: 12px;
                font-size: 13px;
            }

            .filter-card {
                padding: 13px;
                border-radius: 13px;
            }

            .table-card {
                border-radius: 13px;
            }

            /* Инпуты дат и селект склада — одинаковая высота/паддинги/шрифт */
            .field input,
            .field select {
                width: 100%;
                height: 44px;
                min-width: 0;
                padding-left: 8px;
                padding-right: 8px;
                font-size: 12px;
            }

            .field label {
                font-size: 11px;
            }

            /*
             * Три поля (От / До / Склад) — равные по ширине колонки,
             * ровно выстроены и на самых узких экранах.
             */
            .filters {
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
            }

            .filters .field {
                grid-column: span 1;
                width: 100%;
                min-width: 0;
            }

            .filter-actions {
                grid-column: 1 / -1;
                flex-direction: row;
            }

            .filter-actions .btn {
                width: auto;
                flex: 1;
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
                <span class="plus">+</span>
                <span>Новое оприходование</span>
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
                        <ul style="margin: 5px 0 0 18px; padding: 0;">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
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


                    {{-- От --}}
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


                    {{-- До --}}
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


                    {{-- Склад --}}
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
                                (string) request('warehouse_id')===(string) $warehouse->id
                                )
                                >
                                {{ $warehouse->name }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Кнопки --}}
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
                    {{ $appropriations->total() == 1 ? 'документ' : 'документов' }}
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
                                    {{ $appropriation->appropriation_date?->format('d.m.Y') }}
                                </span>
                            </td>


                            {{-- Склад --}}
                            <td>
                                <span class="warehouse">
                                    {{ $appropriation->warehouse->name ?? '—' }}
                                </span>
                            </td>


                            {{-- Количество позиций --}}
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
                                        title="Просмотреть">
                                        👁
                                    </a>


                                    {{-- Редактирование --}}
                                    <a
                                        href="{{ route('admin.appropriations.edit', $appropriation) }}"
                                        class="action"
                                        title="Редактировать">
                                        ✎
                                    </a>


                                    {{-- Удаление --}}
                                    <form
                                        action="{{ route('admin.appropriations.destroy', $appropriation) }}"
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
                        <span>‹</span>
                    </div>

                    @else

                    <a
                        href="{{ $appropriations->previousPageUrl() }}">
                        ‹
                    </a>

                    @endif


                    {{-- Pages --}}
                    @foreach($appropriations->getUrlRange(
                    max(1, $appropriations->currentPage() - 2),
                    min(
                    $appropriations->lastPage(),
                    $appropriations->currentPage() + 2
                    )
                    ) as $page => $url)

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
                        <span>›</span>
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

</body>

</html>