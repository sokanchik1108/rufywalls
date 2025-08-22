<div class="modal fade" id="editModal{{ $variant->id }}" tabindex="-1"
    aria-labelledby="editModalLabel{{ $variant->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="row g-3">
                        <h5 class="text-primary">Редактирование оттенка</h5>

                        <input type="hidden" name="variants[{{ $variant->id }}][id]" value="{{ $variant->id }}">

                        <div class="col-md-6">
                            <label class="form-label">Артикул</label>
                            <input type="text" name="variants[{{ $variant->id }}][sku]" class="form-control" value="{{ $variant->sku }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Оттенок</label>
                            <input type="text" name="variants[{{ $variant->id }}][color]" class="form-control" value="{{ $variant->color }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Изображения</label>
                            <input type="file" name="variants[{{ $variant->id }}][images][]" class="form-control" multiple>
                            @if (!empty($images))
                            <div class="mt-2">
                                @foreach($images as $img)
                                <img src="{{ asset('storage/' . $img) }}" width="80" class="me-2 mb-2 preview" loading="lazy">
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <hr>

                        <h5 class="text-primary">Редактирование товара</h5>

                        <hr class="mt-4">

                        @php $wrapperId = 'new-variants-wrapper-' . $variant->id; @endphp

                        <div id="{{ $wrapperId }}"></div>

                        <button type="button"
                            class="btn btn-sm btn-outline-primary mt-2 add-variant-btn"
                            data-wrapper-id="{{ $wrapperId }}">
                            + Добавить оттенок
                        </button>

                        <div class="col-md-6">
                            <label class="form-label">Статус</label>
                            <select name="status" class="form-select">
                                <option value="">Без статуса</option>
                                <option value="новинка" {{ $product->status === 'новинка' ? 'selected' : '' }}>Новинка</option>
                                <option value="распродажа" {{ $product->status === 'распродажа' ? 'selected' : '' }}>Распродажа</option>
                                <option value="хит продаж" {{ $product->status === 'хит продаж' ? 'selected' : '' }}>Хит продаж</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Старая цена</label>
                            <input type="number" step="0.01" name="discount_price" class="form-control"
                                value="{{ $product->discount_price }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Название</label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Бренд</label>
                            <input type="text" name="brand" class="form-control" value="{{ $product->brand }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Страна</label>
                            <input type="text" name="country" class="form-control" value="{{ $product->country }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Раппорт</label>
                            <input type="text" name="sticking" class="form-control" value="{{ $product->sticking }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Материал</label>
                            <input type="text" name="material" class="form-control" value="{{ $product->material }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Цена прихода</label>
                            <input type="text" name="purchase_price" class="form-control" value="{{ $product->purchase_price }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Цена продажи</label>
                            <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ $product->sale_price }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Категории</label>
                            <select name="category_ids[]" class="form-select" multiple required>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $product->categories->contains($category->id) ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Комнаты</label>
                            <select name="room_ids[]" class="form-select" multiple>
                                @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ $product->rooms->contains($room->id) ? 'selected' : '' }}>
                                    {{ $room->room_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Описание</label>
                            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Подробное описание</label>
                            <textarea name="detailed" class="form-control">{{ $product->detailed }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="mt-4">

                <h5 class="text-primary mt-3" style="margin-left: 20px;">Привязка компаньонов</h5>

                <div class="col-md-12 mt-2" style="margin-left: 20px; max-width:95%;">
                    <label class="form-label">Компаньоны (другие варианты)</label>
                    <select name="variants[{{ $variant->id }}][companion_variant_ids][]" class="form-select" multiple>
                        @foreach($allVariants as $otherVariant)
                        @if($otherVariant->id !== $variant->id)
                        @php
                        $selected = $variant->companions->contains('id', $otherVariant->id);
                        @endphp
                        <option value="{{ $otherVariant->id }}" {{ $selected ? 'selected' : '' }}>
                            {{ $otherVariant->sku ?? 'Без артикула' }}
                            ({{ $otherVariant->product->name }})
                        </option>
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