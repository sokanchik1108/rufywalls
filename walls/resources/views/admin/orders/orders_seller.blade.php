@extends('layouts.app')

@section('title', 'Заказы продавцов')

@section('content')

@php
$selectedDate = request()->get('date') ?? now()->format('Y-m-d');
$prevDate = \Carbon\Carbon::parse($selectedDate)->subDay()->format('Y-m-d');
$nextDate = \Carbon\Carbon::parse($selectedDate)->addDay()->format('Y-m-d');
@endphp

<div class="container py-3">

    {{-- SUCCESS --}}
    @if(session('success'))
    <div id="successAlert" class="alert alert-success shadow-sm rounded-3">
        {{ session('success') }}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="fw-bold mb-0">Заказы</h5>

        <a href="{{ route('admin.orders.create') }}"
            class="btn btn-primary btn-sm rounded-pill px-3">
            + Создать
        </a>
    </div>

    {{-- DATE NAV --}}
    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">

        <a href="{{ route('admin.orders.seller', ['date' => $prevDate]) }}"
            class="btn btn-outline-secondary btn-sm px-3">
            ←
        </a>

        <form method="GET" class="flex-grow-1 text-center">
            <input type="date"
                name="date"
                value="{{ $selectedDate }}"
                onchange="this.form.submit()"
                class="form-control form-control-sm text-center fw-semibold">
        </form>

        <a href="{{ route('admin.orders.seller', ['date' => $nextDate]) }}"
            class="btn btn-outline-secondary btn-sm px-3">
            →
        </a>

    </div>

    {{-- SEARCH --}}
    <input type="text"
        id="searchInput"
        class="form-control form-control-sm mb-3"
        placeholder="Поиск по заказу, имени, телефону">

    {{-- ORDERS --}}
    <div id="mobileOrders">

        @foreach($orders as $order)

        <div class="card mb-2 shadow-sm border-0 rounded-4">

            <div class="card-body p-3">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center">
                    <strong>#{{ $order->id }}</strong>
                    <small class="text-muted">
                        {{ $order->created_at->format('d.m.Y H:i') }}
                    </small>
                </div>

                {{-- CLIENT --}}
                <div class="mt-2">
                    <div class="fw-semibold">{{ $order->name }}</div>
                    <div class="text-muted small">{{ $order->phone }}</div>
                </div>

                {{-- ACTIONS --}}
                <div class="d-flex gap-2 mt-3">

                    {{-- EDIT --}}
                    <a href="{{ route('admin.orders.edit', $order->id) }}"
                        class="btn btn-warning btn-sm w-50 d-flex justify-content-center align-items-center gap-1">
                        ✏️ Ред
                    </a>

                    {{-- DELETE --}}
                    <form action="{{ route('admin.orders.destroy', $order->id) }}"
                        method="POST"
                        class="w-50"
                        onsubmit="return confirm('Удалить заказ?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-outline-danger btn-sm w-100 d-flex justify-content-center align-items-center gap-1">
                            🗑 Удал
                        </button>

                    </form>

                </div>

                {{-- DETAILS --}}
                <button class="btn btn-primary btn-sm w-100 mt-2"
                    data-bs-toggle="modal"
                    data-bs-target="#orderModal{{ $order->id }}">
                    👁 Детали
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

                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title">Заказ #{{ $order->id }}</h6>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="p-2 bg-light rounded mb-2">
                        <b>Дата:</b> {{ $order->created_at->format('d.m.Y H:i') }}
                    </div>

                    <div class="p-2 bg-light rounded mb-2">
                        <b>Имя:</b> {{ $order->name }}
                    </div>

                    <div class="p-2 bg-light rounded mb-2">
                        <b>Телефон:</b> {{ $order->phone }}
                    </div>

                    @if($order->comment)
                    <div class="p-2 bg-light rounded mb-2">
                        <b>Комментарий:</b> {{ $order->comment }}
                    </div>
                    @endif

                    <hr>

                    @foreach($order->items as $item)

                    <div class="border rounded p-2 mb-2">
                        <div class="fw-bold">{{ $item->variant->sku ?? '—' }}</div>
                        <div>Кол-во: {{ $item->quantity }}</div>
                        <div class="text-muted small">{{ $item->warehouse_name }}</div>
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

                if (q.length < 1) {
                    container.innerHTML = original;
                    return;
                }

                fetch(`/admin/orders/search?q=${q}`)
                    .then(r => r.json())
                    .then(data => {

                        container.innerHTML = '';

                        data.forEach(o => {

                            container.innerHTML += `
<div class="card mb-2 shadow-sm border-0 rounded-4">
  <div class="card-body p-3">
    <div class="d-flex justify-content-between">
      <strong>#${o.id}</strong>
      <small>${o.created_at ? o.created_at.replace('T', ' ').slice(0,16) : ''}</small>
    </div>

    <div class="fw-semibold mt-2">${o.name}</div>
    <div class="text-muted small">${o.phone}</div>

    <div class="d-flex gap-2 mt-3">

      <a href="/admin/orders/${o.id}/edit" class="btn btn-warning btn-sm w-50">✏️ Ред</a>

      <form action="/admin/orders/${o.id}" method="POST" class="w-50">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger btn-sm w-100">🗑 Удал</button>
      </form>

    </div>

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

    .card {
        border-radius: 14px;
    }

    .btn {
        border-radius: 12px;
    }
</style>

@endsection