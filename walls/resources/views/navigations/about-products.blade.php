@extends('layouts.main')

@section('title', 'О продукции')

@section('content')
<div class="container pt-5 mt-5 px-3 px-md-4 d-flex justify-content-center align-items-start min-vh-100" style="margin-bottom: 100px;"> 
    <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 800px;">
        <div class="card-body p-4 p-md-5">

            <div class="text-center mb-4">
                <h1 class="h3 fw-bold text-dark">
                    Обои, которые создают уют<br class="d-none d-md-block" />
                    и выдерживают повседневную жизнь
                </h1>
            </div>

            <p class="text-secondary fs-5 mb-4">
                В нашем магазине вы найдёте <strong>качественные моющиеся обои</strong> — практичный выбор для тех, кто ценит комфорт, стиль и долговечность.
            </p>

            <p class="text-secondary fs-5 mb-4">
                Мы предлагаем широкий ассортимент продукции <strong>из России, Узбекистана и Китая</strong>, проверенной временем и доверием наших клиентов. В коллекции — как <strong>лаконичная классика</strong>, так и <strong>современные дизайнерские решения</strong>: от нейтральных пастельных оттенков до ярких акцентных узоров.
            </p>

            <p class="text-secondary fs-5 mb-4">
                Наши обои не боятся влаги, легко очищаются и устойчивы к износу. Они отлично подходят <strong>для любых комнат и пространств</strong> — дома, в офисе, в общественных помещениях.
            </p>

            <p class="text-secondary fs-5 mb-4">
                <strong>Приходите в наш магазин</strong>, чтобы увидеть качество своими глазами и найти идеальные обои именно для вашего интерьера.
            </p>

            <div class="mt-5 pt-3 border-top">
                <h5 class="fw-semibold text-dark mb-3">Отзывы наших клиентов</h5>
                <p class="text-secondary fs-5 mb-0">
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
        h1.h3 {
            font-size: 1.5rem;
        }

        .fs-5 {
            font-size: 1.1rem;
        }

        .card-body {
            padding: 1.5rem !important;
        }
    }

    @media (max-width: 576px) {
        h1.h3 {
            font-size: 1.3rem;
        }

        .fs-5 {
            font-size: 1rem;
        }

        .card {
            border-radius: 12px;
        }
    }
</style>

@include('partials.footer')
@endsection
