<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} — товар</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        html,
        body {
            background-color: #ffffff;
        }

        .carousel-item img {
            width: 100%;
            height: 600px;
            object-fit: cover;
            background: #fff;
        }

        .carousel-item img {
            width: 100%;
            height: 600px;
            object-fit: cover;
            background: #fff;
        }

        /* Планшеты и ниже */
        @media (max-width: 1024px) {
            .carousel-item img {
                height: 450px;
            }
        }

        /* Смартфоны */
        @media (max-width: 768px) {
            .carousel-item img {
                height: 500px;
            }
        }

        /* Очень маленькие экраны */
        @media (max-width: 480px) {
            .carousel-item img {
                height: 400px;
                object-fit: cover;
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

        #variant-select {
            border: 1px solid #ddd;
            padding: 0.5rem 1rem;
            font-size: 14px;
            border-radius: 8px;
            background-color: #f9f9f9;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        #variant-select:focus {
            border-color: #000;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
            outline: none;
        }

        label[for="variant-select"] {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
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
    </style>
</head>

<body class="text-gray-900">

    <a href="{{ route('cart') }}"
        class="text-decoration-none"
        style="position: fixed; top: 30px; right: 40px; z-index: 1050;">
        <div class="cart-icon">
            <i class="bi bi-bag"></i>
            <span class="cart-count" id="cart-count">{{ $cartCount }}</span>
        </div>
    </a>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


    <!-- Уведомление -->
    <div id="cart-message" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-xs sm:max-w-sm md:max-w-md hidden">
        <div id="cart-message-inner"
            class="flex items-center gap-2 px-4 py-3 rounded-xl shadow-md text-white bg-black animate-fade-in-down text-sm sm:text-base">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-7.414 7.414a1 1 0 01-1.414 0L3.293 9.414a1 1 0 111.414-1.414L8 11.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            <span id="cart-message-text" class="flex-1">Товар добавлен в корзину</span>
        </div>
    </div>

    <!-- Назад -->
    <div class="absolute left-4 sm:left-6 z-10 top-6">
        <a href="{{ route('catalog') }}" class="text-base text-gray-500 hover:text-gray-800">
            ← Назад к списку товаров
        </a>
    </div>

    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 sm:mt-24">
        <div class="bg-white space-y-10">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 min-h-[400px]">
                <!-- Галерея -->
                <div class="lg:w-1/2 w-full flex flex-col">
                    @php $images = json_decode($activeVariant->images); @endphp
                    @if ($images)
                    <div>
                        <div id="mainCarousel" class="carousel slide">
                            <div class="carousel-inner" id="variant-images">
                                @foreach($images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Изображение {{ $index + 1 }}">
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
                    </div>

                    <div class="thumbnail-container mt-3">
                        @foreach($images as $index => $image)
                        <img src="{{ asset('storage/' . $image) }}"
                            class="{{ $index == 0 ? 'active' : '' }}"
                            data-bs-target="#mainCarousel"
                            data-bs-slide-to="{{ $index }}"
                            alt="Миниатюра {{ $index + 1 }}">
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Информация -->
                <div class="lg:w-1/2 w-full">
                    <div class="space-y-6">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-medium font-serif tracking-tight leading-tight">
                                {{ $product->name }}
                            </h1>
                            <p class="text-xl sm:text-2xl font-bold text-black mt-2">
                                {{ number_format($product->sale_price, 2) }} ₸
                            </p>

                            <!-- Форма -->
                            <form id="add-to-cart-form" class="d-flex flex-wrap gap-2 mt-3">
                                @csrf
                                <input type="hidden" id="product-id" value="{{ $product->id }}">
                                <input type="hidden" name="variant_id" id="variant-id-input" value="{{ $activeVariant->id }}">


                                <button type="submit"
                                    class="btn btn-dark flex-shrink-0 px-4 py-2"
                                    style="font-size: 15px; font-weight: 500; min-width: 150px;">
                                    В корзину
                                </button>

                                <div class="input-group" style="width: 110px;">
                                    <button type="button" class="btn btn-outline-secondary py-1 px-2" onclick="changeQuantity(-1)">−</button>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $variantStock }}"
                                        class="form-control text-center" required style="font-size: 14px;">
                                    <button type="button" class="btn btn-outline-secondary py-1 px-2" onclick="changeQuantity(1)">+</button>
                                </div>
                            </form>

                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-600">

                            <div>
                                <p class="uppercase text-xs text-gray-400">Артикул</p>
                                <p id="variant-sku">{{ $activeVariant->sku }}</p>
                            </div>

                            <div>
                                <p class="uppercase text-xs text-gray-400">Бренд</p>
                                <p>{{ $product->brand }}</p>
                            </div>
                            <div>
                                <p class="uppercase text-xs text-gray-400">Страна</p>
                                <p>{{ $product->country }}</p>
                            </div>

                            <div>
                                <p class="uppercase text-xs text-gray-400">Материал</p>
                                <p>{{ $product->material }}</p>
                            </div>
                            <div>
                                <p class="uppercase text-xs text-gray-400">Раппорт (стыковка)</p>
                                <p>{{ $product->sticking }}</p>
                            </div>
                            <div>
                                <p class="uppercase text-xs text-gray-400">Остаток</p>
                                <p id="variant-stock"> {{ $variantStock }} шт.</p>
                            </div>

                        </div>

                        <div class="text-sm text-gray-600 mt-3">
                            <p class="mb-1 font-semibold text-black">Размеры рулона:</p>
                            <p class="mb-1">Высота: 10.05 м</p>
                            <p>Ширина: 1.06 м</p>
                        </div>



                        <div class="mb-3" style="max-width: 180px;">
                            <label for="variant-select" class="form-label">Выберите оттенок:</label>
                            <select id="variant-select" class="form-select" style="max-width: 300px;">
                                @foreach ($variants as $variant)
                                <option value="{{ $variant->id }}" {{ $variant->id === $activeVariant->id ? 'selected' : '' }}>
                                    {{ $variant->color }} ({{ $variant->sku }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        @if($product->companions->count())
                        <div class="mt-6">
                            <h2 class="text-base font-semibold mb-2">Компаньоны</h2>

                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @php
                                $companions = $product->companions->merge($product->companionOf)->unique('id');
                                @endphp


                                @foreach ($companions as $companion)
                                @foreach($companion->variants as $variant)
                                <a href="{{ route('product.show', $companion->id) }}"
                                    class="block border rounded p-2 hover:shadow-sm transition text-sm leading-tight">
                                    <p class="font-medium truncate" style="margin-bottom: 3px;"> {{ $companion->name }}</p>
                                    <p class="text-gray-600">{{ $variant->sku }}</p>
                                    <p class="text-gray-600">{{ $variant->color }}</p>
                                </a>
                                @endforeach
                                @endforeach
                            </div>
                        </div>
                        @endif



                    </div>
                </div>
            </div>

            <!-- Описание -->
            @if($product->detailed)
            <div class="pt-6 border-t pb-16">
                <h2 class="text-xl font-semibold mb-2">Подробное описание</h2>
                <p class="text-gray-700 leading-relaxed text-justify whitespace-pre-line text-sm">
                    {{ $product->detailed }}
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Скрипт -->
    <script>
        const quantityInput = document.getElementById('quantity');
        const variantInput = document.getElementById('variant-id-input');
        let zoomImages = [];
        let currentZoomIndex = 0;

        function openZoom(src) {
            const zoomModal = document.getElementById('zoomModal');
            const zoomImage = document.getElementById('zoomImage');

            currentZoomIndex = zoomImages.indexOf(src);
            if (currentZoomIndex === -1) currentZoomIndex = 0;

            zoomImage.src = zoomImages[currentZoomIndex];
            zoomModal.classList.remove('hidden');
        }

        function closeZoom() {
            document.getElementById('zoomModal').classList.add('hidden');
        }

        function showZoomImage() {
            const zoomImage = document.getElementById('zoomImage');
            zoomImage.src = zoomImages[currentZoomIndex];
        }

        function prevZoomImage() {
            currentZoomIndex = (currentZoomIndex - 1 + zoomImages.length) % zoomImages.length;
            showZoomImage();
        }

        function nextZoomImage() {
            currentZoomIndex = (currentZoomIndex + 1) % zoomImages.length;
            showZoomImage();
        }

        document.getElementById('variant-select').addEventListener('change', function() {
            const variantId = this.value;

            fetch(`/variant/${variantId}`)
                .then(response => response.json())
                .then(data => {
                    variantInput.value = data.id;

                    document.getElementById('variant-sku').textContent = data.sku;
                    document.getElementById('variant-stock').textContent = data.stock + ' шт.';

                    if (quantityInput) {
                        quantityInput.max = data.stock;
                        quantityInput.setAttribute('max', data.stock);
                        quantityInput.value = Math.min(parseInt(quantityInput.value) || 1, data.stock);
                    }

                    const carouselInner = document.getElementById('variant-images');
                    carouselInner.innerHTML = '';
                    zoomImages = [];

                    data.images.forEach((img, index) => {
                        const fullImgSrc = `/storage/${img}`;
                        zoomImages.push(fullImgSrc);

                        const div = document.createElement('div');
                        div.className = 'carousel-item' + (index === 0 ? ' active' : '');
                        div.innerHTML = `<img src="${fullImgSrc}" alt="Изображение ${index + 1}" style="cursor: zoom-in;">`;
                        div.querySelector('img').addEventListener('click', () => openZoom(fullImgSrc));
                        carouselInner.appendChild(div);
                    });

                    const thumbnailContainer = document.querySelector('.thumbnail-container');
                    thumbnailContainer.innerHTML = '';
                    data.images.forEach((img, index) => {
                        const thumb = document.createElement('img');
                        thumb.src = `/storage/${img}`;
                        thumb.alt = `Миниатюра ${index + 1}`;
                        thumb.className = index === 0 ? 'active' : '';
                        thumb.setAttribute('data-bs-target', '#mainCarousel');
                        thumb.setAttribute('data-bs-slide-to', index);

                        thumb.addEventListener('click', () => {
                            document.querySelectorAll('.thumbnail-container img').forEach(i => i.classList.remove('active'));
                            thumb.classList.add('active');
                        });

                        thumbnailContainer.appendChild(thumb);
                    });
                });
        });

        function changeQuantity(delta) {
            if (!quantityInput) return;
            let value = parseInt(quantityInput.value) || 1;
            const min = parseInt(quantityInput.min) || 1;
            const max = parseInt(quantityInput.max) || 999;
            value += delta;
            quantityInput.value = Math.max(min, Math.min(max, value));
        }

        document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
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
                })
                .then(async response => {
                    const data = await response.json();
                    const messageBox = document.getElementById('cart-message');
                    const innerBox = document.getElementById('cart-message-inner');
                    const textBox = document.getElementById('cart-message-text');

                    if (!response.ok) throw new Error(data.message || 'Ошибка запроса');

                    innerBox.classList.remove('bg-red-500');
                    innerBox.classList.add('bg-black');
                    textBox.textContent = data.message || 'Товар добавлен в корзину';
                    messageBox.classList.remove('hidden');
                    setTimeout(() => messageBox.classList.add('hidden'), 3000);

                    if (document.querySelector('#cart-count')) {
                        document.querySelector('#cart-count').textContent = data.cart_count;
                    }

                    localStorage.setItem('cartUpdated', Date.now());
                })

                .catch(() => {
                    const messageBox = document.getElementById('cart-message');
                    const innerBox = document.getElementById('cart-message-inner');
                    const textBox = document.getElementById('cart-message-text');

                    innerBox.classList.remove('bg-black');
                    innerBox.classList.add('bg-red-500');
                    textBox.textContent = 'Не удалось добавить товар. Попробуйте позже.';
                    messageBox.classList.remove('hidden');
                    setTimeout(() => messageBox.classList.add('hidden'), 3000);
                });
        });

        const carousel = document.getElementById('mainCarousel');
        carousel.addEventListener('slid.bs.carousel', (event) => {
            const index = event.to;
            const thumbnails = document.querySelectorAll('.thumbnail-container img');
            thumbnails.forEach(img => img.classList.remove('active'));
            if (thumbnails[index]) thumbnails[index].classList.add('active');
        });

        document.addEventListener('DOMContentLoaded', () => {
            zoomImages = Array.from(document.querySelectorAll('#variant-images img')).map(img => img.src);

            document.querySelectorAll('#variant-images img').forEach((img, index) => {
                img.style.cursor = 'zoom-in';
                img.addEventListener('click', () => openZoom(img.src));
            });
        });

        // Клавиатурная навигация (опционально)
        document.addEventListener('keydown', (e) => {
            const zoomModal = document.getElementById('zoomModal');
            if (zoomModal.classList.contains('hidden')) return;

            if (e.key === 'ArrowLeft') prevZoomImage();
            if (e.key === 'ArrowRight') nextZoomImage();
            if (e.key === 'Escape') closeZoom();
        });
    </script>

    >
    <!-- Модальное окно для увеличения изображения -->
    <div id="zoomModal" class="fixed inset-0 z-[9999] bg-black/80 flex items-center justify-center hidden">
        <span class="absolute top-4 right-6 text-white text-3xl cursor-pointer z-10" onclick="closeZoom()">×</span>

        <!-- Стрелки -->
        <button id="prevZoom" class="absolute left-4 text-white text-4xl z-10" onclick="prevZoomImage()">‹</button>
        <button id="nextZoom" class="absolute right-4 text-white text-4xl z-10" onclick="nextZoomImage()">›</button>

        <img id="zoomImage"
            src=""
            alt="Увеличенное изображение"
            class="object-contain rounded shadow-lg max-w-[100vw] max-h-[100vh] sm:max-w-[90vw] sm:max-h-[90vh]">
    </div>
    
</body>

</html>