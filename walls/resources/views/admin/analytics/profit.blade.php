<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аналитика прибыли</title>
</head>

<body>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --profit-blue: #01142f;
            --profit-blue-hover: #02214b;

            --profit-bg: #f6f7f9;
            --profit-card: #ffffff;

            --profit-text: #111827;
            --profit-muted: #6b7280;

            --profit-border: #e5e7eb;

            --profit-green: #15803d;
            --profit-red: #dc2626;
        }


        .profit-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 28px 20px 50px;

            font-family: 'Inter', sans-serif;
            color: var(--profit-text);
        }


        /* НАВИГАЦИЯ */

        .page-navigation {
            margin-bottom: 14px;
        }


        .menu-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            height: 34px;
            padding: 0 12px;

            border: 1px solid var(--profit-border);
            border-radius: 8px;

            background: #fff;
            color: var(--profit-muted);

            text-decoration: none;

            font-size: 12px;
            font-weight: 600;

            transition: .15s ease;
        }


        .menu-back-btn:hover {
            border-color: var(--profit-blue);
            color: var(--profit-blue);
            background: #f8fafc;
        }


        .profit-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 24px;
        }


        .profit-title {
            margin: 0;

            font-size: 26px;
            font-weight: 700;
        }


        .profit-subtitle {
            margin-top: 5px;

            color: var(--profit-muted);

            font-size: 13px;
        }


        /* FILTER */

        .profit-filter {
            display: flex;
            gap: 10px;
            align-items: end;

            background: var(--profit-card);

            border: 1px solid var(--profit-border);
            border-radius: 14px;

            padding: 16px;

            margin-bottom: 20px;
        }


        .profit-filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }


        .profit-filter-label {
            font-size: 12px;
            font-weight: 600;

            color: var(--profit-muted);
        }


        .profit-filter input {
            height: 40px;

            border: 1px solid var(--profit-border);
            border-radius: 8px;

            padding: 0 12px;

            font-family: inherit;
            font-size: 13px;

            outline: none;
        }


        .profit-filter input:focus {
            border-color: var(--profit-blue);
        }


        /* DATE INPUT — общий сброс и ровный вид */

        .profit-filter input[type="date"] {
            -webkit-appearance: none;
            appearance: none;

            box-sizing: border-box;

            line-height: 38px;

            padding: 0 10px;

            color: var(--profit-text);

            background: #fff;
        }


        .profit-filter input[type="date"]::-webkit-date-and-time-value {
            text-align: left;
            margin: 0;
            padding: 0;
        }


        .profit-filter input[type="date"]::-webkit-calendar-picker-indicator {
            margin-left: auto;
            padding: 0;

            width: 16px;
            height: 16px;

            opacity: .6;

            cursor: pointer;
        }


        .profit-filter input[type="date"]::-webkit-inner-spin-button {
            display: none;
        }


        .profit-filter button {
            height: 40px;

            border: none;
            border-radius: 8px;

            padding: 0 18px;

            background: var(--profit-blue);
            color: #fff;

            font-family: inherit;
            font-size: 13px;
            font-weight: 600;

            cursor: pointer;
        }


        .profit-filter button:hover {
            background: var(--profit-blue-hover);
        }


        /* MAIN CARDS */

        .profit-cards {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 14px;

            margin-bottom: 14px;
        }


        .profit-card {
            background: var(--profit-card);

            border: 1px solid var(--profit-border);
            border-radius: 14px;

            padding: 18px;
        }


        .profit-card-label {
            color: var(--profit-muted);

            font-size: 12px;
            font-weight: 500;

            margin-bottom: 9px;
        }


        .profit-card-value {
            font-size: 22px;
            font-weight: 700;

            letter-spacing: -0.3px;
        }


        .profit-card-small {
            margin-top: 5px;

            color: var(--profit-muted);

            font-size: 11px;
        }


        .profit-card.profit-main {
            background: var(--profit-blue);
            border-color: var(--profit-blue);

            color: #fff;
        }


        .profit-card.profit-main .profit-card-label,
        .profit-card.profit-main .profit-card-small {
            color: rgba(255, 255, 255, .68);
        }


        .profit-green {
            color: var(--profit-green);
        }


        .profit-red {
            color: var(--profit-red);
        }


        /* SECONDARY */

        .profit-secondary {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 14px;

            margin-bottom: 22px;
        }


        .profit-secondary-card {
            background: var(--profit-card);

            border: 1px solid var(--profit-border);
            border-radius: 14px;

            padding: 17px;
        }


        .profit-secondary-title {
            color: var(--profit-muted);

            font-size: 12px;

            margin-bottom: 8px;
        }


        .profit-secondary-value {
            font-size: 20px;
            font-weight: 700;
        }


        /* SECTION */

        .profit-section {
            background: var(--profit-card);

            border: 1px solid var(--profit-border);
            border-radius: 14px;

            padding: 18px;

            margin-bottom: 18px;
        }


        .profit-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 16px;
        }


        .profit-section-title {
            margin: 0;

            font-size: 15px;
            font-weight: 700;
        }


        /* CHART */

        .profit-chart-container {
            position: relative;

            height: 340px;

            width: 100%;
        }


        /* TABLE */

        .profit-table-wrapper {
            overflow-x: auto;
        }


        .profit-table {
            width: 100%;

            border-collapse: collapse;

            font-size: 13px;
        }


        .profit-table th {
            text-align: left;

            padding: 11px 10px;

            color: var(--profit-muted);

            font-size: 11px;
            font-weight: 600;

            border-bottom: 1px solid var(--profit-border);

            white-space: nowrap;
        }


        .profit-table td {
            padding: 13px 10px;

            border-bottom: 1px solid #f0f1f3;

            white-space: nowrap;
        }


        .profit-table tbody tr:last-child td {
            border-bottom: none;
        }


        .profit-table .sku {
            font-weight: 600;
        }


        .profit-table .number {
            text-align: right;
        }


        .profit-table th.number {
            text-align: right;
        }


        .profit-positive {
            color: var(--profit-green);
            font-weight: 700;
        }


        .profit-negative {
            color: var(--profit-red);
            font-weight: 700;
        }


        .profit-empty {
            padding: 30px 10px;

            text-align: center;

            color: var(--profit-muted);
        }


        /* MOBILE */

        @media (max-width: 800px) {

            .profit-cards {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

        }


        @media (max-width: 600px) {

            .profit-page {
                padding: 20px 12px 40px;
            }

            .profit-header {
                margin-bottom: 18px;
            }

            .profit-title {
                font-size: 22px;
            }

            /* ФИЛЬТР НА ТЕЛЕФОНЕ */

            .profit-filter {
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;

                flex-direction: column;
                align-items: stretch;

                gap: 10px;
            }

            .profit-filter-group {
                width: 100%;
                min-width: 0;
            }

            .profit-filter input {
                display: block;

                width: 100%;
                max-width: 100%;
                min-width: 0;

                box-sizing: border-box;

                height: 40px;

                padding: 0 10px;

                font-size: 13px;

                appearance: none;
                -webkit-appearance: none;
            }

            .profit-filter input[type="date"] {
                width: 100%;
                max-width: 100%;
                min-width: 0;
                box-sizing: border-box;

                line-height: 38px;
            }

            .profit-filter button {
                width: 100%;
                box-sizing: border-box;
            }


            /* КАРТОЧКИ */

            .profit-cards {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 10px;
            }


            .profit-card {
                padding: 14px;
            }


            .profit-card-value {
                font-size: 18px;
            }


            /* ВТОРИЧНЫЕ КАРТОЧКИ */

            .profit-secondary {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 10px;
            }


            .profit-secondary-card {
                padding: 14px;
            }


            .profit-secondary-value {
                font-size: 17px;
            }


            /* СЕКЦИИ */

            .profit-section {
                padding: 14px;
                border-radius: 12px;
            }


            .profit-chart-container {
                height: 300px;
            }

        }


        /* ОЧЕНЬ МАЛЕНЬКИЕ ТЕЛЕФОНЫ */

        @media (max-width: 380px) {

            .profit-filter input[type="date"] {
                padding-left: 8px;
                padding-right: 8px;

                font-size: 12px;
            }

        }
    </style>


    @section('content')

    <div class="profit-page">

        {{-- НАВИГАЦИЯ --}}

        <div class="page-navigation">

            <a
                href="{{ route('admin.analytics.menu') }}"
                class="menu-back-btn">

                ← В меню аналитики

            </a>

        </div>


        {{-- HEADER --}}

        <div class="profit-header">

            <div>

                <h1 class="profit-title">
                    Аналитика прибыли
                </h1>

                <div class="profit-subtitle">
                    Выручка, себестоимость и прибыль
                </div>

            </div>

        </div>


        {{-- FILTER --}}

        <form
            method="GET"
            action="{{ route('admin.analytics.profit') }}"
            class="profit-filter">

            <div class="profit-filter-group">

                <label class="profit-filter-label">
                    От
                </label>

                <input
                    type="date"
                    name="from"
                    value="{{ $from->format('Y-m-d') }}">

            </div>


            <div class="profit-filter-group">

                <label class="profit-filter-label">
                    До
                </label>

                <input
                    type="date"
                    name="to"
                    value="{{ $to->format('Y-m-d') }}">

            </div>


            <button type="submit">
                Показать
            </button>

        </form>


        {{-- MAIN CARDS --}}

        <div class="profit-cards">

            <div class="profit-card">

                <div class="profit-card-label">
                    Выручка
                </div>

                <div class="profit-card-value">
                    {{ number_format($revenue, 0, '.', ' ') }} ₸
                </div>

                <div class="profit-card-small">
                    Продажи − возвраты
                </div>

            </div>


            <div class="profit-card">

                <div class="profit-card-label">
                    Себестоимость товара
                </div>

                <div class="profit-card-value">
                    {{ number_format($cost, 0, '.', ' ') }} ₸
                </div>

            </div>


            <div class="profit-card profit-main">

                <div class="profit-card-label">
                    Прибыль 
                </div>

                <div class="profit-card-value">
                    {{ number_format($profit, 0, '.', ' ') }} ₸
                </div>

                <div class="profit-card-small">
                    Выручка − себестоимость
                </div>

            </div>


            <div class="profit-card">

                <div class="profit-card-label">
                    Маржинальность
                </div>

                <div class="profit-card-value">
                    {{ number_format($margin, 1, '.', ' ') }}%
                </div>

            </div>

        </div>


        {{-- SECONDARY CARDS --}}

        <div class="profit-secondary">

            <div class="profit-secondary-card">

                <div class="profit-secondary-title">
                    Продано
                </div>

                <div class="profit-secondary-value">
                    {{ number_format($soldQuantity, 0, '.', ' ') }}
                    шт.
                </div>

            </div>


            <div class="profit-secondary-card">

                <div class="profit-secondary-title">
                    Возвращено
                </div>

                <div class="profit-secondary-value">
                    {{ number_format($returnsQuantity, 0, '.', ' ') }}
                    шт.
                </div>

            </div>

        </div>


        {{-- DAILY CHART --}}

        <div class="profit-section">

            <div class="profit-section-header">

                <h2 class="profit-section-title">
                    Прибыль по дням
                </h2>

            </div>


            <div class="profit-chart-container">

                <canvas id="profitChart"></canvas>

            </div>

        </div>


        {{-- PRODUCTS --}}

        <div class="profit-section">

            <div class="profit-section-header">

                <h2 class="profit-section-title">
                    Прибыль по товарам
                </h2>

            </div>


            <div class="profit-table-wrapper">

                <table class="profit-table">

                    <thead>

                        <tr>

                            <th>
                                Артикул
                            </th>

                            <th class="number">
                                Продано
                            </th>

                            <th class="number">
                                Возвраты
                            </th>

                            <th class="number">
                                Выручка
                            </th>

                            <th class="number">
                                Себестоимость
                            </th>

                            <th class="number">
                                Прибыль
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($productStats as $product)

                        <tr>

                            <td class="sku">
                                {{ $product['sku'] }}
                            </td>


                            <td class="number">
                                {{ number_format(
                                $product['quantity'],
                                0,
                                '.',
                                ' '
                            ) }}
                            </td>


                            <td class="number">
                                {{ number_format(
                                $product['returns'],
                                0,
                                '.',
                                ' '
                            ) }}
                            </td>


                            <td class="number">
                                {{ number_format(
                                $product['revenue'],
                                0,
                                '.',
                                ' '
                            ) }}
                                ₸
                            </td>


                            <td class="number">
                                {{ number_format(
                                $product['cost'],
                                0,
                                '.',
                                ' '
                            ) }}
                                ₸
                            </td>


                            <td class="number
                            {{ $product['profit'] >= 0
                                ? 'profit-positive'
                                : 'profit-negative'
                            }}">

                                {{ number_format(
                                $product['profit'],
                                0,
                                '.',
                                ' '
                            ) }}
                                ₸

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="6"
                                class="profit-empty">
                                За выбранный период продаж нет
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const dailyData =
                    @json(array_values($dailyStats));


                const labels =
                    dailyData.map(function(item) {

                        const date =
                            new Date(
                                item.date + 'T00:00:00'
                            );

                        return date.toLocaleDateString(
                            'ru-RU', {
                                day: '2-digit',
                                month: '2-digit'
                            }
                        );

                    });


                const revenue =
                    dailyData.map(function(item) {

                        return Math.round(
                            item.revenue
                        );

                    });


                const cost =
                    dailyData.map(function(item) {

                        return Math.round(
                            item.cost
                        );

                    });


                const profit =
                    dailyData.map(function(item) {

                        return Math.round(
                            item.profit
                        );

                    });


                const ctx =
                    document
                    .getElementById('profitChart')
                    .getContext('2d');


                new Chart(ctx, {

                    type: 'line',

                    data: {

                        labels: labels,

                        datasets: [

                            {
                                label: 'Выручка',

                                data: revenue,

                                borderWidth: 2,

                                tension: 0.35,

                                pointRadius: 2
                            },

                            {
                                label: 'Себестоимость',

                                data: cost,

                                borderWidth: 2,

                                tension: 0.35,

                                pointRadius: 2
                            },

                            {
                                label: 'Прибыль',

                                data: profit,

                                borderWidth: 3,

                                tension: 0.35,

                                pointRadius: 2
                            }

                        ]

                    },


                    options: {

                        responsive: true,

                        maintainAspectRatio: false,


                        interaction: {

                            mode: 'index',

                            intersect: false

                        },


                        plugins: {

                            legend: {

                                position: 'bottom',

                                labels: {

                                    usePointStyle: true,

                                    padding: 18,

                                    font: {

                                        size: 11

                                    }

                                }

                            },


                            tooltip: {

                                callbacks: {

                                    label: function(context) {

                                        return (
                                            context.dataset.label +
                                            ': ' +
                                            new Intl.NumberFormat(
                                                'ru-RU'
                                            ).format(
                                                context.parsed.y
                                            ) +
                                            ' ₸'
                                        );

                                    }

                                }

                            }

                        },


                        scales: {

                            x: {

                                grid: {

                                    display: false

                                },

                                ticks: {

                                    font: {

                                        size: 10

                                    },

                                    autoSkip: true,

                                    maxTicksLimit: 12

                                }

                            },


                            y: {

                                beginAtZero: true,

                                ticks: {

                                    font: {

                                        size: 10

                                    },

                                    callback: function(value) {

                                        return new Intl.NumberFormat(
                                            'ru-RU'
                                        ).format(value) + ' ₸';

                                    }

                                }

                            }

                        }

                    }

                });

            }
        );
    </script>

</body>

</html>