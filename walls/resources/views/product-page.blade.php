<head>
    @php
    // Подготовка данных для SEO
    $materialText = $product->material;
    $materialText = str_ireplace('Винил на флизелине', 'виниловые обои на флизелиновой основе', $materialText);
    $materialText = str_ireplace('бумага', 'бумажные обои', $materialText);
    $productName = $product->name;
    $sku = $activeVariant->sku ?? '';
    $pageUrl = url()->current();

    // Получаем JSON-изображения варианта
    $images = json_decode($activeVariant->images);
    $ogImage = $images && count($images) > 0
    ? asset('storage/' . ltrim($images[0], '/'))
    : asset('images/default.jpg');

    $shortDescription = "Купить $productName — $materialText в Алматы. Новые коллекции KURBANOV WALLS: современные моющиеся и флизелиновые обои. Артикул $sku.";
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 🔹 Title --}}
    <title>{{ $productName }} — стильные {{ $materialText }} в Алматы | KURBANOV WALLS</title>

    {{-- 🔹 Meta Description и Keywords --}}
    <meta name="description" content="{{ $shortDescription }}">
    <meta name="keywords" content="{{ $productName }}, {{ $materialText }}, обои Алматы, купить обои Алматы, KURBANOV WALLS, моющиеся обои, флизелиновые, виниловые">
    <link rel="canonical" href="{{ $pageUrl }}">

    {{-- 🔹 Open Graph --}}
    <meta property="og:title" content="{{ $productName }} — стильные {{ $materialText }} | KURBANOV WALLS">
    <meta property="og:description" content="{{ $shortDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="KURBANOV WALLS">

    {{-- 🔹 Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $productName }} — стильные {{ $materialText }} | KURBANOV WALLS">
    <meta name="twitter:description" content="{{ $shortDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- 🔹 Schema.org Product --}}
    <script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "Product",
            "name": "{{ $productName }}",
            "image": "{{ $ogImage }}",
            "description": "{{ $shortDescription }}",
            "sku": "{{ $sku }}",
            "brand": {
                "@type": "Brand",
                "name": "KURBANOV WALLS"
            },
            "offers": {
                "@type": "Offer",
                "url": "{{ $pageUrl }}",
                "priceCurrency": "KZT",
                "availability": "https://schema.org/InStock"
            }
        }
    </script>

    {{-- 🔹 Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/лого1.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/лого1.png') }}">
    <meta name="theme-color" content="#ffffff">

    {{-- 🔹 Bootstrap и Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            background-color: #ffffff;
        }

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            background-color: #ffffff;
        }

        .carousel-item img {
            width: 100%;
            height: 600px;
            object-fit: cover;
            background: #fff;
        }

        @media (max-width: 1024px) {
            .carousel-item img {
                height: 450px;
            }
        }

        @media (max-width: 768px) {
            .carousel-item img {
                height: 500px;
            }
        }

        @media (max-width: 480px) {
            .carousel-item img {
                height: 400px;
            }
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 40px;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-size: 20px 20px;
            filter: brightness(0);
        }

        .thumbnail-container {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            overflow-x: auto;
        }

        .thumbnail-container img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s;
        }

        .thumbnail-container img.active {
            border-color: #000;
        }

        @media (min-width: 768px) {
            .thumbnail-container img {
                width: 100px;
                height: 70px;
            }
        }

        @keyframes fade-in-down {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out;
        }

        .btn-dark:hover {
            background-color: #333;
        }

        .cart-icon {
            position: relative;
            font-size: 1.4rem;
            color: #222;
            margin-left: 1rem;
        }

        .cart-icon:hover {
            color: #007bff;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background: #dc3545;
            color: #fff;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 50%;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
</head>

<body class="text-dark">


    <a href="{{ route('cart') }}" class="text-decoration-none position-fixed top-0 end-0 p-4 z-3">
        <div class="cart-icon">
            <i class="bi bi-bag"></i>
            <span class="cart-count" id="cart-count">{{ $cartCount }}</span>
        </div>
    </a>

    <div class="position-absolute" style="top: 25px; left: 25px; z-index: 3;">
        <a href="#" onclick="history.back(); return false;" class="text-muted text-decoration-none">
            ← Назад
        </a>
    </div>


    <!-- Уведомление -->
    <div id="cart-message"
        class="position-fixed start-50 translate-middle-x z-3 w-50 text-center d-none"
        style="top: 30px;">
        <div id="cart-message-inner" class="d-inline-flex align-items-center gap-2 px-4 py-3 rounded shadow text-white bg-dark animate-fade-in-down small">
            <i class="bi bi-check-circle-fill"></i>
            <span id="cart-message-text">Товар добавлен в корзину</span>
        </div>
    </div>

    <div class="container mt-5 pt-5">
        <div class="row g-5">
            <!-- Галерея -->
            <div class="col-lg-6">
                @php $images = json_decode($activeVariant->images); @endphp
                @if ($images)
                <div id="mainCarousel" class="carousel slide">
                    <div class="carousel-inner" id="variant-images">
                        @foreach($images as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="Изображение {{ $index + 1 }}">
                        </div>
                        @endforeach
                    </div>
                    @if(count($images) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    @endif
                </div>
                <div class="thumbnail-container mt-3">
                    @foreach($images as $index => $image)
                    <img src="{{ asset('storage/' . $image) }}" class="{{ $index == 0 ? 'active' : '' }}"
                        data-bs-target="#mainCarousel" data-bs-slide-to="{{ $index }}" alt="Миниатюра {{ $index + 1 }}">
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Информация -->
            <div class="col-lg-6">
                <h1 class="h4 fw-bold">
                    {{ $product->name }}
                    <span class="visually-hidden"> — {{ $product->material }} купить в Алматы | KURBANOV WALLS</span>
                </h1>

                @if ($product->sale_price == 0)
                <span class="d-flex align-items-center"
                    style="font-size: 0.9rem; font-weight: 600; color: #6c757d;">
                    <i class="bi bi-info-circle me-2"
                        style="font-size: 1rem; color: #6c757d;"></i>
                    Информацию о цене можно узнать в WhatsApp
                </span>
                @else
                <p style="font-size: 1.25rem; font-weight: 700; color: #333; margin: 0; line-height: 1.2;">
                    {{ number_format($product->sale_price, 2) }} ₸
                </p>
                @endif


                <form id="add-to-cart-form" class="d-flex flex-wrap gap-2 mt-3">
                    @csrf
                    <input type="hidden" id="product-id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="variant-id-input" value="{{ $activeVariant->id }}">

                    <button type="submit" class="btn btn-dark px-4 py-2" style="min-width: 150px;">В корзину</button>

                    <div class="input-group" style="width: 110px;">
                        <button type="button" class="btn btn-outline-secondary py-1 px-2" onclick="changeQuantity(-1)">−</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1"
                            class="form-control text-center" required>
                        <button type="button" class="btn btn-outline-secondary py-1 px-2" onclick="changeQuantity(1)">+</button>
                    </div>
                </form>

                <div class="row mt-4">
                    <div class="col-4 col-sm-6 mb-4">
                        <div class="text-muted small">АРТИКУЛ</div>
                        <div class="text-dark small" id="variant-sku">{{ $activeVariant->sku }}</div>
                    </div>
                    <div class="col-4 col-sm-6 mb-4">
                        <div class="text-muted small">БРЕНД</div>
                        <div class="text-dark small">{{ $product->brand }}</div>
                    </div>
                    <div class="col-4 col-sm-6 mb-4">
                        <div class="text-muted small">СТРАНА</div>
                        <div class="text-dark small">{{ $product->country }}</div>
                    </div>
                    <div class="col-4 col-sm-6 mb-4">
                        <div class="text-muted small">МАТЕРИАЛ</div>
                        <div class="text-dark small">{{ $product->material }}</div>
                    </div>
                    <div class="col-4 col-sm-6 mb-4">
                        <div class="text-muted small">РАППОРТ (СТЫКОВКА)</div>
                        <div class="text-dark small">{{ $product->sticking }}</div>
                    </div>
                    <div class="col-4 col-sm-6 mb-4">
                        <div class="text-muted small">ЦВЕТ</div>
                        <div class="text-dark small" id="variant-color">{{ $activeVariant->color }}</div>
                    </div>

                    <div class="col-4 col-sm-6 mb-4">
                        <div class="text-muted small">Размеры рулона:</div>
                        <div class="text-dark small" id="variant-color">1.06 x 10 м</div>
                    </div>

                    @php
                    $variant = $variant ?? ($activeVariant ?? $product->variants->first());
                    @endphp

                    @if($variant)
                    @php
                    // Собираем компаньонов двусторонне и готовим список SKU
                    $companions = $variant->companions->merge($variant->companionOf)->unique('id');

                    $companionSkus = $companions->map(function ($comp) {
                    return $comp->sku ?: '';
                    })->filter()->values()->all();
                    @endphp

                    @if(!empty($companionSkus))
                    <div class="col-4 col-sm-6 mb-3" id="companions-block">
                        <div class="text-muted small">Компаньоны:</div>
                        <div class="text-dark small">{{ implode(', ', $companionSkus) }}</div>
                    </div>
                    @endif
                    @endif





                </div>
                <div class="mb-3" style="margin-top: 68px;">
                    <div class="text-dark mb-1" style="font-size: 20px;">Все оттенки</div>
                    <div class="d-flex flex-wrap gap-3" id="variant-thumbnails">
                        @foreach ($variants as $variant)
                        <div style="text-align: center; cursor: pointer;">
                            <img
                                src="{{ asset('storage/' . json_decode($variant->images)[0]) }}"
                                alt="{{ $variant->color }}"
                                data-variant-id="{{ $variant->id }}"
                                class="variant-thumbnail {{ $variant->id === $activeVariant->id ? 'border border-dark border-2' : '' }}"

                                style="width: 109px; height: 135px; object-fit: cover; border-radius: 0;">
                            <div style="font-size: 14px; margin-top: 6px; color: #333;">
                                <div>{{ $variant->sku }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>


        <div class="mt-5 pt-4 border-top" style="margin-bottom: 70px;">
            <div class="row g-4">

                {{-- Описание --}}
                <div class="col-md-8">
                    <h5 class="mb-3">Подробное описание</h5>
                    <p class="text-muted" style="white-space: pre-line;">{{ $product->detailed }}</p>
                </div>

                {{-- Компаньон --}}
                <div class="col-md-4">
                    <h5 class="mb-3 text-start">Компаньон</h5> <!-- заголовок по левому краю -->
                    <div id="first-companion-block" style="display: none;">
                        <div class="companion-wrapper">
                            <a href="#" id="companion-link" class="image-container">
                                <img id="companion-image" src="" alt="Изображение компаньона" class="companion-bg">
                                <div class="companion-overlay">
                                    <div class="companion-text">
                                        <div id="companion-sku">Артикул</div>
                                        <div class="divider"></div>
                                        <div id="companion-title">Название</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <style>
                    .companion-wrapper {
                        width: 100%;
                    }

                    .image-container {
                        position: relative;
                        width: 100%;
                        aspect-ratio: 1 / 1;
                        display: block;
                        overflow: hidden;
                        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                        text-decoration: none;
                        /* убираем подчеркивание */
                        color: inherit;
                    }

                    .image-container:hover {
                        transform: translateY(-4px);
                        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.22);
                    }

                    .companion-bg {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        display: block;
                        transition: transform 0.4s ease;
                    }

                    .image-container:hover .companion-bg {
                        transform: scale(1.01);
                    }

                    /* Затемнение снизу */
                    .companion-overlay {
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0));
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        text-align: center;
                        color: #fff;
                        opacity: 0;
                        transition: opacity 0.4s ease;
                    }

                    .image-container:hover .companion-overlay {
                        opacity: 1;
                    }

                    .companion-text {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        flex: 1;
                        color: #fff;
                    }

                    .companion-text #companion-sku {
                        font-size: 1.2rem;
                        font-weight: 500;
                        margin-bottom: 6px;
                        letter-spacing: 0.6px;
                        font-family: 'Playfair Display', serif;
                    }

                    .companion-text #companion-title {
                        font-size: 0.95rem;
                        font-weight: 500;
                        margin: 0;
                        font-family: 'Playfair Display', serif;
                        letter-spacing: 3px;
                        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
                    }

                    .companion-text .divider {
                        width: 120px;
                        height: 1px;
                        background: #01142f;
                        margin: 6px auto;
                    }
                </style>
            </div>
        </div>
    </div>


    @php
    $variantsData = $variants->map(function($v) {
    $companion = $v->companionOf->first();
    return [
    'id' => $v->id,
    'images' => json_decode($v->images) ?? [],
    'sku' => $v->sku,
    'color' => $v->color,
    'companions' => $v->companions->pluck('sku')->toArray(),
    'companion' => $companion ? [
    'id' => $companion->id,
    'sku' => $companion->sku,
    'title' => $companion->name,
    'image' => json_decode($companion->images)[0] ?? ''
    ] : null
    ];
    })->toArray();
    @endphp

    <script>
        const quantityInput = document.getElementById('quantity');
        const variantInput = document.getElementById('variant-id-input');
        let zoomImages = [];
        let currentZoomIndex = 0;

        // PHP-массив всех вариантов
        const variantsData = @json($variantsData);

        // =======================
        // Функции зума
        // =======================
        function openZoom(src) {
            const zoomModal = document.getElementById('zoomModal');
            const zoomImage = document.getElementById('zoomImage');
            currentZoomIndex = zoomImages.indexOf(src);
            if (currentZoomIndex === -1) currentZoomIndex = 0;
            zoomImage.src = zoomImages[currentZoomIndex];
            zoomModal.classList.remove('d-none');
        }

        function closeZoom() {
            document.getElementById('zoomModal').classList.add('d-none');
        }

        function showZoomImage() {
            document.getElementById('zoomImage').src = zoomImages[currentZoomIndex];
        }

        function prevZoomImage() {
            currentZoomIndex = (currentZoomIndex - 1 + zoomImages.length) % zoomImages.length;
            showZoomImage();
        }

        function nextZoomImage() {
            currentZoomIndex = (currentZoomIndex + 1) % zoomImages.length;
            showZoomImage();
        }

        // Найти вариант с 7-й картинкой
        function findVariantWithSeventhImage(excludeId = null) {
            return variantsData.find(v => Array.isArray(v.images) && v.images.length >= 7 && v.id !== excludeId);
        }

        // =======================
        // Загрузка варианта
        // =======================
        function loadVariantById(variantId) {
            fetch(`/variant/${variantId}`)
                .then(res => res.json())
                .then(data => {
                    // SKU и цвет
                    document.getElementById('variant-sku').textContent = data.sku;
                    document.getElementById('variant-color').textContent = data.color;

                    // Галерея
                    const carouselInner = document.getElementById('variant-images');
                    carouselInner.innerHTML = '';
                    zoomImages = [];
                    if (Array.isArray(data.images)) {
                        data.images.forEach((img, index) => {
                            const fullSrc = `/storage/${img}`;
                            zoomImages.push(fullSrc);

                            const div = document.createElement('div');
                            div.className = 'carousel-item' + (index === 0 ? ' active' : '');
                            div.innerHTML = `<img src="${fullSrc}" class="d-block w-100" alt="Изображение ${index+1}" style="cursor: zoom-in;">`;
                            div.querySelector('img').addEventListener('click', () => openZoom(fullSrc));
                            carouselInner.appendChild(div);
                        });
                    }

                    // Миниатюры
                    const thumbnailContainer = document.querySelector('.thumbnail-container');
                    if (thumbnailContainer) {
                        thumbnailContainer.innerHTML = '';
                        if (Array.isArray(data.images)) {
                            data.images.forEach((img, index) => {
                                const thumb = document.createElement('img');
                                thumb.src = `/storage/${img}`;
                                thumb.alt = `Миниатюра ${index+1}`;
                                thumb.className = index === 0 ? 'active' : '';
                                thumb.addEventListener('click', () => {
                                    document.querySelectorAll('.thumbnail-container img').forEach(i => i.classList.remove('active'));
                                    thumb.classList.add('active');
                                    const carousel = bootstrap.Carousel.getInstance(document.getElementById('mainCarousel'));
                                    if (carousel) carousel.to(index);
                                });
                                thumbnailContainer.appendChild(thumb);
                            });
                        }
                    }

                    // Баннер (7-я картинка)
                    const bannerWrapper = document.getElementById('variant-banner-wrapper');
                    const banner = document.getElementById('variant-banner');
                    let bannerSrc = null;

                    if (Array.isArray(data.images) && data.images.length >= 7) {
                        bannerSrc = `/storage/${data.images[6]}`;
                    } else {
                        const other = findVariantWithSeventhImage(data.id);
                        if (other) bannerSrc = `/storage/${other.images[6]}`;
                    }

                    if (banner && bannerWrapper) {
                        if (bannerSrc) {
                            banner.src = bannerSrc;
                            bannerWrapper.style.display = '';
                        } else {
                            bannerWrapper.style.display = 'none';
                        }
                    }

                    // Компаньоны
                    const companionsContainer = document.getElementById('companions-block');
                    if (companionsContainer) {
                        companionsContainer.innerHTML = data.companions && data.companions.length > 0 ?
                            `<div class="text-muted small">Компаньоны:</div><div class="text-dark small">${data.companions.join(', ')}</div>` :
                            '';
                    }

                    // Первый компаньон
                    const companionBlock = document.getElementById('first-companion-block');

                    if (data.companion && data.companion.id && data.companion.variant_id) {
                        document.getElementById('companion-image').src = data.companion.image ? `/storage/${data.companion.image}` : '/images/no-image.jpg';
                        document.getElementById('companion-sku').textContent = data.companion.sku;
                        document.getElementById('companion-title').textContent = data.companion.title || '';

                        // Ссылка теперь открывает продукт с активным вариантом
                        document.getElementById('companion-link').href = `/product/${data.companion.id}?variant=${data.companion.variant_id}`;

                        if (companionBlock) companionBlock.style.display = 'block';
                    } else if (companionBlock) {
                        companionBlock.style.display = 'none';
                    }

                    // Обновляем URL
                    window.history.replaceState({},
                        '',
                        `/product/{{ $product->slug }}?variant=${variantId}`
                    );
                });
        }

        // =======================
        // Инициализация страницы
        // =======================
        document.addEventListener('DOMContentLoaded', () => {
            // Клики по миниатюрам вариантов
            document.querySelectorAll('.variant-thumbnail').forEach(img => {
                img.addEventListener('click', () => {
                    const id = img.getAttribute('data-variant-id');
                    document.querySelectorAll('.variant-thumbnail').forEach(i => i.classList.remove('border', 'border-2', 'border-dark'));
                    img.classList.add('border', 'border-dark', 'border-2');

                    variantInput.value = id;
                    loadVariantById(id);
                });
            });

            // Загружаем начальный вариант
            if (variantInput.value) loadVariantById(variantInput.value);

            // Зум клавиши
            document.addEventListener('keydown', e => {
                const zoomModal = document.getElementById('zoomModal');
                if (!zoomModal || zoomModal.classList.contains('d-none')) return;
                if (e.key === 'ArrowLeft') prevZoomImage();
                if (e.key === 'ArrowRight') nextZoomImage();
                if (e.key === 'Escape') closeZoom();
            });

            // Форма "Добавить в корзину"
            const form = document.getElementById('add-to-cart-form');
            if (form) {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    const variantId = variantInput.value;
                    const quantity = quantityInput.value;
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('/cart/add', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            variant_id: variantId,
                            quantity
                        })
                    }).then(async res => {
                        const data = await res.json();
                        const messageBox = document.getElementById('cart-message');
                        const innerBox = document.getElementById('cart-message-inner');
                        const textBox = document.getElementById('cart-message-text');

                        if (!res.ok) throw new Error(data.message || 'Ошибка запроса');

                        innerBox.classList.remove('bg-danger');
                        innerBox.classList.add('bg-dark');
                        textBox.textContent = data.message || 'Товар добавлен в корзину';
                        messageBox.classList.remove('d-none');
                        setTimeout(() => messageBox.classList.add('d-none'), 3000);

                        const cartCount = document.querySelector('#cart-count');
                        if (cartCount) cartCount.textContent = data.cart_count;
                        localStorage.setItem('cartUpdated', Date.now());
                    }).catch(() => {
                        const messageBox = document.getElementById('cart-message');
                        const innerBox = document.getElementById('cart-message-inner');
                        const textBox = document.getElementById('cart-message-text');
                        innerBox.classList.remove('bg-dark');
                        innerBox.classList.add('bg-danger');
                        textBox.textContent = 'Не удалось добавить товар. Попробуйте позже.';
                        messageBox.classList.remove('d-none');
                        setTimeout(() => messageBox.classList.add('d-none'), 3000);
                    });
                });
            }

            // Кнопки +/- количества
            window.changeQuantity = delta => {
                let value = parseInt(quantityInput.value) || 1;
                const min = parseInt(quantityInput.min) || 1;
                value = Math.max(min, value + delta);
                quantityInput.value = value;
            };
        });
    </script>




    <!-- Модальное окно для увеличения изображения -->
    <div id="zoomModal"
        class="position-fixed top-0 start-0 w-100 h-100 d-none bg-dark bg-opacity-75"
        style="z-index: 1050; display: flex; align-items: center; justify-content: center;">

        <!-- Закрыть -->
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3 btn-close-white"
            aria-label="Закрыть" onclick="closeZoom()"></button>

        <!-- Стрелки -->
        <button type="button" class="btn text-white position-absolute start-0 top-50 translate-middle-y fs-2 ps-3"
            onclick="prevZoomImage()">‹</button>
        <button type="button" class="btn text-white position-absolute end-0 top-50 translate-middle-y fs-2 pe-3"
            onclick="nextZoomImage()">›</button>

        <!-- Изображение -->
        <img id="zoomImage"
            src=""
            alt="Увеличенное изображение"
            class="img-fluid rounded shadow"
            style="max-width: 90vw; max-height: 90vh;">
    </div>

    <style>
        #zoomImage {
            max-width: 90vw !important;
            max-height: 90vh !important;
        }

        @media (max-width: 768px) {
            #zoomImage {
                max-width: 100vw !important;
                max-height: 100vh !important;
            }
        }
    </style>


    <div id="variant-banner-wrapper" style="margin-top: 100px; margin-bottom: 50px;">
        <img id="variant-banner"
            src=""
            alt="Баннер варианта"
            loading="lazy"
            style="width: 100%; height: auto; object-fit: cover;">
    </div>


    @include('partials.footer')

</body>

</html>