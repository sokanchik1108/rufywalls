
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
            line-height: 1.45;
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
            padding: 20px 24px 40px;
        }


        /* =====================================================
           TOP HEADER
        ===================================================== */

        .top {
            position: relative;

            width: 100%;
            min-height: 46px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 22px;
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

            font-size: 24px;
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
            gap: 7px;

            min-height: 38px;
            padding: 0 13px;

            border: 1px solid transparent;
            border-radius: 9px;

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

            box-shadow:
                0 1px 2px rgba(0, 0, 0, .08);
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
            min-width: 165px;
        }


        /* =====================================================
           ALERTS
        ===================================================== */

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 11px;

            margin-bottom: 16px;
            padding: 13px 15px;

            border-radius: 11px;

            font-size: 13px;
        }

        .alert-icon {
            width: 20px;
            height: 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            flex: 0 0 20px;

            border-radius: 50%;

            font-size: 11px;
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
            border-radius: 14px;

            margin-bottom: 16px;

            box-shadow:
                0 1px 2px rgba(15, 23, 42, .02);
        }

        .card-inner {
            padding: 20px;
        }


        /* =====================================================
           RECEIPT INFO + COMMENT
        ===================================================== */

        .receipt-info-comment {
            display: grid;

            grid-template-columns:
                170px 210px minmax(0, 1fr);

            gap: 12px;

            align-items: stretch;
        }

        .info-item {
            min-height: 86px;

            display: flex;
            flex-direction: column;
            justify-content: center;

            padding: 14px 16px;

            background: var(--soft);

            border: 1px solid #edf0f3;
            border-radius: 11px;
        }

        .info-label {
            display: block;

            margin-bottom: 6px;

            color: #9ca3af;

            font-size: 10px;
            font-weight: 700;

            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .info-value {
            color: var(--text);

            font-size: 14px;
            font-weight: 650;
        }

        .comment-block {
            min-width: 0;
        }

        .comment-block textarea {
            width: 100%;

            height: 86px;
            min-height: 86px;

            padding: 12px 13px;

            border: 1px solid #dfe3e8;
            border-radius: 11px;

            background: #fff;
            color: var(--text);

            outline: none;

            resize: vertical;

            font-size: 13px;

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

        .warning {
            margin-top: 14px;
            margin-bottom: 0;
        }


        /* =====================================================
           SECTION HEADER
        ===================================================== */

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;

            padding: 18px 20px;

            border-bottom: 1px solid #eef0f2;
        }

        .section-title {
            margin: 0;

            font-size: 16px;
            line-height: 1.2;

            font-weight: 720;
            letter-spacing: -.01em;
        }

        .section-subtitle {
            margin-top: 4px;

            color: #9ca3af;

            font-size: 11px;
        }


        /* =====================================================
           PRODUCTS
        ===================================================== */

        .products-body {
            padding: 14px;
        }

        .item {
            position: relative;

            display: grid;

            grid-template-columns:
                minmax(180px, 1.35fr)
                minmax(130px, 1fr)
                72px
                92px;

            gap: 7px;

            padding: 8px 48px 8px 10px;

            background: #fff;

            border: 1px solid #e6e9ed;
            border-radius: 9px;

            margin-bottom: 6px;

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


        /* =====================================================
           LEFT PART
        ===================================================== */

        .product-main {
            display: contents;
        }

        .identity-box {
            min-height: 42px;

            display: grid;

            grid-template-columns:
                minmax(0, 1fr) minmax(0, 1fr);

            align-items: center;

            gap: 14px;

            padding: 0;

            border: 0;
            background: transparent;
        }

        .identity-sku {
            min-width: 0;

            color: #111827;

            font-size: 13px;
            line-height: 1.25;

            font-weight: 750;

            overflow-wrap: anywhere;
        }

        .identity-batch {
            min-width: 0;

            color: #7b8490;

            font-size: 13px;
            line-height: 1.3;

            overflow-wrap: anywhere;
        }

        .identity-batch span {
            color: #9ca3af;
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

            font-size: 9px;
            line-height: 1;
            font-weight: 650;
        }

        input,
        textarea,
        select {
            width: 100%;

            min-height: 34px;

            padding: 6px 9px;

            border: 1px solid #dfe3e8;
            border-radius: 9px;

            background: #fff;
            color: #111827;

            outline: none;

            font-size: 13px;

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

            min-height: 34px;

            display: flex;
            align-items: center;

            padding: 6px 9px;

            border: 1px solid #e8ebef;
            border-radius: 9px;

            background: #f8fafc;
            color: #1f2937;

            font-size: 13px;
            font-weight: 600;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }


        /* =====================================================
           EXISTING IDENTITY
        ===================================================== */

        .existing-identity {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr) minmax(0, 1fr);

            align-items: center;

            gap: 14px;

            min-height: 42px;

            padding: 9px 11px;

            border: 1px solid #e8ebef;
            border-radius: 9px;

            background: #f8fafc;
        }

        .existing-identity .identity-sku {
            padding-right: 14px;

            border-right: 1px solid #e1e5e9;
        }

        .existing-identity .identity-batch {
            padding-left: 0;
        }

        .hint {
            color: #9ca3af;

            font-size: 10px;
            line-height: 1.45;
        }

        .hint strong {
            color: #6b7280;
            font-weight: 650;
        }


        /* =====================================================
           DELETE BUTTON
        ===================================================== */

        .delete-item-btn {
            position: absolute;

            top: 12px;
            right: 12px;
            left: auto;

            z-index: 20;

            width: 32px;
            height: 32px;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 0;

            border: 1px solid #eceff2;
            border-radius: 8px;

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

            padding-top: 58px;

            border-style: dashed;
            border-color: #d2d8df;

            background: #fcfcfd;
        }

        .new-item-header {
            position: absolute;

            top: 12px;
            left: 16px;
            right: 54px;

            height: 32px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            pointer-events: none;
        }

        .new-item-title {
            display: inline-flex;
            align-items: center;

            gap: 7px;

            color: #6b7280;

            font-size: 10px;
            font-weight: 750;

            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .new-item-title::before {
            content: "+";

            width: 22px;
            height: 22px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 6px;

            background: #f0f2f5;
            color: #4b5563;

            font-size: 15px;
            font-weight: 500;
        }

        .new-item-header .delete-item-btn {
            position: absolute;

            top: -2px;
            right: -42px;

            pointer-events: auto;
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
            padding-right: 38px;
        }

        .autocomplete-icon {
            position: absolute;

            top: 50%;
            right: 12px;

            width: 16px;
            height: 16px;

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

            width: 6px;
            height: 1.5px;

            background: currentColor;

            border-radius: 2px;

            transform: rotate(45deg);
        }

        .autocomplete-dropdown {
            position: absolute;

            top: calc(100% + 5px);
            left: 0;
            right: 0;

            z-index: 1000;

            display: none;

            max-height: 280px;

            overflow-y: auto;

            background: #fff;

            border: 1px solid #dfe3e8;
            border-radius: 10px;

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

            padding: 10px 12px;

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

            font-size: 12px;
            font-weight: 750;
        }

        .autocomplete-option-name {
            max-width: 100%;

            color: #8b95a3;

            font-size: 10px;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .autocomplete-empty {
            padding: 13px 12px;

            color: #9ca3af;

            font-size: 11px;

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
           BATCH
        ===================================================== */

        .batch-select-wrap {
            position: relative;
        }

        .batch-select-wrap select:disabled {
            background: #f8fafc;
            color: #a0a7b0;
            cursor: not-allowed;
        }

        .new-item {
            grid-template-columns:
                minmax(110px, .75fr)
                minmax(100px, .65fr)
                minmax(150px, 1.45fr)
                minmax(105px, .9fr);
        }

        .new-item .product-main {
            display: contents;
        }

        .new-item .product-main>.field:first-child {
            min-width: 0;
        }

        .new-item .batch-select-wrap {
            min-width: 0;
        }


        /* =====================================================
           ADD ITEM BUTTON
        ===================================================== */

        .add-item-btn {
            width: auto;

            min-height: 38px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 7px;

            margin-top: 12px;

            padding: 0 14px;

            border: 1px dashed #cfd5dc;
            border-radius: 9px;

            background: #fafbfc;
            color: #4b5563;

            font-size: 12px;
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
            width: 20px;
            height: 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 5px;

            background: #111827;
            color: #fff;

            font-size: 15px;
            font-weight: 400;

            line-height: 1;
        }


        /* =====================================================
           MOBILE / TABLET
        ===================================================== */

        @media (max-width: 900px) {

            .page {
                padding: 20px 16px 30px;
            }

            .top-left,
            .top-right {
                width: 160px;
            }

            .receipt-info-comment {
                grid-template-columns: 1fr 1fr;
            }

            .comment-block {
                grid-column: 1 / -1;
            }

            .item {
                grid-template-columns:
                    minmax(150px, 1.25fr)
                    minmax(110px, 1fr)
                    68px
                    88px;
                gap: 6px;
                padding: 7px 44px 7px 8px;
            }

            .product-main,
            .product-values {
                display: contents;
            }
        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 640px) {

            body {
                font-size: 13px;
            }

            .page {
                padding: 10px 10px 24px;
            }


            /* HEADER */

            .top {
                min-height: 40px;

                margin-bottom: 16px;

                padding: 0;
            }

            .top-left,
            .top-right {
                width: auto;
            }

            .top-center {
                position: absolute;
            }

            h1 {
                font-size: 18px;
            }

            .back-button {
                min-height: 34px;

                padding: 0 10px;

                font-size: 11px;
            }

            .back-icon {
                font-size: 14px;
            }

            .save-top-button {
                min-width: 0;

                min-height: 34px;

                padding: 0 10px;

                font-size: 10px;
            }


            /* CARDS */

            .card {
                border-radius: 12px;

                margin-bottom: 12px;
            }

            .card-inner {
                padding: 12px;
            }


            /* INFO */

            .receipt-info-comment {
                grid-template-columns: 1fr 1fr;

                gap: 7px;
            }

            .info-item {
                min-height: 30px;

                padding: 10px 11px;

                border-radius: 9px;
            }

            .info-label {
                margin-bottom: 3px;

                font-size: 8px;
            }

            .info-value {
                font-size: 11px;
            }

            .comment-block {
                grid-column: 1 / -1;
            }

            .comment-block textarea {
                height: 50px;
                min-height: 50px;

                padding: 9px;

                font-size: 12px;
            }


            /* ALERT */

            .alert {
                margin-bottom: 10px;

                padding: 10px 11px;

                border-radius: 9px;

                font-size: 10px;
            }

            .alert-icon {
                width: 18px;
                height: 18px;

                flex-basis: 18px;

                font-size: 9px;
            }

            .warning {
                margin-top: 9px !important;
            }


            /* SECTION */

            .section-head {
                padding: 13px 12px;
            }

            .section-title {
                font-size: 14px;
            }

            .section-subtitle {
                font-size: 9px;
            }


            /* PRODUCTS */

            .products-body {
                padding: 9px;
            }

            .item {
                grid-template-columns:
                    minmax(0, 1.25fr)
                    minmax(0, 1fr)
                    58px
                    74px;

                gap: 5px;

                padding: 52px 7px 7px;

                margin-bottom: 5px;

                border-radius: 8px;
            }

            .product-main,
            .product-values {
                display: contents;
            }

            /* NEW ITEM */
            .new-item {
                padding-top: 52px;
            }


            /* FIELDS */

            .field {
                gap: 2px;
                min-width: 0;
            }

            label {
                font-size: 7px;
                line-height: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            input,
            textarea,
            select {
                min-height: 30px;

                height: 30px;

                padding: 4px 6px;

                border-radius: 6px;

                font-size: 10px;
            }

            .readonly {
                min-height: 30px;

                height: 30px;

                padding: 4px 6px;

                border-radius: 6px;

                font-size: 9px;
            }


            /* EXISTING */

            .existing-identity {
                grid-template-columns:
                    minmax(0, 1fr) minmax(0, 1fr);

                gap: 5px;

                min-height: 30px;

                padding: 5px;

                border-radius: 6px;
            }

            .existing-identity .identity-sku {
                padding-right: 8px;
            }

            .identity-box {
                min-height: 38px;

                grid-template-columns:
                    minmax(0, 1fr) minmax(0, 1fr);

                gap: 8px;
            }

            .identity-sku {
                font-size: 11px;
            }

            .identity-batch {
                font-size: 9px;
            }

            .hint {
                margin-top: 1px;
                font-size: 7px;
                line-height: 1.15;
            }


            /* DELETE */

            .delete-item-btn {
                top: 10px;
                right: 10px;

                width: 29px;
                height: 29px;

                border-radius: 7px;
            }

            .delete-item-btn svg {
                width: 13px;
                height: 13px;
            }


            /* NEW ITEM */

            .new-item {
                padding-top: 55px;
            }

            .new-item-header {
                top: 10px;
                left: 10px;
                right: 49px;

                height: 31px;
            }

            .new-item-title {
                font-size: 9px;
            }

            .new-item-title::before {
                width: 20px;
                height: 20px;

                font-size: 13px;
            }

            .new-item-header .delete-item-btn {
                top: -1px;
                right: -40px;
            }


            /* AUTOCOMPLETE */

            .autocomplete-dropdown {
                max-height: 230px;
            }

            .autocomplete-option {
                padding: 9px 10px;
            }

            .autocomplete-option-sku {
                font-size: 11px;
            }

            .autocomplete-option-name {
                font-size: 9px;
            }


            /* ADD BUTTON */

            .add-item-btn {
                min-height: 36px;

                padding: 0 12px;

                margin-top: 10px;

                font-size: 11px;
                width: 100%;
            }

            .add-item-icon {
                width: 19px;
                height: 19px;

                font-size: 14px;
            }

        }


        /* =====================================================
           VERY SMALL
        ===================================================== */

        @media (max-width: 380px) {

            .page {
                padding-left: 8px;
                padding-right: 8px;
            }

            h1 {
                font-size: 16px;
            }

            .back-button {
                padding: 0 8px;

                font-size: 10px;
            }

            .save-top-button {
                padding: 0 8px;

                font-size: 9px;
            }

            .info-item {
                padding: 9px;
            }

            .info-value {
                font-size: 10px;
            }

            .item {
                grid-template-columns:
                    minmax(0, 1.15fr)
                    minmax(0, .9fr)
                    52px
                    68px;

                gap: 4px;

                padding-left: 6px;
                padding-right: 6px;
            }

            input,
            textarea,
            select {
                font-size: 10px;
            }

            .identity-sku {
                font-size: 10px;
            }

            .identity-batch {
                font-size: 10px;
            }

            .readonly {
                font-size: 9px;
            }

            .existing-identity {
                gap: 6px;
                padding: 7px;
            }

            .existing-identity .identity-sku {
                padding-right: 6px;
            }

            .new-item {
                grid-template-columns:
                    minmax(0, .8fr)
                    minmax(0, .7fr)
                    minmax(0, 1.45fr)
                    minmax(0, .9fr);
            }

            .new-item .product-main {
                display: contents;
            }

        }
    </style>

</head>


<body>


    <div class="page">


        {{-- =====================================================
         TOP HEADER
    ====================================================== --}}

        <div class="top">


            {{-- НАЗАД --}}

            <div class="top-left">

                <a
                    href="{{ route('admin.receipts.index') }}"
                    class="btn btn-light back-button">

                    <span class="back-icon">
                        ←
                    </span>

                    Назад

                </a>

            </div>


            {{-- НОМЕР ПРИЁМКИ ПО ЦЕНТРУ --}}

            <div class="top-center">

                <h1>

                    Приёмка

                    <span class="receipt-number">
                        #{{ $receipt->id }}
                    </span>

                </h1>

            </div>


            {{-- СОХРАНИТЬ --}}

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
         SUCCESS
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


        {{-- =====================================================
         RECEIPT ERROR
    ====================================================== --}}

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


        {{-- =====================================================
         VALIDATION ERRORS
    ====================================================== --}}

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
         MAIN FORM
    ====================================================== --}}

        <form
            id="receipt-edit-form"
            method="POST"
            action="{{ route('admin.receipts.update', $receipt) }}">

            @csrf

            @method('PUT')


            {{-- =================================================
             DATE + WAREHOUSE + COMMENT
        ================================================== --}}

            <div class="card">

                <div class="card-inner">

                    <div class="receipt-info-comment">


                        {{-- СКЛАД --}}

                        <div class="info-item">

                            <span class="info-label">
                                Склад: <strong style="color: black;">{{ $receipt->warehouse->name }}</strong>
                            </span>


                        </div>


                        {{-- ДАТА --}}

                        <div class="info-item">

                            <span class="info-label">
                                Дата приёмки: <strong style="color: black;">{{ $receipt->receipt_date->format('d.m.Y') }}</strong>
                            </span>

                        </div>


                        {{-- КОММЕНТАРИЙ --}}

                        <div class="comment-block">

                            <div class="field">

                                <textarea
                                    name="comment"
                                    placeholder="Комментарий к приёмке">{{ old('comment', $receipt->comment) }}</textarea>

                            </div>

                        </div>


                    </div>


                    {{-- WARNING --}}

                    <div class="alert alert-warning warning">

                        <div class="alert-icon">
                            !
                        </div>

                        <div class="alert-content">

                            Дата и склад приёмки после создания не изменяются.
                            Если они указаны неправильно — удалите приёмку
                            и создайте её заново.

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

                    </div>

                </div>


                <div class="products-body">


                    {{-- =========================================
                     EXISTING ITEMS
                ========================================== --}}

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
                                width="15"
                                height="15"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <polyline
                                    points="3 6 5 6 21 6">
                                </polyline>

                                <path
                                    d="M19 6l-1 14H6L5 6">
                                </path>

                                <path d="M10 11v6"></path>

                                <path d="M14 11v6"></path>

                                <path d="M9 6V4h6v2"></path>

                            </svg>


                        </button>


                        {{-- =================================
                         SKU + BATCH
                    ================================== --}}

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


                        {{-- =================================
                         QUANTITY + PRICE
                    ================================== --}}

                        <div class="product-values">


                            {{-- КОЛИЧЕСТВО --}}

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

                                    Уже использовано:

                                    <strong>
                                        {{ $consumed }} шт.
                                    </strong>

                                    <br>

                                    Минимум:

                                    <strong>
                                        {{ $consumed }} шт.
                                    </strong>

                                </div>

                                @endif


                            </div>


                            {{-- ЦЕНА --}}

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

                                    Текущая цена:

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


                    {{-- =================================================
                     NEW ITEMS
                ================================================== --}}

                    <div id="new-items"></div>


                    {{-- =================================================
                     ADD BUTTON
                ================================================== --}}

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
           ADD NEW ITEM
        ====================================================== */

        function addNewItem() {

            const container =
                document.getElementById('new-items');

            const index =
                newItemIndex++;


            const item =
                document.createElement('div');

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
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <polyline
                        points="3 6 5 6 21 6">
                    </polyline>

                    <path
                        d="M19 6l-1 14H6L5 6">
                    </path>

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
                item.querySelector('.autocomplete-input');


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
                input.closest('.autocomplete');

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
                input.closest('.new-item');


            if (!item) {
                return;
            }


            const hiddenId =
                item.querySelector('.new-variant-id');


            hiddenId.value = '';

            item.querySelector(
                '.selected-variant'
            ).textContent = '';


            resetBatchSelect(item);


            dropdown.innerHTML = '';


            if (!value) {

                dropdown.classList.remove('show');

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

                dropdown.classList.add('show');

                return;

            }


            results.forEach(variant => {

                const option =
                    document.createElement('button');


                option.type = 'button';

                option.className =
                    'autocomplete-option';


                const sku =
                    escapeHtml(
                        variant.sku ?? ''
                    );


                const name =
                    escapeHtml(
                        variant.name ?? ''
                    );


                option.innerHTML = `

                <span class="autocomplete-option-sku">
                    ${sku}
                </span>

                <span class="autocomplete-option-name">
                    ${name}
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


                dropdown.appendChild(option);

            });


            dropdown.classList.add('show');

        }



        /* =====================================================
           OPEN SEARCH
        ====================================================== */

        function openVariantSearch(input) {

            const autocomplete =
                input.closest('.autocomplete');

            if (!autocomplete) {
                return;
            }


            const dropdown =
                autocomplete.querySelector(
                    '.autocomplete-dropdown'
                );


            if (input.value.trim()) {

                searchNewVariant(input);

                return;

            }


            dropdown.innerHTML = '';


            variants
                .slice(0, 30)
                .forEach(variant => {

                    const option =
                        document.createElement('button');


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


                    dropdown.appendChild(option);

                });


            if (variants.length) {

                dropdown.classList.add('show');

            }

        }



        /* =====================================================
           SELECT VARIANT
        ====================================================== */

        function selectNewVariant(input, variant) {

            const item =
                input.closest('.new-item');


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
                input.closest('.autocomplete')
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


            changeNewVariant(item);

        }



        /* =====================================================
           CHANGE VARIANT / LOAD BATCHES
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
                button.closest('.new-item');


            if (item) {

                item.remove();

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


            form.appendChild(csrf);

            form.appendChild(method);


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
           CLOSE AUTOCOMPLETE OUTSIDE
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
           PROTECT FROM DOUBLE SUBMIT
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
    </script>


</body>

</html>
