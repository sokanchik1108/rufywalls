@extends('layouts.app')

@section('title', 'База данных')

@section('content')

<!-- Стили и скрипты -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f9fafb;
        color: #1f2937;
    }

    .container-fluid {
        padding-left: 16px;
        padding-right: 16px;
    }

    h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: white;
        height: 100%;
    }

    .card-img-top {
        height: 220px;
        object-fit: cover;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .card .btn {
        min-height: 38px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .badge-room {
        margin-right: 4px;
        background-color: #e5e7eb;
        color: #374151;
        font-weight: 500;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: 10px 14px;
        font-size: 0.95rem;
        transition: border-color 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    img.preview {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .alert-success {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #d1fae5;
        border-radius: 10px;
    }

    /* =========================
       Поиск
    ========================= */

    .search-wrapper {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        position: relative;
    }

    .search-box {
        position: relative;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.2s ease;
    }

    .search-box:focus-within {
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        border-color: #6366f1;
    }

    .modern-search {
        width: 100%;
        padding: 12px 40px 12px 42px;
        border: none;
        border-radius: 12px;
        background: transparent;
        font-size: 0.95rem;
        color: #111827;
    }

    .modern-search:focus {
        outline: none;
    }

    .clear-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 0;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .clear-btn:hover {
        background-color: #f3f4f6;
    }

    .clear-btn svg {
        pointer-events: none;
        stroke: #6b7280;
        transition: stroke 0.2s ease;
    }

    .clear-btn:hover svg {
        stroke: #374151;
    }

    /* =========================
       Автодополнение
    ========================= */

    .ui-autocomplete {
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 5px 0;
        font-size: 0.95rem;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.2s ease-in-out;
        z-index: 9999;
    }

    .ui-menu-item {
        padding: 8px 16px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .ui-menu-item:hover {
        background-color: #f3f4f6;
    }

    /* =========================
       Фильтр цены
    ========================= */

    .price-filter {
        max-width: 1100px;
        margin: 0 auto 25px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
    }

    .price-filter-title {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 15px;
    }

    .price-filter .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .filter-btn {
        min-height: 46px;
        border-radius: 10px;
        font-weight: 600;
    }

    .reset-btn {
        min-height: 46px;
        border-radius: 10px;
        font-weight: 600;
    }

    /* =========================
       Пагинация
    ========================= */

    .pagination {
        margin-top: 30px;
    }

    /* =========================
       Общие
    ========================= */

    .form-select,
    .form-select:focus {
        font-size: 16px !important;
        transform: none !important;
        -webkit-transform: none !important;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* =========================
       Адаптив
    ========================= */

    @media (max-width: 768px) {
        h2 {
            font-size: 1.5rem;
        }

        .price-filter {
            padding: 15px;
        }

        .search-wrapper {
            max-width: 100%;
        }
    }
</style>

<div class="container-fluid my-5">

    <h2 class="text-center fw-bold mb-4">
        База данных (Склад)
    </h2>

    {{-- Сообщение об успехе --}}
    @if(session('success'))
        <div class="alert alert-success text-center mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Ошибки --}}
    @if($errors->any())
        <div class="alert alert-danger mb-4">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif


    {{-- =========================
         ПОИСК ПО АРТИКУЛУ
    ========================== --}}
    <div class="search-wrapper mb-4">

        <div class="search-box">

            <input
                type="text"
                id="searchSku"
                class="modern-search"
                placeholder="Поиск по артикулу..."
                value="{{ request('sku') }}"
                autocomplete="off"
            >

            <button
                id="clearSearch"
                class="clear-btn"
                type="button"
                title="Очистить поиск"
                aria-label="Очистить"
                style="{{ request('sku') ? 'display:flex;' : '' }}"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="14"
                    height="14"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="#6b7280"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>

        </div>

    </div>


    {{-- =========================
         ФИЛЬТР ПО ЦЕНЕ ПРИХОДА
    ========================== --}}
    <div class="price-filter">

        <div class="price-filter-title">
            Фильтр по цене прихода
        </div>

        <div class="row g-3">

            {{-- От --}}
            <div class="col-md-4">

                <label
                    for="purchasePriceMin"
                    class="form-label"
                >
                    Цена прихода от
                </label>

                <input
                    type="number"
                    id="purchasePriceMin"
                    class="form-control"
                    min="0"
                    step="0.01"
                    placeholder="Например: 1000"
                    value="{{ request('purchase_price_min') }}"
                >

            </div>


            {{-- До --}}
            <div class="col-md-4">

                <label
                    for="purchasePriceMax"
                    class="form-label"
                >
                    Цена прихода до
                </label>

                <input
                    type="number"
                    id="purchasePriceMax"
                    class="form-control"
                    min="0"
                    step="0.01"
                    placeholder="Например: 5000"
                    value="{{ request('purchase_price_max') }}"
                >

            </div>


            {{-- Кнопки --}}
            <div class="col-md-4 d-flex align-items-end gap-2">

                <button
                    type="button"
                    id="filterPrice"
                    class="btn btn-primary filter-btn flex-grow-1"
                >
                    Фильтровать
                </button>

                <button
                    type="button"
                    id="resetFilters"
                    class="btn btn-outline-secondary reset-btn"
                >
                    Сбросить
                </button>

            </div>

        </div>

    </div>


    {{-- =========================
         СПИСОК ВАРИАНТОВ
    ========================== --}}
    <div
        id="variant-list-container"
        class="row g-4 justify-content-center"
    >

        @include('admin.partials.variant-cards', [
            'variants' => $variants,
            'categories' => $categories,
            'rooms' => $rooms,
            'allVariants' => $allVariants
        ])

    </div>

</div>


<script>
$(document).ready(function () {

    const $input = $('#searchSku');
    const $clearBtn = $('#clearSearch');
    const $container = $('#variant-list-container');

    /*
    |--------------------------------------------------------------------------
    | SKU AUTOCOMPLETE
    |--------------------------------------------------------------------------
    */

    $input.autocomplete({

        source: function (request, response) {

            $.ajax({

                url: "{{ route('admin.variants.autocomplete') }}",

                data: {
                    term: request.term
                },

                success: function (data) {
                    response(data);
                },

                error: function () {
                    response([]);
                }

            });

        },

        minLength: 1,
        delay: 100,

        select: function (event, ui) {

            $input.val(ui.item.value);

            fetchVariants(1);

            return false;
        }

    });


    /*
    |--------------------------------------------------------------------------
    | ПОИСК ПО ENTER
    |--------------------------------------------------------------------------
    */

    $input.on('keydown', function (e) {

        if (e.key === 'Enter') {

            e.preventDefault();

            fetchVariants(1);
        }

    });


    /*
    |--------------------------------------------------------------------------
    | ОЧИСТКА SKU
    |--------------------------------------------------------------------------
    */

    $clearBtn.on('click', function () {

        $input.val('').focus();

        $(this).hide();

        fetchVariants(1);

    });


    /*
    |--------------------------------------------------------------------------
    | ПОКАЗ / СКРЫТИЕ КНОПКИ ОЧИСТКИ
    |--------------------------------------------------------------------------
    */

    $input.on('input', function () {

        $clearBtn.toggle(
            $(this).val().length > 0
        );

    });


    /*
    |--------------------------------------------------------------------------
    | ФИЛЬТР ПО ЦЕНЕ
    |--------------------------------------------------------------------------
    */

    $('#filterPrice').on('click', function () {

        const min = $('#purchasePriceMin').val();
        const max = $('#purchasePriceMax').val();

        if (
            min !== '' &&
            max !== '' &&
            Number(min) > Number(max)
        ) {

            alert(
                'Минимальная цена не может быть больше максимальной.'
            );

            return;
        }

        fetchVariants(1);

    });


    /*
    |--------------------------------------------------------------------------
    | ENTER В ПОЛЯХ ЦЕНЫ
    |--------------------------------------------------------------------------
    */

    $('#purchasePriceMin, #purchasePriceMax').on(
        'keydown',
        function (e) {

            if (e.key === 'Enter') {

                e.preventDefault();

                $('#filterPrice').click();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | СБРОС ВСЕХ ФИЛЬТРОВ
    |--------------------------------------------------------------------------
    */

    $('#resetFilters').on('click', function () {

        $input.val('');

        $('#purchasePriceMin').val('');

        $('#purchasePriceMax').val('');

        $clearBtn.hide();

        fetchVariants(1);

    });


    /*
    |--------------------------------------------------------------------------
    | ПАГИНАЦИЯ
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.pagination a',
        function (e) {

            e.preventDefault();

            const url = new URL(
                $(this).attr('href'),
                window.location.origin
            );

            const page =
                url.searchParams.get('page') || 1;

            fetchVariants(page);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ЗАГРУЗКА ВАРИАНТОВ
    |--------------------------------------------------------------------------
    */

    function fetchVariants(page = 1) {

        const sku =
            $input.val().trim();

        const purchasePriceMin =
            $('#purchasePriceMin').val();

        const purchasePriceMax =
            $('#purchasePriceMax').val();


        /*
        |--------------------------------------------------------------------------
        | Проверка диапазона
        |--------------------------------------------------------------------------
        */

        if (
            purchasePriceMin !== '' &&
            purchasePriceMax !== '' &&
            Number(purchasePriceMin) >
            Number(purchasePriceMax)
        ) {

            alert(
                'Минимальная цена не может быть больше максимальной.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */

        $.ajax({

            url: "{{ route('admin.database') }}",

            method: 'GET',

            data: {

                page: page,

                sku: sku,

                purchase_price_min:
                    purchasePriceMin,

                purchase_price_max:
                    purchasePriceMax

            },

            success: function (data) {

                /*
                |--------------------------------------------------------------------------
                | Обновляем карточки
                |--------------------------------------------------------------------------
                */

                $container.html(
                    data.html
                );


                /*
                |--------------------------------------------------------------------------
                | Обновляем URL
                |--------------------------------------------------------------------------
                */

                const params =
                    new URLSearchParams();


                if (sku !== '') {

                    params.set(
                        'sku',
                        sku
                    );

                }


                if (purchasePriceMin !== '') {

                    params.set(
                        'purchase_price_min',
                        purchasePriceMin
                    );

                }


                if (purchasePriceMax !== '') {

                    params.set(
                        'purchase_price_max',
                        purchasePriceMax
                    );

                }


                params.set(
                    'page',
                    page
                );


                history.pushState(
                    null,
                    '',
                    '?' + params.toString()
                );

            },

            error: function (xhr) {

                console.error(xhr);

                alert(
                    'Ошибка при загрузке данных'
                );

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | ВОССТАНОВЛЕНИЕ ФИЛЬТРОВ ИЗ URL
    |--------------------------------------------------------------------------
    */

    const urlParams =
        new URLSearchParams(
            window.location.search
        );


    /*
    |--------------------------------------------------------------------------
    | SKU
    |--------------------------------------------------------------------------
    */

    if (urlParams.has('sku')) {

        $input.val(
            urlParams.get('sku')
        );

        $clearBtn.show();

    }


    /*
    |--------------------------------------------------------------------------
    | Минимальная цена
    |--------------------------------------------------------------------------
    */

    if (
        urlParams.has(
            'purchase_price_min'
        )
    ) {

        $('#purchasePriceMin').val(
            urlParams.get(
                'purchase_price_min'
            )
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Максимальная цена
    |--------------------------------------------------------------------------
    */

    if (
        urlParams.has(
            'purchase_price_max'
        )
    ) {

        $('#purchasePriceMax').val(
            urlParams.get(
                'purchase_price_max'
            )
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Если есть фильтры — загружаем через AJAX
    |--------------------------------------------------------------------------
    */

    if (

        urlParams.has('sku') ||

        urlParams.has(
            'purchase_price_min'
        ) ||

        urlParams.has(
            'purchase_price_max'
        )

    ) {

        fetchVariants(
            urlParams.get('page') || 1
        );

    }


    /*
    |--------------------------------------------------------------------------
    | ДИНАМИЧЕСКОЕ ДОБАВЛЕНИЕ ОТТЕНКОВ
    |--------------------------------------------------------------------------
    */

    let variantIndex = 0;


    /*
    |--------------------------------------------------------------------------
    | Добавить оттенок
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.add-variant-btn',
        function () {

            const wrapperId =
                $(this).data(
                    'wrapper-id'
                );

            const $wrapper =
                $('#' + wrapperId);


            const block = $(`
                <div class="row g-3 border p-3 mb-3 rounded">

                    <div class="col-md-5">

                        <label class="form-label">
                            Артикул
                        </label>

                        <input
                            type="text"
                            name="new_variants[${variantIndex}][sku]"
                            class="form-control"
                            placeholder="Введите артикул"
                        >

                    </div>


                    <div class="col-md-5">

                        <label class="form-label">
                            Оттенок
                        </label>

                        <input
                            type="text"
                            name="new_variants[${variantIndex}][color]"
                            class="form-control"
                            placeholder="Введите цвет"
                        >

                    </div>


                    <div class="col-md-2 d-flex align-items-end mb-2">

                        <button
                            type="button"
                            class="btn btn-sm btn-danger remove-variant-btn"
                        >
                            Удалить
                        </button>

                    </div>

                </div>
            `);


            $wrapper.append(block);

            variantIndex++;

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Удалить оттенок
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.remove-variant-btn',
        function () {

            $(this)
                .closest('.row')
                .remove();

        }
    );

});
</script>

@endsection
