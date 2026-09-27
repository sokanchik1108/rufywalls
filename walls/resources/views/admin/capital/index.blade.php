<!DOCTYPE html>

<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1,
        maximum-scale=1, user-scalable=no">

    <title>Капитал</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --navy: #01142f;
            --navy2: #02214b;
            --text: #172033;
            --muted: #7b8494;
            --border: #e7eaf0;
            --bg: #f6f7f9;
            --green: #087f5b;
            --red: #c62828;
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
        }

        body.modal-open {
            overflow: hidden;
        }

        button,
        input,
        textarea {
            font: inherit;
        }

        .capital-page {
            width: min(1180px, calc(100% - 28px));
            margin: 0 auto;
            padding: 18px 0 35px;
        }

        /* =========================
           HEADER
           ========================= */

        .capital-header {
            margin-bottom: 15px;
        }

        .capital-header h1 {
            margin: 0 0 4px;
            color: var(--navy);
            font-size: 25px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -.3px;
        }

        .capital-header p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.4;
        }

        /* =========================
           ALERTS
           ========================= */

        .capital-alert {
            margin-bottom: 10px;
            padding: 10px 13px;
            border-radius: 8px;
            font-size: 12px;
            line-height: 1.4;
        }

        .capital-alert-success {
            background: #e9f8f0;
            color: #146c3a;
        }

        .capital-alert-error {
            background: #fdeeee;
            color: #a32121;
        }

        .capital-errors {
            margin: 0;
            padding-left: 18px;
        }

        .capital-errors li+li {
            margin-top: 3px;
        }

        /* =========================
           TOTAL CAPITAL
           ========================= */

        .capital-total-card {
            margin-bottom: 10px;
            padding: 17px 19px;
            background: var(--navy);
            border-radius: 11px;
            color: #fff;
        }

        .capital-total-title {
            margin-bottom: 5px;
            color: rgba(255, 255, 255, .68);
            font-size: 11px;
            line-height: 1.3;
        }

        .capital-total-value {
            color: #fff;
            font-size: 29px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .capital-total-value span {
            margin-left: 2px;
            font-size: 15px;
            font-weight: 500;
        }

        .capital-formula {
            margin-top: 8px;
            color: rgba(255, 255, 255, .58);
            font-size: 11px;
            line-height: 1.5;
        }

        /* =========================
           SUMMARY CARDS
           ========================= */

        .capital-cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 10px;
        }

        .capital-card {
            min-width: 0;
            padding: 13px 14px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .capital-card-title {
            margin-bottom: 5px;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.3;
        }

        .capital-card-value {
            color: var(--navy);
            font-size: 18px;
            line-height: 1.2;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .capital-card-value span {
            margin-left: 2px;
            font-size: 12px;
            font-weight: 500;
        }

        /* =========================
           CLICKABLE PERSONAL EXPENSE CARD
           ========================= */

        .capital-card-clickable {
            position: relative;
            cursor: pointer;
            user-select: none;
            transition:
                border-color .15s ease,
                background .15s ease,
                transform .15s ease;
        }

        .capital-card-clickable:hover {
            border-color: #cbd2dc;
            background: #fcfdfe;
        }

        .capital-card-clickable:active {
            transform: scale(.99);
        }

        .capital-card-clickable:focus {
            outline: none;
            border-color: #466180;
            box-shadow: 0 0 0 2px rgba(2, 33, 75, .07);
        }

        .capital-card-open {
            float: right;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 21px;
            height: 21px;
            border-radius: 6px;
            background: #f3f5f7;
            color: #687180;
            font-size: 13px;
            line-height: 1;
        }

        .capital-card-hint {
            margin-top: 5px;
            color: #9aa1ad;
            font-size: 9px;
            line-height: 1.2;
        }

        /* =========================
           MAIN GRID
           ========================= */

        .capital-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(300px, .8fr);
            gap: 10px;
            align-items: start;
        }

        .capital-panel {
            min-width: 0;
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .capital-panel-header {
            padding: 13px 15px;
            border-bottom: 1px solid #edf0f3;
        }

        .capital-panel-header h2 {
            margin: 0;
            color: var(--navy);
            font-size: 15px;
            line-height: 1.25;
            font-weight: 750;
        }

        .capital-panel-header p {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.4;
        }

        .capital-panel-body {
            padding: 14px 15px;
        }

        /* =========================
           EXPENSE HISTORY
           ========================= */

        .expense-list {
            display: flex;
            flex-direction: column;
        }

        .expense-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 14px;
            border-bottom: 1px solid #edf0f2;
        }

        .expense-item:last-child {
            border-bottom: 0;
        }

        .expense-info {
            min-width: 0;
            flex: 1;
        }

        .expense-date {
            margin-bottom: 3px;
            color: var(--muted);
            font-size: 10px;
        }

        .expense-comment {
            color: var(--text);
            font-size: 12px;
            line-height: 1.35;
            word-break: break-word;
        }

        .expense-comment.empty {
            color: #9aa1ad;
        }

        .expense-right {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-shrink: 0;
        }

        .expense-amount {
            color: var(--navy);
            font-size: 12px;
            font-weight: 750;
            white-space: nowrap;
        }

        /* =========================
           DELETE BUTTON
           ========================= */

        .expense-delete {
            position: relative;
            display: block;
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            padding: 0;
            margin: 0;
            border: 0;
            border-radius: 7px;
            background: #f4f5f7;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            transition:
                background .15s ease,
                transform .15s ease;
        }

        .expense-delete::before,
        .expense-delete::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 12px;
            height: 2px;
            border-radius: 2px;
            background: #7f8794;
            transform-origin: center center;
        }

        .expense-delete::before {
            transform: translate(-50%, -50%) rotate(45deg);
        }

        .expense-delete::after {
            transform: translate(-50%, -50%) rotate(-45deg);
        }

        .expense-delete:hover {
            background: #fdeeee;
        }

        .expense-delete:hover::before,
        .expense-delete:hover::after {
            background: var(--red);
        }

        .expense-delete:active {
            transform: scale(.94);
        }

        .expense-empty {
            padding: 27px 14px;
            color: #8a919d;
            font-size: 12px;
            text-align: center;
        }

        /* =========================
           MANAGEMENT
           ========================= */

        .management-section {
            border-bottom: 1px solid #edf0f3;
        }

        .management-section:last-child {
            border-bottom: 0;
        }

        .management-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            padding: 12px 0;
            border: 0;
            background: transparent;
            color: var(--navy);
            text-align: left;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
        }

        .management-toggle-content {
            min-width: 0;
        }

        .management-toggle-title {
            display: block;
            color: var(--navy);
            font-size: 14px;
            line-height: 1.3;
            font-weight: 750;
        }

        .management-toggle-subtitle {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.35;
        }

        .management-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 27px;
            height: 27px;
            flex: 0 0 27px;
            border-radius: 7px;
            background: #f4f5f7;
            color: #687180;
            font-size: 15px;
            line-height: 1;
            transition:
                transform .2s ease,
                background .2s ease;
        }

        .management-toggle:hover .management-arrow {
            background: #edf0f4;
        }

        .management-section.open .management-arrow {
            transform: rotate(180deg);
        }

        .management-content {
            display: none;
            padding: 0 0 14px;
        }

        .management-section.open .management-content {
            display: block;
        }

        /* =========================
           FORMS
           ========================= */

        .capital-form-group {
            margin-bottom: 10px;
        }

        .capital-form-group:last-child {
            margin-bottom: 0;
        }

        .capital-label {
            display: block;
            margin-bottom: 5px;
            color: #4c5668;
            font-size: 11px;
            font-weight: 650;
        }

        .capital-input,
        .capital-textarea {
            width: 100%;
            border: 1px solid #dce0e6;
            border-radius: 7px;
            outline: none;
            background: #fff;
            color: var(--text);
            font-size: 13px;
            transition:
                border-color .15s,
                box-shadow .15s;
        }

        .capital-input {
            height: 38px;
            padding: 7px 10px;
        }

        .capital-textarea {
            min-height: 72px;
            padding: 9px 10px;
            resize: vertical;
        }

        .capital-input:focus,
        .capital-textarea:focus {
            border-color: #466180;
            box-shadow: 0 0 0 2px rgba(2, 33, 75, .06);
        }

        .capital-button {
            width: 100%;
            min-height: 38px;
            padding: 0 12px;
            border: 0;
            border-radius: 7px;
            background: var(--navy);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .capital-button:hover {
            background: var(--navy2);
        }

        /* =========================
           INFO
           ========================= */

        .capital-info {
            margin-top: 13px;
            padding: 10px 11px;
            border-radius: 7px;
            background: #f5f7f9;
            color: #697386;
            font-size: 11px;
            line-height: 1.45;
        }

        .capital-info strong {
            color: #465064;
        }

        /* =========================
           MODAL
           ========================= */

        .capital-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        .capital-modal.active {
            display: flex;
        }

        .capital-modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(1, 20, 47, .48);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }

        /*
         * Большая модалка на всю доступную область.
         * Таблица внутри получает свой скролл по вертикали.
         */
        .capital-modal-window {
            position: relative;
            z-index: 1;

            width: min(1100px, 100%);
            height: min(900px, calc(100vh - 24px));
            max-height: calc(100vh - 24px);

            display: flex;
            flex-direction: column;

            overflow: hidden;

            background: #fff;
            border-radius: 12px;
            box-shadow: 0 18px 50px rgba(0, 0, 0, .18);
        }

        .capital-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;

            flex: 0 0 auto;

            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
        }

        .capital-modal-header h2 {
            margin: 0;
            color: var(--navy);
            font-size: 18px;
            line-height: 1.25;
            font-weight: 800;
        }

        .capital-modal-header p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.4;
        }

        /* =========================
           MODAL CLOSE
           ========================= */

        .capital-modal-close {
            position: relative;

            display: flex;
            align-items: center;
            justify-content: center;

            width: 32px;
            height: 32px;
            flex: 0 0 32px;

            padding: 0;
            border: 0;
            border-radius: 8px;

            background: #f4f5f7;
            cursor: pointer;
        }

        .capital-modal-close::before,
        .capital-modal-close::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;

            width: 14px;
            height: 2px;

            border-radius: 2px;
            background: #687180;
        }

        .capital-modal-close::before {
            transform: translate(-50%, -50%) rotate(45deg);
        }

        .capital-modal-close::after {
            transform: translate(-50%, -50%) rotate(-45deg);
        }

        .capital-modal-close:hover {
            background: #fdeeee;
        }

        .capital-modal-close:hover::before,
        .capital-modal-close:hover::after {
            background: var(--red);
        }

        /* =========================
           MODAL SUMMARY
           ========================= */

        .capital-modal-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;

            flex: 0 0 auto;

            padding: 10px 15px;
            background: #fafbfc;
            border-bottom: 1px solid var(--border);
        }

        .capital-modal-summary>div {
            min-width: 0;
            padding: 9px 11px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        .capital-modal-summary-label {
            display: block;
            margin-bottom: 3px;
            color: var(--muted);
            font-size: 10px;
        }

        .capital-modal-summary strong {
            display: block;
            color: var(--navy);
            font-size: 15px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* =========================
           EXPENSE TABLE
           ========================= */

        .capital-expenses-table-wrap {
            min-height: 0;
            flex: 1 1 auto;

            overflow-y: auto;
            overflow-x: hidden;

            -webkit-overflow-scrolling: touch;
        }

        .capital-expenses-table {
            width: 100%;
            min-width: 0;

            border-collapse: collapse;
            table-layout: fixed;
        }

        .capital-expenses-table th {
            padding: 9px 12px;

            background: #fafbfc;
            border-bottom: 1px solid var(--border);

            color: var(--muted);
            font-size: 10px;
            font-weight: 650;
            text-align: left;
            white-space: nowrap;

            position: sticky;
            top: 0;
            z-index: 2;
        }

        .capital-expenses-table td {
            padding: 10px 12px;

            border-bottom: 1px solid #edf0f2;

            color: var(--text);
            font-size: 12px;
            line-height: 1.35;
            vertical-align: middle;
        }

        .capital-expenses-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .capital-expenses-table th:nth-child(1),
        .capital-expenses-table td:nth-child(1) {
            width: 95px;
        }

        .capital-expenses-table th:nth-child(3),
        .capital-expenses-table td:nth-child(3) {
            width: 125px;
        }

        .capital-expenses-table th:nth-child(4),
        .capital-expenses-table td:nth-child(4) {
            width: 48px;
            text-align: center;
        }

        .expense-table-date {
            color: var(--muted) !important;
            white-space: nowrap;
        }

        .expense-table-comment {
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .expense-table-empty {
            color: #9aa1ad;
        }

        .expense-table-amount {
            color: var(--navy) !important;
            font-weight: 750;
            white-space: nowrap;
        }

        .expense-table-action form {
            display: flex;
            justify-content: center;
        }

        .capital-modal-empty {
            flex: 1;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px 20px;

            color: #8a919d;
            font-size: 12px;
            text-align: center;
        }

        .capital-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            padding: 8px 11px;
            border: 1px solid var(--border);
            border-radius: 7px;
            background: #fff;
            color: var(--navy);
            text-decoration: none;
            font-size: 12px;
            font-weight: 650;
            transition: background .15s ease, border-color .15s ease;
        }

        .capital-back:hover {
            background: #f5f7f9;
            border-color: #cbd2dc;
        }

        .capital-back-arrow {
            font-size: 15px;
            line-height: 1;
        }

        /* =========================
           TABLET
           ========================= */

        @media (max-width: 950px) {

            .capital-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .capital-grid {
                grid-template-columns: 1fr;
            }
        }

        /* =========================
           MOBILE
           ========================= */

        @media (max-width: 600px) {

            .capital-page {
                width: calc(100% - 16px);
                padding: 11px 0 25px;
            }

            .capital-header {
                margin-bottom: 11px;
            }

            .capital-header h1 {
                font-size: 22px;
            }

            .capital-header p {
                font-size: 11px;
            }

            /* TOTAL */

            .capital-total-card {
                margin-bottom: 8px;
                padding: 15px;
                border-radius: 9px;
            }

            .capital-total-title {
                font-size: 10px;
            }

            .capital-total-value {
                font-size: 25px;
            }

            .capital-total-value span {
                font-size: 13px;
            }

            .capital-formula {
                margin-top: 7px;
                font-size: 10px;
            }

            /* CARDS */

            .capital-cards {
                grid-template-columns: 1fr 1fr;
                gap: 6px;
                margin-bottom: 8px;
            }

            .capital-card {
                padding: 11px;
                border-radius: 8px;
            }

            .capital-card-title {
                margin-bottom: 4px;
                font-size: 9px;
            }

            .capital-card-value {
                font-size: 16px;
            }

            .capital-card-value span {
                font-size: 10px;
            }

            .capital-card-open {
                width: 20px;
                height: 20px;
                font-size: 12px;
            }

            .capital-card-hint {
                font-size: 8px;
            }

            /* PANELS */

            .capital-grid {
                gap: 8px;
            }

            .capital-panel {
                border-radius: 9px;
            }

            .capital-panel-header {
                padding: 12px 13px;
            }

            .capital-panel-header h2 {
                font-size: 14px;
            }

            .capital-panel-header p {
                font-size: 10px;
            }

            .capital-panel-body {
                padding: 12px 13px;
            }

            /* EXPENSES */

            .expense-item {
                align-items: center;
                gap: 7px;
                padding: 11px 12px;
            }

            .expense-date {
                font-size: 9px;
            }

            .expense-comment {
                font-size: 11px;
            }

            .expense-right {
                gap: 6px;
            }

            .expense-amount {
                font-size: 11px;
            }

            .expense-delete {
                width: 28px;
                height: 28px;
                flex-basis: 28px;
            }

            /* MANAGEMENT */

            .management-toggle {
                padding: 13px 0;
            }

            .management-toggle-title {
                font-size: 14px;
            }

            .management-toggle-subtitle {
                font-size: 10px;
            }

            .management-arrow {
                width: 28px;
                height: 28px;
                flex-basis: 28px;
            }

            /* FORMS */

            .capital-label {
                font-size: 10px;
            }

            .capital-input {
                height: 38px;
                font-size: 13px;
            }

            .capital-textarea {
                min-height: 68px;
                font-size: 13px;
            }

            .capital-button {
                min-height: 38px;
                font-size: 11px;
            }

            /* =========================
               FULLSCREEN MODAL MOBILE
               ========================= */

            .capital-modal {
                align-items: stretch;
                justify-content: stretch;
                padding: 0;
            }

            .capital-modal-overlay {
                background: #fff;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }

            .capital-modal-window {
                width: 100%;
                height: 100dvh;
                max-height: 100dvh;

                border-radius: 0;
                box-shadow: none;
            }

            .capital-modal-header {
                padding: 13px 12px;
            }

            .capital-modal-header h2 {
                font-size: 17px;
            }

            .capital-modal-header p {
                font-size: 10px;
            }

            .capital-modal-close {
                width: 30px;
                height: 30px;
                flex-basis: 30px;
            }

            .capital-modal-summary {
                gap: 6px;
                padding: 8px 10px;
            }

            .capital-modal-summary>div {
                padding: 7px 8px;
                border-radius: 7px;
            }

            .capital-modal-summary-label {
                margin-bottom: 2px;
                font-size: 8px;
            }

            .capital-modal-summary strong {
                font-size: 13px;
            }

            /* =========================
               COMPACT MOBILE TABLE
               ========================= */

            .capital-expenses-table {
                width: 100%;
                min-width: 0;
            }

            .capital-expenses-table th {
                padding: 8px 6px;
                font-size: 8px;
            }

            .capital-expenses-table td {
                padding: 9px 6px;
                font-size: 10px;
                line-height: 1.3;
            }

            /*
             * На телефоне:
             * Дата — 69px
             * Комментарий — всё оставшееся
             * Сумма — 92px
             * Удаление — 34px
             */

            .capital-expenses-table th:nth-child(1),
            .capital-expenses-table td:nth-child(1) {
                width: 69px;
            }

            .capital-expenses-table th:nth-child(2),
            .capital-expenses-table td:nth-child(2) {
                width: auto;
            }

            .capital-expenses-table th:nth-child(3),
            .capital-expenses-table td:nth-child(3) {
                width: 92px;
            }

            .capital-expenses-table th:nth-child(4),
            .capital-expenses-table td:nth-child(4) {
                width: 34px;
                padding-left: 2px;
                padding-right: 2px;
            }

            .expense-table-date {
                font-size: 9px !important;
            }

            .expense-table-amount {
                font-size: 9px !important;
                letter-spacing: -.15px;
            }

            .expense-table-action form {
                width: 100%;
            }

            .expense-table-action .expense-delete {
                width: 25px;
                height: 25px;
                flex-basis: 25px;
                border-radius: 6px;
            }

            .expense-table-action .expense-delete::before,
            .expense-table-action .expense-delete::after {
                width: 10px;
                height: 2px;
            }

            .capital-modal-empty {
                padding: 30px 15px;
            }
        }

        /* =========================
           VERY SMALL PHONES
           ========================= */

        @media (max-width: 380px) {

            .capital-page {
                width: calc(100% - 12px);
            }

            .capital-total-value {
                font-size: 23px;
            }

            .capital-card-value {
                font-size: 15px;
            }

            .capital-card-title {
                font-size: 8px;
            }

            .expense-amount {
                font-size: 10px;
            }

            .expense-delete {
                width: 27px;
                height: 27px;
                flex-basis: 27px;
            }

            /*
             * Ещё немного ужимаем таблицу,
             * чтобы она гарантированно помещалась
             * даже на узких экранах.
             */

            .capital-expenses-table th {
                padding-left: 5px;
                padding-right: 5px;
            }

            .capital-expenses-table td {
                padding-left: 5px;
                padding-right: 5px;
            }

            .capital-expenses-table th:nth-child(1),
            .capital-expenses-table td:nth-child(1) {
                width: 65px;
            }

            .capital-expenses-table th:nth-child(3),
            .capital-expenses-table td:nth-child(3) {
                width: 88px;
            }

            .capital-expenses-table th:nth-child(4),
            .capital-expenses-table td:nth-child(4) {
                width: 32px;
            }

            .expense-table-amount {
                font-size: 8.5px !important;
            }

            .expense-table-action .expense-delete {
                width: 23px;
                height: 23px;
                flex-basis: 23px;
            }
        }
    </style>

</head>

<body>

    @php

    /*
    * Итоговая формула:
    *
    * Деньги на руках
    * + Себестоимость товара
    * + Личные расходы
    * - Долги поставщикам
    */

    $totalCapital =
    (float) $cashOnHand
    + (float) $inventoryCost
    + (float) $personalExpenses
    - (float) $supplierDebt;

    @endphp

    <div class="capital-page">

        <a href="{{ route('admin.analytics.menu') }}" class="capital-back">
            <span class="capital-back-arrow">←</span>
            Меню
        </a>

        {{-- HEADER --}}

        <div class="capital-header">

            <h1>Капитал</h1>

            <p>
                Текущее состояние денежных средств, товара и обязательств
            </p>

        </div>


        {{-- SUCCESS --}}

        @if(session('success'))

        <div class="capital-alert capital-alert-success">

            {{ session('success') }}

        </div>

        @endif


        {{-- VALIDATION ERRORS --}}

        @if($errors->any())

        <div class="capital-alert capital-alert-error">

            <ul class="capital-errors">

                @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

                @endforeach

            </ul>

        </div>

        @endif


        {{-- TOTAL CAPITAL --}}

        <div class="capital-total-card">

            <div class="capital-total-title">
                Итоговый капитал
            </div>

            <div class="capital-total-value">

                {{ number_format($totalCapital, 0, ',', ' ') }}

                <span>₸</span>

            </div>

            <div class="capital-formula">

                {{ number_format($cashOnHand, 0, ',', ' ') }}

                +

                {{ number_format($inventoryCost, 0, ',', ' ') }}

                +

                {{ number_format($personalExpenses, 0, ',', ' ') }}

                −

                {{ number_format($supplierDebt, 0, ',', ' ') }}

            </div>

        </div>


        {{-- SUMMARY CARDS --}}

        <div class="capital-cards">

            {{-- CASH --}}

            <div class="capital-card">

                <div class="capital-card-title">
                    Деньги на руках
                </div>

                <div class="capital-card-value">

                    {{ number_format($cashOnHand, 0, ',', ' ') }}

                    <span>₸</span>

                </div>

            </div>


            {{-- INVENTORY --}}

            <div class="capital-card">

                <div class="capital-card-title">
                    Себестоимость товара
                </div>

                <div class="capital-card-value">

                    {{ number_format($inventoryCost, 0, ',', ' ') }}

                    <span>₸</span>

                </div>

            </div>


            {{-- PERSONAL EXPENSES CLICKABLE --}}

            <div
                class="capital-card capital-card-clickable"
                onclick="openPersonalExpensesModal()"
                role="button"
                tabindex="0"
                onkeydown="
                if(event.key === 'Enter' || event.key === ' ')
                    openPersonalExpensesModal()
            ">

                <div class="capital-card-title">

                    Личные расходы

                    <span class="capital-card-open">
                        ↗
                    </span>

                </div>

                <div class="capital-card-value">

                    {{ number_format($personalExpenses, 0, ',', ' ') }}

                    <span>₸</span>

                </div>

                <div class="capital-card-hint">
                    Нажмите для просмотра
                </div>

            </div>


            {{-- SUPPLIER DEBT --}}

            <div class="capital-card">

                <div class="capital-card-title">
                    Долги поставщикам
                </div>

                <div class="capital-card-value">

                    {{ number_format($supplierDebt, 0, ',', ' ') }}

                    <span>₸</span>

                </div>

            </div>

        </div>


        {{-- CONTENT --}}

        <div class="capital-grid">




            {{-- MANAGEMENT --}}

            <div class="capital-panel">

                <div class="capital-panel-header">

                    <h2>
                        Управление
                    </h2>

                    <p>
                        Нажмите на нужный блок, чтобы открыть форму
                    </p>

                </div>


                <div class="capital-panel-body">

                    {{-- CASH --}}

                    <div class="management-section">

                        <button
                            type="button"
                            class="management-toggle"
                            onclick="toggleCapitalSection(this)"
                            aria-expanded="false">

                            <span class="management-toggle-content">

                                <span class="management-toggle-title">
                                    Деньги на руках
                                </span>

                                <span class="management-toggle-subtitle">
                                    Изменить текущую сумму
                                </span>

                            </span>

                            <span class="management-arrow">
                                ↓
                            </span>

                        </button>


                        <div class="management-content">

                            <form
                                action="{{ route('admin.capital.cash.update') }}"
                                method="POST">

                                @csrf

                                <div class="capital-form-group">

                                    <label
                                        for="cash_on_hand"
                                        class="capital-label">
                                        Сумма
                                    </label>

                                    <input
                                        type="number"
                                        id="cash_on_hand"
                                        name="cash_on_hand"
                                        class="capital-input"
                                        value="{{ old('cash_on_hand', $cashOnHand) }}"
                                        min="0"
                                        step="0.01"
                                        required>

                                </div>


                                <button
                                    type="submit"
                                    class="capital-button">
                                    Сохранить деньги на руках
                                </button>

                            </form>

                        </div>

                    </div>


                    {{-- PERSONAL EXPENSE --}}

                    <div class="management-section">

                        <button
                            type="button"
                            class="management-toggle"
                            onclick="toggleCapitalSection(this)"
                            aria-expanded="false">

                            <span class="management-toggle-content">

                                <span class="management-toggle-title">
                                    Добавить личный расход
                                </span>

                                <span class="management-toggle-subtitle">
                                    Сумма, дата и комментарий
                                </span>

                            </span>

                            <span class="management-arrow">
                                ↓
                            </span>

                        </button>


                        <div class="management-content">

                            <form
                                action="{{ route(
                                'admin.capital.personal-expense.store'
                            ) }}"
                                method="POST">

                                @csrf

                                <div class="capital-form-group">

                                    <label
                                        for="amount"
                                        class="capital-label">
                                        Сумма
                                    </label>

                                    <input
                                        type="number"
                                        id="amount"
                                        name="amount"
                                        class="capital-input"
                                        value="{{ old('amount') }}"
                                        min="0.01"
                                        step="0.01"
                                        placeholder="Например, 50000"
                                        required>

                                </div>


                                <div class="capital-form-group">

                                    <label
                                        for="entry_date"
                                        class="capital-label">
                                        Дата
                                    </label>

                                    <input
                                        type="date"
                                        id="entry_date"
                                        name="entry_date"
                                        class="capital-input"
                                        value="{{ old(
                                        'entry_date',
                                        now()->format('Y-m-d')
                                    ) }}"
                                        required>

                                </div>


                                <div class="capital-form-group">

                                    <label
                                        for="comment"
                                        class="capital-label">
                                        Комментарий
                                    </label>

                                    <textarea
                                        id="comment"
                                        name="comment"
                                        class="capital-textarea"
                                        maxlength="1000"
                                        placeholder="Например: личная покупка">{{ old('comment') }}</textarea>

                                </div>


                                <button
                                    type="submit"
                                    class="capital-button">
                                    Добавить расход
                                </button>

                            </form>

                        </div>

                    </div>


                    {{-- INFO --}}

                    <div class="capital-info">

                        <strong>Важно:</strong>

                        личные расходы учитываются отдельно
                        и не относятся к расходам бизнеса.

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================
     PERSONAL EXPENSES MODAL
     ========================= --}}

    <div
        id="personalExpensesModal"
        class="capital-modal"
        aria-hidden="true">

        <div
            class="capital-modal-overlay"
            onclick="closePersonalExpensesModal()"></div>


        <div
            class="capital-modal-window"
            role="dialog"
            aria-modal="true"
            aria-labelledby="personalExpensesModalTitle">

            {{-- MODAL HEADER --}}

            <div class="capital-modal-header">

                <div>

                    <h2 id="personalExpensesModalTitle">
                        Личные расходы
                    </h2>

                    <p>
                        Все расходы владельца / руководителя
                    </p>

                </div>


                <button
                    type="button"
                    class="capital-modal-close"
                    onclick="closePersonalExpensesModal()"
                    aria-label="Закрыть"></button>

            </div>


            {{-- MODAL SUMMARY --}}

            <div class="capital-modal-summary">

                <div>

                    <span class="capital-modal-summary-label">
                        Всего расходов
                    </span>

                    <strong>
                        {{ $personalExpenseEntries->count() }}
                    </strong>

                </div>


                <div>

                    <span class="capital-modal-summary-label">
                        Общая сумма
                    </span>

                    <strong>
                        {{ number_format($personalExpenses, 0, ',', ' ') }} ₸
                    </strong>

                </div>

            </div>


            {{-- MODAL TABLE --}}

            @if($personalExpenseEntries->count())

            <div class="capital-expenses-table-wrap">

                <table class="capital-expenses-table">

                    <thead>

                        <tr>

                            <th>
                                Дата
                            </th>

                            <th>
                                Комментарий
                            </th>

                            <th>
                                Сумма
                            </th>

                            <th></th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($personalExpenseEntries as $entry)

                        <tr>

                            <td class="expense-table-date">

                                {{ $entry->entry_date?->format('d.m.Y') }}

                            </td>


                            <td class="expense-table-comment">

                                @if($entry->comment)

                                {{ $entry->comment }}

                                @else

                                <span class="expense-table-empty">
                                    Без комментария
                                </span>

                                @endif

                            </td>


                            <td class="expense-table-amount">

                                −

                                {{ number_format(
                                        (float) $entry->amount,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                ₸

                            </td>


                            <td class="expense-table-action">

                                <form
                                    action="{{ route(
                                            'admin.capital.personal-expense.destroy',
                                            $entry
                                        ) }}"
                                    method="POST"
                                    onsubmit="return confirm('Удалить этот личный расход?')">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="expense-delete"
                                        title="Удалить"
                                        aria-label="Удалить личный расход"></button>

                                </form>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            @else

            <div class="capital-modal-empty">
                Личных расходов пока нет.
            </div>

            @endif

        </div>

    </div>


    <script>
        /* =========================
       MANAGEMENT TOGGLE
       ========================= */

        function toggleCapitalSection(button) {

            const section = button.closest('.management-section');

            if (!section) {
                return;
            }

            const isOpen = section.classList.toggle('open');

            button.setAttribute(
                'aria-expanded',
                isOpen ? 'true' : 'false'
            );
        }


        /* =========================
           PERSONAL EXPENSE MODAL
           ========================= */

        function openPersonalExpensesModal() {

            const modal = document.getElementById(
                'personalExpensesModal'
            );

            if (!modal) {
                return;
            }

            modal.classList.add('active');

            modal.setAttribute(
                'aria-hidden',
                'false'
            );

            document.body.classList.add('modal-open');
        }


        function closePersonalExpensesModal() {

            const modal = document.getElementById(
                'personalExpensesModal'
            );

            if (!modal) {
                return;
            }

            modal.classList.remove('active');

            modal.setAttribute(
                'aria-hidden',
                'true'
            );

            document.body.classList.remove('modal-open');
        }


        /* Закрытие по Escape */

        document.addEventListener('keydown', function(event) {

            if (event.key === 'Escape') {

                closePersonalExpensesModal();

            }

        });
    </script>

</body>

</html>