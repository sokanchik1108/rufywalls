@extends('layouts.main')

@section('title', 'Корзина')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 fw-semibold text-body-emphasis">Корзина</h1>

    @if($cartItems->isEmpty())
        <div class="text-center text-muted fs-5 mb-4">
            Ваша корзина пуста.
        </div>
        <div class="text-center">
            <a href="{{ route('catalog') }}" class="btn btn-outline-secondary rounded-pill px-4">
                ← Вернуться в каталог
            </a>
        </div>
    @else
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <a href="{{ route('catalog') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        ← Продолжить покупки
    </a>

    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите очистить корзину?');">
        @csrf
        <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
            Очистить корзину
        </button>
    </form>
</div>


        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table class="table align-middle border rounded-3 overflow-hidden shadow-sm">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th>Товар</th>
                        <th>Цена</th>
                        <th>Кол-во</th>
                        <th>Сумма</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                        <tr data-product-id="{{ $item['product']->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['product']->name }}" class="me-3 rounded-2" style="height: 80px; width: 80px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $item['product']->name }}</div>
                                        <div class="text-muted small">Артикул: {{ $item['product']->article }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ number_format($item['product']->sale_price, 2) }} ₽</td>
                            <td>
                                <input type="number"
                                    name="quantity"
                                    value="{{ $item['quantity'] }}"
                                    min="1"
                                    data-product-id="{{ $item['product']->id }}"
                                    class="form-control form-control-sm quantity-input"
                                    style="width: 70px;">
                            </td>
                            <td class="text-muted item-total">{{ number_format($item['total'], 2) }} ₽</td>
                            <td>
                                <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-light border-0 text-danger" title="Удалить">
                                        ✕
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-light">
                        <td colspan="3" class="text-end fw-semibold">Итого:</td>
                        <td colspan="2" class="fw-bold cart-total">{{ number_format($total, 2) }} ₽</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="d-md-none">
            @foreach($cartItems as $item)
                <div class="card mb-3 border rounded-3 shadow-sm" data-product-id="{{ $item['product']->id }}">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['product']->name }}" class="rounded-2" style="width: 80px; height: 80px; object-fit: cover;">
                            @endif
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1">{{ $item['product']->name }}</div>
                                <div class="text-muted small mb-2">Артикул: {{ $item['product']->article }}</div>
                                <div class="text-muted small">Цена: {{ number_format($item['product']->sale_price, 2) }} ₽</div>
                                <div class="text-muted small">Сумма: <span class="item-total">{{ number_format($item['total'], 2) }} ₽</span></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-3 gap-2">
                            <input type="number"
                                name="quantity"
                                value="{{ $item['quantity'] }}"
                                min="1"
                                data-product-id="{{ $item['product']->id }}"
                                class="form-control form-control-sm quantity-input"
                                style="width: 70px;">
                            <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST" class="ms-auto">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger w-100">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="text-end fw-semibold fs-5 mt-3">
                Итого: <span class="cart-total">{{ number_format($total, 2) }} ₽</span>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="#" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm">
                Оформить заказ
            </a>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', () => {
                const productId = input.dataset.productId;
                const quantity = input.value;

                fetch(`/cart/update/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ quantity: quantity })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const container = input.closest('tr') || input.closest('.card');
                        const itemTotalEl = container.querySelector('.item-total');
                        const cartTotalEls = document.querySelectorAll('.cart-total');

                        if (itemTotalEl) itemTotalEl.textContent = data.itemTotal + ' ₽';
                        cartTotalEls.forEach(el => el.textContent = data.cartTotal + ' ₽');
                    } else {
                        alert('Ошибка: товар не найден.');
                    }
                })
                .catch(err => {
                    console.error('Ошибка обновления корзины:', err);
                });
            });
        });
    });
</script>
@endsection
