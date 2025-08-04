@extends('layouts.app')

@section('title', 'Остатки товаров по складам')

@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<div class="container-fluid py-3" style="padding-left: 50px; padding-right: 50px;">


    <h5 class="text-center mb-3">Остатки товаров по складам</h5>


    <form method="GET" action="{{ route('admin.stocks.view_all') }}" class="row g-2" style="margin-bottom: 80px;">
        <div class="col-12 col-md-6 d-flex gap-2">
            <input type="text" name="sku" id="sku-autocomplete" placeholder="Поиск по артикулу..." class="form-control form-control-sm" value="{{ request('sku') }}">
            <button type="submit" class="btn btn-sm btn-primary">Найти</button>
            @if(request('sku') || request('sort'))
            <a href="{{ route('admin.stocks.view_all') }}" class="btn btn-sm btn-outline-secondary">Сбросить</a>
            @endif
        </div>
        <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center">
            <label for="sort" class="me-2 mb-0 small">Сортировка:</label>
            <select name="sort" id="sort" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="">По умолчанию</option>
                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>По возрастанию</option>
                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>По убыванию</option>
            </select>
        </div>
    </form>

    @if($variants->isEmpty())
    <div class="alert alert-warning text-center small">Ничего не найдено.</div>
    @else
    <div class="row g-3">
        @foreach($variants as $variant)
        @php
        $product = $variant->product;

        $colorClasses = [
        'color-warehouse-1', 'color-warehouse-2', 'color-warehouse-3',
        'color-warehouse-4', 'color-warehouse-5', 'color-warehouse-6',
        'color-warehouse-7', 'color-warehouse-8'
        ];

        $warehouseColorMap = [];
        foreach ($warehouses as $index => $warehouse) {
        $warehouseColorMap[$warehouse->id] = $colorClasses[$index % count($colorClasses)];
        }

        $warehousesByBatch = [];
        foreach ($variant->batches as $batch) {
        foreach ($warehouses as $warehouse) {
        $matched = $batch->warehouses->firstWhere('id', $warehouse->id);
        $quantity = $matched ? $matched->pivot->quantity : 0;
        $warehousesByBatch[$batch->id][$warehouse->id] = [
        'name' => $warehouse->name,
        'quantity' => $quantity,
        'color_class' => $warehouseColorMap[$warehouse->id],
        ];
        }
        }

        $stockByWarehouse = [];
        foreach ($warehouses as $warehouse) {
        $stockByWarehouse[$warehouse->id] = [
        'name' => $warehouse->name,
        'quantity' => $variant->stock_per_warehouse[$warehouse->id]['quantity'] ?? 0,
        'color_class' => $warehouseColorMap[$warehouse->id],
        ];
        }
        @endphp

        <div class="col-12 col-md-4 col-lg-3">
            <div class="card border h-100 shadow-sm">
                <div class="card-body p-2 small">
                    <div class="fw-bold mb-1">
                        {{ $product->name }}
                        <div class="text-muted">Артикул: <strong style="font-size: 1.0rem;">{{ $variant->sku }}</strong></div>
                    </div>

                    <div class="mb-2" style="font-size: 1.0rem;"><strong>Общий остаток:</strong> {{ $variant->stock_balance }} шт.</div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 1.0rem;">По складам:</div>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach($stockByWarehouse as $wh)
                            <span class="badge {{ $wh['color_class'] }} fw-semibold" style="font-size: 0.90rem;">
                                {{ $wh['name'] }}: {{ $wh['quantity'] }}
                            </span>
                            @endforeach
                        </div>
                    </div>



                    <div>
                        <div class="fw-semibold mb-1" style="font-size: 0.9rem;">Партии:</div>
                        @foreach($variant->batches as $batch)
                        <div class="border rounded px-2 py-1 mb-1">
                            <div class="mb-1">
                                <strong style="font-size: 0.85rem;">Партия:</strong>
                                <span style="font-size: 0.85rem;">{{ $batch->batch_code }}</span>
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($warehousesByBatch[$batch->id] as $wh)
                                @if($wh['quantity'] > 0)
                                <span class="badge {{ $wh['color_class'] }} fw-semibold" style="font-size: 0.85rem;">
                                    {{ $wh['name'] }}: {{ $wh['quantity'] }}
                                </span>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>


                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $variants->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>

<style>
    .color-warehouse-1 {
        background-color: #2C2C2C;
        color: #fff;
    }

    .color-warehouse-2 {
        background-color: #3E4C59;
        color: #fff;
    }

    .color-warehouse-3 {
        background-color: green;
        color: #fff;
    }

    .color-warehouse-4 {
        background-color: red;
        color: #fff;
    }

    .color-warehouse-5 {
        background-color: purple;
        color: #fff;
    }

    .color-warehouse-6 {
        background-color: #1F2937;
        color: #fff;
    }

    .color-warehouse-7 {
        background-color: #52525B;
        color: #fff;
    }

    .color-warehouse-8 {
        background-color: #1E1E1E;
        color: #fff;
    }

    .badge {
        font-size: 11px;
        padding: 4px 7px;
        font-weight: 500;
        border-radius: 4px;
        display: inline-block;
    }

    .card-title {
        font-size: 15px;
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 0.75rem 0.5rem;
        }

        .badge {
            font-size: 10px;
            padding: 3px 6px;
        }
    }
</style>

<script>
    $(function() {
        $('#sku-autocomplete').autocomplete({
            source: "{{ route('admin.variants.autocomplete') }}",
            minLength: 1
        });
    });
</script>
@endsection