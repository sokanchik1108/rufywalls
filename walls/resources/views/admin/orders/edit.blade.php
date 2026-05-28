@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    body {
        background: #f4f6fb;
        font-family: 'Segoe UI', sans-serif;
    }

    .container {
        padding: 20px
    }

    #orderControls {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        margin-bottom: 20px;
    }

    #cancelOrder {
        color: #ef4444;
        cursor: pointer;
    }

    #finishOrder {
        background: #3b82f6;
        color: #fff;
        border: none;
        font-weight: 600;
        padding: 8px 16px;
    }

    .order-item {
        background: #fff;
        padding: 12px;
        margin-bottom: 12px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .05);
    }

    .price-input {
        border: none;
        border-bottom: 1px solid #ef4444;
        width: 90px;
    }

    .item-total {
        font-weight: 600;
        margin-left: 10px;
    }

    .remove-item {
        float: right;
        border: none;
        background: none;
        color: #ef4444;
        font-size: 20px;
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

        <input type="text" name="name" class="form-control mb-2" value="{{ $order->name }}">
        <input type="text" name="phone" class="form-control mb-2" value="{{ $order->phone }}">
        <input type="text" name="comment" class="form-control mb-2" value="{{ $order->comment }}">

        {{-- 🔥 СКИДКА --}}
        <label>Скидка</label>
        <input type="number" id="discountInput" name="discount"
            class="form-control mb-3"
            value="{{ $order->discount ?? 0 }}">

        <div id="itemsWrapper">

            @foreach($order->items as $i => $item)

            <div class="order-item">

                <button type="button" class="remove-item">&times;</button>

                <div>
                    <b>{{ $item->variant->sku }}</b>
                    ({{ $item->batch_code }})
                </div>

                <div style="margin-top:10px">
                    {{ $item->quantity }} ×

                    {{-- 🔥 PRICE --}}
                    <input type="number"
                        step="0.01"
                        name="items[{{ $i }}][price]"
                        class="price-input"
                        value="{{ $item->price }}">

                    <span class="item-total"></span>
                </div>

                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                <input type="hidden" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}">

            </div>

            @endforeach

        </div>

        <div style="text-align:right;font-weight:700">
            ИТОГО: <span id="orderTotal">0</span> ₸
        </div>

    </form>
</div>

<script>
    function recalc() {
        let sum = 0;

        $('.order-item').each(function() {

            let qty = parseInt($(this).find('input[name$="[quantity]"]').val()) || 0;
            let price = parseFloat($(this).find('.price-input').val()) || 0;

            let total = qty * price;

            $(this).find('.item-total').text(total.toFixed(2) + ' ₸');

            sum += total;
        });

        let discount = parseFloat($('#discountInput').val()) || 0;

        $('#orderTotal').text((sum - discount).toFixed(2));
    }

    $(document).on('input', '.price-input, #discountInput', recalc);

    $(document).on('click', '.remove-item', function() {
        $(this).closest('.order-item').remove();
        recalc();
    });

    recalc();

    $('#cancelOrder').click(() => history.back());
</script>

@endsection