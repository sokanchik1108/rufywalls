@extends('layouts.app')

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

    .search-wrapper {
        max-width: 420px;
        margin: 0 auto 2rem;
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

    .clear-btn svg {
        pointer-events: none;
        stroke: #6b7280;
        transition: stroke 0.2s ease;
    }

    .clear-btn:hover svg {
        stroke: #374151;
    }

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
    }

    .ui-menu-item {
        padding: 8px 16px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .ui-menu-item:hover {
        background-color: #f3f4f6;
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
</style>

<div class="container-fluid my-5">
    <h2 class="text-center fw-bold mb-4">База данных (Склад)</h2>

    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <div class="search-wrapper">
        <div class="search-box">
            <input type="text" id="searchSku" class="modern-search" placeholder="Поиск по артикулу...">
            <button id="clearSearch" class="clear-btn" type="button" title="Очистить поиск" aria-label="Очистить">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div id="variant-list-container" class="row g-4 justify-content-center">
        @include('admin.partials.variant-cards', [
        'variants' => $variants,
        'categories' => $categories,
        'rooms' => $rooms
        ])
    </div>
</div>

<script>
    $(document).ready(function() {
        const $input = $('#searchSku');
        const $clearBtn = $('#clearSearch');
        const $container = $('#variant-list-container');

        // SKU autocomplete
        $input.autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('admin.variants.autocomplete') }}",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            delay: 100
        });

        // Поиск по Enter
        $input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchVariants(1);
            }
        });

        // Очистка поиска
        $clearBtn.on('click', function() {
            $input.val('').focus();
            $(this).hide();
            fetchVariants(1);
        });

        // Показ/скрытие кнопки очистки
        $input.on('input', function() {
            $clearBtn.toggle($(this).val().length > 0);
        });

        // Пагинация
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            fetchVariants(page);
        });

        // Получение вариантов по SKU и странице
        function fetchVariants(page = 1) {
            const sku = $input.val().trim();

            $.ajax({
                url: "{{ route('admin.database') }}",
                method: 'GET',
                data: {
                    page: page,
                    sku: sku
                },
                success: function(data) {
                    $container.html(data.html);
                    history.pushState(null, '', '?sku=' + encodeURIComponent(sku) + '&page=' + page);
                },
                error: function() {
                    alert('Ошибка при загрузке данных');
                }
            });
        }

        // При загрузке страницы: если есть параметры — подгружаем
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('sku')) {
            $input.val(urlParams.get('sku'));
            $clearBtn.show();
            fetchVariants(urlParams.get('page') || 1);
        }

        /// Динамическое добавление оттенков
        let variantIndex = 0;

        // При клике на кнопку "Добавить оттенок"
        $(document).on('click', '.add-variant-btn', function() {
            const wrapperId = $(this).data('wrapper-id');
            const $wrapper = $('#' + wrapperId);

            const block = $(`
        <div class="row g-3 border p-3 mb-3 rounded">
            <div class="col-md-5">
                <label class="form-label">Артикул</label>
                <input type="text" name="new_variants[${variantIndex}][sku]" class="form-control" placeholder="Введите артикул">
            </div>
            <div class="col-md-5">
                <label class="form-label">Оттенок</label>
                <input type="text" name="new_variants[${variantIndex}][color]" class="form-control" placeholder="Введите цвет">
            </div>
            <div class="col-md-2 d-flex align-items-end mb-2">
                <button type="button" class="btn btn-sm btn-danger remove-variant-btn">Удалить</button>
            </div>
        </div>
    `);

            $wrapper.append(block);
            variantIndex++; // увеличиваем индекс ТОЛЬКО после добавления
        });

        // Удаление блока варианта
        $(document).on('click', '.remove-variant-btn', function() {
            $(this).closest('.row').remove();
        });

    });
</script>

@endsection