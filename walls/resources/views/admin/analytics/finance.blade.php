
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Финансы</title>

    <style>
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

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
            letter-spacing: -.4px;
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
            min-width: 0;
        }

        .field label {
            padding-left: 3px;
            font-size: 11px;
            color: #8a919c;
        }

        .field input,
        .field select {
            height: 38px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0 10px;
            background: #fff;
            color: #111827;
            outline: none;
            font-size: 13px;
            font-family: sans-serif;
        }

        .field input:focus,
        .field select:focus {
            border-color: #9ca3af;
        }

        /* DATE FIELDS */

        .filter-box > .field:nth-child(1),
        .filter-box > .field:nth-child(2) {
            width: 145px;
            min-width: 145px;
        }

        .filter-box > .field:nth-child(1) input[type="date"],
        .filter-box > .field:nth-child(2) input[type="date"] {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            height: 38px;
            min-height: 38px;
            padding: 0 8px;
            appearance: none;
            font-family: sans-serif;
            font-size: 13px;
            font-weight: 400;
            line-height: normal;
            text-align: center;
            vertical-align: middle;
        }

        .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit,
        .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit,
        .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit-fields-wrapper,
        .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit-fields-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 0;
            margin: 0;
            line-height: normal;
        }

        .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit-text,
        .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit-month-field,
        .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit-day-field,
        .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit-year-field,
        .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit-text,
        .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit-month-field,
        .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit-day-field,
        .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit-year-field {
            padding: 0;
            margin: 0;
            line-height: normal;
        }

        .filter-box > .field:nth-child(1) input[type="date"]::-webkit-calendar-picker-indicator,
        .filter-box > .field:nth-child(2) input[type="date"]::-webkit-calendar-picker-indicator {
            width: 18px;
            height: 18px;
            margin-left: 4px;
            padding: 0;
            cursor: pointer;
        }

        .point-field {
            min-width: 150px;
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
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .finance-card {
            background: #fff;
            border: 1px solid #eceef1;
            border-radius: 13px;
            padding: 17px;
            min-width: 0;
        }

        .finance-card-title {
            font-size: 12px;
            color: #8a919c;
            margin-bottom: 9px;
        }

        .finance-card-value {
            font-size: 21px;
            font-weight: 700;
            letter-spacing: -.4px;
            overflow-wrap: anywhere;
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
            letter-spacing: -.2px;
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
            min-width: 780px;
            border-collapse: collapse;
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
            white-space: nowrap;
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

        .payment-table tbody tr {
            transition: background .15s ease;
        }

        .payment-table tbody tr:hover {
            background: #fafafa;
        }

        .payment-table tfoot td {
            padding: 14px 9px;
            border-top: 2px solid #e5e7eb;
            border-bottom: 0;
            background: #fafafa;
        }

        .payment-amount {
            font-weight: 650;
            white-space: nowrap;
        }

        .payment-description {
            color: #6b7280;
        }

        .payment-point {
            white-space: nowrap;
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

        /* TABLET */

        @media (max-width: 1200px) {
            .finance-cards {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .filter-box {
                flex-wrap: wrap;
            }

            .point-field {
                min-width: 140px;
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
                max-width: 100%;
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 8px;
            }

            .field {
                min-width: 0;
                width: 100%;
            }

            .field input,
            .field select {
                width: 100%;
                max-width: 100%;
                min-width: 0;
                padding: 0 6px;
                font-size: 13px;
            }

            .filter-box > .field:nth-child(1),
            .filter-box > .field:nth-child(2) {
                width: 100%;
                min-width: 0;
                max-width: 100%;
            }

            .filter-box > .field:nth-child(1) input[type="date"],
            .filter-box > .field:nth-child(2) input[type="date"] {
                width: 100%;
                min-width: 0;
                max-width: 100%;
                height: 38px;
                min-height: 38px;
                padding: 0 6px;
                font-size: 13px;
                line-height: normal;
                text-align: center;
            }

            .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit,
            .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit,
            .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit-fields-wrapper,
            .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit-fields-wrapper {
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                margin: 0;
                line-height: normal;
            }

            .filter-box > .field:nth-child(1) input[type="date"]::-webkit-calendar-picker-indicator,
            .filter-box > .field:nth-child(2) input[type="date"]::-webkit-calendar-picker-indicator {
                width: 17px;
                height: 17px;
                margin-left: 3px;
                padding: 0;
            }

            .point-field {
                grid-column: 1 / -1;
                min-width: 0;
            }

            .filter-box .btn {
                grid-column: 1 / -1;
                width: 100%;
            }

            .finance-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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
            .payment-table {
                min-width: 680px;
            }

            .payment-table th,
            .payment-table td {
                padding: 9px 12px;
                font-size: 12px;
                white-space: nowrap;
            }

            .payment-description {
                max-width: 220px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .filter-box > .field:nth-child(1) input[type="date"],
            .filter-box > .field:nth-child(2) input[type="date"] {
                height: 38px;
                min-height: 38px;
                padding: 0 6px;
                line-height: normal;
                text-align: center;
            }

            .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit,
            .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit,
            .filter-box > .field:nth-child(1) input[type="date"]::-webkit-datetime-edit-fields-wrapper,
            .filter-box > .field:nth-child(2) input[type="date"]::-webkit-datetime-edit-fields-wrapper {
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                margin: 0;
                line-height: normal;
            }
        }
    </style>
</head>

<body>

<div class="finance-page">

    {{-- НАВИГАЦИЯ --}}
    <div class="analytics-navigation">
        <a
            href="{{ route('admin.analytics.menu') }}"
            class="analytics-nav-btn"
        >
            ← В меню аналитики
        </a>
    </div>

    {{-- HEADER --}}
    <div class="finance-header">

        <div class="finance-title">
            <h1>Финансы</h1>

            <p>
                {{ $from->format('d.m.Y') }}
                —
                {{ $to->format('d.m.Y') }}

                @if($selectedPointOfSale)
                    ·
                    {{ $pointsOfSale->firstWhere('id', $selectedPointOfSale)->name ?? '' }}
                @endif
            </p>
        </div>

        {{-- ФИЛЬТР --}}
        <form method="GET" class="filter-box">

            <div class="field">
                <label>С</label>
                <input
                    type="date"
                    name="from"
                    value="{{ $from->format('Y-m-d') }}"
                >
            </div>

            <div class="field">
                <label>По</label>
                <input
                    type="date"
                    name="to"
                    value="{{ $to->format('Y-m-d') }}"
                >
            </div>

            <div class="field point-field">
                <label>Точка продаж</label>

                <select name="point_of_sale_id">
                    <option value="">Все точки</option>

                    @foreach($pointsOfSale as $point)
                        <option
                            value="{{ $point->id }}"
                            @selected($selectedPointOfSale == $point->id)
                        >
                            {{ $point->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
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

        <div class="finance-card">
            <div class="finance-card-title">Выручка</div>
            <div class="finance-card-value blue">
                {{ number_format($revenue, 0, ',', ' ') }} ₸
            </div>
        </div>

        <div class="finance-card">
            <div class="finance-card-title">Себестоимость товара</div>
            <div class="finance-card-value">
                {{ number_format($cost, 0, ',', ' ') }} ₸
            </div>
        </div>

        <div class="finance-card">
            <div class="finance-card-title">Прибыль с продаж</div>
            <div class="finance-card-value {{ $salesProfit >= 0 ? 'green' : 'red' }}">
                {{ number_format($salesProfit, 0, ',', ' ') }} ₸
            </div>
        </div>

        <div class="finance-card">
            <div class="finance-card-title">Остальные расходы</div>
            <div class="finance-card-value red">
                {{ number_format($otherExpenses, 0, ',', ' ') }} ₸
            </div>
        </div>

        <div class="finance-card net-profit">
            <div class="finance-card-title">Чистая прибыль</div>
            <div class="finance-card-value {{ $netProfit >= 0 ? 'green' : 'red' }}">
                {{ number_format($netProfit, 0, ',', ' ') }} ₸
            </div>
        </div>

    </div>

    {{-- СПОСОБЫ ОПЛАТЫ: ПРОДАЖИ, ВОЗВРАТЫ, ИТОГО --}}
    <div class="section">

        <h2>Способы оплаты</h2>

        @php
            $salesPaymentMethodTotals = $salesPaymentMethodTotals ?? [];
            $returnPaymentMethodTotals = $returnPaymentMethodTotals ?? [];

            $allPaymentMethods = array_unique(array_merge(
                array_keys($salesPaymentMethodTotals),
                array_keys($returnPaymentMethodTotals)
            ));

            sort($allPaymentMethods);

            $totalSalesPayments = 0.0;
            $totalReturnPayments = 0.0;
            $totalNetPayments = 0.0;
        @endphp

        @if(count($allPaymentMethods) > 0)

            <div class="table-wrapper">

                <table class="payment-table">

                    <thead>
                        <tr>
                            <th>Способ оплаты</th>
                            <th>Сумма продаж</th>
                            <th>Сумма возвратов</th>
                            <th>Итого</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($allPaymentMethods as $method)

                            @php
                                $salesAmount = (float) (
                                    $salesPaymentMethodTotals[$method] ?? 0
                                );

                                $returnAmount = (float) (
                                    $returnPaymentMethodTotals[$method] ?? 0
                                );

                                $netAmount = $salesAmount - $returnAmount;

                                $totalSalesPayments += $salesAmount;
                                $totalReturnPayments += $returnAmount;
                                $totalNetPayments += $netAmount;
                            @endphp

                            <tr>
                                <td>
                                    {{ $method }}
                                </td>

                                <td class="payment-amount" style="color:#15803d;">
                                    {{ number_format($salesAmount, 0, ',', ' ') }} ₸
                                </td>

                                <td class="payment-amount" style="color:#dc2626;">
                                    {{ number_format($returnAmount, 0, ',', ' ') }} ₸
                                </td>

                                <td
                                    class="payment-amount"
                                    style="color:{{ $netAmount >= 0 ? '#15803d' : '#dc2626' }};"
                                >
                                    {{ number_format($netAmount, 0, ',', ' ') }} ₸
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                    <tfoot>
                        <tr>
                            <td style="font-weight:700;">
                                Общий итог
                            </td>

                            <td class="payment-amount" style="color:#15803d;">
                                {{ number_format($totalSalesPayments, 0, ',', ' ') }} ₸
                            </td>

                            <td class="payment-amount" style="color:#dc2626;">
                                {{ number_format($totalReturnPayments, 0, ',', ' ') }} ₸
                            </td>

                            <td
                                class="payment-amount"
                                style="color:{{ $totalNetPayments >= 0 ? '#15803d' : '#dc2626' }};"
                            >
                                {{ number_format($totalNetPayments, 0, ',', ' ') }} ₸
                            </td>
                        </tr>
                    </tfoot>

                </table>

            </div>

        @else

            <div class="empty">
                За выбранный период оплат и возвратов нет.
            </div>

        @endif

    </div>

    {{-- ИСХОДЯЩИЕ ПЛАТЕЖИ --}}
    <div class="section">

        <h2>Исходящие платежи</h2>

        <div class="table-wrapper">

            <table class="payment-table">

                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Вид расхода</th>
                        <th>Сумма</th>
                        <th>Точка продаж</th>
                        <th>Описание</th>
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

                            <td class="payment-point">
                                {{ $payment->pointOfSale->name ?? 'Общий расход' }}
                            </td>

                            <td class="payment-description">
                                {{ $payment->description ?: '—' }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5">
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
