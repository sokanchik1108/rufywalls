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
                height: 300px;
            }
        }

        /* Очень маленькие экраны */
        @media (max-width: 480px) {
            .carousel-item img {
                height: auto;
                max-height: 60vh;
                object-fit: contain;
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
    </style>
</head>

<body class="text-gray-900">

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
                    @if($product->images)
                    @php $images = json_decode($product->images); @endphp
                    <div>
                        <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
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

                                <button type="submit"
                                    class="btn btn-dark flex-shrink-0 px-4 py-2"
                                    style="font-size: 15px; font-weight: 500; min-width: 150px;">
                                    В корзину
                                </button>

                                <div class="input-group" style="width: 110px;">
                                    <button type="button" class="btn btn-outline-secondary py-1 px-2" onclick="changeQuantity(-1)">−</button>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->quantity }}"
                                        class="form-control text-center" required style="font-size: 14px;">
                                    <button type="button" class="btn btn-outline-secondary py-1 px-2" onclick="changeQuantity(1)">+</button>
                                </div>
                            </form>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-600">

                            <div>
                                <p class="uppercase text-xs text-gray-400">Артикул</p>
                                <p>{{ $product->article }}</p>
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
                                <p class="uppercase text-xs text-gray-400">Цвет</p>
                                <p>{{ $product->color }}</p>
                            </div>
                            <div>
                                <p class="uppercase text-xs text-gray-400">Материал</p>
                                <p>{{ $product->material }}</p>
                            </div>
                            <div>
                                <p class="uppercase text-xs text-gray-400">Поклейка</p>
                                <p>{{ $product->sticking }}</p>
                            </div>
                            <div>
                                <p class="uppercase text-xs text-gray-400">Остаток</p>
                                <p>{{ $product->quantity }} шт.</p>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600 mt-3">
                            <p class="mb-1 font-semibold text-black">Размеры рулона:</p>
                            <p class="mb-1">Высота: 10.05 м</p>
                            <p>Ширина: 1.06 м</p>
                        </div>
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
        function changeQuantity(delta) {
            const input = document.getElementById('quantity');
            let value = parseInt(input.value) || 1;
            const min = parseInt(input.min);
            const max = parseInt(input.max);
            value += delta;
            input.value = Math.max(min, Math.min(max, value));
        }

        document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = document.getElementById('product-id').value;
            const quantity = document.getElementById('quantity').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/cart/add/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
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

        document.querySelectorAll('.thumbnail-container img').forEach(thumb => {
            thumb.addEventListener('click', () => {
                document.querySelectorAll('.thumbnail-container img').forEach(img => img.classList.remove('active'));
                thumb.classList.add('active');
            });
        });

        const thumbnails = document.querySelectorAll('.thumbnail-container img');
        const carousel = document.getElementById('mainCarousel');
        carousel.addEventListener('slid.bs.carousel', (event) => {
            const index = event.to;
            thumbnails.forEach(img => img.classList.remove('active'));
            if (thumbnails[index]) thumbnails[index].classList.add('active');
        });
    </script>
</body>

</html>