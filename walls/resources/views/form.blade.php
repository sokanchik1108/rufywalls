<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить обои</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container { max-width: 800px; margin: 0 auto; }
        .variant-group { position: relative; background: #f9f9f9; }
        .remove-variant { position: absolute; top: 10px; right: 10px; }
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
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Название</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                                <hr>
                <h5>Оттенки</h5>
                <div id="variants-container">
                    <div class="variant-group border rounded p-3 mb-3">
                        <div class="mb-2">
                            <label class="form-label">Артикул (SKU)</label>
                            <input type="text" name="variants[0][sku]" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Остаток</label>
                            <input type="number" name="variants[0][stock]" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Цвет</label>
                            <input type="text" name="variants[0][color]" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Изображения</label>
                            <input type="file" name="variants[0][images][]" class="form-control" multiple required>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-variant">Удалить</button>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mb-3" id="add-variant">Добавить оттенок</button>

                <div class="mb-3">
                    <label class="form-label">Цена прихода</label>
                    <input type="text" step="0.01" name="purchase_price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Цена продажи</label>
                    <input type="number" step="0.01" name="sale_price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Бренд</label>
                    <input type="text" name="brand" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Страна</label>
                    <input type="text" class="form-control" name="country" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Партия</label>
                    <input type="text" class="form-control" name="party">
                </div>

                <div class="mb-3">
                    <label class="form-label">Тип поклейки</label>
                    <input type="text" class="form-control" name="sticking" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Материал</label>
                    <input type="text" class="form-control" name="material" required>
                </div>

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

                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Подробнее</label>
                    <textarea name="detailed" class="form-control" rows="4" required></textarea>
                </div>



                <button type="submit" class="btn btn-success">Сохранить</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let variantIndex = 1;

    document.getElementById('add-variant').addEventListener('click', () => {
        const container = document.getElementById('variants-container');
        const newVariant = document.createElement('div');
        newVariant.classList.add('variant-group', 'border', 'rounded', 'p-3', 'mb-3');
        newVariant.innerHTML = `
            <div class="mb-2">
                <label class="form-label">Артикул (SKU)</label>
                <input type="text" name="variants[${variantIndex}][sku]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Остаток</label>
                <input type="number" name="variants[${variantIndex}][stock]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Цвет</label>
                <input type="text" name="variants[${variantIndex}][color]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Изображения</label>
                <input type="file" name="variants[${variantIndex}][images][]" class="form-control" multiple required>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-variant">Удалить</button>
        `;
        container.appendChild(newVariant);
        variantIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant-group').remove();
        }
    });
</script>
</body>
</html>
