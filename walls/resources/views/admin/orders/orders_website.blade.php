@extends('layouts.app') 

@section('title', 'Заказы с сайта')

@section('content')
<div class="container py-5">
    <h1 class="fw-semibold mb-4">Заказы с сайта</h1>

    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3">{{ session('success') }}</div>
    @endif

    @if($orders->isEmpty())
        <p class="text-muted">Нет заказов с сайта.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-sm rounded-4 overflow-hidden bg-white">
                <thead class="table-light">
                    <tr class="text-nowrap">
                        <th>ID</th>
                        <th>Клиент</th>
                        <th>Телефон</th>
                        <th>Комментарий</th>
                        <th>Статус</th>
                        <th>Товары</th>
                        <th>Создан</th>
                        <th class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="table-info">
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ $order->comment ?? '—' }}</td>

                            <td style="min-width: 140px">
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm rounded-3 shadow-none border-0 bg-light" onchange="this.form.submit()">
                                        @foreach(['Новый', 'Подтвержден', 'Завершен', 'Отменен'] as $status)
                                            <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>

                            <td>
                                <ul class="list-unstyled mb-0">
                                    @foreach($order->items as $item)
                                        <li class="d-flex align-items-start gap-3 mb-3 flex-wrap">
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" width="60" height="60" class="rounded-2 object-fit-cover shadow-sm border">
                                            @endif
                                            <div class="small lh-sm" style="min-width: 180px">
                                                <div class="fw-semibold mb-1">{{ $item->variant->product->name ?? '—' }}</div>
                                                <div class="text-muted">Артикул: {{ $item->variant->sku ?? '—' }}</div>
                                                <div class="text-muted">Кол-во: {{ $item->quantity }}</div>
                                                <div class="text-muted">Цена: {{ number_format($item->price, 0, ',', ' ') }} ₸</div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Удалить заказ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection