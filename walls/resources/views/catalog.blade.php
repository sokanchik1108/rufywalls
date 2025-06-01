@extends('layouts.main')

@section('title', 'Каталог')

@section('content')
<div class="catalog-header">
    <h1>КАТАЛОГ ОБОЕВ RAFY WALLS</h1>
</div>

<div class="catalog-container">
    <!-- Фильтры -->
    <div class="filters">
        <h3>Фильтры</h3>

        <a href="{{ route('catalog') }}" style="color:black;text-decoration:none;">Все товары</a>

        <form id="filter-form" method="GET" action="{{ route('catalog') }}" style="margin-top: 10px;">


            <input type="hidden" name="room_id" id="room_id" value="{{ request('room_id') }}">


            <!-- Категории -->
            <div class="mb-3">
                <label for="category_id" class="form-label">Категория</label>
                <select name="category_id" class="form-select" id="category_id">
                    <option value="">Все</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                    @endforeach
                </select>
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

            <!-- Цена -->
            <div class="filter-section">
                <label>Цена от</label>
                <input type="number" name="price_min" value="{{ request('price_min') }}">
                <label>до</label>
                <input type="number" name="price_max" value="{{ request('price_max') }}">
            </div>

            <!-- Наличие -->
            <div class="filter-section">
                <label class="checkbox-item">
                    <input type="checkbox" name="in_stock" value="1" @if(request('in_stock')=='1' ) checked @endif>
                    Только в наличии
                </label>
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
                    <input type="checkbox" name="color[]" value="{{ $color }}" {{ is_array(request('color')) && in_array($color, request('color')) ? 'checked' : '' }}>
                    {{ $color }}
                </label>
                @endforeach
            </div>

            <div class="filter-section">
                <a href="{{ route('catalog') }}" class="filters-reset-btn">Сбросить фильтры</a>
            </div>
        </form>
    </div>

    <!-- Каталог -->
    <div class="product-list" id="product-container">
        @include('partials.products', ['products' => $products])
    </div>
</div>

<!-- JavaScript для AJAX -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filter-form');

        initRoomLinks();
        initTopBarListeners();

        form.addEventListener('change', function(e) {
            sendAjax();
        });

        document.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                fetch(link.href, {
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
                        initTopBarListeners();
                    });
            }
        });

        function sendAjax() {
            const formData = new FormData(form);

            const searchInput = document.getElementById('search');
            const sortSelect = document.getElementById('sort');
            if (searchInput) formData.set('search', searchInput.value);
            if (sortSelect) formData.set('sort', sortSelect.value);

            const params = new URLSearchParams(formData).toString();

            fetch(`{{ route('catalog') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('product-container').innerHTML = html;
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    initTopBarListeners();
                })
                .catch(error => console.error('Ошибка при фильтрации:', error));
        }

        function initTopBarListeners() {
            const searchInput = document.getElementById('search');
            const sortSelect = document.getElementById('sort');

            // Слушаем Enter в поле поиска (input type="text")
            if (searchInput) {
                searchInput.removeEventListener('keypress', searchInput._listener || (() => {}));
                const handler = function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        sendAjax();
                    }
                };
                searchInput.addEventListener('keypress', handler);
                searchInput._listener = handler;
            }

            // Изменение сортировки — сразу применяем
            if (sortSelect) {
                sortSelect.removeEventListener('change', sortSelect._listener || (() => {}));
                const handler = sendAjax;
                sortSelect.addEventListener('change', handler);
                sortSelect._listener = handler;
            }
        }

        function initRoomLinks() {
            document.querySelectorAll('.filter-links a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const roomId = this.dataset.room;
                    document.getElementById('room_id').value = roomId;
                    sendAjax();
                });
            });
        }
    });
</script>



<!-- Стили -->
<style>
    .catalog-header {
        text-align: center;
        background-color: #f0f0f0;
        padding: 50px 0;
    }

    .catalog-header h1 {
        font-size: 48px;
        font-weight: bold;
        color: #333;
        text-transform: uppercase;
    }

    .catalog-container {
        display: flex;
        padding: 30px;
        gap: 30px;
    }

    .filters {
        width: 20%;
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 10px;
    }

    .filters h3 {
        font-size: 22px;
        margin-bottom: 10px;
    }

    .filter-section {
        margin-bottom: 20px;
    }

    .filter-section label {
        font-size: 14px;
        color: #555;
        display: block;
        margin-bottom: 8px;
    }

    .filter-links {
        padding-left: 0;
        margin: 0;
    }

    .filter-links li {
        list-style: none;
        margin-bottom: 6px;
    }

    .filter-links li a {
        text-decoration: none;
        color: #333;
        font-size: 14px;
        transition: transform 0.2s ease, color 0.3s ease;
        display: inline-block;
    }

    .filter-links li a:hover {
        transform: scale(1.05);
        color: black;
    }

    .checkbox-item {
        display: block;
        margin: 5px 0;
        font-size: 14px;
        color: #333;
    }

    .filters-reset-btn {
        display: inline-block;
        width: 100%;
        padding: 12px;
        background-color: black;
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .filters-reset-btn:hover {
        background-color: #222;
    }

    .product-list {
        flex-grow: 1;
    }

    .no-results {
        font-size: 18px;
        text-align: center;
        margin-top: 30px;
        color: #555;
    }
</style>
@endsection