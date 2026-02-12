<form id="filter-form">
    <input type="hidden" name="room_id" id="room_id" value="{{ request('room_id') }}">

    <!-- 🔹 Только со скидкой -->
    <div class="filter-section" style="margin-top: 15px;">
        <label class="text-checkbox">
            <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') == '1' ? 'checked' : '' }}>
            <span style="font-size: medium;">Только со скидкой</span>
        </label>
    </div>

    <!-- 🔹 Категории (множественный выбор) -->
    <div class="filter-section">
        <label class="filter-label">Категории</label>
        <div class="filter-multiselect">
            <div class="select-display">Все</div>
            <div class="options">
                @foreach($categories as $category)
                <div class="option" data-value="{{ $category->slug }}">
                    {{ $category->category_name }}
                </div>
                @endforeach
            </div>
            <input type="hidden" name="category_id" value="">
        </div>
    </div>

    <!-- 🔹 Статус -->
    <div class="filter-section">
        <label class="filter-label">Статус</label>
        @foreach(['новинка', 'хит продаж', 'распродажа'] as $status)
        <label class="checkbox-item">
            <input type="checkbox" name="status[]" value="{{ $status }}"
                {{ is_array(request('status')) && in_array($status, request('status')) ? 'checked' : '' }}>
            {{ ucfirst($status) }}
        </label>
        @endforeach
    </div>

    <!-- 🔹 Цена -->
    <div class="filter-section">
        <label class="filter-label">Цена от</label>
        <input type="number" name="price_min" value="{{ request('price_min') }}">
        <label class="filter-label">до</label>
        <input type="number" name="price_max" value="{{ request('price_max') }}">
    </div>

    <!-- 🔹 Бренды -->
    <div class="filter-section">
        <label class="filter-label">Бренды</label>
        @foreach($brands as $brand)
        <label class="checkbox-item">
            <input type="checkbox" name="brand[]" value="{{ $brand }}"
                {{ is_array(request('brand')) && in_array($brand, request('brand')) ? 'checked' : '' }}>
            {{ $brand }}
        </label>
        @endforeach
    </div>

    <!-- 🔹 Материалы -->
    <div class="filter-section">
        <label class="filter-label">Материалы</label>
        @foreach($materials as $material)
        <label class="checkbox-item">
            <input type="checkbox" name="material[]" value="{{ $material }}"
                {{ is_array(request('material')) && in_array($material, request('material')) ? 'checked' : '' }}>
            {{ $material }}
        </label>
        @endforeach
    </div>

    <!-- 🔹 Цвета -->
    <div class="filter-section">
        <label class="filter-label">Цвета</label>
        @foreach($colors as $color)
        <label class="checkbox-item">
            <input type="checkbox" name="color[]" value="{{ $color }}"
                {{ is_array(request('color')) && in_array($color, request('color')) ? 'checked' : '' }}>
            {{ $color }}
        </label>
        @endforeach
    </div>

    <!-- 🔹 Раппорт -->
    <div class="filter-section">
        <label class="filter-label">Раппорт (стыковка)</label>
        <label class="checkbox-item">
            <input type="checkbox" name="sticking" value="yes" {{ request('sticking') === 'yes' ? 'checked' : '' }}>
            Есть
        </label>
        <label class="checkbox-item">
            <input type="checkbox" name="sticking" value="no" {{ request('sticking') === 'no' ? 'checked' : '' }}>
            Нет
        </label>
    </div>


    <div class="filter-section">
        <a href="{{ route('catalog') }}" class="filters-reset-btn">Сбросить фильтры</a>
    </div>

    <!-- 🔹 Результаты -->
    <div id="filter-result" class="filter-result">

    </div>
</form>

<style>
    .filter-section {
        margin-bottom: 15px;
    }

    .filter-section .filter-label {
        font-size: 16px;
        /* чуть больше шрифт для заголовков */
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
    }

    .filter-section label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
        cursor: pointer;
    }

    .checkbox-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #222;
    }

    .category-item.selected {
        background: #f0f0f0;
        border-radius: 8px;
        padding: 4px 8px;
    }

    .filter-links {
        list-style: none;
        padding: 0;
        margin: 10px 0;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-links li a {
        display: inline-block;
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .filter-links li a.active,
    .filter-links li a:hover {
        background: #222;
        color: #fff;
    }

    input[type="number"],
    select {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        outline: none;
    }

    .filters-reset-btn {
        display: inline-block;
        color: #d32f2f;
        text-decoration: none;
        font-weight: 600;
    }

    .filters-reset-btn:hover {
        text-decoration: underline;
    }

    .text-checkbox {
        cursor: pointer;
        font-weight: 700;
    }

    .text-checkbox input {
        display: none;
    }

    .text-checkbox span {
        color: #d32f2f;
        font-size: 1.2rem;
        transition: color 0.3s;
    }

    .text-checkbox input:checked+span {
        color: #b0b0b0;
    }

    .filter-result {
        text-align: center;
        margin-top: 20px;
        font-weight: 700;
        font-size: 1.1rem;
        color: #222;
    }
</style>

<style>
    /* --- Мультиселект категорий --- */
    .filter-multiselect {
        position: relative;
        width: 100%;
        max-width: 280px;
        font-family: 'Arial', sans-serif;
        user-select: none;
    }

    .filter-multiselect .select-display {
        border: 1px solid #ddd;
        padding: 10px 14px;
        cursor: pointer;
        background-color: #fff;
        color: #333;
        font-size: 15px;
        /* немного увеличенный шрифт */
        transition: border-color 0.2s, box-shadow 0.2s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-multiselect .select-display:hover {
        border-color: #aaa;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .filter-multiselect .select-display::after {
        content: '▼';
        font-size: 10px;
        margin-left: 8px;
        transition: transform 0.3s;
    }

    .filter-multiselect.open .select-display::after {
        transform: rotate(-180deg);
    }

    .filter-multiselect .options {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        max-height: 220px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
        z-index: 1000;
        margin-top: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: opacity 0.2s, transform 0.2s;
    }

    .filter-multiselect .options.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .filter-multiselect .option {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 15px;
        /* чуть больше шрифт */
        color: #333;
        transition: background 0.2s, color 0.2s;
    }

    .filter-multiselect .option:hover {
        background-color: #f5f5f5;
    }

    .filter-multiselect .option.active {
        background-color: black;
        color: #fff;
        font-weight: 500;
    }

    .filter-multiselect .option+.option {
        border-top: 1px solid #eee;
    }

    /* Ограничение ширины родителя */
    .filter-section {
        max-width: 100%;
        width: 100%;
    }

    /* --- Мобильные стили --- */
    @media (max-width: 767px) {
        .filter-multiselect {
            max-width: 95%;
            /* почти на всю ширину экрана */
        }

        .filter-multiselect .select-display {
            font-size: 14px;
            /* чуть крупнее для удобства */
        }

        .filter-multiselect .option {
            font-size: 14px;
            /* чуть крупнее для удобства */
            padding: 12px 16px;
        }
    }
</style>

<style>
    /* --- Заголовок + кнопка в одном блоке --- */
    .filters-modal-header {
        position: sticky;
        top: 0;
        z-index: 10000;
        background: #fff;
        width: 100%;
        /* блок во всю ширину */
        padding: 16px;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        /* кнопка растянута */
        border: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    /* Заголовок во всю ширину, текст слева */
    .filters-modal-header h3 {
        font-size: 24px;
        font-weight: 700;
        color: #111;
        margin: 0 0 16px 0;
        /* отступ снизу */
        text-align: left;
        width: 100%;
        /* заголовок во всю ширину блока */
    }

    /* Кнопка "Показать товары" */
    .filters-modal-header .apply-filters {
        width: 100%;
        /* кнопка растянута на всю ширину */
        padding: 14px 0;
        font-size: 16px;
        background: #000;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        outline: none;
        box-shadow: none;
        transition: background 0.2s;
    }

    .filters-modal-header .apply-filters:hover {
        background: #222;
    }

    /* Крестик закрыть */
    .close-filters {
        position: absolute;
        right: 16px;
        top: 16px;
        font-size: 28px;
        background: none;
        border: none;
        color: #222;
        cursor: pointer;
        line-height: 1;
    }

    /* Модальное окно */
    .filters-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: #fff;
        z-index: 9999;
        overflow-y: auto;
        padding: 0 0 40px 0;
        animation: fadeIn 0.2s ease;
    }

    .filters-modal.visible {
        display: block;
    }

    .filters-modal-content {
        max-width: 520px;
        margin: 0 auto;
        padding: 0 16px;
        /* боковые отступы для остального контента */
    }

    /* Мобильная версия */
    @media (max-width: 767px) {
        .filters-modal-header h3 {
            font-size: 22px;
        }

        .filters-modal-header .apply-filters {
            font-size: 15px;
            padding: 12px 0;
        }
    }

    /* Анимация */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>