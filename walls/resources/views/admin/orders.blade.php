@extends('layouts.main') 

@section('title', 'Заказы')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-semibold text-body-emphasis mb-0">Список заказов</h1>
        @if($orders->isNotEmpty())
            <form action="{{ route('admin.orders.clear') }}" method="POST" onsubmit="return confirm('Удалить все заказы?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger rounded-pill px-4">
                    <i class="bi bi-trash3"></i> Удалить все
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3">{{ session('success') }}</div>
    @endif

    @if($orders->isEmpty())
        <p class="text-muted">Нет заказов.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-sm rounded-4 overflow-hidden bg-white">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">ID</th>
                        <th class="text-nowrap">Клиент</th>
                        <th class="text-nowrap">Телефон</th>
                        <th class="text-nowrap">Комментарий</th>
                        <th class="text-nowrap">Статус</th>
                        <th class="text-nowrap">Товары</th>
                        <th class="text-nowrap">Создан</th>
                        <th class="text-nowrap text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="text-muted">{{ $order->id }}</td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td class="text-muted">{{ $order->comment ?? '—' }}</td>
                            <td>
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
                                        <li class="d-flex align-items-start gap-3 mb-3">
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}"
                                                     alt="Изображение"
                                                     width="60"
                                                     height="60"
                                                     class="rounded-2 object-fit-cover shadow-sm border">
                                            @endif
                                            <div class="small lh-sm">
                                                <div class="fw-semibold mb-1">{{ $item->variant->product->name ?? '—' }}</div>
                                                <div class="text-muted">Артикул: <span class="text-body">{{ $item->variant->sku ?? '—' }}</span></div>
                                                <div class="text-muted">Кол-во: <span class="text-body">{{ $item->quantity }}</span></div>
                                                <div class="text-muted">Цена: <span class="text-body">{{ number_format($item->price, 0, ',', ' ') }} ₸</span></div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</td>
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
