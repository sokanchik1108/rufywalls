@extends('layouts.main')

@section('title', 'Доставка')

@section('content')
<div class="container pt-5 mt-5 d-flex justify-content-center align-items-start min-vh-100 px-2" style="margin-bottom: 200px;">
    <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 600px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h1 class="h3 fw-bold text-dark">Условия доставки</h1>
                <p class="text-muted">Выберите удобный для вас способ получения заказа</p>
            </div>

            <div class="mb-4">
                <h5 class="fw-semibold">Платная доставка</h5>
                <p class="text-secondary mb-0">
                    Доставка осуществляется по городу и области. Все расходы по доставке оплачиваются клиентом.
                    Стоимость зависит от расстояния и объёма заказа. Уточняйте подробности у менеджера.
                </p>
            </div>

            <div class="mb-4">
                <h5 class="fw-semibold">Самовывоз</h5>
                <p class="text-secondary mb-0">
                    Вы можете забрать заказ самостоятельно по адресу:
                    <br>
                    <a href="https://go.2gis.com/9VZVk" class="text-decoration-none text-primary" target="_blank">
                        Рыскулова розыбакиева, рынок Сауран, новое здание №109, Казахстан, Алматы
                    </a>
                </p>
                <p class="text-secondary mt-2 mb-0">Предварительно уточните готовность заказа по телефону.</p>
            </div>

            <div class="mb-4">
                <h5 class="fw-semibold">Способы оплаты</h5>
                <p class="text-secondary mb-0">
                    Вы можете оплатить заказ одним из следующих способов:
                </p>
                <ul class="text-secondary mt-2 ps-3 mb-0">
                    <li>Наличными — только в магазине при самовывозе</li>
                    <li>Переводом на Kaspi Gold</li>
                    <li>Переводом на карту Halyk Bank</li>
                </ul>
                <p class="text-secondary mt-2 mb-0">Реквизиты для перевода уточняйте у менеджера.</p>
            </div>

            <div class="text-center mt-4">
                <p class="fw-semibold mb-1">По всем вопросам:</p>
                <a href="tel:+77077121255" class="text-decoration-none text-primary d-block">+7 707 712 1255</a>
                <a href="https://wa.me/77077121255" target="_blank" class="text-decoration-none text-success">Написать в WhatsApp</a>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
@endsection
