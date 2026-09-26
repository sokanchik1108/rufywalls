<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>
        Оприходование #{{ $appropriation->id }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --bg: #f4f5f7;
            --card: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
            --primary: #111827;
            --danger: #dc2626;
            --radius: 14px;
        }

        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);

            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Arial,
                sans-serif;

            font-size: 14px;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        .page {
            width: 100%;
            max-width: 1080px;

            margin: 0 auto;

            padding: 22px 18px 40px;
        }

        /* =====================================================
           HEADER
        ===================================================== */

        .top {
            display: flex;
            align-items: center;

            gap: 12px;

            margin-bottom: 18px;
        }

        .back-btn {
            width: 40px;
            height: 40px;

            flex: 0 0 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px solid var(--border);
            border-radius: 11px;

            background: #fff;
            color: #111827;

            text-decoration: none;

            transition: .15s ease;
        }

        .back-btn:hover {
            background: #f9fafb;
        }

        .back-btn svg {
            width: 18px;
            height: 18px;
        }

        .title-block {
            min-width: 0;
        }

        .page-title {
            margin: 0;

            font-size: 22px;
            line-height: 1.2;

            font-weight: 700;

            letter-spacing: -.4px;
        }

        .page-subtitle {
            margin-top: 3px;

            color: var(--muted);

            font-size: 12px;
        }

        .top-actions {
            margin-left: auto;

            display: flex;
            align-items: center;

            gap: 7px;
        }

        /* =====================================================
           BUTTONS
        ===================================================== */

        .btn {
            min-height: 40px;

            padding: 0 14px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 6px;

            border-radius: 10px;

            text-decoration: none;

            font-size: 12px;
            font-weight: 600;

            transition: .15s ease;
        }

        .btn svg {
            width: 14px;
            height: 14px;
        }

        .btn-primary {
            border: 1px solid var(--primary);

            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: #1f2937;
        }

        .btn-danger {
            border: 1px solid #fecaca;

            background: #fff;
            color: var(--danger);
        }

        .btn-danger:hover {
            background: #fef2f2;
        }

        /* =====================================================
           ALERTS
        ===================================================== */

        .alerts {
            margin-bottom: 12px;
        }

        .alert {
            padding: 11px 13px;

            border-radius: 10px;

            font-size: 12px;
            line-height: 1.45;

            margin-bottom: 8px;
        }

        .alert:last-child {
            margin-bottom: 0;
        }

        .alert-success {
            border: 1px solid #bbf7d0;

            background: #fff;

            color: #166534;
        }

        .alert-error {
            border: 1px solid #fecaca;

            background: #fff;

            color: #991b1b;
        }

        /* =====================================================
           CARD
        ===================================================== */

        .card {
            margin-bottom: 12px;

            background: var(--card);

            border: 1px solid var(--border);
            border-radius: var(--radius);

            overflow: hidden;
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 10px;

            padding: 14px 16px;

            border-bottom: 1px solid #f0f1f3;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
        }

        .section-description {
            margin-top: 2px;

            color: var(--muted);

            font-size: 11px;
        }

        .card-body {
            padding: 16px;
        }

        /* =====================================================
           DOCUMENT INFO
        ===================================================== */

        .info-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr);

            gap: 10px;
        }

        .info-item {
            min-width: 0;
        }

        .info-label {
            display: block;

            margin-bottom: 5px;

            color: #6b7280;

            font-size: 10px;
            font-weight: 600;
        }

        .info-value {
            min-width: 0;

            color: #111827;

            font-size: 13px;
            font-weight: 600;

            overflow-wrap: anywhere;
        }

        /* =====================================================
           COUNT
        ===================================================== */

        .items-count {
            min-width: 25px;
            height: 25px;

            padding: 0 8px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 8px;

            background: #f3f4f6;
            color: #4b5563;

            font-size: 11px;
            font-weight: 700;
        }

        /* =====================================================
           TABLE
        ===================================================== */

        .items-table-head {
            display: grid;

            grid-template-columns:
                40px minmax(140px, 1fr) minmax(140px, 1fr) 110px 160px;

            gap: 8px;

            padding: 10px 16px 9px;

            background: #fafafa;

            border-bottom: 1px solid #f0f1f3;
        }

        .items-table-head div {
            color: #6b7280;

            font-size: 10px;
            font-weight: 700;

            white-space: nowrap;
        }

        .items-table-head div:first-child {
            text-align: center;
        }

        .items-table-head div:nth-child(4) {
            text-align: center;
        }

        .items-table-head div:last-child {
            text-align: right;
        }

        .items {
            padding: 0 10px;
        }

        .item {
            display: grid;

            grid-template-columns:
                40px minmax(140px, 1fr) minmax(140px, 1fr) 110px 160px;

            gap: 8px;

            align-items: center;

            min-width: 0;

            min-height: 42px;

            padding: 7px 6px;

            border-bottom: 1px solid #f0f1f3;
        }

        .item:last-child {
            border-bottom: 0;
        }

        .item-index {
            color: #9ca3af;

            font-size: 10px;
            font-weight: 600;

            text-align: center;
        }

        .item-label {
            display: none;
        }

        .item-sku {
            min-width: 0;

            color: #111827;

            font-size: 12px;
            font-weight: 700;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-batch {
            min-width: 0;

            color: #4b5563;

            font-size: 11px;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-quantity {
            color: #111827;

            font-size: 12px;
            font-weight: 600;

            white-space: nowrap;

            text-align: center;
        }

        .item-price {
            color: #111827;

            font-size: 12px;
            font-weight: 600;

            white-space: nowrap;

            text-align: right;
        }

        /* =====================================================
           TOTAL
        ===================================================== */

        .totals {
            display: flex;

            justify-content: flex-end;
            align-items: center;

            padding: 12px 16px;

            background: #fafafa;

            border-top: 1px solid #f0f1f3;
        }

        .total-box {
            min-width: 120px;
        }

        .total-label {
            margin-bottom: 3px;

            color: #6b7280;

            font-size: 9px;
            font-weight: 600;
        }

        .total-value {
            color: #111827;

            font-size: 14px;
            font-weight: 700;

            white-space: nowrap;
        }

        /* =====================================================
           COMMENT
        ===================================================== */

        .comment {
            margin-top: 12px;

            padding: 14px 16px;

            background: #fff;

            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .comment-title {
            margin-bottom: 6px;

            color: #111827;

            font-size: 12px;
            font-weight: 700;
        }

        .comment-text {
            color: #6b7280;

            font-size: 12px;
            line-height: 1.5;

            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        /* =====================================================
           EMPTY
        ===================================================== */

        .empty {
            padding: 30px 16px;

            color: #9ca3af;

            font-size: 11px;

            text-align: center;
        }

        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 700px) {

            .page {
                width: 100%;
                max-width: 100%;

                padding: 7px 5px 18px;
            }

            .top {
                gap: 6px;

                margin-bottom: 8px;
            }

            .back-btn {
                width: 33px;
                height: 33px;

                flex-basis: 33px;

                border-radius: 8px;
            }

            .back-btn svg {
                width: 14px;
                height: 14px;
            }

            .page-title {
                font-size: 16px;

                letter-spacing: -.25px;
            }

            .page-subtitle {
                display: none;
            }

            .top-actions {
                gap: 4px;
            }

            .btn {
                min-height: 33px;
                height: 33px;

                padding: 0 8px;

                gap: 4px;

                border-radius: 7px;

                font-size: 9px;
            }

            .btn svg {
                width: 11px;
                height: 11px;
            }

            .alerts {
                margin-bottom: 6px;
            }

            .alert {
                padding: 8px 9px;

                border-radius: 7px;

                font-size: 10px;

                margin-bottom: 5px;
            }

            .card {
                width: 100%;
                max-width: 100%;

                margin-bottom: 6px;

                border-radius: 9px;
            }

            .card-head {
                padding: 7px 8px;
            }

            .card-body {
                padding: 8px;
            }

            .section-title {
                font-size: 12px;
            }

            .section-description {
                display: none;
            }

            .items-count {
                min-width: 20px;
                height: 20px;

                border-radius: 6px;

                font-size: 9px;
            }

            /* DOCUMENT */

            .info-grid {
                grid-template-columns:
                    minmax(0, 1fr) minmax(0, 1fr);

                gap: 9px 10px;
            }

            .info-label {
                margin-bottom: 2px;

                font-size: 8px;
            }

            .info-value {
                font-size: 11px;
            }

            /* =================================================
               MOBILE TABLE HEADER
            ================================================= */

            .items-table-head {
                display: grid;

                grid-template-columns:
                    18px minmax(0, 1fr) minmax(0, .75fr) 48px 72px;

                gap: 4px;

                padding: 7px 7px;

                background: #fafafa;
            }

            .items-table-head div {
                font-size: 7px;

                overflow: hidden;
                text-overflow: ellipsis;

                text-transform: none;

                white-space: nowrap;
            }

            .items-table-head div:first-child {
                text-align: center;
            }

            .items-table-head div:nth-child(4) {
                text-align: center;
            }

            .items-table-head div:last-child {
                text-align: right;
            }

            /* =================================================
               MOBILE ITEMS — ONE ROW
            ================================================= */

            .items {
                padding: 4px;
            }

            .item {
                display: grid;

                grid-template-columns:
                    18px minmax(0, 1fr) minmax(0, .75fr) 48px 72px;

                gap: 4px;

                align-items: center;

                width: 100%;
                min-width: 0;

                min-height: 32px;

                padding: 5px 5px;

                margin-bottom: 3px;

                border: 1px solid #edf0f2;
                border-radius: 6px;

                background: #fafafa;
            }

            .item:last-child {
                margin-bottom: 0;
            }

            .item-index {
                font-size: 7px;
            }

            .item-sku {
                min-width: 0;

                font-size: 9px;

                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .item-batch {
                min-width: 0;

                font-size: 8px;

                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .item-quantity {
                font-size: 8px;

                white-space: nowrap;

                text-align: center;
            }

            .item-price {
                font-size: 8px;

                white-space: nowrap;

                text-align: right;
            }

            /* TOTAL */

            .totals {
                display: flex;

                justify-content: flex-end;

                padding: 9px;
            }

            .total-label {
                margin-bottom: 2px;

                font-size: 7px;
            }

            .total-value {
                font-size: 11px;
            }

            /* COMMENT */

            .comment {
                margin-top: 6px;

                padding: 9px;

                border-radius: 9px;
            }

            .comment-title {
                margin-bottom: 4px;

                font-size: 10px;
            }

            .comment-text {
                font-size: 10px;

                line-height: 1.4;
            }

            .empty {
                padding: 20px 8px;

                font-size: 9px;
            }
        }

        /* =====================================================
           VERY SMALL PHONES
        ===================================================== */

        @media (max-width: 360px) {

            .page {
                padding-left: 4px;
                padding-right: 4px;
            }

            .page-title {
                font-size: 15px;
            }

            .back-btn {
                width: 31px;
                height: 31px;

                flex-basis: 31px;
            }

            .btn {
                min-height: 31px;
                height: 31px;

                padding-left: 6px;
                padding-right: 6px;

                font-size: 8px;
            }

            .btn svg {
                width: 10px;
                height: 10px;
            }

            .info-grid {
                gap: 7px 8px;
            }

            .info-value {
                font-size: 10px;
            }

            .items-table-head {
                grid-template-columns:
                    16px minmax(0, 1fr) minmax(0, .65fr) 43px 63px;

                gap: 3px;

                padding: 6px;
            }

            .items-table-head div {
                font-size: 6.5px;
            }

            .item {
                grid-template-columns:
                    16px minmax(0, 1fr) minmax(0, .65fr) 43px 63px;

                gap: 3px;

                min-height: 30px;

                padding: 4px;
            }

            .item-index {
                font-size: 6px;
            }

            .item-sku {
                font-size: 8px;
            }

            .item-batch {
                font-size: 7px;
            }

            .item-quantity,
            .item-price {
                font-size: 7px;
            }

            .total-value {
                font-size: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        {{-- =====================================================
         HEADER
    ===================================================== --}}

        <div class="top">

            <a
                href="{{ route('admin.appropriations.index') }}"
                class="back-btn"
                aria-label="Назад">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">

                    <path d="M15 18l-6-6 6-6" />

                </svg>

            </a>


            <div class="title-block">

                <h1 class="page-title">
                    Оприходование #{{ $appropriation->id }}
                </h1>

                <div class="page-subtitle">
                    Просмотр документа
                </div>

            </div>


            <div class="top-actions">

                <a
                    href="{{ route('admin.appropriations.edit', $appropriation) }}"
                    class="btn btn-primary">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">

                        <path d="M12 20h9" />

                        <path
                            d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />

                    </svg>

                    <span>
                        Редактировать
                    </span>

                </a>


                <form
                    action="{{ route('admin.appropriations.destroy', $appropriation) }}"
                    method="POST"
                    style="margin:0;"
                    onsubmit="return confirm(
                    'Удалить оприходование #{{ $appropriation->id }}?\n\n' +
                    'Если товар уже использован, удаление будет запрещено.'
                );">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-danger">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">

                            <path d="M3 6h18" />
                            <path d="M8 6V4h8v2" />
                            <path d="M19 6l-1 15H6L5 6" />
                            <path d="M10 11v6" />
                            <path d="M14 11v6" />

                        </svg>

                        <span>
                            Удалить
                        </span>

                    </button>

                </form>

            </div>

        </div>


        {{-- =====================================================
         ALERTS
    ===================================================== --}}

        @if(session('success') || session('appropriation_error') || session('error'))

        <div class="alerts">

            @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

            @endif

            @if(session('appropriation_error'))

            <div class="alert alert-error">
                {{ session('appropriation_error') }}
            </div>

            @endif

            @if(session('error'))

            <div class="alert alert-error">
                {{ session('error') }}
            </div>

            @endif

        </div>

        @endif


        {{-- =====================================================
         DOCUMENT INFO
    ===================================================== --}}

        <div class="card">

            <div class="card-body">

                <div class="info-grid">

                    <div class="info-item">

                        <div class="info-label">
                            Номер
                        </div>

                        <div class="info-value">
                            #{{ $appropriation->id }}
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Дата
                        </div>

                        <div class="info-value">
                            {{ $appropriation->appropriation_date
                            ? $appropriation->appropriation_date->format('d.m.Y')
                            : '—'
                        }}
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Склад
                        </div>

                        <div class="info-value">
                            {{ $appropriation->warehouse->name ?? '—' }}
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Позиций
                        </div>

                        <div class="info-value">
                            {{ $appropriation->items->count() }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
         PRODUCTS
    ===================================================== --}}

        <div class="card">

            <div class="card-head">

                <div>

                    <div class="section-title">
                        Товары
                    </div>

                    <div class="section-description">
                        Список товаров в оприходовании
                    </div>

                </div>

                <div class="items-count">
                    {{ $appropriation->items->count() }}
                </div>

            </div>


            @if($appropriation->items->count())

            @php
            $grandQuantity = 0;
            @endphp


            {{-- =================================================
                 TABLE HEADER
            ================================================== --}}

            <div class="items-table-head">

                <div>
                    №
                </div>

                <div>
                    Артикул
                </div>

                <div>
                    Партия
                </div>

                <div>
                    Кол-во
                </div>

                <div>
                    Себестоимость, шт.
                </div>

            </div>


            {{-- =================================================
                 ITEMS
            ================================================== --}}

            <div class="items">

                @foreach($appropriation->items as $item)

                @php
                $quantity = (int) $item->quantity;

                $purchasePrice =
                (float) $item->purchase_price;

                $grandQuantity += $quantity;
                @endphp


                <div class="item">

                    {{-- NUMBER --}}

                    <div class="item-index">
                        {{ $loop->iteration }}
                    </div>


                    {{-- SKU --}}

                    <div class="item-sku">
                        {{ $item->variant->sku ?? '—' }}
                    </div>


                    {{-- BATCH --}}

                    <div class="item-batch">
                        {{ $item->batch->batch_code ?? '—' }}
                    </div>


                    {{-- QUANTITY --}}

                    <div class="item-quantity">
                        {{ number_format(
                                $quantity,
                                0,
                                ',',
                                ' '
                            ) }}
                        шт.
                    </div>


                    {{-- PURCHASE PRICE --}}

                    <div class="item-price">
                        {{ number_format(
                                $purchasePrice,
                                2,
                                ',',
                                ' '
                            ) }}
                        ₸
                    </div>

                </div>

                @endforeach

            </div>


            {{-- =================================================
                 TOTAL QUANTITY
            ================================================== --}}



            @else

            <div class="empty">
                В документе нет товаров.
            </div>

            @endif

        </div>


        {{-- =====================================================
         COMMENT
    ===================================================== --}}

        @if($appropriation->comment)

        <div class="comment">

            <div class="comment-title">
                Комментарий
            </div>

            <div class="comment-text">
                {{ $appropriation->comment }}
            </div>

        </div>

        @endif

    </div>

</body>

</html>