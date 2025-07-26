@extends('layouts.main')

@section('title', 'О продукции - RAFY WALLS')

@section('meta')
<meta name="description" content="Узнайте больше о продукции RAFY WALLS — стильные, качественные обои для любого интерьера. Материалы, бренды, технологии.">
@endsection

@section('content')
<div class="container pt-5 mt-5 px-3 px-md-4 d-flex justify-content-center align-items-start min-vh-100" style="margin-bottom: 100px;">
    <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 800px;">
        <div class="card-body p-3 p-md-4">

            <div class="text-center mb-3">
                <h1 class="h4 fw-bold text-dark">
                    Обои, которые создают уют<br class="d-none d-md-block" />
                    и выдерживают повседневную жизнь
                </h1>
            </div>

            <p class="text-secondary fs-6 mb-3">
                В нашем магазине вы найдёте <strong>качественные моющиеся обои</strong> — практичный выбор для тех, кто ценит комфорт, стиль и долговечность.
            </p>

            <p class="text-secondary fs-6 mb-3">
                Мы предлагаем широкий ассортимент продукции <strong>из России, Узбекистана, Китая и Беларуси</strong>, проверенной временем и доверием наших клиентов. В коллекции — как <strong>лаконичная классика</strong>, так и <strong>современные дизайнерские решения</strong>: от нейтральных пастельных оттенков до ярких акцентных узоров.
            </p>

            <p class="text-secondary fs-6 mb-3">
                Наши обои не боятся влаги, легко очищаются и устойчивы к износу. Они отлично подходят <strong>для любых комнат и пространств</strong> — дома, в офисе, в общественных помещениях.
            </p>

            <p class="text-secondary fs-6 mb-3">
                <strong>Приходите в наш магазин</strong>, чтобы увидеть качество своими глазами и найти идеальные обои именно для вашего интерьера.
            </p>

            <div class="mt-4 pt-3 border-top">
                <h5 class="fw-semibold text-dark mb-2">Отзывы наших клиентов</h5>
                <p class="text-secondary fs-6 mb-0">
                    Ознакомьтесь с отзывами и результатами наших работ на нашей Instagram-странице:
                    <br>
                    <a href="https://www.instagram.com/rafy_walls" target="_blank" class="text-primary text-decoration-none">
                        @Rafy_walls
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>

{{-- Адаптивные стили --}}
<style>
    @media (max-width: 768px) {
        h1.h4 {
            font-size: 1.1rem;
        }

        h5.fw-semibold {
            font-size: 0.95rem;
        }

        .fs-6, p, .text-secondary {
            font-size: 0.85rem;
        }

        .card-body {
            padding: 1.25rem !important;
        }

        .container {
            padding-top: 1.5rem !important;
        }
    }

    @media (max-width: 576px) {
        h1.h4 {
            font-size: 1rem;
        }

        h5.fw-semibold {
            font-size: 0.9rem;
        }

        .fs-6, p, .text-secondary {
            font-size: 0.8rem;
        }
    }
</style>

@include('partials.footer')
@endsection
