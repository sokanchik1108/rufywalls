@extends('layouts.app')

@section('title', 'Заказы продавцов')

@section('content')

@php
$selectedDate = request()->get('date') ?? now()->format('Y-m-d');
$prevDate = \Carbon\Carbon::parse($selectedDate)->subDay()->format('Y-m-d');
$nextDate = \Carbon\Carbon::parse($selectedDate)->addDay()->format('Y-m-d');
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<div class="container py-3">

    {{-- ALERT --}}
    @if(session('success'))
    <div class="alert alert-success rounded-3 shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold mb-0">Заказы</h5>

        <a href="{{ route('admin.orders.create') }}"
            class="btn btn-primary btn-sm rounded-3 px-3">
            Создать
        </a>
    </div>

    {{-- DATE --}}
    <div class="d-flex gap-2 align-items-center mb-3">

        <a href="{{ route('admin.orders.seller', ['date' => $prevDate]) }}"
            class="btn btn-light border btn-sm">
            <i class="bi bi-chevron-left"></i>
        </a>

        <form method="GET" class="flex-grow-1">
            <input type="date"
                name="date"
                value="{{ $selectedDate }}"
                onchange="this.form.submit()"
                class="form-control form-control-sm text-center rounded-3">
        </form>

        <a href="{{ route('admin.orders.seller', ['date' => $nextDate]) }}"
            class="btn btn-light border btn-sm">
            <i class="bi bi-chevron-right"></i>
        </a>

    </div>

    {{-- SEARCH --}}
    <input type="text"
        id="searchInput"
        class="form-control form-control-sm mb-3 rounded-3"
        placeholder="Поиск заказов">

    {{-- LIST --}}
    <div id="mobileOrders">

        @foreach($orders as $order)

        <div class="card mb-2 border-0 shadow-sm rounded-4 position-relative">

            {{-- EDIT --}}
            <a href="{{ route('admin.orders.edit', $order->id) }}"
                class="icon-btn icon-edit">
                <i class="bi bi-pencil"></i>
            </a>

            {{-- DELETE --}}
            <form action="{{ route('admin.orders.destroy', $order->id) }}"
                method="POST"
                class="icon-btn icon-delete"
                onsubmit="return confirm('Удалить заказ?')">

                @csrf
                @method('DELETE')

                <button type="submit">
                    <i class="bi bi-trash"></i>
                </button>
            </form>

            <div class="card-body pt-5">

                <div class="d-flex justify-content-between">
                    <span class="fw-semibold">#{{ $order->id }}</span>
                    <span class="text-muted small">
                        {{ $order->created_at->format('d.m.Y H:i') }}
                    </span>
                </div>

                <div class="mt-2">
                    <div class="fw-medium">{{ $order->name }}</div>
                    <div class="text-muted small">{{ $order->phone }}</div>
                </div>

                <button class="btn btn-primary btn-sm w-100 mt-2 rounded-3"
                    data-bs-toggle="modal"
                    data-bs-target="#orderModal{{ $order->id }}">
                    Подробнее
                </button>

            </div>
        </div>

        @endforeach

    </div>

    {{-- MODALS --}}
    @foreach($orders as $order)

    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">

        <div class="modal-dialog modal-fullscreen">

            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title">Заказ #{{ $order->id }}</h6>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-2">
                        <div class="text-muted small">Дата</div>
                        <div>{{ $order->created_at->format('d.m.Y H:i') }}</div>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted small">Имя</div>
                        <div>{{ $order->name }}</div>
                    </div>

                    <div class="mb-2">
                        <div class="text-muted small">Телефон</div>
                        <div>{{ $order->phone }}</div>
                    </div>

                    @if($order->comment)
                    <div class="mb-2">
                        <div class="text-muted small">Комментарий</div>
                        <div>{{ $order->comment }}</div>
                    </div>
                    @endif

                    <hr>

                    @foreach($order->items as $item)
                    <div class="border rounded-3 p-2 mb-2">
                        <div class="fw-medium">{{ $item->variant->sku ?? '—' }}</div>
                        <div class="text-muted small">
                            Кол-во: {{ $item->quantity }} • {{ $item->warehouse_name }}
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
        const original = container.innerHTML;

        let timer;

        search.addEventListener('input', function() {

            clearTimeout(timer);

            timer = setTimeout(() => {

                const q = this.value.trim();

                if (!q.length) {
                    container.innerHTML = original;
                    return;
                }

                fetch(`/admin/orders/search?q=${q}`)
                    .then(r => r.json())
                    .then(data => {

                        container.innerHTML = '';

                        data.forEach(o => {

                            container.innerHTML += `
<div class="card mb-2 border-0 shadow-sm rounded-4 position-relative">

    <a href="/admin/orders/${o.id}/edit" class="icon-btn icon-edit">
        <i class="bi bi-pencil"></i>
    </a>

    <form action="/admin/orders/${o.id}" method="POST"
          class="icon-btn icon-delete"
          onsubmit="return confirm('Удалить заказ?')">

        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="DELETE">

        <button type="submit">
            <i class="bi bi-trash"></i>
        </button>
    </form>

    <div class="card-body pt-5">

        <div class="d-flex justify-content-between">
            <span class="fw-semibold">#${o.id}</span>
            <span class="text-muted small">${o.created_at ? o.created_at.replace('T',' ').slice(0,16) : ''}</span>
        </div>

        <div class="mt-2 fw-medium">${o.name}</div>
        <div class="text-muted small">${o.phone}</div>

        <button class="btn btn-primary btn-sm w-100 mt-2 rounded-3"
                data-bs-toggle="modal"
                data-bs-target="#orderModal${o.id}">
            Подробнее
        </button>

    </div>
</div>`;
                        });

                    });

            }, 300);

        });

    });
</script>

{{-- STYLE --}}
<style>
    body {
        background: #f6f7fb;
    }

    .icon-btn {
        position: absolute;
        top: 8px;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 14px;
    }

    .icon-edit {
        left: 8px;
        background: #eef4ff;
        color: #0d6efd;
    }

    .icon-delete {
        right: 8px;
    }

    .icon-delete button {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 8px;
        background: #ffecec;
        color: #dc3545;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        border-radius: 14px;
    }
</style>

@endsection