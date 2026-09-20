<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Списание #{{ $writeOff->id }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #f4f5f7;
            color: #17191c;
            font-family: Arial, sans-serif;
        }

        .page {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }


        /* =========================
           HEADER
        ========================= */

        .top {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .top-left {
            grid-column: 1;
            justify-self: start;
        }

        .title {
            grid-column: 2;
            margin: 0;
            text-align: center;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 700;
        }

        .top-right {
            grid-column: 3;
            justify-self: end;
        }


        /* =========================
           BUTTONS
        ========================= */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            padding: 0 14px;
            border-radius: 9px;
            border: 0;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            transition: .15s ease;
        }

        .btn-dark {
            background: #111827;
            color: #fff;
        }

        .btn-dark:hover {
            background: #000;
        }

        .btn-light {
            background: #fff;
            color: #111827;
            border: 1px solid #dfe3e8;
        }

        .btn-light:hover {
            background: #f8f9fa;
        }

        .btn-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .btn-danger:hover {
            background: #fecaca;
        }


        /* =========================
           ALERTS
        ========================= */

        .success,
        .error {
            padding: 11px 13px;
            margin-bottom: 15px;
            border-radius: 9px;
            font-size: 13px;
        }

        .success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }


        /* =========================
           CARDS
        ========================= */

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .card-title {
            margin: 0 0 13px;
            font-size: 16px;
            line-height: 1.2;
            font-weight: 700;
        }


        /* =========================
           INFO
        ========================= */

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 7px;
        }

        .info {
            min-width: 0;
            padding: 10px;
            border: 1px solid #edf0f2;
            border-radius: 9px;
            background: #fafafa;
        }

        .info-label {
            margin-bottom: 4px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
        }

        .info-value {
            color: #111827;
            font-size: 14px;
            font-weight: 600;
            word-break: break-word;
        }

        .comment {
            margin-top: 7px;
            padding: 10px;
            border: 1px solid #edf0f2;
            border-radius: 9px;
            background: #fafafa;
            color: #374151;
            font-size: 13px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .comment-label {
            margin-bottom: 4px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
        }


        /* =========================
           TABLE
        ========================= */

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            padding: 8px 2px;
            border-bottom: 1px solid #edf0f2;
            text-align: left;
            vertical-align: middle;
            font-size: 16px;
        }

        th {
            background: #f8f9fa;
            color: #4b5563;
            font-size: 12px;
            font-weight: 700;
            white-space: normal;
            line-height: 1.15;
        }

        tbody tr {
            transition: background .12s ease;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }


        /* =========================
           COLUMN WIDTHS
        ========================= */

        .index {
            width: 28px;
            color: #6b7280;
            white-space: nowrap;
            font-size: 16px;
        }

        .sku {
            width: 108px;
            font-weight: 700;
            white-space: nowrap;
            font-size: 16px;
        }

        .batch {
            width: 118px;
            white-space: nowrap;
            font-weight: 600;
            font-size: 16px;
        }

        .quantity {
            width: 102px;
            white-space: nowrap;
            font-weight: 600;
            font-size: 16px;
        }

        .unit-cost {
            width: 140px;
            white-space: nowrap;
            font-size: 16px;
            font-weight: 600;
        }

        .total-cost {
            width: 140px;
            color: #111827;
            white-space: nowrap;
            font-size: 16px;
            font-weight: 600;
        }

        .source {
            width: 175px;
            color: #555;
            font-size: 16px;
            font-weight: 600;
        }


        /* =========================
           SOURCE
        ========================= */

        .source-list {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
        }

        .source-item {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            padding: 3px 4px;
            border-radius: 5px;
            background: #f3f4f6;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            white-space: normal;
            line-height: 1.15;
        }


        /* =========================
           TOTAL
        ========================= */

        .total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding-top: 10px;
            margin-top: 9px;
            border-top: 1px solid #e5e7eb;
        }

        .total-left {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .total-label {
            color: #6b7280;
            font-size: 12px;
        }

        .total-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .total-quantity {
            color: #111827;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        .grand-total-cost {
            color: #111827;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }


        /* =========================
           EMPTY
        ========================= */

        .empty {
            padding: 35px 20px;
            text-align: center;
            color: #777;
            font-size: 13px;
        }


        /* =========================
           DANGER
        ========================= */

        .danger {
            border: 1px solid #fecaca;
            background: #fffafa;
        }

        .danger-title {
            margin: 0 0 6px;
            color: #991b1b;
            font-size: 15px;
            font-weight: 700;
        }

        .danger-text {
            margin: 0 0 13px;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.45;
        }


        /* =========================
           TABLET / MOBILE
        ========================= */

        @media (max-width: 800px) {

            .page {
                padding: 14px;
            }

            .top {
                grid-template-columns: 40px 1fr 40px;
                gap: 7px;
                margin-bottom: 15px;
            }

            .top-left {
                grid-column: 1;
                justify-self: start;
            }

            .title {
                grid-column: 2;
                font-size: 21px;
            }

            .top-right {
                grid-column: 3;
                justify-self: end;
            }

            .top-left .btn {
                width: 40px;
                height: 40px;
                padding: 0;
                font-size: 0;
            }

            .top-left .btn::before {
                content: "←";
                font-size: 19px;
                line-height: 1;
            }

            .top-right .btn {
                width: 40px;
                height: 40px;
                padding: 0;
                font-size: 0;
            }

            .top-right .btn::before {
                content: "+";
                font-size: 25px;
                line-height: 1;
                font-weight: 400;
            }

            .card {
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 12px;
            }

            .card-title {
                font-size: 15px;
                margin-bottom: 11px;
            }

            .info-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 5px;
            }

            .info {
                padding: 8px 6px;
            }

            .info-label {
                font-size: 11px;
            }

            .info-value {
                font-size: 13px;
            }

            .comment {
                padding: 8px;
                font-size: 12px;
            }


            /* TABLE */

            table {
                width: 850px;
                min-width: 850px;
            }

            th,
            td {
                padding: 7px 2px;
                font-size: 14px;
            }

            th {
                font-size: 10px;
            }

            .index,
            .sku,
            .batch,
            .quantity,
            .unit-cost,
            .total-cost,
            .source {
                font-size: 12px;
            }

            .source-item {
                font-size: 13px;
                padding: 3px 3px;
            }


            /* TOTAL */

            .total {
                gap: 8px;
            }

            .total-right {
                gap: 8px;
            }

            .total-label {
                font-size: 11px;
            }

            .total-quantity {
                font-size: 13px;
            }

            .grand-total-cost {
                font-size: 13px;
            }

            .danger-text {
                font-size: 12px;
            }
        }


        /* =========================
           SMALL MOBILE
        ========================= */

        @media (max-width: 500px) {

            .page {
                padding: 10px;
            }

            .top {
                grid-template-columns: 36px 1fr 36px;
                gap: 6px;
            }

            .title {
                font-size: 19px;
            }

            .top-left .btn,
            .top-right .btn {
                width: 36px;
                height: 36px;
            }

            .top-left .btn::before {
                font-size: 18px;
            }

            .top-right .btn::before {
                font-size: 23px;
            }

            .card {
                padding: 10px;
            }

            .info-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 4px;
            }

            .info {
                padding: 7px 5px;
            }

            .info-label {
                margin-bottom: 3px;
                font-size: 11px;
            }

            .info-value {
                font-size: 13px;
            }

            .comment {
                margin-top: 6px;
                padding: 8px;
                font-size: 12px;
            }


            /* TABLE */

            table {
                width: 850px;
                min-width: 850px;
            }

            th,
            td {
                padding: 6px 2px;
                font-size: 13px;
            }

            th {
                font-size: 9px;
            }

            .index,
            .sku,
            .batch,
            .quantity,
            .unit-cost,
            .total-cost,
            .source {
                font-size: 12px;
            }

            .source-item {
                font-size: 12px;
                padding: 2px 3px;
            }


            /* TOTAL */

            .total {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .total-right {
                justify-content: space-between;
                gap: 8px;
            }

            .total-label {
                font-size: 11px;
            }

            .total-quantity {
                font-size: 13px;
            }

            .grand-total-cost {
                font-size: 13px;
            }

            .empty {
                padding: 30px 15px;
            }

            .btn-danger {
                width: 100%;
            }

        }
    </style>

</head>


<body>

    <div class="page">


        <!-- =========================
         HEADER
    ========================== -->

        <div class="top">

            <div class="top-left">

                <a
                    href="{{ route('admin.writeoffs.index') }}"
                    class="btn btn-light"
                    title="Назад">
                    ← Назад
                </a>

            </div>


            <h1 class="title">
                Списание #{{ $writeOff->id }}
            </h1>


            <div class="top-right">

                <a
                    href="{{ route('admin.writeoffs.create') }}"
                    class="btn btn-dark"
                    title="Новое списание">
                    + Новое
                </a>

            </div>

        </div>


        <!-- =========================
         MESSAGES
    ========================== -->

        @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

        @endif


        @if(session('error'))

        <div class="error">
            {{ session('error') }}
        </div>

        @endif


        @if(session('writeoff_error'))

        <div class="error">
            {{ session('writeoff_error') }}
        </div>

        @endif


        <!-- =========================
         INFO
    ========================== -->

        <div class="card">

            <h2 class="card-title">
                Информация
            </h2>


            <div class="info-grid">

                <div class="info">

                    <div class="info-label">
                        Дата
                    </div>

                    <div class="info-value">
                        {{ $writeOff->writeoff_date?->format('d.m.Y') ?? '—' }}
                    </div>

                </div>


                <div class="info">

                    <div class="info-label">
                        Склад
                    </div>

                    <div class="info-value">
                        {{ $writeOff->warehouse?->name ?? '—' }}
                    </div>

                </div>


                <div class="info">

                    <div class="info-label">
                        Количество
                    </div>

                    <div class="info-value">
                        {{ $writeOff->items->sum('quantity') }} шт.
                    </div>

                </div>

            </div>


            @if($writeOff->comment)

            <div class="comment">

                <div class="comment-label">
                    Комментарий
                </div>

                {{ $writeOff->comment }}

            </div>

            @endif

        </div>


        <!-- =========================
         PRODUCTS
    ========================== -->

        <div class="card">

            <h2 class="card-title">
                Товары
            </h2>


            @if($writeOff->items->count())


            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th class="index">
                                #
                            </th>

                            <th class="sku">
                                Артикул
                            </th>

                            <th class="batch">
                                Партия
                            </th>

                            <th class="quantity">
                                Количество
                            </th>

                            <th class="unit-cost">
                                Себестоимость
                            </th>

                            <th class="total-cost">
                                Сумма
                            </th>

                            <th class="source">
                                Источник
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        @foreach($writeOff->items as $index => $item)

                        @php

                        $itemMovements = $movements->filter(
                        fn ($movement) =>
                        (int) $movement->source_id === (int) $item->id
                        );


                        $allocations = collect();


                        foreach ($itemMovements as $movement) {

                        foreach ($movement->allocations as $allocation) {

                        $allocations->push($allocation);

                        }

                        }


                        $itemTotalCost = 0;


                        foreach ($allocations as $allocation) {

                        if ($allocation->layer) {

                        $unitCost = app(
                        \App\Services\FifoService::class
                        )->getLayerUnitCost(
                        $allocation->layer
                        );


                        $itemTotalCost +=
                        $unitCost *
                        $allocation->quantity;

                        }

                        }


                        $itemQuantity = (int) $item->quantity;


                        $itemUnitCost = $itemQuantity > 0
                        ? $itemTotalCost / $itemQuantity
                        : 0;


                        $sources = collect();


                        foreach ($allocations as $allocation) {

                        if (!$allocation->layer) {
                        continue;
                        }


                        $sourceType =
                        $allocation->layer->source_type;


                        if ($sourceType === 'initial') {

                        $sourceName = 'Начальный остаток';

                        } elseif ($sourceType === 'receipt') {

                        $sourceName = 'Приход';

                        } else {

                        $sourceName =
                        $sourceType ?: '—';

                        }


                        if (!$sources->contains($sourceName)) {

                        $sources->push($sourceName);

                        }

                        }

                        @endphp


                        <tr>

                            <td class="index">
                                {{ $index + 1 }}
                            </td>


                            <td class="sku">
                                {{ $item->variant?->sku ?? '—' }}
                            </td>


                            <td class="batch">
                                {{ $item->batch?->batch_code ?? '—' }}
                            </td>


                            <td class="quantity">
                                {{ $item->quantity }} шт.
                            </td>


                            <td class="unit-cost">

                                @if($allocations->count())

                                {{ number_format(
                                        $itemUnitCost,
                                        2,
                                        '.',
                                        ' '
                                    ) }} ₸

                                @else

                                —

                                @endif

                            </td>


                            <td class="total-cost">

                                @if($allocations->count())

                                {{ number_format(
                                        $itemTotalCost,
                                        2,
                                        '.',
                                        ' '
                                    ) }} ₸

                                @else

                                —

                                @endif

                            </td>


                            <td class="source">

                                @if($sources->count())

                                <div class="source-list">

                                    @foreach($sources as $source)

                                    <span class="source-item">
                                        {{ $source }}
                                    </span>

                                    @endforeach

                                </div>

                                @else

                                —

                                @endif

                            </td>

                        </tr>


                        @endforeach


                    </tbody>

                </table>

            </div>


            <!-- =========================
                 TOTAL CALCULATION
            ========================== -->

            @php

            $grandTotalCost = 0;


            foreach ($writeOff->items as $item) {

            $itemMovements = $movements->filter(
            fn ($movement) =>
            (int) $movement->source_id === (int) $item->id
            );


            foreach ($itemMovements as $movement) {

            foreach ($movement->allocations as $allocation) {

            if ($allocation->layer) {

            $unitCost = app(
            \App\Services\FifoService::class
            )->getLayerUnitCost(
            $allocation->layer
            );


            $grandTotalCost +=
            $unitCost *
            $allocation->quantity;

            }

            }

            }

            }

            @endphp


            <div class="total">

                <div class="total-left">

                    <div class="total-label">
                        Итого
                    </div>

                </div>


                <div class="total-right">

                    <div class="total-quantity">
                        {{ $writeOff->items->sum('quantity') }} шт.
                    </div>


                    <div class="grand-total-cost">
                        {{ number_format(
                            $grandTotalCost,
                            2,
                            '.',
                            ' '
                        ) }} ₸
                    </div>

                </div>

            </div>


            @else

            <div class="empty">
                В этом списании нет товаров.
            </div>

            @endif

        </div>


        <!-- =========================
         DELETE
    ========================== -->

        <div class="card danger">

            <h2 class="danger-title">
                Удаление списания
            </h2>


            <p class="danger-text">
                При удалении списания его движения будут отменены,
                а списанный товар будет возвращён на склад.
            </p>


            <form
                action="{{ route('admin.writeoffs.destroy', $writeOff) }}"
                method="POST"
                onsubmit="return confirm('Удалить это списание? Товар будет возвращён на склад.')">

                @csrf

                @method('DELETE')


                <button
                    type="submit"
                    class="btn btn-danger">
                    Удалить списание
                </button>

            </form>

        </div>


    </div>

</body>

</html>