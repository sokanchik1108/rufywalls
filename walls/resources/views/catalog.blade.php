@extends('layouts.main')

@section('title', 'Каталог')

@section('content')
<div class="catalog-header">
    <h1>КАТАЛОГ ОБОЕВ RAFY WALLS</h1>
</div>

<div class="catalog-container">

    @include('partials.filters')

    <!-- Каталог -->
    <div class="product-list" id="product-container">
        @include('partials.products')
    </div>
</div>

    @include('partials.footer')



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

        form.addEventListener('change', sendAjax);

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

            if (search) {
                if (search._handler) search.removeEventListener('keypress', search._handler);
                search._handler = function(e) {
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
                    const roomId = this.dataset.room;
                    document.getElementById('room_id').value = roomId;
                    sendAjax();
                };
                link.addEventListener('click', link._handler);
            });
        }
    });
</script>

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
        flex-wrap: wrap;
        padding: 30px;
        gap: 30px;
    }

    .toggle-filters {
        display: none;
        margin-bottom: 15px;
        padding: 10px 20px;
        background-color: black;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .filters {
        width: 35%;
        min-width: 310px;
        background-color: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        padding: 20px;
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
        width: 62%;
    }

    .no-results {
        font-size: 18px;
        text-align: center;
        margin-top: 30px;
        color: #555;
    }

    @media (max-width: 992px) {

        .filters-wrapper {
            display: block;
        }

        .toggle-filters {
            display: block;
        }

        .catalog-container {
            flex-direction: column;
        }

        .filters {
            display: none;
            position: static;
            width: 100%;
            margin-top: 10px;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .filters.visible {
            display: block;
        }

        .product-list {
            width: 100%;
        }
    }
</style>
@endsection