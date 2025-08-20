@extends('layouts.app')

@section('title', 'Добавить товары')

@section('content')
<style>
    body {
        background: #f8f9fa;
        font-family: system-ui, sans-serif;
        font-size: 14px;
    }

    .form-container {
        max-width: 800px;
        margin: 30px auto;
    }

    .card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }

    .card-header {
        background: #ffffff;
        border-bottom: 1px solid #dee2e6;
        padding: 10px 15px;
    }

    .card-header h4 {
        font-size: 18px;
        margin: 0;
    }

    label {
        font-weight: 500;
        margin-bottom: 4px;
    }

    input,
    select,
    textarea {
        border-radius: 4px !important;
        font-size: 14px;
        padding: 6px 10px;
    }

    .variant-group {
        background: #ffffff;
        border: 1px solid #dee2e6;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 12px;
        position: relative;
    }

    .remove-variant {
        position: absolute;
        top: 6px;
        right: 6px;
        background: none;
        border: none;
        color: #888;
        font-size: 14px;
    }

    .remove-variant:hover {
        color: #dc3545;
    }

    .btn-sm {
        padding: 4px 10px;
        font-size: 13px;
    }

    .form-section-title {
        font-size: 15px;
        margin: 15px 0 8px;
        font-weight: 600;
    }
</style>

<div class="container form-container">
    <div class="card">
        <div class="card-header">
            <h4>Добавить обои</h4>
        </div>
        <div class="card-body">

            {{-- Уведомление об успешном сохранении --}}
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            {{-- Вывод ошибок валидации --}}
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="alert alert-warning" style="background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 12px 15px; border-radius: 6px; font-size: 14px;">
                ⚠️ <strong>Важно:</strong> начинайте заполнять названия, описания и другие текстовые поля с <strong>большой буквы</strong>, чтобы информация отображалась корректно на сайте.
            </div>

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-2">
                    <div class="col-md-6"><label>Название</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-6"><label>Страна</label><input type="text" name="country" class="form-control" required></div>
                    <div class="col-md-6"><label>Раппорт</label><input type="text" name="sticking" class="form-control"></div>
                    <div class="col-md-6"><label>Материал</label><input type="text" name="material" class="form-control" required></div>
                    <div class="col-md-6"><label>Цена прихода</label><input type="text" name="purchase_price" class="form-control" required></div>
                    <div class="col-md-6"><label>Цена продажи</label><input type="number" name="sale_price" class="form-control" required></div>
                    <div class="col-md-6"><label>Бренд</label><input type="text" name="brand" class="form-control" required></div>
                    <div class="col-12">
                        <label>Категории</label>
                        <select name="category_ids[]" class="form-select" multiple required>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label>Комнаты</label>
                        <select name="room_ids[]" class="form-select" multiple required>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12"><label>Описание</label><textarea name="description" class="form-control" rows="2" required></textarea></div>
                    <div class="col-12"><label>Подробнее</label><textarea name="detailed" class="form-control" rows="3" required></textarea></div>

                </div>

                <!-- Варианты -->
                <div class="form-section-title">Оттенки</div>
                <div id="variants-container">
                    <div class="variant-group">
                        <div class="mb-2"><label>Артикул</label>
                            <input type="text" name="variants[0][sku]" class="form-control" required>
                        </div>

                        <div class="mb-2"><label>Цвет</label>
                            <input type="text" name="variants[0][color]" class="form-control" required>
                        </div>

                        <div class="mb-2"><label>Изображения</label>
                            <input type="file" name="variants[0][images][]" class="form-control" multiple required>
                        </div>

                        <div class="mb-2"><label>Компаньоны</label>
                            <select name="variants[0][companions][]" class="form-select" multiple>
                                @foreach($allVariants as $variant)
                                <option value="{{ $variant->id }}">
                                    {{ $variant->sku ?? '(без артикула)' }} — {{ $variant->product->name ?? '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="remove-variant">&times;</button>
                    </div>

                </div>

                <!-- Кнопка добавить вариант -->
                <div class="text-end mt-2 mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-variant">+ Оттенок</button>
                </div>

                <button type="submit" class="btn btn-dark">Сохранить</button>
            </form>
        </div>
    </div>
</div>

<script>
    let variantIndex = 1;

    document.getElementById('add-variant').addEventListener('click', () => {
        const container = document.getElementById('variants-container');
        const variantHtml = `
    <div class="variant-group">
        <div class="mb-2"><label>Артикул</label>
            <input type="text" name="variants[${variantIndex}][sku]" class="form-control" required>
        </div>
        <div class="mb-2"><label>Цвет</label>
            <input type="text" name="variants[${variantIndex}][color]" class="form-control" required>
        </div>
        <div class="mb-2"><label>Изображения</label>
            <input type="file" name="variants[${variantIndex}][images][]" class="form-control" multiple required>
        </div>
        <div class="mb-2"><label>Компаньоны</label>
            <select name="variants[${variantIndex}][companions][]" class="form-select" multiple>
                @foreach($allVariants as $variant)
                <option value="{{ $variant->id }}">
                    {{ $variant->sku ?? '(без артикула)' }} — {{ $variant->product->name ?? '' }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="button" class="remove-variant">&times;</button>
    </div>`;

        container.insertAdjacentHTML('beforeend', variantHtml);
        variantIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant-group').remove();
        }
    });
</script>

@endsection