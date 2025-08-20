<!-- AOS Library (для анимаций) -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init();
</script>

<section class="info-section py-5" id="product-info">
    <div class="container px-3 px-md-4" style="max-width: 1000px;">
        <h2 class="section-title mb-4 text-start" data-aos="fade-up">
            Обои, которые создают уют и легко справляются с повседневностью
        </h2>

        <div class="section-text" data-aos="fade-up" data-aos-delay="100">
            <p>
                В нашем магазине вы найдёте только качественные <strong>моющиеся обои</strong> —
                практичное решение для тех, кто ценит комфорт, эстетику и долговечность.
            </p>

            <p>
                Мы предлагаем широкий выбор обоев <strong>российского, китайского и узбекского производства</strong>,
                проверенных временем и нашими довольными клиентами. В ассортименте — разнообразие дизайнов:
                от лаконичной классики до современных трендов, от нежных пастельных до ярких акцентов.
            </p>

            <p>
                Все обои легко моются, устойчивы к истиранию и подходят как для жилых, так и для общественных помещений.
                Это идеальный выбор для кухни, прихожей, детской или любой другой комнаты, где важна практичность без потери красоты.
            </p>

            <p><strong>Приходите в наш магазин — увидите всё своими глазами и найдёте именно то, что подойдёт вашему интерьеру.</strong></p>
        </div>

        <!-- Блок преимуществ -->

        <!-- Кнопка -->
        <div class="mt-5 text-start" data-aos="fade-up" data-aos-delay="300">
            <a href="{{ route('catalog') }}" class="btn-primary btn-custom">Каталог</a>
        </div>
    </div>
</section>

<style>
    /* Общий стиль блока */
    .info-section {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        background-color: #fdfdfd;
        /* Светлый фон */
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
    }

    /* Заголовок */
    .section-title {
        font-weight: 700;
        font-size: 2rem;
        line-height: 1.3;
        text-align: left;
    }

    /* Текст */
    .section-text p {
        font-size: 1.05rem;
        line-height: 1.75;
        font-weight: 400;
        margin-bottom: 1rem;
        text-align: justify;
        letter-spacing: 0.2px;
    }

    /* Подзаголовок Call to Action */
    .cta-subtext {
        font-size: 1rem;
        color: #666;
    }

    /* Кнопка прежнего цвета */
    .btn-custom {
        background-color: #01142f;
        /* Оригинальный цвет */
        color: white;
        border: none;
        padding: 13px 34px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 30px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-custom:hover {
        color: white;
        text-decoration: none;
        box-shadow: 0 6px 18px rgba(0, 86, 179, 0.4);
        transform: translateY(-2px);
        background-color: #02214b;
    }

    .btn-custom:active {
        transform: scale(0.97);
        box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);
    }

    /* Иконки */
    .icon {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
    }

    .feature-item h5 {
        margin-bottom: 0.3rem;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .feature-item p {
        margin: 0;
        font-size: 0.95rem;
        color: #555;
    }

    /* Планшеты */
    @media (min-width: 768px) {
        .section-title {
            font-size: 2.2rem;
        }

        .section-text p {
            font-size: 1.15rem;
        }

        .btn-custom {
            padding: 15px 42px;
        }
    }

    /* Десктоп */
    @media (min-width: 992px) {
        .section-title {
            font-size: 2.4rem;
        }

        .btn-custom {
            padding: 16px 48px;
            font-size: 1.05rem;
        }
    }

    /* Мобильные */
    @media (max-width: 576px) {
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 0.8rem;
        }

        .section-text p {
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 0.7rem;
        }

        .btn-custom {
            padding: 11px 24px;
            font-size: 0.95rem;
            border-radius: 24px;
        }

        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .feature-item {
            flex-direction: column;
            text-align: left;
        }

        .icon {
            margin-bottom: 0.5rem;
        }
    }
</style>