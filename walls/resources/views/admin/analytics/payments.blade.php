<!DOCTYPE html>

<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Платежи</title>

</head>

<body>

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        body {
            margin: 0;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        /* ========================================
           ОСНОВНАЯ СТРАНИЦА
        ======================================== */

        .payments-page {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px;
            background: #f4f5f8;
            min-height: 100vh;
            color: #111827;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ========================================
           НАВИГАЦИЯ
        ======================================== */

        .payments-navigation {
            display: block;
            width: 100%;
            margin-bottom: 18px;
            position: relative;
            z-index: 100;
        }

        .payments-menu-back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 36px;
            padding: 0 14px;
            border: 1px solid #e5e7eb;
            border-radius: 9px;
            background: #ffffff;
            color: #6b7280;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            cursor: pointer;
            transition:
                background .15s ease,
                border-color .15s ease,
                color .15s ease,
                box-shadow .15s ease;
        }

        .payments-menu-back-btn:hover {
            border-color: #01142f;
            color: #01142f;
            background: #f8fafc;
            box-shadow: 0 1px 3px rgba(1, 20, 47, .08);
        }

        /* ========================================
           HEADER
        ======================================== */

        .payments-header {
            margin-bottom: 24px;
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .payments-title {
            min-width: 0;
        }

        .payments-title h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 750;
            letter-spacing: -.5px;
        }

        .payments-title p {
            margin: 6px 0 0;
            color: #8a919c;
            font-size: 13.5px;
        }

        /* ========================================
           SECTION HEADER
        ======================================== */

        .section-head-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .section-head-row h2 {
            margin: 0;
        }

        /* ========================================
           FILTER
        ======================================== */

        .filter-box {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            background: #fafbfc;
            padding: 10px;
            border: 1px solid #eceef1;
            border-radius: 12px;
            width: auto;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            flex: 0 0 auto;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 0;
            box-sizing: border-box;
        }

        .field label {
            padding-left: 3px;
            font-size: 11px;
            font-weight: 600;
            color: #8a919c;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .field input,
        .field select {
            display: block;
            width: 160px;
            max-width: 100%;
            min-width: 0;
            height: 38px;
            border: 1px solid #e2e4e9;
            border-radius: 9px;
            padding: 0 11px;
            background: #fff;
            color: #111827;
            outline: none;
            font-size: 13px;
            box-sizing: border-box;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .field input:focus,
        .field select:focus {
            border-color: #01142f;
            box-shadow: 0 0 0 3px rgba(1, 20, 47, .08);
        }

        /* ========================================
           BUTTONS
        ======================================== */

        .btn {
            height: 40px;
            border: 0;
            border-radius: 9px;
            padding: 0 16px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 650;
            transition: background .15s ease, box-shadow .15s ease, transform .05s ease;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-primary {
            background: #01142f;
            color: #fff;
            box-shadow: 0 1px 2px rgba(1, 20, 47, .18);
        }

        .btn-primary:hover {
            background: #0a1f42;
            box-shadow: 0 4px 10px rgba(1, 20, 47, .2);
        }

        .btn-danger {
            background: #fff1f2;
            color: #dc2626;
        }

        .btn-danger:hover {
            background: #fee2e2;
        }

        .btn-outline {
            background: #fff;
            color: #01142f;
            border: 1px solid #e2e4e9;
        }

        .btn-outline:hover {
            border-color: #01142f;
            background: #f8fafc;
        }

        /* ========================================
           КНОПКА ПРИМЕНИТЬ ФИЛЬТР
        ======================================== */

        .btn-filter-apply {
            background: #dbeafe;
            color: #1d4ed8;
            height: 38px;
        }

        .btn-filter-apply:hover {
            background: #bfdbfe;
            color: #1e40af;
        }

        /* ========================================
           ФИЛЬТР ПОД ТАБЛИЦЕЙ
        ======================================== */

        .payments-filter {
            margin-top: 18px;
        }

        /* ========================================
           SECTIONS
        ======================================== */

        .section {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            background: #fff;
            border: 1px solid #eceef1;
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 16px;
            box-sizing: border-box;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(17, 24, 39, .03);
        }

        .section h2 {
            margin: 0 0 18px;
            font-size: 16.5px;
            font-weight: 700;
            letter-spacing: -.2px;
        }

        /* ========================================
           FORMS
        ======================================== */

        .form-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 14px;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        .form-grid>div {
            width: 100%;
            min-width: 0;
            max-width: 100%;
            box-sizing: border-box;
        }

        .form-full {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #8a919c;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .form-control {
            display: block;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            height: 42px;
            border: 1px solid #e2e4e9;
            border-radius: 9px;
            padding: 0 12px;
            background: #fff;
            color: #111827;
            box-sizing: border-box;
            outline: none;
            font-size: 13.5px;
            font-family: inherit;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .form-control:focus {
            border-color: #01142f;
            box-shadow: 0 0 0 3px rgba(1, 20, 47, .08);
        }

        select.form-control {
            cursor: pointer;
        }

        textarea.form-control {
            height: 78px;
            padding-top: 10px;
            resize: vertical;
            font-family: inherit;
        }

        /* ПОЛЕ + КНОПКА "ДОБАВИТЬ ВИД РАСХОДА" РЯДОМ */

        .field-with-action {
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        .field-with-action>div {
            flex: 1;
            min-width: 0;
        }

        .field-action-btn {
            flex: 0 0 auto;
            height: 42px;
            padding: 0 13px;
            white-space: nowrap;
        }

        /* ========================================
           DATE INPUT
        ======================================== */

        input[type="date"] {
            -webkit-appearance: none;
            appearance: none;
            display: block;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            color: #111827;
            background: #fff;
        }

        input[type="date"]::-webkit-date-and-time-value {
            text-align: left;
            margin: 0;
            padding: 0;
        }

        input[type="date"]::-webkit-datetime-edit {
            padding: 0;
        }

        input[type="date"]::-webkit-datetime-edit-fields-wrapper {
            padding: 0;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            margin-left: auto;
            padding: 0;
            width: 16px;
            height: 16px;
            opacity: .6;
            cursor: pointer;
        }

        input[type="date"]::-webkit-inner-spin-button {
            display: none;
        }

        .field input[type="date"] {
            height: 38px;
            line-height: 36px;
            padding: 0 11px;
        }

        .field select {
            cursor: pointer;
        }

        input[type="date"].form-control {
            height: 42px;
            line-height: 40px;
            padding: 0 12px;
        }

        /* ========================================
           TABLE
        ======================================== */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: 1px solid #eef0f2;
            border-radius: 12px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        .payment-table th {
            text-align: left;
            color: #9aa0a9;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: 12px 14px;
            background: #fafbfc;
            border-bottom: 1px solid #eef0f2;
            position: sticky;
            top: 0;
        }

        .payment-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f2f3f5;
            color: #222;
            font-size: 13px;
            vertical-align: middle;
        }

        .payment-table tbody tr {
            transition: background .12s ease;
        }

        .payment-table tbody tr:hover {
            background: #fafbfd;
        }

        .payment-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .payment-amount {
            font-weight: 700;
            white-space: nowrap;
            color: #01142f;
        }

        .payment-point {
            white-space: nowrap;
        }

        .delete-form {
            margin: 0;
        }

        .delete-form .btn {
            height: 30px;
            padding: 0 10px;
            font-size: 11px;
        }

        /* ========================================
           EXPENSE TYPES
        ======================================== */

        .expense-type-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .expense-type-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid #f0f1f3;
            border-radius: 10px;
            min-height: 40px;
            transition: border-color .15s ease, background .15s ease;
        }

        .expense-type-item:hover {
            border-color: #e2e4e9;
            background: #fafbfd;
        }

        .expense-type-left {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .expense-type-name {
            font-size: 13px;
            font-weight: 600;
        }

        .expense-type-count {
            color: #a0a6af;
            font-size: 11px;
            background: #f4f5f8;
            padding: 2px 8px;
            border-radius: 999px;
        }

        .expense-type-item .btn {
            height: 30px;
            padding: 0 10px;
            font-size: 11px;
        }

        /* ========================================
           ALERTS
        ======================================== */

        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 13px;
            border: 1px solid transparent;
        }

        .alert-success {
            background: #ecfdf3;
            color: #166534;
            border-color: #d1fadf;
        }

        .errors {
            background: #fff1f2;
            color: #991b1b;
            border: 1px solid #fecdd3;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .empty {
            padding: 30px 10px;
            text-align: center;
            color: #a0a6af;
            font-size: 13px;
        }

        /* ========================================
           MODAL
        ======================================== */

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15, 20, 28, .5);
            padding: 20px;
            overflow-y: auto;
            box-sizing: border-box;
            backdrop-filter: blur(2px);
        }

        .modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-box {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, .22);
            overflow: hidden;
            animation: modalPop .15s ease-out;
        }

        @keyframes modalPop {

            from {
                opacity: 0;
                transform: translateY(-8px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid #eceef1;
        }

        .modal-head h3 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
        }

        .modal-close {
            border: none;
            background: transparent;
            font-size: 22px;
            line-height: 1;
            color: #9aa0a9;
            cursor: pointer;
            padding: 0 4px;
            border-radius: 6px;
            transition: color .15s ease, background .15s ease;
        }

        .modal-close:hover {
            color: #111827;
            background: #f4f5f8;
        }

        .modal-content-body {
            padding: 20px;
        }

        /* =========================
   МОБИЛЬНАЯ ВЕРСИЯ
   ========================= */

        @media (max-width: 800px) {

            .payments-page {
                width: 100%;
                max-width: 100%;
                min-width: 0;
                padding: 12px;
                box-sizing: border-box;
                overflow-x: hidden;
            }

            .payments-navigation {
                width: 100%;
                margin-bottom: 14px;
            }

            .payments-menu-back-btn {
                height: 36px;
                padding: 0 13px;
                font-size: 12px;
            }

            .payments-title h1 {
                font-size: 22px;
            }

            .payments-title p {
                font-size: 12px;
                line-height: 1.4;
            }

            .payments-header {
                margin-bottom: 18px;
            }


            /* =========================
       ЗАГОЛОВКИ СЕКЦИЙ
       ========================= */

            .section-head-row {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
                margin-bottom: 12px;
            }

            .section-head-row h2 {
                width: 100%;
            }


            /* =========================
       ФИЛЬТР ПЛАТЕЖЕЙ
       ========================= */

            .payments-filter {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                align-items: end !important;
                gap: 8px !important;

                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;

                padding: 10px !important;
                margin: 0 !important;

                box-sizing: border-box !important;

                overflow: hidden !important;
            }

            .payments-filter .field {
                display: flex !important;
                flex-direction: column !important;

                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;

                box-sizing: border-box !important;
            }

            .payments-filter .field label {
                display: block !important;

                width: 100% !important;

                margin: 0 0 4px !important;
                padding: 0 3px !important;

                font-size: 11px !important;
            }

            /* Дата "С" */
            .payments-filter .field:nth-child(1) {
                grid-column: 1 !important;
                width: 100% !important;
            }

            /* Дата "По" */
            .payments-filter .field:nth-child(2) {
                grid-column: 2 !important;
                width: 100% !important;
            }

            /* Точка продаж — вся ширина */
            .payments-filter .field:nth-child(3) {
                grid-column: 1 / -1 !important;

                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
            }

            .payments-filter .field input,
            .payments-filter .field input[type="date"],
            .payments-filter .field select {
                display: block !important;

                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;

                height: 40px !important;

                margin: 0 !important;

                padding: 0 10px !important;

                box-sizing: border-box !important;

                font-size: 13px !important;
                line-height: 38px !important;
            }

            /* Кнопка "Применить" — вся ширина */
            .payments-filter .btn-filter-apply {
                grid-column: 1 / -1 !important;

                display: block !important;

                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;

                height: 40px !important;

                margin: 0 !important;

                box-sizing: border-box !important;

                background: #dbeafe !important;
                color: #1d4ed8 !important;
                border: 1px solid #bfdbfe !important;
            }

            .payments-filter .btn-filter-apply:hover {
                background: #bfdbfe !important;
                color: #1e40af !important;
            }


            /* =========================
       СЕКЦИИ
       ========================= */

            .section {
                width: 100%;
                max-width: 100%;
                min-width: 0;

                padding: 14px;

                box-sizing: border-box;

                overflow: hidden;
            }

            .section h2 {
                margin-bottom: 14px;
                font-size: 15px;
            }


            /* =========================
       ФОРМА ДОБАВЛЕНИЯ ПЛАТЕЖА
       ========================= */

            .form-grid {
                display: grid !important;

                grid-template-columns: minmax(0, 1fr) !important;

                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;

                gap: 12px !important;

                box-sizing: border-box !important;
            }

            .form-grid>div {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;

                box-sizing: border-box !important;
            }

            .form-full {
                grid-column: auto !important;
            }

            .form-label {
                display: block;

                margin-bottom: 5px;

                font-size: 11px;
            }

            .form-control,
            input.form-control,
            input[type="number"].form-control,
            input[type="text"].form-control,
            input[type="date"].form-control,
            select.form-control,
            textarea.form-control {
                display: block !important;

                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;

                box-sizing: border-box !important;

                font-size: 13px !important;
            }

            input[type="date"].form-control {
                height: 42px !important;
                line-height: 40px !important;

                padding: 0 12px !important;

                margin: 0 !important;
            }

            input[type="date"].form-control::-webkit-date-and-time-value {
                height: auto;
                min-height: 0;

                padding: 0;
                margin: 0;
            }

            input[type="date"].form-control::-webkit-datetime-edit {
                padding: 0;
                margin: 0;
            }

            input[type="date"].form-control::-webkit-datetime-edit-fields-wrapper {
                padding: 0;
                margin: 0;
            }

            textarea.form-control {
                width: 100% !important;
                max-width: 100% !important;

                font-size: 13px !important;
            }


            /* =========================
       ПОЛЕ С КНОПКОЙ
       ========================= */

            .field-with-action {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .field-with-action>div {
                width: 100%;
                min-width: 0;
            }

            .field-action-btn {
                width: 100% !important;
                height: 42px;

                padding: 0 12px;
            }

            .form-grid .btn {
                display: block;

                width: 100% !important;
                max-width: 100% !important;

                box-sizing: border-box;
            }


            /* =========================
       ТАБЛИЦА
       ========================= */

            .table-wrapper {
                width: calc(100% + 28px);
                max-width: none;

                margin-left: -14px;
                margin-right: -14px;

                padding-left: 14px;
                padding-right: 14px;

                border-radius: 0;
                border-left: 0;
                border-right: 0;

                box-sizing: border-box;

                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .payment-table {
                min-width: 750px;
            }

            .payment-table th,
            .payment-table td {
                padding-left: 6px;
                padding-right: 6px;
            }


            /* =========================
       УВЕДОМЛЕНИЯ
       ========================= */

            .alert,
            .errors {
                padding: 11px 12px;

                font-size: 12px;

                margin-bottom: 12px;
            }


            /* =========================
       ПУСТОЕ СОСТОЯНИЕ
       ========================= */

            .empty {
                padding: 24px 8px;

                font-size: 12px;
            }


            /* =========================
       MODAL
       ========================= */

            .modal-overlay {
                padding: 12px;
            }

            .modal-box {
                width: 100%;
                max-width: 420px;
            }

            .modal-head {
                padding: 15px 16px;
            }

            .modal-content-body {
                padding: 16px;
            }


            /* =========================
       ЗАПРЕТ ГОРИЗОНТАЛЬНОГО
       ПЕРЕПОЛНЕНИЯ
       ========================= */

            input,
            select,
            textarea,
            button {
                -webkit-text-size-adjust: 100%;
                text-size-adjust: 100%;
            }

            .payments-filter {
                margin-top: 18px !important;
            }
        }


        /* =========================
   ОЧЕНЬ МАЛЕНЬКИЕ ЭКРАНЫ
   ========================= */

        @media (max-width: 480px) {

            .payments-filter {
                margin-top: 18px !important;
            }

            .payments-page {
                padding: 9px;
            }

            .section {
                padding: 13px;
            }

            .payments-title h1 {
                font-size: 21px;
            }

            .payments-menu-back-btn {
                height: 34px;

                padding: 0 12px;

                font-size: 11px;
            }


            /* Фильтр */
            .payments-filter {
                grid-template-columns: 1fr 1fr !important;

                gap: 7px !important;

                padding: 8px !important;
            }

            .payments-filter .field label {
                font-size: 10px !important;

                margin-bottom: 3px !important;
            }

            .payments-filter .field input[type="date"],
            .payments-filter .field select {
                height: 38px !important;

                padding: 0 9px !important;

                font-size: 12px !important;
            }

            .payments-filter .btn-filter-apply {
                height: 38px !important;

                font-size: 12px !important;
            }


            /* Таблица */
            .payment-table {
                min-width: 0;

                width: max-content;

                table-layout: auto;
            }

            .payment-table th,
            .payment-table td {
                padding: 9px 18px;

                font-size: 12px;

                white-space: nowrap;
            }

            .payment-table th:first-child,
            .payment-table td:first-child,
            .payment-table th:nth-child(2),
            .payment-table td:nth-child(2),
            .payment-table th:nth-child(3),
            .payment-table td:nth-child(3),
            .payment-table th:nth-child(4),
            .payment-table td:nth-child(4),
            .payment-table th:nth-child(5),
            .payment-table td:nth-child(5),
            .payment-table th:nth-child(6),
            .payment-table td:nth-child(6) {
                width: auto;
            }

            .table-wrapper {
                overflow-x: auto;

                -webkit-overflow-scrolling: touch;
            }


            /* Даты */
            input[type="date"].form-control {
                padding: 0 9px !important;

                font-size: 12px !important;
            }

            .filter-box .field input[type="date"] {
                padding: 0 9px !important;

                font-size: 12px !important;
            }


            /* Виды расходов */
            .expense-type-name {
                font-size: 12px;
            }


            /* Modal */
            .modal-box {
                max-width: 100%;
            }
        }
    </style>

    <div class="payments-page">

        {{-- ========================================
             НАВИГАЦИЯ
        ======================================== --}}

        <div class="payments-navigation">

            <a
                href="{{ route('admin.analytics.menu') }}"
                class="payments-menu-back-btn">

                ← В меню аналитики

            </a>

        </div>


        {{-- ========================================
             HEADER
        ======================================== --}}

        <div class="payments-header">

            <div class="payments-title">

                <h1>
                    Платежи
                </h1>

                <p>
                    Управление исходящими платежами и видами расходов
                </p>

            </div>

        </div>


        {{-- ========================================
             СООБЩЕНИЯ
        ======================================== --}}

        @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

        @endif


        @if($errors->any())

        <div class="errors">

            @foreach($errors->all() as $error)

            <div>
                {{ $error }}
            </div>

            @endforeach

        </div>

        @endif


        {{-- ========================================
             ДОБАВИТЬ ИСХОДЯЩИЙ ПЛАТЁЖ
        ======================================== --}}

        <div class="section">

            <h2>
                Добавить исходящий платёж
            </h2>


            <form
                method="POST"
                action="{{ route('admin.analytics.finance.outgoing-payments.store') }}">

                @csrf

                <input
                    type="hidden"
                    name="from"
                    value="{{ $from->format('Y-m-d') }}">

                <input
                    type="hidden"
                    name="to"
                    value="{{ $to->format('Y-m-d') }}">

                <input
                    type="hidden"
                    name="point_of_sale_id"
                    value="{{ $selectedPointOfSale ?? '' }}">


                <div class="form-grid">

                    {{-- ДАТА --}}

                    <div>

                        <label class="form-label">
                            Дата
                        </label>

                        <input
                            type="date"
                            name="payment_date"
                            class="form-control"
                            value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                            required>

                    </div>


                    {{-- СУММА --}}

                    <div>

                        <label class="form-label">
                            Сумма
                        </label>

                        <input
                            type="number"
                            name="amount"
                            class="form-control"
                            step="0.01"
                            min="0.01"
                            placeholder="150000"
                            value="{{ old('amount') }}"
                            required>

                    </div>


                    {{-- ТОЧКА ПРОДАЖ --}}

                    <div>

                        <label class="form-label">
                            Точка продаж
                        </label>

                        <select
                            name="point_of_sale_id"
                            class="form-control">

                            <option value="">
                                Общие расходы
                            </option>

                            @foreach($pointsOfSale as $point)

                            <option
                                value="{{ $point->id }}"
                                @selected(old('point_of_sale_id', $selectedPointOfSale)==$point->id)>

                                {{ $point->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- ВИД РАСХОДА + КНОПКА ДОБАВИТЬ НОВЫЙ --}}

                    <div>

                        <label class="form-label">
                            Вид расхода
                        </label>

                        <div class="field-with-action">

                            <div>

                                <select
                                    name="expense_type_id"
                                    id="expenseTypeSelect"
                                    class="form-control"
                                    required>

                                    <option value="">
                                        Выберите вид расхода
                                    </option>

                                    @foreach($expenseTypes as $type)

                                    <option
                                        value="{{ $type->id }}"
                                        @selected(old('expense_type_id')==$type->id)>

                                        {{ $type->name }}

                                    </option>

                                    @endforeach

                                </select>

                            </div>


                            <button
                                type="button"
                                class="btn btn-outline field-action-btn"
                                onclick="openExpenseTypeModal()">

                                + Новый вид расхода

                            </button>

                        </div>

                    </div>


                    {{-- ОПИСАНИЕ --}}

                    <div class="form-full">

                        <label class="form-label">
                            Описание
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            placeholder="Например: Аренда магазина за сентябрь">{{ old('description') }}</textarea>

                    </div>


                    {{-- КНОПКА --}}

                    <div class="form-full">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Добавить платёж

                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- ========================================
             ИСХОДЯЩИЕ ПЛАТЕЖИ
        ======================================== --}}

        <div class="section">

            <h2>
                Исходящие платежи
            </h2>


            {{-- ТАБЛИЦА --}}

            <div class="table-wrapper">

                <table class="payment-table">

                    <thead>

                        <tr>

                            <th>
                                Дата
                            </th>

                            <th>
                                Точка продаж
                            </th>

                            <th>
                                Вид расхода
                            </th>

                            <th>
                                Сумма
                            </th>

                            <th>
                                Описание
                            </th>

                            <th>
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($outgoingPayments as $payment)

                        <tr>

                            <td>
                                {{ $payment->payment_date->format('d.m.Y') }}
                            </td>

                            <td class="payment-point">
                                {{ $payment->pointOfSale->name ?? 'Общие расходы' }}
                            </td>

                            <td>
                                {{ $payment->expenseType->name }}
                            </td>

                            <td class="payment-amount">

                                {{ number_format(
                                        $payment->amount,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                ₸

                            </td>

                            <td>
                                {{ $payment->description ?: '—' }}
                            </td>

                            <td>

                                <form
                                    method="POST"
                                    class="delete-form"
                                    action="{{ route(
                                            'admin.analytics.finance.outgoing-payments.destroy',
                                            $payment
                                        ) }}">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Удалить этот платёж?')">

                                        Удалить

                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="6">

                                <div class="empty">
                                    За выбранный период платежей нет.
                                </div>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ========================================
                 ФИЛЬТР ПОД ТАБЛИЦЕЙ
            ======================================== --}}

            <form
                method="GET"
                class="filter-box payments-filter">

                {{-- С --}}

                <div class="field">

                    <label>
                        С
                    </label>

                    <input
                        type="date"
                        name="from"
                        value="{{ $from->format('Y-m-d') }}">

                </div>


                {{-- ПО --}}

                <div class="field">

                    <label>
                        По
                    </label>

                    <input
                        type="date"
                        name="to"
                        value="{{ $to->format('Y-m-d') }}">

                </div>


                {{-- ТОЧКА ПРОДАЖ --}}

                <div class="field">

                    <label>
                        Точка продаж
                    </label>

                    <select name="point_of_sale_id">

                        <option value="">
                            Все точки
                        </option>

                        @foreach($pointsOfSale as $point)

                        <option
                            value="{{ $point->id }}"
                            @selected($selectedPointOfSale==$point->id)>

                            {{ $point->name }}

                        </option>

                        @endforeach

                    </select>

                </div>


                {{-- ПРИМЕНИТЬ --}}

                <button
                    type="submit"
                    class="btn btn-filter-apply">

                    Применить

                </button>

            </form>

        </div>

    </div>


    {{-- ========================================
         MODAL: ДОБАВИТЬ ВИД РАСХОДА
    ======================================== --}}

    <div
        id="expenseTypeModal"
        class="modal-overlay"
        onclick="closeExpenseTypeModal(event)">


        <div
            class="modal-box"
            onclick="event.stopPropagation()">


            <div class="modal-head">

                <h3>
                    Добавить вид расхода
                </h3>

                <button
                    type="button"
                    class="modal-close"
                    onclick="closeExpenseTypeModal()">

                    &times;

                </button>

            </div>


            <div class="modal-content-body">

                <form
                    method="POST"
                    action="{{ route('admin.analytics.finance.expense-types.store') }}">

                    @csrf


                    <div class="form-grid">

                        <div class="form-full">

                            <label class="form-label">
                                Название
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                placeholder="Например: Аренда"
                                value="{{ old('name') }}"
                                required
                                autofocus>

                        </div>


                        <div class="form-full">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                Добавить вид расхода

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <script>
        function openExpenseTypeModal() {

            const modal = document.getElementById('expenseTypeModal');

            if (!modal) return;

            modal.classList.add('active');

            document.body.style.overflow = 'hidden';

        }


        function closeExpenseTypeModal(event) {

            const modal = document.getElementById('expenseTypeModal');

            if (!modal) return;

            if (event && event.target !== modal) return;

            modal.classList.remove('active');

            document.body.style.overflow = '';

        }


        document.addEventListener('keydown', function(event) {

            if (event.key !== 'Escape') return;

            const modal = document.getElementById('expenseTypeModal');

            if (modal && modal.classList.contains('active')) {

                modal.classList.remove('active');

                document.body.style.overflow = '';

            }

        });
    </script>

</body>

</html>