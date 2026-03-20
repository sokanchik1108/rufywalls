@extends('layouts.main')

@section('title', 'Как нас найти — адрес и контакты RAFY WALLS в Алматы')

@section('meta')
<meta name="description" content="Адрес и контакты магазина RAFY WALLS в Алматы. Загляните к нам и подберите обои, которые преобразят ваш интерьер.">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url()->current() }}">
@endsection

@section('content')
<style>
    body {
        background-color: #f8f9fa;
    }

    .card-custom {
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
        background: #fff;
    }

    .map-square {
        aspect-ratio: 1 / 1;
        width: 100%;
        overflow: hidden;
    }

    .map-square iframe {
        width: 100%;
        height: 100%;
    }

    .contact-box {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px;
        /* меньше padding для уменьшения высоты */
        font-size: 0.95rem;
        /* чуть меньше шрифт */
    }

    .contact-address {
        font-size: 1rem;
        font-weight: 500;
        color: #212529;
        line-height: 1.4;
    }

    .map-link {
        font-size: 0.85rem;
        color: gray;
        text-decoration: none;
    }

    .map-link:hover {
        text-decoration: underline;
    }

    .btn-main {
        background: #01142f;
        color: #fff;
        padding: 8px;
        font-weight: 600;
        transition: 0.25s;
        border-radius: 0px;
        font-size: 0.9rem;
    }

    .btn-main:hover {
        background: #02214b;
        color: #fff;
    }

    .divider {
        border-top: 1px solid #eee;
        margin: 15px 0;
    }

    .working-hours-inline {
        font-weight: 500;
        color: #212529;
        margin: 8px 0;
    }
</style>

<div class="container py-5 min-vh-100" itemscope itemtype="https://schema.org/Store">

    <div class="row g-4 justify-content-center align-items-stretch">

        {{-- КАРТА --}}
        <div class="col-md-4 d-flex">
            <div class="card-custom p-2 w-100 d-flex">

                <div class="map-square">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2924.308719181855!2d76.88354610000001!3d43.2725435!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3bcafd5b41935d%3A0x31948c477c473c35!2sRAFY%20WALLS!5e0!3m2!1sru!2skz!4v1700000000000!5m2!1sru!2skz"
                        allowfullscreen=""
                        loading="lazy"></iframe>
                </div>

            </div>
        </div>

        {{-- БЛОК --}}
        <div class="col-md-7 d-flex">
            <div class="card-custom contact-box w-100 h-100">

                <div>
                    <h4 style="font-family: 'Playfair Display', serif;font-weight: 300;font-size: 1.5rem;color: #01142f;letter-spacing: 1px;">
                        RAFY WALLS — АЛМАТЫ
                    </h4>
                    <p class="contact-address mb-2 mt-2" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                        <span itemprop="streetAddress">
                            Рыскулова Розыбакиева, рынок Сауран, новое здание №105 / №109 , Алматы / Казахстан
                        </span>
                    </p>

                    <p class="working-hours-inline mt-3">
                        <strong>График работы:</strong> Вт-Вс 09:00–17:00, Пн выходной
                    </p>

                    <div class="d-flex flex-column gap-3 mt-4">
                        <a href="https://2gis.kz/almaty/firm/70000001102986031" target="_blank" class="map-link">Открыть в 2ГИС</a>
                        <a href="https://www.google.com/maps/place/RAFY+WALLS/@43.2740961,76.8835461,1596m/data=!3m1!1e3!4m6!3m5!1s0x8e3bcafd5b41935d:0x31948c477c473c35!8m2!3d43.2725435!4d76.8853381!16s%2Fg%2F11yj_bl99f!5m1!1e2?entry=ttu&g_ep=EgoyMDI2MDMxNy4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="map-link">Открыть в Google Maps</a>
                        <a href="https://yandex.kz/maps/ru/org/rafy_walls/95584099711/?ll=76.885008%2C43.272481&z=17" target="_blank" class="map-link">Открыть в Яндекс.Картах</a>
                    </div>
                </div>

                <div>
                    <div class="divider"></div>

                    <p class="working-hours-inline"><strong>Связаться с нами:</strong> +7 (777) 355 5704</p>

                    <a href="https://wa.me/77773555704"
                        target="_blank"
                        class="btn btn-main w-100 mt-2">
                        Написать в WhatsApp
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

@include('partials.footer')
@endsection