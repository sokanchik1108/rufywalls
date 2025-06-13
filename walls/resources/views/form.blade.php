<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Добавить обои</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 900px;
            margin: auto;
        }

        .variant-group,
        .batch-group {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            position: relative;
        }

        .remove-variant,
        .remove-batch {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5 form-container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Добавить обои</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
                @endif

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Основные поля товара -->
                    <div class="mb-3"><label class="form-label">Название</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Страна</label><input type="text" name="country" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Тип поклейки</label><input type="text" name="sticking" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Материал</label><input type="text" name="material" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Цена прихода</label><input type="text" name="purchase_price" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Цена продажи</label><input type="number" name="sale_price" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Бренд</label><input type="text" name="brand" class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Категория</label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Комнаты</label>
                        <select name="room_ids[]" class="form-select" multiple required>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Описание</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Подробнее</label><textarea name="detailed" class="form-control" rows="3"></textarea></div>

                    <!-- Оттенки -->
                    <hr>
                    <h5>Оттенки</h5>
                    <div id="variants-container">
                        <!-- Один вариант по умолчанию -->
                        <div class="variant-group mb-3">
                            <div class="mb-2"><label>Артикул</label><input type="text" name="variants[0][sku]" class="form-control" required></div>
                            <div class="mb-2"><label>Цвет</label><input type="text" name="variants[0][color]" class="form-control" required></div>
                            <div class="mb-2"><label>Изображения</label><input type="file" name="variants[0][images][]" class="form-control" multiple required></div>

                            <!-- Партии внутри варианта -->
                            <div class="batches-container mt-3">
                                <h6>Партии</h6>
                                <div class="batch-group mb-2">
                                    <div class="mb-2"><label>Код партии</label><input type="text" name="variants[0][batches][0][batch_code]" class="form-control" required></div>
                                    <div class="mb-2"><label>Остаток</label><input type="number" name="variants[0][batches][0][stock]" class="form-control" required></div>
                                    <button type="button" class="btn btn-sm btn-danger remove-batch">Удалить партию</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary add-batch">Добавить партию</button>

                            <button type="button" class="btn btn-danger btn-sm remove-variant mt-3">Удалить оттенок</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mb-3" id="add-variant">Добавить оттенок</button>

                    <button type="submit" class="btn btn-success">Сохранить</button>
                </form>
            </div>
        </div>
    </div>

    <!-- JS для динамического добавления -->
    <script>
        let variantIndex = 1;

        document.getElementById('add-variant').addEventListener('click', () => {
            const container = document.getElementById('variants-container');

            const variantHtml = `
            <div class="variant-group mb-3">
                <div class="mb-2"><label>Артикул</label><input type="text" name="variants[${variantIndex}][sku]" class="form-control" required></div>
                <div class="mb-2"><label>Цвет</label><input type="text" name="variants[${variantIndex}][color]" class="form-control" required></div>
                <div class="mb-2"><label>Изображения</label><input type="file" name="variants[${variantIndex}][images][]" class="form-control" multiple required></div>

                <div class="batches-container mt-3">
                    <h6>Партии</h6>
                    <div class="batch-group mb-2">
                        <div class="mb-2"><label>Код партии</label><input type="text" name="variants[${variantIndex}][batches][0][batch_code]" class="form-control" required></div>
                        <div class="mb-2"><label>Остаток</label><input type="number" name="variants[${variantIndex}][batches][0][stock]" class="form-control" required></div>
                        <button type="button" class="btn btn-sm btn-danger remove-batch">Удалить партию</button>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary add-batch">Добавить партию</button>
                <button type="button" class="btn btn-danger btn-sm remove-variant mt-3">Удалить оттенок</button>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', variantHtml);
            variantIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant')) {
                e.target.closest('.variant-group').remove();
            }

            if (e.target.classList.contains('add-batch')) {
                const variantGroup = e.target.closest('.variant-group');
                const batchesContainer = variantGroup.querySelector('.batches-container');
                const batchGroups = batchesContainer.querySelectorAll('.batch-group');
                const variantIdx = Array.from(document.getElementById('variants-container').children).indexOf(variantGroup);
                const batchIdx = batchGroups.length;

                const batchHtml = `
                <div class="batch-group mb-2">
                    <div class="mb-2"><label>Код партии</label><input type="text" name="variants[${variantIdx}][batches][${batchIdx}][batch_code]" class="form-control" required></div>
                    <div class="mb-2"><label>Остаток</label><input type="number" name="variants[${variantIdx}][batches][${batchIdx}][stock]" class="form-control" required></div>
                    <button type="button" class="btn btn-sm btn-danger remove-batch">Удалить партию</button>
                </div>
            `;
                batchesContainer.insertAdjacentHTML('beforeend', batchHtml);
            }

            if (e.target.classList.contains('remove-batch')) {
                e.target.closest('.batch-group').remove();
            }
        });
    </script>
</body>

</html>