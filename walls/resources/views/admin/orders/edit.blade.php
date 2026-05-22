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
</style>

<div class="container">

    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
        @csrf
        @method('PUT')

        <div id="orderControls">
            <span id="cancelOrder">ОТМЕНА</span>
            <button type="submit" id="finishOrder">СОХРАНИТЬ</button>
        </div>

        <input type="text" name="name" class="form-control" value="{{ $order->name }}" required>
        <input type="text" name="phone" class="form-control" value="{{ $order->phone }}" required>
        <input type="text" name="comment" class="form-control" value="{{ $order->comment }}">

        <div class="positions-header">
            <span>Позиции</span>
            <span id="positionsCount">({{ $order->items->count() }})</span>
        </div>

        <div id="itemsWrapper">

            @foreach($order->items as $i => $item)
            <div class="order-item">

                <button type="button" class="remove-item">&times;</button>

                <div>
                    <strong>{{ $item->variant->sku }}</strong>
                    (Партия {{ $item->batch_code }})
                </div>

                <div class="order-item-bottom">
                    <span>Кол-во: {{ $item->quantity }}</span>
                </div>

                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                <input type="hidden" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}">

            </div>
            @endforeach

        </div>

    </form>

</div>

<script>
$('#cancelOrder').click(() => window.history.back());

$(document).on('click', '.remove-item', function () {
    $(this).closest('.order-item').remove();
    $('#positionsCount').text('(' + $('.order-item').length + ')');
});
</script>

@endsection