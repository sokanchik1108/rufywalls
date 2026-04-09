@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
    body {
        background: #f4f6fb;
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0
    }

    .container {
        padding: 20px
    }

    #orderControls {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 16px
    }

    #cancelOrder {
        color: #ef4444;
        cursor: pointer
    }

    #finishOrder {
        background: #3b82f6;
        color: #fff;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-transform: uppercase;
        transition: all .2s
    }

    #finishOrder:hover {
        background: #2563eb
    }

    .order-item {
        background: #fff;
        padding: 12px;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .05);
        position: relative;
        border-radius: 10px
    }

    .order-item-bottom {
        margin-top: 6px;
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap
    }

    .price-input {
        border: none;
        border-bottom: 1px solid #ef4444;
        width: 80px;
        background: transparent
    }

    .item-total {
        font-weight: 600;
        margin-left: 6px
    }

    .remove-item {
        position: absolute;
        right: 10px;
        top: 6px;
        border: none;
        background: none;
        font-size: 20px;
        color: #ef4444;
        cursor: pointer
    }

    input.form-control {
        border: none;
        border-bottom: 1px solid #ccc;
        padding: 6px 0;
        margin-bottom: 16px;
        background: transparent
    }

    input:focus {
        outline: none;
        border-bottom: 1px solid #4f46e5
    }

    #orderTotal {
        font-weight: 700;
        font-size: 18px;
        margin-top: 12px;
        text-align: right;
        color: #2563eb
    }
</style>

<div class="container">

    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
        @csrf
        @method('PUT')

        <div id="orderControls">
            <span id="cancelOrder">ОТМЕНА</span>
            <button type="submit" id="finishOrder">СОХРАНИТЬ</button>
        </div>

        <input type="text" name="name" class="form-control" placeholder="Имя клиента" value="{{ $order->name }}" required>
        <input type="text" name="phone" class="form-control" placeholder="Телефон клиента" value="{{ $order->phone }}" required>
        <input type="text" name="comment" class="form-control" placeholder="Комментарий" value="{{ $order->comment }}">

        <div class="positions-header">
            <span>Позиции</span>
            <span id="positionsCount">({{ $order->items->count() }})</span>
        </div>

        <div id="itemsWrapper">
            @foreach($order->items as $i => $item)
            <div class="order-item">
                <div>{{ $item->variant->sku }} (Партия {{ $item->batch_code }})</div>
                <div class="order-item-bottom">
                    <span>{{ $item->quantity }} ×
                        <input type="number" name="items[{{ $i }}][price]" class="price-input" step="0.01" value="{{ $item->price }}">
                    </span>
                    <span>Сумма: <span class="item-total">{{ $item->price * $item->quantity }} тг</span></span>
                </div>
                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                <input type="hidden" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}">
            </div>
            @endforeach
        </div>

        <div id="orderTotal">{{ $order->items->sum(fn($i) => $i->price * $i->quantity) }} тг</div>

    </form>
</div>

<script>
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

    $(document).on('input', '.price-input', recalcOrderTotal);
    $('#cancelOrder').click(() => window.history.back());
</script>

@endsection