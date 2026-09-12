@extends('layouts.app')

@section('title', 'Заказы продавцов')

@section('content')

@php
$selectedDate = request()->get('date')
? \Carbon\Carbon::parse(request()->get('date'))->format('Y-m-d')
: now()->format('Y-m-d');
$prevDate = \Carbon\Carbon::parse($selectedDate)->subDay()->format('Y-m-d');
$nextDate = \Carbon\Carbon::parse($selectedDate)->addDay()->format('Y-m-d');

$totalDaySum = 0;
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --bg: #f4f5f7;
        --surface: #ffffff;
        --ink: #1a1d21;
        --ink-soft: #6e7580;
        --ink-faint: #9aa0a8;
        --border: #dfe3e8;
        --border-strong: #c9ced6;
        --primary: #01142f;
        --primary-hover: #02214b;
        --primary-soft: #eaf1fe;
        --danger: #e5484d;
        --danger-soft: #fdeceb;
        --success: #1c9a6c;
        --success-soft: #e6f6ef;
        --radius: 6px;
        --radius-lg: 8px;
    }

    * {
        box-sizing: border-box
    }

    body {
        background: var(--bg);
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        color: var(--ink);
        font-size: 14px;
        -webkit-font-smoothing: antialiased
    }

    .container {
        max-width: 600px;
    }

    /* ---------- Alert ---------- */
    .alert-success {
        background: var(--success-soft);
        border: 1px solid #bfe6d5;
        color: var(--success);
        border-radius: var(--radius-lg);
        font-size: 13.5px;
        padding: 10px 14px;
        box-shadow: none;
    }

    /* ---------- Header ---------- */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }

    .page-header h5 {
        font-size: 17px;
        font-weight: 700;
        letter-spacing: -.005em;
        margin: 0;
    }

    .btn-primary {
        background: var(--primary);
        border: none;
        color: #fff;
        font-weight: 600;
        font-size: 13px;
        border-radius: var(--radius) !important;
        padding: 7px 16px;
        transition: background .12s;
    }

    .btn-primary:hover,
    .btn-primary:focus {
        background: var(--primary-hover);
        color: #fff;
    }

    /* ---------- Date navigator ---------- */
    .date-nav {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 10px;
    }

    .date-nav .btn-light {
        background: var(--surface);
        border: 1px solid var(--border) !important;
        color: var(--ink-soft);
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius) !important;
        padding: 0;
        transition: border-color .12s, color .12s;
    }

    .date-nav .btn-light:hover {
        border-color: var(--primary) !important;
        color: var(--primary);
        background: var(--surface);
    }

    .date-nav form {
        flex: 1;
    }

    /* ---------- Inputs ---------- */
    .form-control {
        border: 1px solid var(--border) !important;
        border-radius: var(--radius) !important;
        font-family: inherit;
        font-size: 13.5px;
        color: var(--ink);
        background: var(--surface);
        transition: border-color .12s, box-shadow .12s;
        box-shadow: none !important;
    }

    .form-control::placeholder {
        color: var(--ink-faint);
    }

    .form-control:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px var(--primary-soft) !important;
    }

    #searchInput {
        height: 36px;
        font-size: 16px;
    }

    /* ---------- Daily total ---------- */
    .day-total {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 10px 14px;
        margin-bottom: 14px;
    }

    .day-total .label {
        font-size: 12.5px;
        color: var(--ink-soft);
        font-weight: 500;
    }

    .day-total .value {
        font-size: 16px;
        font-weight: 700;
    }

    /* ---------- Order cards ---------- */
    .order-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 12px 14px;
        margin-bottom: 10px;
    }

    .order-card-top {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 8px;
    }

    .order-id {
        font-weight: 700;
        font-size: 14px;
    }

    .order-date {
        font-size: 12px;
        color: var(--ink-faint);
    }

    .order-name {
        font-weight: 600;
        font-size: 13.5px;
    }

    .order-phone {
        font-size: 12.5px;
        color: var(--ink-soft);
        margin-top: 1px;
    }

    .order-card-bottom {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border);
    }

    .order-totals .discount {
        display: block;
        font-size: 11.5px;
        color: var(--ink-faint);
        margin-bottom: 2px;
    }

    .order-totals .final-sum {
        font-size: 15px;
        font-weight: 700;
    }

    .order-actions {
        display: flex;
        gap: 6px;
    }

    .icon-btn {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--ink-soft);
        font-size: 14px;
        transition: border-color .12s, color .12s, background .12s;
    }

    a.icon-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-soft);
    }

    .icon-delete-form {
        display: inline-flex;
        margin: 0;
    }

    .icon-delete-form button.icon-btn {
        cursor: pointer;
    }

    .icon-delete-form button.icon-btn:hover {
        border-color: var(--danger);
        color: var(--danger);
        background: var(--danger-soft);
    }

    .details-btn {
        width: 100%;
        margin-top: 10px;
        background: var(--primary);
        color: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 13px;
        padding: 8px;
        cursor: pointer;
        transition: background .12s, border-color .12s;
    }

    .details-btn:hover {
        background: var(--primary-soft);
        border-color: var(--primary);
    }

    /* ---------- Modal ---------- */
    .modal-content {
        border: none;
        border-radius: 0;
        background: var(--bg);
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .modal-header {
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        padding: 14px 18px;
    }

    .modal-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
    }

    .btn-close {
        opacity: .5;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 16px 18px 20px;
    }

    .modal-body .info-row {
        margin-bottom: 12px;
    }

    .modal-body .info-label {
        font-size: 12px;
        color: var(--ink-soft);
        margin-bottom: 2px;
    }

    .modal-body .info-value {
        font-size: 14.5px;
        font-weight: 600;
    }

    .modal-body hr {
        border-color: var(--border);
        margin: 14px 0;
        opacity: 1;
    }

    .modal-item {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 10px 12px;
        margin-bottom: 8px;
        background: var(--surface);
    }

    .modal-item .item-sku {
        font-weight: 600;
        font-size: 13.5px;
    }

    .modal-item .item-qty {
        color: var(--ink-soft);
        font-size: 12.5px;
        margin-top: 2px;
    }

    .modal-item .item-sum {
        font-weight: 700;
        font-size: 13.5px;
        text-align: right;
        margin-top: 4px;
    }

    .item-batch {
        margin-top: 5px;
        color: var(--ink-soft);
        font-size: 12px;
    }
</style>

<div class="container py-3">

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="page-header">
        <h5>Заказы</h5>

        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary btn-sm">
            Создать
        </a>
    </div>

    <div class="date-nav">

        <a href="{{ route('admin.orders.seller', ['date' => $prevDate]) }}" class="btn btn-light">
            <i class="bi bi-chevron-left"></i>
        </a>

        <form method="GET">
            <input type="date"
                name="date"
                value="{{ $selectedDate }}"
                onchange="this.form.submit()"
                class="form-control form-control-sm text-center">
        </form>

        <a href="{{ route('admin.orders.seller', ['date' => $nextDate]) }}" class="btn btn-light">
            <i class="bi bi-chevron-right"></i>
        </a>

    </div>

    <input type="text" id="searchInput" class="form-control form-control-sm mb-3" placeholder="Поиск заказов">

    {{-- ДНЕВНАЯ СУММА --}}
    @foreach($orders as $order)
    @php
    $totalDaySum += ($order->items->sum(fn($i)=>($i->price ?? 0)*$i->quantity)) - ($order->discount ?? 0);
    @endphp
    @endforeach

    <div class="day-total">
        <span class="label">Общая сумма за день</span>
        <span class="value">{{ number_format($totalDaySum, 0, '.', ' ') }} ₸</span>
    </div>

    <div id="mobileOrders">

        @foreach($orders as $order)

        @php
        $orderSum = $order->items->sum(fn($i)=>($i->price ?? 0)*$i->quantity);
        $finalSum = $orderSum - ($order->discount ?? 0);
        @endphp

        <div class="order-card">

            <div class="order-card-top">
                <span class="order-id">#{{ $order->id }}</span>
                <span class="order-date">{{ $order->order_date?->format('d.m.Y H:i') ?? '—' }}</span>
            </div>

            <div class="order-name">{{ $order->name }}</div>
            <div class="order-phone">{{ $order->phone }}</div>

            <div class="order-card-bottom">
                <div class="order-totals">
                    <span class="discount">Скидка: {{ number_format($order->discount ?? 0, 0, '.', ' ') }} ₸</span>
                    <span class="final-sum">{{ number_format($finalSum, 0, '.', ' ') }} ₸</span>
                </div>

                <div class="order-actions">
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="icon-btn" title="Редактировать">
                        <i class="bi bi-pencil"></i>
                    </a>

                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST"
                        class="icon-delete-form"
                        onsubmit="return confirm('Удалить заказ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="icon-btn" title="Удалить"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>

            <button class="details-btn"
                data-bs-toggle="modal"
                data-bs-target="#orderModal{{ $order->id }}">
                Подробнее
            </button>

        </div>

        @endforeach

    </div>

    {{-- MODALS --}}
    @foreach($orders as $order)

    @php
    $orderSum = $order->items->sum(fn($i)=>($i->price ?? 0)*$i->quantity);
    $finalSum = $orderSum - ($order->discount ?? 0);
    @endphp

    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title">Заказ #{{ $order->id }}</h6>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">


                    <div class="info-row">
                        <div class="info-label">Имя</div>
                        <div class="info-value">
                            {{ $order->name ?: '—' }}
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Номер телефона</div>
                        <div class="info-value">
                            {{ $order->phone ?: '—' }}
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Дата</div>
                        <div class="info-value">
                            {{ $order->order_date?->format('d.m.Y H:i') ?? '—' }}
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Комментарий</div>
                        <div class="info-value">
                            {{ $order->comment ?: '—' }}
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Скидка</div>
                        <div class="info-value" style="color: var(--danger)">
                            {{ number_format($order->discount ?? 0, 0, '.', ' ') }} ₸
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Сумма заказа</div>
                        <div class="info-value">
                            {{ number_format($finalSum, 0, '.', ' ') }} ₸
                        </div>
                    </div>

                    <hr>


                    @foreach($order->items as $item)
                    <div class="modal-item">

                        <div class="item-sku">
                            {{ $item->variant->sku ?? '—' }} ({{ $item->batch_code ?? '—' }})
                        </div>

                        <div class="item-qty">
                            {{ $item->quantity }}
                            ×
                            {{ number_format($item->price ?? 0, 0, '.', ' ') }} ₸
                        </div>

                        <div class="item-sum">
                            {{ number_format(($item->price ?? 0) * $item->quantity, 0, '.', ' ') }} ₸
                        </div>
                    </div>
                    @endforeach

                </div>

            </div>
        </div>
    </div>

    @endforeach

</div>

{{-- SEARCH JS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const search = document.getElementById('searchInput');
        const container = document.getElementById('mobileOrders');

        let timer;

        search.addEventListener('input', function() {

            clearTimeout(timer);

            timer = setTimeout(() => {

                const q = this.value.trim();

                // если пустой поиск — перезагружаем страницу (возвращаем Blade)
                if (!q.length) {
                    location.reload();
                    return;
                }

                fetch(`/admin/orders/search?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {

                        container.innerHTML = '';

                        data.forEach(o => {

                            // считаем сумму заказа
                            let orderSum = 0;

                            if (o.items && o.items.length) {
                                orderSum = o.items.reduce((sum, i) => {
                                    return sum + ((i.price || 0) * (i.quantity || 0));
                                }, 0);
                            }

                            const discount = o.discount || 0;
                            const finalSum = orderSum - discount;

                            const date = o.order_date ?
                                o.order_date.replace('T', ' ').slice(0, 16) :
                                (o.created_at ? o.created_at.replace('T', ' ').slice(0, 16) : '');

                            container.innerHTML += `
<div class="order-card">

    <div class="order-card-top">
        <span class="order-id">#${o.id}</span>
        <span class="order-date">${date}</span>
    </div>

    <div class="order-name">${o.name ?? ''}</div>
    <div class="order-phone">${o.phone ?? ''}</div>

    <div class="order-card-bottom">
        <div class="order-totals">
            <span class="discount">Скидка: ${discount.toLocaleString()} ₸</span>
            <span class="final-sum">${finalSum.toLocaleString()} ₸</span>
        </div>

        <div class="order-actions">
            <a href="/admin/orders/${o.id}/edit" class="icon-btn" title="Редактировать">
                <i class="bi bi-pencil"></i>
            </a>

            <form action="/admin/orders/${o.id}" method="POST"
                  class="icon-delete-form"
                  onsubmit="return confirm('Удалить заказ?')">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">

                <button type="submit" class="icon-btn" title="Удалить">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>

    <button class="details-btn"
            data-bs-toggle="modal"
            data-bs-target="#orderModal${o.id}">
        Подробнее
    </button>

</div>`;
                        });

                    })
                    .catch(err => {
                        console.error('Search error:', err);
                    });

            }, 300);

        });

    });
</script>

@endsection