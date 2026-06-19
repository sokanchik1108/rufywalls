@foreach($variants as $variant)
@php
$product = $variant->product;
$images = json_decode($variant->images ?? '[]', true);
@endphp

<div class="col-6 col-sm-4 col-md-3 col-lg-2">
    <div class="card h-100">
        @if(!empty($images))
        <img src="{{ asset('storage/' . $images[0]) }}" class="card-img-top" alt="Изображение" loading="lazy">
        @endif

        <div class="card-body d-flex flex-column">
            <h5 class="card-title mb-1">Название: {{ $product->name }}</h5>
            <h5 class="card-title mb-1 text-primary">Артикул: {{ $variant->sku }}</h5>
            <p class="text-muted mb-2">Оттенок: <strong>{{ $variant->color }}</strong></p>

            <p class="mb-1"><strong>Категории:</strong>
                {{ $product->categories->pluck('category_name')->implode(', ') ?: '—' }}
            </p>
            <p class="mb-1"><strong>Статус:</strong> {{ $product->status }}</p>
            <p class="mb-1"><strong>Бренд:</strong> {{ $product->brand }}</p>
            <p class="mb-1"><strong>Страна:</strong> {{ $product->country }}</p>
            <p class="mb-1"><strong>Материал:</strong> {{ $product->material }}</p>
            <p class="mb-1"><strong>Цена прихода:</strong> {{ $product->purchase_price }}</p>
            <p class="mb-1"><strong>Цена продажи:</strong> {{ $product->sale_price }}</p>
            <p class="mb-1"><strong>Раппорт:</strong> {{ $product->sticking }}</p>
            <p class="mb-1"><strong>Остаток:</strong> {{ $variant->total_stock ?? 0 }} шт.</p>

            <p class="mb-2"><strong>Комнаты:</strong><br>
                @foreach($product->rooms as $room)
                <span class="badge badge-room">{{ $room->room_name }}</span>
                @endforeach
            </p>

            @if($variant->companions->isNotEmpty())
            <div class="mt-2">
                <p class="mb-1"><strong>Компаньоны:</strong></p>
                <ul class="small ps-3">
                    @foreach($variant->companions as $companion)
                    <li>
                        {{ $companion->product->name }}
                        — {{ $companion->sku }}
                        @if($companion->color)
                        ({{ $companion->color }})
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif



            <p class="mb-3"><strong>Описание:</strong><br>{{ $product->description }}</p>

            <div class="mt-auto">
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


                    <!-- Скрыть/показать товар -->
                    <form action="{{ route('admin.products.toggle-hidden', $product) }}" method="POST" class="mt-2">
                        @csrf
                        @method('PATCH')

                        <!-- важно: гарантирует отправку 0 если чекбокс снят -->
                        <input type="hidden" name="is_hidden" value="0">

                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                name="is_hidden"
                                value="1"
                                id="hideProduct{{ $product->id }}"
                                onchange="this.form.submit()"
                                {{ $product->is_hidden ? 'checked' : '' }}>

                            <label class="form-check-label" for="hideProduct{{ $product->id }}">
                                🔒 Скрыть товар
                            </label>
                        </div>
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
])
@endforeach

@if ($variants->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $variants->links('vendor.pagination.custom') }}
</div>
@endif