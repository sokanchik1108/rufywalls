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

        if (form) form.addEventListener('change', sendAjax);

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

                    // ⚡ инициализация ленивой загрузки для новых каруселей
                    initLazyCarousel(document.querySelectorAll(".carousel"));
                })
                .catch(err => console.error('Ошибка при фильтрации:', err));
        }

        function initAllListeners() {
            initTopBarListeners();
            initRoomLinks();
            initLazyCarousel(document.querySelectorAll(".carousel"));
        }

        function initTopBarListeners() {
            const search = document.getElementById('search');
            const sort = document.getElementById('sort');
            const clearBtn = document.getElementById('clearSearch');

            if (search && clearBtn) {
                clearBtn.style.display = search.value.length > 0 ? 'block' : 'none';
                search.addEventListener('input', () => clearBtn.style.display = search.value.length > 0 ? 'block' : 'none');
                clearBtn.addEventListener('click', () => {
                    search.value = '';
                    clearBtn.style.display = 'none';
                    sendAjax();
                });

                $(search).autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: '{{ route("catalog.autocomplete") }}',
                            data: {
                                term: request.term
                            },
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
                        clearBtn.style.display = 'block';
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

        function initRoomLinks() {
            document.querySelectorAll('.filter-links a').forEach(link => {
                link.removeEventListener('click', link._handler || (() => {}));
                link._handler = function(e) {
                    e.preventDefault();
                    document.getElementById('room_id').value = this.dataset.room;
                    sendAjax();
                };
                link.addEventListener('click', link._handler);
            });
        }

        // ======================== Ленивые карусели ========================
        function initLazyCarousel(carousels) {
            carousels.forEach(carousel => {
                carousel.addEventListener("slide.bs.carousel", function(event) {
                    const currentSlide = event.relatedTarget;
                    const nextSlide = currentSlide.nextElementSibling;
                    const prevSlide = currentSlide.previousElementSibling;
                    [currentSlide, nextSlide, prevSlide].forEach(slide => {
                        if (!slide) return;
                        const img = slide.querySelector("img.lazy-slide");
                        if (img && img.dataset.src && img.src !== img.dataset.src) {
                            img.src = img.dataset.src;
                        }
                    });
                });

                const first = carousel.querySelector(".carousel-item.active");
                const second = first?.nextElementSibling;
                [first, second].forEach(slide => {
                    if (!slide) return;
                    const img = slide.querySelector("img.lazy-slide");
                    if (img && img.dataset.src && img.src !== img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                });
            });
        }
    });
</script>

@endsection