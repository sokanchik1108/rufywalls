<section class="category-line-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="category-line-title">Популярные категории</h2>
            <p class="category-line-subtitle">Найдите оформление, которое подходит вашему интерьеру</p>
        </div>

        <div class="row g-2 justify-content-center">
            @foreach($categories as $category)
            @php
            $variant = $categoryVariants[$category->id] ?? null;
            if(!$variant || !$variant->image7) continue;
            $imagePath = asset('storage/' . $variant->image7);
            @endphp

            <div class="col-6 col-sm-6 col-lg-3">

                <a href="{{ route('catalog', ['category_id' => $category->id]) }}"
                    class="category-line-item d-flex align-items-center">
                    <img src="{{ $imagePath }}" alt="{{ $category->category_name }}"
                        class="category-line-image me-3" loading="lazy">
                    <span class="category-line-name">{{ $category->category_name }}</span>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    .category-line-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.3rem;
        /* было 1.9rem */
        font-weight: 600;
        color: #111;
        margin-bottom: 1rem;
        /* чуть больше отступ */
    }

    .category-line-subtitle {
        font-size: 1.1rem;
        /* было 0.95rem */
        color: #666;
        margin-bottom: 2.5rem;
        /* немного больше пространства */
    }


    .category-line-item:hover {
        transform: translateX(4px);
        background: rgba(0, 0, 0, 0.03);
    }


    .category-line-item:hover .category-line-image {
        transform: scale(1.05);
        opacity: 1;
    }

    .category-line-item {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: inherit;
        padding: 0.6rem 0.5rem;
        transition: transform 0.3s ease, background 0.3s ease;
    }

    .category-line-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        opacity: 0.93;
        transition: transform 0.35s ease, opacity 0.35s ease;
    }

    .category-line-name {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        font-weight: 500;
        color: #222;
        margin-top: 0.5rem;
    }

    @media (max-width: 767.98px) {

        .category-line-title {
            font-size: 1.5rem;
        }

        .category-line-subtitle {
            font-size: 0.9rem;
        }


        .category-line-item {
            flex-direction: column;
            justify-content: center;
            text-align: center;
            padding: 0;
            width: 100%;
            aspect-ratio: 1 / 1;
            overflow: hidden;
        }

        .category-line-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            flex-grow: 1;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .category-line-item:hover .category-line-image {
            transform: scale(1.04);
            opacity: 1;
        }

        .category-line-name {
            bottom: 6px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 0.9rem;
            color: black;
            padding: 3px 0;
            margin: 0;
        }

        .category-line-section .row {
            margin-left: -3px;
            margin-right: -3px;
        }

        .category-line-section .col-sm-6 {
            padding-left: 3px;
            padding-right: 3px;
            margin-bottom: 6px;
            position: relative;
        }

        .category-line-section .col-6:nth-child(n+5) {
            display: none !important;
        }
    }
</style>