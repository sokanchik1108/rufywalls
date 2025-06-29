<div class="top-bar">
    <input type="text" id="search" value="{{ request('search') }}" placeholder="Введите название..." class="form-control">
    <select id="sort" class="form-select">
        <option value="">Без сортировки</option>
        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>По возрастанию цены</option>
        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>По убыванию цены</option>
        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>По названию (А-Я)</option>
        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>По названию (Я-А)</option>
    </select>
</div>

<div class="product-grid">
    @forelse ($variants as $variant)
        @php
            $product = $variant->product;
            $images = json_decode($variant->images, true);
        @endphp

        <div class="product-card">
            @if (!empty($images))
                <div id="carousel{{ $variant->id }}" class="carousel slide mb-3">
                    <div class="carousel-inner">
                        @foreach ($images as $index => $image)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="Изображение {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>

                    @if (count($images) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $variant->id }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $variant->id }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    @endif
                </div>
            @endif

            <div class="product-info">
                <h4 class="product-title">{{ $product->name }} ({{ $variant->color }})</h4>

                <div class="product-desc-price">
                    <p>{{ $product->description }}</p>
                    <span>{{ number_format($product->sale_price, 0, '.', ' ') }} ₸</span>
                </div>

                <div class="btn-wrapper">
                    <a href="{{ route('product.show', $product->id) }}" class="btn btn-dark">Подробнее</a>
                </div>
            </div>
        </div>
    @empty
    <p>Товары не найдены.</p>
    @endforelse
</div>

<div class="pagination-wrapper">
    {{ $variants->links('vendor.pagination.custom') }}
</div>

<style>
    .top-bar {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .top-bar input,
    .top-bar select {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 14px;
        width: 40%;
    }

    .top-bar select {
        width: 20%;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .product-card {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-card img {
        width: 100%;
        height: 500px;
        object-fit: cover;
    }

    .product-info {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        padding: 15px;
    }

    .product-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
        line-height: 1.2;
        min-height: 48px;
        overflow: hidden;
    }

    .product-desc-price {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .product-desc-price p {
        font-size: 14px;
        color: #777;
        margin-bottom: 8px;
        word-break: break-word;
        white-space: normal;
        flex-grow: 1;
    }

    .product-desc-price span {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-top: auto;
    }

    .btn-wrapper {
        margin-top: 10px;
    }

    .btn-dark {
        display: block;
        width: 100%;
        background-color: #000;
        color: #fff;
        padding: 10px 15px;
        text-align: center;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.2s ease;
    }

    .btn-dark:hover {
        background-color: #333;
    }

    /* 📱 Адаптивность */
    @media (max-width: 992px) {
        .catalog-container {
            flex-direction: column;
        }

        .filters {
            width: 100%;
            order: 2;
        }

        .product-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .top-bar {
            flex-direction: column;
            gap: 10px;
        }

        .top-bar input,
        .top-bar select {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .product-grid {
            grid-template-columns: 1fr;
        }

        .product-card img {
            height: 300px;
        }
    }
</style>
