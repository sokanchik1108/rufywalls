@extends('layouts.main')

@section('title', 'Доставка - RAFY WALLS')

@section('meta')
<meta name="description" content="Узнайте о способах доставки заказов RAFY WALLS: самовывоз, курьерская доставка, сроки и условия." />
@endsection


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
                    Мы отправляем заказы в любой город Казахстана удобным для вас способом — через транспортную компанию, курьерскую службу или такси по вашему выбору.
                    Мы не сотрудничаем с конкретными службами, поэтому клиент сам выбирает, как и через кого будет отправлен заказ.

                    Все расходы на доставку оплачиваются клиентом — включая доставку по городу и междугороднюю пересылку.

                    Подробности доставки уточняйте у менеджера.
                </p>
            </div>

            <div class="mb-4">
                <h5 class="fw-semibold">Самовывоз</h5>
                <p class="text-secondary mb-2">
                    Вы можете забрать заказ самостоятельно по адресам:
                    <br>
                    <a href="https://2gis.kz/almaty/firm/70000001102986031?m=76.884534%2C43.272083%2F16" class="text-decoration-none text-primary" target="_blank">
                        Рыскулова Розыбакиева, рынок Сауран, новое здание №109, Казахстан, Алматы
                    </a>
                    <br>
                    <a href="https://2gis.kz/almaty/branches/70000001102986030/firm/70000001103994284/76.884749%2C43.272347" class="text-decoration-none text-primary d-inline-block mt-2" target="_blank">
                        Рыскулова Розыбакиева, рынок Сауран, новое здание №105, Казахстан, Алматы
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