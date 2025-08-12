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
            Скидка 5% при онлайн-заказе! <br> Закажите через сайт и получите подтверждение в WhatsApp.
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
        .banner-images {
            display: flex;
            width: 100%;
            height: 60vh;
            min-height: 300px;
            max-height: 530px;
        }

        .banner-image {
            flex: 1 1 33.333%;
        }

        .banner-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            padding: 20px;
            max-width: 800px;
            width: 90%;
            text-align: center;
        }

        .promo-text {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .banner-text h2 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .banner-text p {
            font-size: 1.2rem;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        /* Планшеты */
        @media (max-width: 991px) {
            .banner-text h2 {
                font-size: 2rem;
            }

            .banner-text p {
                font-size: 1rem;
            }

            .promo-text {
                font-size: 1rem;
            }
        }

        /* Телефоны */
        @media (max-width: 768px) {
            .banner-images {
                flex-direction: column;
                height: auto;
            }

            .banner-image {
                flex: none;
                height: 350px;
            }

            .hide-on-mobile {
                display: none !important;
            }

            .banner-text {
                padding: 15px;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                max-width: 90%;
                text-align: center; /* центрируем весь текст */
            }

            .banner-text h2 {
                font-size: 1.6rem;
            }

            .banner-text p {
                font-size: 0.95rem;
            }

            .promo-text {
                font-size: 0.95rem;
                margin-bottom: 20px;
            }
        }

        /* Очень маленькие экраны */
        @media (max-width: 480px) {
            .banner-image {
                height: 400px;
            }

            .banner-text h2 {
                font-size: 1.4rem;
            }

            .banner-text p,
            .promo-text {
                font-size: 0.9rem;
            }
        }
    </style>
</section>
