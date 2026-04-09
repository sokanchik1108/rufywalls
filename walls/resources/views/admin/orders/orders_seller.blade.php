@extends('layouts.app')

@section('title', 'Заказы продавцов')

@section('content')
@php
$selectedDate = request()->get('date') ?? now()->format('Y-m-d');
$prevDate = \Carbon\Carbon::parse($selectedDate)->subDay()->format('Y-m-d');
$nextDate = \Carbon\Carbon::parse($selectedDate)->addDay()->format('Y-m-d');

// Общая сумма всех заказов за день
$totalDay = 0;
foreach($orders as $order){
$totalOrder = 0;
foreach($order->items as $i){
$totalOrder += $i->price * $i->quantity;
}
$finalOrder = $totalOrder - ($order->discount ?? 0);
$totalDay += $finalOrder;
}
@endphp

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h1 class="fw-semibold mb-2">Заказы продавцов</h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.orders.create') }}" class="btn btn-sm btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg"></i> Создать заказ
            </a>
            <form action="{{ route('admin.orders.destroyAll') }}" method="POST" onsubmit="return confirm('Удалить все заказы?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="date" value="{{ request()->get('date') }}">
                <button class="btn btn-sm btn-outline-danger rounded-pill px-4">
                    <i class="bi bi-trash"></i> Удалить все
                </button>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <a href="{{ route('admin.orders.seller', ['date' => $prevDate]) }}" class="btn btn-outline-secondary mb-2">&larr; Назад</a>
        <span class="fw-semibold mb-2">{{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}</span>
        <a href="{{ route('admin.orders.seller', ['date' => $nextDate]) }}" class="btn btn-outline-secondary mb-2">Вперед &rarr;</a>
    </div>

    <div class="mb-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Поиск по № заказа, имени или телефону">
    </div>

    @if(session('success'))
    <div id="successAlert" class="alert alert-success shadow-sm rounded-3">{{ session('success') }} — {{ now()->format('H:i:s') }}</div>
    @endif

    {{-- Таблица для десктопа --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle shadow-sm rounded-4 overflow-hidden bg-white">
            <thead class="table-light">
                <tr class="text-nowrap">
                    <th>№ заказа</th>
                    <th>Клиент</th>
                    <th>Телефон</th>
                    <th>Итог</th>
                    <th class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody id="desktopOrders">
                @foreach($orders as $order)
                @php
                $total = 0;
                foreach($order->items as $i){
                $total += $i->price * $i->quantity;
                }
                $final = $total - ($order->discount ?? 0);
                @endphp
                <tr class="order-row">
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->name }}</td>
                    <td>{{ $order->phone }}</td>
                    <td>
                        @if($final < 0)
                            <span class="text-danger fw-bold">ВОЗВРАТ</span>
                            @endif
                            {{ $final < 0 ? '-' : '' }}{{ number_format(abs($final), 0, ',', ' ') }} ₸
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-warning me-2">Редактировать</a>
                        <button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">Детали</button>
                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Удалить заказ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Удалить</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Общая сумма за день:</td>
                    <td colspan="2" class="fw-bold">{{ $totalDay < 0 ? '-' : '' }}{{ number_format(abs($totalDay),0,',',' ') }} ₸</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Мобильные карточки --}}
    <div id="mobileOrders" class="d-md-none">
        @foreach($orders as $order)
        @php
        $total = 0;
        foreach($order->items as $i){
        $total += $i->price * $i->quantity;
        }
        $final = $total - ($order->discount ?? 0);
        @endphp
        <div class="card mb-3 shadow-sm border-0 rounded-4 order-card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between">
                    <strong>#{{ $order->id }}</strong>
                    <span>
                        @if($final < 0)
                            <span class="text-danger fw-bold">ВОЗВРАТ</span>
                    @endif
                    {{ $final < 0 ? '-' : '' }}{{ number_format(abs($final),0,',',' ') }} ₸
                    </span>
                </div>
                <div class="mt-2">
                    <div class="fw-semibold">{{ $order->name }}</div>
                    <div class="text-muted small">{{ $order->phone }}</div>
                </div>
                @if($order->discount > 0)
                <div class="text-success small mt-1">Скидка {{ number_format($order->discount,0,',',' ') }} ₸</div>
                @endif
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-warning w-100">Редактировать</a>
                    <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">Детали</button>
                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="w-100" onsubmit="return confirm('Удалить заказ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        <div class="card mb-3 shadow-sm border-0 rounded-4">
            <div class="card-body p-3 text-end fw-bold fs-5">
                Общая сумма за день: {{ $totalDay < 0 ? '-' : '' }}{{ number_format(abs($totalDay),0,',',' ') }} ₸
            </div>
        </div>
    </div>

    {{-- Модалки (как раньше, без изменений, только итог с минусом) --}}
    @foreach($orders as $order)
    @php
    $total = 0;
    foreach($order->items as $i){
    $total += $i->price * $i->quantity;
    }
    $final = $total - ($order->discount ?? 0);
    @endphp
    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Заказ #{{ $order->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-12 mb-3">
                                <div class="p-3 bg-light rounded-3 shadow-sm"><strong>Имя:</strong> {{ $order->name }}</div>
                                <div class="p-3 bg-light rounded-3 shadow-sm mt-2"><strong>Телефон:</strong> {{ $order->phone }}</div>
                                @if($order->comment)
                                <div class="p-3 bg-light rounded-3 shadow-sm mt-2"><strong>Комментарий:</strong> {{ $order->comment }}</div>
                                @endif
                                @if($order->discount > 0)
                                <div class="p-3 bg-light rounded-3 shadow-sm mt-2 text-success">
                                    Скидка: {{ number_format($order->discount,0,',',' ') }} ₸
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach($order->items as $item)
                            <div class="col-12">
                                <div class="card p-3 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div>
                                                <strong>{{ $item->variant->sku ?? '—' }}</strong> ( партия {{ $item->batch_code ?? '—'}})
                                                @if($item->quantity < 0)
                                                    <span class="text-danger fw-bold">ВОЗВРАТ</span>
                                                    @endif
                                            </div>
                                            <div>Кол-во: {{ $item->quantity }} × {{ number_format($item->price,0,',',' ') }} ₸</div>
                                        </div>
                                        <div class="fw-bold">Итого: {{ number_format($item->price * $item->quantity,0,',',' ') }} ₸</div>
                                    </div>
                                    <div class="mt-2">
                                        <div>Склад: {{ $item->warehouse_name ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 text-end fw-bold fs-5">
                            Общая сумма: {{ $final < 0 ? '-' : '' }}{{ number_format(abs($final),0,',',' ') }} ₸
                            @if($final < 0)
                                <span class="text-danger fw-bold">(ВОЗВРАТ)</span>
                                @elseif($order->discount > 0)
                                <span class="text-success small">(Скидка {{ number_format($order->discount,0,',',' ') }} ₸)</span>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<style>
    tr:hover {
        background-color: #f1f5f9;
    }

    .btn-outline-secondary {
        min-width: 100px;
    }

    .modal-body {
        background-color: #f8f9fa;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        setTimeout(() => {
            const alert = document.getElementById('successAlert');
            if (alert) {
                alert.style.transition = '0.5s';
                alert.style.opacity = 0;
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);

        const searchInput = document.getElementById('searchInput');
        const desktopContainer = document.getElementById('desktopOrders');
        const mobileContainer = document.getElementById('mobileOrders');
        const originalDesktop = desktopContainer.innerHTML;
        const originalMobile = mobileContainer.innerHTML;

        let timeout = null;
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                if (query.length < 1) {
                    desktopContainer.innerHTML = originalDesktop;
                    mobileContainer.innerHTML = originalMobile;
                    return;
                }

                fetch(`/admin/orders/search?q=${query}`).then(res => res.json()).then(data => {
                    desktopContainer.innerHTML = '';
                    mobileContainer.innerHTML = '';
                    document.querySelectorAll('.dynamic-modal').forEach(m => m.remove());

                    data.forEach(order => {
                        let total = 0;
                        order.items.forEach(i => total += i.price * i.quantity);
                        const isReturn = total < 0;

                        // Десктоп
                        desktopContainer.innerHTML += `
<tr class="order-row">
<td>${order.id}</td>
<td>${order.name}</td>
<td>${order.phone}</td>
<td>${isReturn?'<span class="text-danger fw-bold">ВОЗВРАТ</span>':''} ${Math.abs(total).toLocaleString()} ₸</td>
<td class="text-center">
<button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#orderModal${order.id}">Детали</button>
<form action="/admin/orders/${order.id}" method="POST" class="d-inline" onsubmit="return confirm('Удалить заказ?')">
@csrf
@method('DELETE')
<button class="btn btn-sm btn-outline-danger">Удалить</button>
</form>
</td>
</tr>`;

                        // Мобильные
                        mobileContainer.innerHTML += `
<div class="card mb-3 shadow-sm border-0 rounded-4 order-card">
<div class="card-body p-3">
<div class="d-flex justify-content-between">
<strong>#${order.id}</strong>
<span>${isReturn?'<span class="text-danger fw-bold">ВОЗВРАТ</span>':''} ${Math.abs(total).toLocaleString()} ₸</span>
</div>
<div class="mt-2">
<div class="fw-semibold">${order.name}</div>
<div class="text-muted small">${order.phone}</div>
</div>
<div class="d-flex gap-2 mt-3">
<button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#orderModal${order.id}">Детали</button>
<form action="/admin/orders/${order.id}" method="POST" class="w-100" onsubmit="return confirm('Удалить заказ?')">
@csrf
@method('DELETE')
<button class="btn btn-sm btn-outline-danger w-100">Удалить</button>
</form>
</div>
</div></div>`;

                        // Модалка
                        const modal = document.createElement('div');
                        modal.classList.add('modal', 'fade', 'dynamic-modal');
                        modal.id = `orderModal${order.id}`;
                        modal.tabIndex = -1;
                        modal.innerHTML = `
<div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header bg-primary text-white">
<h5 class="modal-title">Заказ #${order.id}</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="container-fluid">
<div class="row g-3">
${order.items.map(i=>`
<div class="col-12">
<div class="card p-3 mb-2">
<div class="d-flex justify-content-between">
<div>
<div><strong>${i.variant?.sku ?? '—'}</strong> ( партия ${i.batch_code ?? '—'}) ${i.quantity<0?'<span class="text-danger fw-bold">ВОЗВРАТ</span>':''}</div>
<div>Кол-во: ${i.quantity} × ${i.price.toLocaleString()} ₸</div>
</div>
<div class="fw-bold">Итого: ${(i.price*i.quantity).toLocaleString()} ₸</div>
</div>
<div class="mt-2">
<div>Склад: ${i.warehouse_name ?? '—'}</div>
</div>
</div></div>`).join('')}
</div>
<div class="mt-4 text-end fw-bold fs-5">
Общая сумма: ${Math.abs(total).toLocaleString()} ₸ ${isReturn?'<span class="text-danger fw-bold">(ВОЗВРАТ)</span>':''}
</div>
</div></div></div></div>`;
                        document.body.appendChild(modal);
                    });
                });
            }, 300);
        });

    });
</script>

@endsection