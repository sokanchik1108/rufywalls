@extends('layouts.app')

@section('title', 'Заказы продавцов')

@section('content')
@php
$selectedDate = request()->get('date') ?? now()->format('Y-m-d');
$prevDate = \Carbon\Carbon::parse($selectedDate)->subDay()->format('Y-m-d');
$nextDate = \Carbon\Carbon::parse($selectedDate)->addDay()->format('Y-m-d');
@endphp

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-semibold">Заказы продавцов</h1>
        <div class="d-flex gap-2">
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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.orders.seller', ['date' => $prevDate]) }}" class="btn btn-outline-secondary">&larr; Назад</a>
        <span class="fw-semibold">{{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}</span>
        <a href="{{ route('admin.orders.seller', ['date' => $nextDate]) }}" class="btn btn-outline-secondary">Вперед &rarr;</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success shadow-sm rounded-3">{{ session('success') }}</div>
    @endif

    @if($orders->isEmpty())
    <p class="text-muted">Нет заказов за выбранный день.</p>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm rounded-4 overflow-hidden bg-white">
            <thead class="table-light">
                <tr class="text-nowrap">
                    <th>№ заказа</th>
                    <th>Клиент</th>
                    <th>Телефон</th>
                    <th>Создал</th>
                    <th>Общая сумма</th>
                    <th>Статус</th>
                    <th class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->name }}</td>
                    <td>{{ $order->phone }}</td>
                    <td>{{ $order->user->name ?? '—' }}</td>
                    <td>{{ number_format($order->items->sum(fn($i)=>$i->price*$i->quantity), 0, ',', ' ') }} ₸</td>
                    <td>{{ $order->status }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                            Детали
                        </button>
                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Удалить заказ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Удалить
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Модалки --}}
    @foreach($orders as $order)
    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Заказ #{{ $order->id }} — {{ $order->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Телефон:</strong> {{ $order->phone }}</p>
                    <p><strong>Создал:</strong> {{ $order->user->name ?? '—' }}</p>
                    <p><strong>Комментарий:</strong> {{ $order->comment ?? '—' }}</p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Товар</th>
                                    <th>Артикул</th>
                                    <th>Партия</th>
                                    <th>Склад</th>
                                    <th>Кол-во</th>
                                    <th>Цена</th>
                                    <th>Сумма</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->variant->product->name ?? '—' }}</td>
                                    <td>{{ $item->variant->sku ?? '—' }}</td>
                                    {{-- берём snapshot поля, а не связи --}}
                                    <td>{{ $item->batch_code ?? '—' }}</td>
                                    <td>{{ $item->warehouse_name ?? '—' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price,0,',',' ') }} ₸</td>
                                    <td>{{ number_format($item->price * $item->quantity,0,',',' ') }} ₸</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-semibold">
                                    <td colspan="6" class="text-end">Итого:</td>
                                    <td>{{ number_format($order->items->sum(fn($i)=>$i->price*$i->quantity),0,',',' ') }} ₸</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @endif

</div>

<style>
    tr:hover {
        background-color: #f1f5f9;
    }

    .btn-outline-secondary {
        min-width: 100px;
    }
</style>
@endsection