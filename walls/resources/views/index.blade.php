@extends('layouts.main')

{{-- 🔹 Title --}}
@section('title', 'Корзина - RAFY WALLS')

{{-- 🔹 Meta Description (для SEO, Open Graph и Twitter) --}}
@section('meta_description', 'Ваша корзина RAFY WALLS — шаг до стильного интерьера. Проверьте товары и оформите заказ легко и быстро.')

{{-- 🔹 Дополнительные мета-теги --}}
@section('meta')
<meta name="description" content="@yield('meta_description')" />
<meta name="robots" content="noindex, follow">
<link rel="canonical" href="{{ url('/cart') }}">
@endsection



@section('content')
<div class="container py-5">

    @if(session('success_html') || session('success'))
    <div id="blade-toast" class="position-fixed top-0 start-50 translate-middle-x mt-4 z-1050 animate-fade-in-down" style="max-width: 90%; width: 350px;">
        <div class="d-flex align-items-center bg-dark text-white p-3 rounded shadow position-relative">

            {{-- Показываем иконку только если это обычное сообщение --}}
            @if(session('success') && !session('success_html'))
            <i class="bi bi-check-circle-fill me-2 fs-4"></i>
            @endif

            {{-- Текст сообщения --}}
            <span style="max-width: 300px; word-wrap: break-word; white-space: normal;">
                {!! session('success_html') ?? e(session('success')) !!}
            </span>

            {{-- Кнопка закрытия --}}
            <button type="button"
                class="btn-close btn-close-white position-absolute end-0 top-50 translate-middle-y me-3"
                aria-label="Закрыть"
                onclick="document.getElementById('blade-toast').remove()"></button>
        </div>
    </div>
    @endif






    <h1 class="mb-4 fw-semibold text-body-emphasis">
        Корзина <span class="visually-hidden">RAFY WALLS — оформление заказа на обои в Алматы</span>
    </h1>


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
                <tr data-variant-id="{{ $item['variant_id'] }}">
                    <td>
                        <div class="d-flex align-items-center">
                            @if($item['image'])
                            <a href="{{ route('product.show', $item['product']->id) }}">
                                <img src="{{ asset('storage/' . $item['image']) }}"
                                    alt="{{ $item['product']->name }}"
                                    class="me-3"
                                    style="height: 80px; width: 80px; object-fit: cover; cursor: pointer;">
                            </a>
                            @endif
                            <div>
                                <a href="{{ route('product.show', $item['product']->id) }}"
                                    class="fw-semibold text-decoration-none text-dark">
                                    {{ $item['product']->name }}
                                </a>
                                <div class="text-muted small">Артикул: {{ $item['variant']->sku }}</div>
                                <div class="text-muted small">Оттенок: {{ $item['variant']->color }}</div>
                            </div>
                        </div>

                    </td>
                    <td class="text-muted">
                        @if($item['price'] == 0)
                        <span class="d-flex align-items-center" style="font-size: 0.9rem; font-weight: 500; color: #6c757d;">
                            <i class="bi bi-info-circle me-2" style="font-size: 1rem; color: #6c757d;"></i>
                            Уточните цену в WhatsApp
                        </span>
                        @else
                        {{ number_format($item['price'], 2) }} ₸
                        @endif
                    </td>

                    <td>
                        <input type="number"
                            name="quantity"
                            value="{{ $item['quantity'] }}"
                            min="1"
                            data-variant-id="{{ $item['variant_id'] }}"
                            class="form-control form-control-sm quantity-input"
                            style="width: 70px;">
                    </td>
                    <td class="text-muted item-total">
                        @if($item['price'] == 0)
                        <span style="font-size: 0.9rem; font-weight: 500; color: #6c757d;">—</span>
                        @else
                        {{ number_format($item['total'], 2) }} ₸
                        @endif
                    </td>

                    <td>
                        <form action="{{ route('cart.remove', $item['variant_id']) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-light border-0 text-danger" title="Удалить">✕</button>
                        </form>
                    </td>
                </tr>
                @endforeach

                {{-- Итог --}}
                <tr class="bg-light">
                    <td colspan="3" class="text-end fw-semibold">Итого:</td>
                    <td colspan="2" class="fw-bold cart-total">
                        @if(collect($cartItems)->contains(fn($i) => $i['price'] == 0))
                        <span class="text-muted" style="font-size: 0.95rem;">
                            Уточните цену в WhatsApp
                        </span>
                        @else
                        {{ number_format($total, 2) }} ₸
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="d-md-none">
        @foreach($cartItems as $item)
        <div class="card mb-3 border rounded-3 shadow-sm" data-variant-id="{{ $item['variant_id'] }}">
            <div class="card-body">
                <div class="d-flex gap-3">
                    @if($item['image'])
                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['product']->name }}" class="rounded-2" style="width: 80px; height: 80px; object-fit: cover;">
                    @endif
                    <div class="flex-grow-1">
                        <div class="fw-semibold mb-1">{{ $item['product']->name }}</div>
                        <div class="text-muted small mb-1">Артикул: {{ $item['variant']->sku }}</div>
                        <div class="text-muted small mb-1">Оттенок: {{ $item['variant']->color }}</div>
                        <div class="text-muted small mb-1">
                            Цена:
                            @if($item['price'] == 0)
                            <span class="d-flex align-items-center" style="font-size: 0.85rem; font-weight: 500; color: #6c757d;">
                                <i class="bi bi-info-circle me-1" style="font-size: 0.9rem; color: #6c757d;"></i>
                                Уточните цену в WhatsApp
                            </span>
                            @else
                            {{ number_format($item['price'], 2) }} ₸
                            @endif
                        </div>
                        <div class="text-muted small">
                            Сумма:
                            @if($item['price'] == 0)
                            <span style="font-size: 0.85rem; font-weight: 500; color: #6c757d;">—</span>
                            @else
                            <span class="item-total">{{ number_format($item['total'], 2) }} ₸</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center mt-3 gap-2">
                    <input type="number"
                        name="quantity"
                        value="{{ $item['quantity'] }}"
                        min="1"
                        data-variant-id="{{ $item['variant_id'] }}"
                        class="form-control form-control-sm quantity-input"
                        style="width: 70px;">

                    <form action="{{ route('cart.remove', $item['variant_id']) }}" method="POST" class="ms-auto">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger w-100">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Итог для мобилы --}}
        <div class="text-end fw-semibold fs-5 mt-3">
            Итого:
            @if(collect($cartItems)->contains(fn($i) => $i['price'] == 0))
            <span class="text-muted" style="font-size: 0.95rem;">
                Уточните цену в WhatsApp
            </span>
            @else
            <span class="cart-total">{{ number_format($total, 2) }} ₸</span>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <a href="{{ route('checkout') }}" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm">
            Оформить заказ
        </a>
    </div>
    @endif
</div>

<!-- Универсальное уведомление -->
<div id="toast-message" class="position-fixed top-0 start-50 translate-middle-x mt-4 z-1050 d-none" style="max-width: 90%; width: 350px;">
    <div id="toast-inner" class="d-flex align-items-center gap-2 px-4 py-3 rounded shadow text-white bg-dark animate-fade-in-down">
        <svg id="toast-icon" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0" width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
            <path id="toast-icon-path" d="" />
        </svg>
        <span id="toast-text" class="flex-grow-1 small">Сообщение</span>
    </div>
</div>



<style>
    @keyframes fade-in-down {
        0% {
            opacity: 0;
            transform: translateY(-20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-down {
        animation: fade-in-down 0.4s ease-out;
    }

    .fade-out {
        opacity: 0;
        transition: opacity 0.4s ease;
    }
</style>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        function showToast(message = 'Уведомление', type = 'success') {
            const box = document.getElementById('toast-message');
            const inner = document.getElementById('toast-inner');
            const text = document.getElementById('toast-text');
            const path = document.getElementById('toast-icon-path');

            text.textContent = message;
            inner.classList.remove('bg-dark', 'bg-danger');

            if (type === 'success') {
                inner.classList.add('bg-dark');
                path.setAttribute('d', 'M16.707 5.293a1 1 0 010 1.414l-7.414 7.414a1 1 0 01-1.414 0L3.293 9.414a1 1 0 111.414-1.414L8 11.586l7.293-7.293a1 1 0 011.414 0z');
            } else {
                inner.classList.add('bg-danger');
                path.setAttribute('d', 'M4.646 4.646a.5.5 0 01.708 0L10 9.293l4.646-4.647a.5.5 0 01.708.708L10.707 10l4.647 4.646a.5.5 0 01-.708.708L10 10.707l-4.646 4.647a.5.5 0 01-.708-.708L9.293 10 4.646 5.354a.5.5 0 010-.708z');
            }

            box.classList.remove('d-none');

            setTimeout(() => {
                box.classList.add('opacity-0');
            }, 2800);

            setTimeout(() => {
                box.classList.add('d-none');
                box.classList.remove('opacity-0');
            }, 4000);
        }

        function formatNumber(number) {
            return Number(number).toLocaleString('ru-RU', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', () => {
                const variantId = input.dataset.variantId;
                const quantity = parseInt(input.value);

                fetch(`/cart/update/${variantId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            quantity
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const container = input.closest('tr') || input.closest('.card');
                            const itemTotalEl = container.querySelector('.item-total');
                            const cartTotalEls = document.querySelectorAll('.cart-total');

                            // Обновляем сумму по позиции
                            if (itemTotalEl) {
                                if (data.itemPrice == 0) {
                                    itemTotalEl.textContent = '—';
                                } else {
                                    itemTotalEl.textContent = formatNumber(data.itemTotal) + ' ₸';
                                }
                            }

                            // Обновляем общий итог
                            cartTotalEls.forEach(el => {
                                if (data.hasZeroPrices) {
                                    el.innerHTML = '<span class="text-muted" style="font-size: 0.95rem;">Уточните цену в WhatsApp</span>';
                                } else {
                                    el.textContent = formatNumber(data.cartTotal) + ' ₸';
                                }
                            });

                            showToast('Количество обновлено', 'success');
                        } else {
                            showToast(data.error || 'Ошибка при обновлении количества.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('Ошибка обновления корзины:', err);
                        showToast('Ошибка связи с сервером. Попробуйте позже.', 'error');
                    });
            });
        });
    });
</script>
@endsection