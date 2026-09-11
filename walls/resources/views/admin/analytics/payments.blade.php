<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Платежи</title>
</head>

<body>

<style>

    * {
        box-sizing: border-box;
    }

    .payments-page {
        width: 100%;
        max-width: 100%;
        padding: 24px;
        background: #f6f7f9;
        min-height: 100vh;
        color: #111827;
        font-family: sans-serif;
        overflow-x: hidden;
    }

    /* ========================================
       НАВИГАЦИЯ
    ======================================== */

    .payments-navigation {
        display: block;
        width: 100%;
        margin-bottom: 16px;
        position: relative;
        z-index: 100;
    }

    .payments-menu-back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        height: 36px;
        padding: 0 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #ffffff;
        color: #6b7280;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        cursor: pointer;
        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease;
    }

    .payments-menu-back-btn:hover {
        border-color: #01142f;
        color: #01142f;
        background: #f8fafc;
    }

    /* ========================================
       HEADER
    ======================================== */

    .payments-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
    }

    .payments-title {
        min-width: 0;
    }

    .payments-title h1 {
        margin: 0;
        font-size: 25px;
        font-weight: 700;
        letter-spacing: -.4px;
    }

    .payments-title p {
        margin: 7px 0 0;
        color: #8a919c;
        font-size: 13px;
    }

    /* ========================================
       FILTER
    ======================================== */

    .filter-box {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        background: #fff;
        padding: 8px;
        border: 1px solid #eceef1;
        border-radius: 12px;
        width: auto;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
        box-sizing: border-box;
    }

    .field label {
        padding-left: 3px;
        font-size: 11px;
        color: #8a919c;
    }

    .field input {
        display: block;
        width: 150px;
        max-width: 100%;
        min-width: 0;
        height: 38px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0 10px;
        background: #fff;
        color: #111827;
        outline: none;
        font-size: 13px;
        box-sizing: border-box;
    }

    /* ========================================
       BUTTONS
    ======================================== */

    .btn {
        height: 38px;
        border: 0;
        border-radius: 8px;
        padding: 0 14px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: .15s ease;
        box-sizing: border-box;
    }

    .btn-primary {
        background: #01142f;
        color: #fff;
    }

    .btn-primary:hover {
        background: #02214b;
    }

    .btn-danger {
        background: #fff1f2;
        color: #dc2626;
    }

    .btn-danger:hover {
        background: #fee2e2;
    }

    /* ========================================
       SECTIONS
    ======================================== */

    .section {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        background: #fff;
        border: 1px solid #eceef1;
        border-radius: 13px;
        padding: 18px;
        margin-bottom: 12px;
        box-sizing: border-box;
        overflow: hidden;
    }

    .section h2 {
        margin: 0 0 16px;
        font-size: 16px;
        font-weight: 650;
        letter-spacing: -.2px;
    }

    /* ========================================
       FORMS
    ======================================== */

    .form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 10px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .form-grid > div {
        width: 100%;
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
    }

    .form-full {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        margin-bottom: 5px;
        font-size: 11px;
        color: #8a919c;
    }

    .form-control {
        display: block;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        height: 40px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0 11px;
        background: #fff;
        color: #111827;
        box-sizing: border-box;
        outline: none;
        font-size: 13px;
        font-family: inherit;
    }

    .form-control:focus {
        border-color: #9ca3af;
    }

    select.form-control {
        cursor: pointer;
    }

    textarea.form-control {
        height: 72px;
        padding-top: 10px;
        resize: vertical;
        font-family: inherit;
    }

    /* ========================================
       DATE INPUT
    ======================================== */

    input[type="date"] {
        display: block;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    input[type="date"].form-control {
        height: 40px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        padding: 0 10px;
        box-sizing: border-box;
    }

    /* ========================================
       TABLE
    ======================================== */

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .payment-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 650px;
    }

    .payment-table th {
        text-align: left;
        color: #9aa0a9;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
        padding: 10px 9px;
        border-bottom: 1px solid #eef0f2;
    }

    .payment-table td {
        padding: 11px 9px;
        border-bottom: 1px solid #f2f3f5;
        color: #222;
        font-size: 13px;
        vertical-align: middle;
    }

    .payment-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .payment-amount {
        font-weight: 650;
        white-space: nowrap;
    }

    .delete-form {
        margin: 0;
    }

    .delete-form .btn {
        height: 30px;
        padding: 0 9px;
        font-size: 11px;
    }

    /* ========================================
       EXPENSE TYPES
    ======================================== */

    .expense-type-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 18px;
    }

    .expense-type-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 11px;
        border: 1px solid #f0f1f3;
        border-radius: 8px;
        min-height: 40px;
    }

    .expense-type-left {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .expense-type-name {
        font-size: 13px;
        font-weight: 500;
    }

    .expense-type-count {
        color: #a0a6af;
        font-size: 11px;
    }

    .expense-type-item .btn {
        height: 30px;
        padding: 0 9px;
        font-size: 11px;
    }

    .add-expense-type {
        padding-top: 18px;
        border-top: 1px solid #eef0f2;
    }

    .add-expense-type-title {
        margin-bottom: 9px;
        font-size: 12px;
        font-weight: 600;
        color: #8a919c;
    }

    /* ========================================
       ALERTS
    ======================================== */

    .alert {
        padding: 11px 13px;
        border-radius: 9px;
        margin-bottom: 14px;
        font-size: 13px;
    }

    .alert-success {
        background: #ecfdf3;
        color: #166534;
    }

    .errors {
        background: #fff1f2;
        color: #991b1b;
        padding: 11px 13px;
        border-radius: 9px;
        margin-bottom: 14px;
        font-size: 13px;
    }

    .empty {
        padding: 24px 10px;
        text-align: center;
        color: #a0a6af;
        font-size: 13px;
    }

    /* ========================================
       MOBILE
    ======================================== */

    @media (max-width: 800px) {

        .payments-page {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            padding: 10px;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        /* HEADER */

        .payments-header {
            display: block;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            margin-bottom: 14px;
        }

        .payments-title {
            width: 100%;
            margin-bottom: 13px;
        }

        .payments-title h1 {
            font-size: 22px;
        }

        /* ========================================
           ФИЛЬТР ПЕРИОДА
        ======================================== */

        .filter-box {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            padding: 8px !important;
            margin: 0 !important;
            gap: 8px !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }

        .filter-box .field {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        .filter-box .field label {
            display: block;
            width: 100%;
            margin: 0 0 4px;
            padding: 0;
        }

        /* ВЕРХНИЕ ДАТЫ — 95% */

        .filter-box .field input,
        .filter-box .field input[type="date"] {
            display: block !important;
            width: 95% !important;
            max-width: 95% !important;
            min-width: 0 !important;
            height: 38px !important;
            margin: 0 !important;
            padding: 0 8px !important;
            box-sizing: border-box !important;
            font-size: 13px !important;
        }

        .filter-box .btn {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            height: 38px !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }

        /* ========================================
           SECTIONS
        ======================================== */

        .section {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            padding: 13px;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* ========================================
           ФОРМА ДОБАВЛЕНИЯ ПЛАТЕЖА
        ======================================== */

        .form-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            gap: 10px !important;
            box-sizing: border-box !important;
        }

        .form-grid > div {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        .form-full {
            grid-column: auto !important;
        }

        /* ========================================
           ВСЕ ОСТАЛЬНЫЕ ИНПУТЫ — 100%
        ======================================== */

        .form-control,
        input.form-control,
        input[type="number"].form-control,
        select.form-control {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        /* ========================================
           ТОЛЬКО DATE — 95%
        ======================================== */

        input[type="date"].form-control {
            display: block !important;
            width: 95% !important;
            max-width: 95% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        /* ========================================
           НИЖНЯЯ ДАТА
        ======================================== */

        input[type="date"].form-control {
            height: 40px !important;
            width: 95% !important;
            max-width: 95% !important;
            min-width: 0 !important;
            padding: 0 10px !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            line-height: normal !important;
        }

        input[type="date"].form-control::-webkit-date-and-time-value {
            height: auto;
            min-height: 0;
            padding: 0;
            margin: 0;
        }

        input[type="date"].form-control::-webkit-datetime-edit {
            padding: 0;
            margin: 0;
        }

        input[type="date"].form-control::-webkit-datetime-edit-fields-wrapper {
            padding: 0;
            margin: 0;
        }

        /* ========================================
           TEXTAREA
        ======================================== */

        textarea.form-control {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* ========================================
           КНОПКИ ФОРМЫ
        ======================================== */

        .form-grid .btn {
            display: block;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box;
        }

        /* ========================================
           ТАБЛИЦА
        ======================================== */

        .table-wrapper {
            width: calc(100% + 26px);
            max-width: none;
            margin-left: -13px;
            margin-right: -13px;
            padding-left: 13px;
            padding-right: 13px;
            box-sizing: border-box;
        }
    }

    /* ========================================
       VERY SMALL MOBILE
    ======================================== */

    @media (max-width: 480px) {

        .payments-page {
            padding: 8px;
        }

        .section {
            padding: 12px;
        }

        .payments-title h1 {
            font-size: 21px;
        }

        .payments-menu-back-btn {
            height: 34px;
            padding: 0 12px;
            font-size: 11px;
        }

        .expense-type-name {
            font-size: 12px;
        }
    }

</style>


    <div class="payments-page">


        {{-- ========================================
         НАВИГАЦИЯ
    ======================================== --}}

        <div class="payments-navigation">

            <a
                href="{{ route('admin.analytics.menu') }}"
                class="payments-menu-back-btn">

                ← В меню аналитики

            </a>

        </div>


        {{-- ========================================
         HEADER
    ======================================== --}}

        <div class="payments-header">

            <div class="payments-title">

                <h1>
                    Платежи
                </h1>

                <p>
                    Управление исходящими платежами и видами расходов
                </p>

            </div>


            {{-- ФИЛЬТР ПЕРИОДА --}}

            <form
                method="GET"
                class="filter-box">

                <div class="field">

                    <label>
                        С
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


                <button
                    type="submit"
                    class="btn btn-primary">

                    Применить

                </button>

            </form>

        </div>


        {{-- ========================================
         СООБЩЕНИЯ
    ======================================== --}}

        @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

        @endif


        @if($errors->any())

        <div class="errors">

            @foreach($errors->all() as $error)

            <div>
                {{ $error }}
            </div>

            @endforeach

        </div>

        @endif


        {{-- ========================================
         ДОБАВИТЬ ИСХОДЯЩИЙ ПЛАТЁЖ
    ======================================== --}}

        <div class="section">

            <h2>
                Добавить исходящий платёж
            </h2>


            <form
                method="POST"
                action="{{ route('admin.analytics.finance.outgoing-payments.store') }}">

                @csrf

                <input
                    type="hidden"
                    name="from"
                    value="{{ $from->format('Y-m-d') }}">

                <input
                    type="hidden"
                    name="to"
                    value="{{ $to->format('Y-m-d') }}">


                <div class="form-grid">


                    {{-- ДАТА --}}

                    <div>

                        <label class="form-label">
                            Дата
                        </label>

                        <input
                            type="date"
                            name="payment_date"
                            class="form-control"
                            value="{{ now()->format('Y-m-d') }}"
                            required>

                    </div>


                    {{-- СУММА --}}

                    <div>

                        <label class="form-label">
                            Сумма
                        </label>

                        <input
                            type="number"
                            name="amount"
                            class="form-control"
                            step="0.01"
                            min="0.01"
                            placeholder="150000"
                            required>

                    </div>


                    {{-- ВИД РАСХОДА --}}

                    <div class="form-full">

                        <label class="form-label">
                            Вид расхода
                        </label>

                        <select
                            name="expense_type_id"
                            class="form-control"
                            required>

                            <option value="">
                                Выберите вид расхода
                            </option>

                            @foreach($expenseTypes as $type)

                            <option value="{{ $type->id }}">
                                {{ $type->name }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- ОПИСАНИЕ --}}

                    <div class="form-full">

                        <label class="form-label">
                            Описание
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            placeholder="Например: Аренда магазина за сентябрь"></textarea>

                    </div>


                    {{-- КНОПКА --}}

                    <div class="form-full">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Добавить платёж

                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- ========================================
         ИСХОДЯЩИЕ ПЛАТЕЖИ
    ======================================== --}}

        <div class="section">

            <h2>
                Исходящие платежи
            </h2>


            <div class="table-wrapper">

                <table class="payment-table">

                    <thead>

                        <tr>

                            <th>
                                Дата
                            </th>

                            <th>
                                Вид расхода
                            </th>

                            <th>
                                Сумма
                            </th>

                            <th>
                                Описание
                            </th>

                            <th></th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($outgoingPayments as $payment)

                        <tr>

                            <td>
                                {{ $payment->payment_date->format('d.m.Y') }}
                            </td>

                            <td>
                                {{ $payment->expenseType->name }}
                            </td>

                            <td class="payment-amount">

                                {{ number_format(
                                    $payment->amount,
                                    0,
                                    ',',
                                    ' '
                                ) }}

                                ₸

                            </td>

                            <td>
                                {{ $payment->description ?: '—' }}
                            </td>

                            <td>

                                <form
                                    method="POST"
                                    class="delete-form"
                                    action="{{ route(
                                        'admin.analytics.finance.outgoing-payments.destroy',
                                        $payment
                                    ) }}">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Удалить этот платёж?')">

                                        Удалить

                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="5">

                                <div class="empty">
                                    За выбранный период платежей нет.
                                </div>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ========================================
         ВИДЫ РАСХОДОВ
    ======================================== --}}

        <div class="section">

            <h2>
                Виды расходов
            </h2>


            <div class="expense-type-list">

                @forelse($expenseTypes as $type)

                <div class="expense-type-item">

                    <div class="expense-type-left">

                        <span class="expense-type-name">
                            {{ $type->name }}
                        </span>

                        <span class="expense-type-count">

                            {{ $type->outgoing_payments_count }}

                            платежей

                        </span>

                    </div>


                    @if($type->outgoing_payments_count == 0)

                    <form
                        method="POST"
                        action="{{ route(
                                'admin.analytics.finance.expense-types.destroy',
                                $type
                            ) }}">

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                            onclick="return confirm('Удалить этот вид расхода?')">

                            Удалить

                        </button>

                    </form>

                    @endif

                </div>

                @empty

                <div class="empty">
                    Виды расходов ещё не добавлены.
                </div>

                @endforelse

            </div>


            {{-- ДОБАВИТЬ ВИД РАСХОДА --}}

            <div class="add-expense-type">

                <div class="add-expense-type-title">
                    Добавить новый вид расхода
                </div>


                <form
                    method="POST"
                    action="{{ route('admin.analytics.finance.expense-types.store') }}">

                    @csrf


                    <div class="form-grid">

                        <div class="form-full">

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                placeholder="Например: Аренда"
                                required>

                        </div>


                        <div class="form-full">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                Добавить вид расхода

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</body>

</html>