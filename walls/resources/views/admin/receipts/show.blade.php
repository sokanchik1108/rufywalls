<!DOCTYPE html>
<html lang="ru">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, maximum-scale=1.0"
>

<title>Приёмка #{{ $receipt->id }}</title>

<style>

    * {
        box-sizing: border-box;
    }

    html {
        -webkit-text-size-adjust: 100%;
    }

    body {
        margin: 0;
        background: #f5f6f8;
        color: #17191d;
        font-family:
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            Roboto,
            Arial,
            sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    button {
        font: inherit;
    }

    .page {
        width: 100%;
        max-width: 760px;
        margin: 0 auto;
        padding: 10px 12px 28px;
    }

    /* =========================================================
       HEADER
    ========================================================= */

    .header {
        height: 44px;
        display: grid;
        grid-template-columns: 36px 1fr 36px;
        align-items: center;
        margin-bottom: 10px;
    }

    .back,
    .edit-button {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e1e3e7;
        border-radius: 9px;
        background: #fff;
        color: #30343a;
        transition:
            background .15s ease,
            border-color .15s ease;
    }

    .back:hover,
    .edit-button:hover {
        background: #f0f1f3;
        border-color: #d5d8dc;
    }

    .back svg,
    .edit-button svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .title {
        text-align: center;
        font-size: 17px;
        font-weight: 750;
        letter-spacing: -.3px;
    }

    /* =========================================================
       INFO + COMMENT — ONE BLOCK
    ========================================================= */

    .info-block {
        overflow: hidden;
        margin-bottom: 8px;
        border: 1px solid #e3e5e8;
        border-radius: 11px;
        background: #fff;
    }

    .info {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
    }

    .info-item {
        min-width: 0;
        padding: 10px 12px;
    }

    .info-item + .info-item {
        border-left: 1px solid #eceef0;
    }

    .info-label {
        margin-bottom: 3px;
        color: #989da5;
        font-size: 9px;
        line-height: 1;
        font-weight: 500;
    }

    .info-value {
        overflow: hidden;
        color: #24272c;
        font-size: 12px;
        line-height: 1.25;
        font-weight: 650;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    /* =========================================================
       COMMENT
    ========================================================= */

    .comment {
        padding: 9px 12px 10px;
        border-top: 1px solid #eceef0;
    }

    .comment-title {
        margin-bottom: 3px;
        color: #9a9fa7;
        font-size: 9px;
        line-height: 1;
        font-weight: 500;
    }

    .comment-text {
        color: #42474e;
        font-size: 11px;
        line-height: 1.4;
        word-break: break-word;
    }

    /* =========================================================
       PRODUCTS
    ========================================================= */

    .products {
        overflow: hidden;
        border: 1px solid #e3e5e8;
        border-radius: 11px;
        background: #fff;
    }

    .products-header {
        height: 38px;
        padding: 0 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #eceef0;
        color: #24272c;
        font-size: 11px;
        font-weight: 700;
    }

    .products-count {
        color: #9a9fa7;
        font-size: 10px;
        font-weight: 500;
    }

    /* =========================================================
       PRODUCT
    ========================================================= */

    .product {
        min-height: 50px;
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            auto
            auto;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-bottom: 1px solid #f0f1f3;
    }

    .product:last-child {
        border-bottom: 0;
    }

    .product-main {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .sku {
        overflow: hidden;
        min-width: 0;
        color: #17191d;
        font-size: 12px;
        line-height: 1.2;
        font-weight: 700;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .batch {
        flex-shrink: 0;
        max-width: 130px;
        overflow: hidden;
        color: black;
        font-size: 10px;
        line-height: 1.2;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .batch::before {
        content: "(";
    }

    .batch::after {
        content: ")";
    }

    /* =========================================================
       QUANTITY
    ========================================================= */

    .quantity {
        min-width: 55px;
        text-align: right;
        color: #444950;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 650;
        white-space: nowrap;
    }

    /* =========================================================
       PRICE
    ========================================================= */

    .price {
        min-width: 76px;
        text-align: right;
        color: #17191d;
        font-size: 11px;
        line-height: 1.2;
        font-weight: 700;
        white-space: nowrap;
    }

    /* =========================================================
       DELETE
    ========================================================= */

    .actions {
        width: 100%;
        margin-top: 8px;
    }

    .delete-form {
        width: 100%;
        margin: 0;
    }

    .delete {
        width: 100%;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #f0d7d7;
        border-radius: 9px;
        background: #fff;
        color: #d43b3b;
        cursor: pointer;
        font-size: 11px;
        font-weight: 650;
        transition:
            background .15s ease,
            border-color .15s ease;
    }

    .delete:hover {
        background: #fff5f5;
        border-color: #e8c4c4;
    }

    .delete svg {
        width: 15px;
        height: 15px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 600px) {

        .page {
            padding: 7px 7px 22px;
        }

        .header {
            height: 40px;
            grid-template-columns: 34px 1fr 34px;
            margin-bottom: 7px;
        }

        .back,
        .edit-button {
            width: 34px;
            height: 34px;
            border-radius: 8px;
        }

        .back svg,
        .edit-button svg {
            width: 15px;
            height: 15px;
        }

        .title {
            font-size: 16px;
        }

        /* INFO BLOCK */

        .info-block {
            margin-bottom: 6px;
            border-radius: 9px;
        }

        .info-item {
            padding: 8px 9px;
        }

        .info-label {
            font-size: 8px;
        }

        .info-value {
            font-size: 10.5px;
        }

        /* COMMENT */

        .comment {
            padding: 8px 9px 9px;
        }

        .comment-title {
            font-size: 8px;
        }

        .comment-text {
            font-size: 10px;
        }

        /* PRODUCTS */

        .products {
            border-radius: 9px;
        }

        .products-header {
            height: 34px;
            padding: 0 9px;
            font-size: 10px;
        }

        .products-count {
            font-size: 9px;
        }

        .product {
            min-height: 43px;
            grid-template-columns:
                minmax(0, 1fr)
                auto
                auto;
            gap: 6px;
            padding: 7px 9px;
        }

        .product-main {
            gap: 5px;
        }

        .sku {
            font-size: 10.5px;
        }

        .batch {
            max-width: 95px;
            font-size: 10px;
        }

        .quantity {
            min-width: 43px;
            font-size: 9.5px;
        }

        .price {
            min-width: 65px;
            font-size: 9.5px;
        }

        /* DELETE */

        .actions {
            margin-top: 6px;
        }

        .delete {
            height: 35px;
            border-radius: 8px;
            font-size: 10px;
        }
    }

    /* =========================================================
       VERY SMALL PHONES
    ========================================================= */

    @media (max-width: 360px) {

        .page {
            padding-left: 5px;
            padding-right: 5px;
        }

        .product {
            gap: 5px;
            padding-left: 7px;
            padding-right: 7px;
        }

        .sku {
            font-size: 10px;
        }

        .batch {
            max-width: 80px;
            font-size: 10px;
        }

        .quantity {
            min-width: 39px;
            font-size: 9px;
        }

        .price {
            min-width: 59px;
            font-size: 9px;
        }
    }

</style>

</head>

<body>

<div class="page">

    {{-- =========================================================
         HEADER
    ========================================================= --}}

    <div class="header">

        <a
            href="{{ route('admin.receipts.index') }}"
            class="back"
            title="Назад"
            aria-label="Назад"
        >
            <svg viewBox="0 0 24 24">
                <path d="M19 12H5"></path>
                <path d="M12 19l-7-7 7-7"></path>
            </svg>
        </a>

        <div class="title">
            Приёмка #{{ $receipt->id }}
        </div>

        <a
            href="{{ route('admin.receipts.edit', $receipt) }}"
            class="edit-button"
            title="Изменить"
            aria-label="Изменить"
        >
            <svg viewBox="0 0 24 24">
                <path d="M12 20h9"></path>
                <path
                    d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"
                ></path>
            </svg>
        </a>

    </div>

    {{-- =========================================================
         INFO + COMMENT — ONE BLOCK
    ========================================================= --}}

    <div class="info-block">

        <div class="info">

            <div class="info-item">

                <div class="info-label">
                    Дата
                </div>

                <div class="info-value">
                    {{ $receipt->receipt_date->format('d.m.Y') }}
                </div>

            </div>

            <div class="info-item">

                <div class="info-label">
                    Склад
                </div>

                <div class="info-value">
                    {{ $receipt->warehouse->name }}
                </div>

            </div>

            <div class="info-item">

                <div class="info-label">
                    Количество
                </div>

                <div class="info-value">
                    {{ $receipt->items->sum('quantity') }} шт.
                </div>

            </div>

        </div>

        @if($receipt->comment)

            <div class="comment">

                <div class="comment-title">
                    Комментарий
                </div>

                <div class="comment-text">
                    {{ $receipt->comment }}
                </div>

            </div>

        @endif

    </div>

    {{-- =========================================================
         PRODUCTS
    ========================================================= --}}

    <div class="products">

        <div class="products-header">

            <span>
                Товары
            </span>

            <span class="products-count">
                {{ $receipt->items->count() }} поз.
            </span>

        </div>

        @foreach($receipt->items as $item)

            <div class="product">

                <div class="product-main">

                    <div class="sku">
                        {{ $item->variant->sku }}
                    </div>

                    <div class="batch">
                        {{ $item->batch->batch_code }}
                    </div>

                </div>

                <div class="quantity">
                    {{ $item->quantity }} шт.
                </div>

                <div class="price">
                    {{ number_format(
                        (float) $item->purchase_price,
                        0,
                        ',',
                        ' '
                    ) }} ₸
                </div>

            </div>

        @endforeach

    </div>

    {{-- =========================================================
         DELETE
    ========================================================= --}}

    <div class="actions">

        <form
            method="POST"
            action="{{ route('admin.receipts.destroy', $receipt) }}"
            class="delete-form"
            onsubmit="return confirm('Удалить эту приёмку? Неиспользованный остаток будет убран со склада.')"
        >

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="delete"
                title="Удалить"
                aria-label="Удалить"
            >

                <svg viewBox="0 0 24 24">

                    <polyline points="3 6 5 6 21 6"></polyline>

                    <path d="M19 6l-1 14H6L5 6"></path>

                    <path d="M10 11v6"></path>

                    <path d="M14 11v6"></path>

                    <path d="M9 6V4h6v2"></path>

                </svg>

                <span>
                    Удалить приёмку
                </span>

            </button>

        </form>

    </div>

</div>

</body>
</html>

