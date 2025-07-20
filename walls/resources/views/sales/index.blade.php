<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Учёт продаж</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-4">
    <div class="container">
        <h1 class="mb-4">Учёт продаж</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- 🔄 Переключение дней --}}
        @php
            $currentDate = \Carbon\Carbon::parse($selectedDate ?? now());
            $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
            $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="?date={{ $prevDate }}" class="btn btn-outline-secondary">&laquo; {{ $prevDate }}</a>
            <h4 class="mb-0">{{ $currentDate->format('d.m.Y') }}</h4>
            <a href="?date={{ $nextDate }}" class="btn btn-outline-secondary">{{ $nextDate }} &raquo;</a>
        </div>

        {{-- 📌 Форма добавления продажи --}}
        <form method="POST" action="{{ route('admin.sales.store') }}" class="mb-4">
            @csrf
            <div class="row g-2">
                <div class="col-md-2">
                    <input name="sale_date" type="date" class="form-control" value="{{ $currentDate->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <input name="sku" type="text" class="form-control" placeholder="Артикул" required>
                </div>
                <div class="col-md-2">
                    <input name="price" type="number" step="0.01" class="form-control" placeholder="Цена" required>
                </div>
                <div class="col-md-2">
                    <input name="quantity" type="number" class="form-control" placeholder="Кол-во (можно -)" required>
                </div>
                <div class="col-md-2">
                    <select name="payment_method" class="form-control" required>
                        <option value="нал">Нал</option>
                        <option value="перевод">Перевод</option>
                        <option value="qr">QR</option>
                        <option value="halyk">Halyk</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Добавить</button>
                </div>
            </div>
        </form>

        {{-- 📊 Таблица продаж за выбранный день --}}
        @php $sales = $salesByDate[$currentDate->format('Y-m-d')] ?? collect(); @endphp

        <div class="card mb-3">
            <div class="card-header">
                <strong>{{ $currentDate->format('d.m.Y') }}</strong>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Артикул</th>
                            <th>Цена</th>
                            <th>Кол-во</th>
                            <th>Сумма</th>
                            <th>Оплата</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr @if ($sale->quantity < 0) class="table-danger" @endif>
                                <td>{{ $sale->sku }}</td>
                                <td>{{ number_format($sale->price, 2) }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td>{{ number_format($sale->total, 2) }}</td>
                                <td>{{ $sale->payment_method }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.sales.destroy', $sale->id) }}"
                                          onsubmit="return confirm('Удалить запись?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        {{-- 📌 Итог за день --}}
                        <tr class="table-secondary fw-bold">
                            <td colspan="3">Итого</td>
                            <td>{{ number_format($sales->sum('total'), 2) }}</td>
                            <td colspan="2"></td>
                        </tr>

                        {{-- 📌 Продажи по оплате --}}
                        <tr class="table-light">
                            <td colspan="6">
                                <strong>Продажи по оплате:</strong>
                                <ul class="mb-0">
                                    @foreach (['нал', 'перевод', 'qr', 'halyk'] as $method)
                                        @php
                                            $positive = $sales->where('payment_method', $method)->where('quantity', '>', 0)->sum('total');
                                        @endphp
                                        <li>{{ ucfirst($method) }}: {{ number_format($positive, 2) }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>

                        {{-- 📌 Возвраты по оплате --}}
                        <tr class="table-warning">
                            <td colspan="6">
                                <strong>Возвраты:</strong>
                                <ul class="mb-0">
                                    @foreach (['нал', 'перевод', 'qr', 'halyk'] as $method)
                                        @php
                                            $negative = $sales->where('payment_method', $method)->where('quantity', '<', 0)->sum('total');
                                        @endphp
                                        @if ($negative)
                                            <li>{{ ucfirst($method) }}: <span class="text-danger">−{{ number_format(abs($negative), 2) }}</span></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
