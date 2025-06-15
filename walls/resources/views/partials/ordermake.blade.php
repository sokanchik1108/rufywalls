@extends('layouts.main')

@section('title', 'Как оформить заказ')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-dark">Как оформить заказ ?</h1>
        <p class="fs-5 text-muted">Всего 5 шагов — и мы начнём собирать ваш заказ</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Шаги оформления --}}
            <div class="steps mb-5">
                <div class="step-block">
                    <div class="step-number">1</div>
                    Перейдите в <a href="{{ route('catalog') }}">каталог</a> и выберите товар.
                </div>
                <div class="step-block">
                    <div class="step-number">2</div>
                    Нажмите <strong>«В корзину»</strong> возле нужного товара.
                </div>
                <div class="step-block">
                    <div class="step-number">3</div>
                    Перейдите в <a href="{{ route('cart') }}">корзину</a> и нажмите <strong>«Оформить заказ»</strong>.
                </div>
                <div class="step-block">
                    <div class="step-number">4</div>
                    Заполните имя и номер телефона.
                </div>
                <div class="step-block">
                    <div class="step-number">5</div>
                    <strong>Подтвердите заказ</strong> через WhatsApp.
                </div>
            </div>

            {{-- Подтверждение через WhatsApp --}}
            <div class="confirm-box mb-5 text-center">
                <div class="mb-3">
                    <i class="bi bi-whatsapp fs-1 text-success"></i>
                </div>
                <h5 class="fw-bold mb-2 text-dark">Подтвердите заказ в WhatsApp</h5>
                <p class="mb-3 text-dark">
                    <u>Без подтверждения</u> заказ не будет обработан. Просто нажмите кнопку ниже и отправьте сообщение.
                </p>
                <a href="https://wa.me/77077121255?text=Здравствуйте,%20я%20хочу%20подтвердить%20заказ"
                   class="btn btn-success btn-lg px-4 rounded-pill shadow-sm" target="_blank">
                    📲 Подтвердить через WhatsApp
                </a>
            </div>

        </div>
    </div>
</div>

{{-- Стили --}}
<style>
    body {
        background-color: #f8f9fa;
    }

    .steps {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .step-block {
        background-color: #ffffff;
        border: 1px solid #e3e3e3;
        border-radius: 16px;
        padding: 1.5rem 2rem;
        font-size: 1.25rem;
        position: relative;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    .step-number {
        width: 42px;
        height: 42px;
        background-color: #01142f;
        color: #fff;
        font-weight: bold;
        font-size: 1.1rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .confirm-box {
        background-color: #fff8e1;
        border: 1px solid #ffe58f;
        border-radius: 20px;
        padding: 2rem;
        transition: transform 0.2s ease;
    }

    .confirm-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .confirm-box .btn-success {
        font-weight: 600;
        font-size: 1.1rem;
        background-color: #28a745;
        border: none;
    }

    .confirm-box .btn-success:hover {
        background-color: #218838;
    }

    a {
        color: #01142f;
        text-decoration: underline;
    }

    a:hover {
        color: #02214b;
    }
</style>
@endsection
