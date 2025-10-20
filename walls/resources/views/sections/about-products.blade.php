<section class="info-section" id="product-info" style="background-image: url('{{ asset('images/главстрбаннер2.jpg') }}');">
    <div class="text-content" data-aos="fade-left">
        <!-- Заголовок секции -->
        <h2 class="section-title">
            Обои, которые создают уют и легко справляются с повседневностью
        </h2>

        <div class="section-text">
            <p>
                В нашем магазине вы найдёте только качественные <strong>моющиеся обои</strong> — практичное решение для тех, кто ценит комфорт, эстетику и долговечность.
            </p>

            <p>
                Мы предлагаем широкий выбор обоев <strong>российского, китайского и узбекского производства</strong>, проверенных временем и нашими довольными клиентами.
                В ассортименте — разнообразие дизайнов: от лаконичной классики до современных трендов, от нежных пастельных до ярких акцентов.
            </p>

            <p>
                Все обои <strong>изготовлены из экологически чистых и премиальных материалов</strong>, безопасных для вашей семьи и окружающей среды, а также имеют <strong>сертификат качества</strong>, подтверждающий их надёжность и соответствие стандартам.
            </p>

            <p>
                Все обои легко моются, устойчивы к истиранию и подходят как для жилых, так и для общественных помещений.
                Это идеальный выбор для кухни, прихожей, детской или любой другой комнаты, где важна практичность без потери красоты.
            </p>

            <p>
                <strong class="mobile-white">
                    Приходите в наш магазин — увидите всё своими глазами и найдёте именно то, что подойдёт вашему интерьеру.
                </strong>
            </p>
        </div>

        <div class="mt-4">
            <a href="{{ route('catalog') }}" class="btn-custom slide-in-right">Перейти в каталог &rarr;</a>
        </div>
    </div>
</section>

<style>
    .info-section {
        position: relative;
        /* путь к картинке */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh;
        display: flex;
        align-items: flex-start;
        /* текст чуть выше */
        justify-content: flex-end;
        /* текст справа */
        padding: 5% 5% 0 5%;
    }

    .text-content {
        max-width: 880px;
        /* ширина текста */
        color: #000;
        /* черный текст */
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

    /* Кнопка с плавным появлением справа */
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
        .section-text p {
            font-size: 1rem;
        }

        .section-title {
            font-size: 2rem;
        }

        .btn-custom {
            padding: 12px 30px;
            font-size: 0.95rem;
        }

        .info-section {
            padding: 5% 6% 0 5%;
        }
    }

    /* Мобильные устройства */
    @media (max-width: 768px) {
        .info-section {
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: auto;
            padding: 2rem 5%;
        }

        .mobile-white {
            color: #ffffff;
        }

        .text-content {
            max-width: 90%;
        }

        /* Кнопка появляется снизу */
        .slide-in-right {
            transform: translateY(50px);
            animation: slideInUp 1.2s forwards;
            animation-timing-function: ease-out;
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

        .section-title {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .section-text p {
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .btn-custom {
            padding: 12px 28px;
            font-size: 0.95rem;
        }
    }

    /* Очень маленькие экраны */
    @media (max-width: 480px) {
        .section-title {
            font-size: 1.5rem;
        }

        .section-text p {
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 1.2rem;
        }

        .btn-custom {
            padding: 10px 24px;
            font-size: 0.85rem;
        }
    }
</style>