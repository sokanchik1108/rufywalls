@extends('layouts.app')

@section('title', 'Возвраты')

@section('content')
@php
use Carbon\Carbon;
@endphp

<style>
    .returns-page {
        max-width: 1500px;
        margin: 0 auto;
        padding: 24px;
        color: #172033;
    }

    .returns-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
    }

    .returns-title {
        margin: 0;
        font-size: 27px;
        font-weight: 750;
        letter-spacing: -.5px;
    }

    .returns-subtitle {
        margin: 7px 0 0;
        color: #788195;
        font-size: 13px;
    }

    .returns-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .returns-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 0 15px;
        border: 1px solid #e0e5ed;
        border-radius: 10px;
        background: #fff;
        color: #273247;
        font-size: 13px;
        font-weight: 650;
        text-decoration: none;
        cursor: pointer;
        transition: .15s ease;
    }

    .returns-btn:hover {
        background: #f7f9fc;
        border-color: #cbd4e1;
        color: #172033;
    }

    .returns-btn-primary {
        background: #172b4d;
        color: #fff;
        border-color: #172b4d;
    }

    .returns-btn-primary:hover {
        background: #233e68;
        color: #fff;
        border-color: #233e68;
    }

    .returns-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .returns-stat {
        position: relative;
        overflow: hidden;
        padding: 20px;
        background: #fff;
        border: 1px solid #e8ecf2;
        border-radius: 15px;
        box-shadow: 0 3px 12px rgba(20, 35, 60, .035);
    }

    .returns-stat-label {
        margin-bottom: 12px;
        color: #7b8495;
        font-size: 12px;
        font-weight: 650;
    }

    .returns-stat-value {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -.6px;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .returns-stat-description {
        margin-top: 8px;
        color: #929aaa;
        font-size: 11px;
    }

    .returns-stat-icon {
        position: absolute;
        top: 17px;
        right: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 39px;
        height: 39px;
        border-radius: 12px;
        background: #eef3fb;
        color: #294b7c;
        font-size: 19px;
    }

    .returns-stat-money .returns-stat-icon {
        background: #e9f8ef;
        color: #16824a;
    }

    .returns-filters {
        display: grid;
        grid-template-columns: minmax(220px, 1.7fr) minmax(150px, 1fr) minmax(150px, 1fr) auto auto;
        gap: 10px;
        align-items: end;
        margin-bottom: 18px;
        padding: 17px;
        background: #fff;
        border: 1px solid #e8ecf2;
        border-radius: 14px;
    }

    .returns-field {
        min-width: 0;
    }

    .returns-field label {
        display: block;
        margin-bottom: 7px;
        color: #626d80;
        font-size: 11px;
        font-weight: 700;
    }

    .returns-input {
        width: 100%;
        height: 41px;
        box-sizing: border-box;
        padding: 0 11px;
        border: 1px solid #dfe4ec;
        border-radius: 9px;
        outline: none;
        background: #fff;
        color: #172033;
        font-size: 13px;
    }

    .returns-input:focus {
        border-color: #5276ae;
        box-shadow: 0 0 0 3px rgba(82, 118, 174, .1);
    }

    .returns-table-card {
        overflow: hidden;
        background: #fff;
        border: 1px solid #e8ecf2;
        border-radius: 15px;
        box-shadow: 0 3px 12px rgba(20, 35, 60, .035);
    }

    .returns-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 17px 19px;
        border-bottom: 1px solid #edf0f5;
    }

    .returns-table-title {
        margin: 0;
        font-size: 15px;
        font-weight: 750;
    }

    .returns-table-count {
        color: #7b8495;
        font-size: 12px;
    }

    .returns-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .returns-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .returns-table th {
        padding: 13px 15px;
        background: #f8f9fb;
        border-bottom: 1px solid #e9edf3;
        color: #788195;
        font-size: 10px;
        font-weight: 750;
        text-transform: uppercase;
        letter-spacing: .45px;
        white-space: nowrap;
    }

    .returns-table td {
        padding: 15px;
        border-bottom: 1px solid #edf0f5;
        color: #273247;
        font-size: 12px;
        vertical-align: middle;
    }

    .returns-table tbody tr:last-child td {
        border-bottom: none;
    }

    .returns-table tbody tr:hover {
        background: #fafbfd;
    }

    .return-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 48px;
        padding: 6px 9px;
        border-radius: 7px;
        background: #eef3fb;
        color: #294b7c;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .return-number:hover {
        background: #dfe9f8;
        color: #17345e;
    }

    .return-secondary {
        display: block;
        margin-top: 5px;
        color: #8a93a3;
        font-size: 10px;
    }

    .return-order-link {
        color: #294b7c;
        font-weight: 750;
        text-decoration: none;
    }

    .return-order-link:hover {
        text-decoration: underline;
    }

    .return-comment {
        display: block;
        max-width: 220px;
        margin-top: 5px;
        color: #8a93a3;
        font-size: 10px;
        overflow-wrap: anywhere;
    }

    .return-items {
        display: flex;
        flex-direction: column;
        gap: 7px;
        min-width: 190px;
        max-width: 310px;
    }

    .return-item {
        padding: 8px 9px;
        background: #f8f9fb;
        border: 1px solid #edf0f5;
        border-radius: 8px;
    }

    .return-item-name {
        color: #293449;
        font-size: 11px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .return-item-details {
        margin-top: 4px;
        color: #858e9f;
        font-size: 10px;
        line-height: 1.5;
    }

    .return-quantity {
        display: inline-block;
        padding: 5px 8px;
        border-radius: 7px;
        background: #fff4e5;
        color: #a15d08;
        font-size: 11px;
        font-weight: 750;
        white-space: nowrap;
    }

    .return-amount {
        color: #16824a;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
    }

    .return-payment-list {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 125px;
    }

    .return-payment {
        display: flex;
        flex-direction: column;
        gap: 3px;
        padding: 7px 8px;
        border: 1px solid #e8ecf2;
        border-radius: 7px;
        background: #fff;
    }

    .return-payment-name {
        color: #697488;
        font-size: 10px;
    }

    .return-payment-amount {
        color: #273247;
        font-size: 11px;
        font-weight: 750;
        white-space: nowrap;
    }

    .return-empty {
        padding: 45px 20px !important;
        text-align: center;
        color: #929aaa !important;
    }

    .return-empty-icon {
        margin-bottom: 10px;
        font-size: 27px;
    }

    .returns-pagination {
        padding: 16px 18px;
        border-top: 1px solid #edf0f5;
    }

    .returns-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .returns-alert {
        margin-bottom: 16px;
        padding: 13px 15px;
        border: 1px solid #bfe8ce;
        border-radius: 10px;
        background: #effaf3;
        color: #176c3c;
        font-size: 13px;
    }

    .returns-alert-error {
        border-color: #f0c5c5;
        background: #fff3f3;
        color: #a52c2c;
    }

    @media (max-width: 950px) {
        .returns-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .returns-field-search {
            grid-column: 1 / -1;
        }

        .returns-filters .returns-btn {
            width: 100%;
        }
    }

    @media (max-width: 600px) {
        .returns-page {
            padding: 13px;
        }

        .returns-header {
            align-items: flex-start;
        }

        .returns-title {
            font-size: 22px;
        }

        .returns-subtitle {
            max-width: 220px;
            line-height: 1.5;
        }

        .returns-header-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .returns-header-actions .returns-btn {
            min-height: 35px;
            padding: 0 10px;
            font-size: 11px;
        }

        .returns-stats {
            gap: 9px;
        }

        .returns-stat {
            padding: 14px;
            border-radius: 12px;
        }

        .returns-stat-label {
            max-width: 100px;
            min-height: 30px;
            font-size: 10px;
        }

        .returns-stat-value {
            font-size: 21px;
        }

        .returns-stat-icon {
            top: 12px;
            right: 12px;
            width: 30px;
            height: 30px;
            font-size: 15px;
        }

        .returns-stat-description {
            font-size: 9px;
            line-height: 1.4;
        }

        .returns-filters {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 12px;
        }

        .returns-field-search {
            grid-column: 1 / -1;
        }

        .returns-filters .returns-btn {
            min-height: 39px;
            padding: 0 8px;
            font-size: 11px;
        }

        .returns-table-header {
            padding: 14px;
        }

        .returns-table th,
        .returns-table td {
            padding: 12px 11px;
        }

        .returns-table th {
            font-size: 9px;
        }

        .returns-table td {
            font-size: 11px;
        }

        .return-items {
            min-width: 175px;
        }
    }

    @media (max-width: 600px) {

        .returns-page input,
        .returns-page select,
        .returns-page textarea {
            font-size: 16px !important;
        }
    }
</style>

<div class="returns-page">

    {{-- Заголовок --}}
    <div class="returns-header">
        <div>
            <h1 class="returns-title">Возвраты</h1>
            <p class="returns-subtitle">
                История возвратов товаров и денежных средств
            </p>
        </div>

        <div class="returns-header-actions">
            @if(auth()->check() && auth()->user()->is_owner)
            <a href="/admin" class="returns-btn">
                ← Меню
            </a>
            @endif
        </div>
    </div>

    {{-- Сообщения --}}
    @if(session('success'))
    <div class="returns-alert">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="returns-alert returns-alert-error">
        {{ session('error') }}
    </div>
    @endif

    {{-- Статистика --}}
    <div class="returns-stats">

        <div class="returns-stat">
            <div class="returns-stat-icon">↩</div>

            <div class="returns-stat-label">
                Количество возвратов
            </div>

            <div class="returns-stat-value">
                {{ number_format($totalReturns ?? 0, 0, '.', ' ') }}
            </div>

            <div class="returns-stat-description">
                С учётом выбранных фильтров
            </div>
        </div>

        <div class="returns-stat returns-stat-money">
            <div class="returns-stat-icon">₸</div>

            <div class="returns-stat-label">
                Общая сумма возвратов
            </div>

            <div class="returns-stat-value">
                {{ number_format((float) ($totalRefundAmount ?? 0), 0, '.', ' ') }} ₸
            </div>

            <div class="returns-stat-description">
                Сумма платежей по найденным возвратам
            </div>
        </div>

    </div>

    {{-- Фильтры --}}
    <form method="GET"
        action="{{ route('admin.returns.index') }}"
        class="returns-filters">

        <div class="returns-field returns-field-search">
            <label for="returns-search">Поиск</label>

            <input
                id="returns-search"
                type="text"
                name="search"
                class="returns-input"
                value="{{ $search ?? request('search', '') }}"
                placeholder="№ возврата, заказа, артикул, товар...">
        </div>

        <div class="returns-field">
            <label for="returns-date-from">Дата с</label>

            <input
                id="returns-date-from"
                type="date"
                name="date_from"
                class="returns-input"
                value="{{ $dateFrom ?? request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
        </div>

        <div class="returns-field">
            <label for="returns-date-to">Дата по</label>

            <input
                id="returns-date-to"
                type="date"
                name="date_to"
                class="returns-input"
                value="{{ $dateTo ?? request('date_to', now()->format('Y-m-d')) }}">
        </div>

        <button type="submit" class="returns-btn returns-btn-primary">
            Найти
        </button>

        <a href="{{ route('admin.returns.index') }}" class="returns-btn">
            Сбросить
        </a>

    </form>

    {{-- Таблица возвратов --}}
    <div class="returns-table-card">

        <div class="returns-table-header">
            <h2 class="returns-table-title">
                Список возвратов
            </h2>

            <span class="returns-table-count">
                Найдено: {{ $returns->total() }}
            </span>
        </div>

        <div class="returns-table-wrap">
            <table class="returns-table">
                <thead>
                    <tr>
                        <th>Возврат</th>
                        <th>Дата возврата</th>
                        <th>Заказ</th>
                        <th>Товары</th>
                        <th>Сумма возврата</th>
                        <th>Способ возврата</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($returns as $return)
                    @php
                    $returnDate = $return->return_date
                    ? \Carbon\Carbon::parse($return->return_date)
                    : null;

                    $payments = $return->payments ?? collect();

                    $refundAmount = $payments->sum(function ($payment) {
                    return (float) ($payment->amount ?? 0);
                    });
                    @endphp

                    <tr>
                        {{-- Номер возврата --}}
                        <td>
                            <a
                                href="{{ route('admin.orders.returns.show', $return->id) }}"
                                class="return-number">
                                #{{ $return->id }}
                            </a>
                        </td>

                        {{-- Дата возврата --}}
                        <td>
                            @if($returnDate)
                            <strong>{{ $returnDate->format('d.m.Y') }}</strong>

                            <span class="return-secondary">
                                {{ $returnDate->format('H:i') }}
                            </span>
                            @else
                            —
                            @endif
                        </td>

                        {{-- Заказ --}}
                        <td>
                            <a
                                href="{{ route('admin.orders.returns.show', $return->id) }}"
                                class="return-order-link">
                                Заказ #{{ $return->order_id ?? '—' }}
                            </a>

                            @if($return->order?->pointOfSale)
                            <span class="return-secondary">
                                {{ $return->order->pointOfSale->name }}
                            </span>
                            @endif
                        </td>

                        {{-- Товары и количество в одном столбце --}}
                        <td>
                            <div class="return-items">
                                @forelse($return->items ?? collect() as $item)
                                @php
                                $variant = $item->variant;
                                $product = $variant?->product;

                                $sku = $variant?->sku
                                ?? $item->orderItem?->variant?->sku
                                ?? '—';

                                $productName = $product?->name
                                ?? $item->orderItem?->variant?->product?->name
                                ?? 'Товар';

                                $batchName = $item->batch?->code
                                ?? $item->batch?->name
                                ?? null;

                                $warehouseName = $item->warehouse?->name
                                ?? null;
                                @endphp

                                <div class="return-item">
                                    <div class="return-item-name">
                                        {{ $sku }}
                                        · <strong>{{ number_format((float) $item->quantity, 0, '.', ' ') }} шт.</strong>

                                        @if($batchName)
                                        <br>Партия: {{ $batchName }}
                                        @endif

                                        @if($warehouseName)
                                        <br>Склад: {{ $warehouseName }}
                                        @endif
                                    </div>


                                </div>
                                @empty
                                <span class="return-secondary">
                                    Товары не найдены
                                </span>
                                @endforelse
                            </div>
                        </td>

                        {{-- Сумма возврата --}}
                        <td>
                            <div class="return-amount">
                                {{ number_format((float) $refundAmount, 0, '.', ' ') }} ₸
                            </div>
                        </td>

                        {{-- Способ возврата --}}
                        <td>
                            <div class="return-payment-list">
                                @forelse($payments as $payment)
                                <div class="return-payment">
                                    <span class="return-payment-name">
                                        {{ $payment->paymentMethod?->name ?? 'Способ не указан' }}
                                    </span>

                                    <span class="return-payment-amount">
                                        {{ number_format((float) ($payment->amount ?? 0), 0, '.', ' ') }} ₸
                                    </span>
                                </div>
                                @empty
                                <span class="return-secondary">
                                    Нет платежей
                                </span>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="return-empty">
                            <div class="return-empty-icon">↩</div>

                            <strong>Возвраты не найдены</strong>

                            <div class="return-secondary">
                                Измени параметры поиска или сбрось фильтры.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
        <div class="returns-pagination">
            {{ $returns->links() }}
        </div>
        @endif

    </div>

</div>
@endsection