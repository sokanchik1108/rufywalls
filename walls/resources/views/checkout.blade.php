@extends('layouts.main')

{{-- 🔹 Title --}}
@section('title', 'Оформление заказа — RAFY WALLS | Обои в Алматы')

{{-- 🔹 Meta Description (используется для SEO, Open Graph и Twitter) --}}
@section('meta_description', 'Оформите заказ на обои RAFY WALLS. Проверьте товары и подтвердите покупку в WhatsApp. Уют, качество и стиль — выбирайте RAFY WALLS.')

{{-- 🔹 Дополнительные мета-теги --}}
@section('meta')
<meta name="description" content="@yield('meta_description')" />
<meta name="robots" content="noindex, follow">
<link rel="canonical" href="{{ url('/checkout') }}">
@endsection


@section('content')
<div class="container py-5">

    {{-- SEO-заголовок --}}
    <h1 class="mb-4 fw-semibold text-body-emphasis">
        Оформление заказа <span class="visually-hidden">на обои RAFY WALLS в Алматы</span>
    </h1>

    {{-- 🔹 Предупреждение о подтверждении --}}
    <div class="alert alert-warning rounded-3 shadow-sm mb-4">
        <strong>Важно!</strong> После оформления заказа <u>подтвердите его</u>, написав нам в
        <a href="https://wa.me/77077121255?text=Здравствуйте,%20я%20хочу%20подтвердить%20заказ"
            class="text-success fw-semibold" target="_blank">
            WhatsApp
        </a>.
        Без подтверждения заказ не будет обработан.
        <br>
        <span class="d-flex align-items-center mt-2" style="font-size: 0.95rem; font-weight: 500;">
            <i class="bi bi-info-circle me-2" style="font-size: 1rem;"></i>
            Оплата производится во время подтверждения заказа в WhatsApp.
        </span>
    </div>

    {{-- 🔹 Форма оформления заказа --}}
    <form action="{{ route('checkout.submit') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" class="form-control" name="name" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Телефон</label>
            <input type="text" class="form-control" name="phone" required placeholder="+7 712 346 7890">
            @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Комментарий</label>
            <textarea name="comment" class="form-control" rows="3"></textarea>
        </div>

        <h4 class="mt-4">Ваш заказ</h4>
        <ul class="list-group mb-4">
            @foreach($cartItems as $item)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    @if ($item['image'])
                    <a href="{{ route('product.show', $item['product']->id) }}">
                        <img src="{{ asset('storage/' . $item['image']) }}"
                            width="80" height="80"
                            style="object-fit: cover; cursor: pointer;">
                    </a>
                    @else
                    <a href="{{ route('product.show', $item['product']->id) }}"
                        class="bg-secondary text-white d-flex justify-content-center align-items-center"
                        style="width: 80px; height: 80px; text-decoration: none; border-radius: 6px; cursor: pointer;">
                        Нет фото
                    </a>
                    @endif
                    <div>
                        <a href="{{ route('product.show', $item['product']->id) }}"
                            class="text-decoration-none text-dark fw-semibold">
                            {{ $item['product']->name }}
                        </a>
                        <br>
                        <small class="text-muted">{{ $item['variant']->sku }} — {{ $item['quantity'] }} шт.</small>
                    </div>
                </div>

                <div class="fw-semibold">
                    @if($item['price'] == 0)
                    <span class="d-flex align-items-center text-muted" style="font-size: 0.9rem; font-weight: 500;">
                        <i class="bi bi-info-circle me-1" style="font-size: 1rem; color: #6c757d;"></i>
                        Уточните цену в WhatsApp
                    </span>
                    @else
                    {{ number_format($item['total'], 0, ',', ' ') }} ₸
                    @endif
                </div>
            </li>
            @endforeach

            {{-- Итог --}}
            <li class="list-group-item fw-bold d-flex justify-content-between">
                Итого:
                @if(collect($cartItems)->contains(fn($i) => $i['price'] == 0))
                <span class="text-muted" style="font-size: 0.95rem;">Уточните цену в WhatsApp</span>
                @else
                <span>{{ number_format($total, 0, ',', ' ') }} ₸</span>
                @endif
            </li>
        </ul>

        <button type="submit" class="custom-order-btn">🛒 Оформить заказ</button>
    </form>

    <style>
        .custom-order-btn {
            background-color: #01142f;
            color: #fff;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(1, 20, 47, 0.3);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .custom-order-btn:hover {
            background-color: #02214b;
            transform: translateY(-2px);
        }

        .custom-order-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(1, 20, 47, 0.4);
        }
    </style>
</div>
@endsection