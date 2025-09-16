<section class="sale-banner" style="position: relative; width: 100%; overflow: hidden;">

    <!-- Изображения -->
    <div class="banner-images">
        <div class="banner-image">
            <img src="{{ asset('images/баннер2.webp') }}" alt="Обои RAFY WALLS">
        </div>
        <div class="banner-image hide-on-mobile">
            <img src="{{ asset('images/баннер6.webp') }}" alt="Обои RAFY WALLS">
        </div>
        <div class="banner-image hide-on-mobile">
            <img src="{{ asset('images/баннер3.webp') }}" alt="Обои RAFY WALLS">
        </div>
    </div>

    <!-- Затемняющий слой -->
    <div class="banner-overlay"></div>

    <!-- Текст -->
    <div class="banner-text">
        <div class="promo-text">
            Скидка 5% при онлайн-заказе! <br>
            Закажите через сайт и получите <strong style="color: #FFFFDD;">5% скидки</strong>. Подтверждение в — WhatsApp.
        </div>
        <h2>ПРЕВРАТИ СТЕНЫ В ИСКУССТВО</h2>
        <p>
            Обои, которые делают интерьер живым, тёплым и по-настоящему твоим.
        </p>
        <p>
            Мы не просто продаём обои — мы помогаем создавать пространство, в которое хочется возвращаться.
        </p>
    </div>

    <!-- Стили -->
    <style>
        /* Уменьшаем высоту баннера */
        .banner-images {
            display: flex;
            width: 100%;
            height: 45vh;
            /* было 60vh */
            min-height: 200px;
            /* было 300px */
            max-height: 400px;
            /* было 530px */
        }

        .banner-image {
            flex: 1 1 33.333%;
        }

        .banner-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hide-on-mobile {
            display: block;
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        .banner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            padding: 10px;
            /* было 20px */
            max-width: 700px;
            /* было 800px */
            width: 90%;
            text-align: center;
        }

        .banner-text h2 {
            font-size: 1.8rem;
            /* было крупнее */
            margin-bottom: 0.5rem;
        }

        .banner-text p {
            font-size: 1rem;
            line-height: 1.4;
            margin-bottom: 0.4rem;
        }

        .promo-text {
            font-size: 1rem;
            margin-bottom: 1rem;
        }



        /* Планшеты (средние экраны) */
        @media (max-width: 991px) {
            .banner-text {
                max-width: 600px;
                padding: 8px;
            }

            .banner-text h2 {
                font-size: 1.8rem;
            }

            .banner-text p {
                font-size: 1rem;
            }
        }

        /* Мобильные телефоны */
        @media (max-width: 576px) {
            .hide-on-mobile {
                display: none;
            }

            .banner-images {
                height: 40vh;
                max-height: 300px;
            }

            .banner-text {
                padding: 7px;
                max-width: 90%;
            }

            .banner-text h2 {
                font-size: 1.2rem;
                font-weight: bold;
                margin-bottom: 15px;
            }

            .banner-text p {
                font-size: 0.8rem;
                line-height: 1.3;
            }

            .promo-text {
                font-size: 0.8rem;
                padding: 4px 7px;
            }
        }
    </style>
</section>