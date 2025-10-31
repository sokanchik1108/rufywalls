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

<style>
    /* Планшеты + iPad Pro + iPad Air */
    @media (min-width: 768px) and (max-width: 1024px) {
        .filters-wrapper {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
            margin-bottom: 10px;
        }

        /* Кнопка фильтров видна на планшетах */
        .toggle-filters {
            display: block;
            margin-bottom: 8px;
            font-size: 1.5rem;
            /* увеличили */
            padding: 12px 24px;
            /* увеличили */
            cursor: pointer;
            border-radius: 4px;
        }

        /* Блок фильтров скрыт по умолчанию */
        #filters {
            width: 100%;
            display: none;
            margin-bottom: 15px;
        }

        /* Показываем блок по классу visible */
        #filters.visible {
            display: block;
        }

        /* Заголовок фильтров */
        #filters h3 {
            font-size: 1.5rem;
            /* увеличили */
            font-weight: 700;
            margin-bottom: 12px;
        }

        /* Текст всех лейблов, ссылок, спанов */
        #filters label,
        #filters a,
        #filters span {
            font-size: 1.3rem;
            /* увеличили */
        }

        /* Поля ввода чисел */
        input[type="number"] {
            width: 100%;
            font-size: 1.1rem;
            /* чуть больше для удобства */
            padding: 6px 8px;
        }

        /* Селекты */
        select.form-select {
            width: 100%;
            font-size: 1.25rem;
            /* отдельно увеличили */
            padding: 8px 12px;
            /* отдельно увеличили */
        }

        .filter-section,
        .filter-links,
        .mb-3 {
            margin-bottom: 12px;
            /* чуть больше отступы */
        }
    }


    /* Телефоны */
    @media (max-width: 767px) {
        .toggle-filters {
            margin-bottom: -3px;
        }
    }
</style>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('filter-form');
    const toggleFiltersButton = document.querySelector('.toggle-filters');
    const filtersBlock = document.getElementById('filters');

    if (toggleFiltersButton) {
        toggleFiltersButton.addEventListener('click', () => {
            filtersBlock.classList.toggle('visible');
        });
    }

    if (form) form.addEventListener('change', sendAjax);

    initTopBarListeners();
    initRoomLinks(sendAjax);
    if (typeof initLazyCarousel === "function") initLazyCarousel(document.querySelectorAll(".carousel"));

    // ======================== AJAX обновление каталога ========================
    function sendAjax() {
        if (!window.catalogRoutes || !window.catalogRoutes.catalog) {
            console.error('catalog route not found. Add window.catalogRoutes.catalog in blade.');
            return;
        }

        const formData = new FormData(form);
        const search = document.getElementById('search');
        const sort = document.getElementById('sort');

        if (search) formData.set('search', search.value);
        if (sort) formData.set('sort', sort.value);

        const params = new URLSearchParams(formData).toString();
        const url = `${window.catalogRoutes.catalog}?${params}`;

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.text();
            })
            .then(html => {
                const container = document.getElementById('product-container');
                if (container) container.innerHTML = html;
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                // 🔁 Реинициализация после AJAX
                initTopBarListeners();
                initRoomLinks(sendAjax);
                if (typeof initLazyCarousel === "function")
                    initLazyCarousel(document.querySelectorAll(".carousel"));
            })
            .catch(err => console.error('Ошибка при фильтрации:', err));
    }

    // ======================== Верхняя панель (поиск, сортировка, очистка) ========================
    function initTopBarListeners() {
        const search = document.getElementById('search');
        const sort = document.getElementById('sort');
        const clearBtn = document.getElementById('clearSearch');

        if (search && clearBtn) {
            clearBtn.style.display = search.value.length > 0 ? 'block' : 'none';

            search.addEventListener('input', () => {
                clearBtn.style.display = search.value.length > 0 ? 'block' : 'none';
            });

            clearBtn.addEventListener('click', () => {
                search.value = '';
                clearBtn.style.display = 'none';
                sendAjax();
            });

            // --- AUTOCOMPLETE ---
            if (typeof $ === 'undefined' || typeof $.ui === 'undefined' || typeof $(search).autocomplete !== 'function') {
                console.error('jQuery UI autocomplete не найдена. Убедись, что jQuery UI подключен ДО filters.js');
            } else {
                const autocompleteUrl = window.catalogRoutes && window.catalogRoutes.autocomplete ? window.catalogRoutes.autocomplete : null;
                if (!autocompleteUrl) {
                    console.error('autocomplete route not found. Add window.catalogRoutes.autocomplete in blade.');
                } else {
                    $(search).autocomplete({
                        source: function(request, response) {
                            $.ajax({
                                url: autocompleteUrl,
                                method: 'GET',
                                data: { term: request.term },
                                success: function(data) {
                                    if (!Array.isArray(data) || data.length === 0) {
                                        response([{
                                            label: 'Товары не найдены',
                                            value: '',
                                            disabled: true
                                        }]);
                                    } else {
                                        response(data);
                                    }
                                },
                                error: function(xhr, status, err) {
                                    console.error('Autocomplete error:', status, err);
                                    response([]);
                                }
                            });
                        },
                        minLength: 1,
                        delay: 100,
                        select: function(event, ui) {
                            if (ui.item.disabled || ui.item.value === '') {
                                event.preventDefault();
                                return false;
                            }
                            search.value = ui.item.value;
                            clearBtn.style.display = 'block';
                            sendAjax();
                        }
                    }).autocomplete("instance")._renderItem = function(ul, item) {
                        const li = $("<li>");
                        const wrapper = $("<div>").text(item.label || item.value || '');
                        if (item.disabled) {
                            wrapper.css({
                                color: "#000",
                                fontStyle: "italic",
                                pointerEvents: "none",
                                cursor: "default"
                            });
                        }
                        wrapper.addClass("ui-menu-item-wrapper");
                        return li.append(wrapper).appendTo(ul);
                    };
                }
            }

            // Enter key
            if (search._handler) search.removeEventListener('keypress', search._handler);
            search._handler = e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendAjax();
                }
            };
            search.addEventListener('keypress', search._handler);
        }

        if (sort) {
            if (sort._handler) sort.removeEventListener('change', sort._handler);
            sort._handler = sendAjax;
            sort.addEventListener('change', sort._handler);
        }
    }

    // ======================== Комнаты / фильтр по ссылке ========================
    function initRoomLinks(sendAjax) {
        document.querySelectorAll('.filter-links a').forEach(link => {
            link.removeEventListener('click', link._handler || (() => {}));
            link._handler = function(e) {
                e.preventDefault();
                const roomInput = document.getElementById('room_id');
                if (roomInput) {
                    roomInput.value = this.dataset.room;
                    sendAjax();
                }
            };
            link.addEventListener('click', link._handler);
        });
    }
});

</script>