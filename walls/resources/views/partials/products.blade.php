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