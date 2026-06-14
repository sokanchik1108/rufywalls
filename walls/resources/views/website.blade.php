@extends('layouts.main')

{{-- 🔹 Title --}}
@section('title', 'Обои в Алматы — виниловые, флизелиновые и моющиеся обои | KURBANOV WALLS')

{{-- 🔹 Meta Description --}}
@section('meta_description', 'Купить обои в Алматы в KURBANOV WALLS. Виниловые обои на флизелине, флизелиновые и моющиеся обои. Доставка по Казахстану, широкий выбор коллекций.')

{{-- 🔹 Meta --}}
@section('meta')
<link rel="canonical" href="{{ url()->current() }}">
<meta name="keywords" content="обои Алматы, купить обои Алматы, виниловые обои, флизелиновые обои, моющиеся обои, магазин обоев Казахстан, KURBANOV WALLS, Kurbanov Walls, kurbanovwalls, kurbanov walls, Курбанов Воллс">

<meta property="og:title" content="@yield('title')">
<meta property="og:description" content="@yield('meta_description')">
<meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
@endsection

@section('content')
<section class="info-section" id="product-info">

    <div class="text-content">


        <h1 class="title">
            Обои в Алматы — моющиеся виниловые обои на флизелине с доставкой по Казахстану
        </h1>

        <div class="section-text">


            <p>
                Мы предлагаем широкий выбор современных
                <strong>виниловых обоев на флизелине</strong> в Алматы.
                У нас вы можете купить обои для квартиры, дома и коммерческих помещений с доставкой по всему Казахстану.
            </p>

            <p>
                Все обои изготовлены из экологически чистых и премиальных материалов, безопасных для вашей семьи и окружающей среды, а также имеют сертификат качества, подтверждающий их надёжность и соответствие стандартам
            </p>

            <p>
                В каталоге представлены обои производства <strong>России, Китая и Узбекистана</strong>.
                Все коллекции проходят контроль качества и соответствуют современным стандартам.
            </p>

            <p>
                <strong>
                    Купить обои можно онлайн с быстрой доставкой по всему Казахстану —
                    удобно, безопасно и по доступным ценам.
                </strong>
            </p>

        </div>

        <div class="mt-5">
            <a href="{{ route('catalog') }}" class="btn-custom slide-in-right">
                Перейти в каталог →
            </a>
        </div>

    </div>
</section>

<style>
    .info-section {
        position: relative;
        background-image: url('{{ asset('images/11316-01 7.webp') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        padding: 5% 5% 0 8%;
    }

    .text-content {
        max-width: 880px;
        color: #000;
        text-align: left;
    }

    .section-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 1.2rem;
    }

    .section-text p {
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .btn-custom {
        background-color: #01142f;
        color: #fff;
        padding: 14px 36px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 30px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-custom:hover {
        background-color: #02214b;
        transform: translateY(-2px);
    }

    .slide-in-right {
        transform: translateX(100%);
        opacity: 0;
        animation: slideInRight 1.2s forwards;
        animation-delay: 0.5s;
        animation-timing-function: ease-out;
    }

    .title {
        font-size: 1.8rem;
        /* было больше — стало компактнее */
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 1.2rem;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Планшет */
    @media (max-width: 992px) {
        .section-title {
            font-size: 2rem;
        }

        .section-text p {
            font-size: 1rem;
        }

        .btn-custom {
            padding: 12px 30px;
        }
    }

    /* Мобильные устройства */
    @media (max-width: 768px) {
        .info-section {
            background-image: url('{{ asset('images/11212-05 5.webp') }}');
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: auto;
            padding: 3rem 6%;
        }

        .text-content {
            max-width: 95%;
        }

        .section-title {
            font-size: 1.8rem;
        }

        .section-text p {
            font-size: 1rem;
        }

        .btn-custom {
            padding: 12px 28px;
            font-size: 0.95rem;
        }

        .slide-in-right {
            transform: translateY(50px);
            animation: slideInUp 1.2s forwards;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    }

    @media (max-width: 480px) {
        .section-title {
            font-size: 1.5rem;
        }

        .section-text p {
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .title {
                font-size: 1rem;
                line-height: 1.3;
            }
        }
    }
</style>

@include('sections.cards')
@include('sections.about-us')
@include('sections.categories')
@include('partials.footer')

@endsection