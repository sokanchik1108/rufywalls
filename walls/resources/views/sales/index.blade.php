<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Учёт продаж</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-size: 0.9rem;
            background: #f9f9f9;
        }

        h1 {
            font-size: 1.4rem;
        }

        .form-label {
            font-size: 0.8rem;
        }

        .form-control,
        .form-select {
            font-size: 0.85rem;
            padding: 0.4rem 0.6rem;
        }

        .btn {
            font-size: 0.85rem;
            padding: 0.35rem 0.9rem;
        }

        th,
        td {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .btn-cross {
            padding: 0.2rem 0.5rem;
            font-size: 1rem;
            line-height: 1;
        }

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.03);
        }

        .table-sm td, .table-sm th {
            padding: 0.45rem;
        }

        ul {
            padding-left: 1rem;
        }

        @media (max-width: 576px) {
            h1 {
                font-size: 1.2rem;
            }
            .table-responsive {
                font-size: 0.82rem;
            }
            .form-control,
            .form-select {
                font-size: 0.82rem;
            }
        }
    </style>
</head>

<body class="py-4">
    <div class="container">
        <h1 class="mb-3">Учёт продаж</h1>

        @if (session('success'))
            <div class="alert alert-success py-2 px-3 small">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2 px-3 small">{{ session('error') }}</div>
        @endif

        @php
            $currentDate = \Carbon\Carbon::parse($selectedDate ?? now());
            $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
            $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <a href="?date={{ $prevDate }}" class="btn btn-outline-secondary btn-sm mb-2">&laquo; {{ $prevDate }}</a>
            <h5 class="mb-2">{{ $currentDate->format('d.m.Y') }}</h5>
            <a href="?date={{ $nextDate }}" class="btn btn-outline-secondary btn-sm mb-2">{{ $nextDate }} &raquo;</a>
        </div>

        {{-- Форма --}}
        <form method="POST" action="{{ route('admin.sales.store') }}" class="mb-4">
            @csrf
            <div class="row g-2">
                <div class="col-6 col-sm-4 col-md-2">
                    <input name="sale_date" type="date" class="form-control" value="{{ $currentDate->format('Y-m-d') }}" required>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <input name="sku" id="skuInput" type="text" class="form-control" placeholder="Артикул" required>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <select name="batch_id" id="batchSelect" class="form-select" required>
                        <option value="">Партия</option>
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <input name="price" type="number" step="0.01" class="form-control" placeholder="Цена" required>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <input name="quantity" type="number" class="form-control" placeholder="Кол-во" required>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <select name="payment_method" class="form-select" required>
                        <option value="нал">Нал</option>
                        <option value="перевод">Перевод</option>
                        <option value="qr">QR</option>
                        <option value="halyk">Halyk</option>
                    </select>
                </div>
                <div class="col-12 col-sm-4 col-md-2">
                    <button class="btn btn-primary w-100">Добавить</button>
                </div>
            </div>
        </form>

        {{-- Таблица --}}
        @php $sales = $salesByDate[$currentDate->format('Y-m-d')] ?? collect(); @endphp

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Артикул</th>
                                <th class="text-end">Цена</th>
                                <th class="text-end">Кол-во</th>
                                <th class="text-end">Сумма</th>
                                <th>Оплата</th>
                                <th class="text-center">✕</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                            <tr @if ($sale->quantity < 0) class="table-danger" @endif>
                                <td>{{ $sale->sku }}</td>
                                <td class="text-end">{{ number_format($sale->price, 2) }}</td>
                                <td class="text-end">{{ $sale->quantity }}</td>
                                <td class="text-end">{{ number_format($sale->total, 2) }}</td>
                                <td>{{ ucfirst($sale->payment_method) }}</td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('admin.sales.destroy', $sale->id) }}" onsubmit="return confirm('Удалить запись?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger btn-cross" title="Удалить">×</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach

                            {{-- Итого --}}
                            <tr class="table-secondary fw-bold">
                                <td colspan="3">Итого</td>
                                <td class="text-end">{{ number_format($sales->sum('total'), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>

                            {{-- Оплата --}}
                            <tr class="table-light">
                                <td colspan="6">
                                    <strong>Продажи по способу оплаты:</strong>
                                    <ul class="mb-0">
                                        @foreach (['нал', 'перевод', 'qr', 'halyk'] as $method)
                                            @php $positive = $sales->where('payment_method', $method)->where('quantity', '>', 0)->sum('total'); @endphp
                                            <li>{{ ucfirst($method) }}: {{ number_format($positive, 2) }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>

                            {{-- Возвраты --}}
                            <tr class="table-warning">
                                <td colspan="6">
                                    <strong>Возвраты:</strong>
                                    <ul class="mb-0">
                                        @foreach (['нал', 'перевод', 'qr', 'halyk'] as $method)
                                            @php $negative = $sales->where('payment_method', $method)->where('quantity', '<', 0)->sum('total'); @endphp
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
    </div>

    {{-- Автозаполнение партий --}}
    <script>
        $(function () {
            $('#skuInput').autocomplete({
                source: '{{ route("admin.variants.autocomplete") }}',
                minLength: 1,
                select: function (event, ui) {
                    const sku = ui.item.value;
                    $.ajax({
                        url: '/admin/batches/by-sku/' + sku,
                        success: function (data) {
                            const $select = $('#batchSelect');
                            $select.empty().append(`<option value="">Партия</option>`);
                            data.forEach(batch => {
                                $select.append(`<option value="${batch.id}">Партия ${batch.batch_code ?? '—'} (${batch.stock} шт)</option>`);
                            });
                        }
                    });
                }
            });
        });
    </script>

    {{-- jQuery UI --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</body>

</html>
