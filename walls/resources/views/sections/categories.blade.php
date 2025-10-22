<section class="category-line-section position-relative">
    <img src="{{ asset('images/главстрбаннер2.jpg') }}" alt="Фон" class="category-bg-image">

    <!-- Заголовок сверху -->
    <div class="category-header text-center mt-5">
        <h2 class="category-line-title">Популярные категории</h2>
        <p class="category-line-subtitle">Выберите стиль для вашего пространства</p>
    </div>

    <!-- Категории немного выше центра -->
    <div class="category-center d-flex justify-content-center align-items-center flex-column">
        <div class="category-list text-center mt-n5">
            @foreach($categories as $category)
            <a href="{{ route('catalog', ['category_id' => $category->id]) }}" class="category-link">
                {{ $category->category_name }}
            </a>
            @endforeach
        </div>
    </div>
</section>

<style>
    .category-bg-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .category-line-section {
        position: relative;
        width: 100%;
        min-height: 100vh;
        overflow: hidden;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .category-header {
        z-index: 2;
    }

    .category-line-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.6rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-top: 230px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .category-line-subtitle {
        font-size: 1.2rem;
        color: #333;
        opacity: 0.9;
        margin-top: 25px;
    }

    .category-center {
        flex: 1;
        display: flex;
        align-items: flex-end;
        /* Смещаем блок выше центра */
        justify-content: center;
        padding-bottom: 40vh;
        /* Контролирует высоту блока категорий */
        z-index: 2;
    }

    .category-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2.4rem;
        max-width: 900px;
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

    /* Анимация появления */
    .category-link {
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

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Адаптив */
    @media (max-width: 767.98px) {
        .category-line-title {
            font-size: 1.4rem;
            margin-top: 150px;
        }

        .category-line-subtitle {
            font-size: 1rem;
            margin-bottom: -100px;
            margin-top: 45px;
        }

        .category-list {
            gap: 1.2rem;
        }

        .category-link {
            font-size: 1rem;
        }

        .category-center {
            padding-bottom: 32vh;
        }
    }
</style>