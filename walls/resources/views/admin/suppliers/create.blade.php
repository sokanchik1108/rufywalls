<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Добавить поставщика</title>

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

        .supplier-page {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
        }

        /* HEADER */

        .page-header {
            margin-bottom: 25px;
        }

        .page-title {
            margin: 0 0 7px;
            color: #01142f;
            font-size: 28px;
            font-weight: 700;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #687386;
            font-size: 14px;
            text-decoration: none;
            transition: color .2s ease;
        }

        .back-link:hover {
            color: #01142f;
        }

        /* ALERT */

        .alert {
            padding: 14px 16px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-size: 14px;
        }

        .alert-danger {
            color: #a51d2d;
            background: #fff0f1;
            border: 1px solid #f1c9ce;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert-danger li+li {
            margin-top: 5px;
        }

        /* CARD */

        .form-card {
            background: #fff;
            border: 1px solid #e5e9ef;
            border-radius: 14px;
            box-shadow: 0 3px 14px rgba(15, 31, 52, 0.05);
            padding: 28px;
        }

        /* FORM */

        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #263246;
            font-size: 14px;
            font-weight: 600;
        }

        .required {
            color: #c62828;
        }

        .form-control {
            width: 100%;
            min-height: 44px;
            padding: 10px 13px;
            border: 1px solid #d9dee7;
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: #172033;
            font-family: inherit;
            font-size: 14px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .form-control:focus {
            border-color: #58769b;
            box-shadow: 0 0 0 3px rgba(88, 118, 155, 0.10);
        }

        textarea.form-control {
            min-height: 110px;
            resize: vertical;
            line-height: 1.5;
        }

        .form-help {
            margin-top: 6px;
            color: #8a93a1;
            font-size: 12px;
        }

        /* BUTTONS */

        .form-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-top: 5px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 19px;
            border-radius: 8px;
            border: 1px solid transparent;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all .2s ease;
        }

        .btn-primary {
            background: #01142f;
            color: #fff;
        }

        .btn-primary:hover {
            background: #02214b;
        }

        .btn-secondary {
            background: #fff;
            color: #475467;
            border-color: #d9dee7;
        }

        .btn-secondary:hover {
            background: #f7f8fa;
        }

        /* MOBILE */

        @media (max-width: 700px) {

            .supplier-page {
                padding: 18px 12px;
            }

            .page-title {
                font-size: 23px;
            }

            .form-card {
                padding: 20px 16px;
                border-radius: 11px;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .form-actions .btn {
                width: 100%;
            }
        }

        @media (max-width: 420px) {

            .supplier-page {
                padding: 14px 10px;
            }

            .page-title {
                font-size: 21px;
            }

            .form-card {
                padding: 18px 14px;
            }

            .form-control {
                min-height: 42px;
            }
        }
    </style>
</head>

<body>

    <div class="supplier-page">

        {{-- HEADER --}}
        <div class="page-header">

            <h1 class="page-title">
                Добавить поставщика
            </h1>

            <a
                href="{{ route('admin.suppliers.index') }}"
                class="back-link">
                ← Назад к поставщикам
            </a>

        </div>


        {{-- ERRORS --}}
        @if($errors->any())

        <div class="alert alert-danger">

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
        <div class="form-card">

            <form
                action="{{ route('admin.suppliers.store') }}"
                method="POST">

                @csrf


                {{-- NAME --}}
                <div class="form-group">

                    <label
                        for="name"
                        class="form-label">
                        Название поставщика
                        <span class="required">*</span>
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Например: Artex"
                        required
                        autofocus>

                </div>


                {{-- PHONE --}}
                <div class="form-group">

                    <label
                        for="phone"
                        class="form-label">
                        Телефон
                    </label>

                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        class="form-control"
                        value="{{ old('phone') }}"
                        placeholder="+7 (___) ___-__-__">

                </div>


                {{-- COMMENT --}}
                <div class="form-group">

                    <label
                        for="comment"
                        class="form-label">
                        Комментарий
                    </label>

                    <textarea
                        id="comment"
                        name="comment"
                        class="form-control"
                        rows="4"
                        placeholder="Дополнительная информация о поставщике...">{{ old('comment') }}</textarea>

                </div>


                {{-- BUTTONS --}}
                <div class="form-actions">

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Сохранить поставщика
                    </button>

                    <a
                        href="{{ route('admin.suppliers.index') }}"
                        class="btn btn-secondary">
                        Отмена
                    </a>

                </div>

            </form>

        </div>

    </div>

</body>

</html>