<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все товары</title>
    <!-- Подключение Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Стили для карточек */
        .card {
            width: 18rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            object-fit: cover;
            height: 180px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.4rem;
            /* Увеличил размер шрифта */
            font-weight: bold;
        }

        .card-text {
            font-size: 1rem;
            /* Увеличил размер шрифта */
            color: #555;
            margin-bottom: 1.5rem;
        }

        .btn {
            border-radius: 5px;
            font-size: 1rem;
            /* Увеличил размер шрифта */
            padding: 10px 15px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }


        /* Адаптивность для мобильных */
        @media (max-width: 768px) {
            .card {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h2 class="text-center mb-4" style="font-size: 2rem;">Все товары</h2> <!-- Увеличил размер шрифта заголовка -->

        {{-- Проверка на успешное сообщение --}}
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <!-- Контейнер для карточек товаров -->
        <div class="product-container">
            @foreach($products as $product)
            <div class="card shadow-sm">
                <div class="card-body">


                    <!-- Слайдер для изображений -->
                    @if($product->images)
                    @php
                    $images = json_decode($product->images);
                    @endphp
                    <div id="carousel{{ $product->id }}" class="carousel slide mb-3" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($images as $index => $image)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="Image">
                            </div>
                            @endforeach
                        </div>
                        @if(count($images) > 1)
                        @include('partials.carousel')
                        @endif

                    </div>
                    @endif

                    <h5 class="card-title">{{ $product->name }}</h5>

                    <p class="card-text">
                        <strong>Цена продажи:</strong> {{ $product->sale_price}}<br>
                        <strong>Цена прихода:</strong> {{ $product->purchase_price }}<br>
                        <strong>Количество:</strong> {{ $product->quantity }}<br>
                        <strong>Бренд:</strong> {{ $product->brand }}<br>
                        <strong>Артикул:</strong> {{ $product->article }}<br>
                        <strong>Страна:</strong> {{ $product->country }}<br>
                        <strong>Цвет:</strong> {{ $product->color }}<br>
                        <strong>Партия:</strong> {{ $product->party }}<br>
                        <strong>Тип поклейки:</strong> {{ $product->sticking }}<br>
                        <strong>Материал:</strong> {{ $product->material }}<br>
                        <strong>Категория:</strong> {{ $product->category->category_name }}<br>
                        <strong>Описание:</strong><textarea name="description" class="form-control mt-2" rows="4" placeholder="Введите описание товара">{{ $product->description }}</textarea><br>
                        <strong>Комната:</strong> @foreach($product->rooms as $room)
                        <span class="badge bg-secondary">{{ $room->room_name }}</span>
                        @endforeach
                    </p>
                </div>

                <!-- Кнопки находятся внизу карточки -->
                <div class="card-footer d-flex justify-content-center gap-2">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $product->id }}">
                        Редактировать
                    </button>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этот товар?');" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Модальное окно -->
        <div class="modal fade" id="editModal{{ $product->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $product->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $product->id }}">Редактировать товар</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Название</label>
                                <input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Артикул</label>
                                <input type="text" class="form-control" name="article" value="{{ $product->article }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Бренд</label>
                                <input type="text" class="form-control" name="brand" value="{{ $product->brand }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Страна</label>
                                <input type="text" class="form-control" name="country" value="{{ $product->country }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Цвет</label>
                                <input type="text" class="form-control" name="color" value="{{ $product->color }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Партия</label>
                                <input type="text" class="form-control" name="party" value="{{ $product->party }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Тип поклейки</label>
                                <input type="text" class="form-control" name="sticking" value="{{ $product->sticking }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Материал</label>
                                <input type="text" class="form-control" name="material" value="{{ $product->material }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Цена прихода</label>
                                <input type="number" step="0.01" class="form-control" name="purchase_price" value="{{ $product->purchase_price }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Цена продажи</label>
                                <input type="number" step="0.01" class="form-control" name="sale_price" value="{{ $product->sale_price }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Количество</label>
                                <input type="number" class="form-control" name="quantity" value="{{ $product->quantity }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Категория</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Комнаты</label>
                                <select name="room_ids[]" class="form-select" multiple required>
                                    @foreach($rooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ $product->rooms->contains($room->id) ? 'selected' : '' }}>
                                        {{ $room->room_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Загрузить новые изображения (опционально)</label>
                                <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Описание товара</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Введите описание товара"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                            <button type="submit" class="btn btn-success">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endforeach
    </div>
    </div>

    <!-- Подключение Bootstrap JS (по желанию) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>