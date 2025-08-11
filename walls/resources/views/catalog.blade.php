@extends('layouts.main')

@section('title', 'Каталог - RAFY WALLS')

@section('meta')
<meta name="description" content="Каталог обоев RAFY WALLS — широкий выбор и быстрая доставка.">
@endsection

@section('content')
<div class="py-5 px-3 text-center" style="background-color: #01142f; color: #FFFFDD;">
<h1 class="h2 text-uppercase mb-2" style="font-weight: 450;font-size:30px;">Каталог RAFY WALLS</h1>

    <p class="fs-5 mb-2">Стены — важнейший элемент атмосферы</p>
    <p class="fs-6 mb-0" style="max-width: 700px; margin: 0 auto;">
        Мы верим, что стены должны вдохновлять. Наш каталог объединяет коллекции,
        в которых каждая текстура и оттенок создают настроение. Найдите то, что близко именно вам!
    </p>
</div>



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
        initLazyImages(); // 👈 запускаем при первой загрузке

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
                    initLazyImages(); // 👈 запускаем после ajax
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

        // 🔁 Lazy loading изображений — функция
        function initLazyImages() {
            const lazyImages = document.querySelectorAll('img.lazy-img');

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.addEventListener('load', () => hideOverlay(img));
                            img.addEventListener('error', () => hideOverlay(img));
                            img.classList.remove('lazy-img');
                            observer.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: "100px 0px",
                    threshold: 0.01
                });

                lazyImages.forEach(img => observer.observe(img));
            } else {
                lazyImages.forEach(img => {
                    img.src = img.dataset.src;
                    img.addEventListener('load', () => hideOverlay(img));
                    img.addEventListener('error', () => hideOverlay(img));
                    img.classList.remove('lazy-img');
                });
            }

            function hideOverlay(img) {
                const overlay = img.previousElementSibling;
                if (overlay && overlay.classList.contains('loading-overlay')) {
                    overlay.style.display = 'none';
                }
            }
        }
    });
</script>
@endsection