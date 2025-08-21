@extends('layouts.app')

@section('title', 'Остатки на складе: ' . $warehouse->name)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-semibold mb-1">{{ $warehouse->name }}</h2>
            <p class="text-muted small mb-0">Обзор всех партий по артикулам</p>
        </div>
    </div>

    {{-- Фильтры --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end" id="filterForm">
                <div class="col-md-5">
                    <label for="autocomplete-sku" class="form-label small text-muted">Артикул</label>
                    <input type="text" name="search" id="autocomplete-sku" class="form-control"
                        placeholder="Например: 11199-01"
                        value="{{ request('search') }}" autocomplete="off">
                </div>

                <div class="col-md-3">
                    <label for="sort" class="form-label small text-muted">Сортировка</label>
                    <select name="sort" id="sort" class="form-select"
                        onchange="document.getElementById('filterForm').submit()">
                        <option value="">Без сортировки</option>
                        <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>По возрастанию</option>
                        <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>По убыванию</option>
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-dark">Найти</button>
                </div>

                <div class="col-md-2 d-grid">
                    <a href="{{ route('admin.stocks.batch_overview', ['id' => $warehouse->id]) }}"
                        class="btn btn-outline-secondary">Сброс</a>
                </div>
            </form>
        </div>
    </div>

    @if($grouped->isEmpty())
    <div class="alert alert-light border text-center py-4">
        Нет данных для отображения
    </div>
    @else
    <div class="accordion" id="variantAccordion">
        @foreach($grouped as $variantId => $variantBatches)
        @php
        $variant = $variantBatches->first()->variant;
        $total = 0;
        $hasLowBatch = false;

        foreach ($variantBatches as $batch) {
        $pivot = $batch->warehouses->firstWhere('id', $warehouse->id)?->pivot;
        if ($pivot) {
        $total += $pivot->quantity;
        if ($pivot->quantity <= 5) {
            $hasLowBatch=true;
            }
            }
            }
            $isLowStock=$total <=5;
            @endphp

            <div class="accordion-item border-0 mb-2 shadow-sm rounded">
            <h2 class="accordion-header" id="heading{{ $variantId }}">
                <button class="accordion-button collapsed {{ ($isLowStock || $hasLowBatch) ? 'bg-light border-start border-4 border-danger' : '' }}"
                    type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse{{ $variantId }}" aria-expanded="false"
                    aria-controls="collapse{{ $variantId }}">
                    <div class="d-flex flex-column">
                        <span class="fw-semibold">
                            {{ $variant->sku }}
                            @if($isLowStock)
                            <span class="badge bg-danger ms-2">Малый остаток</span>
                            @elseif($hasLowBatch)
                            <span class="badge bg-warning text-dark ms-2">Есть партии с малым остатком</span>
                            @endif
                        </span>
                        <small class="text-muted">{{ $variant->product->name ?? 'Без названия' }}</small>
                    </div>
                </button>
            </h2>
            <div id="collapse{{ $variantId }}" class="accordion-collapse collapse"
                aria-labelledby="heading{{ $variantId }}" data-bs-parent="#variantAccordion">
                <div class="accordion-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Партия</th>
                                <th class="text-end">Количество</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($variantBatches as $batch)
                            @php
                            $pivot = $batch->warehouses->firstWhere('id', $warehouse->id)?->pivot;
                            @endphp
                            @if($pivot)
                            <tr class="{{ $pivot->quantity <= 6 ? 'table-warning' : '' }}">
                                <td class="ps-3">
                                    {{ $batch->batch_code }}
                                    @if($pivot->quantity <= 5)
                                        <span class="badge bg-danger ms-2">Мало</span>
                                        @endif
                                </td>
                                <td class="text-end pe-3 fw-semibold">{{ $pivot->quantity }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th class="text-end pe-2">Итого:</th>
                                <th class="text-end pe-3 fw-bold">{{ $total }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
    </div>
    @endforeach
</div>

<div class="mt-4">
    {{ $grouped->links('vendor.pagination.custom') }}
</div>
@endif
</div>

{{-- jQuery Autocomplete --}}
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(function() {
        $("#autocomplete-sku").autocomplete({
            source: "{{ route('admin.variants.autocomplete') }}",
            minLength: 2
        });
    });
</script>
@endsection