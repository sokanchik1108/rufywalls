<div class="top-bar">
    <input type="text" name="search" id="search-input" placeholder="Поиск..." value="{{ request('search') }}">
    
    <select name="sort" id="sort-select">
        <option value="">Сортировка</option>
        <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Цена ↑</option>
        <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Цена ↓</option>
        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Новинки</option>
    </select>
</div>


<div class="product-grid">
    @forelse($products as $product)
        <div class="product-card">
            @php
                $images = $product->images ? json_decode($product->images, true) : [];
                $img = $images[0] ?? 'https://via.placeholder.com/300x200';
            @endphp
            <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}">
            <div class="product-info">
                <h4>{{ $product->name }}</h4>
                <p>{{ Str::limit($product->description, 60) }}</p>
                <span>{{ number_format($product->sale_price, 0, '.', ' ') }} ₽</span>
            </div>
        </div>
    @empty
        <p>Товары не найдены.</p>
    @endforelse
</div>

{{ $products->links() }}

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
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }

    .product-info {
        padding: 15px;
    }

    .product-info h4 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .product-info p {
        font-size: 14px;
        color: #777;
        margin-bottom: 8px;
    }

    .product-info span {
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }
</style>
