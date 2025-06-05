<section class="info-section py-5 bg-white" id="product-info">
    <div class="container px-2 px-md-4" style="max-width: 1000px;">
        <h2 class="section-title mb-4">Обои, которые создают уют и легко справляются с повседневностью</h2>

        <div class="section-text">
            <p>В нашем магазине вы найдёте только качественные <strong>моющиеся обои</strong>— практичное решение для тех, кто ценит комфорт, эстетику и долговечность.</p>

            <p>Мы предлагаем широкий выбор обоев <strong>российского, китайского и узбекского производства</strong>, проверенных временем и нашими довольными клиентами. В ассортименте — разнообразие дизайнов: от лаконичной классики до современных трендов, от нежных пастельных до ярких акцентов.</p>

            <p>Все обои легко моются, устойчивы к истиранию и подходят как для жилых, так и для общественных помещений. Это идеальный выбор для кухни, прихожей, детской или любой другой комнаты, где важна практичность без потери красоты.</p>

            <p><strong>Приходите в наш магазин — увидите всё своими глазами и найдёте именно то, что подойдёт вашему интерьеру.</strong></p>
        </div>

        @if (!isset($showButton) || $showButton)
        <div class="mt-3">
            <a href="{{ route('catalog') }}" class="btn-primary btn-custom">Каталог</a>
        </div>
        @endif
    </div>
</section>

<style>
    /* about-products */

    .info-section {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }

    .section-title {
        font-weight: 700;
        /* чуть жирнее */
        font-size: 3rem;
        /* увеличен размер */
        text-align: left;
        max-width: 800px;
    }

    .section-text p {
        font-size: 1.3rem;
        line-height: 1.7;
        font-weight: 400;
        margin-bottom: 0.8rem;
        /* Уменьшенные промежутки */
        text-align: left;
    }

    .btn-custom {
        background-color: #01142f;
        color: white;
        border: none;
        padding: 18px 50px;
        /* увеличенный размер кнопки */
        font-size: 1rem;
        /* крупнее текст */
        font-weight: 600;
        border-radius: 30px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }

    .btn-custom:hover {
        background-color: #01142f;
        color: white;
        text-decoration: none;
        box-shadow: 0 6px 18px rgba(0, 86, 179, 0.4);
        transform: translateY(-2px);
        background-color: #02214b;
    }
</style>