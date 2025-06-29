@extends('layouts.app') 

@section('title', 'Заказы')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
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
                        <tr>
                            <td class="text-muted">{{ $order->id }}</td>
                            <td style="min-width: 120px">{{ $order->name }}</td>
                            <td style="min-width: 130px">{{ $order->phone }}</td>
                            <td class="text-muted" style="min-width: 150px">{{ $order->comment ?? '—' }}</td>
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
                            <td style="min-width: 260px">
                                <ul class="list-unstyled mb-0">
                                    @foreach($order->items as $item)
                                        <li class="d-flex align-items-start gap-3 mb-3 flex-wrap">
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}"
                                                     alt="Изображение"
                                                     width="60"
                                                     height="60"
                                                     class="rounded-2 object-fit-cover shadow-sm border">
                                            @endif
                                            <div class="small lh-sm" style="min-width: 180px">
                                                <div class="fw-semibold mb-1">{{ $item->variant->product->name ?? '—' }}</div>
                                                <div class="text-muted">Артикул: <span class="text-body">{{ $item->variant->sku ?? '—' }}</span></div>
                                                <div class="text-muted">Кол-во: <span class="text-body">{{ $item->quantity }}</span></div>
                                                <div class="text-muted">Цена: <span class="text-body">{{ number_format($item->price, 0, ',', ' ') }} ₸</span></div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-muted text-nowrap">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td class="text-center text-nowrap">
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

<style>
    /* Общие стили */
    .table th, .table td {
        vertical-align: middle;
    }

    /* На экранах до 768px */
    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 0.8rem;
            padding: 0.5rem;
            white-space: nowrap;
        }

        .table td img {
            width: 50px;
            height: 50px;
        }

        .btn-sm, .form-select-sm {
            font-size: 0.75rem;
        }

        h1.fw-semibold {
            font-size: 1.25rem;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table td .lh-sm {
            min-width: 180px;
        }
    }

    /* На экранах до 576px */
    @media (max-width: 576px) {
        .table th, .table td {
            font-size: 0.75rem;
            padding: 0.4rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        .form-select-sm {
            padding: 0.2rem 0.4rem;
        }

        .table td .lh-sm {
            min-width: 160px;
        }

        .table td img {
            width: 45px;
            height: 45px;
        }

        h1.fw-semibold {
            font-size: 1.1rem;
        }
    }
</style>

@endsection
