@extends('layouts.main')

{{-- 🔹 Title --}}
@section('title', 'Доставка заказов — RAFY WALLS в Алматы и Казахстане')

{{-- 🔹 Meta Description --}}
@section('meta_description', 'Узнайте о способах доставки заказов RAFY WALLS: курьерская доставка, самовывоз, сроки и условия по Алматы и всему Казахстану.')

{{-- 🔹 Дополнительные мета-теги --}}
@section('meta')
<meta name="description" content="@yield('meta_description')" />
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url()->current() }}">

{{-- 🔹 Open Graph для соцсетей --}}
<meta property="og:title" content="@yield('title')">
<meta property="og:description" content="@yield('meta_description')">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="RAFY WALLS">

{{-- 🔹 Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('title')">
<meta name="twitter:description" content="@yield('meta_description')">
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
                    Клиент оплачивает доставку самостоятельно.
                    Подробности уточняйте у менеджера.
                </p>
            </div>

            <div class="mb-4">
                <h5 class="fw-semibold">Самовывоз</h5>
                <p class="text-secondary mb-2">
                    Адреса для самовывоза:
                    <br>
                    <a href="https://2gis.kz/almaty/firm/70000001102986031?m=76.884534%2C43.272083%2F16" class="text-decoration-none text-primary" target="_blank">
                        Рыскулова Розыбакиева, рынок Сауран, новое здание №109, Алматы
                    </a>
                    <br>
                    <a href="https://2gis.kz/almaty/branches/70000001102986030/firm/70000001103994284/76.884749%2C43.272347" class="text-decoration-none text-primary d-inline-block mt-2" target="_blank">
                        Рыскулова Розыбакиева, рынок Сауран, новое здание №105, Алматы
                    </a>
                </p>
                <p class="text-secondary mt-2 mb-0">Предварительно уточните готовность заказа по телефону.</p>
            </div>

            <div class="mb-4">
                <h5 class="fw-semibold">Способы оплаты</h5>
                <p class="text-secondary mb-0">
                    Доступные способы оплаты:
                </p>
                <ul class="text-secondary mt-2 ps-3 mb-0">
                    <li>Наличными — только при самовывозе</li>
                    <li>Перевод на Kaspi Gold</li>
                    <li>Перевод на карту Halyk Bank</li>
                </ul>
                <p class="text-secondary mt-2 mb-0">Реквизиты уточняйте у менеджера.</p>
            </div>

            <div class="text-center mt-4">
                <p class="fw-semibold mb-1">По всем вопросам:</p>
                <a href="tel:+77077121255" class="text-decoration-none text-primary d-block">+7 707 712 12 55</a>
                <a href="https://wa.me/77077121255" target="_blank" class="text-decoration-none text-success">Написать в WhatsApp</a>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
@endsection
