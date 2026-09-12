@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --bg: #f4f5f7;
        --surface: #ffffff;
        --ink: #1a1d21;
        --ink-soft: #6e7580;
        --ink-faint: #9aa0a8;
        --border: #dfe3e8;
        --border-strong: #c9ced6;
        --primary: #01142f;
        --primary-hover: #02214b;
        --primary-soft: #eaf1fe;
        --danger: #e5484d;
        --danger-soft: #fdeceb;
        --radius: 6px;
        --radius-lg: 8px;
    }

    * {
        box-sizing: border-box
    }

    body {
        background: var(--bg);
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        color: var(--ink);
        margin: 0;
        padding: 0;
        font-size: 14px;
        -webkit-font-smoothing: antialiased
    }

    .container {
        padding: 0 0 40px;
        max-width: 600px;
        margin: 0 auto
    }

    /* ---------- Top bar ---------- */
    #orderControls {
        position: sticky;
        margin-top: -18px;
        margin-bottom: 10px;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
    }

    #cancelOrder {
        color: var(--ink-soft);
        font-weight: 500;
        font-size: 13.5px;
        cursor: pointer;
        padding: 7px 10px;
        border-radius: var(--radius);
        transition: background .12s, color .12s
    }

    #cancelOrder:hover {
        background: var(--bg);
        color: var(--ink)
    }

    #finishOrder {
        background: var(--primary);
        color: #fff;
        border: none;
        font-weight: 600;
        font-size: 13.5px;
        cursor: pointer;
        border-radius: var(--radius);
        padding: 8px 18px;
        transition: background .12s
    }

    #finishOrder:hover {
        background: var(--primary-hover)
    }

    .page-title {
        padding: 18px 20px 16px;
        font-size: 17px;
        font-weight: 700;
        letter-spacing: -.005em
    }

    /* ---------- Section panels ---------- */
    .panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        margin: 0 16px 14px;
        padding: 14px 16px 16px;
    }

    .section-label {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--ink-soft);
        margin: 0 0 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }

    .section-label .count {
        font-weight: 500;
        color: var(--ink-faint);
    }

    /* ---------- Fields ---------- */
    .field {
        margin-bottom: 12px
    }

    .field:last-child {
        margin-bottom: 0
    }

    .field-row {
        display: flex;
        gap: 10px
    }

    .field-row .field {
        flex: 1;
        min-width: 0
    }

    label.field-label {
        display: block;
        font-size: 12px;
        color: var(--ink-soft);
        margin-bottom: 5px;
        font-weight: 500;
    }

    select.form-select {
        padding: 6px 10px;
    }

    input.form-control,
    select.form-select {
        display: block;
        width: 100%;
        height: 40px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0 10px;
        margin-bottom: 0;
        background: var(--surface);
        font-family: inherit;
        font-size: 16px;
        line-height: 38px;
        color: var(--ink);
        box-sizing: border-box;
        transition: border-color .12s, box-shadow .12s;
        appearance: none;
        -webkit-appearance: none;
    }

    input.form-control::placeholder {
        color: var(--ink-faint)
    }

    select.form-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='9' viewBox='0 0 14 9' fill='none'%3E%3Cpath d='M1 1L7 7L13 1' stroke='%236e7580' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
    }

    input:focus,
    select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-soft);
    }

    /* ---------- Date / datetime input reset ---------- */

    input[type="datetime-local"],
    input[type="date"] {
        -webkit-appearance: none;
        appearance: none;

        height: 40px;
        line-height: 38px;

        color: var(--ink);
        background: var(--surface);
    }

    input[type="datetime-local"]::-webkit-date-and-time-value,
    input[type="date"]::-webkit-date-and-time-value {
        text-align: left;
        margin: 0;
        padding: 0;
    }

    input[type="datetime-local"]::-webkit-datetime-edit,
    input[type="date"]::-webkit-datetime-edit {
        padding: 0;
    }

    input[type="datetime-local"]::-webkit-datetime-edit-fields-wrapper,
    input[type="date"]::-webkit-datetime-edit-fields-wrapper {
        padding: 0;
    }

    input[type="datetime-local"]::-webkit-calendar-picker-indicator,
    input[type="date"]::-webkit-calendar-picker-indicator {
        margin-left: 6px;
        padding: 0;
        width: 16px;
        height: 16px;
        opacity: .6;
        cursor: pointer;
    }

    input[type="datetime-local"]::-webkit-inner-spin-button,
    input[type="date"]::-webkit-inner-spin-button {
        display: none;
    }

    /* ---------- Positions ---------- */
    #itemsWrapper:empty {
        display: block;
        text-align: center;
        color: var(--ink-faint);
        font-size: 13px;
        padding: 16px 10px 4px;
    }

    #itemsWrapper:empty::before {
        content: "Пока нет добавленных позиций";
    }

    .order-item {
        border: 1px solid var(--border);
        padding: 10px 12px;
        margin-bottom: 8px;
        position: relative;
        border-radius: var(--radius);
    }

    .order-item .item-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding-right: 20px;
    }

    .order-item .item-title {
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.4;
    }

    .order-item .item-title .meta {
        color: var(--ink-faint);
        font-weight: 500;
    }

    .order-item .warehouse {
        display: block;
        font-size: 12px;
        color: var(--ink-soft);
        margin-top: 2px;
    }

    .return-label {
        display: inline-block;
        color: var(--danger);
        background: var(--danger-soft);
        font-weight: 600;
        font-size: 11px;
        margin-left: 6px;
        padding: 1px 6px;
        border-radius: 4px;
        vertical-align: middle;
    }

    .remove-item {
        position: absolute;
        right: 8px;
        top: 8px;
        border: none;
        background: none;
        width: 22px;
        height: 22px;
        line-height: 1;
        font-size: 17px;
        color: var(--ink-faint);
        cursor: pointer;
        border-radius: 4px;
        transition: background .12s, color .12s;
    }

    .remove-item:hover {
        background: var(--danger-soft);
        color: var(--danger);
    }

    .order-item-bottom {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        font-size: 13px;
    }

    .qty-times {
        color: var(--ink-soft);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .price-field {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .price-input {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        width: 84px;
        padding: 5px 24px 5px 7px;
        background: var(--surface);
        font-family: inherit;
        font-size: 16px;
        color: var(--ink);
        transition: border-color .12s;
    }

    .price-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-soft);
    }

    .price-field::after {
        content: "тг";
        position: absolute;
        right: 8px;
        font-size: 11.5px;
        color: var(--ink-faint);
        pointer-events: none;
    }

    .item-total {
        font-weight: 600;
    }

    /* ---------- Add position button ---------- */
    #openProductModal {
        width: 100%;
        margin: 0 0 12px;
        padding: 9px;
        background: var(--surface);
        color: var(--primary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background .12s, border-color .12s;
        background-color: #01142f;
        color: white;
    }

    #openProductModal:hover {
        background: var(--primary-soft);
        border-color: var(--primary);
    }

    /* ---------- Totals ---------- */
    .discount-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 2px 0 4px;
    }

    .discount-row label.field-label {
        margin-bottom: 0;
    }

    #discountInput {
        width: 120px;
        text-align: right;
        margin-bottom: 0;
    }

    .total-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        border-top: 1px solid var(--border);
        margin: 10px -16px 0;
        padding: 12px 16px 0;
    }

    .total-row .total-label {
        font-size: 13px;
        color: var(--ink-soft);
        font-weight: 500;
    }

    #orderTotal {
        font-weight: 700;
        font-size: 19px;
        letter-spacing: -.005em;
        color: var(--ink);
    }

    /* ---------- Modals ---------- */
    .modal-fullscreen {
        max-width: 100%;
        width: 100%;
        height: 100%;
        margin: 0
    }

    .modal-fullscreen .modal-content {
        height: 100%;
        border-radius: 0;
        border: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        background: var(--bg);
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .modal-header {
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        padding: 14px 18px;
    }

    .modal-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
    }

    .btn-close {
        opacity: .5;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 16px 18px 20px;
        overflow-y: auto;
    }

    #modalSkuInput {
        margin-bottom: 12px;
    }

    .batch-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 11px 13px;
        margin-bottom: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        font-size: 13.5px;
        transition: border-color .12s, background .12s;
    }

    .batch-card:hover {
        border-color: var(--primary);
        background: var(--primary-soft);
    }

    .batch-card .batch-info {
        flex: 1;
        min-width: 0;
    }

    .batch-card .batch-sku {
        font-weight: 600;
        line-height: 1.35;
    }

    .batch-card .batch-code {
        font-size: 12px;
        color: var(--ink-soft);
    }

    .batch-card .batch-qty {
        flex: none;
        font-size: 12px;
        font-weight: 600;
        color: var(--ink-soft);
        white-space: nowrap;
    }

    #batchDetailTable {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 4px;
    }

    #batchDetailTable thead th {
        text-align: left;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--ink-soft);
        padding: 0 8px 8px;
        border-bottom: 1px solid var(--border);
    }

    #batchDetailTable tbody td {
        padding: 10px 8px;
        border-bottom: 1px solid var(--border);
        font-size: 13.5px;
        vertical-align: middle;
    }

    #batchDetailTable tbody tr:last-child td {
        border-bottom: none;
    }

    .qty-input {
        width: 100%;
        font-size: 16px;
        padding: 6px 8px;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        font-weight: 600;
        font-family: inherit;
        background: var(--surface);
    }

    .qty-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-soft);
    }

    #addBatchToOrder {
        width: 100%;
        background: var(--primary);
        color: #fff;
        border: none;
        font-weight: 600;
        font-size: 13.5px;
        cursor: pointer;
        border-radius: var(--radius);
        padding: 11px;
        transition: background .12s;
    }

    #addBatchToOrder:hover {
        background: var(--primary-hover);
    }

    /* jQuery UI autocomplete restyle */
    .ui-autocomplete {
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-lg) !important;
        box-shadow: 0 4px 14px rgba(20, 24, 30, .08) !important;
        padding: 4px !important;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        z-index: 3000 !important;
    }

    .ui-menu-item-wrapper {
        border-radius: var(--radius) !important;
        padding: 8px 9px !important;
        border: none !important;
        font-size: 13.5px;
    }

    .ui-menu-item-wrapper.ui-state-active {
        background: var(--primary-soft) !important;
        color: var(--primary) !important;
        border: none !important;
        margin: 0 !important;
    }
</style>

<div class="container">

    <form method="POST" action="{{ route('admin.orders.store') }}" id="orderForm">
        @csrf

        <div id="orderControls">
            <span id="cancelOrder">Отмена</span>
            <button type="submit" id="finishOrder">Готово</button>
        </div>

        <div class="panel">

            <div class="field">
                <label class="field-label">Имя клиента</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="field">
                <label class="field-label">Телефон клиента</label>
                <input type="text" name="phone" class="form-control" placeholder="+7 (___) ___-__-__" required>
            </div>

            <div class="field">
                <label class="field-label">Комментарий</label>
                <input type="text" name="comment" class="form-control" placeholder="Необязательно">
            </div>

            <div class="field-row">
                <div class="field">
                    <label class="field-label">Дата и время</label>
                    <input type="datetime-local"
                        name="order_date"
                        class="form-control"
                        value="{{ now('Asia/Almaty')->format('Y-m-d\TH:i') }}"
                        required>
                </div>

                <div class="field">
                    <label class="field-label">Склад</label>
                    <select id="warehouseSelect" class="form-select">
                        <option value="">Все склады</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="section-label">
                Позиции
                <span class="count" id="positionsCount">(0)</span>
            </div>

            <button type="button" id="openProductModal">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 2.5V13.5M2.5 8H13.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                </svg>
                Добавить позицию
            </button>

            <div id="itemsWrapper"></div>
        </div>

        <div class="panel">
            <div class="section-label">Итог</div>

            <div class="discount-row">
                <label class="field-label" for="discountInput">Скидка</label>
                <input type="number" id="discountInput" name="discount" class="form-control" value="0">
            </div>

            <div class="total-row">
                <span class="total-label">Итого к оплате</span>
                <div id="orderTotal">0 тг</div>
            </div>
        </div>

    </form>
</div>

{{-- MODALS --}}
<div class="modal fade modal-fullscreen" id="productModal">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Выберите партию</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="modalSkuInput" class="form-control mb-2" placeholder="Введите SKU">
                <div id="modalBatchesSimple"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-fullscreen" id="batchDetailModal">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Введите количество (минус = возврат)</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm" id="batchDetailTable">
                    <thead>
                        <tr>
                            <th>Склад</th>
                            <th>Доступно</th>
                            <th>Количество</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button class="btn w-100" id="addBatchToOrder">Добавить</button>
            </div>
        </div>
    </div>
</div>

<script>
    let itemIndex = 0;
    let currentBatch = {};
    let batchesState = {};

    $('#openProductModal').click(() => {
        // сброс поиска перед каждым открытием модалки
        $('#modalSkuInput').val('');
        $('#modalBatchesSimple').empty();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('productModal')).show();
    });

    $('#modalSkuInput').autocomplete({
        source: '{{ route("admin.variants.autocomplete") }}',
        minLength: 1,
        appendTo: '#productModal',
        select: function(e, ui) {
            loadBatchesSimple(ui.item.value);
        }
    });

    function loadBatchesSimple(sku) {
        $.get('/admin/batches/by-sku/' + sku, function(data) {
            let $list = $('#modalBatchesSimple').empty();
            data.forEach(batch => {
                if (!batchesState[batch.id]) {
                    batchesState[batch.id] = {};
                    batch.warehouses.forEach(w => batchesState[batch.id][w.id] = w.pivot.quantity);
                }
                const totalQty = batch.warehouses.reduce((sum, w) => sum + batchesState[batch.id][w.id], 0);
                $list.append(`
                <div class="batch-card batch-select" data-batch='${JSON.stringify(batch)}' data-sku='${sku}'>
                    <div class="batch-info">
                        <div class="batch-sku">${sku}</div>
                        <div class="batch-code">Партия ${batch.batch_code}</div>
                    </div>
                    <div class="batch-qty">Доступно ${totalQty}</div>
                </div>
            `);
            });
        });
    }

    $(document).on('click', '.batch-select', function() {
        currentBatch = JSON.parse($(this).attr('data-batch'));
        currentBatch.sku = $(this).attr('data-sku');

        const selectedWarehouse = $('#warehouseSelect').val();
        const batchWarehouses = selectedWarehouse ?
            currentBatch.warehouses.filter(w => w.id == selectedWarehouse) :
            currentBatch.warehouses;

        const $tbody = $('#batchDetailTable tbody').empty();

        batchWarehouses.forEach(w => {
            const available = batchesState[currentBatch.id][w.id];

            $tbody.append(`
            <tr>
                <td>${w.name}</td>
                <td>${available}</td>
                <td>
                    <input type="number"
                           class="form-control qty-input"
                           data-warehouse="${w.id}"
                           min="-${available}"
                           max="${available}"
                           placeholder="Кол-во">
                </td>
            </tr>
        `);
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('productModal')).hide();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('batchDetailModal')).show();
    });

    $('#addBatchToOrder').click(function() {
        $('#batchDetailTable tbody tr').each(function() {
            const $input = $(this).find('.qty-input');
            let qty = parseInt($input.val());
            if (isNaN(qty) || qty === 0) return;

            const warehouseId = $input.data('warehouse');
            const available = batchesState[currentBatch.id][warehouseId];

            if (qty > available) {
                alert('Недостаточно товара');
                return;
            }

            const warehouseName = currentBatch.warehouses.find(w => w.id == warehouseId).name;
            const label = qty < 0 ? '<span class="return-label">ВОЗВРАТ</span>' : '';

            $('#itemsWrapper').append(`
            <div class="order-item">
                <div class="item-head">
                    <div>
                        <div class="item-title">${currentBatch.sku} <span class="meta">· Партия ${currentBatch.batch_code}</span>${label}</div>
                        <span class="warehouse">Склад: ${warehouseName}</span>
                    </div>
                    <button type="button" class="remove-item">&times;</button>
                </div>

                <div class="order-item-bottom">
                    <span class="qty-times">${qty} ×
                        <span class="price-field">
                            <input type="number"
                                   name="items[${itemIndex}][price]"
                                   class="price-input"
                                   step="0.01"
                                   placeholder="цена">
                        </span>
                    </span>

                    <span>Сумма: <span class="item-total">0 тг</span></span>
                </div>

                <input type="hidden" name="items[${itemIndex}][sku]" value="${currentBatch.sku}">
                <input type="hidden" name="items[${itemIndex}][batch_id]" value="${currentBatch.id}">
                <input type="hidden" name="items[${itemIndex}][warehouse_id]" value="${warehouseId}">
                <input type="hidden" name="items[${itemIndex}][quantity]" value="${qty}">
                <input type="hidden" name="items[${itemIndex}][warehouse_name]" value="${warehouseName}">
                <input type="hidden" name="items[${itemIndex}][batch_code]" value="${currentBatch.batch_code}">
            </div>
        `);

            if (qty > 0) {
                batchesState[currentBatch.id][warehouseId] -= qty;
            }

            itemIndex++;
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('batchDetailModal')).hide();
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

        const discount = parseFloat($('#discountInput').val()) || 0;
        let final = sum - discount;

        if (final < 0) final = 0;

        $('#orderTotal').text(final.toFixed(2) + ' тг');
    }

    $(document).on('input', '.price-input', recalcOrderTotal);
    $(document).on('input', '#discountInput', recalcOrderTotal);

    $(document).on('click', '.remove-item', function() {
        $(this).closest('.order-item').remove();
        recalcOrderTotal();
        $('#positionsCount').text('(' + $('.order-item').length + ')');
    });

    /* ✔️ ВАЛИДАЦИЯ ЦЕНЫ */
    $('#orderForm').on('submit', function(e) {

        let valid = true;

        $('.price-input').each(function() {
            const val = $(this).val();

            if (val === '' || parseFloat(val) <= 0) {
                valid = false;
                $(this).css('border-color', '#e5484d');
            } else {
                $(this).css('border-color', 'var(--border)');
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Заполните цену у всех позиций');
        }
    });

    $('#cancelOrder').click(() => window.history.back());
</script>

@endsection