<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Минималистичная шапка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        header {
            background-color: #fff;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
            padding: 0.5rem 1rem;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #222;
            letter-spacing: 1px;
            user-select: none;
        }

        .catalog-btn {
            background-color: #01142f;
            color: white;
            font-size: 15px;
            font-weight: 600;
            padding: 0.6rem 2rem;
            border-radius: 40px;
            transition: background-color 0.3s ease;
            border: none;
            text-decoration: none;
            white-space: nowrap;
        }

        .catalog-btn:hover {
            background-color: #02214b;
            color: #fff;
            text-decoration: none;
        }

        .nav-link {
            color: #444;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover,
        .nav-link:focus {
            color: #007bff;
            text-decoration: none;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: #fff;
                padding: 1rem;
                border-top: 1px solid #ddd;
            }

            .catalog-btn {
                width: 100%;
                text-align: center;
                margin-top: 10px;
                margin-left: 10px;
            }

            .navbar-nav {
                gap: 0.5rem;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>

<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Логотип -->
            <a class="navbar-brand" href="/">RAFY WALLS</a>

            <!-- Бургер-кнопка -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Меню и кнопка -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto d-flex align-items-lg-center gap-3">
                    <li class="nav-item"><a class="nav-link" href="#contacts">Адрес</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacts">Контакты</a></li>
                    <li class="nav-item"><a class="nav-link" href="#delivery">Доставка</a></li>
                    <li class="nav-item"><a class="nav-link" href="#product-info">О нашей продукции</a></li>
                </ul>
                <div class="d-lg-flex justify-content-end">
                    <a href="{{ route('catalog') }}" class="catalog-btn mt-2 mt-lg-0">Каталог</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
