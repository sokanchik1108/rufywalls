<!-- Кнопка для мобильных + фильтры рядом -->
<div class="filters-wrapper">
    <button class="toggle-filters">Фильтры</button>
    <!-- Фильтры -->
    <div class="filters" id="filters">
        <h3 style="margin-bottom: 15px;">Фильтры</h3>

        <a href="{{ route('catalog') }}" style="color:black;text-decoration:none;"><strong>Все товары</strong></a>

        <form id="filter-form" method="GET" action="{{ route('catalog') }}" style="margin-top: 10px;">
            <input type="hidden" name="room_id" id="room_id" value="{{ request('room_id') }}">


            <div class="filter-section" style="margin: 10px 0;">
                <label class="text-checkbox">
                    <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') == '1' ? 'checked' : '' }}>
                    <span>Только со скидкой</span>
                </label>
            </div>

            <style>
                .text-checkbox {
                    cursor: pointer;
                    font-weight: 700;
                }

                /* Скрываем сам чекбокс */
                .text-checkbox input {
                    display: none;
                }

                .text-checkbox span {
                    color: #d32f2f;
                    /* насыщенный красный по умолчанию */
                    transition: color 0.3s;
                }

                /* Когда выбран, меняем цвет текста */
                .text-checkbox input:checked+span {
                    color: #b0b0b0;
                    /* светло-серый */
                }
            </style>


            <!-- Категории -->
            <div class="mb-3">
                <label for="category_id" class="form-label">Категория</label>
                <select name="category_id" class="form-select" id="category_id">
                    <option value="">Все категории</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-section">
                <label>Статус</label>

                <label class="checkbox-item">
                    <input type="checkbox" name="status[]" value="новинка"
                        {{ is_array(request('status')) && in_array('новинка', request('status')) ? 'checked' : '' }}>
                    Новинка
                </label>

                <label class="checkbox-item">
                    <input type="checkbox" name="status[]" value="хит продаж"
                        {{ is_array(request('status')) && in_array('хит продаж', request('status')) ? 'checked' : '' }}>
                    Хит продаж
                </label>

                <label class="checkbox-item">
                    <input type="checkbox" name="status[]" value="распродажа"
                        {{ is_array(request('status')) && in_array('распродажа', request('status')) ? 'checked' : '' }}>
                    Распродажа
                </label>
            </div>





            <!-- Комнаты -->
            <ul class="filter-links mb-3">
                <li><a href="#" data-room="">Все</a></li>
                @foreach ($rooms as $room)
                <li>
                    <a href="#" data-room="{{ $room->id }}" class="{{ request('room_id') == $room->id ? 'active' : '' }}">
                        {{ $room->room_name }}
                    </a>
                </li>
                @endforeach
            </ul>

            <!-- Статусы товаров -->



            <!-- Цена -->
            <div class="filter-section">
                <label>Цена от</label>
                <input type="number" name="price_min" value="{{ request('price_min') }}">
                <label>до</label>
                <input type="number" name="price_max" value="{{ request('price_max') }}">
            </div>



            <!-- Бренды -->
            <div class="filter-section">
                <label>Бренды</label>
                @foreach($brands as $brand)
                <label class="checkbox-item">
                    <input type="checkbox" name="brand[]" value="{{ $brand }}" {{ is_array(request('brand')) && in_array($brand, request('brand')) ? 'checked' : '' }}>
                    {{ $brand }}
                </label>
                @endforeach
            </div>

            <!-- Материалы -->
            <div class="filter-section">
                <label>Материалы</label>
                @foreach($materials as $material)
                <label class="checkbox-item">
                    <input type="checkbox" name="material[]" value="{{ $material }}" {{ is_array(request('material')) && in_array($material, request('material')) ? 'checked' : '' }}>
                    {{ $material }}
                </label>
                @endforeach
            </div>

            <!-- Цвета -->
            <div class="filter-section">
                <label>Цвета</label>
                @foreach($colors as $color)
                <label class="checkbox-item">
                    <input type="checkbox" name="color[]" value="{{ $color }}"
                        {{ is_array(request('color')) && in_array($color, request('color')) ? 'checked' : '' }}>
                    {{ $color }}
                </label>
                @endforeach
            </div>

            <!-- Раппорт (стыковка) -->
            <div class="filter-section">
                <label>Раппорт (стыковка)</label>

                <label class="checkbox-item">
                    <input type="checkbox" name="sticking" value="yes" id="sticking-yes" data-value="yes"
                        {{ request('sticking') === 'yes' ? 'checked' : '' }}>
                    Есть
                </label>

                <label class="checkbox-item">
                    <input type="checkbox" name="sticking" value="no" id="sticking-no" data-value="no"
                        {{ request('sticking') === 'no' ? 'checked' : '' }}>
                    Нет
                </label>
            </div>

            <div class="filter-section">
                <a href="{{ route('catalog') }}" class="filters-reset-btn">Сбросить фильтры</a>
            </div>
        </form>
    </div>
</div>