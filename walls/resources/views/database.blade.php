<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все товары</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Segoe UI', sans-serif;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 260px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.2;
            min-height: 2.6em;
            /* Под 2 строки */
            margin-bottom: 0.5rem;
            overflow: hidden;
        }

        .card-text {
            flex-grow: 1;
            font-size: 0.95rem;
            color: #555;
        }


        .btn {
            font-size: 0.875rem;
        }

        textarea.form-control {
            resize: vertical;
        }
    </style>

</head>

<body>

    <div class="container my-5">
        <h2 class="text-center">Все товары</h2>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <div class="row g-4">
            @foreach($products as $product)
            <div class="col-sm-6 col-md-4 col-lg-3 d-flex">
                <div class="card w-100 d-flex flex-column">
                    @if($product->images)
                    @php $images = json_decode($product->images); @endphp
                    <div id="carousel{{ $product->id }}" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($images as $index => $image)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image) }}" class="d-block w-100 card-img-top" alt="Image">
                            </div>
                            @endforeach
                        </div>
                        @if(count($images) > 1)
                        @include('partials.carousel')
                        @endif
                    </div>
                    @endif

                    <div class="card-body flex-grow-1 d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>

                        <div class="card-text mb-3">
                            <strong>ID:</strong> {{ $product->id }}<br>
                            <strong>Цена продажи:</strong> {{ $product->sale_price }}<br>
                            <strong>Цена прихода:</strong> {{ $product->purchase_price }}<br>
                            <strong>Кол-во:</strong> {{ $product->quantity }}<br>
                            <strong>Бренд:</strong> {{ $product->brand }}<br>
                            <strong>Артикул:</strong> {{ $product->article }}<br>
                            <strong>Страна:</strong> {{ $product->country }}<br>
                            <strong>Цвет:</strong> {{ $product->color }}<br>
                            <strong>Партия:</strong> {{ $product->party }}<br>
                            <strong>Поклейка:</strong> {{ $product->sticking }}<br>
                            <strong>Материал:</strong> {{ $product->material }}<br>
                            <strong>Категория:</strong> {{ $product->category->category_name }}<br>
                            <strong>Описание:</strong>
                            <textarea class="form-control mt-1 mb-2" rows="3" readonly>{{ $product->description }}</textarea>
                            <strong>Подробное Описание:</strong>
                            <textarea class="form-control mt-1 mb-2" rows="3" readonly>{{ $product->detailed }}</textarea>
                            <strong>Комнаты:</strong><br>
                            @foreach($product->rooms as $room)
                            <span class="badge bg-secondary mb-1">{{ $room->room_name }}</span>
                            @endforeach
                        </div>

                        <div class="mt-auto d-flex justify-content-between">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $product->id }}">Редактировать</button>

                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                onsubmit="return confirm('Удалить этот товар?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal{{ $product->id }}" tabindex="-1"
                aria-labelledby="editModalLabel{{ $product->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel{{ $product->id }}">Редактировать товар</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Закрыть"></button>
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
                                    <input type="text" step="0.01" class="form-control" name="purchase_price" value="{{ $product->purchase_price }}" required>
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
                                        <option value="{{ $room->id }}" {{ $product->rooms->contains($room->id) ? 'selected' : '' }}>
                                            {{ $room->room_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Описание</label>
                                    <textarea name="description" class="form-control" rows="4" required>{{ $product->description }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Подробное Описание</label>
                                    <textarea name="detailed" class="form-control" rows="4" required>{{ $product->detailed}}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Загрузить изображения</label>
                                    <input type="file" class="form-control" name="images[]" multiple>
                                </div>
                            </div>
                            <div class="modal-footer mt-3">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>