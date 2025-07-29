<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Учёт продаж</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light py-4">
    <div class="container">
        <h1 class="mb-4">Учёт продаж</h1>

        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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

        {{-- 🔽 Форма добавления --}}
        <form method="POST" action="{{ route('admin.sales.store') }}" class="mb-4">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Дата:</label>
                    <input name="sale_date" type="date" class="form-control" value="{{ $currentDate->format('Y-m-d') }}" required>
                </div>
                {{-- SKU + выбор партии --}}
                <div class="col-md-2">
                    <label class="form-label">Артикул:</label>
                    <input name="sku" id="skuInput" type="text" class="form-control" placeholder="Артикул" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Партия:</label>
                    <select name="batch_id" id="batchSelect" class="form-select" required>
                        <option value="">Выберите</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Цена:</label>
                    <input name="price" type="number" step="0.01" class="form-control" placeholder="Цена" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Кол-во:</label>
                    <input name="quantity" type="number" class="form-control" placeholder="Кол-во (можно -)" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Оплата:</label>
                    <select name="payment_method" class="form-select" required>
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

        {{-- 📊 Таблица продаж --}}
        @php $sales = $salesByDate[$currentDate->format('Y-m-d')] ?? collect(); @endphp

        <div class="card mb-3">
            <div class="card-header"><strong>{{ $currentDate->format('d.m.Y') }}</strong></div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Артикул</th>
                            <th class="text-end">Цена</th>
                            <th class="text-end">Кол-во</th>
                            <th class="text-end">Сумма</th>
                            <th>Оплата</th>
                            <th></th>
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
                                <td>
                                    <form method="POST" action="{{ route('admin.sales.destroy', $sale->id) }}"
                                        onsubmit="return confirm('Удалить запись?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Удалить</button>
                                    </form>
                                </td>
                        </tr>
                        @endforeach

                        {{-- Итого за день --}}
                        <tr class="table-secondary fw-bold">
                            <td colspan="3">Итого</td>
                            <td class="text-end">{{ number_format($sales->sum('total'), 2) }}</td>
                            <td colspan="2"></td>
                        </tr>

                        {{-- Продажи по способу оплаты --}}
                        <tr class="table-light">
                            <td colspan="6">
                                <strong>Продажи по способу оплаты:</strong>
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

                        {{-- Возвраты --}}
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

    {{-- Autocomplete для артикула --}}
    <script>
        $(function() {
            $('#skuInput').autocomplete({
                source: '{{ route("admin.variants.autocomplete") }}',
                minLength: 1,
                select: function(event, ui) {
                    const sku = ui.item.value;

                    // Загружаем партии
                    $.ajax({
                        url: '/admin/batches/by-sku/' + sku,
                        success: function(data) {
                            const $select = $('#batchSelect');
                            $select.empty().append(`<option value="">Выберите</option>`);
                            data.forEach(batch => {
                                $select.append(`<option value="${batch.id}">Партия ${batch.batch_code ?? '—'} (${batch.stock} шт)</option>`);
                            });
                        }
                    });
                }
            });
        });
    </script>


    {{-- jQuery UI (для autocomplete) --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</body>

</html>