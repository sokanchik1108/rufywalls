@foreach($variants as $variant)
    @php $product = $variant->product; @endphp
@php $images = json_decode($variant->images ?? '[]', true); @endphp

<div class="col-6 col-sm-4 col-md-3 col-lg-2">
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

                <div class="d-grid gap-2">
                    <!-- Редактировать -->
                    <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal"
                        data-bs-target="#editModal{{ $variant->id }}">
                        ✏️ Редактировать
                    </button>

                    <!-- Удалить вариант -->
                    <form action="{{ route('admin.variant.delete', $variant->id) }}" method="POST"
                        onsubmit="return confirm('Вы уверены, что хотите удалить этот вариант?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                            ❌ Удалить вариант
                        </button>
                    </form>

                    <!-- Удалить товар -->
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                        onsubmit="return confirm('Вы уверены, что хотите удалить весь товар со всеми вариантами?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-100">
                            🗑️ Удалить товар
                        </button>
                    </form>
                </div>


            </div>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования -->
@include('admin.partials.variant-edit-modal', [
'variant' => $variant,
'product' => $product,
'images' => $images,
'categories' => $categories,
'rooms' => $rooms,
'allProducts' => $allProducts
])

@endforeach


@if ($variants->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $variants->links('vendor.pagination.custom') }}
</div>
@endif
