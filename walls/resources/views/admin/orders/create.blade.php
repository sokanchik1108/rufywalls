@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* --- оставляем весь CSS как у тебя --- */
    body {
        background: #f4f6fb;
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        padding: 20px;
    }

    #orderControls {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 16px;
    }

    #cancelOrder {
        color: #ef4444;
        cursor: pointer;
    }

    #finishOrder,
    #addBatchToOrder,
    #openProductModal {
        background: #3b82f6;
        color: #fff;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.2s;
    }

    #finishOrder:hover,
    #addBatchToOrder:hover,
    #openProductModal:hover {
        background: #2563eb;
    }

    #openProductModal {
        width: 100%;
        margin-top: 10px;
        padding: 12px;
    }

    .positions-header {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        margin: 20px 0 10px;
    }

    .order-item {
        background: #fff;
        padding: 12px;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        position: relative;
        border-radius: 10px;
    }

    .order-item-top {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        align-items: center;
    }

    .warehouse {
        color: #4f46e5;
        margin-right: 10px;
        font-weight: 500;
    }

    .order-item-bottom {
        margin-top: 6px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .order-item-bottom span,
    .available-total,
    .batch-warehouses,
    td:nth-child(2),
    td:nth-child(3) {
        font-weight: 600;
    }

    .price-input {
        border: none;
        border-bottom: 1px solid #ef4444;
        width: 80px;
        background: transparent;
    }

    .item-total {
        font-weight: 600;
        margin-left: 6px;
    }

    .remove-item {
        position: absolute;
        right: 10px;
        top: 6px;
        border: none;
        background: none;
        font-size: 20px;
        color: #ef4444;
        cursor: pointer;
    }

    input.form-control,
    select.form-select {
        border: none;
        border-bottom: 1px solid #ccc;
        padding: 6px 0;
        margin-bottom: 16px;
        background: transparent;
        border-radius: 0;
    }

    input:focus,
    select:focus {
        outline: none;
        border-bottom: 1px solid #4f46e5;
    }

    select.form-select {
        color: #4f46e5;
        font-weight: 600;
    }

    .modal-fullscreen {
        max-width: 100%;
        width: 100%;
        height: 100%;
        margin: 0;
    }

    .modal-fullscreen .modal-content {
        height: 100%;
        border-radius: 0;
        padding: 16px;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .modal-fullscreen .modal-header {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 8px;
    }

    .modal-fullscreen .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .modal-body {
        flex: 1;
        overflow-y: auto;
        margin-top: 8px;
    }

    .batch-card {
        background: #f9f9fb;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 12px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 16px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        transition: all 0.2s;
    }

    .batch-card:hover {
        background: #f3f4f6;
        transform: translateY(-2px);
    }

    .qty-input {
        width: 80px;
        font-size: 16px;
        padding: 6px 8px;
        border-radius: 10px;
        border: 1px solid #ddd;
        font-weight: 600;
    }

    .available-total {
        font-size: 14px;
        color: #666;
        margin-top: 4px;
        font-weight: 600;
    }

    #orderTotal {
        font-weight: 700;
        font-size: 18px;
        margin-top: 12px;
        text-align: right;
        color: #2563eb;
    }
</style>

<div class="container">
    <form method="POST" action="{{ route('admin.orders.store') }}" id="orderForm">
        @csrf

        <div id="orderControls">
            <span id="cancelOrder">ОТМЕНА</span>
            <button type="submit" id="finishOrder">ГОТОВО</button>
        </div>

        <input type="text" name="name" class="form-control" placeholder="Имя клиента" required>
        <input type="text" name="phone" class="form-control" placeholder="Телефон клиента" required>
        <input type="text" name="comment" class="form-control" placeholder="Комментарий">

        <select id="warehouseSelect" class="form-select">
            <option value="">Все склады ></option>
            @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
            @endforeach
        </select>

        <div class="positions-header">
            <span>Позиции</span>
            <span id="positionsCount">(0)</span>
        </div>

        <div id="itemsWrapper"></div>

        <div id="orderTotal">0 тг</div>

        <button type="button" id="openProductModal">добавить позицию</button>
    </form>
</div>

{{-- Модалки --}}
<div class="modal fade modal-fullscreen" id="productModal">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Выберите партию</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="modalSkuInput" class="form-control mb-2" placeholder="Введите SKU...">
                <div id="modalBatchesSimple"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-fullscreen" id="batchDetailModal">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Выберите количество</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm" id="batchDetailTable">
                    <thead>
                        <tr>
                            <th>Склад</th>
                            <th>Доступно</th>
                            <th>Кол-во</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button class="btn w-100" id="addBatchToOrder">Добавить в заказ</button>
            </div>
        </div>
    </div>
</div>

<script>
    let itemIndex = 0,
        currentBatch = {},
        batchWarehouses = [],
        batchesState = {};

    function resetProductModal() {
        $('#modalSkuInput').val('');
        $('#modalBatchesSimple').empty();
    }

    function resetBatchDetailModal() {
        $('#batchDetailTable tbody').empty();
        currentBatch = {};
        batchWarehouses = [];
    }

    $('#openProductModal').click(() => bootstrap.Modal.getOrCreateInstance(document.getElementById('productModal')).show());
    $('#productModal').on('show.bs.modal hidden.bs.modal', resetProductModal);
    $('#batchDetailModal').on('hidden.bs.modal', resetBatchDetailModal);

    // Autocomplete SKU
    $('#modalSkuInput').autocomplete({
        source: '{{ route("admin.variants.autocomplete") }}',
        minLength: 1,
        appendTo: '#productModal',
        select: function(e, ui) {
            loadBatchesSimple(ui.item.value);
        }
    });

    function loadBatchesSimple(sku) {
        const selectedWarehouse = $('#warehouseSelect').val();
        $.get('/admin/batches/by-sku/' + sku, function(data) {
            let $list = $('#modalBatchesSimple').empty();
            data.forEach(batch => {
                if (!batchesState[batch.id]) {
                    batchesState[batch.id] = {};
                    batch.warehouses.forEach(w => batchesState[batch.id][w.id] = w.pivot.quantity);
                }
                let filtered = batch.warehouses;
                if (selectedWarehouse) filtered = filtered.filter(w => w.id == selectedWarehouse);
                const totalQty = filtered.reduce((sum, w) => sum + batchesState[batch.id][w.id], 0);
                if (totalQty <= 0) return;
                $list.append(`<div class="batch-card batch-select" data-batch='${JSON.stringify(batch)}' data-sku='${sku}'>
                <div>${sku} (Партия ${batch.batch_code})</div>
                <div class="batch-warehouses">Доступно: ${totalQty} шт</div>
            </div>`);
            });
        });
    }

    $(document).on('click', '.batch-select', function() {
        currentBatch = JSON.parse($(this).attr('data-batch'));
        currentBatch.sku = $(this).attr('data-sku');
        let selectedWarehouse = $('#warehouseSelect').val();
        batchWarehouses = currentBatch.warehouses;
        if (selectedWarehouse) batchWarehouses = batchWarehouses.filter(w => w.id == selectedWarehouse);
        const $tbody = $('#batchDetailTable tbody').empty();
        batchWarehouses.forEach(w => {
            const available = batchesState[currentBatch.id][w.id] || 0;
            if (available <= 0) return;
            $tbody.append(`<tr>
            <td>${w.name}</td>
            <td>${available} шт</td>
            <td><input type="number" class="form-control qty-input" data-warehouse="${w.id}" max="${available}" min="1" placeholder="Кол-во"></td>
        </tr>`);
        });
        bootstrap.Modal.getOrCreateInstance(document.getElementById('productModal')).hide();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('batchDetailModal')).show();
    });

    // Добавление партии в заказ с snapshot
    $('#addBatchToOrder').click(function() {
        let hasError = false;
        $('#batchDetailTable tbody tr').each(function() {
            const $input = $(this).find('.qty-input');
            let qty = parseInt($input.val());
            if (!qty || qty <= 0) {
                alert('Введите количество!');
                hasError = true;
                return false;
            }
            const warehouseId = $input.data('warehouse');
            let available = batchesState[currentBatch.id][warehouseId] || 0;
            if (qty > available) {
                alert(`Недостаточно товара на складе ${warehouseId}.`);
                hasError = true;
                return false;
            }

            const warehouseName = currentBatch.warehouses.find(w => w.id == warehouseId).name;

            $('#itemsWrapper').append(`
        <div class="order-item" data-batch-id="${currentBatch.id}" data-warehouse-id="${warehouseId}">
            <span class="warehouse">Склад: ${warehouseName}</span>
            <button type="button" class="remove-item">&times;</button>
            <div class="order-item-top"><span>${currentBatch.sku} (Партия ${currentBatch.batch_code})</span></div>
            <div class="order-item-bottom">
                <span>Кол-во: ${qty} шт</span>
                <span>Цена: <input type="number" name="items[${itemIndex}][price]" class="price-input" step="0.01" placeholder="Цена" required></span>
                <span>Сумма: <span class="item-total">0 тг</span></span>
            </div>
            <input type="hidden" name="items[${itemIndex}][sku]" value="${currentBatch.variant_sku ?? currentBatch.sku}">
            <input type="hidden" name="items[${itemIndex}][batch_id]" value="${currentBatch.id}">
            <input type="hidden" name="items[${itemIndex}][warehouse_id]" value="${warehouseId}">
            <input type="hidden" name="items[${itemIndex}][quantity]" value="${qty}">
            <input type="hidden" name="items[${itemIndex}][batch_code]" value="${currentBatch.batch_code}">
            <input type="hidden" name="items[${itemIndex}][warehouse_name]" value="${warehouseName}">
        </div>
        `);
            itemIndex++;
            batchesState[currentBatch.id][warehouseId] -= qty;
        });
        if (!hasError) bootstrap.Modal.getOrCreateInstance(document.getElementById('batchDetailModal')).hide();
        setTimeout(recalcOrderTotal, 100);
    });

    // Удаление позиции
    $(document).on('click', '.remove-item', function() {
        const $card = $(this).closest('.order-item');
        const batchId = $card.data('batch-id');
        const warehouseId = $card.data('warehouse-id');
        const qty = parseInt($card.find('input[name$="[quantity]"]').val());
        if (batchesState[batchId] && batchesState[batchId][warehouseId] !== undefined) batchesState[batchId][warehouseId] += qty;
        $card.remove();
        recalcOrderTotal();
        $('#positionsCount').text('(' + $('.order-item').length + ')');
    });

    function recalcItemTotal($item) {
        const price = parseFloat($item.find('.price-input').val()) || 0;
        const qty = parseInt($item.find('input[name$="[quantity]"]').val()) || 0;
        const total = price * qty;
        $item.find('.item-total').text(total.toFixed(2) + ' тг');
        return total;
    }

    function recalcOrderTotal() {
        let sum = 0;
        $('.order-item').each(function() {
            sum += recalcItemTotal($(this));
        });
        $('#orderTotal').text(sum.toFixed(2) + ' тг');
    }

    $(document).on('input', '.price-input', function() {
        recalcOrderTotal();
    });

    $('#orderForm').submit(function(e) {
        let invalid = false;
        $('.price-input:visible').each(function() {
            if (!$(this).val() || parseFloat($(this).val()) <= 0) {
                invalid = true;
                $(this).addClass('is-invalid');
            } else $(this).removeClass('is-invalid');
        });
        if (invalid) {
            alert('Пожалуйста, укажите цену для всех товаров перед созданием заказа.');
            e.preventDefault();
        }
    });

    $('#cancelOrder').click(() => window.history.back());
</script>

@endsection