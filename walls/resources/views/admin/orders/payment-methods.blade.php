<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Способы оплаты</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f5f7;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        .page {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0;
            font-size: 28px;
        }

        .menu-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            padding: 0 18px;
            border-radius: 8px;
            background: #01142f;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .menu-btn:hover {
            background: #02214b;
            color: #fff;
        }

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-title {
            margin: 0 0 15px;
            font-size: 18px;
            font-weight: 700;
        }

        .form {
            display: flex;
            gap: 10px;
        }

        .input {
            width: 100%;
            height: 44px;
            padding: 0 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
        }

        .input:focus {
            border-color: #01142f;
        }

        .add-btn {
            height: 44px;
            padding: 0 22px;
            border: 0;
            border-radius: 8px;
            background: #01142f;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
            white-space: nowrap;
        }

        .add-btn:hover {
            background: #02214b;
        }

        .alert {
            padding: 13px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .success {
            background: #ecfdf3;
            color: #166534;
        }

        .error {
            background: #fef2f2;
            color: #991b1b;
        }

        .method {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .method-name {
            font-size: 15px;
            font-weight: 600;
        }

        .delete-btn {
            height: 36px;
            padding: 0 13px;
            border: 1px solid #fecaca;
            border-radius: 7px;
            background: #fff;
            color: #dc2626;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #fef2f2;
        }

        .empty {
            color: #6b7280;
            font-size: 14px;
            padding: 10px 0;
        }

        @media (max-width: 600px) {
            .page {
                padding: 15px 10px;
            }

            .header {
                margin-bottom: 15px;
            }

            h1 {
                font-size: 22px;
            }

            .menu-btn {
                height: 38px;
                padding: 0 12px;
                font-size: 13px;
            }

            .card {
                padding: 15px;
                border-radius: 9px;
            }

            .form {
                flex-direction: column;
            }

            .add-btn {
                width: 100%;
            }

            .method {
                padding: 10px;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <div class="header">

        <h1>Способы оплаты</h1>

        <a href="{{ url('/admin') }}" class="menu-btn">
            Меню
        </a>

    </div>


    @if(session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="alert error">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="alert error">

            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach

        </div>

    @endif


    <div class="card">

        <h2 class="card-title">
            Добавить способ оплаты
        </h2>

        <form
            action="{{ route('admin.payment-methods.store') }}"
            method="POST"
            class="form">

            @csrf

            <input
                type="text"
                name="name"
                class="input"
                placeholder="Например: Kaspi QR"
                value="{{ old('name') }}"
                maxlength="255"
                required
            >

            <button
                type="submit"
                class="add-btn">
                Добавить
            </button>

        </form>

    </div>


    <div class="card">

        <h2 class="card-title">
            Способы оплаты
        </h2>

        @forelse($paymentMethods as $method)

            <div class="method">

                <div class="method-name">
                    {{ $method->name }}
                </div>

                <form
                    action="{{ route('admin.payment-methods.destroy', $method->id) }}"
                    method="POST"
                    onsubmit="return confirm('Удалить способ оплаты?');">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="delete-btn">
                        Удалить
                    </button>

                </form>

            </div>

        @empty

            <div class="empty">
                Способы оплаты пока не добавлены.
            </div>

        @endforelse

    </div>

</div>

</body>

</html>

