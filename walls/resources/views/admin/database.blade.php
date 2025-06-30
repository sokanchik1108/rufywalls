@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f2f4f7;
        font-family: 'Segoe UI', sans-serif;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .card-img-top {
        height: 220px;
        object-fit: cover;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .badge-room {
        margin-right: 4px;
        background-color: #6c757d;
    }

    .modal-header,
    .modal-footer {
        border: none;
    }

    .form-label {
        font-weight: 500;
    }

    .form-control,
    .form-select,
    textarea {
        border-radius: 10px;
    }

    .modal-content {
        border-radius: 16px;
    }

    img.preview {
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    .form-select[multiple] {
        height: auto;
        min-height: 120px;
        padding: 10px;
        border-radius: 12px;
        background-color: #fff;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: border-color 0.2s, box-shadow 0.2s;
        font-size: 14px;
    }

    .form-select[multiple]:focus {
        border-color: #80bdff;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-select option {
        padding: 5px 10px;
    }

    .form-select option:checked {
        background-color: #0d6efd !important;
        color: white;
    }
</style>
</head>

<body>
    <div class="container my-5">
        <h2 class="text-center fw-bold mb-4">Варианты товаров (Оттенки)</h2>

        @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            @foreach($products as $product)
            @foreach($product->variants as $variant)
            @php $images = json_decode($variant->images ?? '[]', true); @endphp

            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    @if(!empty($images))
                    <img src="{{ asset('storage/' . $images[0]) }}" class="card-img-top" alt="Изображение">
                    @endif

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-1">Название: {{ $product->name }}</h5>
                        <h5 class="card-title mb-1 text-primary">Артикул: {{ $variant->sku }}</h5>
                        <p class="text-muted mb-2">Оттенок: <strong>{{ $variant->color }}</strong></p>

                        <p class="mb-1"><strong>Категория:</strong> {{ $product->category->category_name ?? '—' }}</p>
                        <p class="mb-1"><strong>Бренд:</strong> {{ $product->brand }}</p>
                        <p class="mb-1"><strong>Страна:</strong> {{ $product->country }}</p>
                        <p class="mb-1"><strong>Материал:</strong> {{ $product->material }}</p>
                        <p class="mb-1"><strong>Цена прихода:</strong> {{ $product->purchase_price }}</p>
                        <p class="mb-1"><strong>Цена продажи:</strong> {{ $product->sale_price }}</p>
                        <p class="mb-1"><strong>Раппорт:</strong> {{ $product->sticking }}</p>

                        <p class="mb-2"><strong>Комнаты:</strong><br>
                            @foreach($product->rooms as $room)
                            <span class="badge badge-room">{{ $room->room_name }}</span>
                            @endforeach
                        </p>

                        @if($product->companions->isNotEmpty())
                        <div class="mt-2">
                            <p class="mb-1"><strong>Компаньоны:</strong></p>
                            <ul class="small ps-3">
                                @foreach($product->companions as $companion)
                                <li>{{ $companion->name }} —
                                    @php $compSkus = $companion->variants->pluck('sku')->filter()->implode(', ') @endphp
                                    {{ $compSkus }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif


                        <p class="mb-3"><strong>Описание:</strong><br>{{ $product->description }}</p>

                        <div class="mt-auto">
                            @php $totalStock = $variant->batches->sum('stock'); @endphp
                            <p class="fw-semibold">Общий остаток: {{ $totalStock }} шт.</p>
                            <p class="mb-1"><strong>Партии:</strong></p>
                            <ul class="small ps-3">
                                @foreach($variant->batches as $batch)
                                <li>Партия {{ $batch->batch_code ?? '—' }}: {{ $batch->stock }} шт.</li>
                                @endforeach
                            </ul>

                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <!-- Редактировать -->
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $variant->id }}">
                                    ✏️ Редактировать
                                </button>

                                <!-- Удалить вариант -->
                                <form action="{{ route('admin.variant.delete', $variant->id) }}" method="POST"
                                    onsubmit="return confirm('Вы уверены, что хотите удалить этот вариант?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        🗑️ Удалить вариант
                                    </button>
                                </form>

                                <!-- Удалить товар -->
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('Вы уверены, что хотите удалить весь товар со всеми вариантами?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        ❌ Удалить товар
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Модальное окно редактирования -->
            <div class="modal fade" id="editModal{{ $variant->id }}" tabindex="-1"
                aria-labelledby="editModalLabel{{ $variant->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Закрыть"></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                <div class="row g-3">


                                    <h5 class="text-primary">Редактирование оттенка</h5>

                                    <input type="hidden" name="variants[{{ $variant->id }}][id]"
                                        value="{{ $variant->id }}">

                                    <div class="col-md-6">
                                        <label class="form-label">Артикул</label>
                                        <input type="text" name="variants[{{ $variant->id }}][sku]"
                                            class="form-control" value="{{ $variant->sku }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Цвет</label>
                                        <input type="text" name="variants[{{ $variant->id }}][color]"
                                            class="form-control" value="{{ $variant->color }}" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Изображения</label>
                                        <input type="file"
                                            name="variants[{{ $variant->id }}][images][]"
                                            class="form-control" multiple>

                                        @if (!empty($images))
                                        <div class="mt-2">
                                            @foreach($images as $img)
                                            <img src="{{ asset('storage/' . $img) }}" width="80"
                                                class="me-2 mb-2 preview">
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Партии</label>
                                        <div class="list-group">
                                            @foreach($variant->batches as $batch)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>Партия:</strong> {{ $batch->batch_code ?? '—' }}
                                                    <input type="hidden"
                                                        name="variants[{{ $variant->id }}][batches][{{ $batch->id }}][batch_code]"
                                                        value="{{ $batch->batch_code }}">
                                                </div>
                                                <div style="max-width: 120px;">
                                                    <input type="number"
                                                        name="variants[{{ $variant->id }}][batches][{{ $batch->id }}][stock]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $batch->stock }}"
                                                        placeholder="Остаток">
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <p class="fw-semibold mt-2">Общий остаток по партиям: {{ $variant->batches->sum('stock') }} шт.</p>
                                    </div>


                                    <hr>

                                    <h5 class="text-primary">Редактирование товара</h5>

                                    <div class="col-md-6">
                                        <label class="form-label">Название</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $product->name }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Бренд</label>
                                        <input type="text" name="brand" class="form-control"
                                            value="{{ $product->brand }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Страна</label>
                                        <input type="text" name="country" class="form-control"
                                            value="{{ $product->country }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Раппорт</label>
                                        <input type="text" name="sticking" class="form-control"
                                            value="{{ $product->sticking }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Материал</label>
                                        <input type="text" name="material" class="form-control"
                                            value="{{ $product->material }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Цена прихода</label>
                                        <input type="text" name="purchase_price"
                                            class="form-control" value="{{ $product->purchase_price }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Цена продажи</label>
                                        <input type="number" step="0.01" name="sale_price"
                                            class="form-control" value="{{ $product->sale_price }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Категория</label>
                                        <select name="category_id" class="form-select">
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Комнаты</label>
                                        <select name="room_ids[]" class="form-select" multiple>
                                            @foreach($rooms as $room)
                                            <option value="{{ $room->id }}"
                                                {{ $product->rooms->contains($room->id) ? 'selected' : '' }}>
                                                {{ $room->room_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Описание</label>
                                        <textarea name="description"
                                            class="form-control">{{ $product->description }}</textarea>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Подробное описание</label>
                                        <textarea name="detailed"
                                            class="form-control">{{ $product->detailed }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-4">

                            <h5 class="text-primary mt-3" style="margin-left: 20px;">Привязка компаньонов</h5>

                            <div class="col-md-12 mt-2" style="margin-left: 20px;max-width:95%;">
                                <label class="form-label">Компаньоны (другие товары)</label>
                                <select name="companion_variant_ids[]" class="form-select" multiple>
                                    @foreach($allProducts as $other)
                                    @if($other->id !== $product->id)
                                    @php
                                    $skus = $other->variants->pluck('sku')->filter()->implode(', ');
                                    $firstVariantId = $other->variants->first()?->id;
                                    $selected = $product->companions->contains($other->id);
                                    @endphp
                                    @if($firstVariantId)
                                    <option value="{{ $firstVariantId }}" {{ $selected ? 'selected' : '' }}>
                                        {{ $skus }} ({{ $other->name }})
                                    </option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отмена</button>
                                <button type="submit" class="btn btn-success">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @endforeach
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
@endsection