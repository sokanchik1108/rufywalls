@extends('layouts.app')

@section('title', 'Склад: ' . $warehouse->name)

@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<style>
    .container-custom {
        padding-left: 50px;
        padding-right: 50px;
    }

    .card {
        border-radius: 0.75rem;
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
    }

    .existing-batch {
        transition: background-color 0.2s ease;
    }

    .existing-batch:hover {
        background-color: #d1e7dd !important;
    }

    .new-batch input {
        border-radius: 0;
    }

    .btn-outline-dark {
        border-radius: 0;
    }
</style>

<div class="container-fluid py-4" style="padding-left: 50px; padding-right: 50px;">

    <h5 class="mb-3">Склад: {{ $warehouse->name }}</h5>

    @if(session('success'))
    <div class="alert alert-success small">{{ session('success') }}</div>
    @endif

    @if(session('error_list'))
    <div class="alert alert-danger small">
        <ul class="mb-0 ps-3">
            @foreach(session('error_list') as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Поиск --}}
    <form method="GET" id="search-form" class="mb-4">
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" name="sku" id="sku-autocomplete"
                placeholder="Поиск по артикулу" value="{{ request('sku') }}">
            <button class="btn btn-outline-secondary" type="submit">Поиск</button>
        </div>
    </form>

    {{-- Основная форма --}}
    <form method="POST" action="{{ route('admin.stocks.update_warehouse', $warehouse->id) }}">
        @csrf
        <div class="row g-4">
            @forelse($variants as $variant)
            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2">

                <div class="card h-100 bg-light shadow-sm">
                    <div class="card-body px-3 py-4">
                        <div class="mb-3">
                            <strong class="small text-dark" style="font-size:15px;">{{ $variant->sku }}</strong><br>
                            <span class="text-muted small">{{ $variant->product->name ?? 'Без названия' }}</span>
                        </div>

                        {{-- Существующие партии --}}
                        @foreach($variant->batches as $batch)
                        @php
                        $pivot = $batch->warehouses->firstWhere('id', $warehouse->id)?->pivot;
                        @endphp
                        @if($pivot)
                        <div class="mb-2 p-2 border-start border-success bg-success-subtle rounded position-relative existing-batch" data-code="{{ $batch->batch_code }}">
                            <label class="form-label form-label-sm d-block small text-success">
                                Партия: {{ $batch->batch_code }}
                            </label>
                            <input type="number" name="batch[{{ $batch->id }}]" class="form-control form-control-sm" value="{{ $pivot->quantity }}">
                            <button type="button" class="btn-close btn-sm position-absolute end-0 top-0 m-2"
                                onclick="deleteBatchFromWarehouse({{ $batch->id }}, {{ $warehouse->id }}, this)">
                            </button>
                        </div>
                        @endif
                        @endforeach

                        {{-- Добавление новой партии --}}
                        <div class="mt-3 p-2 border-start border-warning bg-warning-subtle rounded new-batch">
                            <label class="form-label form-label-sm small text-warning">Добавить партию</label>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" class="form-control form-control-sm new-batch-code" placeholder="Код">
                                <input type="number" class="form-control form-control-sm new-batch-quantity" placeholder="Кол-во">
                                <button type="button" class="btn btn-outline-dark"
                                    onclick="addBatchToWarehouse({{ $variant->id }}, {{ $warehouse->id }}, this)">
                                    ➕
                                </button>
                            </div>
                        </div>

                        {{-- Общее количество --}}
                        @php
                        $totalQuantity = 0;
                        foreach ($variant->batches as $batch) {
                        $warehouseData = $batch->warehouses->firstWhere('id', $warehouse->id);
                        if ($warehouseData && $warehouseData->pivot) {
                        $totalQuantity += $warehouseData->pivot->quantity;
                        }
                        }
                        @endphp

                        <div class="medium text-muted mt-2 total-quantity" data-variant-id="{{ $variant->id }}">
                            <strong>На складе:</strong> <span class="quantity-value">{{ $totalQuantity }}</span> шт.
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-warning small">Ничего не найдено.</div>
            </div>
            @endforelse
        </div>
    </form>

    {{-- Пагинация --}}
    @if($variants instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-4">
        {{ $variants->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>

{{-- Скрипты --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(function() {
        $("#sku-autocomplete").autocomplete({
            source: "{{ route('admin.variants.autocomplete') }}",
            minLength: 2,
            select: function(event, ui) {
                $('#sku-autocomplete').val(ui.item.value);
                $('#search-form').submit();
            }
        });

        if ("{{ request('sku') }}") {
            let resetBtn = $('<button type="button" class="btn btn-outline-danger">✕</button>');
            resetBtn.click(() => {
                $('#sku-autocomplete').val('');
                $('#search-form').submit();
            });
            $('#search-form .input-group').append(resetBtn);
        }

        $('input[name^="batch["]').on('change', function() {
            const $input = $(this);
            const batchId = $input.attr('name').match(/\d+/)[0];
            const quantity = $input.val();

            $.post("{{ route('admin.batches.update') }}", {
                _token: "{{ csrf_token() }}",
                batch_id: batchId,
                    warehouse_id: {{ $warehouse->id }},
                quantity: quantity
            }).done(() => {
                if ($input.next('.update-success').length === 0) {
                    $input.after('<span class="update-success text-success small ms-2">Остатки обновлены</span>');
                }
                setTimeout(() => {
                    $input.next('.update-success').fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 1500);
                updateTotalQuantity($input.closest('.card-body'));
            }).fail(err => {
                alert('Ошибка при обновлении количества');
                console.error(err);
            });
        });
    });

    function updateTotalQuantity($card) {
        let sum = 0;
        $card.find('input[name^="batch["]').each(function() {
            const val = parseInt($(this).val());
            if (!isNaN(val)) sum += val;
        });
        $card.find('.quantity-value').text(sum);
    }

    function addBatchToWarehouse(variantId, warehouseId, btn) {
        const $card = $(btn).closest('.card-body');
        const code = $card.find('.new-batch-code').val().trim();
        const quantity = $card.find('.new-batch-quantity').val();

        if (!code || quantity <= 0) {
            alert('Укажите корректный код и количество.');
            return;
        }

        const exists = $card.find(`.existing-batch[data-code="${code}"]`).length > 0;
        if (exists) {
            alert('Партия с таким кодом уже существует на складе.');
            return;
        }

        $.post("{{ route('admin.batches.add') }}", {
            _token: "{{ csrf_token() }}",
            variant_id: variantId,
            warehouse_id: warehouseId,
            code: code,
            quantity: quantity
        }).done((data) => {
            $card.find('.new-batch-code').val('');
            $card.find('.new-batch-quantity').val('');

            const html = `
            <div class="mb-2 p-2 border-start border-success bg-success-subtle rounded position-relative existing-batch" data-code="${data.code}">
                <label class="form-label form-label-sm d-block small text-success">
                    Партия: ${data.code}
                </label>
                <input type="number" name="batch[${data.batch_id}]" class="form-control form-control-sm" value="${data.quantity}">
                <button type="button" class="btn-close btn-sm position-absolute end-0 top-0 m-2"
                    onclick="deleteBatchFromWarehouse(${data.batch_id}, ${warehouseId}, this)">
                </button>
            </div>`;
            $card.find('.new-batch-code').closest('.mt-3').before(html);
            updateTotalQuantity($card);
        }).fail(err => {
            alert('Ошибка при добавлении партии');
            console.error(err);
        });
    }

    function deleteBatchFromWarehouse(batchId, warehouseId, btn) {
        if (!confirm('Удалить эту партию со склада?')) return;

        const $card = $(btn).closest('.card-body');

        $.post("{{ route('admin.batches.remove') }}", {
            _token: "{{ csrf_token() }}",
            batch_id: batchId,
            warehouse_id: warehouseId
        }).done(() => {
            $(btn).closest('.mb-2').remove();
            updateTotalQuantity($card);
        }).fail(err => {
            alert('Ошибка при удалении партии');
            console.error(err);
        });
    }
</script>
@endsection