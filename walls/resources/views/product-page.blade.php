<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} — товар</title>

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        html,
        body {
            background-color: #ffffff;
        }

        .carousel,
        .carousel-inner,
        .carousel-item {
            height: 100%;
        }

        .carousel-item img {
            width: 100%;
            height: 600px;
            object-fit: cover;
            background: #fff;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 40px;
            /* уменьшение ширины кликабельной зоны */
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-size: 20px 20px;
            /* уменьшение иконки */
            filter: brightness(0);
            /* делает иконку черной */
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
    </style>
</head>

<body class="text-gray-900">

    <!-- Кнопка Назад вне основного блока -->
    <div class="absolute top-6 left-6 z-10">
        <a href="{{ route('catalog') }}" class="text-base text-gray-500 hover:text-gray-800">
            ← Назад к списку товаров
        </a>
    </div>

    <div class="max-w-screen-xl mx-auto px-6 mt-12">
        <div class="bg-white p-0 lg:p-0 space-y-10">

            <!-- Основной блок -->
            <div class="flex flex-col lg:flex-row gap-12" style="margin-top: 150px; min-height: 600px;">
                <!-- Галерея (займёт всю высоту) -->
                <div class="lg:w-1/2 w-full flex flex-col justify-between">
                    @if($product->images)
                    @php $images = json_decode($product->images); @endphp

                    <!-- Основной карусель -->
                    <div class="flex-grow">
                        <div id="mainCarousel" class="carousel slide h-full" data-bs-ride="carousel">
                            <div class="carousel-inner h-full">
                                @foreach($images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Изображение {{ $index + 1 }}">
                                </div>
                                @endforeach
                            </div>
                            @if(count($images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden">Предыдущий</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                                <span class="visually-hidden">Следующий</span>
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Миниатюры -->
                    <div class="thumbnail-container mt-4">
                        @foreach($images as $index => $image)
                        <img
                            src="{{ asset('storage/' . $image) }}"
                            class="{{ $index == 0 ? 'active' : '' }}"
                            data-bs-target="#mainCarousel"
                            data-bs-slide-to="{{ $index }}"
                            alt="Миниатюра {{ $index + 1 }}">
                        @endforeach
                    </div>
                    @endif
                </div>


                <!-- Информация -->
                <div class="lg:w-1/2 w-full flex flex-col justify-between">
                    <div class="space-y-8">
                        <div>
                            <h1 class="text-2xl font-medium font-serif tracking-tight leading-tight w-fit">
                                {{ $product->name }}
                            </h1>
                            <p class="text-2xl font-bold text-black mt-2 w-fit">
                                {{ number_format($product->sale_price, 2) }} ₸
                            </p>

<button class="bg-black text-white py-3 px-6 mt-4 rounded-xl text-sm font-medium hover:bg-gray-800 transition " style="width:100%;">
    Добавить в корзину 
</button>

                        </div>

                        <div class="grid grid-cols-2 gap-6 text-sm text-gray-600">
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

                        <div class="text-sm text-gray-600 mt-3 ">
                            <p class=" mb-1" style="font-size: large;font-weight:600;color:#000">Размеры рулона:</p>
                            <p class="mb-1">Высота: 10.05 м</p>
                            <p>Ширина: 1.06 м</p>
                        </div>


                    </div>
                </div>
            </div>

            <!-- Подробное описание -->
            @if($product->detailed)
            <div class="pt-6 border-t mb-36" style="margin-bottom: 150px;">
                <h2 class="text-xl font-semibold mb-2">Подробное описание</h2>
                <p class="text-gray-700 leading-relaxed text-justify whitespace-pre-line">
                    {{ $product->detailed }}
                </p>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.querySelectorAll('.thumbnail-container img').forEach((thumb) => {
            thumb.addEventListener('click', () => {
                document.querySelectorAll('.thumbnail-container img').forEach(img => img.classList.remove('active'));
                thumb.classList.add('active');
            });
        });
    </script>

    <script>
        const thumbnails = document.querySelectorAll('.thumbnail-container img');
        const carousel = document.getElementById('mainCarousel');

        // При клике на миниатюру — устанавливаем активный класс (уже есть)
        thumbnails.forEach((thumb) => {
            thumb.addEventListener('click', () => {
                thumbnails.forEach(img => img.classList.remove('active'));
                thumb.classList.add('active');
            });
        });

        // При смене слайда в карусели — синхронизируем thumbnail
        carousel.addEventListener('slid.bs.carousel', (event) => {
            const index = event.to; // Получаем индекс активного слайда
            thumbnails.forEach(img => img.classList.remove('active'));
            if (thumbnails[index]) {
                thumbnails[index].classList.add('active');
            }
        });
    </script>


</body>

</html>