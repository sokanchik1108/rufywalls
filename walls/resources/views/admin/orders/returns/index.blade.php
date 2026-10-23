@extends('layouts.app')

@section('title', 'Все возвраты')

@section('content')
<div class="returns-page">

    {{-- Заголовок --}}
    <div class="returns-header">
        <div>
            <div class="returns-breadcrumb">
                <a href="{{ route('admin.orders.seller') }}">Продажи</a>
                <span>/</span>
                <span>Возвраты</span>
            </div>

            <h1>Все возвраты</h1>
            <p>История возвратов товаров и денежных средств</p>
        </div>

        <a href="{{ route('admin.orders.seller') }}" class="returns-back">
            <span class="back-arrow">←</span>
            К продажам
        </a>
    </div>

    {{-- Статистика --}}
    <div class="returns-stats">

        <div class="returns-stat-card">
            <div class="stat-icon stat-icon-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 7h18M5 7V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v2"/>
                    <path d="M5 7v13h14V7"/>
                    <path d="M9 12h6"/>
                </svg>
            </div>

            <div class="stat-content">
                <div class="stat-label">Всего возвратов</div>
                <div class="stat-value">
                    {{ number_format($totalReturns ?? 0, 0, '.', ' ') }}
                </div>
                <div class="stat-description">Количество оформленных возвратов</div>
            </div>
        </div>

        <div class="returns-stat-card">
            <div class="stat-icon stat-icon-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                    <path d="M3 10h18"/>
                    <path d="M7 15h3"/>
                </svg>
            </div>

            <div class="stat-content">
                <div class="stat-label">Сумма возвратов</div>
                <div class="stat-value stat-value-money">
                    {{ number_format($totalRefund ?? 0, 0, '.', ' ') }}
                    <span>₸</span>
                </div>
                <div class="stat-description">Общая сумма возвратов денег</div>
            </div>
        </div>

        <div class="returns-stat-card">
            <div class="stat-icon stat-icon-purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19V5"/>
                    <path d="M4 19h16"/>
                    <path d="m7 15 4-4 3 2 5-6"/>
                    <path d="M15 7h4v4"/>
                </svg>
            </div>

            <div class="stat-content">
                <div class="stat-label">На этой странице</div>
                <div class="stat-value">
                    {{ $returns->count() }}
                </div>
                <div class="stat-description">
                    Из {{ $returns->total() }} записей
                </div>
            </div>
        </div>

    </div>

    {{-- Фильтры --}}
    <div class="returns-panel">
        <div class="panel-heading">
            <div>
                <h2>Поиск и фильтры</h2>
                <p>Найдите возврат по номеру или периоду</p>
            </div>

            <div class="filter-heading-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 5h16l-6 7v6l-4 2v-8z"/>
                </svg>
            </div>
        </div>

        <form action="{{ route('admin.returns.index') }}"
              method="GET"
              class="returns-filter-form">

            <div class="filter-field filter-search">
                <label for="return-search">Поиск</label>

                <div class="input-with-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m16 16 4 4"/>
                    </svg>

                    <input
                        type="text"
                        id="return-search"
                        name="search"
                        value="{{ $search ?? request('search') }}"
                        placeholder="Номер возврата или заказа..."
                    >
                </div>
            </div>

            <div class="filter-field">
                <label for="date-from">Дата с</label>

                <input
                    type="date"
                    id="date-from"
                    name="date_from"
                    value="{{ $dateFrom ?? request('date_from') }}"
                >
            </div>

            <div class="filter-field">
                <label for="date-to">Дата по</label>

                <input
                    type="date"
                    id="date-to"
                    name="date_to"
                    value="{{ $dateTo ?? request('date_to') }}"
                >
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m16 16 4 4"/>
                    </svg>
                    Найти
                </button>

                <a href="{{ route('admin.returns.index') }}" class="btn-reset">
                    Сбросить
                </a>
            </div>

        </form>
    </div>

    {{-- Таблица возвратов --}}
    <div class="returns-panel returns-table-panel">

        <div class="panel-heading table-panel-heading">
            <div>
                <h2>История возвратов</h2>
                <p>
                    @if($returns->total() > 0)
                        Показаны записи
                        {{ $returns->firstItem() }}–{{ $returns->lastItem() }}
                        из {{ $returns->total() }}
                    @else
                        Возвраты не найдены
                    @endif
                </p>
            </div>

            <div class="table-total-badge">
                {{ $returns->total() }} записей
            </div>
        </div>

        <div class="returns-table-wrapper">
            <table class="returns-table">
                <thead>
                    <tr>
                        <th class="col-number">Возврат</th>
                        <th>Заказ</th>
                        <th>Дата</th>
                        <th>Товары</th>
                        <th>Сумма возврата</th>
                        <th>Способ возврата</th>
                        <th class="col-action">Действия</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($returns as $return)
                        @php
                            $returnDate = $return->created_at;

                            $items = $return->items ?? collect();

                            $refundAmount = ($return->payments ?? collect())
                                ->sum('amount');

                            $productNames = $items->map(function ($item) {
                                return $item->variant?->product?->name
                                    ?? $item->orderItem?->variant?->product?->name
                                    ?? $item->product?->name
                                    ?? 'Товар';
                            })->unique()->values();

                            $paymentNames = ($return->payments ?? collect())
                                ->map(function ($payment) {
                                    return $payment->paymentMethod?->name
                                        ?? $payment->paymentMethod?->title
                                        ?? null;
                                })
                                ->filter()
                                ->unique()
                                ->values();
                        @endphp

                        <tr>
                            {{-- Номер возврата --}}
                            <td>
                                <div class="return-number">
                                    <span class="return-number-icon">
                                        <svg viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="1.8"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 7h16v13H4z"/>
                                            <path d="M8 7V5a4 4 0 0 1 8 0v2"/>
                                            <path d="m9 13 2 2 4-4"/>
                                        </svg>
                                    </span>

                                    <div>
                                        <strong>#{{ $return->id }}</strong>
                                        <span class="return-subtitle">Возврат</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Заказ --}}
                            <td>
                                @if($return->order)
                                    <div class="order-info">
                                        <span class="order-number">
                                            Заказ #{{ $return->order->id }}
                                        </span>

                                        @if($return->order->pointOfSale)
                                            <span class="order-point">
                                                {{ $return->order->pointOfSale->name }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="muted-text">Заказ не указан</span>
                                @endif
                            </td>

                            {{-- Дата --}}
                            <td>
                                <div class="date-info">
                                    <span class="date-main">
                                        {{ $returnDate?->format('d.m.Y') ?? '—' }}
                                    </span>

                                    <span class="date-time">
                                        {{ $returnDate?->format('H:i') ?? '' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Товары --}}
                            <td>
                                <div class="products-info">
                                    <span class="products-count">
                                        {{ $items->count() }}
                                        {{ $items->count() == 1 ? 'позиция' : 'позиций' }}
                                    </span>

                                    <div class="products-names">
                                        {{ $productNames->take(2)->implode(', ') ?: 'Нет данных' }}

                                        @if($productNames->count() > 2)
                                            <span class="products-more">
                                                +{{ $productNames->count() - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Сумма --}}
                            <td>
                                <div class="refund-amount">
                                    {{ number_format($refundAmount, 0, '.', ' ') }}
                                    <span>₸</span>
                                </div>
                            </td>

                            {{-- Способ возврата --}}
                            <td>
                                @if($paymentNames->isNotEmpty())
                                    <div class="payment-methods">
                                        @foreach($paymentNames as $paymentName)
                                            <span class="payment-tag">
                                                {{ $paymentName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="muted-text">Не указан</span>
                                @endif
                            </td>

                            {{-- Действия --}}
                            <td>
                                <a
                                    href="{{ route('admin.orders.returns.show', $return->id) }}"
                                    class="btn-details"
                                    title="Посмотреть возврат"
                                >
                                    <svg viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="1.8"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    Подробнее
                                </a>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="returns-empty">
                                    <div class="empty-icon">
                                        <svg viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="1.6"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="7"/>
                                            <path d="m16 16 4 4"/>
                                            <path d="M8 11h6"/>
                                        </svg>
                                    </div>

                                    <h3>Возвраты не найдены</h3>

                                    <p>
                                        Попробуйте изменить параметры поиска
                                        или сбросить фильтры.
                                    </p>

                                    <a href="{{ route('admin.returns.index') }}"
                                       class="btn-reset-empty">
                                        Сбросить фильтры
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Компактная пагинация --}}
        @if($returns->hasPages())
            <div class="returns-pagination">
                <div class="pagination-description">
                    Страница <strong>{{ $returns->currentPage() }}</strong>
                    из <strong>{{ $returns->lastPage() }}</strong>
                </div>

                <div class="pagination-controls">
                    {{ $returns->onEachSide(1)->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

    </div>

</div>
@endsection


<style>
    .returns-page {
        --returns-primary: #17365d;
        --returns-primary-hover: #234b7b;
        --returns-text: #172033;
        --returns-muted: #758195;
        --returns-border: #e6eaf0;
        --returns-bg: #f5f7fb;

        width: 100%;
        max-width: 100%;
        min-width: 0;
        color: var(--returns-text);
        padding-bottom: 30px;
        box-sizing: border-box;
    }

    .returns-page *,
    .returns-page *::before,
    .returns-page *::after {
        box-sizing: border-box;
    }

    /* Заголовок */

    .returns-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 25px;
    }

    .returns-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        align-items: center;
        color: #8a95a6;
        font-size: 12px;
        margin-bottom: 10px;
    }

    .returns-breadcrumb a {
        color: var(--returns-primary);
        text-decoration: none;
    }

    .returns-breadcrumb a:hover {
        text-decoration: underline;
    }

    .returns-header h1 {
        margin: 0;
        font-size: 28px;
        line-height: 1.3;
        font-weight: 750;
        letter-spacing: -.7px;
        color: #13233b;
    }

    .returns-header p {
        margin: 7px 0 0;
        color: var(--returns-muted);
        font-size: 13px;
    }

    .returns-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 0 15px;
        border: 1px solid var(--returns-border);
        border-radius: 9px;
        background: #fff;
        color: #34445d;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: .2s;
    }

    .returns-back:hover {
        background: #f5f8fc;
        border-color: #cbd5e1;
        color: var(--returns-primary);
    }

    .back-arrow {
        font-size: 18px;
        line-height: 1;
    }

    /* Статистика */

    .returns-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 22px;
    }

    .returns-stat-card {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        min-width: 0;
        padding: 21px;
        border: 1px solid var(--returns-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(21, 39, 67, .025);
    }

    .stat-icon {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 12px;
    }

    .stat-icon svg {
        width: 23px;
        height: 23px;
    }

    .stat-icon-blue {
        color: #315d9b;
        background: #eaf1ff;
    }

    .stat-icon-green {
        color: #16805a;
        background: #e6f7ef;
    }

    .stat-icon-purple {
        color: #7451b5;
        background: #f1eaff;
    }

    .stat-content {
        min-width: 0;
    }

    .stat-label {
        margin-bottom: 7px;
        color: #657187;
        font-size: 12px;
        font-weight: 600;
    }

    .stat-value {
        overflow-wrap: anywhere;
        color: #172b48;
        font-size: 26px;
        line-height: 1.2;
        font-weight: 750;
        letter-spacing: -.5px;
    }

    .stat-value-money {
        font-size: 24px;
    }

    .stat-value-money span {
        color: #657187;
        font-size: 15px;
        font-weight: 600;
    }

    .stat-description {
        margin-top: 7px;
        color: #97a0af;
        font-size: 11px;
        line-height: 1.5;
    }

    /* Общие панели */

    .returns-panel {
        min-width: 0;
        margin-bottom: 22px;
        overflow: hidden;
        border: 1px solid var(--returns-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(21, 39, 67, .025);
    }

    .panel-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 21px 23px;
        border-bottom: 1px solid #edf0f5;
    }

    .panel-heading h2 {
        margin: 0;
        color: #1c2b43;
        font-size: 16px;
        line-height: 1.4;
        font-weight: 700;
    }

    .panel-heading p {
        margin: 5px 0 0;
        color: #8791a2;
        font-size: 12px;
    }

    .filter-heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        border-radius: 10px;
        background: #f0f4fa;
        color: var(--returns-primary);
    }

    .filter-heading-icon svg {
        width: 19px;
        height: 19px;
    }

    /* Фильтры */

    .returns-filter-form {
        display: grid;
        grid-template-columns: minmax(200px, 1.6fr) minmax(140px, 1fr)
                               minmax(140px, 1fr) auto;
        align-items: end;
        gap: 15px;
        padding: 22px 23px 24px;
    }

    .filter-field {
        display: flex;
        flex-direction: column;
        min-width: 0;
        gap: 8px;
    }

    .filter-field label {
        color: #42516a;
        font-size: 12px;
        font-weight: 650;
    }

    .filter-field input {
        display: block;
        width: 100%;
        min-width: 0;
        height: 42px;
        padding: 0 12px;
        border: 1px solid #dfe5ee;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #24344d;
        font-family: inherit;
        font-size: 13px;
        transition: border-color .2s, box-shadow .2s;
    }

    .filter-field input:focus {
        border-color: #6c8bb8;
        box-shadow: 0 0 0 3px rgba(49, 93, 155, .09);
    }

    .filter-field input::placeholder {
        color: #a4adbb;
    }

    .input-with-icon {
        position: relative;
    }

    .input-with-icon svg {
        position: absolute;
        z-index: 1;
        left: 12px;
        top: 50%;
        width: 17px;
        height: 17px;
        color: #98a3b3;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .input-with-icon input {
        padding-left: 38px;
    }

    .filter-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filter,
    .btn-reset {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        height: 42px;
        padding: 0 15px;
        border-radius: 8px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 650;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: .2s;
    }

    .btn-filter {
        border: 1px solid var(--returns-primary);
        background: var(--returns-primary);
        color: #fff;
    }

    .btn-filter:hover {
        border-color: var(--returns-primary-hover);
        background: var(--returns-primary-hover);
        color: #fff;
    }

    .btn-filter svg {
        width: 16px;
        height: 16px;
    }

    .btn-reset {
        border: 1px solid #dfe5ee;
        background: #fff;
        color: #59677d;
    }

    .btn-reset:hover {
        background: #f5f7fb;
        color: #253c5c;
    }

    /* Таблица */

    .returns-table-panel {
        margin-bottom: 0;
    }

    .table-panel-heading {
        padding: 20px 23px;
    }

    .table-total-badge {
        flex-shrink: 0;
        padding: 7px 11px;
        border: 1px solid #e2e9f4;
        border-radius: 7px;
        background: #f3f6fb;
        color: #405b80;
        font-size: 11px;
        font-weight: 650;
    }

    .returns-table-wrapper {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        overscroll-behavior-x: contain;
    }

    .returns-table {
        width: 100%;
        min-width: 970px;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
    }

    .returns-table thead th {
        padding: 14px 16px;
        border-bottom: 1px solid #e8edf4;
        background: #f8fafd;
        color: #718096;
        font-size: 10px;
        font-weight: 750;
        letter-spacing: .45px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .returns-table tbody td {
        padding: 16px;
        border-bottom: 1px solid #edf0f5;
        vertical-align: middle;
        background: #fff;
    }

    .returns-table tbody tr:last-child td {
        border-bottom: none;
    }

    .returns-table tbody tr {
        transition: background .15s;
    }

    .returns-table tbody tr:hover td {
        background: #fbfcff;
    }

    .col-number {
        width: 130px;
    }

    .col-action {
        width: 140px;
        text-align: center !important;
    }

    .return-number {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .return-number-icon {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 9px;
        background: #edf3fc;
        color: #345b91;
    }

    .return-number-icon svg {
        width: 18px;
        height: 18px;
    }

    .return-number strong {
        display: block;
        color: #243b5d;
        font-size: 13px;
        font-weight: 750;
    }

    .return-subtitle {
        display: block;
        margin-top: 3px;
        color: #9aa4b3;
        font-size: 10px;
    }

    .order-info,
    .date-info,
    .products-info {
        min-width: 0;
    }

    .order-number {
        display: block;
        color: #354b6a;
        font-size: 12px;
        font-weight: 650;
        white-space: nowrap;
    }

    .order-point {
        display: block;
        max-width: 150px;
        margin-top: 5px;
        overflow: hidden;
        color: #929cad;
        font-size: 10px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .date-main {
        display: block;
        color: #34445b;
        font-size: 12px;
        font-weight: 650;
        white-space: nowrap;
    }

    .date-time {
        display: block;
        margin-top: 4px;
        color: #929cad;
        font-size: 11px;
    }

    .products-count {
        display: inline-block;
        margin-bottom: 5px;
        padding: 4px 7px;
        border-radius: 5px;
        background: #f0f4fa;
        color: #4e6482;
        font-size: 10px;
        font-weight: 650;
        white-space: nowrap;
    }

    .products-names {
        max-width: 190px;
        overflow-wrap: anywhere;
        color: #6e7b90;
        font-size: 11px;
        line-height: 1.5;
    }

    .products-more {
        display: inline-block;
        margin-left: 3px;
        color: #315d9b;
        font-weight: 700;
        white-space: nowrap;
    }

    .refund-amount {
        color: #16805a;
        font-size: 13px;
        font-weight: 750;
        white-space: nowrap;
    }

    .refund-amount span {
        font-size: 11px;
        font-weight: 600;
    }

    .payment-methods {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        max-width: 140px;
    }

    .payment-tag {
        display: inline-block;
        padding: 5px 8px;
        border: 1px solid #e5eaf1;
        border-radius: 6px;
        background: #f8fafd;
        color: #53647c;
        font-size: 10px;
        line-height: 1.3;
    }

    .muted-text {
        color: #9aa4b3;
        font-size: 11px;
    }

    .btn-details {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 34px;
        padding: 0 10px;
        border: 1px solid #dce5f2;
        border-radius: 7px;
        background: #fff;
        color: #315782;
        font-size: 11px;
        font-weight: 650;
        text-decoration: none;
        white-space: nowrap;
        transition: .2s;
    }

    .btn-details:hover {
        border-color: #c3d5ef;
        background: #edf4ff;
        color: #17365d;
    }

    .btn-details svg {
        width: 15px;
        height: 15px;
    }

    /* Пустое состояние */

    .returns-empty {
        display: flex;
        align-items: center;
        flex-direction: column;
        padding: 55px 20px;
        text-align: center;
    }

    .empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        margin-bottom: 17px;
        border-radius: 18px;
        background: #f0f4fa;
        color: #7386a1;
    }

    .empty-icon svg {
        width: 30px;
        height: 30px;
    }

    .returns-empty h3 {
        margin: 0;
        color: #273851;
        font-size: 16px;
        font-weight: 700;
    }

    .returns-empty p {
        max-width: 340px;
        margin: 9px 0 18px;
        color: #8b96a7;
        font-size: 12px;
        line-height: 1.6;
    }

    .btn-reset-empty {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0 13px;
        border: 1px solid #dfe5ee;
        border-radius: 8px;
        color: #405675;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-reset-empty:hover {
        background: #f5f7fb;
        color: #17365d;
    }

    /* Пагинация — компактная, без растягивания на всю страницу */

    .returns-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px 20px;
        padding: 15px 22px;
        border-top: 1px solid #edf0f5;
        background: #fff;
    }

    .pagination-description {
        color: #8490a1;
        font-size: 11px;
    }

    .pagination-description strong {
        color: #354760;
        font-weight: 700;
    }

    .pagination-controls {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex: 0 1 auto;
        min-width: 0;
        max-width: 100%;
    }

    .returns-page .pagination-controls nav {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        max-width: 100%;
        min-width: 0;
    }

    .returns-page .pagination-controls nav > div:first-child {
        display: none !important;
    }

    .returns-page .pagination-controls nav > div:last-child {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0;
        min-width: 0;
    }

    .returns-page .pagination-controls nav > div:last-child > div:first-child {
        display: none !important;
    }

    .returns-page .pagination {
        display: flex !important;
        flex-wrap: wrap;
        justify-content: flex-end;
        align-items: center;
        gap: 5px;
        width: auto !important;
        max-width: 100%;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none;
    }

    .returns-page .pagination .page-item {
        display: inline-flex;
        margin: 0 !important;
    }

    .returns-page .pagination .page-link {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-width: 34px;
        width: auto;
        height: 34px;
        padding: 0 9px;
        border: 1px solid #e1e7f0;
        border-radius: 7px !important;
        background: #fff;
        color: #43546e;
        font-size: 12px;
        line-height: 1;
        text-decoration: none;
        box-shadow: none;
        transition: .2s;
    }

    .returns-page .pagination .page-link:hover {
        z-index: 1;
        border-color: #b9cce7;
        background: #f0f5fc;
        color: #17365d;
    }

    .returns-page .pagination .page-item.active .page-link {
        z-index: 1;
        border-color: #17365d;
        background: #17365d;
        color: #fff;
    }

    .returns-page .pagination .page-item.disabled .page-link {
        border-color: #edf0f5;
        background: #f8fafd;
        color: #b1bac7;
        cursor: not-allowed;
    }

    /* Адаптация */

    @media (max-width: 1200px) {
        .returns-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-actions {
            align-self: end;
        }

        .returns-stats {
            gap: 12px;
        }

        .returns-stat-card {
            padding: 17px;
        }

        .stat-value {
            font-size: 23px;
        }
    }

    @media (max-width: 768px) {
        .returns-page {
            padding-bottom: 18px;
        }

        .returns-header {
            align-items: flex-start;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }

        .returns-header h1 {
            font-size: 24px;
        }

        .returns-stats {
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 17px;
        }

        .returns-stat-card {
            align-items: center;
            padding: 15px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
        }

        .stat-value {
            font-size: 23px;
        }

        .returns-panel {
            margin-bottom: 17px;
            border-radius: 11px;
        }

        .panel-heading,
        .table-panel-heading {
            padding: 16px;
        }

        .returns-filter-form {
            grid-template-columns: 1fr;
            gap: 14px;
            padding: 16px;
        }

        .filter-actions {
            width: 100%;
        }

        .btn-filter,
        .btn-reset {
            flex: 1;
        }

        .returns-table {
            min-width: 970px;
        }

        .returns-pagination {
            align-items: flex-start;
            flex-direction: column;
            padding: 15px;
        }

        .pagination-controls,
        .returns-page .pagination-controls nav {
            justify-content: flex-start;
            width: 100%;
        }

        .returns-page .pagination {
            justify-content: flex-start;
            gap: 4px;
        }

        .returns-page .pagination .page-link {
            min-width: 32px;
            height: 32px;
            padding: 0 7px;
            font-size: 11px;
        }
    }

    @media (max-width: 380px) {
        .returns-page .pagination {
            gap: 3px;
        }

        .returns-page .pagination .page-link {
            min-width: 29px;
            height: 30px;
            padding: 0 5px;
            font-size: 10px;
        }

        .table-total-badge {
            padding: 6px 8px;
            font-size: 10px;
        }
    }
</style>
