<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Аналитика</title>
</head>

<body>

<style>

    /* ========================================
       MAIN
    ======================================== */

    .analytics-menu {
        padding: 25px;
        background: #f6f7f9;
        min-height: 100vh;
        font-family: Arial, Helvetica, sans-serif;
        color: #111827;
    }

    .analytics-menu,
    .analytics-menu a,
    .analytics-menu div,
    .analytics-menu button {
        font-family: Arial, Helvetica, sans-serif;
    }


    /* ========================================
       HEADER
    ======================================== */

    .analytics-header {
        margin-bottom: 25px;
    }

    .analytics-header-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .analytics-header h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
        color: #111827;
        letter-spacing: -.4px;
    }

    .analytics-header p {
        margin: 7px 0 0;
        color: #8a919c;
        font-size: 14px;
    }


    /* ========================================
       HEADER BUTTONS
       МЕНЮ + ВЫЙТИ — ОДИНАКОВЫЙ СТИЛЬ
    ======================================== */

    .header-actions {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-shrink: 0;
    }

    .header-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        height: 38px;
        min-width: 72px;

        padding: 9px 18px;

        border: 1px solid #e5e7eb;
        border-radius: 10px;

        background: #fff;
        color: #374151;

        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px;
        font-weight: 500;

        line-height: 1;
        text-decoration: none !important;

        cursor: pointer;

        box-sizing: border-box;

        transition:
            background .15s ease,
            color .15s ease,
            border-color .15s ease;
    }

    .header-button:hover {
        background: #f3f4f6;
        color: #111827;
        border-color: #d1d5db;
    }

    .header-button.logout-button:hover {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .header-actions form {
        margin: 0;
        padding: 0;
    }


    /* ========================================
       GRID
    ======================================== */

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        max-width: 1100px;
    }


    /* ========================================
       CARD
    ======================================== */

    .analytics-card {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        background: #fff;
        border: 1px solid #eceef1;
        border-radius: 15px;

        padding: 20px;

        text-decoration: none !important;
        color: inherit !important;

        box-sizing: border-box;

        transition:
            transform .15s ease,
            box-shadow .15s ease,
            border-color .15s ease;
    }

    .analytics-card:hover {
        transform: translateY(-2px);
        border-color: #dfe3e8;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .06);
    }


    /* ========================================
       CARD LEFT
    ======================================== */

    .analytics-card-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }


    /* ========================================
       ICON
    ======================================== */

    .analytics-icon {
        width: 45px;
        height: 45px;
        min-width: 45px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 11px;
        background: #f3f4f6;

        font-size: 20px;

        box-sizing: border-box;
    }


    /* ========================================
       TITLE
    ======================================== */

    .analytics-card-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }


    /* ========================================
       DESCRIPTION
    ======================================== */

    .analytics-card-description {
        font-size: 12px;
        line-height: 1.4;
        color: #9aa0a9;
    }


    /* ========================================
       ARROW
    ======================================== */

    .analytics-arrow {
        color: #a0a6af;
        font-size: 20px;
        flex-shrink: 0;
        transition: transform .15s ease;
    }

    .analytics-card:hover .analytics-arrow {
        transform: translateX(3px);
    }


    /* ========================================
       TABLET
    ======================================== */

    @media (max-width: 1000px) {

        .analytics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

    }


    /* ========================================
       MOBILE
    ======================================== */

    @media (max-width: 650px) {

        .analytics-menu {
            padding: 14px;
        }

        .analytics-header {
            margin-bottom: 16px;
        }

        .analytics-header-top {
            align-items: center;
        }

        .analytics-header h1 {
            font-size: 22px;
        }

        .analytics-header p {
            font-size: 13px;
        }

        .header-actions {
            gap: 5px;
        }

        .header-button {
            height: 34px;
            min-width: 62px;
            padding: 8px 12px;
            font-size: 12px;
        }

        .analytics-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .analytics-card {
            padding: 15px;
            border-radius: 12px;
        }

        .analytics-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            font-size: 18px;
        }

        .analytics-card-title {
            font-size: 14px;
        }

        .analytics-card-description {
            font-size: 11px;
        }

    }

</style>


<div class="analytics-menu">


    {{-- ========================================
         HEADER
    ======================================== --}}

    <div class="analytics-header">

        <div class="analytics-header-top">

            <div>

                <h1>
                    Аналитика
                </h1>

                <p>
                    Управление показателями и финансами бизнеса
                </p>

            </div>


            {{-- ========================================
                 КНОПКИ ВЛАДЕЛЬЦА И ВЫХОД
            ======================================== --}}

            <div class="header-actions">

                @if(auth()->user()->is_owner)

                    <a
                        href="/admin"
                        class="header-button">

                        Меню

                    </a>

                @endif


                <form
                    action="{{ route('logout') }}"
                    method="POST">

                    @csrf

                    <button
                        type="submit"
                        class="header-button logout-button">

                        Выйти

                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- ========================================
         CARDS
    ======================================== --}}

    <div class="analytics-grid">


        {{-- ========================================
             ЗАКАЗЫ
        ======================================== --}}

        <a
            href="{{ route('admin.analytics.index') }}"
            class="analytics-card">

            <div class="analytics-card-left">

                <div class="analytics-icon">
                    📊
                </div>

                <div>

                    <div class="analytics-card-title">
                        Аналитика заказов
                    </div>

                    <div class="analytics-card-description">
                        Продажи, возвраты и статистика заказов
                    </div>

                </div>

            </div>

            <div class="analytics-arrow">
                →
            </div>

        </a>


        {{-- ========================================
             ПРИБЫЛЬ
        ======================================== --}}

        <a
            href="{{ route('admin.analytics.profit') }}"
            class="analytics-card">

            <div class="analytics-card-left">

                <div class="analytics-icon">
                    📈
                </div>

                <div>

                    <div class="analytics-card-title">
                        Аналитика прибыли продаж
                    </div>

                    <div class="analytics-card-description">
                        Выручка, себестоимость и прибыль по товарам
                    </div>

                </div>

            </div>

            <div class="analytics-arrow">
                →
            </div>

        </a>


        {{-- ========================================
             ФИНАНСЫ
        ======================================== --}}

        <a
            href="{{ route('admin.analytics.finance') }}"
            class="analytics-card">

            <div class="analytics-card-left">

                <div class="analytics-icon">
                    ₸
                </div>

                <div>

                    <div class="analytics-card-title">
                        Аналитика финансов
                    </div>

                    <div class="analytics-card-description">
                        Выручка, расходы и чистая прибыль
                    </div>

                </div>

            </div>

            <div class="analytics-arrow">
                →
            </div>

        </a>


        {{-- ========================================
             ПЛАТЕЖИ
        ======================================== --}}

        <a
            href="{{ route('admin.analytics.payments') }}"
            class="analytics-card">

            <div class="analytics-card-left">

                <div class="analytics-icon">
                    💳
                </div>

                <div>

                    <div class="analytics-card-title">
                        Платежи
                    </div>

                    <div class="analytics-card-description">
                        Исходящие платежи и виды расходов
                    </div>

                </div>

            </div>

            <div class="analytics-arrow">
                →
            </div>

        </a>


    </div>

</div>


</body>

</html>

