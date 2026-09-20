<!DOCTYPE html>

<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover"
    >

    <meta
        name="theme-color"
        content="#f5f6f8"
    >

    <title>Новое списание</title>

    <style>

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html {
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        body {
            margin: 0;
            background: #f5f6f8;
            color: #17191c;
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
            font-size: 14px;
        }

        input,
        select,
        textarea {
            -webkit-appearance: none;
            appearance: none;
            touch-action: manipulation;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
        }

        .page {
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
            padding: 14px;
        }

        /* =========================
           HEADER
        ========================= */

        .top {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .title {
            margin: 0;
            text-align: center;
            font-size: 20px;
            line-height: 1.15;
            font-weight: 750;
            letter-spacing: -0.45px;
            white-space: nowrap;
        }

        .back {
            justify-self: start;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            height: 34px;
            padding: 0 11px;

            border: 1px solid #e1e4e8;
            border-radius: 9px;

            background: #fff;
            color: #3f444b;

            text-decoration: none;
            font-size: 12px;
            font-weight: 650;

            white-space: nowrap;
        }

        .top-submit {
            justify-self: end;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            height: 34px;
            padding: 0 13px;

            border: 0;
            border-radius: 9px;

            background: #01142f;
            color: #fff;

            cursor: pointer;
            font-size: 12px;
            font-weight: 700;

            white-space: nowrap;
        }

        .top-submit:active {
            transform: scale(.98);
        }

        /* =========================
           ALERT
        ========================= */

        .alert {
            margin-bottom: 10px;
            padding: 9px 11px;
            border-radius: 9px;
            font-size: 12px;
            line-height: 1.4;
        }

        .alert.error {
            color: #991b1b;
            background: #fff1f2;
            border: 1px solid #fecdd3;
        }

        .alert.success {
            color: #065f46;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
        }

        .alert ul {
            margin: 4px 0 0 16px !important;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            background: #fff;
            border: 1px solid #e6e8ec;
            border-radius: 13px;
            padding: 13px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .025);
        }

        /* =========================
           GENERAL FORM
        ========================= */

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 9px;
        }

        .field {
            min-width: 0;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 4px;
            color: #727780;
            font-size: 10px;
            line-height: 1.1;
            font-weight: 700;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #e0e3e7;
            border-radius: 8px;
            background: #fff;
            color: #17191c;
            transition:
                border-color .15s ease,
                box-shadow .15s ease;
        }

        input,
        select {
            height: 36px;
            padding: 0 9px;
        }

        textarea {
            height: 50px;
            min-height: 50px;
            padding: 8px 9px;
            resize: none;
            line-height: 1.35;
        }

        input::placeholder,
        textarea::placeholder {
            color: #a4a8ae;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #b9bec6;
            box-shadow: 0 0 0 2px rgba(17, 24, 39, .045);
        }

        /* =========================
           SELECT ARROW
        ========================= */

        select {
            background-image:
                linear-gradient(45deg, transparent 50%, #8b9199 50%),
                linear-gradient(135deg, #8b9199 50%, transparent 50%);

            background-position:
                calc(100% - 12px) 15px,
                calc(100% - 8px) 15px;

            background-size:
                4px 4px,
                4px 4px;

            background-repeat: no-repeat;
            padding-right: 25px;
        }

        /* =========================
           ITEMS HEADER
        ========================= */

        .items-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 9px;
        }

        .items-title {
            font-size: 13px;
            font-weight: 750;
            letter-spacing: -.1px;
        }

        .add-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            width: 31px;
            height: 31px;

            padding: 0;

            border: 0;
            border-radius: 8px;

            background: #01142f;
            color: #fff;

            cursor: pointer;

            font-size: 17px;
            font-weight: 500;
            line-height: 1;
        }

        .add-btn:active {
            transform: scale(.98);
        }

        /* =========================
           ITEMS
        ========================= */

        .items {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .item {
            position: relative;

            padding: 8px;

            border: 1px solid #e7e9ed;
            border-radius: 10px;

            background: #fafbfc;
        }

        .item-grid {
            display: grid;

            /*
             * Артикул | Партия | Кол-во | ×
             */

            grid-template-columns:
                minmax(120px, 1.15fr)
                minmax(125px, 1fr)
                68px
                30px;

            gap: 6px;
            align-items: end;
        }

        .item-field {
            min-width: 0;
            position: relative;
        }

        .item-field label {
            margin-bottom: 3px;
            font-size: 9px;
            color: #7c8188;
        }

        .sku-input,
        .batch-select,
        .quantity-input {
            height: 34px;
            font-size: 12px;
        }

        .sku-input {
            font-weight: 650;
        }

        .batch-select {
            padding-left: 8px;
            padding-right: 20px;
        }

        .quantity-input {
            padding: 0 5px;
            text-align: center;
            font-weight: 700;
        }

        .remove-btn {
            width: 30px;
            height: 34px;

            padding: 0;

            border: 1px solid #e1e4e8;
            border-radius: 8px;

            background: #fff;
            color: #a3a7ad;

            cursor: pointer;

            font-size: 17px;
            line-height: 1;
        }

        .remove-btn:hover,
        .remove-btn:active {
            color: #dc2626;
            border-color: #fecaca;
            background: #fff7f7;
        }

        /* =========================
           BATCH STOCK
        ========================= */

        .batch-stock {
            position: absolute;

            left: 1px;
            top: calc(100% + 2px);

            color: #858a91;

            font-size: 9px;
            line-height: 1;

            white-space: nowrap;
        }

        .batch-stock strong {
            color: #059669;
            font-weight: 700;
        }

        /* =========================
           SEARCH SUGGESTIONS
        ========================= */

        .suggestions {
            position: absolute;

            z-index: 100;

            left: 0;
            right: 0;
            top: calc(100% + 4px);

            display: none;

            max-height: 230px;
            overflow-y: auto;

            border: 1px solid #e1e4e8;
            border-radius: 9px;

            background: #fff;

            box-shadow:
                0 10px 25px rgba(15, 23, 42, .11);
        }

        .suggestion {
            padding: 8px 9px;

            border-bottom: 1px solid #f0f1f3;

            cursor: pointer;
        }

        .suggestion:last-child {
            border-bottom: 0;
        }

        .suggestion:active,
        .suggestion:hover {
            background: #f8f9fa;
        }

        .suggestion-main {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 7px;
        }

        .suggestion-sku {
            min-width: 0;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;

            font-size: 12px;
            font-weight: 750;
        }

        .suggestion-stock {
            color: #059669;

            font-size: 10px;
            font-weight: 700;

            white-space: nowrap;
        }

        .suggestion-product {
            margin-top: 2px;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;

            color: #858a91;

            font-size: 10px;
        }

        /* =========================
           EMPTY
        ========================= */

        .empty {
            padding: 13px;

            border: 1px dashed #d6d9de;
            border-radius: 9px;

            color: #858a91;

            text-align: center;

            font-size: 11px;
        }

        .no-batches {
            color: #dc2626;
            font-size: 10px;
        }

        /* =========================
           TABLET
        ========================= */

        @media (max-width: 700px) {

            .page {
                padding: 10px;
            }

            .card {
                padding: 10px;
                border-radius: 11px;
            }

            .top {
                gap: 6px;
                margin-bottom: 9px;
            }

            .title {
                font-size: 18px;
            }

            .back {
                height: 32px;
                padding: 0 9px;
                font-size: 11px;
            }

            .top-submit {
                height: 32px;
                padding: 0 10px;
                font-size: 11px;
            }
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 520px) {

            .page {
                padding:
                    max(8px, env(safe-area-inset-top))
                    8px
                    max(10px, env(safe-area-inset-bottom));
            }

            .top {
                grid-template-columns: 1fr auto 1fr;
                gap: 5px;
                margin-bottom: 9px;
            }

            .title {
                font-size: 16px;
                letter-spacing: -.3px;
                white-space: nowrap;
            }

            .back {
                height: 30px;
                border-radius: 8px;
                font-size: 10px;
                padding: 0 8px;
            }

            .top-submit {
                height: 30px;
                border-radius: 8px;
                font-size: 10px;
                padding: 0 9px;
            }

            .card {
                padding: 9px;
                margin-bottom: 8px;
                border-radius: 10px;
            }

            /*
             * Склад + дата
             * остаются в одной строке
             */

            .form-grid {
                grid-template-columns: 1fr 1fr;
                gap: 7px;
            }

            .field.full {
                grid-column: 1 / -1;
            }

            label {
                font-size: 9px;
                margin-bottom: 3px;
            }

            input,
            select {
                height: 34px;
                padding-left: 8px;
                padding-right: 8px;
                border-radius: 7px;
                font-size: 13px;
            }

            textarea {
                height: 42px;
                min-height: 42px;
                padding: 6px 8px;
                border-radius: 7px;
                font-size: 13px;
            }

            select {
                background-position:
                    calc(100% - 10px) 14px,
                    calc(100% - 6px) 14px;
            }

            .items-head {
                margin-bottom: 7px;
            }

            .items-title {
                font-size: 12px;
            }

            .add-btn {
                width: 29px;
                height: 29px;
                font-size: 16px;
            }

            .item {
                padding: 6px;
                border-radius: 8px;
            }

            /*
             * На телефоне всё в одну строку:
             *
             * Артикул | Партия | Кол-во | ×
             */

            .item-grid {
                grid-template-columns:
                    minmax(0, 1.15fr)
                    minmax(0, 1fr)
                    58px
                    28px;

                gap: 4px;
            }

            .item-field label {
                font-size: 8px;
                margin-bottom: 2px;
            }

            .sku-input,
            .batch-select,
            .quantity-input {
                height: 32px;
                font-size: 11px;
                border-radius: 7px;
            }

            .sku-input {
                padding-left: 7px;
                padding-right: 7px;
            }

            .batch-select {
                padding-left: 6px;
                padding-right: 17px;
            }

            .quantity-input {
                padding: 0 3px;
            }

            .remove-btn {
                width: 28px;
                height: 32px;
                border-radius: 7px;
                font-size: 16px;
            }

            .batch-stock {
                display: none;
            }

            .suggestions {
                max-height: 210px;
                border-radius: 8px;
            }

            .suggestion {
                padding: 7px 8px;
            }

            .suggestion-sku {
                font-size: 11px;
            }

            .suggestion-stock {
                font-size: 9px;
            }

            .suggestion-product {
                font-size: 9px;
            }
        }

        /* =========================
           VERY SMALL PHONES
        ========================= */

        @media (max-width: 360px) {

            .page {
                padding-left: 6px;
                padding-right: 6px;
            }

            .top {
                gap: 3px;
            }

            .card {
                padding: 8px;
            }

            .item {
                padding: 5px;
            }

            .item-grid {
                grid-template-columns:
                    minmax(0, 1.08fr)
                    minmax(0, .95fr)
                    52px
                    26px;

                gap: 3px;
            }

            .sku-input,
            .batch-select,
            .quantity-input {
                font-size: 10px;
            }

            .remove-btn {
                width: 26px;
            }

            .title {
                font-size: 15px;
            }

            .back {
                font-size: 9px;
                padding: 0 6px;
            }

            .top-submit {
                font-size: 9px;
                padding: 0 7px;
            }
        }

    </style>

</head>

<body>

<div class="page">

    {{-- HEADER --}}

    <div class="top">

        <a
            href="{{ route('admin.writeoffs.index') }}"
            class="back"
        >
            ← Назад
        </a>

        <h1 class="title">
            Новое списание
        </h1>

        <button
            type="submit"
            form="writeoffForm"
            class="top-submit"
        >
            Создать
        </button>

    </div>


    {{-- ERROR --}}

    @if(session('writeoff_error'))

        <div class="alert error">

            {{ session('writeoff_error') }}

        </div>

    @endif


    {{-- VALIDATION --}}

    @if($errors->any())

        <div class="alert error">

            <strong>
                Проверьте данные:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('admin.writeoffs.store') }}"
        id="writeoffForm"
    >

        @csrf


        {{-- ОСНОВНАЯ ИНФОРМАЦИЯ --}}

        <div class="card">

            <div class="form-grid">

                {{-- СКЛАД --}}

                <div class="field">

                    <label for="warehouse_id">
                        Склад
                    </label>

                    <select
                        name="warehouse_id"
                        id="warehouse_id"
                        required
                    >

                        <option value="">
                            Выберите
                        </option>

                        @foreach($warehouses as $warehouse)

                            <option
                                value="{{ $warehouse->id }}"
                                @selected(
                                    old('warehouse_id') == $warehouse->id
                                )
                            >
                                {{ $warehouse->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ДАТА --}}

                <div class="field">

                    <label for="writeoff_date">
                        Дата
                    </label>

                    <input
                        type="date"
                        name="writeoff_date"
                        id="writeoff_date"
                        value="{{ old(
                            'writeoff_date',
                            now('Asia/Almaty')->format('Y-m-d')
                        ) }}"
                        required
                    >

                </div>


                {{-- КОММЕНТАРИЙ --}}

                <div class="field full">

                    <label for="comment">
                        Комментарий
                    </label>

                    <textarea
                        name="comment"
                        id="comment"
                        placeholder="Причина списания..."
                    >{{ old('comment') }}</textarea>

                </div>

            </div>

        </div>


        {{-- ТОВАРЫ --}}

        <div class="card">

            <div class="items-head">

                <div class="items-title">
                    Товары
                </div>

                <button
                    type="button"
                    class="add-btn"
                    id="addItemBtn"
                    title="Добавить товар"
                    aria-label="Добавить товар"
                >
                    +
                </button>

            </div>


            <div
                class="items"
                id="items"
            ></div>

        </div>

    </form>

</div>


<script>

    const warehouseInput =
        document.getElementById(
            'warehouse_id'
        );

    const itemsContainer =
        document.getElementById(
            'items'
        );

    const addItemBtn =
        document.getElementById(
            'addItemBtn'
        );

    const form =
        document.getElementById(
            'writeoffForm'
        );


    let itemIndex = 0;


    /* ==========================================
       ESCAPE HTML
    ========================================== */

    function escapeHtml(value) {

        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

    }


    /* ==========================================
       CREATE ITEM
    ========================================== */

    function createItem() {

        const index =
            itemIndex++;

        const item =
            document.createElement(
                'div'
            );

        item.className =
            'item';

        item.dataset.index =
            index;


        item.innerHTML = `

            <div class="item-grid">

                <!-- АРТИКУЛ -->

                <div class="item-field sku-field">

                    <label>
                        Артикул
                    </label>

                    <input
                        type="text"
                        class="sku-input"
                        placeholder="Артикул"
                        autocomplete="off"
                        inputmode="text"
                    >

                    <input
                        type="hidden"
                        name="items[${index}][variant_id]"
                        class="variant-id"
                    >

                    <div class="suggestions"></div>

                </div>


                <!-- ПАРТИЯ -->

                <div class="item-field batch-field">

                    <label>
                        Партия
                    </label>

                    <select
                        name="items[${index}][batch_id]"
                        class="batch-select"
                        disabled
                        required
                    >

                        <option value="">
                            Партия
                        </option>

                    </select>

                    <div class="batch-stock"></div>

                </div>


                <!-- КОЛИЧЕСТВО -->

                <div class="item-field quantity-field">

                    <label>
                        Кол-во
                    </label>

                    <input
                        type="number"
                        name="items[${index}][quantity]"
                        class="quantity-input"
                        min="1"
                        step="1"
                        value="1"
                        required
                        disabled
                        inputmode="numeric"
                    >

                </div>


                <!-- УДАЛИТЬ -->

                <div class="item-field remove-field">

                    <label>&nbsp;</label>

                    <button
                        type="button"
                        class="remove-btn"
                        title="Удалить"
                        aria-label="Удалить товар"
                    >
                        ×
                    </button>

                </div>

            </div>

        `;


        itemsContainer.appendChild(
            item
        );

        setupItem(
            item
        );

        focusSku(
            item
        );

    }


    /* ==========================================
       FOCUS SKU
    ========================================== */

    function focusSku(item) {

        const input =
            item.querySelector(
                '.sku-input'
            );

        if (!input) {
            return;
        }

        setTimeout(() => {

            input.focus();

        }, 50);

    }


    /* ==========================================
       SETUP ITEM
    ========================================== */

    function setupItem(item) {

        const skuInput =
            item.querySelector(
                '.sku-input'
            );

        const variantId =
            item.querySelector(
                '.variant-id'
            );

        const batchSelect =
            item.querySelector(
                '.batch-select'
            );

        const quantityInput =
            item.querySelector(
                '.quantity-input'
            );

        const suggestions =
            item.querySelector(
                '.suggestions'
            );

        const batchStock =
            item.querySelector(
                '.batch-stock'
            );

        const removeBtn =
            item.querySelector(
                '.remove-btn'
            );


        let searchTimer =
            null;


        /* ======================================
           SEARCH INPUT
        ====================================== */

        skuInput.addEventListener(
            'input',
            function () {

                clearTimeout(
                    searchTimer
                );


                variantId.value =
                    '';


                batchSelect.innerHTML = `

                    <option value="">
                        Партия
                    </option>

                `;


                batchSelect.disabled =
                    true;


                quantityInput.disabled =
                    true;


                quantityInput.value =
                    1;


                batchStock.innerHTML =
                    '';


                const search =
                    this.value.trim();


                if (!warehouseInput.value) {

                    suggestions.innerHTML = `

                        <div class="suggestion">
                            Сначала выберите склад.
                        </div>

                    `;

                    suggestions.style.display =
                        'block';

                    return;

                }


                if (!search) {

                    suggestions.style.display =
                        'none';

                    return;

                }


                searchTimer =
                    setTimeout(() => {

                        searchVariants(
                            item,
                            search
                        );

                    }, 220);

            }
        );


        /* ======================================
           BATCH CHANGE
        ====================================== */

        batchSelect.addEventListener(
            'change',
            function () {

                const selected =
                    this.options[
                        this.selectedIndex
                    ];


                const stock =
                    selected?.dataset?.stock;


                if (
                    stock !== undefined &&
                    stock !== ''
                ) {

                    batchStock.innerHTML = `

                        Остаток:

                        <strong>
                            ${escapeHtml(stock)} шт.
                        </strong>

                    `;


                    quantityInput.max =
                        stock;


                    quantityInput.disabled =
                        false;


                    quantityInput.value =
                        1;


                    quantityInput.focus();


                    quantityInput.select();

                } else {

                    batchStock.innerHTML =
                        '';

                    quantityInput.disabled =
                        true;

                }

            }
        );


        /* ======================================
           REMOVE
        ====================================== */

        removeBtn.addEventListener(
            'click',
            function () {

                item.remove();


                if (
                    !itemsContainer.children.length
                ) {

                    createItem();

                }

            }
        );


        /* ======================================
           CLOSE SUGGESTIONS
        ====================================== */

        document.addEventListener(
            'click',
            function (event) {

                if (
                    !item.contains(
                        event.target
                    )
                ) {

                    suggestions.style.display =
                        'none';

                }

            }
        );

    }


    /* ==========================================
       SEARCH VARIANTS
    ========================================== */

    async function searchVariants(
        item,
        search
    ) {

        const suggestions =
            item.querySelector(
                '.suggestions'
            );


        const warehouseId =
            warehouseInput.value;


        if (!warehouseId) {
            return;
        }


        try {

            const url =
                `{{ route('admin.writeoffs.search-variants') }}` +
                `?warehouse_id=${encodeURIComponent(warehouseId)}` +
                `&search=${encodeURIComponent(search)}`;


            const response =
                await fetch(
                    url,
                    {
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Ошибка поиска'
                );

            }


            const variants =
                await response.json();


            suggestions.innerHTML =
                '';


            if (!variants.length) {

                suggestions.innerHTML = `

                    <div class="suggestion">
                        Ничего не найдено
                    </div>

                `;


                suggestions.style.display =
                    'block';

                return;

            }


            variants.forEach(
                variant => {

                    const element =
                        document.createElement(
                            'div'
                        );


                    element.className =
                        'suggestion';


                    element.innerHTML = `

                        <div class="suggestion-main">

                            <span class="suggestion-sku">

                                ${escapeHtml(
                                    variant.sku
                                )}

                            </span>

                            <span class="suggestion-stock">

                                ${escapeHtml(
                                    variant.stock
                                )} шт.

                            </span>

                        </div>

                        <div class="suggestion-product">

                            ${escapeHtml(
                                variant.product || ''
                            )}

                        </div>

                    `;


                    element.addEventListener(
                        'click',
                        function () {

                            selectVariant(
                                item,
                                variant
                            );

                        }
                    );


                    suggestions.appendChild(
                        element
                    );

                }
            );


            suggestions.style.display =
                'block';


        } catch (error) {

            suggestions.innerHTML = `

                <div class="suggestion">
                    Ошибка поиска товара
                </div>

            `;


            suggestions.style.display =
                'block';

        }

    }


    /* ==========================================
       SELECT VARIANT
    ========================================== */

    function selectVariant(
        item,
        variant
    ) {

        const skuInput =
            item.querySelector(
                '.sku-input'
            );

        const variantId =
            item.querySelector(
                '.variant-id'
            );

        const batchSelect =
            item.querySelector(
                '.batch-select'
            );

        const quantityInput =
            item.querySelector(
                '.quantity-input'
            );

        const suggestions =
            item.querySelector(
                '.suggestions'
            );

        const batchStock =
            item.querySelector(
                '.batch-stock'
            );


        skuInput.value =
            variant.sku;


        variantId.value =
            variant.id;


        suggestions.style.display =
            'none';


        batchSelect.innerHTML = `

            <option value="">
                Партия
            </option>

        `;


        batchStock.innerHTML =
            '';


        quantityInput.value =
            1;


        quantityInput.disabled =
            true;


        if (
            !variant.batches ||
            !variant.batches.length
        ) {

            batchSelect.innerHTML = `

                <option value="">
                    Нет партий
                </option>

            `;


            batchSelect.disabled =
                true;

            return;

        }


        variant.batches.forEach(
            batch => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    batch.id;


                option.dataset.stock =
                    batch.quantity;


                option.textContent =
                    `${batch.batch_code} — ${batch.quantity} шт.`;


                batchSelect.appendChild(
                    option
                );

            }
        );


        batchSelect.disabled =
            false;


        /*
         * Сразу открываем выбор партии.
         */

        batchSelect.focus();

    }


    /* ==========================================
       WAREHOUSE CHANGE
    ========================================== */

    warehouseInput.addEventListener(
        'change',
        function () {

            /*
             * Остатки партий зависят
             * от склада.
             *
             * Поэтому полностью
             * очищаем товары.
             */

            itemsContainer.innerHTML =
                '';

            createItem();

        }
    );


    /* ==========================================
       ADD ITEM
    ========================================== */

    addItemBtn.addEventListener(
        'click',
        function () {

            createItem();

        }
    );


    /* ==========================================
       FORM VALIDATION
    ========================================== */

    form.addEventListener(
        'submit',
        function (event) {

            const items =
                itemsContainer.querySelectorAll(
                    '.item'
                );


            if (!warehouseInput.value) {

                event.preventDefault();

                alert(
                    'Выберите склад.'
                );

                warehouseInput.focus();

                return;

            }


            if (!items.length) {

                event.preventDefault();

                alert(
                    'Добавьте хотя бы один товар.'
                );

                return;

            }


            let valid =
                true;


            items.forEach(
                item => {

                    const variantId =
                        item.querySelector(
                            '.variant-id'
                        )?.value;


                    const batchId =
                        item.querySelector(
                            '.batch-select'
                        )?.value;


                    const quantity =
                        parseInt(
                            item.querySelector(
                                '.quantity-input'
                            )?.value || 0,
                            10
                        );


                    const batchOption =
                        item.querySelector(
                            '.batch-select'
                        )?.selectedOptions?.[0];


                    const stock =
                        parseInt(
                            batchOption?.dataset?.stock || 0,
                            10
                        );


                    if (
                        !variantId ||
                        !batchId ||
                        quantity < 1 ||
                        (stock > 0 &&
                            quantity > stock)
                    ) {

                        valid =
                            false;


                        item.style.borderColor =
                            '#fca5a5';

                    } else {

                        item.style.borderColor =
                            '#e7e9ed';

                    }

                }
            );


            if (!valid) {

                event.preventDefault();

                alert(
                    'Для каждого товара выберите артикул, партию и корректное количество.'
                );

            }

        }
    );


    /* ==========================================
       CREATE FIRST ITEM
    ========================================== */

    createItem();

</script>

</body>

</html>

