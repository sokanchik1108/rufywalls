@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<style>
    body {
        background: #fdfdfd;
        font-size: 0.9rem;
        color: #222;
    }

    h1 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    h5 {
        font-size: 0.95rem;
        font-weight: 500;
    }

    .form-label {
        font-size: 0.75rem;
        color: #666;
    }

    .form-control,
    .form-select {
        font-size: 16px !important;
        padding: 0.35rem 0.55rem;
        border-radius: 4px;
        border-color: #ddd;
    }

    .btn {
        font-size: 0.85rem;
        border-radius: 4px;
    }

    .btn-primary {
        background: #000;
        border: none;
    }

    .btn-outline-secondary {
        border-color: #ccc;
        color: #333;
    }

    .btn-cross {
        padding: 0.1rem 0.45rem;
        font-size: 0.9rem;
        line-height: 1;
    }

    .table {
        border-color: #e9ecef;
    }

    .table th {
        font-weight: 500;
        background: #fafafa;
        color: #555;
    }

    .table-sm td,
    .table-sm th {
        padding: 0.4rem;
    }

    .alert {
        font-size: 0.8rem;
        border-radius: 4px;
        padding: 0.4rem 0.8rem;
    }

    @media (max-width: 576px) {
        h1 {
            font-size: 1rem;
        }

        .form-control,
        .form-select {
            font-size: 0.8rem;
        }

        .table-responsive {
            font-size: 0.8rem;
        }
    }
</style>

<div class="container">

    @php
    $currentWarehouseId = $selectedWarehouseId ?? null;
    $currentDate = \Carbon\Carbon::parse($selectedDate ?? now());
    $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
    $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');
    @endphp

    <h1>
        Изменение остатков
        @if ($currentWarehouseId)
        @php $currentWarehouse = $warehouses->firstWhere('id', $currentWarehouseId); @endphp
        <span class="text-muted fw-normal">({{ $currentWarehouse->name ?? 'Неизвестный склад' }})</span>
        @endif
    </h1>

    @if ($currentWarehouseId)
    <div class="mb-3">
        <a href="{{ route('admin.sales.select_warehouse') }}" class="btn btn-sm btn-outline-secondary">
            ← Вернуться к выбору склада
        </a>
    </div>
    @endif

    {{-- Фильтр по дате --}}
    <form method="GET" class="mb-3 d-flex align-items-center gap-2 flex-wrap" id="dateFilterForm">
        <input type="date" name="date" class="form-control w-auto" value="{{ request('date', $currentDate->format('Y-m-d')) }}">
        <input type="hidden" name="warehouse_id" value="{{ $currentWarehouseId }}">
    </form>

    <script>
        const dateInput = document.querySelector('input[name="date"]');
        const form = document.getElementById('dateFilterForm');
        dateInput.addEventListener('change', () => {
            form.submit();
        });
    </script>

    {{-- Навигация по дням --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <a href="?date={{ $prevDate }}&warehouse_id={{ $currentWarehouseId }}" class="btn btn-outline-secondary btn-sm mb-2">&laquo; {{ $prevDate }}</a>
        <h5 class="mb-2">{{ $currentDate->format('d.m.Y') }}</h5>
        <a href="?date={{ $nextDate }}&warehouse_id={{ $currentWarehouseId }}" class="btn btn-outline-secondary btn-sm mb-2">{{ $nextDate }} &raquo;</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success small">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger small">{{ session('error') }}</div>
    @endif

    {{-- Форма добавления продажи --}}
    <form method="POST" action="{{ route('admin.sales.store') }}" class="mb-4">
        @csrf
        <input type="hidden" name="warehouse_id" value="{{ $currentWarehouseId }}">
        <div class="row g-2">
            <div class="col-6 col-sm-4 col-md-2">
                <input name="sale_date" type="date" class="form-control" value="{{ $currentDate->format('Y-m-d') }}" required>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <input name="sku" id="skuInput" type="text" class="form-control" placeholder="Артикул" required autocomplete="off">
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <select name="batch_id" id="batchSelect" class="form-select" required>
                    <option value="">Партия</option>
                </select>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <input name="quantity" type="number" class="form-control" placeholder="Кол-во" required>
            </div>
            <div class="col-12 col-sm-4 col-md-2">
                <button class="btn btn-primary w-100">Сохранить</button>
            </div>
        </div>
    </form>

    @php
    $sales = $salesByDate[$currentDate->format('Y-m-d')] ?? collect();
    if ($currentWarehouseId) {
    $sales = $sales->filter(fn($sale) => $sale->warehouse_id == $currentWarehouseId);
    }
    @endphp

    {{-- Таблица продаж --}}
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Артикул</th>
                    <th class="text-end">Кол-во</th>
                    <th class="text-center">✕</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr @if($sale->quantity < 0) class="table-danger" @endif>
                        <td>{{ $sale->sku }} (Партия {{ $sale->batch->batch_code ?? '—' }})</td>
                        <td class="text-end">{{ $sale->quantity }}</td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('admin.sales.destroy', $sale->id) }}" onsubmit="return confirm('Удалить запись?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger btn-cross" title="Удалить">×</button>
                            </form>
                        </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Записей нет</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mb-4 mt-5">
        <h5>История продаж для возврата</h5>

        <div class="row g-2">
            <div class="col-12 col-sm-6 col-md-4 position-relative">
                <input type="text" id="returnSkuInput" class="form-control pe-5 w-100"
                    placeholder="Артикул для возврата" autocomplete="off">
                <button type="button" id="clearReturnInput"
                    class="clear-btn position-absolute top-50 end-0 translate-middle-y"
                    style="display:none;">×</button>
            </div>
        </div>

        <div class="table-responsive mt-2" id="returnHistoryTableContainer" style="display:none;">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Партия</th>
                        <th class="text-end">Кол-во</th>
                        <th>Склад</th>
                    </tr>
                </thead>
                <tbody id="returnHistoryTableBody"></tbody>
            </table>
        </div>
    </div>

    <style>
        .clear-btn {
            border: none;
            background: transparent;
            font-size: 1.4rem;
            line-height: 1;
            color: #888;
            cursor: pointer;
            padding: 0 0.75rem;
            transition: color 0.2s ease;
            z-index: 2;
        }

        .clear-btn:hover {
            color: #333;
        }

        /* Мобильная адаптация */
        @media (max-width: 576px) {
            #returnSkuInput {
                font-size: 1rem;
                padding-right: 2.2rem;
            }

            .clear-btn {
                font-size: 1.5rem;
                padding: 0 1.2rem;
                right: 0.25rem;
            }

            .table {
                font-size: 0.9rem;
            }

            h5 {
                font-size: 1rem;
            }
        }
    </style>



</div>

<script>
    const currentWarehouseId = @json($currentWarehouseId);

    // Автокомплит для добавления продажи
    $(function() {
        $('#skuInput').autocomplete({
            source: '{{ route("admin.variants.autocomplete") }}',
            minLength: 1,
            select: function(event, ui) {
                const sku = ui.item.value;
                if (!currentWarehouseId) {
                    alert('Склад не выбран.');
                    return;
                }

                $.ajax({
                    url: '/admin/batches/by-sku/' + encodeURIComponent(sku),
                    success: function(data) {
                        const $select = $('#batchSelect');
                        $select.empty().append(`<option value="">Партия</option>`);
                        data.forEach(batch => {
                            if (!batch.warehouses || !Array.isArray(batch.warehouses)) return;
                            const warehouse = batch.warehouses.find(w => w.id == currentWarehouseId);
                            if (warehouse && warehouse.pivot && warehouse.pivot.quantity !== undefined) {
                                const qty = warehouse.pivot.quantity;
                                $select.append(`<option value="${batch.id}">Партия ${batch.batch_code ?? '—'} (${qty} шт)</option>`);
                            }
                        });
                    }
                });
            }
        });
    });

    // Автокомплит + крестик для возврата
    $(function() {
        const $input = $('#returnSkuInput');
        const $clearBtn = $('#clearReturnInput');
        const $tableContainer = $('#returnHistoryTableContainer');
        const $tbody = $('#returnHistoryTableBody');

        // автокомплит
        $input.autocomplete({
            source: '{{ route("admin.variants.autocomplete") }}',
            minLength: 1,
            select: function(event, ui) {
                const sku = ui.item.value;
                $.ajax({
                    url: '/admin/sales/history/' + encodeURIComponent(sku),
                    success: function(data) {
                        $tbody.empty();

                        if (data.length === 0) {
                            $tbody.append('<tr><td colspan="4" class="text-center text-muted">Нет продаж</td></tr>');
                        } else {
                            data.forEach(sale => {
                                $tbody.append(`
                                    <tr>
                                        <td>${sale.sale_date}</td>
                                        <td>${sale.batch_code ?? '—'}</td>
                                        <td class="text-end">${sale.quantity}</td>
                                        <td>${sale.warehouse_name}</td>
                                    </tr>
                                `);
                            });
                        }

                        $tableContainer.show();
                    }
                });
            }
        });

        // показываем крестик при вводе
        $input.on('input', function() {
            const hasText = $(this).val().trim() !== '';
            $clearBtn.toggle(hasText);
            if (!hasText) {
                $tableContainer.hide();
                $tbody.empty();
            }
        });

        // очистка при клике на крестик
        $clearBtn.on('click', function() {
            $input.val('').focus();
            $clearBtn.hide();
            $tableContainer.hide();
            $tbody.empty();
        });
    });
</script>




@endsection