<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Точки продаж</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7fa;
            color: #01142f;
            font-family: Arial, sans-serif;
        }

        .page {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* =========================
           HEADER
        ========================= */

        .header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .header-title {
            flex: 1;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
        }

        .header-btn {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e2e7ee;

            color: #01142f;
            text-decoration: none;

            transition: 0.2s ease;
        }

        .header-btn:hover {
            background: #eef2f7;
            transform: translateY(-1px);
        }

        .header-btn svg {
            width: 21px;
            height: 21px;

            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* =========================
           ALERTS
        ========================= */

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #eaf8ef;
            color: #176b36;
            border: 1px solid #c9ecd5;
        }

        .alert-error {
            background: #fff0f0;
            color: #a12626;
            border: 1px solid #f1cccc;
        }

        /* =========================
           ADD FORM
        ========================= */

        .form-card {
            background: #ffffff;
            border: 1px solid #e2e7ee;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-title {
            margin-bottom: 6px;
            font-size: 18px;
            font-weight: 700;
        }

        .form-description {
            margin-bottom: 18px;
            color: #7a8798;
            font-size: 13px;
        }

        .form-row {
            display: flex;
            align-items: flex-end;
            gap: 12px;
        }

        .form-group {
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;

            font-size: 13px;
            font-weight: 600;
            color: #26364d;
        }

        .form-input {
            width: 100%;
            height: 46px;

            padding: 0 14px;

            border: 1px solid #d9e0e8;
            border-radius: 10px;

            background: #ffffff;
            color: #01142f;

            font-size: 14px;
            outline: none;

            transition: 0.2s ease;
        }

        .form-input::placeholder {
            color: #9aa5b4;
        }

        .form-input:focus {
            border-color: #01142f;
            box-shadow: 0 0 0 3px rgba(1, 20, 47, 0.08);
        }

        .form-button {
            height: 46px;
            padding: 0 20px;

            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            border: 1px solid #01142f;
            border-radius: 10px;

            background: #01142f;
            color: #ffffff;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition: 0.2s ease;
        }

        .form-button:hover {
            background: #02214b;
        }

        .form-button svg {
            width: 17px;
            height: 17px;

            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .field-error {
            margin-top: 7px;
            color: #b42318;
            font-size: 12px;
        }

        /* =========================
           TABLE CARD
        ========================= */

        .card {
            background: #ffffff;
            border: 1px solid #e2e7ee;
            border-radius: 16px;
            overflow: hidden;
        }

        /* =========================
           TABLE
        ========================= */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8fafc;
        }

        th {
            padding: 16px 18px;

            text-align: left;

            font-size: 13px;
            font-weight: 700;
            color: #657286;

            border-bottom: 1px solid #e8edf3;

            white-space: nowrap;
        }

        td {
            padding: 16px 18px;

            font-size: 14px;

            border-bottom: 1px solid #edf0f4;

            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr {
            transition: background 0.15s ease;
        }

        tbody tr:hover {
            background: #fafbfd;
        }

        .id-cell {
            width: 80px;
            color: #788598;
            font-weight: 600;
        }

        .name-cell {
            font-weight: 600;
            color: #01142f;
        }

        .date-cell {
            color: #68768a;
            white-space: nowrap;
        }

        /* =========================
           DELETE
        ========================= */

        .actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .action-btn {
            width: 38px;
            height: 38px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 10px;
            border: 1px solid #f0d3d0;

            background: #ffffff;
            color: #b42318;

            cursor: pointer;

            transition: 0.2s ease;
        }

        .action-btn:hover {
            background: #fff1f0;
        }

        .action-btn svg {
            width: 18px;
            height: 18px;

            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .delete-form {
            margin: 0;
        }

        /* =========================
           EMPTY
        ========================= */

        .empty {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-icon {
            width: 64px;
            height: 64px;

            margin: 0 auto 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 18px;

            background: #f1f4f8;
            color: #68768a;
        }

        .empty-icon svg {
            width: 30px;
            height: 30px;

            fill: none;
            stroke: currentColor;
            stroke-width: 1.7;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .empty-title {
            margin-bottom: 8px;

            font-size: 18px;
            font-weight: 700;
        }

        .empty-text {
            color: #7a8798;
            font-size: 14px;
        }

        /* =========================
           MOBILE
        ========================= */

        .mobile-list {
            display: none;
        }

        .mobile-card {
            padding: 16px;
            border-bottom: 1px solid #edf0f4;
        }

        .mobile-card:last-child {
            border-bottom: none;
        }

        .mobile-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;
            margin-bottom: 10px;
        }

        .mobile-id {
            color: #7b8797;
            font-size: 13px;
            font-weight: 600;
        }

        .mobile-date {
            color: #7b8797;
            font-size: 12px;
        }

        .mobile-name {
            margin-bottom: 14px;

            font-size: 16px;
            font-weight: 700;

            color: #01142f;
        }

        .mobile-actions {
            display: flex;
            justify-content: flex-end;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 700px) {

            .page {
                padding: 12px;
            }

            .header {
                margin-bottom: 16px;
            }

            .header-title {
                font-size: 20px;
            }

            .header-btn {
                width: 42px;
                height: 42px;
                flex-basis: 42px;
            }

            .form-card {
                padding: 16px;
                border-radius: 14px;
            }

            .form-row {
                flex-direction: column;
                align-items: stretch;
            }

            .form-button {
                width: 100%;
            }

            .desktop-table {
                display: none;
            }

            .mobile-list {
                display: block;
            }

            .card {
                border-radius: 14px;
            }
        }

    </style>

</head>

<body>

<div class="page">

    {{-- =========================
         HEADER
    ========================== --}}

    <div class="header">

        <a
            href="{{ url('/admin') }}"
            class="header-btn"
            title="Назад"
        >
            <svg viewBox="0 0 24 24">
                <path d="M19 12H5"></path>
                <path d="M12 19l-7-7 7-7"></path>
            </svg>
        </a>

        <div class="header-title">
            Точки продаж
        </div>

        <div
            style="
                width: 44px;
                height: 44px;
                flex: 0 0 44px;
            "
        ></div>

    </div>


    {{-- =========================
         SUCCESS
    ========================== --}}

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- =========================
         ERROR
    ========================== --}}

    @if(session('error'))

        <div class="alert alert-error">
            {{ session('error') }}
        </div>

    @endif


    {{-- =========================
         VALIDATION ERRORS
    ========================== --}}

    @if($errors->any())

        <div class="alert alert-error">

            <strong>
                Не удалось выполнить операцию:
            </strong>

            <ul style="margin: 8px 0 0 18px; padding: 0;">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================
         ADD FORM
    ========================== --}}

    <div class="form-card">

        <div class="form-title">
            Добавить точку продаж
        </div>

        <div class="form-description">
            Укажите название новой точки продаж.
        </div>

        <form
            method="POST"
            action="{{ route('admin.points-of-sale.store') }}"
        >

            @csrf

            <div class="form-row">

                <div class="form-group">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Название точки продаж
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        value="{{ old('name') }}"
                        placeholder="Например: Бутик 105"
                        maxlength="255"
                        autocomplete="off"
                        required
                    >

                    @error('name')

                        <div class="field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <button
                    type="submit"
                    class="form-button"
                >

                    <svg viewBox="0 0 24 24">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>

                    Добавить

                </button>

            </div>

        </form>

    </div>


    {{-- =========================
         POINTS LIST
    ========================== --}}

    <div class="card">

        @if($pointsOfSale->count())

            {{-- =========================
                 DESKTOP
            ========================== --}}

            <div class="table-wrapper desktop-table">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Название
                            </th>

                            <th>
                                Дата создания
                            </th>

                            <th style="text-align: right;">
                                Действия
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($pointsOfSale as $pointOfSale)

                            <tr>

                                <td class="id-cell">
                                    #{{ $pointOfSale->id }}
                                </td>

                                <td class="name-cell">
                                    {{ $pointOfSale->name }}
                                </td>

                                <td class="date-cell">

                                    @if($pointOfSale->created_at)

                                        {{ \Carbon\Carbon::parse($pointOfSale->created_at)->format('d.m.Y H:i') }}

                                    @else

                                        —

                                    @endif

                                </td>

                                <td>

                                    <div class="actions">

                                        <form
                                            method="POST"
                                            action="{{ route('admin.points-of-sale.destroy', $pointOfSale->id) }}"
                                            class="delete-form"
                                            onsubmit="return confirm(@js(
                                                'Удалить точку продаж «' .
                                                $pointOfSale->name .
                                                '»?'
                                            ));"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="action-btn"
                                                title="Удалить"
                                            >

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

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- =========================
                 MOBILE
            ========================== --}}

            <div class="mobile-list">

                @foreach($pointsOfSale as $pointOfSale)

                    <div class="mobile-card">

                        <div class="mobile-card-top">

                            <div class="mobile-id">
                                #{{ $pointOfSale->id }}
                            </div>

                            <div class="mobile-date">

                                @if($pointOfSale->created_at)

                                    {{ \Carbon\Carbon::parse($pointOfSale->created_at)->format('d.m.Y H:i') }}

                                @else

                                    —

                                @endif

                            </div>

                        </div>


                        <div class="mobile-name">
                            {{ $pointOfSale->name }}
                        </div>


                        <div class="mobile-actions">

                            <form
                                method="POST"
                                action="{{ route('admin.points-of-sale.destroy', $pointOfSale->id) }}"
                                class="delete-form"
                                onsubmit="return confirm(@js(
                                    'Удалить точку продаж «' .
                                    $pointOfSale->name .
                                    '»?'
                                ));"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="action-btn"
                                    title="Удалить"
                                >

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

                    </div>

                @endforeach

            </div>

        @else

            {{-- =========================
                 EMPTY
            ========================== --}}

            <div class="empty">

                <div class="empty-icon">

                    <svg viewBox="0 0 24 24">

                        <path d="M3 21h18"></path>

                        <path d="M5 21V5l7-3 7 3v16"></path>

                        <path d="M9 9h1"></path>

                        <path d="M14 9h1"></path>

                        <path d="M9 13h1"></path>

                        <path d="M14 13h1"></path>

                        <path d="M10 21v-4h4v4"></path>

                    </svg>

                </div>

                <div class="empty-title">
                    Точек продаж пока нет
                </div>

                <div class="empty-text">
                    Добавьте первую точку продаж с помощью формы выше.
                </div>

            </div>

        @endif

    </div>

</div>

</body>

</html>

