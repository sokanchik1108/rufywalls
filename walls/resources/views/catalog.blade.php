@extends('layouts.main')

@section('title', 'RAFY WALLS — купить обои в Алматы и по Казахстану')


@section('meta')
<meta name="description" content="Купить обои в Алматы и по Казахстану — RAFY WALLS. Большой выбор дизайнов, доставка по стране. Обновите интерьер стильно и легко." />
@endsection

@section('content')
<section class="catalog-banner" style="position: relative; width: 100%; overflow: hidden;">

    <!-- Изображения -->
    <div class="catalog-images">
        <div class="catalog-image">
            <img src="{{ asset('images/баннер8.webp') }}" alt="Каталог RAFY WALLS">
        </div>
        <div class="catalog-image hide-on-mobile">
            <img src="{{ asset('images/баннер12.webp') }}" alt="Каталог RAFY WALLS">
        </div>
        <div class="catalog-image hide-on-mobile">
            <img src="{{ asset('images/баннер4.webp') }}" alt="Каталог RAFY WALLS">
        </div>
    </div>

    <!-- Затемняющий слой -->
    <div class="catalog-overlay"></div>

    <!-- Текст -->
    <div class="catalog-text">
        <h1 class="h2 mb-3">
            Каталог <span class="company-name">RAFY WALLS</span>
        </h1>
        <p class="fs-5 mb-2">Стены — важнейший элемент атмосферы</p>
        <p class="fs-6 mb-0">
            Мы верим, что стены должны вдохновлять. Наш каталог объединяет коллекции,
            в которых каждая текстура и оттенок создают настроение. Найдите то, что близко именно вам!
        </p>
    </div>

    <!-- Стили -->
    <style>
        .company-name {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            font-size: 1.9rem;
            letter-spacing: 2px;
        }

        .catalog-images {
            display: flex;
            width: 100%;
            height: 40vh;
            min-height: 220px;
            max-height: 420px;
        }

        .catalog-image {
            flex: 1 1 33.333%;
        }

        .catalog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .catalog-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
        }

        .catalog-text {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            padding: 20px;
            max-width: 750px;
            width: 90%;
            text-align: center;
        }

        .catalog-text h1 {
            font-size: 1.9rem;
            font-weight: 500;
        }

        .catalog-text p {
            font-size: 1.05rem;
            line-height: 1.5;
        }

        /* Планшеты */
        @media (max-width: 1024px) and (min-width: 769px) {
            .catalog-images {
                flex-direction: row;
                height: 500px;
                /* высота под контент или можно задать фиксированную */
            }

            .catalog-image {
                flex: 1 1 100%;
                /* первый блок занимает всю ширину */
                display: none;
                /* по умолчанию скрываем все */
            }

            /* Показываем только первый блок */
            .catalog-image:first-child {
                display: block;
                width: 100%;
                height: 100%;
                /* можно изменить высоту */
            }

            .catalog-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .catalog-text h1 {
                font-size: 1.8rem;
            }

            .catalog-text p {
                font-size: 0.95rem;
            }

            .company-name {
                font-size: 1.7rem;
            }
        }


        /* Телефоны */
        @media (max-width: 768px) {
            .catalog-banner {
                display: none;
                /* Скрываем всю секцию на малых экранах */
            }

            .catalog-images {
                flex-direction: column;
                height: auto;
            }

            .catalog-image {
                flex: none;
                height: 250px;
            }

            .catalog-text {
                padding: 8px;
                top: 45%;
                left: 50%;
                transform: translate(-50%, -50%);
                max-width: 90%;
                text-align: center;
            }

            .catalog-text h1 {
                font-size: 1rem;
            }

            .catalog-text p {
                font-size: 0.75rem;
            }

            .company-name {
                font-size: 1rem;
            }
        }

        /* Очень маленькие экраны */
        @media (max-width: 480px) {
            .catalog-image {
                height: 240px;
            }

            .catalog-text h1 {
                font-size: 0.9rem;
            }

            .catalog-text p {
                font-size: 0.7rem;
            }

            .company-name {
                font-size: 0.9rem;
            }
        }
    </style>


</section>







<div class="catalog-container">

    @include('partials.filters')

    <!-- Каталог -->
    <div class="product-list" id="product-container">
        @include('partials.products')
    </div>
</div>

@include('partials.footer')


@section('styles')
<link rel="stylesheet" href="{{ asset('css/catalog.min.css') }}?v=1.0.0">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>




<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filter-form');
    const toggleFiltersButton = document.querySelector('.toggle-filters');
    const filtersBlock = document.getElementById('filters');

    if (toggleFiltersButton) {
        toggleFiltersButton.addEventListener('click', () => {
            filtersBlock.classList.toggle('visible');
        });
    }

    initAllListeners();

    if (form) {
        form.addEventListener('change', sendAjax);
    }

    function sendAjax() {
        const formData = new FormData(form);
        const search = document.getElementById('search');
        const sort = document.getElementById('sort');

        if (search) formData.set('search', search.value);
        if (sort) formData.set('sort', sort.value);

        const params = new URLSearchParams(formData).toString();
        const url = `{{ route('catalog') }}?${params}`;

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('product-container').innerHTML = html;
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                initAllListeners();
            })
            .catch(err => console.error('Ошибка при фильтрации:', err));
    }

    function initAllListeners() {
        initTopBarListeners();
        initRoomLinks();
    }

    function initTopBarListeners() {
        const search = document.getElementById('search');
        const sort = document.getElementById('sort');
        const clearBtn = document.getElementById('clearSearch');

        if (search && clearBtn) {
            clearBtn.style.display = search.value.length > 0 ? 'block' : 'none';

            search.addEventListener('input', function() {
                clearBtn.style.display = this.value.length > 0 ? 'block' : 'none';
            });

            clearBtn.addEventListener('click', function() {
                search.value = '';
                clearBtn.style.display = 'none';
                sendAjax();
            });
        }

        if (search) {
            $(search).autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '{{ route("catalog.autocomplete") }}',
                        data: { term: request.term },
                        success: function(data) {
                            response(data.length === 0 ? [{
                                label: 'Товары не найдены',
                                value: '',
                                disabled: true
                            }] : data);
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
                    if (clearBtn) clearBtn.style.display = 'block';
                    sendAjax();
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                const li = $("<li>");
                const wrapper = $("<div>").text(item.label);

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

            // По Enter
            if (search._handler) search.removeEventListener('keypress', search._handler);
            search._handler = function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendAjax();
                }
            };
            search.addEventListener('keypress', search._handler);

            $(document).on('menufocus', '.ui-menu-item-wrapper.no-results', function(e) {
                e.preventDefault();
            });
        }

        if (sort) {
            if (sort._handler) sort.removeEventListener('change', sort._handler);
            sort._handler = sendAjax;
            sort.addEventListener('change', sort._handler);
        }
    }

    function initRoomLinks() {
        document.querySelectorAll('.filter-links a').forEach(link => {
            link.removeEventListener('click', link._handler || (() => {}));
            link._handler = function(e) {
                e.preventDefault();
                const roomId = this.dataset.room;
                document.getElementById('room_id').value = roomId;
                sendAjax();
            };
            link.addEventListener('click', link._handler);
        });
    }
});
</script>

@endsection