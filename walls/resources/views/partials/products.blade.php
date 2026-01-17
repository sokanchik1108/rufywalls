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
    /************************************************************
    * ✅ ГЛОБАЛЬНЫЕ СТАТИЧЕСКИЕ И СЕССИОННЫЕ ПЕРЕМЕННЫЕ
    ************************************************************/
    static $groupColorMap = [];
    static $usedGroupColors = [];
    static $groupIndex = 0;

    // Достаём кеш 7-х картинок из сессии
    $productSeventhCache = session('product_seventh_cache', []);

    /************************************************************
    * ✅ ПОДГОТОВКА ОСНОВНЫХ ДАННЫХ
    ************************************************************/
    $isVariant = isset($item->product);
    $product = $isVariant ? $item->product : $item;
    $productVariants = $product->variants ?? collect();
    $selectedColors = request()->color ? array_map('strtolower', (array) request()->color) : [];

    /************************************************************
    * ✅ ВЫБОР shownVariant
    ************************************************************/
    if (!empty($selectedColors)) {
    $matchedVariants = $productVariants->filter(function ($v) use ($selectedColors) {
    return in_array(strtolower(trim((string)$v->color)), $selectedColors);
    });

    if ($matchedVariants->isEmpty()) {
    continue;
    }

    $shownVariant = $matchedVariants->first();
    } else {
    if ($isVariant) {
    $shownVariant = $item;
    } else {
    $variantsWith7 = $productVariants->filter(function ($v) {
    $imgs = json_decode($v->images ?? '[]', true) ?? [];
    return count($imgs) >= 7;
    });

    $productVariantsForLogic =
    $variantsWith7->isNotEmpty() && $variantsWith7->count() < $productVariants->count()
        ? $variantsWith7
        : $productVariants;

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
        $colors = $productVariantsForLogic->map(fn($v) => strtolower(trim((string)$v->color)))
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
        strtolower(trim((string)$v->color)) === $targetColor
        ) ?? $productVariantsForLogic->first();
        }
        }

        /************************************************************
        * ✅ 7-я картинка (теперь сохраняется в сессию навсегда)
        ************************************************************/
        $seventhImage = null;
        $imgs = json_decode($shownVariant->images ?? '[]', true) ?? [];

        // 1️⃣ Проверяем, есть ли картинка в сессии
        if (isset($productSeventhCache[$product->id])) {
        $seventhImage = $productSeventhCache[$product->id];
        } else {
        // 2️⃣ Ищем 7-ю среди текущего варианта
        if (isset($imgs[6])) {
        $seventhImage = $imgs[6];
        } else {
        // 3️⃣ Ищем среди всех вариантов
        $fallbackVariant = $productVariants->first(function ($v) {
        $vi = json_decode($v->images ?? '[]', true) ?? [];
        return isset($vi[6]);
        });

        if ($fallbackVariant) {
        $vi = json_decode($fallbackVariant->images ?? '[]', true) ?? [];
        $seventhImage = $vi[6];
        }
        }

        // 4️⃣ Если нашли — сохраняем в сессию
        if ($seventhImage) {
        $productSeventhCache[$product->id] = $seventhImage;
        session(['product_seventh_cache' => $productSeventhCache]);
        }
        }

        /************************************************************
        * ✅ Формирование набора картинок
        ************************************************************/
        $images = [];

        if (request()->filled('search')) {
        // В поиске — только первая
        if (!empty($imgs)) {
        $images[] = $imgs[0];
        }
        } else {
        // 1️⃣ Всегда добавляем сохранённую 7-ю
        if ($seventhImage) {
        $images[] = $seventhImage;
        }

        // 2️⃣ Первая картинка текущего варианта
        $images[] = $imgs[0] ?? null;

        // 3️⃣ Первые картинки других вариантов
        foreach ($productVariants as $otherVariant) {
        if ($otherVariant->id === $shownVariant->id) continue;

        if (!empty($selectedColors)) {
        $variantColor = strtolower(trim((string)$otherVariant->color));
        if (!in_array($variantColor, $selectedColors)) continue;
        }

        $otherImgs = json_decode($otherVariant->images ?? '[]', true) ?? [];
        if (!empty($otherImgs)) {
        $images[] = $otherImgs[0];
        }
        }
        }

        $images = collect($images)->filter(fn($img) => filled($img))->unique()->values()->all();
        @endphp


        {{-- =========================================================
    💎 КАРТОЧКА ТОВАРА
========================================================= --}}
        <a href="{{ route('product.show', $product->id) }}?variant={{ $shownVariant->id }}" class="product-card-link">
            <div class="product-card rafy-card-square">

                @if (!empty($images))
                <div class="rafy-carousel-wrapper position-relative"
                    onmouseenter="this.classList.add('hover-enabled')"
                    onmouseleave="this.classList.remove('hover-enabled')">

                    <div id="carousel{{ $item->id ?? $product->id }}" class="carousel slide">
                        <div class="carousel-inner">
                            @foreach ($images as $index => $image)
                            @if ($image)
                            @php
                            // LCP = первая картинка первого товара
                            $isLCP = $loop->first && $loop->parent->first;
                            @endphp

                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                @if ($isLCP)
                                {{-- ✅ LCP image --}}
                                <img
                                    src="{{ asset('storage/' . $image) }}"
                                    class="rafy-card-img"
                                    alt="{{ $product->name }}"
                                    loading="eager"
                                    fetchpriority="high"
                                    width="400"
                                    height="400">
                                @else
                                {{-- lazy для всех остальных --}}
                                <img
                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=="
                                    data-src="{{ asset('storage/' . $image) }}"
                                    class="rafy-card-img lazy-slide"
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                    width="400"
                                    height="400">
                                @endif
                            </div>
                            @endif
                            @endforeach
                        </div>

                        @if (count($images) > 1)
                        <button class="carousel-control-prev" type="button"
                            data-bs-target="#carousel{{ $item->id ?? $product->id }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button"
                            data-bs-target="#carousel{{ $item->id ?? $product->id }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                        @endif
                    </div>
                </div>
                @endif

                @if (!empty($product->status))
                <div class="rafy-status">{{ $product->status }}</div>
                @endif

                <div class="rafy-overlay"></div>

                <div class="rafy-hover-text">
                    <div class="rafy-articul">{{ $shownVariant->sku ?? '---' }}</div>
                    <div class="rafy-divider"></div>
                    <div class="rafy-name">{{ $product->name }}</div>
                    <div class="rafy-price">
                        @if ($product->sale_price == 0)
                        <div class="price-info">
                            <i class="bi bi-info-circle me-2" style="font-size: 1rem;"></i>
                            Информацию о цене можно узнать в WhatsApp
                        </div>
                        @elseif ($product->discount_price && $product->discount_price > $product->sale_price)
                        <span style="text-decoration: line-through;">
                            {{ number_format($product->discount_price, 0, '.', ' ') }} ₸
                        </span>
                        <span class="text-danger fw-bold ms-2">
                            {{ number_format($product->sale_price, 0, '.', ' ') }} ₸
                        </span>
                        @else
                        <span>{{ number_format($product->sale_price, 0, '.', ' ') }} ₸</span>
                        @endif
                    </div>
                </div>

            </div>
        </a>

        @empty
        <p>Товары не найдены.</p>
        @endforelse
</div>

<div class="pagination-wrapper">
    {{ $variants->links('vendor.pagination.custom') }}
</div>

<style>
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

    /* ========== Сетка товаров ========== */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
    }

    /* ========== Карточки ========== */
    .rafy-card-square {
        position: relative;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: box-shadow 0.25s ease;
        cursor: pointer;
        width: 100%;
        aspect-ratio: 1 / 1;
        will-change: opacity, background;
    }

    .rafy-card-square:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* ========== Карусель ========== */
    .rafy-carousel-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        will-change: opacity, background;
    }

    .rafy-card-img {
        width: 100%;
        height: 500px;
        object-fit: cover;
        backface-visibility: hidden;
        transform: translateZ(0);
        transition: none;
        /* убираем любые анимации изображения */
    }

    /* ========== Статус ========== */
    .rafy-status {
        position: absolute;
        top: 8px;
        left: 50%;
        transform: translateX(-50%);
        background: red;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 400;
        padding: 2px 6px;
        z-index: 3;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* ========== Затемнение ========== */
    .rafy-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0);
        transition: background 0.2s linear;
        z-index: 5;
        pointer-events: none;
    }

    .rafy-card-square:hover .rafy-overlay {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.45), transparent);
    }

    /* ========== Hover текст ========== */
    .rafy-hover-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        opacity: 0;
        transition: opacity 0.2s linear;
        z-index: 5;
        text-align: center;
        position: absolute;
        inset: 0;
        will-change: opacity;
        pointer-events: none;
    }

    .rafy-card-square:hover .rafy-hover-text {
        opacity: 1;
    }

    /* ========== Текст внутри hover ========== */
    .rafy-articul {
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
        font-family: 'Playfair Display', serif;
    }

    .rafy-divider {
        width: 70px;
        height: 1px;
        background: #01142f;
        margin: 4px auto;
    }

    .rafy-name {
        font-size: 0.9rem;
        font-weight: 300;
        margin: 0;
        font-family: 'Playfair Display', serif;
        letter-spacing: 1px;
    }

    .rafy-price {
        font-size: 1rem;
        font-weight: 300;
        margin-top: 4px;
        font-family: 'Playfair Display', serif;
    }

    .price-info {
        font-size: 0.8rem;
        /* уменьшенный шрифт */
        line-height: 1.3;
        text-align: center;
    }

    /* ===================== Мобильный ховер ===================== */
    @media (max-width: 768px) {

        /* Скрываем стандартный hover эффект */
        .rafy-card-square:hover .rafy-overlay,
        .rafy-card-square:hover .rafy-hover-text {
            opacity: 0;
        }
    }

    /* ========== Адаптивность ========== */
    @media (max-width: 1024px) {
        .top-bar {
            flex-direction: column;
        }

        .top-bar select {
            width: 100%;
        }
    }

    @media (max-width: 1024px) {
        .product-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .product-grid {
            grid-template-columns: 1fr;
        }

        .rafy-card-img {
            height: 400px;
        }
    }

    /* ===== Активный hover при удержании (работает на телефонах) ===== */
    .rafy-card-square.touch-active .rafy-overlay {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.45), transparent) !important;
        opacity: 1 !important;
        transition: background 0.2s linear;
    }

    .rafy-card-square.touch-active .rafy-hover-text {
        opacity: 1 !important;
        pointer-events: auto;
        transition: opacity 0.2s linear;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        /* ========================================================
           LAZY-LOADING + КАРУСЕЛЬ (оставлено без изменений)
        ========================================================= */
        document.querySelectorAll(".carousel").forEach(carousel => {
            const loadSlideImages = slides => {
                slides.forEach(slide => {
                    const img = slide?.querySelector("img.lazy-slide");
                    if (img && img.dataset.src && img.src !== img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                });
            };

            carousel.addEventListener("slide.bs.carousel", e => {
                loadSlideImages([
                    e.relatedTarget,
                    e.relatedTarget?.nextElementSibling,
                    e.relatedTarget?.previousElementSibling
                ]);
            });

            const first = carousel.querySelector(".carousel-item.active");
            loadSlideImages([first, first?.nextElementSibling]);
        });

        /* ========================================================
           ГЛОБАЛЬНЫЙ DELEGATED HOVER/TOUCH 
           Работает для всех карточек, даже если они перерисованы.
        ========================================================= */

        const HOVER_DELAY = 150;
        const TAP_THRESHOLD = 250;

        let activeCard = null;

        // Хранение состояния для каждой карточки
        const state = new WeakMap();

        function getState(card) {
            if (!state.has(card)) {
                state.set(card, {
                    hoverTimer: null,
                    touchStartTime: 0,
                    touchMoved: false,
                    hoverActive: false,
                    touchStartX: 0,
                    touchStartY: 0,
                    tappedInCarousel: false
                });
            }
            return state.get(card);
        }

        /* ============================
           TOUCH START
        ============================= */
        document.addEventListener("touchstart", (e) => {
            const link = e.target.closest(".product-card-link");
            const card = link?.querySelector(".rafy-card-square");
            if (!card) return;

            const st = getState(card);

            st.touchStartTime = Date.now();
            st.touchMoved = false;
            st.hoverActive = card.classList.contains("touch-active");
            st.tappedInCarousel = !!e.target.closest(".rafy-carousel-wrapper");

            const t = e.touches[0];
            st.touchStartX = t.clientX;
            st.touchStartY = t.clientY;

            clearTimeout(st.hoverTimer);

            if (!st.hoverActive) {
                st.hoverTimer = setTimeout(() => {
                    if (activeCard && activeCard !== card) {
                        activeCard.classList.remove("touch-active");
                    }
                    card.classList.add("touch-active");
                    activeCard = card;
                    st.hoverActive = true;
                    navigator.vibrate?.(30);
                }, HOVER_DELAY);
            }
        }, {
            passive: true
        });

        /* ============================
           TOUCH MOVE
        ============================= */
        document.addEventListener("touchmove", (e) => {
            const link = e.target.closest(".product-card-link");
            const card = link?.querySelector(".rafy-card-square");
            if (!card) return;

            const st = getState(card);

            const t = e.touches[0];
            const dx = Math.abs(t.clientX - st.touchStartX);
            const dy = Math.abs(t.clientY - st.touchStartY);

            if (dx > 10 || dy > 10) {
                st.touchMoved = true;
                clearTimeout(st.hoverTimer);
            }
        }, {
            passive: true
        });

        /* ============================
           TOUCH END (passive: false!!)
        ============================= */
        document.addEventListener("touchend", (e) => {
            const link = e.target.closest(".product-card-link");
            const card = link?.querySelector(".rafy-card-square");
            if (!card) return;

            const st = getState(card);
            const touchDuration = Date.now() - st.touchStartTime;

            clearTimeout(st.hoverTimer);

            // свайп
            if (st.touchMoved) return;

            // долгое удержание → только hover
            if (touchDuration > TAP_THRESHOLD && !st.hoverActive) {
                e.preventDefault();
                st.hoverActive = true;
                return;
            }

            // если hover включен и тап по карусели — не переходим
            if (st.hoverActive && st.tappedInCarousel) {
                e.preventDefault();
                return;
            }

            // если включён hover и тап не по карусели — выключаем hover
            if (st.hoverActive && !st.tappedInCarousel) {
                card.classList.remove("touch-active");
                if (activeCard === card) activeCard = null;
                st.hoverActive = false;
                e.preventDefault();
                return;
            }

            // обычный короткий тап → переход
            if (!st.hoverActive && !st.tappedInCarousel) {
                window.location.href = link.href;
            }
        }, {
            passive: false
        });

        /* ============================
           TAP вне карточки → снять hover
        ============================= */
        document.addEventListener("touchstart", (e) => {
            if (!e.target.closest(".rafy-card-square") && activeCard) {
                activeCard.classList.remove("touch-active");
                activeCard = null;
            }
        }, {
            passive: true
        });

    });
</script>