<section class="category-line-section position-relative">
    <img src="{{ asset('images/главстрбаннер2.jpg') }}" alt="Фон" class="category-bg-image">

    <div class="category-content text-center">
        <h2 class="category-line-title">Популярные категории</h2>
        <p class="category-line-subtitle">Выберите стиль для вашего пространства</p>

        <div class="category-list">
            @foreach($categories as $category)
            <a href="{{ route('catalog', ['category_id' => $category->id]) }}" class="category-link">
                {{ $category->category_name }}
            </a>
            @endforeach
        </div>
    </div>
</section>

<style>
    /* ====== ОСНОВНОЙ КОНТЕЙНЕР ====== */
    .category-line-section {
        position: relative;
        width: 100%;
        height: 100vh;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: #f8f8f8;
    }

    /* ====== ФОНОВОЕ ИЗОБРАЖЕНИЕ ====== */
    .category-bg-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    /* ====== КОНТЕНТ ====== */
    .category-content {
        position: relative;
        z-index: 2;
        max-width: 950px;
        width: 100%;
        padding: 0 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* ====== ТЕКСТ ====== */
    .category-line-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #1a1a1a;
        margin: 0 0 1rem 0;
        line-height: 1.2;
    }

    .category-line-subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 1.25rem;
        color: #333;
        opacity: 0.9;
        margin: 0 0 2.5rem 0;
        line-height: 1.4;
    }

    /* ====== СПИСОК КАТЕГОРИЙ ====== */
    .category-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        width: 100%;
    }

    .category-link {
        position: relative;
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem;
        font-weight: 600;
        color: #111;
        text-decoration: none;
        text-transform: capitalize;
        padding: 0.4rem 0;
        transition: color 0.3s ease;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s forwards;
    }

    .category-link:nth-child(odd) {
        animation-delay: 0.1s;
    }

    .category-link:nth-child(even) {
        animation-delay: 0.2s;
    }

    .category-link::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 0%;
        height: 2px;
        background: linear-gradient(90deg, #000, #666);
        transition: width 0.4s ease;
    }

    .category-link:hover {
        color: #000;
    }

    .category-link:hover::after {
        width: 100%;
    }

    /* ====== АНИМАЦИЯ ====== */
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ====== АДАПТИВ ====== */
    @media (max-width: 1200px) {
        .category-line-title {
            font-size: 2.4rem;
        }
        .category-line-subtitle {
            font-size: 1.15rem;
        }
    }

    @media (max-width: 991.98px) {
        .category-line-title {
            font-size: 2rem;
        }
        .category-line-subtitle {
            font-size: 1.05rem;
            margin-bottom: 2rem;
        }
        .category-list {
            gap: 1.5rem;
        }
    }

    @media (max-width: 767.98px) {
        .category-content {
            padding: 0 1rem;
        }
        .category-line-title {
            font-size: 1.6rem;
        }
        .category-line-subtitle {
            font-size: 0.95rem;
            margin-bottom: 1.8rem;
        }
        .category-list {
            gap: 1rem;
        }
        .category-link {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 480px) {
        .category-line-title {
            font-size: 1.4rem;
        }
        .category-line-subtitle {
            font-size: 0.9rem;
        }
    }
</style>
