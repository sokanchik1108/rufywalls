<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} — RAFY WALLS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $product->name }} — обои, которые работают на стиль. RAFY WALLS.">


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
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
        class="position-fixed start-50 translate-middle-x z-3 w-100 text-center d-none"
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
                <h1 class="h4 fw-bold">{{ $product->name }}</h1>
                <p class="h5 fw-bold text-dark">{{ number_format($product->sale_price, 2) }} ₸</p>

                <form id="add-to-cart-form" class="d-flex flex-wrap gap-2 mt-3">
                    @csrf
                    <input type="hidden" id="product-id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="variant-id-input" value="{{ $activeVariant->id }}">

                    <button type="submit" class="btn btn-dark px-4 py-2" style="min-width: 150px;">В корзину</button>

                    <div class="input-group" style="width: 110px;">
                        <button type="button" class="btn btn-outline-secondary py-1 px-2" onclick="changeQuantity(-1)">−</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $variantStock }}"
                            class="form-control text-center" required>
                        <button type="button" class="btn btn-outline-secondary py-1 px-2" onclick="changeQuantity(1)">+</button>
                    </div>
                </form>

                <div class="row mt-3">
                    <div class="col-sm-6 mb-2">
                        <div class="text-muted small">АРТИКУЛ</div>
                        <div class="text-dark small" id="variant-sku">{{ $activeVariant->sku }}</div>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <div class="text-muted small">БРЕНД</div>
                        <div class="text-dark small">{{ $product->brand }}</div>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <div class="text-muted small">СТРАНА</div>
                        <div class="text-dark small">{{ $product->country }}</div>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <div class="text-muted small">МАТЕРИАЛ</div>
                        <div class="text-dark small">{{ $product->material }}</div>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <div class="text-muted small">РАППОРТ (СТЫКОВКА)</div>
                        <div class="text-dark small">{{ $product->sticking }}</div>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <div class="text-muted small">ОСТАТОК</div>
                        <div class="text-dark small" id="variant-stock">{{ $variantStock }} шт.</div>
                    </div>
                </div>




                <div class="mt-3 mb-3">
                    <div class="text-dark medium">Размеры рулона:</div>
                    <div class="text-muted small">Высота: 10.05 м</div>
                    <div class="text-muted small">Ширина: 1.06 м</div>
                </div>


                <div class="mb-3" style="max-width: 240px;">
                    <label for="variant-select" class="form-label text-dark mb-1" style="font-size: 14px;">Выберите оттенок</label>
                    <select id="variant-select"
                        class="form-select text-dark"
                        style="font-size: 14px; padding: 6px 10px; height: 38px;">
                        @foreach ($variants as $variant)
                        <option value="{{ $variant->id }}" {{ $variant->id === $activeVariant->id ? 'selected' : '' }}>
                            {{ $variant->color }} ({{ $variant->sku }})
                        </option>
                        @endforeach
                    </select>
                </div>


                @if($product->companions->count())
                <div class="mt-4">
                    <h5 class="mb-2">Компаньоны</h5>
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-2">
                        @php
                        $companions = $product->companions->merge($product->companionOf)->unique('id');
                        @endphp
                        @foreach ($companions as $companion)
                        @foreach($companion->variants as $variant)
                        <div class="col">
                            <a href="{{ route('product.show', $companion->id) }}"
                                class="text-decoration-none border rounded p-2 d-block bg-white hover-shadow-sm">
                                <div class="fw-semibold text-dark small">{{ $companion->name }}</div>
                                <div class="text-muted small">{{ $variant->sku }}</div>
                                <div class="text-muted small">{{ $variant->color }}</div>
                            </a>
                        </div>
                        @endforeach
                        @endforeach
                    </div>

                </div>
                @endif
            </div>
        </div>

        @if($product->detailed)
        <div class="mt-5 pt-4 border-top">
            <h5 class="mb-3">Подробное описание</h5>
            <p class="text-muted" style="white-space: pre-line;">{{ $product->detailed }}</p>
        </div>
        @endif
    </div>

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
            zoomModal.classList.remove('d-none');
        }

        function closeZoom() {
            document.getElementById('zoomModal').classList.add('d-none');
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
                        div.innerHTML = `<img src="${fullImgSrc}" class="d-block w-100" alt="Изображение ${index + 1}" style="cursor: zoom-in;">`;
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

                    innerBox.classList.remove('bg-danger');
                    innerBox.classList.add('bg-dark');
                    textBox.textContent = data.message || 'Товар добавлен в корзину';
                    messageBox.classList.remove('d-none');
                    setTimeout(() => messageBox.classList.add('d-none'), 3000);

                    if (document.querySelector('#cart-count')) {
                        document.querySelector('#cart-count').textContent = data.cart_count;
                    }

                    localStorage.setItem('cartUpdated', Date.now());
                })
                .catch(() => {
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

        document.addEventListener('keydown', (e) => {
            const zoomModal = document.getElementById('zoomModal');
            if (zoomModal.classList.contains('d-none')) return;

            if (e.key === 'ArrowLeft') prevZoomImage();
            if (e.key === 'ArrowRight') nextZoomImage();
            if (e.key === 'Escape') closeZoom();
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



</body>

</html>