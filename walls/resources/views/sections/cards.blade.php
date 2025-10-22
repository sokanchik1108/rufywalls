<!-- Подключаем шрифты -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@400;500&display=swap" rel="stylesheet">

<section class="rafy-walls-section" style="background-color:#f9f6f2; padding-top:5rem;padding-bottom:5rem;">
    <div class="container px-2 px-md-0 mb-3">
        <!-- Заголовок -->
        <div class="rafy-header text-center">
            <h2 class="rafy-main-title">Новые коллекции RAFY WALLS</h2>
            <p class="rafy-subtitle">
                Следите за последними трендами интерьера с новинками сезона — стильные, качественные и эксклюзивные коллекции для вдохновения
            </p>
        </div>

        <!-- Карточки -->
        <div class="row g-2 g-md-3">
            @foreach ($variants as $variant)
            @php
            $product = $variant->product;
            $images = json_decode($variant->images, true);
            $imageIndex = isset($images[6]) ? 6 : 0;
            $imagePath = !empty($images) && isset($images[$imageIndex])
            ? asset('storage/' . $images[$imageIndex])
            : asset('images/no-image.jpg');
            @endphp
            <div class="col-6 col-md-4 col-lg-4">
                <a href="{{ route('product.show', $product->id) }}" class="rafy-card-link">
                    <div class="rafy-card-square">
                        <div class="rafy-status">{{ $product->status }}</div>
                        <div class="rafy-img-wrapper">
                            <img src="{{ $imagePath }}" alt="{{ $product->name }}" class="rafy-card-img">
                        </div>
                        <div class="rafy-hover-text">
                            <p class="rafy-articul">{{ $variant->sku ?? '---' }}</p>
                            <div class="rafy-divider"></div>
                            <h5 class="rafy-name">{{ $product->name ?? 'Название товара' }}</h5>
                        </div>
                        <div class="rafy-overlay"></div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <!-- Кнопка -->
        <div class="text-center mt-3">
            <a href="{{ route('catalog') }}" class="rafy-all-btn">Все новинки</a>
        </div>
    </div>
</section>

<style>
    /* Кнопка "Все новинки" */
    .rafy-all-btn {
        background-color: #01142f;
        color: white;
        border: none;
        padding: 12px 32px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin-top: 10px;
    }

    .rafy-all-btn:hover {
        color: white;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);
        transform: translateY(-2px);
        background-color: #02214b;
    }

    /* Заголовок */
    .rafy-header {
        margin-bottom: 2rem;
    }

    .rafy-main-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.9rem;
        letter-spacing: 1.5px;
        font-weight: 600;
        color: black;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .rafy-subtitle {
        font-size: 1rem;
        letter-spacing: 0.2px;
        line-height: 1.4;
        margin: 0 auto;
        font-weight: 500;
        text-align: center;
        max-width: 1300px;
        color: #333;
    }

    /* Карточки */
    .rafy-card-square {
        position: relative;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        transition: transform .3s ease, box-shadow .3s ease;
        cursor: pointer;
        width: 100%;
        aspect-ratio: 1 / 1;
        border-radius: 0;
    }

    .rafy-card-square:hover {
        transform: translateY(-1.5px);
        box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
    }

    .rafy-img-wrapper {
        overflow: hidden;
        position: absolute;
        inset: 0;
    }

    .rafy-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .25s ease;
    }

    .rafy-card-square:hover .rafy-card-img {
        transform: scale(1.02);
    }

    .rafy-status {
        position: absolute;
        top: 8px;
        left: 50%;
        transform: translateX(-50%);
        background: red;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 300;
        padding: 2px 5px;
        z-index: 3;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .rafy-hover-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        opacity: 0;
        transition: opacity .25s ease, transform .25s ease;
        transform: scale(0.95);
        z-index: 2;
        text-align: center;
        position: absolute;
        inset: 0;
    }

    .rafy-card-square:hover .rafy-hover-text {
        opacity: 1;
        transform: scale(1);
    }

    .rafy-articul {
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
        font-family: 'Playfair Display', serif;
    }

    .rafy-divider {
        width: 100px;
        height: 1px;
        background: #01142f;
        margin: 4px auto;
    }

    .rafy-name {
        font-size: 0.8rem;
        font-weight: 500;
        margin: 0;
        font-family: 'Playfair Display', serif;
        letter-spacing: 1.2px;
        text-shadow: 0 1px 6px rgba(0, 0, 0, 0.5);
    }

    .rafy-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.5), transparent);
        opacity: 0;
        transition: opacity .25s ease;
        z-index: 1;
    }

    .rafy-card-square:hover .rafy-overlay {
        opacity: 1;
    }

    /* Ограничение количества карточек */
    @media (min-width: 992px) {

        /* На десктопах — только 3 карточки */
        .rafy-walls-section .row>div:nth-child(n+4) {
            display: none;
        }
    }

    @media (max-width: 991.98px) {

        /* На телефонах и планшетах — до 6 карточек */
        .rafy-walls-section .row>div:nth-child(n+7) {
            display: none;
        }
    }

    /* Адаптивность */
    @media (max-width: 991.98px) {
        .rafy-main-title {
            font-size: 1.6rem;
            letter-spacing: 2px;
        }

        .rafy-subtitle {
            font-size: 0.95rem;
            max-width: 500px;
        }
    }

    @media (max-width: 575.98px) {
        .rafy-main-title {
            font-size: 1.4rem;
            letter-spacing: 1.3px;
        }

        .rafy-subtitle {
            font-size: 0.9rem;
        }

        .rafy-articul {
            font-size: 0.8rem;
        }

        .rafy-name {
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .rafy-status {
            font-size: 0.6rem;
            padding: 2px 4px;
        }

        .rafy-all-btn {
            font-size: 0.85rem;
            padding: 10px 28px;
        }
    }
</style>