<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить обои</title>

    <!-- Подключение Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Уменьшаем размеры формы */
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Стили для превью изображений */
        .image-preview {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .image-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-5 form-container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Добавить обои</h4>
            </div>
            <div class="card-body">

                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Превью изображений -->
                <div id="image-preview" class="image-preview"></div>

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="images">Изображения</label>
                        <input type="file" name="images[]" class="form-control" multiple required onchange="previewImages(event)">
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Название</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="purchase_price" class="form-label">Цена прихода</label>
                        <input type="text" step="0.01" name="purchase_price" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="sale_price" class="form-label">Цена продажи</label>
                        <input type="number" step="0.01" name="sale_price" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Количество</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="brand" class="form-label">Бренд</label>
                        <input type="text" name="brand" class="form-control" required>
                    </div>


                    <div class="mb-3">
                        <label for="article" class="form-label">Артикул</label>
                        <input type="text" class="form-control" name="article" required>
                    </div>

                    <div class="mb-3">
                        <label for="country" class="form-label">Страна</label>
                        <input type="text" class="form-control" name="country" required>
                    </div>

                    <div class="mb-3">
                        <label for="color" class="form-label">Цвет</label>
                        <input type="text" class="form-control" name="color" required>
                    </div>

                    <div class="mb-3">
                        <label for="party" class="form-label">Партия</label>
                        <input type="text" class="form-control" name="party">
                    </div>

                    <div class="mb-3">
                        <label for="sticking" class="form-label">Тип поклейки</label>
                        <input type="text" class="form-control" name="sticking" required>
                    </div>

                    <div class="mb-3">
                        <label for="material" class="form-label">Материал</label>
                        <input type="text" class="form-control" name="material" required>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Категория</label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="room_ids" class="form-label">Комнаты</label>
                        <select name="room_ids[]" class="form-select" multiple required>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}"
                                @if(isset($product) && $product->rooms->contains($room->id)) selected @endif
                                >{{ $room->room_name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Удерживайте Ctrl (Cmd) для выбора нескольких</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Описание товара</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Введите описание товара"></textarea>
                    </div>

                    
                    <div class="mb-3">
                        <label for="detailed" class="form-label">Подробнее Описание товара</label>
                        <textarea name="detailed" class="form-control" rows="4" placeholder="Введите описание товара"></textarea>
                    </div>


                    <button type="submit" class="btn btn-success">Сохранить</button>
                </form>

            </div>
        </div>
    </div>

    <!-- Подключение Bootstrap JS (по желанию) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Функция для отображения выбранных изображений
        function previewImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('image-preview');
            previewContainer.innerHTML = ''; // Очистить контейнер перед добавлением новых изображений

            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    previewContainer.appendChild(img);
                };

                reader.readAsDataURL(files[i]);
            }
        }
    </script>
</body>

</html>