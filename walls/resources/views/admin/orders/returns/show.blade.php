@extends('layouts.app')

@section('title', 'Возврат №' . $customerReturn->id)

@section('content')
@php
    $order = $customerReturn->order;

    $orderDate = $order?->order_date
        ? \Carbon\Carbon::parse($order->order_date)->format('d.m.Y')
        : $order?->created_at?->format('d.m.Y');

    $shopName = $order?->pointOfSale?->name
        ?? $order?->point_of_sale_name
        ?? '—';

    $customerName = $order?->name ?? '—';
    $customerPhone = $order?->phone;
    $orderItems = $order?->items ?? collect();

    $orderDiscount = max(0, (float) ($order?->discount ?? 0));

    $itemsTotal = 0;

    foreach ($rows as $row) {
        $orderItem = $row['item']->orderItem;
        $quantity = max(0, (int) ($row['quantity'] ?? 0));
        $price = max(0, (float) ($orderItem?->price ?? 0));

        $itemsTotal += $price * $quantity;
    }

    $itemsTotal = round($itemsTotal, 2);

    // Сумма платежей, сохранённых именно в текущем документе.
    $paymentsTotal = round(
        (float) $customerReturn->payments->sum('amount'),
        2
    );

    // Определяем количество, возвращённое в других документах.
    $otherReturns = \App\Models\CustomerReturnItem::query()
        ->whereHas('customerReturn', function ($query) use ($customerReturn) {
            $query->where('order_id', $customerReturn->order_id)
                ->where('id', '!=', $customerReturn->id);
        })
        ->selectRaw('order_item_id, SUM(quantity) as returned_quantity')
        ->groupBy('order_item_id')
        ->get()
        ->keyBy('order_item_id');

    $orderQuantities = [];
    $previouslyReturned = [];
    $currentQuantities = [];

    foreach ($orderItems as $orderItem) {
        $key = (string) $orderItem->id;

        $orderQuantities[$key] = max(0, (int) $orderItem->quantity);

        $previouslyReturned[$key] = max(
            0,
            (int) ($otherReturns->get($orderItem->id)?->returned_quantity ?? 0)
        );
    }

    foreach ($rows as $row) {
        $orderItem = $row['item']->orderItem;

        if (!$orderItem) {
            continue;
        }

        $key = (string) $orderItem->id;

        $currentQuantities[$key] = ($currentQuantities[$key] ?? 0)
            + max(0, (int) ($row['quantity'] ?? 0));
    }

    $isFullReturn = $orderItems->isNotEmpty();

    foreach ($orderItems as $orderItem) {
        $key = (string) $orderItem->id;

        $returnedTotal = ($previouslyReturned[$key] ?? 0)
            + ($currentQuantities[$key] ?? 0);

        if ($returnedTotal < max(0, (int) $orderItem->quantity)) {
            $isFullReturn = false;
            break;
        }
    }

    // Для первоначального отображения берём уже сохранённые платежи.
    // Не пересчитываем старый документ по текущему состоянию других возвратов.
    $discountToDeduct = min(
        $orderDiscount,
        max(0, $itemsTotal - $paymentsTotal)
    );

    $returnTotal = $paymentsTotal;

    $returnDate = $customerReturn->return_date
        ? \Carbon\Carbon::parse($customerReturn->return_date)->format('Y-m-d\TH:i')
        : $customerReturn->created_at?->format('Y-m-d\TH:i');
@endphp

<style>
.return-page {
    --blue: #0878e8;
    --text: #172033;
    --muted: #758094;
    --line: #e4e8ee;
    --soft: #f6f8fa;
    max-width: 1000px;
    margin: 0 auto;
    padding: 12px 16px 30px;
    color: var(--text);
    font-size: 14px;
}
.return-page * { box-sizing: border-box; }
.return-page .return-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
}
.return-page .back-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: #fff;
    color: var(--text);
    text-decoration: none;
}
.return-page .page-title {
    margin: 0;
    font-size: 20px;
    line-height: 1.3;
    font-weight: 650;
    overflow-wrap: anywhere;
}
.return-page .header-spacer { flex: 1; }
.return-page .btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    padding: 8px 12px;
    border: 1px solid #dc3545;
    border-radius: 7px;
    background: #fff;
    color: #dc3545;
    font: inherit;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
}
.return-page .btn-delete:hover { background: #dc3545; color: #fff; }
.return-page .info-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr 1fr;
    gap: 12px;
    padding: 11px 0;
    border-block: 1px solid var(--line);
    margin-bottom: 18px;
}
.return-page .info-item { min-width: 0; }
.return-page .info-label {
    display: block;
    margin-bottom: 3px;
    color: var(--muted);
    font-size: 11px;
}
.return-page .info-value {
    font-size: 12px;
    line-height: 1.4;
    font-weight: 600;
    overflow-wrap: anywhere;
}
.return-page .customer-phone {
    display: block;
    margin-top: 2px;
    color: var(--muted);
    font-size: 10px;
    font-weight: 400;
}
.return-page .notice {
    padding: 10px 12px;
    margin-bottom: 12px;
    border: 1px solid transparent;
    border-radius: 7px;
    font-size: 12px;
    line-height: 1.5;
}
.return-page .notice-success { color: #16834a; background: #edf9f1; border-color: #ccebd6; }
.return-page .notice-error { color: #c53030; background: #fff1f1; border-color: #f5cccc; }
.return-page .notice ul { margin: 5px 0 0; padding-left: 18px; }
.return-page .section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin: 0 0 9px;
    color: var(--muted);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .4px;
    text-transform: uppercase;
}
.return-page .product-list { display: flex; flex-direction: column; gap: 8px; }
.return-page .product-card {
    min-width: 0;
    padding: 10px 12px;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: #fff;
}
.return-page .product-top {
    display: grid;
    grid-template-columns: minmax(0,1fr) auto;
    align-items: center;
    gap: 8px;
    margin-bottom: 9px;
}
.return-page .product-sku { min-width: 0; font-size: 13px; line-height: 1.4; font-weight: 650; overflow-wrap: anywhere; }
.return-page .product-name { margin-top: 3px; color: var(--muted); font-size: 11px; font-weight: 400; }
.return-page .product-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }
.return-page .product-warehouse { color: var(--muted); font-size: 12px; text-align: right; }
.return-page .product-available { color: #16834a; font-size: 11px; font-weight: 600; white-space: nowrap; }
.return-page .product-bottom {
    display: grid;
    grid-template-columns: minmax(0,1fr) 75px minmax(0,1fr);
    align-items: center;
    gap: 8px;
    padding-top: 8px;
    border-top: 1px solid #f0f2f5;
}
.return-page .price-note { color: var(--muted); font-size: 11px; }
.return-page .qty-input {
    width: 75px;
    height: 32px;
    padding: 3px;
    border: 0;
    border-bottom: 1px solid #aab3c0;
    border-radius: 0;
    outline: none;
    background: transparent;
    color: var(--text);
    font: inherit;
    font-size: 14px;
    font-weight: 650;
    text-align: center;
    appearance: textfield;
    -moz-appearance: textfield;
}
.return-page .qty-input::-webkit-inner-spin-button,
.return-page .qty-input::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
.return-page .qty-input:focus { border-bottom: 2px solid var(--blue); }
.return-page .qty-view { text-align: center; font-weight: 650; }
.return-page .line-total { min-width: 0; text-align: right; font-size: 13px; font-weight: 650; white-space: nowrap; }
.return-page .total-panel {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 16px;
    padding: 12px 13px;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: var(--soft);
}
.return-page .total-label { color: var(--muted); font-size: 12px; }
.return-page .total-value { font-size: 21px; font-weight: 700; white-space: nowrap; }
.return-page .section { margin-top: 21px; }
.return-page .form-control,
.return-page .form-select {
    width: 100%;
    min-width: 0;
    min-height: 37px;
    padding: 7px 9px;
    border: 1px solid #dce1e8;
    border-radius: 7px;
    background: #fff;
    color: var(--text);
    font: inherit;
    font-size: 12px;
    box-shadow: none;
}
.return-page .form-control:focus,
.return-page .form-select:focus { border-color: var(--blue); outline: none; }
.return-page .payment-list { display: flex; flex-direction: column; gap: 8px; }
.return-page .payment-row { display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1fr); gap: 8px; }
.return-page .payment-summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 12px; color: var(--muted); font-size: 12px; }
.return-page .payment-summary strong { color: var(--text); font-size: 13px; }
.return-page .is-valid { color: #16834a !important; }
.return-page .is-invalid { color: #cf3333 !important; }
.return-page .form-error { margin-top: 6px; color: #cf3333; font-size: 12px; }
.return-page .edit-input { display: none; }
.return-page.editing .view-value { display: none; }
.return-page.editing .edit-input { display: block; }
.return-page .bottom-actions { display: flex; justify-content: flex-end; flex-wrap: wrap; gap: 8px; margin-top: 22px; }
.return-page .btn-cancel,
.return-page .btn-submit,
.return-page .btn-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 39px;
    padding: 9px 15px;
    border-radius: 7px;
    font: inherit;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
}
.return-page .btn-cancel { border: 1px solid var(--line); background: #fff; color: #536074; }
.return-page .btn-edit { border: 1px solid var(--blue); background: #fff; color: var(--blue); }
.return-page .btn-submit { border: 1px solid var(--blue); background: var(--blue); color: #fff; }
.return-page .hidden { display: none !important; }

@media (max-width: 600px) {
    .return-page { padding: 9px 10px 22px; }
    .return-page .return-header { gap: 7px; }
    .return-page .page-title { font-size: 16px; }
    .return-page .btn-delete { min-height: 32px; padding: 6px 8px; font-size: 11px; }
    .return-page .info-grid { grid-template-columns: 1.2fr .9fr .9fr; gap: 7px; padding: 10px 0; }
    .return-page .info-label { font-size: 10px; }
    .return-page .info-value { font-size: 11px; }
    .return-page .product-card { padding: 9px 10px; }
    .return-page .product-sku { font-size: 12px; }
    .return-page .product-warehouse { font-size: 11px; }
    .return-page .product-available { font-size: 10px; }
    .return-page .product-bottom { grid-template-columns: minmax(0,1fr) 58px minmax(0,1fr); gap: 5px; }
    .return-page .qty-input { width: 58px; }
    .return-page .line-total { font-size: 12px; }
    .return-page .total-value { font-size: 19px; }
    .return-page .form-control,
    .return-page .form-select { min-height: 36px; padding: 6px; font-size: 11px; }
    .return-page .bottom-actions { display: grid; grid-template-columns: 1fr 1fr; }
    .return-page .bottom-actions > * { min-width: 0; }
    .return-page .btn-cancel,
    .return-page .btn-submit,
    .return-page .btn-edit { width: 100%; padding: 9px 6px; font-size: 12px; }
}


@media (max-width: 600px) {
    .return-page input,
    .return-page select,
    .return-page textarea {
        font-size: 16px !important;
    }
}


</style>

<div class="return-page" id="returnPage">
    <div class="return-header">
        <a href="{{ route('admin.orders.edit', $customerReturn->order_id) }}"
           class="back-button"
           aria-label="Назад">←</a>

        <h1 class="page-title">Возврат №{{ $customerReturn->id }}</h1>

        <div class="header-spacer"></div>

        <button type="submit" class="btn-delete" form="deleteReturnForm">
            Удалить
        </button>
    </div>

    @if(session('success'))
        <div class="notice notice-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="notice notice-error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="notice notice-error">
            <strong>Проверь данные:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">Покупатель</span>
            <div class="info-value">
                {{ $customerName }}
                @if($customerPhone)
                    <span class="customer-phone">{{ $customerPhone }}</span>
                @endif
            </div>
        </div>

        <div class="info-item">
            <span class="info-label">Дата заказа</span>
            <div class="info-value">{{ $orderDate ?: '—' }}</div>
        </div>

        <div class="info-item">
            <span class="info-label">Магазин</span>
            <div class="info-value">{{ $shopName }}</div>
        </div>
    </div>

    <form action="{{ route('admin.orders.returns.update', $customerReturn->id) }}"
          method="POST"
          id="returnForm">
        @csrf
        @method('PUT')

        <div class="section-heading">
            <span>Товары</span>
            <span>{{ count($rows) }} поз.</span>
        </div>

        <div class="product-list">
            @forelse($rows as $index => $row)
                @php
                    $item = $row['item'];
                    $orderItem = $item->orderItem;
                    $variant = $item->variant;
                    $product = $variant?->product;

                    $sku = $variant?->sku ?? '—';
                    $productName = $product?->name ?? '—';

                    $batch = $row['batch'] ?? $item->batch;
                    $warehouse = $row['warehouse'] ?? $item->warehouse;

                    $batchCode = $batch?->batch_code ?? $batch?->code ?? '—';
                    $warehouseName = $warehouse?->name ?? '—';

                    $price = max(0, (float) ($orderItem?->price ?? 0));
                    $quantity = max(0, (int) ($row['quantity'] ?? 0));
                    $maxQuantity = max(0, (int) ($row['available_quantity'] ?? 0));
                    $groupKey = (string) ($row['key'] ?? '');

                    $fieldName = 'items.' . $index . '.quantity';
                    $fieldValue = old($fieldName, $quantity);
                @endphp

                <div class="product-card"
                     data-product-card
                     data-price="{{ $price }}"
                     data-order-item-id="{{ $orderItem?->id }}"
                     data-max="{{ $maxQuantity }}">

                    <input type="hidden"
                           name="items[{{ $index }}][key]"
                           value="{{ $groupKey }}">

                    <div class="product-top">
                        <div class="product-sku">
                            {{ $sku }} ({{ $batchCode }})
                            <div class="product-name">{{ $productName }}</div>
                        </div>

                        <div class="product-meta">
                            <div class="product-warehouse">{{ $warehouseName }}</div>
                            <div class="product-available">
                                Доступно всего: {{ $maxQuantity }} шт.
                            </div>
                        </div>
                    </div>

                    <div class="product-bottom">
                        <div>
                            <div class="price-note">Цена продажи</div>
                            <div class="price-note">
                                {{ number_format($price, 2, '.', ' ') }} ₸
                            </div>
                        </div>

                        <div>
                            <div class="view-value qty-view">{{ $quantity }}</div>

                            <input type="number"
                                   class="qty-input edit-input"
                                   name="items[{{ $index }}][quantity]"
                                   value="{{ $fieldValue }}"
                                   min="0"
                                   max="{{ $maxQuantity }}"
                                   step="1"
                                   inputmode="numeric"
                                   data-quantity
                                   disabled
                                   required>
                        </div>

                        <div class="line-total" data-line-total>
                            {{ number_format($price * $quantity, 2, '.', ' ') }} ₸
                        </div>
                    </div>
                </div>
            @empty
                <div class="notice notice-error">В возврате нет товарных позиций.</div>
            @endforelse
        </div>

        <div class="payment-summary">
            <span>Сумма товаров</span>
            <strong id="returnSubtotal">
                {{ number_format($itemsTotal, 2, '.', ' ') }} ₸
            </strong>
        </div>

        <div class="payment-summary">
            <span>
                Скидка заказа
                <span id="discountCondition">
                    {{ $discountToDeduct > 0 ? '(сохранённая скидка)' : '(не вычиталась)' }}
                </span>
            </span>
            <strong id="returnDiscount">
                −{{ number_format($discountToDeduct, 2, '.', ' ') }} ₸
            </strong>
        </div>

        <div class="total-panel">
            <div class="total-label">Итого к возврату</div>
            <div class="total-value" id="returnTotal">
                {{ number_format($returnTotal, 2, '.', ' ') }} ₸
            </div>
        </div>

        <div class="section">
            <div class="section-heading"><span>Дата возврата</span></div>

            <div class="view-value info-value">
                {{ $customerReturn->return_date
                    ? \Carbon\Carbon::parse($customerReturn->return_date)->format('d.m.Y H:i')
                    : '—' }}
            </div>

            <input type="datetime-local"
                   name="return_date"
                   value="{{ old('return_date', $returnDate) }}"
                   class="form-control edit-input"
                   disabled
                   required>
        </div>

        <div class="section">
            <div class="section-heading"><span>Возврат денег</span></div>

            <div class="payment-list">
                @forelse($customerReturn->payments as $payment)
                    @php
                        $methodName = $payment->paymentMethod?->name
                            ?? $payment->method
                            ?? 'Способ оплаты';
                    @endphp

                    <div class="payment-row">
                        <div>
                            <div class="view-value info-value">{{ $methodName }}</div>

                            <select name="payments[{{ $payment->id }}][payment_method_id]"
                                    class="form-select edit-input"
                                    disabled
                                    required>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}"
                                        @selected((string) old(
                                            'payments.' . $payment->id . '.payment_method_id',
                                            $payment->payment_method_id
                                        ) === (string) $method->id)>
                                        {{ $method->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <div class="view-value info-value">
                                {{ number_format((float) $payment->amount, 2, ',', ' ') }} ₸
                            </div>

                            <input type="number"
                                   name="payments[{{ $payment->id }}][amount]"
                                   value="{{ old('payments.' . $payment->id . '.amount', $payment->amount) }}"
                                   min="0.01"
                                   step="0.01"
                                   class="form-control edit-input payment-amount"
                                   disabled
                                   required>
                        </div>
                    </div>
                @empty
                    <div class="info-value">Платежи не найдены.</div>
                @endforelse
            </div>

            <div class="payment-summary">
                <span>Всего возвращено</span>
                <strong id="paymentTotal">
                    {{ number_format($paymentsTotal, 2, ',', ' ') }} ₸
                </strong>
            </div>

            <div class="payment-summary">
                <span>Разница с итогом возврата</span>
                <strong id="paymentDifference">0,00 ₸</strong>
            </div>

            <div class="form-error" id="paymentError" hidden>
                Сумма платежей отличается от итоговой суммы возврата.
            </div>
        </div>

        <div class="section">
            <div class="section-heading"><span>Комментарий</span></div>

            <div class="view-value comment-view">
                {{ $customerReturn->comment ?: '—' }}
            </div>

            <textarea name="comment"
                      class="form-control edit-input"
                      rows="3"
                      maxlength="5000"
                      disabled>{{ old('comment', $customerReturn->comment) }}</textarea>
        </div>

        <div class="bottom-actions">
            <button type="button" class="btn-cancel hidden" id="cancelEdit">Отмена</button>
            <button type="button" class="btn-edit" id="editButton">Редактировать</button>
            <button type="submit" class="btn-submit hidden" id="saveButton">Сохранить изменения</button>
        </div>
    </form>

    <form action="{{ route('admin.orders.returns.destroy', $customerReturn->id) }}"
          method="POST"
          id="deleteReturnForm"
          onsubmit="return confirm('Удалить возврат №{{ $customerReturn->id }}? Это действие нельзя отменить.');">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
(function () {
    const page = document.getElementById('returnPage');
    const form = document.getElementById('returnForm');

    if (!page || !form) return;

    const editButton = document.getElementById('editButton');
    const cancelButton = document.getElementById('cancelEdit');
    const saveButton = document.getElementById('saveButton');

    const subtotalElement = document.getElementById('returnSubtotal');
    const discountElement = document.getElementById('returnDiscount');
    const discountCondition = document.getElementById('discountCondition');
    const totalElement = document.getElementById('returnTotal');

    const paymentTotalElement = document.getElementById('paymentTotal');
    const differenceElement = document.getElementById('paymentDifference');
    const paymentError = document.getElementById('paymentError');

    const orderQuantities = @json($orderQuantities);
    const previouslyReturned = @json($previouslyReturned);
    const orderDiscount = Number(@json($orderDiscount)) || 0;

    const initialValues = new Map();

    form.querySelectorAll('input, textarea, select').forEach(field => {
        if (field.name) initialValues.set(field.name, field.value);
    });

    const number = value => {
        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : 0;
    };

    const money = value => new Intl.NumberFormat('ru-RU', {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2
    }).format(value) + ' ₸';

    function getCurrentQuantities() {
        const quantities = {};

        page.querySelectorAll('[data-product-card]').forEach(card => {
            const id = String(card.dataset.orderItemId || '');
            const input = card.querySelector('[data-quantity]');

            if (!id || !input) return;

            const quantity = Math.max(
                0,
                Math.floor(number(input.value))
            );

            quantities[id] = (quantities[id] || 0) + quantity;
        });

        return quantities;
    }

    function isFullReturn() {
        const current = getCurrentQuantities();
        const ids = Object.keys(orderQuantities);

        return ids.length > 0 && ids.every(id => {
            const sold = number(orderQuantities[id]);
            const returnedBefore = number(previouslyReturned[id]);
            const returnedNow = number(current[id]);

            return returnedBefore + returnedNow >= sold;
        });
    }

    function calculateItems() {
        let subtotal = 0;

        page.querySelectorAll('[data-product-card]').forEach(card => {
            const price = number(card.dataset.price);
            const input = card.querySelector('[data-quantity]');
            const max = number(input.max);

            let quantity = Math.max(0, Math.floor(number(input.value)));
            quantity = Math.min(quantity, max);

            if (String(quantity) !== input.value) {
                input.value = String(quantity);
            }

            const lineTotal = Math.round(
                (price * quantity + Number.EPSILON) * 100
            ) / 100;

            subtotal += lineTotal;

            card.querySelector('[data-line-total]').textContent = money(lineTotal);
            card.querySelector('.qty-view').textContent = String(quantity);
        });

        return Math.round((subtotal + Number.EPSILON) * 100) / 100;
    }

    function calculatePayments() {
        let total = 0;

        page.querySelectorAll('.payment-amount').forEach(input => {
            total += Math.max(0, number(input.value));
        });

        return Math.round((total + Number.EPSILON) * 100) / 100;
    }

    function calculate() {
        const subtotal = calculateItems();
        const paymentTotal = calculatePayments();
        const fullReturn = isFullReturn();

        // При редактировании проверяем полноту возврата заново.
        // Если возвращён весь заказ, скидка вычитается из суммы товаров.
        const discount = fullReturn
            ? Math.min(orderDiscount, subtotal)
            : 0;

        const total = Math.max(0, Math.round(
            (subtotal - discount + Number.EPSILON) * 100
        ) / 100);

        const difference = Math.round(
            (total - paymentTotal + Number.EPSILON) * 100
        ) / 100;

        const balanced = Math.abs(difference) < 0.01;

        subtotalElement.textContent = money(subtotal);
        discountElement.textContent = '−' + money(discount);
        totalElement.textContent = money(total);

        discountCondition.textContent = fullReturn
            ? '(полный возврат)'
            : '(частичный возврат)';

        paymentTotalElement.textContent = money(paymentTotal);
        differenceElement.textContent = money(difference);

        differenceElement.classList.toggle('is-valid', balanced);
        differenceElement.classList.toggle('is-invalid', !balanced);

        paymentError.hidden = balanced;
    }

    function setEditing(editing) {
        page.classList.toggle('editing', editing);

        form.querySelectorAll('.edit-input').forEach(field => {
            field.disabled = !editing;
        });

        editButton.classList.toggle('hidden', editing);
        cancelButton.classList.toggle('hidden', !editing);
        saveButton.classList.toggle('hidden', !editing);

        calculate();
    }

    editButton.addEventListener('click', () => setEditing(true));

    cancelButton.addEventListener('click', () => {
        initialValues.forEach((value, name) => {
            form.querySelectorAll('[name]').forEach(field => {
                if (field.name === name) field.value = value;
            });
        });

        setEditing(false);
    });

    page.addEventListener('input', event => {
        if (event.target.matches('[data-quantity], .payment-amount')) {
            calculate();
        }
    });

    page.addEventListener('change', event => {
        if (event.target.matches('[data-quantity]')) {
            const input = event.target;

            input.value = String(Math.min(
                number(input.max),
                Math.max(0, Math.floor(number(input.value)))
            ));

            calculate();
        }
    });

    form.addEventListener('submit', event => {
        let valid = true;

        page.querySelectorAll('[data-quantity]').forEach(input => {
            const value = number(input.value);

            if (
                !Number.isInteger(value) ||
                value < 0 ||
                value > number(input.max)
            ) {
                valid = false;
                input.setCustomValidity(
                    'Количество превышает доступный остаток.'
                );
                input.reportValidity();
            } else {
                input.setCustomValidity('');
            }
        });

        if (!valid) {
            event.preventDefault();
        }
    });

    calculate();
})();
</script>
@endsection
