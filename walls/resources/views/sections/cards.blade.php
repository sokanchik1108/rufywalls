<!-- Подключаем Playfair Display и Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@400;500&display=swap" rel="stylesheet">

<section class="rafy-walls-section py-4 bg-light">
    <div class="container px-2 px-md-0 mb-4"> <!-- убрали text-center -->
        <!-- Заголовок -->
        <div class="rafy-header">
            <h2 class="rafy-main-title">Новинки RAFY WALLS</h2>
            <div class="rafy-title-line"></div>
            <p class="rafy-subtitle text-center">Следите за последними трендами интерьера с новинками сезона — стильные, качественные и эксклюзивные коллекции для вдохновения</p>
        </div>

        <!-- Карточки -->
        <div class="row g-2 g-md-4">
            @foreach ($variants as $variant)
            @php
            $product = $variant->product;
            $images = json_decode($variant->images, true);
            $imageIndex = isset($images[6]) ? 6 : 0;
            $imagePath = !empty($images) && isset($images[$imageIndex]) ? asset('storage/' . $images[$imageIndex]) : asset('images/no-image.jpg');
            @endphp
            <div class="col-6 col-md-4">
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

        <!-- После блока с карточками -->
        <div class="text-center mt-4">
            <a href="{{ route('catalog') }}" class="rafy-all-btn">Все новинки</a>
        </div>

    </div>
</section>

<style>
    .rafy-all-btn {
        background-color: #01142f;
        color: white;
        border: none;
        padding: 14px 38px;
        /* чуть больше, чем было 11.7px 30.6px */
        font-size: 1rem;
        /* увеличили с 0.9rem */
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin-top: 15px;
    }


    .rafy-all-btn:hover {
        color: white;
        text-decoration: none;
        box-shadow: 0 5.4px 16.2px rgba(0, 86, 179, 0.4);
        transform: translateY(-2px);
        background-color: #02214b;
    }

    .rafy-header {
        display: block;
        /* заголовок слева */
        margin-bottom: 2rem;
    }

    /* Заголовок */
    .rafy-main-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.9rem;
        /* Базовый размер */
        letter-spacing: 2px;
        font-weight: 600;
        color: black;
        margin: 0 0 20px 0;
        text-align: left;
        /* Заголовок слева */
    }

    /* Подзаголовок */
    .rafy-subtitle {
        font-size: 1.1rem;
        letter-spacing: 0.3px;
        line-height: 1.4;
        margin: 0 auto;
        /* центрируем блок */
        font-weight: 550;
        text-align: center;
        max-width: 700px;
        /* ограничение ширины */
        word-wrap: break-word;
        /* перенос длинных слов */
    }

    /* Карточки */
    .rafy-card-square {
        position: relative;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        transition: transform .3s ease, box-shadow .3s ease;
        cursor: pointer;
        width: 100%;
        aspect-ratio: 1 / 1;
        /* сохраняем квадрат */
    }

    .rafy-img-wrapper,
    .rafy-overlay,
    .rafy-hover-text {
        position: absolute;
        inset: 0;
    }

    .rafy-card-square:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.22);
    }

    .rafy-img-wrapper {
        overflow: hidden;
    }

    .rafy-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }

    .rafy-card-square:hover .rafy-card-img {
        transform: scale(1.01);
    }

    .rafy-status {
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: red;
        color: #fff;
        font-size: 0.8rem;
        font-weight: 300;
        padding: 4px 10px;
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
        transition: opacity .4s ease, transform .4s ease;
        transform: scale(0.9);
        z-index: 2;
        text-align: center;
    }

    .rafy-card-square:hover .rafy-hover-text {
        opacity: 1;
        transform: scale(1);
    }

    .rafy-articul {
        font-size: 1.2rem;
        font-weight: 500;
        margin-bottom: 6px;
        letter-spacing: 0.6px;
        font-family: 'Playfair Display', serif;
    }

    .rafy-divider {
        width: 120px;
        height: 1px;
        background: #01142f;
        margin: 6px auto;
    }

    .rafy-name {
        font-size: 0.95rem;
        font-weight: 500;
        margin: 0;
        font-family: 'Playfair Display', serif;
        letter-spacing: 3px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
    }

    .rafy-overlay {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
        opacity: 0;
        transition: opacity .4s ease;
        z-index: 1;
    }

    .rafy-card-square:hover .rafy-overlay {
        opacity: 1;
    }

    /* Адаптивность */

    /* На планшетах */
    @media (max-width: 991.98px) {
        .rafy-main-title {
            font-size: 1.8rem;
            letter-spacing: 3px;
        }

        .rafy-subtitle {
            font-size: 1rem;
            max-width: 500px;
        }
    }

    @media (max-width: 575.98px) {
        .rafy-main-title {
            font-size: 1.5rem;
            letter-spacing: 2px;
        }

        .rafy-subtitle {
            font-size: 0.95rem;
        }

        .rafy-articul {
            font-size: 1rem;
        }

        .rafy-name {
            font-size: 0.85rem;
            letter-spacing: 2px;
        }

        .rafy-status {
            font-size: 0.7rem;
            font-weight: 400;
            /* уменьшили с 0.8rem */
            padding: 3px 5px;
            /* уменьшили с 4px 10px */
        }

        .rafy-all-btn {
            font-size: 0.9rem;
            /* уменьшили с 1rem */
            padding: 12px 32px;
            /* уменьшили с 16px 42px */
        }
    }
</style>