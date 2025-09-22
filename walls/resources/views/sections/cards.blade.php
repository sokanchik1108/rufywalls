<!-- Подключаем Playfair Display и Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@400;500&display=swap" rel="stylesheet">

<section class="rafy-walls-section py-5 mb-5 bg-light">
    <div class="container text-center" style="margin-bottom: 70px;">
        <!-- Новый дизайн заголовка -->
        <div class="rafy-header">
            <h2 class="rafy-main-title">Новинки RAFY WALLS</h2>
            <div class="rafy-title-line"></div>
            <p class="rafy-subtitle">Эксклюзивные коллекции с премиальными материалами и современным дизайном</p>
        </div>

        <div class="row g-4">
            @foreach ($variants as $variant)
            @php
            $product = $variant->product;
            $images = json_decode($variant->images, true);
            @endphp
            <div class="col-12 col-sm-6 col-md-4">
                <a href="{{ route('product.show', $product->id) }}" class="rafy-card-link">
                    <div class="rafy-card-square">
                        <div class="rafy-status">{{ $product->status }}</div>
                        <div class="rafy-img-wrapper">
                            <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $product->name }}" class="rafy-card-img">
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
    </div>
</section>

<style>
    /* Секция */
    .rafy-walls-section {
        margin-bottom: 4rem;
        /* чуть больше отступ снизу */
    }

    /* Заголовок */
    .rafy-header {
        display: inline-block;
        margin-bottom: 2rem;
    }

    .rafy-main-title {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        letter-spacing: 4px;
        font-weight: 700;
        color: black;
        margin: 0 0 20px 0;
    }

    .rafy-subtitle {
        font-size: 1.1rem;
        color: #555;
        letter-spacing: 0.3px;
        line-height: 1.4;
        margin: 0;
        font-weight: 550;
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
    }

    .rafy-card-square:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.22);
    }

    .rafy-img-wrapper {
        overflow: hidden;
        height: 100%;
    }

    .rafy-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }

    .rafy-card-square:hover .rafy-card-img {
        transform: scale(1.02);
    }

    .rafy-status {
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: #01142f;
        color: #fff;
        font-size: 0.8rem;
        font-weight: 300;
        padding: 4px 10px;
        border-radius: 0px;
        z-index: 3;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .rafy-hover-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        text-align: center;
        color: #fff;
        opacity: 0;
        transition: opacity .4s ease, transform .4s ease;
        z-index: 2;
    }

    .rafy-card-square:hover .rafy-hover-text {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .rafy-articul {
        font-size: 1.5rem;
        font-weight: 500;
        margin-bottom: 8px;
        letter-spacing: 0.6px;
        font-family: 'Playfair Display', serif;
    }

    .rafy-divider {
        width: 150px;
        height: 1px;
        background: #01142f;
        margin: 8px auto;
    }

    .rafy-name {
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
        font-family: 'Playfair Display', serif;
        letter-spacing: 5px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        transition: color .3s ease;
    }

    .rafy-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 90%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
        opacity: 0;
        transition: opacity .4s ease;
        z-index: 1;
    }

    .rafy-card-square:hover .rafy-overlay {
        opacity: 1;
    }

    /* Адаптивность для маленьких экранов */
    @media (max-width: 575.98px) {

        /* Заголовок меньше */
        .rafy-main-title {
            font-size: 1.5rem;
            letter-spacing: 2px;
        }

        .rafy-subtitle {
            font-size: 0.95rem;
        }

        /* Карточки чуть уже и выше */
        .col-12 {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
            display: flex;
            justify-content: center;
        }

        .rafy-card-square {
            aspect-ratio: auto;
            height: 300px;
            /* выше, чем на больших экранах */
        }

        .rafy-img-wrapper {
            height: 100%;
        }

        .rafy-card-img {
            height: 100%;
            object-fit: cover;
        }
    }

</style>