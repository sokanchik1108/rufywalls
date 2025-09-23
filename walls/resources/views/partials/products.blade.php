<div class="top-bar">
    <div class="search-wrapper">
        <input
            type="text"
            id="search"
            value="{{ request('search') }}"
            placeholder="Введите название или артикул..."
            class="form-control"
            style="font-size: 16px;">
        <button type="button" id="clearSearch">&times;</button>
    </div>

    <select id="sort" class="form-select">
        <option value="">Без сортировки</option>
        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>По возрастанию цены</option>
        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>По убыванию цены</option>
        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>По названию (А-Я)</option>
        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>По названию (Я-А)</option>
    </select>
</div>

<div class="product-grid">
    @forelse ($variants as $item)
    @php
    /**
    * Логика выбора варианта для карточки
    */

    static $groupColorMap = [];
    static $usedGroupColors = [];
    static $groupIndex = 0;

    $isVariant = isset($item->product);
    $product = $isVariant ? $item->product : $item;
    $productVariants = $product->variants ?? collect();

    // --- Выбираем показной вариант ---
    if ($isVariant) {
        $shownVariant = $item;
    } else {
        // варианты с >= 7 картинками
        $variantsWith7 = $productVariants->filter(function ($v) {
            $imgs = json_decode($v->images ?? '[]', true) ?? [];
            return count($imgs) >= 7;
        });

        if ($variantsWith7->isNotEmpty() && $variantsWith7->count() < $productVariants->count()) {
            // есть смешанные: берём только среди с 7 картинками
            $productVariantsForLogic = $variantsWith7;
        } else {
            // иначе обычная логика
            $productVariantsForLogic = $productVariants;
        }

        // --- логика по цвету ---
        $groupVariantIds = collect();
        foreach ($productVariants as $v) {
            $groupVariantIds->push($v->id);
            $companions = method_exists($v, 'companions') ? ($v->companions ?? collect()) : collect();
            $companionOf = method_exists($v, 'companionOf') ? ($v->companionOf ?? collect()) : collect();
            $groupVariantIds = $groupVariantIds
                ->merge($companions->pluck('id'))
                ->merge($companionOf->pluck('id'));
        }
        $groupVariantIds = $groupVariantIds->unique()->sort()->values();
        $groupKey = $groupVariantIds->join('-');

        if (!isset($groupColorMap[$groupKey])) {
            $colors = $productVariantsForLogic->map(fn($v) => strtolower(trim((string) $v->color)))
                ->filter()
                ->unique()
                ->values();

            if ($colors->isEmpty()) {
                $fallback = strtolower(trim((string) optional($productVariantsForLogic->first())->color));
                $colors = collect($fallback ? [$fallback] : []);
            }

            $countColors = max(1, $colors->count());
            $colorIndex = $groupIndex % $countColors;
            $groupColorMap[$groupKey] = $colors[$colorIndex] ?? null;
            $usedGroupColors[] = $groupColorMap[$groupKey];
            $groupIndex++;
        }

        $targetColor = $groupColorMap[$groupKey];
        $shownVariant = $productVariantsForLogic->first(fn($v) =>
            $targetColor !== null &&
            strtolower(trim((string) $v->color)) === $targetColor
        ) ?? $productVariantsForLogic->first();
    }

    $color = $shownVariant->color ?? null;

    // --- Картинки для карусели ---
    $currentImages = json_decode($shownVariant->images ?? '[]', true) ?? [];
    $images = [];

    if (!empty($currentImages)) {
        $count = count($currentImages);
        if ($count >= 7) {
            $images[] = $currentImages[6]; // 7-я
            $images[] = $currentImages[0]; // первая
        } else {
            $images[] = $currentImages[0]; // только первая
        }
    }

    foreach ($productVariants as $otherVariant) {
        if ($shownVariant && $otherVariant->id === $shownVariant->id) continue;
        $otherImages = json_decode($otherVariant->images ?? '[]', true) ?? [];
        if (!empty($otherImages)) {
            $images[] = $otherImages[0];
        }
    }

    $images = collect($images)
        ->filter(fn($p) => filled($p))
        ->unique()
        ->values()
        ->all();
    @endphp

    <div class="product-card">
        @if (!empty($images))
        <div id="carousel{{ $item->id ?? $product->id }}" class="carousel slide mb-3">
            <div class="carousel-inner">
                @foreach ($images as $index => $image)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    <div class="position-relative" style="width: 100%; height: auto;">
                        <div class="loading-overlay"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                           background: rgba(255, 255, 255, 0.7); display: flex; align-items: center;
                           justify-content: center; z-index: 10; font-size: 14px; color: #555;">
                            Загружаем...
                        </div>
                        <img data-src="{{ asset('storage/' . $image) }}"
                            class="d-block w-100 lazy-img"
                            alt="Фото товара {{ $product->name ?? '' }}"
                            width="100%">
                    </div>
                </div>
                @endforeach
            </div>

            @if (count($images) > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $item->id ?? $product->id }}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $item->id ?? $product->id }}" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
            @endif
        </div>
        @endif

        <div class="product-info">
            <h4 class="product-title">
                {{ $product->name }}
                @if ($color)
                ({{ $color }})
                @endif

                @if (!empty($product->status))
                <span class="product-status ">
                    {{ $product->status }}
                </span>
                @endif
            </h4>
            <div class="product-desc-price">
                <p>{{ $product->description }}</p>

                @if ($product->discount_price && $product->discount_price > $product->sale_price)
                <div>
                    <span class="text-muted" style="text-decoration: line-through;">
                        {{ number_format($product->discount_price, 0, '.', ' ') }} ₸
                    </span>
                    <span class="text-danger fw-bold ms-2">
                        {{ number_format($product->sale_price, 0, '.', ' ') }} ₸
                    </span>
                </div>
                @else
                <span>{{ number_format($product->sale_price, 0, '.', ' ') }} ₸</span>
                @endif
            </div>

            <div class="btn-wrapper">
                <a href="{{ route('product.show', $product->id) }}" class="btn btn-dark">Подробнее</a>
            </div>
        </div>
    </div>
    @empty
    <p>Товары не найдены.</p>
    @endforelse
</div>

<div class="pagination-wrapper">
    {{ $variants->links('vendor.pagination.custom') }}
</div>




<style>
    .product-status {
        display: inline-block;
        background-color: red;
        /* тёмно-синий */
        color: #fff;
        font-size: 12px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 4px;
    }

    /* ========== Общие стили для Top Bar ========== */
    .top-bar {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    /* ========== Поле поиска с крестиком ========== */
    .search-wrapper {
        position: relative;
        flex: 1;
        min-width: 250px;
    }

    .search-wrapper input {
        width: 100%;
        padding: 10px 35px 10px 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 14px;
        background-color: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .search-wrapper input:focus {
        outline: none;
        border-color: #aaa;
        box-shadow: none;
    }

    /* Кнопка-крестик */
    .search-wrapper button#clearSearch {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        font-size: 18px;
        color: #aaa;
        cursor: pointer;
        display: none;
        padding: 0;
        line-height: 1;
        transition: color 0.2s ease;
    }

    .search-wrapper button#clearSearch:hover {
        color: #555;
    }

    /* ========== Сортировка Select ========== */
    .top-bar select {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 14px;
        width: 200px;
        min-width: 150px;
        transition: border-color 0.2s ease;
        background-color: #fff;
        appearance: none;
    }

    .top-bar select:focus {
        outline: none;
        border-color: #aaa;
        box-shadow: none;
    }

    /* ========== Autocomplete jQuery UI ========== */
    .ui-autocomplete {
        position: absolute;
        z-index: 1051;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 5px 0;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        font-family: inherit;
    }

    .ui-menu-item {
        padding: 8px 15px;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .ui-menu-item-wrapper:hover {
        background-color: transparent;
        color: #007bff;
    }

    .ui-menu-item-wrapper.ui-state-active {
        background-color: transparent !important;
        color: #007bff !important;
    }


    /* ========== Убираем синий бордер у всех полей при фокусе ========== */
    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        box-shadow: none;
    }

    /* ========== Каталог карточек ========== */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .product-card {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-card img {
        width: 100%;
        height: 500px;
        object-fit: cover;
    }

    .product-info {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        padding: 15px;
    }

    .product-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
        line-height: 1.2;
        min-height: 48px;
        overflow: hidden;
    }

    .product-desc-price {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .product-desc-price p {
        font-size: 14px;
        color: #777;
        margin-bottom: 8px;
        word-break: break-word;
        white-space: normal;
        flex-grow: 1;
    }

    .product-desc-price span {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-top: auto;
    }

    .btn-wrapper {
        margin-top: 10px;
    }

    .btn-dark {
        display: block;
        width: 100%;
        background-color: #000;
        color: #fff;
        padding: 10px 15px;
        text-align: center;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.2s ease;
    }

    .btn-dark:hover {
        background-color: #333;
    }

    /* ========== Адаптивность ========== */
    @media (max-width: 1024px) {
        .product-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .top-bar {
            flex-direction: column;
        }

        .top-bar select {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .product-grid {
            grid-template-columns: 1fr;
        }

        .product-card img {
            height: 300px;
        }
    }
</style>