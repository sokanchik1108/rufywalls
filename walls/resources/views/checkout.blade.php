@extends('layouts.main')

@section('title', 'Оформление заказа')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Оформление заказа</h1>

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
                    <img src="{{ asset('storage/' . $item['image']) }}" width="80" height="80" style="object-fit: cover;">
                    @else
                    <div class="bg-secondary text-white d-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
                        Нет фото
                    </div>
                    @endif
                    <div>
                        <div>{{ $item['product']->name }}</div>
                        <small class="text-muted">{{ $item['variant']->sku }} — {{ $item['quantity'] }} шт.</small>
                    </div>
                </div>
                <div class="fw-semibold">
                    {{ number_format($item['total'], 0, ',', ' ') }} ₸
                </div>
            </li>
            @endforeach
            <li class="list-group-item fw-bold d-flex justify-content-between">
                Итого:
                <span>{{ number_format($total, 0, ',', ' ') }} ₸</span>
            </li>
        </ul>

        {{-- Предупреждение о подтверждении в WhatsApp --}}
        {{-- Предупреждение о подтверждении в WhatsApp --}}
        <div class="alert alert-warning rounded-3 shadow-sm mb-4">
            <strong>Важно!</strong> После оформления заказа <u>не забудьте подтвердить его</u>, написав нам в
            <a href="https://wa.me/77077121255?text=Здравствуйте,%20я%20хочу%20подтвердить%20заказ" class="text-success fw-semibold" target="_blank">
                WhatsApp
            </a>.
            Без подтверждения заказ не будет обработан.
        </div>


        <button type="submit"
            class="custom-order-btn">
            🛒 Оформить заказ
        </button>

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



    </form>
</div>
@endsection