<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1,
        maximum-scale=1, user-scalable=no">

    <title>Долги поставщикам</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --navy: #01142f;
            --navy-2: #02214b;
            --text: #172033;
            --muted: #7b8494;
            --border: #e8ebf0;
            --bg: #f6f7f9;
            --card: #ffffff;
            --soft: #f8f9fb;
            --danger: #c62828;
            --danger-soft: #fff3f3;
            --green: #087f5b;
            --green-soft: #ecfdf5;
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
                Helvetica,
                sans-serif;
        }

        a {
            -webkit-tap-highlight-color: transparent;
        }

        button,
        input {
            font: inherit;
        }

        .page {
            width: min(1080px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 50px;
        }

        /* =========================
           HEADER
        ========================= */

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .header-left {
            min-width: 0;
        }

        .eyebrow {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .title {
            margin: 0;
            color: var(--text);
            font-size: 26px;
            line-height: 1.15;
            font-weight: 750;
            letter-spacing: -0.5px;
        }

        .back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 40px;
            padding: 0 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: #465064;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: .18s ease;
            white-space: nowrap;
        }

        .back:hover {
            border-color: #d6dae2;
            color: var(--navy);
            transform: translateY(-1px);
        }

        /* =========================
           ALERTS
        ========================= */

        .success,
        .error {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.45;
        }

        .success {
            background: var(--green-soft);
            border: 1px solid #b7ecd9;
            color: var(--green);
        }

        .error {
            background: var(--danger-soft);
            border: 1px solid #f5caca;
            color: var(--danger);
        }

        /* =========================
           TOTAL CARD
        ========================= */

        .summary {
            position: relative;
            overflow: hidden;
            background: var(--navy);
            border-radius: 18px;
            padding: 24px 26px;
            margin-bottom: 24px;
            color: #fff;
            box-shadow: 0 8px 30px rgba(1, 20, 47, .12);
        }

        .summary::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            right: -65px;
            top: -75px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .045);
        }

        .summary::before {
            content: "";
            position: absolute;
            width: 110px;
            height: 110px;
            right: 90px;
            bottom: -75px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .035);
        }

        .summary-content {
            position: relative;
            z-index: 1;
        }

        .summary-label {
            color: rgba(255, 255, 255, .62);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .summary-value {
            font-size: 32px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .summary-caption {
            margin-top: 9px;
            color: rgba(255, 255, 255, .52);
            font-size: 11px;
        }

        /* =========================
           SECTION
        ========================= */

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 15px;
            font-weight: 750;
            color: var(--text);
        }

        .section-count {
            color: var(--muted);
            font-size: 12px;
        }

        /* =========================
           SUPPLIERS
        ========================= */

        .suppliers {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .supplier {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 17px;
            transition:
                border-color .18s ease,
                box-shadow .18s ease,
                transform .18s ease;
        }

        .supplier:hover {
            border-color: #dce1e8;
            box-shadow: 0 8px 24px rgba(16, 24, 40, .055);
            transform: translateY(-1px);
        }

        .supplier-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .supplier-info {
            min-width: 0;
        }

        .supplier-name {
            color: var(--text);
            font-size: 15px;
            line-height: 1.3;
            font-weight: 750;
            word-break: break-word;
        }

        .supplier-phone {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .debt {
            flex-shrink: 0;
            text-align: right;
        }

        .debt-label {
            color: var(--muted);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .debt-value {
            color: var(--danger);
            font-size: 17px;
            line-height: 1.15;
            font-weight: 800;
            white-space: nowrap;
        }

        .debt-value.zero {
            color: var(--green);
        }

        /* =========================
           STATS
        ========================= */

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 7px;
            margin-top: 15px;
        }

        .stat {
            min-width: 0;
            padding: 10px;
            background: var(--soft);
            border: 1px solid #f0f2f5;
            border-radius: 9px;
        }

        .stat-label {
            min-height: 25px;
            color: var(--muted);
            font-size: 9px;
            line-height: 1.25;
        }

        .stat-value {
            margin-top: 3px;
            color: #303949;
            font-size: 12px;
            line-height: 1.2;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* =========================
           ACTION
        ========================= */

        .actions {
            margin-top: 13px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 40px;
            border-radius: 9px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            transition: .18s ease;
        }

        .btn-primary {
            background: var(--navy);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--navy-2);
        }

        .btn-light {
            background: #f1f3f6;
            color: #3d4656;
        }

        .btn-light:hover {
            background: #e9ecf1;
        }

        /* =========================
           EMPTY
        ========================= */

        .empty {
            padding: 45px 20px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 15px;
            text-align: center;
        }

        .empty-icon {
            width: 44px;
            height: 44px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f1f3f6;
            color: #697386;
            font-size: 20px;
        }

        .empty-title {
            color: var(--text);
            font-size: 14px;
            font-weight: 700;
        }

        .empty-text {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        /* =========================
           TABLET
        ========================= */

        @media (max-width: 800px) {
            .suppliers {
                grid-template-columns: 1fr;
            }
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 600px) {
            .page {
                width: calc(100% - 20px);
                padding: 14px 0 30px;
            }

            .header {
                align-items: center;
                margin-bottom: 15px;
                gap: 10px;
            }

            .eyebrow {
                font-size: 10px;
                margin-bottom: 3px;
            }

            .title {
                font-size: 20px;
                letter-spacing: -.3px;
            }

            .back {
                min-height: 36px;
                padding: 0 11px;
                border-radius: 9px;
                font-size: 11px;
            }

            .summary {
                padding: 19px;
                margin-bottom: 18px;
                border-radius: 14px;
            }

            .summary-label {
                font-size: 11px;
            }

            .summary-value {
                font-size: 26px;
                letter-spacing: -.7px;
            }

            .summary-caption {
                font-size: 10px;
                margin-top: 7px;
            }

            .section-head {
                margin-bottom: 9px;
            }

            .section-title {
                font-size: 14px;
            }

            .section-count {
                font-size: 11px;
            }

            .suppliers {
                gap: 9px;
            }

            .supplier {
                padding: 13px;
                border-radius: 12px;
            }

            .supplier-top {
                gap: 9px;
            }

            .supplier-name {
                font-size: 14px;
            }

            .supplier-phone {
                font-size: 11px;
                margin-top: 3px;
            }

            .debt-label {
                font-size: 8px;
            }

            .debt-value {
                font-size: 15px;
            }

            .stats {
                gap: 5px;
                margin-top: 12px;
            }

            .stat {
                padding: 8px 7px;
                border-radius: 8px;
            }

            .stat-label {
                min-height: 23px;
                font-size: 10px;
            }

            .stat-value {
                font-size: 12px;
            }

            .actions {
                margin-top: 9px;
            }

            .btn {
                min-height: 38px;
                border-radius: 8px;
                font-size: 11px;
            }

            .empty {
                padding: 35px 15px;
                border-radius: 12px;
            }
        }

        @media (max-width: 380px) {
            .page {
                width: calc(100% - 14px);
            }

            .header {
                gap: 6px;
            }

            .title {
                font-size: 18px;
            }

            .back {
                padding: 0 9px;
                font-size: 10px;
            }

            .summary {
                padding: 16px;
            }

            .summary-value {
                font-size: 23px;
            }

            .supplier {
                padding: 11px;
            }

            .supplier-top {
                gap: 5px;
            }

            .supplier-name {
                font-size: 13px;
            }

            .debt-value {
                font-size: 14px;
            }

            .stat {
                padding: 7px 5px;
            }

            .stat-label {
                font-size: 9px;
            }

            .stat-value {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        {{-- HEADER --}}
        <header class="header">

            <div class="header-left">
                <div class="eyebrow">
                    Финансы
                </div>

                <h1 class="title">
                    Долги поставщикам
                </h1>
            </div>

            <a
                href="{{ route('admin.analytics.menu') }}"
                class="back">
                ← Меню
            </a>

        </header>


        {{-- FLASH SUCCESS --}}
        @if(session('success'))

        <div class="success">
            <span>✓</span>

            <div>
                {{ session('success') }}
            </div>
        </div>

        @endif


        {{-- VALIDATION ERRORS --}}
        @if($errors->any())

        <div class="error">
            <span>!</span>

            <div>
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>

        @endif


        {{-- TOTAL DEBT --}}
        <section class="summary">

            <div class="summary-content">

                <div class="summary-label">
                    Общий долг поставщикам
                </div>

                <div class="summary-value">
                    {{ number_format(
                    $totalDebt,
                    2,
                    ',',
                    ' '
                ) }} ₸
                </div>

                <div class="summary-caption">
                    Текущая сумма обязательств перед поставщиками
                </div>

            </div>

        </section>


        {{-- SUPPLIERS --}}
        @if($supplierData->isEmpty())

        <div class="empty">

            <div class="empty-icon">
                ₽
            </div>

            <div class="empty-title">
                Поставщиков пока нет
            </div>

            <div class="empty-text">
                Добавленные поставщики появятся здесь.
            </div>

        </div>

        @else

        <div class="section-head">

            <div class="section-title">
                Поставщики
            </div>

            <div class="section-count">
                {{ $supplierData->count() }}
                {{ $supplierData->count() == 1 ? 'поставщик' : 'поставщиков' }}
            </div>

        </div>


        <div class="suppliers">

            @foreach($supplierData as $data)

            @php
            $supplier = $data['supplier'];

            $debt = max(
            (float) $data['debt'],
            0
            );
            @endphp


            <article class="supplier">

                <div class="supplier-top">

                    <div class="supplier-info">

                        <div class="supplier-name">
                            {{ $supplier->name }}
                        </div>

                        @if($supplier->phone)

                        <div class="supplier-phone">
                            {{ $supplier->phone }}
                        </div>

                        @endif

                    </div>


                    <div class="debt">

                        <div class="debt-label">
                            Текущий долг
                        </div>

                        <div class="debt-value {{ $debt <= 0 ? 'zero' : '' }}">
                            {{ number_format(
                                    $debt,
                                    2,
                                    ',',
                                    ' '
                                ) }} ₸
                        </div>

                    </div>

                </div>


                <div class="stats">

                    <div class="stat">

                        <div class="stat-label">
                            Приёмки
                        </div>

                        <div class="stat-value">
                            {{ number_format(
                                    $data['receipts_total'],
                                    2,
                                    ',',
                                    ' '
                                ) }} ₸
                        </div>

                    </div>


                    <div class="stat">

                        <div class="stat-label">
                            Оплата при приёмках
                        </div>

                        <div class="stat-value">
                            {{ number_format(
                                    $data['receipt_payments'],
                                    2,
                                    ',',
                                    ' '
                                ) }} ₸
                        </div>

                    </div>


                    <div class="stat">

                        <div class="stat-label">
                            Отдельные оплаты
                        </div>

                        <div class="stat-value">
                            {{ number_format(
                                    $data['manual_payments'],
                                    2,
                                    ',',
                                    ' '
                                ) }} ₸
                        </div>

                    </div>

                </div>


                <div class="actions">

                    @if($debt > 0)

                    <a
                        href="{{ route(
                                    'admin.supplier-debts.show',
                                    $supplier
                                ) }}"
                        class="btn btn-primary">
                        Погасить / история
                    </a>

                    @else

                    <a
                        href="{{ route(
                                    'admin.supplier-debts.show',
                                    $supplier
                                ) }}"
                        class="btn btn-light">
                        История
                    </a>

                    @endif

                </div>

            </article>

            @endforeach

        </div>

        @endif

    </div>

</body>

</html>