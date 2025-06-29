@extends('layouts.main')

@section('title', 'Как оформить заказ')

@section('content')
<div class="container py-5 px-3 px-md-4">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-dark">Как оформить заказ?</h1>
        <p class="fs-5 text-muted">Всего 5 шагов — и мы начнём собирать ваш заказ</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">

            {{-- Шаги оформления --}}
            <div class="steps mb-5">
                <div class="step-block">
                    <div class="step-number">1</div>
                    <div class="step-text">Перейдите в <a href="{{ route('catalog') }}">каталог</a> и выберите товар.</div>
                </div>
                <div class="step-block">
                    <div class="step-number">2</div>
                    <div class="step-text">Нажмите <strong>«В корзину»</strong> возле нужного товара.</div>
                </div>
                <div class="step-block">
                    <div class="step-number">3</div>
                    <div class="step-text">Перейдите в <a href="{{ route('cart') }}">корзину</a> и нажмите <strong>«Оформить заказ»</strong>.</div>
                </div>
                <div class="step-block">
                    <div class="step-number">4</div>
                    <div class="step-text">Заполните имя и номер телефона.</div>
                </div>
                <div class="step-block">
                    <div class="step-number">5</div>
                    <div class="step-text"><strong>Подтвердите заказ</strong> через WhatsApp.</div>
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
                   class="btn btn-success btn-lg px-4 py-2 rounded-pill shadow-sm w-100 w-md-auto"
                   target="_blank">
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
        padding: 1.25rem 1.5rem;
        font-size: 1.15rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        flex-wrap: nowrap;
    }

    .step-number {
        width: 40px;
        height: 40px;
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

    .step-text {
        word-break: break-word;
        flex: 1;
        min-width: 0;
    }

    .confirm-box {
        background-color: #fff8e1;
        border: 1px solid #ffe58f;
        border-radius: 20px;
        padding: 2rem 1.5rem;
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

    @media (max-width: 576px) {
        .step-block {
            font-size: 1rem;
            padding: 1rem;
            flex-direction: row;
            flex-wrap: wrap;
        }

        .step-number {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }

        .confirm-box {
            padding: 1.5rem 1rem;
        }

        .confirm-box .btn-success {
            font-size: 1rem;
            padding: 0.75rem 1rem;
        }
    }
</style>

@include('partials.footer')
@endsection
