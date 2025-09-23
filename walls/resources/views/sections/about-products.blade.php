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

            <p><strong>Приходите в наш магазин — увидите всё своими глазами и найдёте именно то, что подойдёт вашему интерьеру.</strong></p>
        </div>

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
        border-radius: 10.8px;
        /* 90% от 12px */
        box-shadow: 0 7.2px 18px rgba(0, 0, 0, 0.03);
        padding-top: 4.5rem;
        /* чуть меньше, чем py-5 */
        padding-bottom: 4.5rem;
    }

    /* Заголовок */
    .section-title {
        font-weight: 700;
        font-size: 1.8rem;
        /* 90% от 2rem */
        line-height: 1.3;
        text-align: left;
    }

    /* Текст */
    .section-text p {
        font-size: 1rem;
        /* 90% от 1.05rem */
        line-height: 1.65;
        font-weight: 500;
        margin-bottom: 0.9rem;
        text-align: justify;
        letter-spacing: 0.2px;
    }

    /* Кнопка */
    .btn-custom {
        background-color: #01142f;
        color: white;
        border: none;
        padding: 11.7px 30.6px;
        /* 90% от 13px 34px */
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 27px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-custom:hover {
        color: white;
        text-decoration: none;
        box-shadow: 0 5.4px 16.2px rgba(0, 86, 179, 0.4);
        transform: translateY(-2px);
        background-color: #02214b;
    }

    .btn-custom:active {
        transform: scale(0.97);
        box-shadow: 0 3.6px 10.8px rgba(0, 86, 179, 0.3);
    }

    /* Иконки */
    .icon {
        width: 36px;
        /* 90% от 40px */
        height: 36px;
        flex-shrink: 0;
    }

    /* Преимущества */
    .feature-item h5 {
        margin-bottom: 0.27rem;
        font-size: 0.99rem;
        font-weight: 600;
    }

    .feature-item p {
        margin: 0;
        font-size: 0.86rem;
        color: #555;
    }

    /* Медиа-запросы */
    @media (min-width: 768px) {
        .section-title {
            font-size: 1.98rem;
        }

        .section-text p {
            font-size: 1.03rem;
        }

        .btn-custom {
            padding: 13.5px 37.8px;
        }
    }

    @media (min-width: 992px) {
        .section-title {
            font-size: 2.16rem;
        }

        .btn-custom {
            padding: 14.4px 43.2px;
            font-size: 0.95rem;
        }
    }

    @media (max-width: 576px) {
        .section-title {
            font-size: 1.35rem;
            margin-bottom: 0.72rem;
        }

        .section-text p {
            font-size: 0.85rem;
            line-height: 1.4;
            margin-bottom: 0.63rem;
            font-weight: 450;
        }

        .btn-custom {
            padding: 9.9px 21.6px;
            font-size: 0.86rem;
            border-radius: 21.6px;
        }

        .container {
            padding-left: 0.9rem;
            padding-right: 0.9rem;
        }

        .feature-item {
            flex-direction: column;
            text-align: left;
        }

        .icon {
            margin-bottom: 0.45rem;
        }
    }
</style>