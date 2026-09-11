<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <style>
        .finance-page {
            padding: 24px;
            background: #f6f7f9;
            min-height: 100vh;
            color: #111827;
            font-family: sans-serif;
        }

        /* NAVIGATION */

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

        /* HEADER */

        .finance-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .finance-title h1 {
            margin: 0;
            font-size: 25px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -0.4px;
        }

        .finance-title p {
            margin: 7px 0 0;
            color: #8a919c;
            font-size: 13px;
        }

        /* FILTER */

        .filter-box {
            display: flex;
            align-items: flex-end;
            gap: 8px;

            background: #fff;
            padding: 8px;

            border: 1px solid #eceef1;
            border-radius: 12px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .field label {
            padding-left: 3px;
            font-size: 11px;
            color: #8a919c;
        }

        .field input {
            height: 38px;

            border: 1px solid #e5e7eb;
            border-radius: 8px;

            padding: 0 10px;

            background: #fff;
            color: #111827;

            outline: none;
            font-size: 13px;
        }

        .field input:focus {
            border-color: #9ca3af;
        }

        /* BUTTON */

        .btn {
            height: 38px;

            border: 0;
            border-radius: 8px;

            padding: 0 14px;

            cursor: pointer;

            font-size: 13px;
            font-weight: 600;

            transition: .15s ease;
        }

        .btn-primary {
            background: #01142f;
            color: #fff;
        }

        .btn-primary:hover {
            background: #02214b;
        }

        /* FINANCIAL CARDS */

        .finance-cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;

            margin-bottom: 12px;
        }

        .finance-card {
            background: #fff;

            border: 1px solid #eceef1;
            border-radius: 13px;

            padding: 17px;
        }

        .finance-card-title {
            font-size: 12px;
            color: #8a919c;

            margin-bottom: 9px;
        }

        .finance-card-value {
            font-size: 21px;
            font-weight: 700;

            letter-spacing: -0.4px;

            white-space: nowrap;
        }

        .finance-card-value.blue {
            color: #1d4ed8;
        }

        .finance-card-value.green {
            color: #15803d;
        }

        .finance-card-value.red {
            color: #dc2626;
        }

        .finance-card.net-profit {
            border-color: #dfe3e8;
        }

        /* SECTION */

        .section {
            background: #fff;

            border: 1px solid #eceef1;
            border-radius: 13px;

            padding: 18px;

            margin-bottom: 12px;
        }

        .section h2 {
            margin: 0 0 16px;

            font-size: 16px;
            font-weight: 650;

            letter-spacing: -0.2px;
        }

        /* TABLE */

        .table-wrapper {
            overflow-x: auto;

            -webkit-overflow-scrolling: touch;

            margin: 0 -18px;
            padding: 0 18px;
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

        .payment-description {
            color: #6b7280;
        }

        /* TOTAL */

        .payments-total {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;

            margin-top: 15px;
            padding-top: 14px;

            border-top: 1px solid #eef0f2;
        }

        .payments-total-label {
            color: #8a919c;

            font-size: 12px;
        }

        .payments-total-value {
            font-size: 16px;
            font-weight: 700;

            color: #dc2626;

            white-space: nowrap;
        }

        /* EMPTY */

        .empty {
            padding: 28px 10px;

            text-align: center;

            color: #a0a6af;

            font-size: 13px;
        }

        /* ALERTS */

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

        .alert-error {
            background: #fff1f2;
            color: #991b1b;
        }

        /* TABLE ROW HOVER */

        .payment-table tbody tr {
            transition: background .15s ease;
        }

        .payment-table tbody tr:hover {
            background: #fafafa;
        }

        /* TABLET */

        @media (max-width: 1100px) {

            .finance-cards {
                grid-template-columns: repeat(3, 1fr);
            }

            .finance-card:last-child {
                grid-column: span 1;
            }
        }

        /* MOBILE */

        @media (max-width: 800px) {

            .finance-page {
                padding: 14px;
            }

            .analytics-navigation {
                margin-bottom: 14px;
            }

            .analytics-nav-btn {
                height: 34px;
                font-size: 12px;
            }

            .finance-header {
                display: block;

                margin-bottom: 16px;
            }

            .finance-title {
                margin-bottom: 13px;
            }

            .finance-title h1 {
                font-size: 22px;
            }

            .filter-box {
                width: 100%;

                box-sizing: border-box;

                display: grid;

                grid-template-columns: 1fr 1fr;

                gap: 8px;
            }

            .filter-box .btn {
                grid-column: 1 / -1;

                width: 100%;
            }

            .field input {
                width: 100%;

                box-sizing: border-box;
            }

            .finance-cards {
                grid-template-columns: 1fr 1fr;

                gap: 8px;
            }

            .finance-card {
                padding: 14px;

                border-radius: 11px;
            }

            .finance-card:last-child {
                grid-column: 1 / -1;
            }

            .finance-card-title {
                font-size: 11px;

                margin-bottom: 7px;
            }

            .finance-card-value {
                font-size: 18px;
            }

            .section {
                padding: 15px;

                border-radius: 11px;
            }

            .section h2 {
                font-size: 15px;

                margin-bottom: 14px;
            }

            .table-wrapper {
                margin: 0 -15px;

                padding: 0 15px;
            }

            .payments-total {
                align-items: flex-start;
            }
        }

        /* SMALL MOBILE */

        @media (max-width: 480px) {

            .finance-page {
                padding: 10px;
            }

            .finance-title h1 {
                font-size: 20px;
            }

            .finance-cards {
                gap: 7px;
            }

            .finance-card {
                padding: 12px;
            }

            .finance-card-value {
                font-size: 16px;
            }

            .finance-card-title {
                font-size: 10px;
            }

            .section {
                padding: 13px;
            }

            .payment-table th {
                font-size: 9px;
            }

            .payment-table td {
                font-size: 12px;
            }

            .payments-total-value {
                font-size: 15px;
            }
        }
    </style>


    <div class="finance-page">

        {{-- НАВИГАЦИЯ --}}

        <div class="analytics-navigation">

            <a
                href="{{ route('admin.analytics.menu') }}"
                class="analytics-nav-btn">
                ← В меню аналитики
            </a>

        </div>


        {{-- HEADER --}}

        <div class="finance-header">

            <div class="finance-title">

                <h1>
                    Финансы
                </h1>

                <p>
                    {{ $from->format('d.m.Y') }}
                    —
                    {{ $to->format('d.m.Y') }}
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


        {{-- СООБЩЕНИЯ --}}

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


        {{-- ФИНАНСОВЫЕ ПОКАЗАТЕЛИ --}}

        <div class="finance-cards">


            {{-- ВЫРУЧКА --}}

            <div class="finance-card">

                <div class="finance-card-title">
                    Выручка
                </div>

                <div class="finance-card-value blue">
                    {{ number_format($revenue, 0, ',', ' ') }} ₸
                </div>

            </div>


            {{-- СЕБЕСТОИМОСТЬ --}}

            <div class="finance-card">

                <div class="finance-card-title">
                    Себестоимость товара
                </div>

                <div class="finance-card-value">
                    {{ number_format($cost, 0, ',', ' ') }} ₸
                </div>

            </div>


            {{-- ПРИБЫЛЬ С ПРОДАЖ --}}

            <div class="finance-card">

                <div class="finance-card-title">
                    Прибыль с продаж
                </div>

                <div class="finance-card-value {{ $salesProfit >= 0 ? 'green' : 'red' }}">
                    {{ number_format($salesProfit, 0, ',', ' ') }} ₸
                </div>

            </div>


            {{-- ОСТАЛЬНЫЕ РАСХОДЫ --}}

            <div class="finance-card">

                <div class="finance-card-title">
                    Остальные расходы
                </div>

                <div class="finance-card-value red">
                    {{ number_format($otherExpenses, 0, ',', ' ') }} ₸
                </div>

            </div>


            {{-- ЧИСТАЯ ПРИБЫЛЬ --}}

            <div class="finance-card net-profit">

                <div class="finance-card-title">
                    Чистая прибыль
                </div>

                <div class="finance-card-value {{ $netProfit >= 0 ? 'green' : 'red' }}">
                    {{ number_format($netProfit, 0, ',', ' ') }} ₸
                </div>

            </div>

        </div>


        {{-- ИСХОДЯЩИЕ ПЛАТЕЖИ --}}

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

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($outgoingPayments as $payment)

                        <tr>

                            <td>
                                {{ $payment->payment_date->format('d.m.Y') }}
                            </td>


                            <td>
                                {{ $payment->expenseType->name ?? '—' }}
                            </td>


                            <td class="payment-amount">
                                {{ number_format($payment->amount, 0, ',', ' ') }} ₸
                            </td>


                            <td class="payment-description">
                                {{ $payment->description ?: '—' }}
                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="4">

                                <div class="empty">
                                    За выбранный период исходящих платежей нет.
                                </div>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ИТОГО ПЛАТЕЖЕЙ --}}

            <div class="payments-total">

                <div class="payments-total-label">
                    Всего исходящих платежей
                </div>

                <div class="payments-total-value">
                    {{ number_format($otherExpenses, 0, ',', ' ') }} ₸
                </div>

            </div>

        </div>

    </div>

</body>

</html>