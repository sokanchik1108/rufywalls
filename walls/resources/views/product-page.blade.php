<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} — карточка товара</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-images img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container my-5">
    <a href="{{ route('database') }}" class="btn btn-secondary mb-3">← Назад к списку товаров</a>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">{{ $product->name }}</h3>
        </div>

        <div class="card-body">
            <!-- Изображения -->
            @if($product->images)
                <div class="product-images mb-3 d-flex">
                    @foreach(json_decode($product->images, true) as $img)
                        <img src="{{ asset('storage/' . $img) }}" alt="Фото {{ $product->name }}">
                    @endforeach
                </div>
            @endif

            <!-- Основная информация -->
            <dl class="row">
                <dt class="col-sm-3">Артикул:</dt>
                <dd class="col-sm-9">{{ $product->article }}</dd>

                <dt class="col-sm-3">Бренд:</dt>
                <dd class="col-sm-9">{{ $product->brand }}</dd>

                <dt class="col-sm-3">Страна:</dt>
                <dd class="col-sm-9">{{ $product->country }}</dd>

                <dt class="col-sm-3">Цвет:</dt>
                <dd class="col-sm-9">{{ $product->color }}</dd>

                <dt class="col-sm-3">Материал:</dt>
                <dd class="col-sm-9">{{ $product->material }}</dd>

                <dt class="col-sm-3">Тип поклейки:</dt>
                <dd class="col-sm-9">{{ $product->sticking }}</dd>

                <dt class="col-sm-3">Партия:</dt>
                <dd class="col-sm-9">{{ $product->party ?? '—' }}</dd>

                <dt class="col-sm-3">Категория:</dt>
                <dd class="col-sm-9">{{ $product->category->category_name ?? '—' }}</dd>

                <dt class="col-sm-3">Комнаты:</dt>
                <dd class="col-sm-9">
                    @foreach($product->rooms as $room)
                        <span class="badge bg-info text-dark">{{ $room->room_name }}</span>
                    @endforeach
                </dd>

                <dt class="col-sm-3">Цена прихода:</dt>
                <dd class="col-sm-9">{{ number_format($product->purchase_price, 2) }} ₽</dd>

                <dt class="col-sm-3">Цена продажи:</dt>
                <dd class="col-sm-9">{{ number_format($product->sale_price, 2) }} ₽</dd>

                <dt class="col-sm-3">Количество на складе:</dt>
                <dd class="col-sm-9">{{ $product->quantity }} шт.</dd>
            </dl>

            <!-- Описание -->
            <div class="mb-3">
                <h5>Описание:</h5>
                <p>{{ $product->description }}</p>
            </div>

            <div class="mb-3">
                <h5>Подробное описание:</h5>
                <p>{{ $product->detailed }}</p>
            </div>

        </div>
    </div>
</div>

</body>
</html>
