@extends('layouts.app')

@section('title', 'Остатки товаров по складам')

@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<style>
    body {
        background: #f9f9f9;
        font-size: 0.9rem;
        color: #222;
    }

    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
    }

    .card-header {
        background: transparent;
        border-bottom: none;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
    }

    .search-bar {
        background: #fff;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .form-control,
    .form-select {
        font-size: 0.85rem;
    }

    table {
        font-size: 0.85rem;
    }

    th {
        color: #666;
        font-weight: 500;
    }

    td {
        font-weight: 500;
    }

    .accordion-button {
        font-size: 0.85rem;
        padding: 0.6rem 1rem;
    }

    .total {
        font-size: 0.9rem;
        font-weight: 700;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.5rem;
        }

        .search-bar form {
            flex-direction: column;
            gap: 0.75rem;
        }

        .search-bar .col-12 {
            width: 100%;
        }

        table {
            font-size: 0.8rem;
        }

        .accordion-button {
            font-size: 0.8rem;
        }

        .btn,
        .form-select,
        .form-control {
            width: 100%;
        }
    }
</style>

<div class="container py-3">

    <div class="search-bar">
        <form method="GET" action="{{ route('admin.stocks.view_all') }}" class="row g-2 align-items-center">
            <div class="col-md-6 d-flex flex-wrap gap-2">
                <input type="text" name="sku" id="sku-autocomplete"
                    class="form-control form-control-md flex-grow-1"
                    placeholder="Поиск по артикулу..." value="{{ request('sku') }}">
                <button type="submit" class="btn btn-dark">Найти</button>
                @if(request('sku') || request('sort'))
                <a href="{{ route('admin.stocks.view_all') }}" class="btn btn-outline-secondary">Сбросить</a>
                @endif
            </div>

            <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2 mt-2 mt-md-0">
                <label for="sort" class="text-muted small mb-0">Сортировка:</label>
                <select name="sort" id="sort" class="form-select form-select-md w-auto"
                    onchange="this.form.submit()">
                    <option value="">По умолчанию</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>По возрастанию</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>По убыванию</option>
                </select>
            </div>
        </form>
    </div>

    @php
    $colors = ['#0d6efd', '#198754', '#dc3545', '#fd7e14', '#20c997', '#6f42c1'];
    @endphp

    @foreach ($variants as $variant)
    @php
    $totalStock = $variant->batches->sum(fn($batch) => $batch->warehouses->sum('pivot.quantity'));
    @endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                {{ $variant->sku }} — {{ $variant->product->name }}
                @if($totalStock > 0 && $totalStock <= 5)
                    <span class="badge bg-danger ms-2">Мало товара</span>
                @endif
            </div>
            <div class="total">Всего: {{ $totalStock }}</div>
        </div>
        <div class="card-body pt-0">
            <h6 class="fw-bold mt-2 mb-1">По складам</h6>
            <table class="table table-sm table-borderless">
                <tbody>
                    @foreach ($warehouses as $i => $warehouse)
                    @php
                        $stock = 0;
                        foreach ($variant->batches as $batch) {
                            $stock += $batch->warehouses->where('id', $warehouse->id)->sum('pivot.quantity');
                        }
                        $hasBatches = $variant->batches->filter(fn($batch) =>
                            $batch->warehouses->contains('id', $warehouse->id)
                        )->isNotEmpty();
                    @endphp
                    <tr>
                        <td style="color: {{ $colors[$i % count($colors)] }};">
                            {{ $warehouse->name }}
                            @if($hasBatches && $stock > 0 && $stock <= 5)
                                <span class="badge bg-warning text-dark ms-1">Мало на складе</span>
                            @endif
                        </td>
                        <td class="text-end">{{ $stock }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Accordion for batches -->
            <div class="accordion mt-2" id="accordion{{ $variant->id }}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $variant->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $variant->id }}">
                            <span>Партии товара</span>
                            @php
                                $hasLowStockBatch = $variant->batches->some(fn($batch) =>
                                    $batch->warehouses->sum('pivot.quantity') > 0 &&
                                    $batch->warehouses->sum('pivot.quantity') <= 5
                                );
                            @endphp
                            @if($hasLowStockBatch)
                                <span class="badge bg-danger ms-2">Есть партии с малым остатком</span>
                            @endif
                        </button>
                    </h2>
                    <div id="collapse{{ $variant->id }}" class="accordion-collapse collapse">
                        <div class="accordion-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Партия</th>
                                        <th>Склады</th>
                                        <th class="text-end">Итого</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($variant->batches as $batch)
                                    @php
                                        $batchTotal = $batch->warehouses->sum('pivot.quantity');
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $batch->batch_code }}
                                            @if($batchTotal > 0 && $batchTotal <= 5)
                                                <span class="badge bg-info text-dark ms-1">Малый остаток</span>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($batch->warehouses as $j => $warehouse)
                                            <div style="font-size: 0.8rem;">
                                                <span style="color: {{ $colors[$j % count($colors)] }};">
                                                    {{ $warehouse->name }}
                                                </span>:
                                                <strong>{{ $warehouse->pivot->quantity }}</strong>
                                                @if($warehouse->pivot->quantity > 0 && $warehouse->pivot->quantity <= 5)
                                                    <span class="badge bg-warning text-dark ms-1">Мало</span>
                                                @endif
                                            </div>
                                            @endforeach
                                        </td>
                                        <td class="text-end fw-bold">{{ $batchTotal }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endforeach

    <!-- Пагинация -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $variants->links('vendor.pagination.custom') }}
    </div>

</div>

<script>
    $(function() {
        $('#sku-autocomplete').autocomplete({
            source: "{{ route('admin.variants.autocomplete') }}",
            minLength: 1
        });
    });
</script>
@endsection
