<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}">

    <title>Редактирование оприходования</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --bg: #f4f5f7;
            --card: #fff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
            --primary: #111827;
            --danger: #dc2626;
            --success: #15803d;
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

        /* HEADER */

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

        .top-save-btn {
            margin-left: auto;

            height: 40px;
            padding: 0 16px;

            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;

            border: 1px solid var(--primary);
            border-radius: 10px;

            background: var(--primary);
            color: #fff;

            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .top-save-btn:hover {
            background: #1f2937;
        }

        .top-save-btn svg {
            width: 14px;
            height: 14px;
        }

        /* MESSAGES */

        .message {
            margin-bottom: 12px;
            padding: 11px 13px;

            border-radius: 10px;

            font-size: 12px;
        }

        .message-success {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .message-error {
            border: 1px solid #fecaca;
            background: #fff;
            color: #991b1b;
        }

        .message ul {
            margin: 0;
            padding-left: 18px;
        }

        /* CARD */

        .card {
            margin-bottom: 12px;

            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);

            overflow: visible;
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

        /* INFO */

        .info-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr)
                minmax(170px, 220px);

            gap: 10px;
        }

        .field {
            min-width: 0;
            max-width: 100%;
        }

        .field-label {
            display: block;
            margin-bottom: 6px;

            color: #4b5563;

            font-size: 11px;
            font-weight: 600;
        }

        .field-input,
        .field-select,
        .field-textarea {
            width: 100%;
            min-width: 0;
            max-width: 100%;

            min-height: 42px;

            padding: 0 12px;

            border: 1px solid var(--border);
            border-radius: 10px;

            outline: none;

            background: #fff;
            color: var(--text);
        }

        .field-input:focus,
        .field-select:focus,
        .field-textarea:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(17, 24, 39, .06);
        }

        .field-input[readonly] {
            background: #f3f4f6;
            color: #6b7280;
        }

        .field-textarea {
            height: 76px;
            padding-top: 10px;
            padding-bottom: 10px;
            resize: vertical;
        }

        .comment-field {
            margin-top: 14px;
        }

        /* DATE */

        .field-input[type="date"] {
            display: block;

            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;

            height: 42px;

            padding: 0 8px !important;

            overflow: hidden;

            -webkit-appearance: none;
            appearance: none;

            text-align: center;
        }

        .field-input[type="date"]::-webkit-date-and-time-value {
            width: 100%;
            height: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 0 !important;
            margin: 0 !important;

            text-align: center;
        }

        /* ITEMS */

        .items-head {
            display: flex;
            align-items: center;
            gap: 10px;
        }

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

        #items {
            padding: 10px 12px 0;
        }

        .item {
            position: relative;

            display: grid;

            grid-template-columns:
                1fr .75fr 75px 100px;

            gap: 6px;

            align-items: end;

            margin-bottom: 5px;

            padding: 6px 6px 6px 30px;

            background: #fafafa;

            border: 1px solid var(--border);
            border-radius: 8px;
        }

        .item.used {
            background: #f8fafc;
        }

        .item-number {
            position: absolute;

            left: 8px;
            top: 50%;

            transform: translateY(-50%);

            color: #9ca3af;

            font-size: 9px;
            font-weight: 700;
        }

        .remove-btn {
            position: absolute;

            left: 4px;
            top: 50%;

            transform: translateY(-50%);

            width: 22px;
            height: 22px;

            padding: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 0;
            border-radius: 6px;

            background: transparent;
            color: #9ca3af;
        }

        .remove-btn:hover {
            background: #fee2e2;
            color: var(--danger);
        }

        .remove-btn svg {
            width: 12px;
            height: 12px;
        }

        .remove-btn.disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        /* FIELDS */

        .variant-field,
        .batch-field,
        .quantity-field,
        .price-field {
            min-width: 0;
            max-width: 100%;
        }

        .item-field-label {
            display: block;

            margin-bottom: 2px;

            color: #6b7280;

            font-size: 8px;
            line-height: 1;
            font-weight: 600;

            white-space: nowrap;
        }

        .item-input,
        .item-select {
            width: 100%;
            height: 30px;

            min-width: 0;
            max-width: 100%;

            padding: 0 6px;

            border: 1px solid var(--border);
            border-radius: 6px;

            outline: none;

            background: #fff;
            color: #111827;

            font-size: 11px;
        }

        .item-input:focus,
        .item-select:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 2px rgba(17, 24, 39, .05);
        }

        .item-input[readonly],
        .item-select:disabled {
            background: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
        }

        .quantity-field .item-input {
            text-align: center;
            padding: 0 3px;
        }

        .price-field .item-input {
            text-align: right;
            padding: 0 5px;
        }

        .price-input {
            background: #f3f4f6 !important;
            color: #4b5563 !important;
            cursor: not-allowed;
        }

        /* USED INFO */

        .used-info {
            margin-top: 3px;

            color: #6b7280;

            font-size: 8px;
            line-height: 1.1;
        }

        .used-info strong {
            color: #374151;
        }

        /* AUTOCOMPLETE */

        .autocomplete {
            position: relative;
            min-width: 0;
        }

        .autocomplete-input {
            width: 100%;
            height: 30px;

            min-width: 0;
            max-width: 100%;

            padding: 0 24px 0 7px;

            border: 1px solid var(--border);
            border-radius: 6px;

            outline: none;

            background: #fff;
            color: var(--text);

            font-size: 11px;
            font-weight: 500;
        }

        .autocomplete-input:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 2px rgba(17, 24, 39, .06);
        }

        .autocomplete-input[readonly] {
            background: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
        }

        .autocomplete-arrow {
            position: absolute;

            right: 7px;
            top: 50%;

            transform: translateY(-50%);

            width: 9px;
            height: 9px;

            color: #9ca3af;

            pointer-events: none;
        }

        .autocomplete-list {
            position: absolute;
            z-index: 100;

            top: calc(100% + 4px);
            left: 0;
            right: 0;

            max-height: 220px;

            overflow-y: auto;

            display: none;

            background: #fff;

            border: 1px solid var(--border);
            border-radius: 8px;

            box-shadow: 0 12px 30px rgba(0, 0, 0, .10);
        }

        .autocomplete-list.open {
            display: block;
        }

        .autocomplete-option {
            padding: 7px 9px;

            border-bottom: 1px solid #f3f4f6;

            cursor: pointer;

            font-size: 10px;
            line-height: 1.2;
        }

        .autocomplete-option:last-child {
            border-bottom: 0;
        }

        .autocomplete-option:hover {
            background: #f3f4f6;
        }

        .autocomplete-option strong {
            display: block;

            color: #111827;
            font-size: 11px;
        }

        .autocomplete-option span {
            display: block;

            margin-top: 1px;

            color: #9ca3af;
            font-size: 9px;
        }

        .autocomplete-empty {
            padding: 10px;

            color: #9ca3af;

            font-size: 10px;
            text-align: center;
        }

        .variant-hidden-select {
            display: none;
        }

        /* FOOTER */

        .items-footer {
            padding: 4px 12px 12px;
        }

        .add-item-btn {
            width: 100%;
            height: 36px;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;

            border: 1px dashed #cfd3d8;
            border-radius: 8px;

            background: #fff;
            color: #374151;

            font-size: 11px;
            font-weight: 600;
        }

        .add-item-btn:hover {
            border-color: #9ca3af;
            background: #f9fafb;
        }

        .add-item-btn svg {
            width: 13px;
            height: 13px;
        }

        .empty-state {
            padding: 14px 8px;

            text-align: center;

            color: #9ca3af;

            font-size: 10px;
        }

        /* BOTTOM */

        .bottom-actions {
            display: flex;
            align-items: center;
            gap: 9px;

            margin-top: 14px;
        }

        .btn {
            min-height: 42px;

            padding: 0 17px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 10px;

            text-decoration: none;

            font-size: 12px;
            font-weight: 600;
        }

        .btn-secondary {
            border: 1px solid var(--border);

            background: #fff;
            color: #374151;
        }

        .btn-danger {
            border: 1px solid #fecaca;

            background: #fff;
            color: #dc2626;
        }

        .btn-danger:hover {
            background: #fef2f2;
        }

        .btn-primary {
            border: 1px solid var(--primary);

            background: var(--primary);
            color: #fff;
        }

        /* MODAL */

        .modal-overlay {
            position: fixed;
            inset: 0;

            z-index: 999;

            display: none;

            align-items: center;
            justify-content: center;

            padding: 16px;

            background: rgba(17, 24, 39, .45);

            backdrop-filter: blur(3px);
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal {
            width: 100%;
            max-width: 420px;

            background: #fff;

            border-radius: 16px;

            box-shadow: 0 25px 60px rgba(0, 0, 0, .18);

            overflow: hidden;
        }

        .modal-head {
            padding: 15px 16px;

            border-bottom: 1px solid #f0f1f3;
        }

        .modal-title {
            margin: 0;

            font-size: 15px;
            font-weight: 700;
        }

        .modal-body {
            padding: 16px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;

            padding: 12px 16px;

            border-top: 1px solid #f0f1f3;
        }

        .modal-error {
            display: none;

            margin-top: 8px;

            color: #dc2626;

            font-size: 11px;
        }

        /* MOBILE */

        @media (max-width: 800px) {

            .item {
                grid-template-columns:
                    minmax(0, 1fr)
                    minmax(0, .75fr)
                    70px
                    90px;

                gap: 5px;
            }
        }

        @media (max-width: 600px) {

            body {
                font-size: 12px;
            }

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
            }

            .page-subtitle {
                display: none;
            }

            .top-save-btn {
                height: 33px;
                padding: 0 9px;
                gap: 4px;
                border-radius: 7px;
                font-size: 10px;
            }

            .top-save-btn svg {
                width: 11px;
                height: 11px;
            }

            .card {
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
                font-size: 9px;
                border-radius: 6px;
            }

            .info-grid {
                width: 100%;
                min-width: 0;

                grid-template-columns:
                    minmax(0, 1fr)
                    minmax(0, .55fr);

                gap: 5px;
            }

            .field-label {
                margin-bottom: 2px;
                font-size: 9px;
            }

            .field-input,
            .field-select {
                min-height: 33px;
                height: 33px;

                padding: 0 7px;

                border-radius: 6px;

                font-size: 12px !important;
            }

            .field-input[type="date"] {
                height: 33px;
                min-height: 33px;
                padding: 0 4px !important;
                font-size: 12px !important;
            }

            .field-input[type="date"]::-webkit-date-and-time-value {
                height: 33px;
            }

            .field-textarea {
                height: 48px;
                min-height: 48px;

                padding: 6px 7px;

                font-size: 12px !important;
            }

            .comment-field {
                margin-top: 6px;
            }

            #items {
                padding: 5px 5px 0;
            }

            .item {
                grid-template-columns:
                    minmax(0, 1.15fr)
                    minmax(65px, .75fr)
                    48px
                    65px;

                gap: 3px;

                padding:
                    5px 4px 5px 26px;

                margin-bottom: 4px;

                border-radius: 7px;
            }

            .item-field-label {
                margin-bottom: 1px;
                font-size: 7px;
            }

            .item-input,
            .item-select,
            .autocomplete-input {
                height: 28px;
                min-height: 28px;

                padding: 0 5px;

                border-radius: 5px;

                font-size: 10px !important;
            }

            .autocomplete-input {
                padding-right: 19px;
            }

            .autocomplete-arrow {
                right: 5px;
                width: 8px;
                height: 8px;
            }

            .autocomplete-list {
                max-height: 180px;
                border-radius: 7px;
            }

            .autocomplete-option {
                padding: 7px 8px;
            }

            .autocomplete-option strong {
                font-size: 11px;
            }

            .autocomplete-option span {
                font-size: 8px;
            }

            .remove-btn {
                left: 3px;
                width: 20px;
                height: 20px;
            }

            .remove-btn svg {
                width: 11px;
                height: 11px;
            }

            .used-info {
                font-size: 7px;
            }

            .items-footer {
                padding: 3px 5px 7px;
            }

            .add-item-btn {
                height: 32px;
                border-radius: 6px;
                font-size: 10px;
            }

            .bottom-actions {
                margin-top: 6px;
                gap: 5px;
            }

            .btn {
                min-height: 34px;
                height: 34px;

                padding: 0 10px;

                border-radius: 6px;

                font-size: 10px;
            }

            .modal-overlay {
                padding: 6px;
            }

            .modal {
                max-width: 420px;
                border-radius: 11px;
            }

            .modal-head {
                padding: 9px 10px;
            }

            .modal-title {
                font-size: 13px;
            }

            .modal-body {
                padding: 10px;
            }

            .modal-actions {
                padding: 7px 10px;
            }

            #newBatchCode {
                height: 42px;
                min-height: 42px;

                font-size: 16px !important;
            }
        }

        @media (max-width: 360px) {

            .page {
                padding-left: 4px;
                padding-right: 4px;
            }

            .top-save-btn {
                padding-left: 7px;
                padding-right: 7px;
                font-size: 9px;
            }

            .item {
                grid-template-columns:
                    minmax(0, 1.1fr)
                    minmax(55px, .7fr)
                    44px
                    60px;

                padding-left: 25px;
                gap: 2px;
            }

            .item-field-label {
                font-size: 6.5px;
            }

            .item-input,
            .item-select,
            .autocomplete-input {
                font-size: 9px !important;
                height: 27px;
                min-height: 27px;
            }
        }
    </style>
</head>

<body>

<div class="page">

    {{-- HEADER --}}

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

                <path d="M15 18l-6-6 6-6"/>
            </svg>

        </a>

        <div class="title-block">

            <h1 class="page-title">
                Редактирование оприходования #{{ $appropriation->id }}
            </h1>

            <div class="page-subtitle">
                {{ $appropriation->warehouse->name ?? 'Склад' }}
            </div>

        </div>

        <button
            type="submit"
            form="appropriationForm"
            class="top-save-btn">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2">

                <path d="M5 12l4 4L19 6"/>
            </svg>

            <span>
                Сохранить
            </span>

        </button>

    </div>


    {{-- SUCCESS --}}

    @if(session('success'))

        <div class="message message-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR --}}

    @if(session('appropriation_error'))

        <div class="message message-error">
            {{ session('appropriation_error') }}
        </div>

    @endif


    {{-- VALIDATION ERRORS --}}

    @if($errors->any())

        <div class="message message-error">

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- FORM --}}

    <form
        method="POST"
        action="{{ route('admin.appropriations.update', $appropriation) }}"
        id="appropriationForm">

        @csrf
        @method('PUT')


        {{-- INFO --}}

        <div class="card">

            <div class="card-body">

                <div class="info-grid">

                    <div class="field">

                        <label class="field-label">
                            Склад
                        </label>

                        <input
                            type="text"
                            class="field-input"
                            value="{{ $appropriation->warehouse->name ?? '—' }}"
                            readonly>

                    </div>

                    <div class="field">

                        <label class="field-label">
                            Дата
                        </label>

                        <input
                            type="date"
                            class="field-input"
                            value="{{ $appropriation->appropriation_date?->format('Y-m-d') }}"
                            readonly>

                    </div>

                </div>


                <div class="field comment-field">

                    <label class="field-label">
                        Комментарий
                    </label>

                    <textarea
                        name="comment"
                        class="field-textarea"
                        placeholder="Необязательно">{{ old('comment', $appropriation->comment) }}</textarea>

                </div>

            </div>

        </div>


        {{-- PRODUCTS --}}

        <div class="card">

            <div class="card-head">

                <div class="items-head">

                    <div>

                        <div class="section-title">
                            Товары
                        </div>

                        <div class="section-description">
                            Использованные позиции нельзя удалить или уменьшить ниже использованного количества
                        </div>

                    </div>

                </div>

                <div
                    class="items-count"
                    id="itemsCount">

                    {{ $appropriation->items->count() }}

                </div>

            </div>


            <div id="items">

                @foreach($appropriation->items as $item)

                    @php
                        $layer = $item->inventoryLayer;
                        $used = $layer ? $layer->fifoAllocations->sum('quantity') : 0;
                    @endphp

                    <div
                        class="item {{ $used > 0 ? 'used' : '' }}"
                        data-existing="1"
                        data-item-id="{{ $item->id }}"
                        data-used="{{ $used }}">

                        <div class="item-number">
                            #{{ $loop->iteration }}
                        </div>


                        {{-- DELETE --}}

                        <button
                            type="button"
                            class="remove-btn {{ $used > 0 ? 'disabled' : '' }}"
                            {{ $used > 0 ? 'disabled' : '' }}
                            onclick="deleteExistingItem(this)"
                            title="{{ $used > 0 ? 'Позиция уже использована' : 'Удалить товар' }}">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">

                                <path d="M3 6h18"/>
                                <path d="M8 6V4h8v2"/>
                                <path d="M19 6l-1 15H6L5 6"/>
                                <path d="M10 11v6"/>
                                <path d="M14 11v6"/>

                            </svg>

                        </button>


                        {{-- VARIANT --}}

                        <div class="variant-field">

                            <label class="item-field-label">
                                Артикул
                            </label>

                            <div class="autocomplete">

                                <input
                                    type="text"
                                    class="autocomplete-input"
                                    value="{{ $item->variant->sku ?? '' }}"
                                    readonly>

                                <svg
                                    class="autocomplete-arrow"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">

                                    <path d="M6 9l6 6 6-6"/>

                                </svg>

                            </div>

                            <input
                                type="hidden"
                                name="items[{{ $item->id }}][id]"
                                value="{{ $item->id }}">

                            <input
                                type="hidden"
                                name="items[{{ $item->id }}][variant_id]"
                                value="{{ $item->variant_id }}">

                        </div>


                        {{-- BATCH --}}

                        <div class="batch-field">

                            <label class="item-field-label">
                                Партия
                            </label>

                            <select
                                name="items[{{ $item->id }}][batch_id]"
                                class="item-select batch-select"
                                data-current-batch="{{ $item->batch_id }}"
                                data-variant-id="{{ $item->variant_id }}"
                                onchange="existingBatchChanged(this)"
                                {{ $used > 0 ? 'disabled' : '' }}
                                required>

                                @foreach($item->variant->batches ?? [] as $batch)

                                    <option
                                        value="{{ $batch->id }}"
                                        {{ $batch->id == $item->batch_id ? 'selected' : '' }}>

                                        {{ $batch->batch_code }}

                                    </option>

                                @endforeach

                                @if($used == 0)

                                    <option value="__create_new__">
                                        + Создать новую партию
                                    </option>

                                @endif

                            </select>

                            @if($used > 0)

                                <input
                                    type="hidden"
                                    name="items[{{ $item->id }}][batch_id]"
                                    value="{{ $item->batch_id }}">

                            @endif

                        </div>


                        {{-- QUANTITY --}}

                        <div class="quantity-field">

                            <label class="item-field-label">
                                Кол-во, шт
                            </label>

                            <input
                                type="number"
                                name="items[{{ $item->id }}][quantity]"
                                class="item-input quantity-input"
                                min="{{ max(1, $used) }}"
                                step="1"
                                value="{{ $item->quantity }}"
                                required>

                            @if($used > 0)

                                <div class="used-info">
                                    Использовано:
                                    <strong>{{ $used }}</strong>
                                </div>

                            @endif

                        </div>


                        {{-- PRICE --}}

                        <div class="price-field">

                            <label class="item-field-label">
                                Себестоимость
                            </label>

                            <input
                                type="number"
                                class="item-input price-input"
                                value="{{ $item->purchase_price }}"
                                readonly>

                        </div>

                    </div>

                @endforeach

            </div>


            <div
                class="empty-state"
                id="emptyState"
                style="{{ $appropriation->items->count() ? 'display:none;' : '' }}">

                Добавьте хотя бы один товар

            </div>


            <div class="items-footer">

                <button
                    type="button"
                    class="add-item-btn"
                    onclick="addItem()">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">

                        <path d="M12 5v14"/>
                        <path d="M5 12h14"/>

                    </svg>

                    Добавить товар

                </button>

            </div>

        </div>

    </form>


</div>


{{-- =========================================================
   NEW BATCH MODAL
========================================================= --}}

<div
    class="modal-overlay"
    id="batchModal">

    <div class="modal">

        <div class="modal-head">

            <h2 class="modal-title">
                Новая партия
            </h2>

        </div>

        <div class="modal-body">

            <div class="field">

                <label class="field-label">
                    Код партии
                </label>

                <input
                    type="text"
                    id="newBatchCode"
                    class="field-input"
                    placeholder="Введите код партии"
                    autocomplete="off"
                    inputmode="text">

                <div
                    id="batchModalError"
                    class="modal-error">
                </div>

            </div>

        </div>

        <div class="modal-actions">

            <button
                type="button"
                class="btn btn-secondary"
                onclick="closeBatchModal()">

                Отмена

            </button>

            <button
                type="button"
                class="btn btn-primary"
                onclick="createNewBatch()">

                Создать

            </button>

        </div>

    </div>

</div>


<script>

    /*
     * Контроллер передаёт $variants:
     *
     * [
     *   {
     *      id,
     *      sku,
     *      name,
     *      purchase_price,
     *      batches: [...]
     *   }
     * ]
     */

    const variants = @json($variants);

    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content') ||
        @json(csrf_token());


    /*
     * ВАЖНО:
     * Здесь именно тот route, который должен существовать
     * в web.php:
     *
     * admin.batches.create
     *
     * и он должен вести на создание партии.
     */

    const createBatchUrl =
        @json(route('admin.batches.create'));


    let itemIndex = {{ $appropriation->items->count() + 1000 }};

    let currentBatchSelect = null;

    let currentVariantId = null;

    let previousBatchValues = new WeakMap();


    /* =========================================================
       EXISTING BATCH
    ========================================================= */

    function existingBatchChanged(select) {

        const value = select.value;

        if (value !== '__create_new__') {

            previousBatchValues.set(
                select,
                value
            );

            return;
        }

        const variantId =
            select.dataset.variantId;

        if (!variantId) {

            select.value =
                previousBatchValues.get(select) || '';

            return;
        }

        currentBatchSelect = select;

        currentVariantId = variantId;

        openBatchModal();
    }


    /* =========================================================
       ADD ITEM
    ========================================================= */

    function addItem() {

        const index = itemIndex++;

        const item =
            document.createElement('div');

        item.className = 'item';

        item.dataset.existing = '0';

        item.innerHTML = `

            <div class="item-number">
                #1
            </div>

            <button
                type="button"
                class="remove-btn"
                onclick="removeNewItem(this)"
                title="Удалить товар"
                aria-label="Удалить товар">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">

                    <path d="M3 6h18"/>
                    <path d="M8 6V4h8v2"/>
                    <path d="M19 6l-1 15H6L5 6"/>
                    <path d="M10 11v6"/>
                    <path d="M14 11v6"/>

                </svg>

            </button>


            <div class="variant-field">

                <label class="item-field-label">
                    Артикул
                </label>

                <div class="autocomplete">

                    <input
                        type="text"
                        class="autocomplete-input variant-autocomplete"
                        placeholder="Артикул..."
                        autocomplete="off">

                    <svg
                        class="autocomplete-arrow"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">

                        <path d="M6 9l6 6 6-6"/>

                    </svg>

                    <div class="autocomplete-list"></div>

                </div>

                <select
                    name="items[${index}][variant_id]"
                    class="variant-hidden-select"
                    onchange="variantChanged(this)"
                    required>

                    <option value="">
                        Выберите артикул
                    </option>

                    ${variants.map(v => `

                        <option
                            value="${escapeHtml(String(v.id))}">

                            ${escapeHtml(
                                String(
                                    v.sku ??
                                    v.code ??
                                    ''
                                )
                            )}

                        </option>

                    `).join('')}

                </select>

            </div>


            <div class="batch-field">

                <label class="item-field-label">
                    Партия
                </label>

                <select
                    name="items[${index}][batch_id]"
                    class="item-select batch-select"
                    onchange="batchChanged(this)"
                    required>

                    <option value="">
                        Сначала артикул
                    </option>

                </select>

            </div>


            <div class="quantity-field">

                <label class="item-field-label">
                    Кол-во, шт
                </label>

                <input
                    type="number"
                    name="items[${index}][quantity]"
                    class="item-input quantity-input"
                    min="1"
                    step="1"
                    value="1"
                    required>

            </div>


            <div class="price-field">

                <label class="item-field-label">
                    Себестоимость
                </label>

                <input
                    type="number"
                    class="item-input price-input"
                    value="0"
                    readonly>

            </div>

        `;

        document
            .getElementById('items')
            .appendChild(item);


        const autocompleteInput =
            item.querySelector(
                '.variant-autocomplete'
            );

        const autocompleteList =
            item.querySelector(
                '.autocomplete-list'
            );

        const variantSelect =
            item.querySelector(
                '.variant-hidden-select'
            );


        setupAutocomplete(
            autocompleteInput,
            autocompleteList,
            variantSelect
        );


        updateItems();
    }


    /* =========================================================
       AUTOCOMPLETE
    ========================================================= */

    function setupAutocomplete(
        input,
        list,
        select
    ) {

        let activeIndex = -1;


        function getSku(variant) {

            return String(
                variant.sku ??
                variant.code ??
                variant.article ??
                ''
            );

        }


        function getName(variant) {

            return String(
                variant.name ??
                variant.product_name ??
                ''
            );

        }


        function render(query = '') {

            const search =
                query
                    .trim()
                    .toLowerCase();


            const filtered =
                variants.filter(
                    variant => {

                        const sku =
                            getSku(variant)
                                .toLowerCase();

                        const name =
                            getName(variant)
                                .toLowerCase();

                        return !search ||
                            sku.includes(search) ||
                            name.includes(search);

                    }
                );


            list.innerHTML = '';


            if (!filtered.length) {

                list.innerHTML = `

                    <div class="autocomplete-empty">
                        Ничего не найдено
                    </div>

                `;

                list.classList.add('open');

                return;
            }


            filtered
                .slice(0, 80)
                .forEach(
                    variant => {

                        const option =
                            document.createElement('div');

                        option.className =
                            'autocomplete-option';

                        option.dataset.variantId =
                            variant.id;


                        const sku =
                            getSku(variant);

                        const name =
                            getName(variant);


                        option.innerHTML = `

                            <strong>
                                ${escapeHtml(sku)}
                            </strong>

                            ${
                                name
                                    ? `
                                        <span>
                                            ${escapeHtml(name)}
                                        </span>
                                      `
                                    : ''
                            }

                        `;


                        option.addEventListener(
                            'mousedown',
                            function(e) {

                                e.preventDefault();

                                selectVariant(
                                    variant,
                                    input,
                                    select,
                                    list
                                );

                            }
                        );


                        list.appendChild(option);

                    }
                );


            activeIndex = -1;

            list.classList.add('open');
        }


        input.addEventListener(
            'focus',
            function() {

                render(input.value);

            }
        );


        input.addEventListener(
            'input',
            function() {

                select.value = '';

                select.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true
                        }
                    )
                );


                render(input.value);

            }
        );


        input.addEventListener(
            'keydown',
            function(e) {

                const options =
                    list.querySelectorAll(
                        '.autocomplete-option'
                    );


                if (
                    !list.classList.contains('open')
                ) {
                    return;
                }


                if (e.key === 'ArrowDown') {

                    e.preventDefault();

                    activeIndex =
                        Math.min(
                            activeIndex + 1,
                            options.length - 1
                        );

                    setActiveOption(
                        options,
                        activeIndex
                    );

                }


                if (e.key === 'ArrowUp') {

                    e.preventDefault();

                    activeIndex =
                        Math.max(
                            activeIndex - 1,
                            0
                        );

                    setActiveOption(
                        options,
                        activeIndex
                    );

                }


                if (e.key === 'Enter') {

                    if (
                        activeIndex >= 0 &&
                        options[activeIndex]
                    ) {

                        e.preventDefault();


                        const variant =
                            variants.find(
                                v =>
                                    String(v.id) ===
                                    String(
                                        options[
                                            activeIndex
                                        ]
                                            .dataset
                                            .variantId
                                    )
                            );


                        if (variant) {

                            selectVariant(
                                variant,
                                input,
                                select,
                                list
                            );

                        }

                    }

                }


                if (e.key === 'Escape') {

                    list.classList.remove('open');

                    activeIndex = -1;

                }

            }
        );


        input.addEventListener(
            'blur',
            function() {

                setTimeout(
                    () => {

                        list.classList.remove('open');

                    },
                    150
                );

            }
        );

    }


    function setActiveOption(
        options,
        index
    ) {

        options.forEach(
            option =>
                option.classList.remove('active')
        );


        if (options[index]) {

            options[index]
                .classList.add('active');

            options[index]
                .scrollIntoView({
                    block: 'nearest'
                });

        }

    }


    function selectVariant(
        variant,
        input,
        select,
        list
    ) {

        input.value =
            String(
                variant.sku ??
                variant.code ??
                variant.article ??
                ''
            );


        select.value =
            String(variant.id);


        list.classList.remove('open');


        select.dispatchEvent(
            new Event(
                'change',
                {
                    bubbles: true
                }
            )
        );

    }


    /* =========================================================
       VARIANT CHANGED
    ========================================================= */

    function variantChanged(select) {

        const item =
            select.closest('.item');

        if (!item) {
            return;
        }


        const batchSelect =
            item.querySelector('.batch-select');

        const priceInput =
            item.querySelector('.price-input');


        batchSelect.innerHTML = `

            <option value="">
                Выберите партию
            </option>

        `;


        priceInput.value = '0';


        const variantId =
            select.value;


        if (!variantId) {

            batchSelect.innerHTML = `

                <option value="">
                    Сначала артикул
                </option>

            `;

            return;
        }


        const variant =
            variants.find(
                v =>
                    String(v.id) ===
                    String(variantId)
            );


        if (!variant) {
            return;
        }


        if (
            variant.purchase_price !== undefined &&
            variant.purchase_price !== null
        ) {

            priceInput.value =
                variant.purchase_price;

        }


        const batches =
            variant.batches ??
            variant.existing_batches ??
            [];


        batches.forEach(
            batch => {

                const option =
                    document.createElement('option');

                option.value =
                    batch.id;

                option.textContent =
                    batch.batch_code ??
                    batch.code ??
                    `Партия #${batch.id}`;

                batchSelect.appendChild(
                    option
                );

            }
        );


        const createOption =
            document.createElement('option');

        createOption.value =
            '__create_new__';

        createOption.textContent =
            '+ Создать новую партию';


        batchSelect.appendChild(
            createOption
        );


        previousBatchValues.set(
            batchSelect,
            ''
        );

    }


    /* =========================================================
       BATCH CHANGED
    ========================================================= */

    function batchChanged(select) {

        const value =
            select.value;


        if (value === '__create_new__') {

            const item =
                select.closest('.item');


            const variantSelect =
                item.querySelector(
                    '.variant-hidden-select'
                );


            if (!variantSelect.value) {

                select.value =
                    previousBatchValues.get(
                        select
                    ) || '';

                return;
            }


            currentBatchSelect =
                select;

            currentVariantId =
                variantSelect.value;


            openBatchModal();

            return;
        }


        previousBatchValues.set(
            select,
            value
        );

    }


    /* =========================================================
       CREATE BATCH
    ========================================================= */

    function openBatchModal() {

        const modal =
            document.getElementById(
                'batchModal'
            );

        const input =
            document.getElementById(
                'newBatchCode'
            );


        document
            .getElementById(
                'batchModalError'
            )
            .style.display =
            'none';


        input.value = '';

        modal.classList.add('open');


        setTimeout(
            () => {

                input.focus();

            },
            50
        );

    }


    function closeBatchModal() {

        const modal =
            document.getElementById(
                'batchModal'
            );


        modal.classList.remove('open');


        if (currentBatchSelect) {

            currentBatchSelect.value =
                previousBatchValues.get(
                    currentBatchSelect
                ) || '';

        }


        currentBatchSelect = null;

        currentVariantId = null;

    }


    function showBatchModalError(message) {

        const error =
            document.getElementById(
                'batchModalError'
            );


        error.textContent =
            message;

        error.style.display =
            'block';

    }


    async function createNewBatch() {

        const input =
            document.getElementById(
                'newBatchCode'
            );


        const code =
            input.value.trim();


        if (!code) {

            showBatchModalError(
                'Введите код партии.'
            );

            input.focus();

            return;
        }


        if (!currentVariantId) {

            showBatchModalError(
                'Не выбран артикул.'
            );

            return;
        }


        try {

            const response =
                await fetch(
                    createBatchUrl,
                    {

                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                csrfToken,

                            'X-Requested-With':
                                'XMLHttpRequest'

                        },

                        body:
                            JSON.stringify({

                                variant_id:
                                    currentVariantId,

                                code:
                                    code

                            })

                    }
                );


            const data =
                await response.json();


            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Не удалось создать партию.'
                );

            }


            if (
                !currentBatchSelect ||
                !data.batch
            ) {

                throw new Error(
                    'Сервер не вернул данные партии.'
                );

            }


            const option =
                document.createElement('option');


            option.value =
                data.batch.id;


            option.textContent =
                data.batch.batch_code ??
                data.batch.code;


            const createOption =
                currentBatchSelect.querySelector(
                    'option[value="__create_new__"]'
                );


            if (createOption) {

                currentBatchSelect.insertBefore(
                    option,
                    createOption
                );

            } else {

                currentBatchSelect.appendChild(
                    option
                );

            }


            currentBatchSelect.value =
                data.batch.id;


            previousBatchValues.set(
                currentBatchSelect,
                data.batch.id
            );


            closeBatchModalAfterSuccess();

        } catch (error) {

            showBatchModalError(
                error.message ||
                'Ошибка при создании партии.'
            );

        }

    }


    function closeBatchModalAfterSuccess() {

        const modal =
            document.getElementById(
                'batchModal'
            );

        modal.classList.remove('open');


        currentBatchSelect = null;

        currentVariantId = null;

    }


    /* =========================================================
       DELETE NEW ITEM
    ========================================================= */

    function removeNewItem(button) {

        const item =
            button.closest('.item');

        if (!item) {
            return;
        }

        item.remove();

        updateItems();
    }


    /* =========================================================
       DELETE EXISTING ITEM
    ========================================================= */

    function deleteExistingItem(button) {

        const item =
            button.closest('.item');

        if (!item) {
            return;
        }


        const used =
            Number(
                item.dataset.used || 0
            );


        if (used > 0) {

            alert(
                `Нельзя удалить эту позицию. Уже использовано ${used} шт.`
            );

            return;
        }


        const itemId =
            item.dataset.itemId;


        if (!itemId) {
            return;
        }


        if (
            !confirm(
                'Удалить этот товар из оприходования?'
            )
        ) {

            return;
        }


        const form =
            document.createElement('form');


        form.method = 'POST';


        form.action =
            @json(route(
                'admin.appropriations.items.destroy',
                [
                    'appropriation' =>
                        $appropriation->id,
                    'appropriationItem' =>
                        '__ITEM_ID__'
                ]
            ))
                .replace(
                    '__ITEM_ID__',
                    itemId
                );


        form.innerHTML = `

            <input
                type="hidden"
                name="_token"
                value="${escapeHtml(csrfToken)}">

            <input
                type="hidden"
                name="_method"
                value="DELETE">

        `;


        document.body.appendChild(form);

        form.submit();

    }


    /* =========================================================
       UPDATE
    ========================================================= */

    function updateItems() {

        const items =
            document.querySelectorAll(
                '#items .item'
            );


        document.getElementById(
            'itemsCount'
        ).textContent =
            items.length;


        items.forEach(
            (
                item,
                index
            ) => {

                const number =
                    item.querySelector(
                        '.item-number'
                    );


                if (number) {

                    number.textContent =
                        `#${index + 1}`;

                }

            }
        );


        document.getElementById(
            'emptyState'
        ).style.display =
            items.length
                ? 'none'
                : 'block';

    }


    /* =========================================================
       ESCAPE
    ========================================================= */

    function escapeHtml(value) {

        return String(value)

            .replace(/&/g, '&amp;')

            .replace(/</g, '&lt;')

            .replace(/>/g, '&gt;')

            .replace(/"/g, '&quot;')

            .replace(/'/g, '&#039;');

    }


    /* =========================================================
       FORM VALIDATION
    ========================================================= */

    document
        .getElementById('appropriationForm')
        .addEventListener(
            'submit',
            function(e) {

                const items =
                    document.querySelectorAll(
                        '#items .item'
                    );


                if (!items.length) {

                    e.preventDefault();

                    alert(
                        'Добавьте хотя бы один товар.'
                    );

                    return;
                }


                let valid = true;


                items.forEach(
                    item => {

                        const variant =
                            item.querySelector(
                                '.variant-hidden-select'
                            );


                        const batch =
                            item.querySelector(
                                '.batch-select'
                            );


                        const quantity =
                            item.querySelector(
                                '.quantity-input'
                            );


                        if (
                            item.dataset.existing === '1' &&
                            variant
                        ) {

                            /*
                             * У существующих позиций
                             * variant_id находится в hidden input,
                             * поэтому отдельного select нет.
                             */

                        }


                        if (
                            batch &&
                            batch.value ===
                            '__create_new__'
                        ) {

                            valid = false;

                        }


                        if (
                            quantity &&
                            Number(quantity.value) < 1
                        ) {

                            valid = false;

                        }


                        const used =
                            Number(
                                item.dataset.used || 0
                            );


                        if (
                            quantity &&
                            Number(quantity.value) < used
                        ) {

                            valid = false;

                        }

                    }
                );


                if (!valid) {

                    e.preventDefault();

                    alert(
                        'Проверьте партии и количество товаров.'
                    );

                }

            }
        );


    /* =========================================================
       MODAL
    ========================================================= */

    document
        .getElementById('batchModal')
        .addEventListener(
            'click',
            function(e) {

                if (e.target === this) {

                    closeBatchModal();

                }

            }
        );


    document.addEventListener(
        'keydown',
        function(e) {

            const modal =
                document.getElementById(
                    'batchModal'
                );


            if (
                e.key === 'Escape' &&
                modal.classList.contains('open')
            ) {

                closeBatchModal();

            }


            if (
                e.key === 'Enter' &&
                modal.classList.contains('open') &&
                document.activeElement.id ===
                'newBatchCode'
            ) {

                e.preventDefault();

                createNewBatch();

            }

        }
    );


    /* =========================================================
       INITIAL
    ========================================================= */

    updateItems();

</script>

</body>
</html>