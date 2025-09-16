<section class="rafy-walls-section py-5 bg-light">
    <div class="container my-5">
        <h2 class="text-center mb-4 rafy-title">Обои от RAFY WALLS</h2>
        <p class="text-center rafy-subtitle">Премиум материалы и уникальный дизайн для создания уютного и модного пространства в вашем доме.</p>
        <div class="row g-4"> @foreach ($variants as $variant) @php $product = $variant->product; $images = json_decode($variant->images, true); @endphp <div class="col-md-4">
                <div class="rafy-card-lux"> <!-- Фото с эффектами -->
                    <div class="rafy-image-wrapper"> <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $product->name }}" class="rafy-card-img"> <!-- Красивый оверлей -->
                        <div class="rafy-overlay">
                            <div class="rafy-overlay-content">
                                <h5 class="rafy-overlay-title">{{ $product->name ?? 'Название товара' }}</h5>
                                <p class="rafy-overlay-price">{{ number_format($product->sale_price, 0, ',', ' ') }} ₸</p> <a href="{{ route('product.show', $product->id) }}" class="btn rafy-btn-glass">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> @endforeach </div>
    </div>
</section>

<style>
    /* Заголовок */
    .rafy-title {
        font-size: 2.8rem;
        /* было 3.5rem */
        font-weight: 600;
        color: #1a1a1a;
        text-align: center;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        position: relative;
        font-family: 'Georgia', serif;
    }

    .rafy-title::after {
        content: "";
        display: block;
        width: 64px;
        /* было 80px */
        height: 1.6px;
        background-color: #01142f;
        margin: 12.8px auto 0 auto;
        border-radius: 2px;
    }

    /* Подзаголовок */
    .rafy-subtitle {
        font-size: 1rem;
        /* было 1.2rem */
        font-weight: 300;
        color: #555;
        text-align: center;
        max-width: 544px;
        /* было 680px */
        margin: 0 auto 48px auto;
        font-family: 'Helvetica Neue', sans-serif;
        line-height: 1.6;
        padding: 0 8px;
    }

    /* Карточка */
    .rafy-card-lux {
        position: relative;
        border-radius: 17.6px;
        /* было 22px */
        overflow: hidden;
        box-shadow: 0 9.6px 28px rgba(0, 0, 0, 0.15);
        transition: transform .4s ease, box-shadow .4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .rafy-card-lux:hover {
        transform: translateY(-9.6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.25);
    }

    /* Фото */
    .rafy-card-img {
        width: 100%;
        height: 400px;
        /* было 400px */
        object-fit: cover;
        transition: transform 0.6s ease;
        display: block;
    }

    /* Оверлей */
    .rafy-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 40%, rgba(0, 0, 0, 0) 100%);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        opacity: 0;
        transition: opacity .5s ease;
        padding: 20px;
        /* было 25px */
        border-radius: 17.6px;
    }

    .rafy-card-lux:hover .rafy-overlay {
        opacity: 1;
    }

    .rafy-overlay-content {
        text-align: center;
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12.8px;
        /* было 16px */
        padding: 16px 20px;
        width: 100%;
        max-width: 85%;
    }

    .rafy-overlay-title {
        font-size: 1rem;
        /* было 1.25rem */
        font-weight: 700;
        margin-bottom: 8px;
    }

    .rafy-overlay-price {
        font-size: 1rem;
        /* было 1.2rem */
        font-weight: 600;
        margin-bottom: 12px;
    }

    /* Кнопка */
    .rafy-btn-glass {
        padding: 8px 24px;
        /* было 10px 30px */
        border-radius: 24px;
        font-size: 0.76rem;
        /* было 0.95rem */
        font-weight: 600;
        color: #fff;
        background-color: #01142f;
        border: 1px solid #01142f;
        transition: background-color 0.3s ease, color 0.3s ease;
        display: inline-block;
        text-decoration: none;
        cursor: pointer;
    }

    .rafy-btn-glass:hover {
        background-color: #02204a;
        color: #f0f0f0;
        border-color: #02214b;
    }

    /* ------------------- Адаптивность ------------------- */
    /* Мобильные (до 576px) */
    @media (max-width: 575.98px) {
        .rafy-title {
            font-size: 1.6rem;
            /* было 2rem */
        }

        .rafy-subtitle {
            font-size: 0.9rem;
            max-width: 90%;
            margin-bottom: 32px;
        }

        .rafy-card-img {
            height: 370px;
            /* было 250px */
        }

        .rafy-btn-glass {
            padding: 6px 16px;
            font-size: 0.76rem;
        }
    }

    /* Планшеты (576px - 991.98px) */
    @media (min-width: 576px) and (max-width: 991.98px) {
        .rafy-title {
            font-size: 2rem;
        }

        .rafy-subtitle {
            font-size: 1rem;
            max-width: 80%;
            margin-bottom: 40px;
        }

        .rafy-card-img {
            height: 350px;
            /* было 320px */
        }

        .rafy-btn-glass {
            padding: 7px 20px;
            font-size: 0.8rem;
        }
    }

    /* Десктоп (от 992px) */
    @media (min-width: 992px) {
        .row.g-4>div {
            flex: 0 0 33.3333%;
            max-width: 33.3333%;
        }
    }

/* Скрываем все карточки, кроме первой, только в секции RAFY WALLS на мобильных */
@media (max-width: 575.98px) {
    .rafy-walls-section .row.g-4 > div:nth-child(n+2) {
        display: none;
    }
}

</style>