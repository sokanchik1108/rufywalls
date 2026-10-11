<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Редактирование приёмки #{{ $receipt->id }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --bg: #f5f6f8;
            --card: #ffffff;
            --text: #111827;
            --muted: #8b95a3;
            --border: #e5e7eb;
            --soft: #f8fafc;
            --primary: #111827;

            --success-bg: #ecfdf3;
            --success-border: #bbf7d0;
            --success-text: #166534;

            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --error-text: #991b1b;

            --warning-bg: #fffaf0;
            --warning-border: #fde68a;
            --warning-text: #92400e;

            --danger: #dc2626;
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
                Arial,
                sans-serif;
            font-size: 14px;
            line-height: 1.4;
        }

        button,
        input,
        textarea,
        select {
            font-family: inherit;
        }

        button,
        a,
        select {
            -webkit-tap-highlight-color: transparent;
        }

        .page {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 18px 22px 36px;
        }

        /* =====================================================
           HEADER
        ===================================================== */

        .top {
            position: relative;

            width: 100%;
            min-height: 42px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 16px;
        }

        .top-left,
        .top-right {
            width: 180px;

            display: flex;
            align-items: center;
        }

        .top-right {
            justify-content: flex-end;
        }

        .top-center {
            position: absolute;

            left: 50%;
            top: 50%;

            transform: translate(-50%, -50%);

            text-align: center;
            white-space: nowrap;
        }

        h1 {
            margin: 0;

            color: var(--text);

            font-size: 22px;
            line-height: 1.15;

            font-weight: 750;
            letter-spacing: -.025em;
        }

        .receipt-number {
            color: #9ca3af;
            font-weight: 500;
        }

        /* =====================================================
           BUTTONS
        ===================================================== */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;

            min-height: 36px;
            padding: 0 12px;

            border: 1px solid transparent;
            border-radius: 8px;

            cursor: pointer;
            text-decoration: none;

            font-size: 12px;
            font-weight: 650;

            transition:
                background .15s ease,
                border-color .15s ease,
                color .15s ease,
                transform .15s ease;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-light {
            background: #fff;
            color: #374151;
            border-color: var(--border);
        }

        .btn-light:hover {
            background: #f9fafb;
            border-color: #d3d8df;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: #1f2937;
        }

        .back-button {
            flex: 0 0 auto;
        }

        .back-icon {
            font-size: 15px;
            line-height: 1;
        }

        .save-top-button {
            min-width: 150px;
        }

        /* =====================================================
           ALERTS
        ===================================================== */

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 9px;

            margin-bottom: 12px;
            padding: 10px 12px;

            border-radius: 9px;

            font-size: 12px;
        }

        .alert-icon {
            width: 19px;
            height: 19px;

            display: flex;
            align-items: center;
            justify-content: center;

            flex: 0 0 19px;

            border-radius: 50%;

            font-size: 10px;
            font-weight: 800;
        }

        .alert-content {
            min-width: 0;
        }

        .alert-content div+div {
            margin-top: 3px;
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid var(--success-border);
        }

        .alert-success .alert-icon {
            background: #bbf7d0;
        }

        .alert-error {
            background: var(--error-bg);
            color: var(--error-text);
            border: 1px solid var(--error-border);
        }

        .alert-error .alert-icon {
            background: #fecaca;
        }

        .alert-warning {
            background: var(--warning-bg);
            color: var(--warning-text);
            border: 1px solid var(--warning-border);
        }

        .alert-warning .alert-icon {
            background: #fde68a;
        }

        /* =====================================================
           CARD
        ===================================================== */

        .card {
            background: var(--card);

            border: 1px solid var(--border);
            border-radius: 12px;

            margin-bottom: 12px;

            box-shadow:
                0 1px 2px rgba(15, 23, 42, .02);
        }

        .card-inner {
            padding: 14px;
        }

        /* =====================================================
           RECEIPT INFO
        ===================================================== */

        .receipt-info-comment {
            display: grid;

            grid-template-columns:
                150px 170px minmax(180px, .9fr) minmax(220px, 1.4fr);

            gap: 8px;

            align-items: stretch;
        }

        .info-item {
            min-height: 68px;

            display: flex;
            flex-direction: column;
            justify-content: center;

            padding: 10px 12px;

            background: var(--soft);

            border: 1px solid #edf0f3;
            border-radius: 9px;
        }

        .info-label,
        .info-item>label {
            display: block;

            margin-bottom: 4px;

            color: #9ca3af;

            font-size: 9px;
            line-height: 1.1;
            font-weight: 700;

            text-transform: uppercase;
            letter-spacing: .045em;
        }

        .info-value {
            color: var(--text);

            font-size: 12px;
            font-weight: 650;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .comment-block {
            min-width: 0;
        }

        .comment-block .field {
            height: 100%;
        }

        .comment-block textarea {
            width: 100%;

            height: 68px;
            min-height: 68px;

            padding: 9px 10px;

            border: 1px solid #dfe3e8;
            border-radius: 9px;

            background: #fff;
            color: var(--text);

            outline: none;

            resize: none;

            font-size: 12px;

            transition:
                border-color .15s ease,
                box-shadow .15s ease;
        }

        .comment-block textarea:hover {
            border-color: #cbd1d8;
        }

        .comment-block textarea:focus {
            border-color: #9ca3af;

            box-shadow:
                0 0 0 3px rgba(17, 24, 39, .06);
        }

        /* =====================================================
           FINANCE
           СУММА ПРИЁМКИ / ОПЛАЧЕНО / ДОЛГ
        ===================================================== */

        .finance-block {
            margin-top: 9px;

            display: grid;

            grid-template-columns:
                minmax(180px, 1fr) minmax(150px, .8fr) minmax(150px, .8fr);

            gap: 7px;
        }

        .finance-item {
            min-height: 50px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 10px;

            padding: 8px 11px;

            background: #f8fafc;

            border: 1px solid #edf0f3;
            border-radius: 8px;
        }

        .finance-label {
            color: #9ca3af;

            font-size: 8px;
            line-height: 1.1;
            font-weight: 700;

            text-transform: uppercase;
            letter-spacing: .045em;

            white-space: nowrap;
        }

        .finance-input {
            width: 125px;

            min-height: 31px;
            height: 31px;

            padding: 5px 8px;

            border: 1px solid #dfe3e8;
            border-radius: 7px;

            background: #fff;
            color: #111827;

            outline: none;

            font-size: 12px;
            font-weight: 650;

            text-align: right;
        }

        .finance-input:focus {
            border-color: #9ca3af;

            box-shadow:
                0 0 0 3px rgba(17, 24, 39, .06);
        }

        .finance-value {
            color: #111827;

            font-size: 12px;
            font-weight: 750;

            white-space: nowrap;
        }

        .finance-value.debt {
            color: #dc2626;
        }

        /* =====================================================
           WARNING
        ===================================================== */

        .warning {
            margin-top: 9px;
            margin-bottom: 0;
        }

        /* =====================================================
           SECTION HEADER
        ===================================================== */

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;

            padding: 13px 14px;

            border-bottom: 1px solid #eef0f2;
        }

        .section-title {
            margin: 0;

            font-size: 15px;
            line-height: 1.2;

            font-weight: 720;
            letter-spacing: -.01em;
        }

        .section-subtitle {
            margin-top: 3px;

            color: #9ca3af;

            font-size: 10px;
        }

        /* =====================================================
           PRODUCTS
        ===================================================== */

        .products-body {
            padding: 10px;
        }

        .item {
            position: relative;

            display: grid;

            grid-template-columns:
                minmax(180px, 1.35fr) minmax(130px, 1fr) 72px 92px;

            gap: 6px;

            padding: 7px 46px 7px 8px;

            background: #fff;

            border: 1px solid #e6e9ed;
            border-radius: 8px;

            margin-bottom: 5px;

            align-items: start;

            transition:
                border-color .15s ease,
                box-shadow .15s ease;
        }

        .item:last-child {
            margin-bottom: 0;
        }

        .item:hover {
            border-color: #d8dde3;

            box-shadow:
                0 2px 7px rgba(15, 23, 42, .035);
        }

        .product-main {
            display: contents;
        }

        .product-values {
            display: contents;
        }

        .field {
            display: flex;
            flex-direction: column;

            gap: 3px;

            min-width: 0;
        }

        label {
            color: #4b5563;

            font-size: 8px;
            line-height: 1;
            font-weight: 650;
        }

        input,
        textarea,
        select {
            width: 100%;

            min-height: 32px;

            padding: 5px 8px;

            border: 1px solid #dfe3e8;
            border-radius: 7px;

            background: #fff;
            color: #111827;

            outline: none;

            font-size: 12px;

            transition:
                border-color .15s ease,
                box-shadow .15s ease,
                background .15s ease;
        }

        input:hover,
        textarea:hover,
        select:hover {
            border-color: #cbd1d8;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #9ca3af;

            box-shadow:
                0 0 0 3px rgba(17, 24, 39, .06);
        }

        input::placeholder,
        textarea::placeholder {
            color: #b0b7c0;
        }

        .readonly {
            width: 100%;

            min-height: 32px;

            display: flex;
            align-items: center;

            padding: 5px 8px;

            border: 1px solid #e8ebef;
            border-radius: 7px;

            background: #f8fafc;
            color: #1f2937;

            font-size: 12px;
            font-weight: 600;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .existing-identity {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr) minmax(0, 1fr);

            align-items: center;

            gap: 10px;

            min-height: 32px;

            padding: 6px 8px;

            border: 1px solid #e8ebef;
            border-radius: 7px;

            background: #f8fafc;
        }

        .existing-identity .identity-sku {
            padding-right: 10px;

            border-right: 1px solid #e1e5e9;
        }

        .identity-sku {
            min-width: 0;

            color: #111827;

            font-size: 12px;
            line-height: 1.2;

            font-weight: 750;

            overflow-wrap: anywhere;
        }

        .identity-batch {
            min-width: 0;

            color: #7b8490;

            font-size: 11px;
            line-height: 1.2;

            overflow-wrap: anywhere;
        }

        .hint {
            color: #9ca3af;

            font-size: 8px;
            line-height: 1.3;
        }

        .hint strong {
            color: #6b7280;
            font-weight: 650;
        }

        /* =====================================================
           DELETE
        ===================================================== */

        .delete-item-btn {
            position: absolute;

            top: 9px;
            right: 9px;

            z-index: 20;

            width: 30px;
            height: 30px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 0;

            border: 1px solid #eceff2;
            border-radius: 7px;

            background: #fff;
            color: #9ca3af;

            cursor: pointer;

            transition:
                color .15s ease,
                background .15s ease,
                border-color .15s ease,
                transform .15s ease;
        }

        .delete-item-btn:hover {
            color: #dc2626;

            background: #fef2f2;
            border-color: #fecaca;
        }

        .delete-item-btn:active {
            transform: scale(.95);
        }

        .delete-item-btn:disabled {
            cursor: not-allowed;
            opacity: .45;
        }

        /* =====================================================
           NEW ITEM
        ===================================================== */

        .new-item {
            position: relative;

            padding-top: 51px;

            border-style: dashed;
            border-color: #d2d8df;

            background: #fcfcfd;
        }

        .new-item-header {
            position: absolute;

            top: 10px;
            left: 11px;
            right: 51px;

            height: 30px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            pointer-events: none;
        }

        .new-item-title {
            display: inline-flex;
            align-items: center;

            gap: 6px;

            color: #6b7280;

            font-size: 9px;
            font-weight: 750;

            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .new-item-title::before {
            content: "+";

            width: 20px;
            height: 20px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 5px;

            background: #f0f2f5;
            color: #4b5563;

            font-size: 14px;
            font-weight: 500;
        }

        .new-item-header .delete-item-btn {
            position: absolute;

            top: -1px;
            right: -40px;

            pointer-events: auto;
        }

        .new-item {
            grid-template-columns:
                minmax(110px, .75fr) minmax(100px, .65fr) minmax(150px, 1.45fr) minmax(105px, .9fr);
        }

        .new-item .product-main {
            display: contents;
        }

        /* =====================================================
           AUTOCOMPLETE
        ===================================================== */

        .autocomplete {
            position: relative;
            width: 100%;
        }

        .autocomplete-input-wrap {
            position: relative;
        }

        .autocomplete-input {
            padding-right: 35px;
        }

        .autocomplete-icon {
            position: absolute;

            top: 50%;
            right: 10px;

            width: 15px;
            height: 15px;

            transform: translateY(-50%);

            color: #87919d;

            pointer-events: none;
        }

        .autocomplete-icon::before {
            content: "";

            position: absolute;

            top: 1px;
            left: 1px;

            width: 8px;
            height: 8px;

            border: 1.5px solid currentColor;
            border-radius: 50%;
        }

        .autocomplete-icon::after {
            content: "";

            position: absolute;

            top: 10px;
            left: 9px;

            width: 5px;
            height: 1.5px;

            background: currentColor;

            border-radius: 2px;

            transform: rotate(45deg);
        }

        .autocomplete-dropdown {
            position: absolute;

            top: calc(100% + 4px);
            left: 0;
            right: 0;

            z-index: 1000;

            display: none;

            max-height: 260px;
            overflow-y: auto;

            background: #fff;

            border: 1px solid #dfe3e8;
            border-radius: 9px;

            box-shadow:
                0 12px 30px rgba(15, 23, 42, .12);
        }

        .autocomplete-dropdown.show {
            display: block;
        }

        .autocomplete-option {
            width: 100%;

            display: flex;
            flex-direction: column;
            align-items: flex-start;

            gap: 2px;

            padding: 9px 10px;

            border: 0;
            border-bottom: 1px solid #f0f1f3;

            background: #fff;
            color: #111827;

            text-align: left;

            cursor: pointer;
        }

        .autocomplete-option:last-child {
            border-bottom: 0;
        }

        .autocomplete-option:hover {
            background: #f8fafc;
        }

        .autocomplete-option-sku {
            color: #111827;

            font-size: 11px;
            font-weight: 750;
        }

        .autocomplete-option-name {
            max-width: 100%;

            color: #8b95a3;

            font-size: 9px;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .autocomplete-empty {
            padding: 12px 10px;

            color: #9ca3af;

            font-size: 10px;

            text-align: center;
        }

        .selected-variant {
            margin-top: 2px;

            color: #8b95a3;

            font-size: 8px;
            line-height: 1.1;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* =====================================================
           ADD ITEM
        ===================================================== */

        .add-item-btn {
            width: auto;

            min-height: 36px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 6px;

            margin-top: 9px;

            padding: 0 13px;

            border: 1px dashed #cfd5dc;
            border-radius: 8px;

            background: #fafbfc;
            color: #4b5563;

            font-size: 11px;
            font-weight: 650;

            cursor: pointer;

            transition:
                background .15s ease,
                border-color .15s ease,
                color .15s ease;
        }

        .add-item-btn:hover {
            background: #f5f6f8;
            border-color: #9ca3af;
            color: #111827;
        }

        .add-item-icon {
            width: 19px;
            height: 19px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 5px;

            background: #111827;
            color: #fff;

            font-size: 14px;
            font-weight: 400;

            line-height: 1;
        }

        /* =====================================================
           TABLET
        ===================================================== */

        @media (max-width: 1100px) {

            .receipt-info-comment {
                grid-template-columns:
                    1fr 1fr;
            }

            /*
             * Теперь комментарий НЕ растягиваем на всю строку.
             * На планшете и телефоне поставщик + комментарий
             * находятся рядом.
             */
            .comment-block {
                grid-column: auto;
            }

            .finance-block {
                grid-template-columns:
                    1fr 1fr 1fr;
            }

        }

        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 640px) {

            body {
                font-size: 12px;
            }

            .page {
                padding: 7px 7px 18px;
            }

            .top {
                min-height: 36px;
                margin-bottom: 9px;
            }

            .top-left,
            .top-right {
                width: auto;
            }

            h1 {
                font-size: 16px;
            }

            .back-button {
                min-height: 31px;
                padding: 0 8px;
                font-size: 10px;
            }

            .save-top-button {
                min-width: 0;
                min-height: 31px;
                padding: 0 8px;
                font-size: 10px;
            }

            .card {
                border-radius: 9px;
                margin-bottom: 8px;
            }

            .card-inner {
                padding: 8px;
            }

            /* =================================================
               INFO
               СКЛАД + ДАТА
               ПОСТАВЩИК + КОММЕНТАРИЙ
            ================================================= */

            .receipt-info-comment {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr) minmax(0, 1fr);

                gap: 5px;
            }

            .receipt-info-comment>.info-item {
                min-width: 0;

                min-height: 46px;

                padding: 6px 7px;

                border-radius: 7px;
            }

            /*
             * Поставщик — левая ячейка второй строки
             */
            .receipt-info-comment>.info-item:nth-child(3) {
                grid-column: 1;
            }

            /*
             * Комментарий — правая ячейка второй строки
             */
            .comment-block {
                grid-column: 2;

                min-width: 0;
            }

            .comment-block .field {
                height: 100%;
            }

            .info-label,
            .info-item>label {
                margin-bottom: 2px;

                font-size: 6.5px;
            }

            .info-value {
                font-size: 9px;
            }

            .info-item select {
                min-height: 27px;
                height: 27px;

                padding: 2px 4px;

                font-size: 9px;
            }

            /*
             * Комментарий остаётся textarea,
             * но визуально занимает одну компактную строку.
             */
            .comment-block textarea {
                width: 100%;

                height: 46px;
                min-height: 46px;

                padding: 6px 7px;

                border-radius: 7px;

                font-size: 9px;

                line-height: 1.2;
            }

            /* =================================================
               FINANCE
               СУММА ПРИЁМКИ | ОПЛАЧЕНО | ДОЛГ
               СТРОГО ОДНА СТРОКА
            ================================================= */

            .finance-block {
                display: grid;

                grid-template-columns:
                    minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr);

                gap: 4px;

                margin-top: 5px;
            }

            .finance-item {
                min-width: 0;
                min-height: 42px;

                display: flex;
                flex-direction: column;
                align-items: stretch;
                justify-content: center;

                gap: 2px;

                padding: 5px 6px;

                border-radius: 7px;
            }

            .finance-label {
                margin: 0;

                font-size: 5.8px;

                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .finance-input {
                width: 100%;

                min-height: 25px;
                height: 25px;

                padding: 2px 4px;

                border-radius: 5px;

                font-size: 9px;

                text-align: left;
            }

            .finance-value {
                width: 100%;

                font-size: 9px;

                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .warning {
                margin-top: 5px;
            }

            .alert {
                margin-bottom: 7px;
                padding: 7px 8px;
                border-radius: 7px;
                font-size: 8.5px;
            }

            .alert-icon {
                width: 15px;
                height: 15px;
                flex-basis: 15px;
                font-size: 8px;
            }

            /* =================================================
               PRODUCTS
            ================================================= */

            .section-head {
                padding: 9px 9px;
            }

            .section-title {
                font-size: 12px;
            }

            .section-subtitle {
                margin-top: 2px;
                font-size: 7.5px;
            }

            .products-body {
                padding: 6px;
            }

            .item {
                grid-template-columns:
                    minmax(0, 1.25fr) minmax(0, 1fr) 55px 69px;

                gap: 4px;

                padding: 47px 6px 6px;

                margin-bottom: 4px;

                border-radius: 7px;
            }

            .new-item {
                padding-top: 47px;
            }

            .field {
                gap: 2px;
            }

            label {
                font-size: 6px;
            }

            input,
            textarea,
            select {
                min-height: 28px;
                height: 28px;

                padding: 3px 5px;

                border-radius: 6px;

                font-size: 10px;
            }

            .readonly {
                min-height: 28px;
                height: 28px;

                padding: 3px 5px;

                border-radius: 6px;

                font-size: 9px;
            }

            .identity-sku {
                font-size: 10px;
            }

            .identity-batch {
                font-size: 8px;
            }

            .hint {
                font-size: 6.3px;
                line-height: 1.15;
            }

            .delete-item-btn {
                top: 7px;
                right: 7px;

                width: 26px;
                height: 26px;

                border-radius: 6px;
            }

            .delete-item-btn svg {
                width: 12px;
                height: 12px;
            }

            .new-item-header {
                top: 8px;
                left: 8px;
                right: 46px;

                height: 28px;
            }

            .new-item-title {
                font-size: 7.5px;
            }

            .new-item-title::before {
                width: 18px;
                height: 18px;
                font-size: 12px;
            }

            .new-item-header .delete-item-btn {
                top: -1px;
                right: -37px;
            }

            .autocomplete-dropdown {
                max-height: 210px;
            }

            .autocomplete-option {
                padding: 7px 8px;
            }

            .autocomplete-option-sku {
                font-size: 10px;
            }

            .autocomplete-option-name {
                font-size: 8px;
            }

            .selected-variant {
                font-size: 7px;
            }

            .add-item-btn {
                min-height: 33px;

                width: 100%;

                padding: 0 9px;

                margin-top: 7px;

                font-size: 9.5px;
            }

            .add-item-icon {
                width: 17px;
                height: 17px;
                font-size: 12px;
            }

        }

        /* =====================================================
           VERY SMALL MOBILE
        ===================================================== */

        @media (max-width: 380px) {

            .page {
                padding-left: 5px;
                padding-right: 5px;
            }

            h1 {
                font-size: 15px;
            }

            .back-button {
                padding: 0 7px;
                font-size: 9px;
            }

            .save-top-button {
                padding: 0 7px;
                font-size: 9px;
            }

            .card-inner {
                padding: 7px;
            }

            .receipt-info-comment {
                gap: 4px;
            }

            .receipt-info-comment>.info-item {
                padding: 5px;
            }

            .info-label,
            .info-item>label {
                font-size: 6px;
            }

            .info-value {
                font-size: 8.5px;
            }

            .info-item select {
                font-size: 8px;
            }

            .comment-block textarea {
                padding: 5px 6px;
                font-size: 8.5px;
            }

            .finance-block {
                gap: 3px;
            }

            .finance-item {
                padding: 4px 5px;
                min-height: 40px;
            }

            .finance-label {
                font-size: 5px;
            }

            .finance-input {
                font-size: 8.5px;
                min-height: 24px;
                height: 24px;
            }

            .finance-value {
                font-size: 8.5px;
            }

            .item {
                grid-template-columns:
                    minmax(0, 1.15fr) minmax(0, .9fr) 51px 65px;

                gap: 3px;

                padding-left: 5px;
                padding-right: 5px;
            }

            input,
            textarea,
            select {
                font-size: 9px;
            }

            .new-item {
                grid-template-columns:
                    minmax(0, .8fr) minmax(0, .7fr) minmax(0, 1.45fr) minmax(0, .9fr);
            }

        }
    </style>

</head>

<body>

    @php

    $isOwnerOrAnalyst =
    auth()->user()->is_owner ||
    auth()->user()->can_view_analytics;

    $receiptTotal = $receipt->items->sum(function ($item) {
    return (float) $item->quantity *
    (float) $item->purchase_price;
    });

    $paidAmount = $receipt->payments->sum('amount');

    $debtAmount = max(
    0,
    $receiptTotal - (float) $paidAmount
    );

    @endphp


    <div class="page">


        {{-- =====================================================
         HEADER
    ====================================================== --}}

        <div class="top">

            <div class="top-left">

                <a
                    href="{{ route('admin.receipts.index') }}"
                    class="btn btn-light back-button">

                    <span class="back-icon">←</span>

                    Назад

                </a>

            </div>


            <div class="top-center">

                <h1>

                    Приёмка

                    <span class="receipt-number">
                        #{{ $receipt->id }}
                    </span>

                </h1>

            </div>


            <div class="top-right">

                <button
                    type="submit"
                    form="receipt-edit-form"
                    class="btn btn-primary save-top-button"
                    id="save-button">

                    Сохранить

                </button>

            </div>

        </div>


        {{-- =====================================================
         ALERTS
    ====================================================== --}}

        @if(session('success'))

        <div class="alert alert-success">

            <div class="alert-icon">
                ✓
            </div>

            <div class="alert-content">
                {{ session('success') }}
            </div>

        </div>

        @endif


        @if(session('receipt_error'))

        <div class="alert alert-error">

            <div class="alert-icon">
                !
            </div>

            <div class="alert-content">
                {{ session('receipt_error') }}
            </div>

        </div>

        @endif


        @if($errors->any())

        <div class="alert alert-error">

            <div class="alert-icon">
                !
            </div>

            <div class="alert-content">

                @foreach($errors->all() as $error)

                <div>
                    {{ $error }}
                </div>

                @endforeach

            </div>

        </div>

        @endif


        {{-- =====================================================
         FORM
    ====================================================== --}}

        <form
            id="receipt-edit-form"
            method="POST"
            action="{{ route('admin.receipts.update', $receipt) }}">

            @csrf

            @method('PUT')

            {{-- Для администраторов без видимого поля оплаты отправляем текущую оплаченную сумму. --}}
            @unless($isOwnerOrAnalyst)
            <input
                type="hidden"
                name="paid_amount"
                value="{{ old('paid_amount', $paidAmount) }}">
            @endunless


            {{-- =================================================
             RECEIPT INFO
        ================================================== --}}

            <div class="card">

                <div class="card-inner">

                    <div class="receipt-info-comment">


                        {{-- СКЛАД --}}

                        <div class="info-item">

                            <span class="info-label">
                                Склад
                            </span>

                            <div class="info-value">
                                {{ $receipt->warehouse->name }}
                            </div>

                        </div>


                        {{-- ДАТА --}}

                        <div class="info-item">

                            <span class="info-label">
                                Дата приёмки
                            </span>

                            <div class="info-value">
                                {{ $receipt->receipt_date->format('d.m.Y') }}
                            </div>

                        </div>


                        <div class="info-item">

                            <label for="supplier_id">
                                Поставщик
                            </label>

                            <select
                                name="supplier_id"
                                id="supplier_id">

                                <option value="">
                                    Без поставщика
                                </option>

                                @foreach($suppliers as $supplier)

                                <option
                                    value="{{ $supplier->id }}"
                                    @selected(
                                    old( 'supplier_id' ,
                                    $receipt->supplier_id
                                    ) == $supplier->id
                                    )>

                                    {{ $supplier->name }}

                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- ПОСТАВЩИК --}}




                        {{-- КОММЕНТАРИЙ --}}

                        <div class="comment-block">

                            <div class="field">

                                <label>
                                    Комментарий
                                </label>

                                <textarea
                                    name="comment"
                                    placeholder="Комментарий">{{ old(
                                    'comment',
                                    $receipt->comment
                                ) }}</textarea>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                     FINANCE
                ================================================== --}}

                    @if($isOwnerOrAnalyst)

                    <div class="finance-block">


                        {{-- СУММА ПРИЁМКИ --}}

                        <div class="finance-item">

                            <div class="finance-label">
                                Сумма приёмки
                            </div>

                            <div
                                class="finance-value"
                                id="receipt-total">

                                {{ number_format(
                                    $receiptTotal,
                                    0,
                                    ',',
                                    ' '
                                ) }} ₸

                            </div>

                        </div>


                        {{-- ОПЛАЧЕНО --}}

                        <div class="finance-item">

                            <div class="finance-label">
                                Оплачено
                            </div>

                            <input
                                type="number"
                                name="paid_amount"
                                id="paid_amount"
                                class="finance-input"
                                min="0"
                                step="0.01"
                                value="{{ old(
                                    'paid_amount',
                                    $paidAmount
                                ) }}">

                        </div>


                        {{-- ДОЛГ --}}

                        <div class="finance-item">

                            <div class="finance-label">
                                Долг
                            </div>

                            <div
                                class="finance-value debt"
                                id="receipt-debt">

                                {{ number_format(
                                    $debtAmount,
                                    0,
                                    ',',
                                    ' '
                                ) }} ₸

                            </div>

                        </div>


                    </div>

                    @endif


                    {{-- WARNING --}}

                    <div class="alert alert-warning warning">

                        <div class="alert-icon">
                            !
                        </div>

                        <div class="alert-content">

                            Дата и склад приёмки после создания
                            не изменяются. Если они указаны неправильно —
                            удалите приёмку и создайте её заново.

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
             PRODUCTS
        ================================================== --}}

            <div class="card">


                <div class="section-head">

                    <div>

                        <div class="section-title">
                            Товары
                        </div>

                        <div class="section-subtitle">
                            Изменение количества и закупочной цены
                        </div>

                    </div>

                </div>


                <div class="products-body">


                    {{-- EXISTING ITEMS --}}

                    @foreach($receipt->items as $index => $item)

                    @php

                    $layer = $item->inventoryLayer;

                    $consumed = $layer
                    ? \App\Models\FifoAllocation::where(
                    'inventory_layer_id',
                    $layer->id
                    )->sum('quantity')
                    : 0;

                    @endphp


                    <div class="item">


                        {{-- DELETE --}}

                        <button
                            type="button"
                            class="delete-item-btn"
                            title="Удалить артикул"

                            data-url="{{ route(
                                'admin.receipts.items.destroy',
                                [
                                    'receipt' => $receipt,
                                    'receiptItem' => $item
                                ]
                            ) }}"

                            onclick="deleteExistingItem(this)">

                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <polyline points="3 6 5 6 21 6"></polyline>

                                <path d="M19 6l-1 14H6L5 6"></path>

                                <path d="M10 11v6"></path>

                                <path d="M14 11v6"></path>

                                <path d="M9 6V4h6v2"></path>

                            </svg>

                        </button>


                        {{-- SKU + BATCH --}}

                        <div class="product-main">


                            <div class="field">

                                <label>
                                    Артикул
                                </label>

                                <div class="readonly">
                                    {{ $item->variant->sku }}
                                </div>

                            </div>


                            <div class="field">

                                <label>
                                    Партия
                                </label>

                                <div class="readonly">
                                    {{ $item->batch->batch_code }}
                                </div>

                            </div>


                            <input
                                type="hidden"
                                name="items[{{ $index }}][id]"
                                value="{{ $item->id }}">

                        </div>


                        {{-- QUANTITY + PRICE --}}

                        <div class="product-values">


                            <div class="field">

                                <label>
                                    Количество
                                </label>

                                <input
                                    type="number"
                                    name="items[{{ $index }}][quantity]"

                                    value="{{ old(
                                        'items.' . $index . '.quantity',
                                        $item->quantity
                                    ) }}"

                                    min="{{ max(1, $consumed) }}"

                                    required>

                                @if($consumed > 0)

                                <div class="hint">

                                    Использовано:
                                    <strong>
                                        {{ $consumed }} шт.
                                    </strong>

                                    <br>

                                    Мин:
                                    <strong>
                                        {{ $consumed }} шт.
                                    </strong>

                                </div>

                                @endif

                            </div>


                            <div class="field">

                                <label>
                                    Закупочная цена
                                </label>

                                <input
                                    type="number"
                                    name="items[{{ $index }}][purchase_price]"

                                    value="{{ old(
                                        'items.' . $index . '.purchase_price',
                                        $item->purchase_price
                                    ) }}"

                                    min="0"
                                    step="0.01"
                                    required>

                                <div class="hint">

                                    Текущая:
                                    <strong>

                                        {{ number_format(
                                            (float) $item->purchase_price,
                                            2,
                                            ',',
                                            ' '
                                        ) }} ₸

                                    </strong>

                                </div>

                            </div>


                        </div>


                    </div>

                    @endforeach


                    {{-- NEW ITEMS --}}

                    <div id="new-items"></div>


                    {{-- ADD BUTTON --}}

                    <button
                        type="button"
                        class="add-item-btn"
                        onclick="addNewItem()">

                        <span class="add-item-icon">
                            +
                        </span>

                        Добавить товар

                    </button>


                </div>

            </div>


        </form>


    </div>


    <script>
        /* =====================================================
       VARIANTS
    ====================================================== */

        const variants = @json($variants);

        let newItemIndex = 0;


        /* =====================================================
           RECALCULATE TOTAL / DEBT
        ====================================================== */

        function recalculateReceiptTotal() {

            let total = 0;


            document
                .querySelectorAll(
                    'input[name^="items["][name$="[quantity]"]'
                )
                .forEach(function(quantityInput) {

                    const name = quantityInput.name;

                    const match = name.match(
                        /items\[(.*?)\]\[quantity\]/
                    );

                    if (!match) {
                        return;
                    }

                    const index = match[1];

                    const priceInput =
                        document.querySelector(
                            `input[name="items[${index}][purchase_price]"]`
                        );

                    if (!priceInput) {
                        return;
                    }

                    const quantity =
                        parseFloat(quantityInput.value) || 0;

                    const price =
                        parseFloat(priceInput.value) || 0;

                    total += quantity * price;

                });


            /* NEW ITEMS */

            document
                .querySelectorAll(
                    'input[name^="new_items["][name$="[quantity]"]'
                )
                .forEach(function(quantityInput) {

                    const name = quantityInput.name;

                    const match = name.match(
                        /new_items\[(.*?)\]\[quantity\]/
                    );

                    if (!match) {
                        return;
                    }

                    const index = match[1];

                    const priceInput =
                        document.querySelector(
                            `input[name="new_items[${index}][purchase_price]"]`
                        );

                    if (!priceInput) {
                        return;
                    }

                    const quantity =
                        parseFloat(quantityInput.value) || 0;

                    const price =
                        parseFloat(priceInput.value) || 0;

                    total += quantity * price;

                });


            const totalElement =
                document.getElementById(
                    'receipt-total'
                );


            if (totalElement) {

                totalElement.textContent =
                    formatMoney(total);

            }


            const paidInput =
                document.getElementById(
                    'paid_amount'
                );

            let paid =
                paidInput ?
                parseFloat(paidInput.value) || 0 :
                0;


            /* Не позволяем оплате быть выше общей суммы */

            if (paid > total && paidInput) {

                paid = total;

                paidInput.value =
                    total.toFixed(2);

            }


            const debt =
                Math.max(
                    0,
                    total - paid
                );


            const debtElement =
                document.getElementById(
                    'receipt-debt'
                );


            if (debtElement) {

                debtElement.textContent =
                    formatMoney(debt);

                debtElement.style.color =
                    debt > 0 ?
                    '#dc2626' :
                    '#166534';

            }

        }


        function formatMoney(value) {

            return new Intl.NumberFormat(
                'ru-RU', {
                    maximumFractionDigits: 0
                }
            ).format(value) + ' ₸';

        }


        document.addEventListener(
            'input',
            function(event) {

                if (
                    event.target.matches(
                        'input[name$="[quantity]"], input[name$="[purchase_price]"], #paid_amount'
                    )
                ) {

                    recalculateReceiptTotal();

                }

            }
        );


        /* =====================================================
           IOS / IPHONE ZOOM PROTECTION
        ====================================================== */

        (function() {

            const isIOS =
                /iPad|iPhone|iPod/.test(
                    navigator.userAgent
                ) ||
                (
                    navigator.platform === 'MacIntel' &&
                    navigator.maxTouchPoints > 1
                );

            if (!isIOS) {
                return;
            }


            function prepareForFocus(element) {

                if (!element) {
                    return;
                }

                if (
                    element.tagName !== 'INPUT' &&
                    element.tagName !== 'TEXTAREA' &&
                    element.tagName !== 'SELECT'
                ) {
                    return;
                }


                if (
                    !element.dataset.iosOriginalFontSizeSaved
                ) {

                    element.dataset.iosOriginalFontSize =
                        element.style.fontSize || '';

                    element.dataset.iosOriginalFontSizeSaved =
                        '1';

                }


                element.style.fontSize =
                    '16px';

            }


            function restoreAfterBlur(element) {

                if (!element) {
                    return;
                }

                if (
                    element.dataset.iosOriginalFontSizeSaved !== '1'
                ) {
                    return;
                }

                element.style.fontSize =
                    element.dataset.iosOriginalFontSize || '';

                delete element.dataset.iosOriginalFontSize;
                delete element.dataset.iosOriginalFontSizeSaved;

            }


            document.addEventListener(
                'pointerdown',
                function(event) {

                    const element =
                        event.target.closest(
                            'input, textarea, select'
                        );

                    if (element) {
                        prepareForFocus(element);
                    }

                },
                true
            );


            document.addEventListener(
                'touchstart',
                function(event) {

                    const element =
                        event.target.closest(
                            'input, textarea, select'
                        );

                    if (element) {
                        prepareForFocus(element);
                    }

                },
                true
            );


            document.addEventListener(
                'focusin',
                function(event) {

                    const element =
                        event.target;

                    if (
                        element &&
                        (
                            element.tagName === 'INPUT' ||
                            element.tagName === 'TEXTAREA' ||
                            element.tagName === 'SELECT'
                        )
                    ) {

                        prepareForFocus(element);

                    }

                },
                true
            );


            document.addEventListener(
                'focusout',
                function(event) {

                    const element =
                        event.target;

                    if (
                        element &&
                        (
                            element.tagName === 'INPUT' ||
                            element.tagName === 'TEXTAREA' ||
                            element.tagName === 'SELECT'
                        )
                    ) {

                        setTimeout(
                            function() {

                                restoreAfterBlur(element);

                            },
                            50
                        );

                    }

                },
                true
            );

        })();


        /* =====================================================
           ADD NEW ITEM
        ====================================================== */

        function addNewItem() {

            const container =
                document.getElementById(
                    'new-items'
                );

            const index =
                newItemIndex++;


            const item =
                document.createElement(
                    'div'
                );

            item.className =
                'item new-item';

            item.dataset.index =
                index;


            item.innerHTML = `

        <div class="new-item-header">

            <div class="new-item-title">
                Новый товар
            </div>

            <button
                type="button"
                class="delete-item-btn"
                onclick="removeNewItem(this)"
                title="Удалить">

                <svg
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <polyline points="3 6 5 6 21 6"></polyline>

                    <path d="M19 6l-1 14H6L5 6"></path>

                    <path d="M10 11v6"></path>

                    <path d="M14 11v6"></path>

                    <path d="M9 6V4h6v2"></path>

                </svg>

            </button>

        </div>


        <div class="product-main">

            <div class="field">

                <label>
                    Артикул
                </label>

                <div class="autocomplete">

                    <div class="autocomplete-input-wrap">

                        <input
                            type="text"
                            class="autocomplete-input"
                            placeholder="Введите артикул"
                            autocomplete="off"
                            oninput="searchNewVariant(this)"
                            onfocus="openVariantSearch(this)">

                        <span class="autocomplete-icon"></span>

                    </div>

                    <div class="autocomplete-dropdown"></div>

                    <input
                        type="hidden"
                        name="new_items[${index}][variant_id]"
                        class="new-variant-id"
                        required>

                </div>

                <div class="selected-variant"></div>

            </div>


            <div class="field batch-select-wrap">

                <label>
                    Партия
                </label>

                <select
                    name="new_items[${index}][batch_id]"
                    class="new-batch-select"
                    required
                    disabled>

                    <option value="">
                        Сначала выберите артикул
                    </option>

                </select>

            </div>

        </div>


        <div class="product-values">

            <div class="field">

                <label>
                    Количество
                </label>

                <input
                    type="number"
                    name="new_items[${index}][quantity]"
                    min="1"
                    value="1"
                    required>

            </div>


            <div class="field">

                <label>
                    Закупочная цена
                </label>

                <input
                    type="number"
                    name="new_items[${index}][purchase_price]"
                    class="new-price-input"
                    min="0"
                    step="0.01"
                    value="0"
                    required>

            </div>

        </div>

        `;


            container.appendChild(item);


            const input =
                item.querySelector(
                    '.autocomplete-input'
                );


            if (input) {

                setTimeout(() => {

                    input.focus();

                }, 50);

            }

        }


        /* =====================================================
           SEARCH VARIANT
        ====================================================== */

        function searchNewVariant(input) {

            const autocomplete =
                input.closest(
                    '.autocomplete'
                );

            if (!autocomplete) {
                return;
            }


            const dropdown =
                autocomplete.querySelector(
                    '.autocomplete-dropdown'
                );


            const value =
                input.value
                .trim()
                .toLowerCase();


            const item =
                input.closest(
                    '.new-item'
                );


            if (!item) {
                return;
            }


            const hiddenId =
                item.querySelector(
                    '.new-variant-id'
                );


            hiddenId.value = '';


            item.querySelector(
                '.selected-variant'
            ).textContent = '';


            resetBatchSelect(item);


            dropdown.innerHTML = '';


            if (!value) {

                dropdown.classList.remove(
                    'show'
                );

                return;

            }


            const results =
                variants
                .filter(variant => {

                    const sku =
                        String(
                            variant.sku ?? ''
                        ).toLowerCase();


                    const name =
                        String(
                            variant.name ?? ''
                        ).toLowerCase();


                    return (
                        sku.includes(value) ||
                        name.includes(value)
                    );

                })
                .slice(0, 30);


            if (!results.length) {

                dropdown.innerHTML = `

                <div class="autocomplete-empty">
                    Артикул не найден
                </div>

            `;

                dropdown.classList.add(
                    'show'
                );

                return;

            }


            results.forEach(variant => {

                const option =
                    document.createElement(
                        'button'
                    );


                option.type = 'button';

                option.className =
                    'autocomplete-option';


                option.innerHTML = `

                <span class="autocomplete-option-sku">
                    ${escapeHtml(
                        variant.sku ?? ''
                    )}
                </span>

                <span class="autocomplete-option-name">
                    ${escapeHtml(
                        variant.name ?? ''
                    )}
                </span>

            `;


                option.addEventListener(
                    'mousedown',
                    function(event) {

                        event.preventDefault();

                        selectNewVariant(
                            input,
                            variant
                        );

                    }
                );


                dropdown.appendChild(
                    option
                );

            });


            dropdown.classList.add(
                'show'
            );

        }


        /* =====================================================
           OPEN SEARCH
        ====================================================== */

        function openVariantSearch(input) {

            const autocomplete =
                input.closest(
                    '.autocomplete'
                );

            if (!autocomplete) {
                return;
            }


            const dropdown =
                autocomplete.querySelector(
                    '.autocomplete-dropdown'
                );


            if (input.value.trim()) {

                searchNewVariant(
                    input
                );

                return;

            }


            dropdown.innerHTML = '';


            variants
                .slice(0, 30)
                .forEach(variant => {

                    const option =
                        document.createElement(
                            'button'
                        );


                    option.type = 'button';

                    option.className =
                        'autocomplete-option';


                    option.innerHTML = `

                    <span class="autocomplete-option-sku">
                        ${escapeHtml(
                            variant.sku ?? ''
                        )}
                    </span>

                    <span class="autocomplete-option-name">
                        ${escapeHtml(
                            variant.name ?? ''
                        )}
                    </span>

                `;


                    option.addEventListener(
                        'mousedown',
                        function(event) {

                            event.preventDefault();

                            selectNewVariant(
                                input,
                                variant
                            );

                        }
                    );


                    dropdown.appendChild(
                        option
                    );

                });


            if (variants.length) {

                dropdown.classList.add(
                    'show'
                );

            }

        }


        /* =====================================================
           SELECT VARIANT
        ====================================================== */

        function selectNewVariant(
            input,
            variant
        ) {

            const item =
                input.closest(
                    '.new-item'
                );


            if (!item) {
                return;
            }


            const hiddenId =
                item.querySelector(
                    '.new-variant-id'
                );


            const selectedText =
                item.querySelector(
                    '.selected-variant'
                );


            const dropdown =
                input.closest(
                    '.autocomplete'
                )
                .querySelector(
                    '.autocomplete-dropdown'
                );


            hiddenId.value =
                variant.id;


            input.value =
                variant.sku ?? '';


            selectedText.textContent =
                variant.name ?
                variant.name :
                '';


            dropdown.innerHTML = '';

            dropdown.classList.remove(
                'show'
            );


            changeNewVariant(
                item
            );

        }


        /* =====================================================
           CHANGE VARIANT
        ====================================================== */

        function changeNewVariant(item) {

            if (!item) {
                return;
            }


            const variantIdInput =
                item.querySelector(
                    '.new-variant-id'
                );


            const batchSelect =
                item.querySelector(
                    '.new-batch-select'
                );


            const priceInput =
                item.querySelector(
                    '.new-price-input'
                );


            const variantId =
                Number(
                    variantIdInput.value
                );


            batchSelect.innerHTML = '';


            if (!variantId) {

                resetBatchSelect(item);

                priceInput.value = '0';

                return;

            }


            const variant =
                variants.find(
                    v =>
                    Number(v.id) ===
                    variantId
                );


            if (!variant) {

                resetBatchSelect(item);

                return;

            }


            batchSelect.disabled =
                false;


            if (
                !variant.batches ||
                !variant.batches.length
            ) {

                batchSelect.innerHTML = `

                <option value="">
                    Нет доступных партий
                </option>

            `;

            } else {

                batchSelect.innerHTML = `

                <option value="">
                    Выберите партию
                </option>

            `;


                variant.batches.forEach(
                    batch => {

                        const option =
                            document.createElement(
                                'option'
                            );


                        option.value =
                            batch.id;


                        option.textContent =
                            batch.code;


                        batchSelect.appendChild(
                            option
                        );

                    }
                );

            }


            priceInput.value =
                Number(
                    variant.purchase_price || 0
                ).toFixed(2);

        }


        /* =====================================================
           RESET BATCH
        ====================================================== */

        function resetBatchSelect(item) {

            const batchSelect =
                item.querySelector(
                    '.new-batch-select'
                );


            batchSelect.disabled =
                true;


            batchSelect.innerHTML = `

            <option value="">
                Сначала выберите артикул
            </option>

        `;

        }


        /* =====================================================
           REMOVE NEW ITEM
        ====================================================== */

        function removeNewItem(button) {

            const item =
                button.closest(
                    '.new-item'
                );


            if (item) {

                item.remove();

                recalculateReceiptTotal();

            }

        }


        /* =====================================================
           DELETE EXISTING ITEM
        ====================================================== */

        function deleteExistingItem(button) {

            const url =
                button.dataset.url;


            if (!url) {
                return;
            }


            const confirmed =
                confirm(
                    'Удалить этот артикул из приёмки?\n\n' +
                    'Количество будет списано со склада.'
                );


            if (!confirmed) {
                return;
            }


            button.disabled =
                true;


            button.style.opacity =
                '0.5';


            button.style.pointerEvents =
                'none';


            const form =
                document.createElement(
                    'form'
                );


            form.method =
                'POST';


            form.action =
                url;


            form.style.display =
                'none';


            const csrf =
                document.createElement(
                    'input'
                );


            csrf.type =
                'hidden';

            csrf.name =
                '_token';

            csrf.value =
                '{{ csrf_token() }}';


            const method =
                document.createElement(
                    'input'
                );


            method.type =
                'hidden';

            method.name =
                '_method';

            method.value =
                'DELETE';


            form.appendChild(
                csrf
            );

            form.appendChild(
                method
            );


            document.body.appendChild(
                form
            );


            form.submit();

        }


        /* =====================================================
           ESCAPE HTML
        ====================================================== */

        function escapeHtml(value) {

            return String(
                    value ?? ''
                )
                .replace(
                    /&/g,
                    '&amp;'
                )
                .replace(
                    /</g,
                    '&lt;'
                )
                .replace(
                    />/g,
                    '&gt;'
                )
                .replace(
                    /"/g,
                    '&quot;'
                )
                .replace(
                    /'/g,
                    '&#039;'
                );

        }


        /* =====================================================
           CLOSE AUTOCOMPLETE
        ====================================================== */

        document.addEventListener(
            'click',
            function(event) {

                document
                    .querySelectorAll(
                        '.autocomplete-dropdown.show'
                    )
                    .forEach(
                        dropdown => {

                            const autocomplete =
                                dropdown.closest(
                                    '.autocomplete'
                                );


                            if (
                                autocomplete &&
                                !autocomplete.contains(
                                    event.target
                                )
                            ) {

                                dropdown.classList.remove(
                                    'show'
                                );

                            }

                        }
                    );

            }
        );


        /* =====================================================
           ESC
        ====================================================== */

        document.addEventListener(
            'keydown',
            function(event) {

                if (
                    event.key === 'Escape'
                ) {

                    document
                        .querySelectorAll(
                            '.autocomplete-dropdown.show'
                        )
                        .forEach(
                            dropdown => {

                                dropdown.classList.remove(
                                    'show'
                                );

                            }
                        );

                }

            }
        );


        /* =====================================================
           PROTECT DOUBLE SUBMIT
        ====================================================== */

        document
            .getElementById(
                'receipt-edit-form'
            )
            .addEventListener(
                'submit',
                function() {

                    const button =
                        document.getElementById(
                            'save-button'
                        );


                    if (!button) {
                        return;
                    }


                    button.disabled =
                        true;


                    button.textContent =
                        'Сохранение...';


                    button.style.opacity =
                        '0.7';


                    button.style.cursor =
                        'wait';

                }
            );


        /* =====================================================
           INITIAL CALCULATION
        ====================================================== */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                recalculateReceiptTotal();

            }
        );
    </script>

</body>

</html>