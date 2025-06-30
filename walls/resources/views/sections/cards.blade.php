<section class="rafy-walls-section py-5 bg-light">
    <div class="container" style="margin-bottom: 50px;margin-top:50px;">
        <h2 class="text-center mb-3 rafy-title">Обои Rafy Walls</h2>
        <p class="text-center rafy-subtitle ">Премиум материалы и уникальный дизайн для создания уютного и модного пространства в вашем доме</p>

        <div class="row g-4">
            <!-- Карточка 1 -->
            @foreach ($variants as $variant)
            @php
            $product = $variant->product;
            $images = json_decode($variant->images, true);
            @endphp
            <div class="col-md-4">
                <div class="rafy-card h-100">

                    <div id="carousel{{ $variant->id }}" class="carousel slide mb-3">
                        <div class="carousel-inner">
                            @foreach($images as $index => $image)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image) }}" class="rafy-card-img" alt="Rafy Walls 3">
                            </div>
                            @endforeach
                        </div>
                        @if(count($images) > 1)
                        @include('partials.carousel')
                        @endif

                    </div>

                    <div class="rafy-card-body">
                        <h5 class="rafy-card-title">{{ $product->name ?? 'Название товара' }}</h5>
                        <p class="rafy-card-text">{{ $product->description ?? 'Описание товара' }}</p>

                        <p class="rafy-card-price">{{ number_format($product->sale_price, 0, ',', ' ') }} ₸</p>

                        <a href="{{ route('product.show', $product->id) }}" class="btn btn-dark btn-more">Подробнее</a>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    /* cards */

    .rafy-title {
        font-size: 2.0rem;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .rafy-subtitle {
        font-size: 1.15rem;
        color: #555;
        line-height: 1.6;
        margin-bottom: 80px;
    }

    .rafy-card {
        background-color: #f8f9fa;
        /* тот же, что у секции */
        border: none;
        box-shadow: none;
        padding: 0;
    }

    .rafy-card-img {
        width: 100%;
        height: 370px;
        object-fit: cover;
        margin-bottom: 15px;
    }

    .rafy-card-body {
        padding: 0;
    }

    .rafy-card-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .rafy-card-text {
        font-size: 1rem;
        color: #444;
        margin-bottom: 15px;
    }

    .btn-more {
        padding: 10px 25px;
        font-size: 0.95rem;
        font-weight: 500;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .btn-more:hover {
        background-color: #000;
        color: #fff;
        text-decoration: none;
    }
</style>