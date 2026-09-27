<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Поставщики</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f5f7fa;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
        }

        .suppliers-page {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }

        /* HEADER */

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }

        .page-title {
            margin: 0 0 6px;
            font-size: 28px;
            font-weight: 700;
            color: #01142f;
        }

        .page-subtitle {
            margin: 0;
            font-size: 14px;
            color: #7a8494;
        }

        /* BUTTON */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 18px;
            border-radius: 9px;
            border: 1px solid transparent;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background: #01142f;
            color: #fff;
        }

        .btn-primary:hover {
            background: #02214b;
        }

        .btn-edit {
            background: #f4f7fb;
            color: #174a8b;
            border-color: #dbe4ef;
        }

        .btn-edit:hover {
            background: #e9f0f8;
        }

        .btn-delete {
            background: #fff5f5;
            color: #c62828;
            border-color: #f0d4d4;
        }

        .btn-delete:hover {
            background: #ffe8e8;
        }

        /* ALERTS */

        .alert {
            padding: 14px 16px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-size: 14px;
        }

        .alert-success {
            color: #176b3a;
            background: #edf9f2;
            border: 1px solid #ccebd8;
        }

        .alert-danger {
            color: #a51d2d;
            background: #fff0f1;
            border: 1px solid #f1c9ce;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        /* CARD */

        .suppliers-card {
            background: #fff;
            border: 1px solid #e6eaf0;
            border-radius: 14px;
            box-shadow: 0 3px 12px rgba(15, 31, 52, 0.04);
            overflow: hidden;
        }

        /* TABLE */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .suppliers-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        .suppliers-table thead {
            background: #f8fafc;
        }

        .suppliers-table th {
            padding: 15px 18px;
            text-align: left;
            border-bottom: 1px solid #e7ebf0;
            color: #697386;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            white-space: nowrap;
        }

        .suppliers-table td {
            padding: 17px 18px;
            border-bottom: 1px solid #edf0f4;
            vertical-align: middle;
            font-size: 14px;
        }

        .suppliers-table tbody tr:last-child td {
            border-bottom: none;
        }

        .suppliers-table tbody tr {
            transition: background .15s ease;
        }

        .suppliers-table tbody tr:hover {
            background: #fafcff;
        }

        /* SUPPLIER */

        .supplier-name {
            display: block;
            margin-bottom: 4px;
            color: #01142f;
            font-size: 15px;
            font-weight: 700;
        }

        .supplier-comment {
            max-width: 350px;
            color: #8a93a1;
            font-size: 13px;
            line-height: 1.4;
        }

        .supplier-phone {
            color: #39465a;
        }

        .receipts-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 30px;
            padding: 0 9px;
            border-radius: 7px;
            background: #f0f4f8;
            color: #344054;
            font-size: 13px;
            font-weight: 600;
        }

        /* ACTIONS */

        .actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .actions .btn {
            min-height: 34px;
            padding: 0 12px;
            border-radius: 7px;
            font-size: 13px;
        }

        .actions form {
            margin: 0;
        }

        /* EMPTY */

        .empty-state {
            padding: 70px 20px !important;
            text-align: center;
        }

        .empty-title {
            margin-bottom: 7px;
            color: #344054;
            font-size: 16px;
            font-weight: 600;
        }

        .empty-text {
            color: #98a2b3;
            font-size: 14px;
        }

        /* PAGINATION */

        .pagination-wrapper {
            padding: 18px 20px;
            border-top: 1px solid #edf0f4;
        }

        /* MOBILE */

        @media (max-width: 700px) {

            .suppliers-page {
                padding: 18px 12px;
            }

            .page-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 15px;
            }

            .page-title {
                font-size: 23px;
            }

            .page-subtitle {
                font-size: 13px;
            }

            .page-header .btn {
                width: 100%;
            }

            .suppliers-card {
                border-radius: 11px;
            }

            .suppliers-table {
                min-width: 650px;
            }

            .suppliers-table th,
            .suppliers-table td {
                padding: 13px 12px;
            }

            .supplier-comment {
                max-width: 220px;
            }
        }

        @media (max-width: 420px) {

            .suppliers-page {
                padding: 14px 10px;
            }

            .page-title {
                font-size: 21px;
            }

            .btn {
                min-height: 40px;
            }
        }
    </style>
</head>

<body>

<div class="suppliers-page">

    {{-- HEADER --}}
    <div class="page-header">

        <div>
            <h1 class="page-title">
                Поставщики
            </h1>

            <p class="page-subtitle">
                Справочник поставщиков
            </p>
        </div>

        <a
            href="{{ route('admin.suppliers.create') }}"
            class="btn btn-primary"
        >
            + Добавить поставщика
        </a>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    {{-- TABLE --}}
    <div class="suppliers-card">

        <div class="table-wrapper">

            <table class="suppliers-table">

                <thead>
                    <tr>
                        <th>
                            Поставщик
                        </th>

                        <th>
                            Телефон
                        </th>

                        <th>
                            Приёмок
                        </th>

                        <th style="width: 190px;">
                            Действия
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($suppliers as $supplier)

                        <tr>

                            {{-- NAME --}}
                            <td>

                                <span class="supplier-name">
                                    {{ $supplier->name }}
                                </span>

                                @if($supplier->comment)

                                    <div class="supplier-comment">
                                        {{ $supplier->comment }}
                                    </div>

                                @endif

                            </td>


                            {{-- PHONE --}}
                            <td>

                                <span class="supplier-phone">
                                    {{ $supplier->phone ?: '—' }}
                                </span>

                            </td>


                            {{-- RECEIPTS --}}
                            <td>

                                <span class="receipts-count">
                                    {{ $supplier->receipts_count }}
                                </span>

                            </td>


                            {{-- ACTIONS --}}
                            <td>

                                <div class="actions">

                                    <a
                                        href="{{ route('admin.suppliers.edit', $supplier) }}"
                                        class="btn btn-edit"
                                    >
                                        Изменить
                                    </a>


                                    <form
                                        action="{{ route('admin.suppliers.destroy', $supplier) }}"
                                        method="POST"
                                        onsubmit="return confirm('Удалить поставщика?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-delete"
                                        >
                                            Удалить
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="empty-state"
                            >

                                <div class="empty-title">
                                    Поставщиков пока нет
                                </div>

                                <div class="empty-text">
                                    Добавьте первого поставщика, чтобы использовать его при создании приёмок.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($suppliers->hasPages())

            <div class="pagination-wrapper">
                {{ $suppliers->links() }}
            </div>

        @endif

    </div>

</div>

</body>
</html>

