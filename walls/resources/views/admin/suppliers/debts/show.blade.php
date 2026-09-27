<!DOCTYPE html>

<html lang="ru">
<head>
    <meta charset="UTF-8">


<meta
    name="viewport"
    content="width=device-width, initial-scale=1,
    maximum-scale=1, user-scalable=no"
>

<title>{{ $supplier->name }} — Долги</title>

<style>
    * {
        box-sizing: border-box;
    }

    :root {
        --navy: #01142f;
        --navy2: #02214b;
        --text: #172033;
        --muted: #7b8494;
        --border: #e7eaf0;
        --bg: #f6f7f9;
        --green: #087f5b;
        --red: #c62828;
    }

    html {
        -webkit-text-size-adjust: 100%;
    }

    body {
        margin: 0;
        background: var(--bg);
        color: var(--text);
        font-family:
            Inter,
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            Arial,
            sans-serif;
    }

    button,
    input,
    textarea {
        font: inherit;
    }

    a {
        -webkit-tap-highlight-color: transparent;
    }

    .supplier-debt-page {
        width: min(1180px, calc(100% - 28px));
        margin: 0 auto;
        padding: 18px 0 35px;
    }

    /* HEADER */

    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 16px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        color: #687386;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .back-link:hover {
        color: var(--navy);
    }

    .page-header h1 {
        margin: 0;
        color: var(--navy);
        font-size: 23px;
        line-height: 1.2;
        font-weight: 750;
        letter-spacing: -.3px;
    }

    .supplier-phone {
        margin-top: 3px;
        color: var(--muted);
        font-size: 11px;
    }

    .header-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    /* BUTTONS */

    .btn {
        min-height: 36px;
        border: 0;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        transition: .15s ease;
    }

    .btn-dark {
        background: var(--navy);
        color: #fff;
    }

    .btn-dark:hover {
        background: var(--navy2);
    }

    .btn-blue {
        background: var(--navy2);
        color: #fff;
    }

    .btn-blue:hover {
        background: var(--navy);
    }

    .btn-light {
        background: #eef1f5;
        color: #394354;
    }

    .btn-light:hover {
        background: #e4e8ee;
    }

    /* ALERTS */

    .alert {
        padding: 9px 12px;
        border-radius: 8px;
        margin-bottom: 12px;
        font-size: 12px;
        line-height: 1.4;
    }

    .alert ul {
        margin: 0;
        padding-left: 17px;
    }

    .alert-success {
        background: #e9f8f0;
        color: #146c3a;
    }

    .alert-error {
        background: #fdeeee;
        color: #a32121;
    }

    /* STATS */

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-bottom: 10px;
    }

    .stat-card {
        min-width: 0;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 11px;
        padding: 13px 14px;
    }

    .debt-card {
        background: var(--navy);
        border-color: var(--navy);
        color: #fff;
    }

    .qr-card {
        background: #f0faf6;
        border-color: #d6eee4;
    }

    .stat-label {
        color: var(--muted);
        font-size: 10px;
        line-height: 1.25;
        margin-bottom: 5px;
    }

    .debt-card .stat-label {
        color: rgba(255,255,255,.62);
    }

    .stat-value {
        color: var(--navy);
        font-size: 18px;
        line-height: 1.15;
        font-weight: 800;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .debt-card .stat-value {
        color: #fff;
    }

    .qr-card .stat-value {
        color: var(--green);
    }

    /* BALANCE */

    .balance-box {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 11px;
        padding: 12px 15px;
        margin-bottom: 12px;
    }

    .balance-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 6px 0;
        border-bottom: 1px solid #f0f1f3;
        font-size: 12px;
    }

    .balance-row span {
        color: var(--muted);
    }

    .balance-row strong {
        color: var(--navy);
        white-space: nowrap;
    }

    .balance-row .minus {
        color: var(--green);
    }

    .balance-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding-top: 9px;
        margin-top: 3px;
        font-size: 13px;
        font-weight: 750;
    }

    .balance-total strong {
        color: var(--navy);
        font-size: 17px;
        white-space: nowrap;
    }

    /* SECTIONS */

    .section-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 11px;
        margin-bottom: 12px;
        overflow: hidden;
    }

    .section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 42px;
        padding: 0 14px;
        border-bottom: 1px solid #edf0f3;
    }

    .section-title h2 {
        margin: 0;
        color: var(--navy);
        font-size: 14px;
        font-weight: 750;
    }

    /* TABLE */

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 8px 11px;
        border-bottom: 1px solid #edf0f2;
        text-align: left;
        vertical-align: middle;
        font-size: 11px;
    }

    th {
        background: #fafbfc;
        color: #7a8290;
        font-size: 10px;
        font-weight: 650;
        white-space: nowrap;
    }

    tr:last-child td {
        border-bottom: 0;
    }

    .positive {
        color: var(--green);
        font-weight: 650;
    }

    .debt-text {
        color: var(--red);
        font-weight: 650;
    }

    .paid-text {
        color: var(--green);
        font-weight: 650;
    }

    .amount-plus {
        color: var(--red);
        font-weight: 650;
        white-space: nowrap;
    }

    .amount-minus {
        color: var(--green);
        font-weight: 650;
        white-space: nowrap;
    }

    .type-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 7px;
        border-radius: 5px;
        background: #eef1f5;
        color: #596273;
        font-size: 10px;
        white-space: nowrap;
    }

    .type-opening {
        background: #fff2d8;
        color: #946200;
    }

    .type-payment {
        background: #e8f7ee;
        color: #146c3a;
    }

    .type-adjustment {
        background: #e9eef8;
        color: #214d86;
    }

    .comment-cell {
        max-width: 260px;
        color: #707887;
    }

    .actions-cell {
        white-space: nowrap;
        text-align: right;
    }

    .view-btn {
        color: var(--navy2);
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
    }

    .delete-btn {
        border: 0;
        padding: 0;
        background: transparent;
        color: var(--red);
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
    }

    .empty {
        padding: 22px 15px;
        text-align: center;
        color: #8a919d;
        font-size: 12px;
    }

    /* MODALS */

    .modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 15px;
    }

    .modal.active {
        display: flex;
    }

    .modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(1,20,47,.48);
        backdrop-filter: blur(2px);
    }

    .modal-content {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 430px;
        max-height: calc(100vh - 30px);
        overflow-y: auto;
        background: #fff;
        border-radius: 13px;
        box-shadow: 0 20px 60px rgba(0,0,0,.18);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 15px;
        border-bottom: 1px solid #edf0f3;
    }

    .modal-header h3 {
        margin: 0;
        color: var(--navy);
        font-size: 15px;
    }

    .modal-close {
        width: 30px;
        height: 30px;
        border: 0;
        border-radius: 7px;
        background: #f1f3f6;
        color: #697386;
        font-size: 21px;
        line-height: 1;
        cursor: pointer;
    }

    .modal-content form {
        padding: 15px;
    }

    .modal-info,
    .adjustment-help {
        padding: 9px 11px;
        border-radius: 7px;
        background: #f5f7f9;
        color: #697386;
        margin-bottom: 13px;
        font-size: 11px;
        line-height: 1.4;
    }

    .modal-info strong {
        color: var(--navy);
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #465064;
        font-size: 11px;
        font-weight: 650;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        border: 1px solid #dce0e6;
        border-radius: 8px;
        padding: 9px 10px;
        background: #fff;
        color: var(--text);
        font-size: 13px;
        outline: none;
    }

    .form-group input {
        height: 38px;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #466180;
    }

    .form-group textarea {
        min-height: 72px;
        resize: vertical;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 6px;
        padding-top: 2px;
    }

    /* TABLET */

    @media (max-width: 1050px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 900px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .page-header {
            flex-direction: column;
        }

        .header-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }

    /* MOBILE */

    @media (max-width: 600px) {

        .supplier-debt-page {
            width: calc(100% - 16px);
            padding: 11px 0 25px;
        }

        .page-header {
            gap: 10px;
            margin-bottom: 12px;
        }

        .page-header h1 {
            font-size: 20px;
        }

        .supplier-phone {
            font-size: 10px;
        }

        .header-actions {
            width: 100%;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 6px;
        }

        .header-actions .btn {
            width: 100%;
            min-width: 0;
            min-height: 36px;
            padding: 0 6px;
            font-size: 10px;
        }

        .stats-grid {
            gap: 6px;
            margin-bottom: 8px;
        }

        .stat-card {
            padding: 10px;
            border-radius: 9px;
        }

        .stat-label {
            font-size: 9px;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 15px;
        }

        .balance-box {
            padding: 10px 12px;
            border-radius: 9px;
            margin-bottom: 9px;
        }

        .balance-row {
            padding: 5px 0;
            font-size: 11px;
        }

        .balance-total {
            padding-top: 8px;
            font-size: 12px;
        }

        .balance-total strong {
            font-size: 16px;
        }

        .section-card {
            border-radius: 9px;
            margin-bottom: 9px;
        }

        .section-title {
            min-height: 39px;
            padding: 0 11px;
        }

        .section-title h2 {
            font-size: 13px;
        }

        th,
        td {
            padding: 7px 9px;
            font-size: 10px;
        }

        th {
            font-size: 9px;
        }

        .type-badge {
            font-size: 9px;
            padding: 3px 6px;
        }

        .modal {
            padding: 8px;
        }

        .modal-content {
            max-height: calc(100vh - 16px);
            border-radius: 11px;
        }

        .modal-header {
            padding: 12px;
        }

        .modal-content form {
            padding: 12px;
        }

        .modal-actions .btn {
            min-height: 37px;
            flex: 1;
        }
    }

    @media (max-width: 380px) {

        .supplier-debt-page {
            width: calc(100% - 12px);
        }

        .page-header h1 {
            font-size: 18px;
        }

        .header-actions {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 5px;
        }

        .header-actions .btn {
            font-size: 9px;
            padding: 0 4px;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }

        .stat-value {
            font-size: 14px;
        }

        .balance-row {
            font-size: 10px;
        }

        .balance-total strong {
            font-size: 15px;
        }
    }
</style>

</head>

<body>

<div class="supplier-debt-page">


{{-- HEADER --}}
<div class="page-header">

    <div>
        <a
            href="{{ route('admin.supplier-debts.index') }}"
            class="back-link"
        >
            ← Все поставщики
        </a>

        <h1>{{ $supplier->name }}</h1>

        @if($supplier->phone)
            <div class="supplier-phone">
                {{ $supplier->phone }}
            </div>
        @endif
    </div>

    <div class="header-actions">

        <button
            type="button"
            class="btn btn-dark"
            onclick="openModal('paymentModal')"
        >
            Погасить долг
        </button>

        <button
            type="button"
            class="btn btn-light"
            onclick="openModal('adjustmentModal')"
        >
            Корректировка
        </button>

    </div>

</div>


{{-- MESSAGES --}}
@if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

@endif


@if($errors->any())

    <div class="alert alert-error">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

@endif


{{-- STATS --}}
<div class="stats-grid">

    <div class="stat-card debt-card">
        <div class="stat-label">
            Текущий долг
        </div>

        <div class="stat-value">
            {{ number_format(
                max(0, $debt),
                0,
                ',',
                ' '
            ) }} ₸
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-label">
            Всего приёмок
        </div>

        <div class="stat-value">
            {{ number_format(
                $receiptsTotal,
                0,
                ',',
                ' '
            ) }} ₸
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-label">
            Оплачено при приёмках
        </div>

        <div class="stat-value">
            {{ number_format(
                $receiptPaymentsTotal,
                0,
                ',',
                ' '
            ) }} ₸
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-label">
            Ручные погашения
        </div>

        <div class="stat-value">
            {{ number_format(
                $manualPaymentsTotal,
                0,
                ',',
                ' '
            ) }} ₸
        </div>
    </div>


    {{-- QR ЛЕЗГОВКО --}}
    <div class="stat-card qr-card">
        <div class="stat-label">
            Qr Лезговко
        </div>

        <div class="stat-value">
            {{ number_format(
                $qrLegzovkoPayments ?? 0,
                0,
                ',',
                ' '
            ) }} ₸
        </div>
    </div>

</div>


{{-- BALANCE --}}
<div class="balance-box">

    <div class="balance-row">
        <span>Начальный долг и корректировки</span>

        <strong>
            {{ number_format(
                $openingAndAdjustments,
                0,
                ',',
                ' '
            ) }} ₸
        </strong>
    </div>


    <div class="balance-row">
        <span>Долг по приёмкам</span>

        <strong>
            {{ number_format(
                $receiptsTotal,
                0,
                ',',
                ' '
            ) }} ₸
        </strong>
    </div>


    <div class="balance-row">
        <span>Оплаты при приёмках</span>

        <strong class="minus">
            − {{ number_format(
                $receiptPaymentsTotal,
                0,
                ',',
                ' '
            ) }} ₸
        </strong>
    </div>


    <div class="balance-row">
        <span>Ручные погашения</span>

        <strong class="minus">
            − {{ number_format(
                $manualPaymentsTotal,
                0,
                ',',
                ' '
            ) }} ₸
        </strong>
    </div>


    {{-- QR ЛЕЗГОВКО --}}
    <div class="balance-row">
        <span>QR Лезговко</span>

        <strong class="minus">
            − {{ number_format(
                $qrLegzovkoPayments ?? 0,
                0,
                ',',
                ' '
            ) }} ₸
        </strong>
    </div>


    <div class="balance-total">
        <span>Итого долг</span>

        <strong>
            {{ number_format(
                max(0, $debt),
                0,
                ',',
                ' '
            ) }} ₸
        </strong>
    </div>

</div>


{{-- TRANSACTIONS --}}
<div class="section-card">

    <div class="section-title">
        <h2>Операции по долгу</h2>
    </div>

    @if($supplier->transactions->count())

        @php
            /*
             * Новые операции сверху.
             * Сначала сортировка по дате операции,
             * при одинаковой дате — по времени создания.
             */
            $transactions = $supplier->transactions
                ->sort(function ($a, $b) {

                    $dateA = $a->transaction_date
                        ? $a->transaction_date->format('Y-m-d')
                        : '';

                    $dateB = $b->transaction_date
                        ? $b->transaction_date->format('Y-m-d')
                        : '';

                    if ($dateA !== $dateB) {
                        return strcmp($dateB, $dateA);
                    }

                    $createdA = $a->created_at
                        ? $a->created_at->timestamp
                        : 0;

                    $createdB = $b->created_at
                        ? $b->created_at->timestamp
                        : 0;

                    return $createdB <=> $createdA;
                });
        @endphp

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Тип</th>
                        <th>Сумма</th>
                        <th>Комментарий</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($transactions as $transaction)

                        @php
                            $isPayment =
                                $transaction->type === 'payment';

                            $isOpening =
                                $transaction->type === 'opening_debt';

                            $isAdjustment =
                                $transaction->type === 'adjustment';

                            $amount =
                                (float) $transaction->amount;

                            if ($isPayment) {
                                $displayAmount = -abs($amount);
                            } else {
                                $displayAmount = $amount;
                            }
                        @endphp

                        <tr>

                            <td>
                                {{ optional(
                                    $transaction->transaction_date
                                )->format('d.m.Y') }}
                            </td>

                            <td>

                                @if($isOpening)

                                    <span class="type-badge type-opening">
                                        Начальный долг
                                    </span>

                                @elseif($isPayment)

                                    <span class="type-badge type-payment">
                                        Погашение
                                    </span>

                                @elseif($isAdjustment)

                                    <span class="type-badge type-adjustment">
                                        Корректировка
                                    </span>

                                @else

                                    <span class="type-badge">
                                        {{ $transaction->type }}
                                    </span>

                                @endif

                            </td>

                            <td>

                                <span class="{{ $displayAmount < 0 ? 'amount-minus' : 'amount-plus' }}">
                                    {{ $displayAmount > 0 ? '+' : '' }}
                                    {{ number_format(
                                        $displayAmount,
                                        0,
                                        ',',
                                        ' '
                                    ) }} ₸
                                </span>

                            </td>

                            <td class="comment-cell">
                                {{ $transaction->comment ?: '—' }}
                            </td>

                            <td class="actions-cell">

                                <form
                                    action="{{ route(
                                        'admin.supplier-debts.transaction.destroy',
                                        $transaction
                                    ) }}"
                                    method="POST"
                                    onsubmit="return confirm('Удалить эту операцию?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="delete-btn"
                                    >
                                        Удалить
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="empty">
            Операций по долгу пока нет.
        </div>

    @endif

</div>

</div>

{{-- ========================================================= --}}
{{-- ПОГАШЕНИЕ ДОЛГА --}}
{{-- ========================================================= --}}

<div class="modal" id="paymentModal">


<div
    class="modal-overlay"
    onclick="closeModal('paymentModal')"
></div>

<div class="modal-content">

    <div class="modal-header">

        <h3>Погашение долга</h3>

        <button
            type="button"
            class="modal-close"
            onclick="closeModal('paymentModal')"
        >
            ×
        </button>

    </div>

    <form
        action="{{ route('admin.supplier-debts.payment') }}"
        method="POST"
    >

        @csrf

        <input
            type="hidden"
            name="supplier_id"
            value="{{ $supplier->id }}"
        >

        <div class="modal-info">
            Текущий долг:

            <strong>
                {{ number_format(
                    max(0, $debt),
                    0,
                    ',',
                    ' '
                ) }} ₸
            </strong>
        </div>

        <div class="form-group">

            <label for="payment_amount">
                Сумма погашения
            </label>

            <input
                type="number"
                name="amount"
                id="payment_amount"
                min="0.01"
                step="0.01"
                max="{{ max(0, $debt) }}"
                required
                placeholder="0"
            >

        </div>

        <div class="form-group">

            <label for="payment_date">
                Дата
            </label>

            <input
                type="date"
                name="transaction_date"
                id="payment_date"
                value="{{ now()->format('Y-m-d') }}"
                required
            >

        </div>

        <div class="form-group">

            <label for="payment_comment">
                Комментарий
            </label>

            <textarea
                name="comment"
                id="payment_comment"
                rows="3"
                placeholder="Например: оплата поставщику"
            ></textarea>

        </div>

        <div class="modal-actions">

            <button
                type="button"
                class="btn btn-light"
                onclick="closeModal('paymentModal')"
            >
                Отмена
            </button>

            <button
                type="submit"
                class="btn btn-dark"
            >
                Погасить
            </button>

        </div>

    </form>

</div>
```

</div>

{{-- ========================================================= --}}
{{-- НАЧАЛЬНЫЙ ДОЛГ --}}
{{-- ========================================================= --}}

<div class="modal" id="openingDebtModal">

```
<div
    class="modal-overlay"
    onclick="closeModal('openingDebtModal')"
></div>

<div class="modal-content">

    <div class="modal-header">

        <h3>Добавить начальный долг</h3>

        <button
            type="button"
            class="modal-close"
            onclick="closeModal('openingDebtModal')"
        >
            ×
        </button>

    </div>

    <form
        action="{{ route('admin.supplier-debts.opening-debt') }}"
        method="POST"
    >

        @csrf

        <input
            type="hidden"
            name="supplier_id"
            value="{{ $supplier->id }}"
        >

        <div class="form-group">

            <label for="opening_amount">
                Сумма
            </label>

            <input
                type="number"
                name="amount"
                id="opening_amount"
                min="0.01"
                step="0.01"
                required
                placeholder="200000"
            >

        </div>

        <div class="form-group">

            <label for="opening_date">
                Дата
            </label>

            <input
                type="date"
                name="transaction_date"
                id="opening_date"
                value="{{ now()->format('Y-m-d') }}"
                required
            >

        </div>

        <div class="form-group">

            <label for="opening_comment">
                Комментарий
            </label>

            <textarea
                name="comment"
                id="opening_comment"
                rows="3"
                placeholder="Например: долг до начала работы в системе"
            ></textarea>

        </div>

        <div class="modal-actions">

            <button
                type="button"
                class="btn btn-light"
                onclick="closeModal('openingDebtModal')"
            >
                Отмена
            </button>

            <button
                type="submit"
                class="btn btn-blue"
            >
                Добавить долг
            </button>

        </div>

    </form>

</div>


</div>

{{-- ========================================================= --}}
{{-- КОРРЕКТИРОВКА --}}
{{-- ========================================================= --}}

<div class="modal" id="adjustmentModal">


<div
    class="modal-overlay"
    onclick="closeModal('adjustmentModal')"
></div>

<div class="modal-content">

    <div class="modal-header">

        <h3>Корректировка долга</h3>

        <button
            type="button"
            class="modal-close"
            onclick="closeModal('adjustmentModal')"
        >
            ×
        </button>

    </div>

    <form
        action="{{ route('admin.supplier-debts.adjustment') }}"
        method="POST"
    >

        @csrf

        <input
            type="hidden"
            name="supplier_id"
            value="{{ $supplier->id }}"
        >

        <div class="adjustment-help">
            Положительная сумма увеличит долг.
            Отрицательная — уменьшит.
        </div>

        <div class="form-group">

            <label for="adjustment_amount">
                Сумма
            </label>

            <input
                type="number"
                name="amount"
                id="adjustment_amount"
                step="0.01"
                required
                placeholder="Например: 10000 или -10000"
            >

        </div>

        <div class="form-group">

            <label for="adjustment_date">
                Дата
            </label>

            <input
                type="date"
                name="transaction_date"
                id="adjustment_date"
                value="{{ now()->format('Y-m-d') }}"
                required
            >

        </div>

        <div class="form-group">

            <label for="adjustment_comment">
                Причина
            </label>

            <textarea
                name="comment"
                id="adjustment_comment"
                rows="3"
                required
                placeholder="Укажите причину корректировки"
            ></textarea>

        </div>

        <div class="modal-actions">

            <button
                type="button"
                class="btn btn-light"
                onclick="closeModal('adjustmentModal')"
            >
                Отмена
            </button>

            <button
                type="submit"
                class="btn btn-blue"
            >
                Сохранить
            </button>

        </div>

    </form>

</div>


</div>

<script>

    function openModal(id) {
        const modal = document.getElementById(id);

        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);

        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(event) {

        if (event.key !== 'Escape') {
            return;
        }

        document
            .querySelectorAll('.modal.active')
            .forEach(function(modal) {
                modal.classList.remove('active');
            });

        document.body.style.overflow = '';

    });

</script>

</body>
</html>
