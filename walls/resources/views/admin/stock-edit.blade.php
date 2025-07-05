@extends('layouts.app')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<style>
    html,
    body {
        overflow-x: hidden;
    }

    .container-fluid {
        max-width: 100%;
        overflow-x: hidden;
        padding-left: 12px;
        padding-right: 12px;
    }

    .card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        padding: 0.8rem;
        border-radius: 10px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
        font-size: 13px;
    }

    .card h5 {
        font-size: 14px;
        margin-bottom: 0.4rem;
    }

    .card p {
        margin-bottom: 0.3rem;
    }

    .variant-alert {
        min-height: 1.3em;
        font-size: 12.5px;
        color: #198754;
        transition: opacity 0.3s ease;
    }

    .batch-input {
        max-width: 55px;
        font-size: 12.5px;
        padding: 3px 6px;
        height: auto;
    }

    .batch-label {
        font-size: 12.5px;
    }

    /* Поиск */
    .search-form-container {
        position: relative;
        max-width: 420px;
        margin: 0 auto 2rem;
    }

    .search-form-container input {
        padding: 10px 38px 10px 14px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 14px;
        width: 100%;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .search-form-container input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    .search-form-container #clearSearch {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        font-size: 16px;
        color: #6b7280;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    .search-form-container #clearSearch:hover {
        color: #111827;
    }

    /* Autocomplete */
    .ui-autocomplete {
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 6px 0;
        font-size: 14px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.2s ease-in-out;
        z-index: 9999;
    }

    .ui-menu-item {
        padding: 8px 16px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .ui-menu-item:hover {
        background-color: #f3f4f6;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container-fluid px-2 my-4">
    <h2 class="text-center mb-3">Обновление остатков по партиям</h2>

    <!-- Поиск -->
    <form method="GET" action="{{ route('admin.stock.edit') }}" class="search-form-container">
        <input
            type="text"
            name="sku"
            id="skuInput"
            value="{{ request('sku') }}"
            placeholder="Поиск по артикулу...">

        <select name="sort" class="form-select mt-2" onchange="this.form.submit()">
            <option value="">Сортировать по умолчанию</option>
            <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>По возрастанию количества</option>
            <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>По убыванию количества</option>
        </select>

        @if(request('sku'))
        <button type="button" id="clearSearch" title="Очистить поиск">✕</button>
        @endif
    </form>


    <!-- Карточки -->
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 justify-content-center g-3">
        @foreach($variants as $variant)
        @php $product = $variant->product; $variantId = $variant->id; @endphp
        <div class="col">
            <div class="card h-100 d-flex flex-column justify-content-between">
                <div>
                    <h5>Артикул: {{ $variant->sku }}</h5>
                    <p>Оттенок: {{ $variant->color }}</p>
                    <p>Название: {{ $product->name }}</p>

                    <hr class="my-2">
                    <p class="fw-bold mb-2">Партии:</p>

                    @foreach($variant->batches as $batch)
                    <div class="d-flex justify-content-between align-items-center mb-2 batch-row" data-batch-id="{{ $batch->id }}">
                        <span class="batch-label">Партия {{ $batch->batch_code ?? '—' }}</span>
                        <div class="d-flex align-items-center">
                            <input
                                type="number"
                                class="form-control form-control-sm batch-input me-1"
                                data-batch-id="{{ $batch->id }}"
                                data-variant-id="{{ $variantId }}"
                                value="{{ $batch->stock }}">
                            <button class="btn btn-sm btn-outline-danger delete-batch-btn" data-batch-id="{{ $batch->id }}">
                                ✕
                            </button>
                        </div>
                    </div>
                    @endforeach

                    <!-- Форма добавления новой партии -->
                    <form class="add-batch-form mt-2" data-variant-id="{{ $variantId }}">
                        @csrf
                        <div class="d-flex align-items-center" style="margin-top: 10px;">
                            <input type="text" name="batch_code" placeholder="Код партии" class="form-control form-control-sm me-2" style="max-width: 100px;">
                            <input type="number" name="stock" placeholder="Кол-во" class="form-control form-control-sm me-2" style="max-width: 80px;">
                            <button type="submit" class="btn btn-sm btn-success">+</button>
                        </div>
                    </form>


                    <p class="fw-semibold mt-2 mb-0">
                        Общий остаток:
                        <span class="total-stock" id="total-{{ $variantId }}">
                            {{ $variant->total_stock ?? 0 }}
                        </span> шт.
                    </p>
                </div>
                <div class="variant-alert mt-2" id="alert-{{ $variantId }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $variants->links('vendor.pagination.custom') }}
    </div>
</div>

<script>
    $(document).ready(function() {
        // ✅ Обновление остатков
        $(document).on('input', '.batch-input', function() {
            const input = $(this);
            const batchId = input.data('batch-id');
            const variantId = input.data('variant-id');
            const newStock = parseInt(input.val()) || 0;

            $.ajax({
                url: '{{ route("admin.stock.update") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    stocks: {
                        [batchId]: newStock
                    }
                },
                success: function(response) {
                    if (response.success) {
                        updateTotal(variantId);
                        showAlert(response.message, variantId);
                    }
                },
                error: function() {
                    alert('Ошибка при сохранении');
                }
            });
        });

        // ✅ Удаление партии
        $(document).on('click', '.delete-batch-btn', function() {
            const batchId = $(this).data('batch-id');
            const row = $(`.batch-row[data-batch-id="${batchId}"]`);
            const variantId = row.find('.batch-input').data('variant-id');

            if (!confirm('Удалить эту партию?')) return;

            $.ajax({
                url: `/admin/batches/${batchId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        row.remove();
                        updateTotal(variantId);
                        showAlert('Партия удалена', variantId);
                    }
                },
                error: function() {
                    alert('Ошибка при удалении партии');
                }
            });
        });

        // ✅ Добавление новой партии
        $(document).on('submit', '.add-batch-form', function(e) {
            e.preventDefault();

            const form = $(this);
            const variantId = form.data('variant-id');
            const batchCode = form.find('input[name="batch_code"]').val();
            const stock = parseInt(form.find('input[name="stock"]').val()) || 0;

            if (stock < 0) return alert('Количество должно быть положительным');

            $.ajax({
                url: '{{ route("admin.batches.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    variant_id: variantId,
                    batch_code: batchCode,
                    stock: stock
                },
                success: function(response) {
                    if (response.success && response.batch) {
                        const html = `
                            <div class="d-flex justify-content-between align-items-center mb-2 batch-row" data-batch-id="${response.batch.id}">
                                <span class="batch-label">Партия ${response.batch.batch_code ?? '—'}</span>
                                <div class="d-flex align-items-center">
                                    <input
                                        type="number"
                                        class="form-control form-control-sm batch-input me-1"
                                        data-batch-id="${response.batch.id}"
                                        data-variant-id="${variantId}"
                                        value="${response.batch.stock}">
                                    <button class="btn btn-sm btn-outline-danger delete-batch-btn" data-batch-id="${response.batch.id}">✕</button>
                                </div>
                            </div>
                        `;
                        form.before(html); // добавляем перед формой
                        form.find('input').val('');
                        updateTotal(variantId);
                        showAlert('Партия добавлена', variantId);
                    }
                },
                error: function() {
                    alert('Ошибка при добавлении партии');
                }
            });
        });

        // ✅ Подсказка артикулов
        $('#skuInput').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '{{ route("admin.variants.autocomplete") }}',
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            delay: 100
        });

        // ✅ Очистка поиска
        $('#clearSearch').on('click', function() {
            window.location.href = '{{ route("admin.stock.edit") }}';
        });

        // ✅ Подсчет общего остатка
        function updateTotal(variantId) {
            let total = 0;
            $(`.batch-input[data-variant-id="${variantId}"]`).each(function() {
                total += parseInt($(this).val()) || 0;
            });
            $(`#total-${variantId}`).text(total);
        }

        // ✅ Уведомление
        function showAlert(message, variantId) {
            const box = $(`#alert-${variantId}`);
            box.text(message).css('opacity', 1);
            setTimeout(() => {
                box.fadeOut(300, () => {
                    box.text('').show().css('opacity', 1);
                });
            }, 2000);
        }
    });
</script>


@endsection