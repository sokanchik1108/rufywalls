<section class="rafy-walls-section py-5 bg-light">
    <div class="container my-5">
        <h2 class="text-center mb-4 rafy-title">Обои от RAFY WALLS</h2>
        <p class="text-center rafy-subtitle">Премиум материалы и уникальный дизайн для создания уютного и модного пространства в вашем доме.</p>

        <div class="row g-4">
            @foreach ($variants as $variant)
            @php
                $product = $variant->product;
                $images = json_decode($variant->images, true);
            @endphp

            <div class="col-md-4">
                <div class="rafy-card-lux">

                    <!-- Фото с эффектами -->
                    <div class="rafy-image-wrapper">
                        <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $product->name }}" class="rafy-card-img">

                        <!-- Красивый оверлей -->
                        <div class="rafy-overlay">
                            <div class="rafy-overlay-content">
                                <h5 class="rafy-overlay-title">{{ $product->name ?? 'Название товара' }}</h5>
                                <p class="rafy-overlay-price">{{ number_format($product->sale_price, 0, ',', ' ') }} ₸</p>
                                <a href="{{ route('product.show', $product->id) }}" class="btn rafy-btn-glass">Подробнее</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
/* Заголовок */
.rafy-title {
    font-size: 3.5rem;
    font-weight: 600;
    color: #1a1a1a;
    text-align: center;
    text-transform: none;
    letter-spacing: 0.5px;
    margin-bottom: 20px;
    position: relative;
    font-family: 'Georgia', serif;
}

.rafy-title::after {
    content: "";
    display: block;
    width: 80px;
    height: 2px;
    background-color: #01142f;
    margin: 16px auto 0 auto;
    border-radius: 2px;
}

/* Подзаголовок */
.rafy-subtitle {
    font-size: 1.2rem;
    font-weight: 300;
    color: #555;
    text-align: center;
    max-width: 680px;
    margin: 0 auto 60px auto;
    font-family: 'Helvetica Neue', sans-serif;
    line-height: 1.7;
    padding: 0 10px;
}

/* Карточка */
.rafy-card-lux {
    position: relative;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    transition: transform .4s ease, box-shadow .4s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.rafy-card-lux:hover {
    transform: translateY(-12px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.25);
}

/* Фото */
.rafy-image-wrapper {
    flex-grow: 1;
    overflow: hidden;
}

.rafy-card-img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    transition: transform 0.6s ease;
    display: block;
}

.rafy-card-lux:hover .rafy-card-img {
    transform: scale(1.1);
}

/* Оверлей */
.rafy-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 40%, rgba(0,0,0,0) 100%);
    display: flex;
    align-items: flex-end;
    justify-content: center;
    opacity: 0;
    transition: opacity .5s ease;
    padding: 25px;
    border-radius: 22px;
}

.rafy-card-lux:hover .rafy-overlay {
    opacity: 1;
}

.rafy-overlay-content {
    text-align: center;
    color: #fff;
    backdrop-filter: blur(6px);
    background: rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 20px 25px;
    width: 100%;
    max-width: 85%;
}

.rafy-overlay-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.rafy-overlay-price {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 15px;
}

/* Кнопка */
.rafy-btn-glass {
    padding: 10px 30px;
    border-radius: 30px;
    font-size: 0.95rem;
    font-weight: 600;
    color: #fff;
    background-color: #01142f; /* Темно-синий фон */
    border: 1px solid #01142f; /* Граница такого же цвета */
    transition: background-color 0.3s ease, color 0.3s ease;
    display: inline-block;
    text-decoration: none;
    cursor: pointer;
}

.rafy-btn-glass:hover {
    background-color: #02204a; /* Немного светлее при ховере */
    color: #f0f0f0;
    border-color: #02214b;
}


/* ------------------- Адаптивность ------------------- */

/* Мобильные устройства (до 576px) */
@media (max-width: 575.98px) {
    .rafy-title {
        font-size: 2rem;
    }
    .rafy-subtitle {
        font-size: 1rem;
        max-width: 90%;
        margin-bottom: 40px;
    }
    .rafy-card-img {
        height: 250px;
    }
    .rafy-btn-glass {
        padding: 8px 20px;
        font-size: 0.85rem;
    }
    /* Колонки 1 на мобильных */
    .row.g-4 > div {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

/* Планшеты (576px - 991px) */
@media (min-width: 576px) and (max-width: 991.98px) {
    .rafy-title {
        font-size: 2.6rem;
    }
    .rafy-subtitle {
        font-size: 1.1rem;
        max-width: 85%;
        margin-bottom: 50px;
    }
    .rafy-card-img {
        height: 320px;
    }
    .rafy-btn-glass {
        padding: 9px 25px;
        font-size: 0.9rem;
    }
    /* Колонки 2 на планшетах */
    .row.g-4 > div {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* Десктопы (от 992px) */
@media (min-width: 992px) {
    /* 3 колонки по умолчанию, для уверенности */
    .row.g-4 > div {
        flex: 0 0 33.3333%;
        max-width: 33.3333%;
    }
}

</style>
