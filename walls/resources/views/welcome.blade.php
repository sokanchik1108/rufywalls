<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>RAFY WALLS — Добро пожаловать</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffffff;
            color: #0b1f3a;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .welcome-container {
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h1 {
            font-size: 2.4rem;
            font-weight: bold;
            margin-bottom: 30px;
            color: #0b1f3a;
        }

        h1 span {
            display: block;
            font-size: 1.2rem;
            font-weight: normal;
            color: #6c757d;
        }

        .btn-custom {
            background-color: #0b1f3a;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 500;
            transition: all 0.25s ease;
            text-decoration: none;
            display: block;
        }

        .btn-custom:hover {
            background-color: #132f57;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(11, 31, 58, 0.2);
        }

        .social-icons {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 25px;
        }

        .social-icons a {
            font-size: 1.8rem;
            color: #0b1f3a;
            transition: color 0.2s, transform 0.2s;
        }

        .social-icons a:hover {
            color: #495e86;
            transform: scale(1.2);
        }
    </style>
</head>
<body>

<div class="welcome-container">
    <h1>
        Добро пожаловать <br> в RAFY WALLS!
        <span class="mt-1">Мир качественных обоев</span>
    </h1>

    <a href="{{ url('/') }}" class="btn-custom">Перейти на сайт</a>
    <a href="{{ url('/catalog') }}" class="btn-custom">Каталог</a>
    <a href="https://www.google.com/maps/dir//43.2726002,76.8852111/@43.2722472,76.8838714,497m/data=!3m1!1e3?entry=ttu&g_ep=EgoyMDI1MDcxNi4wIKXMDSoASAFQAw%3D%3D" class="btn-custom" target="_blank">Google Maps</a>
    <a href="https://2gis.kz/almaty/firm/70000001102986031?m=76.884534%2C43.272083%2F16" class="btn-custom" target="_blank">2ГИС</a>

    <div class="social-icons mt-3">
        <a href="https://instagram.com/rafy_walls" target="_blank" aria-label="Instagram">
            <i class="bi bi-instagram"></i>
        </a>
        <a href="https://wa.me/77077121255" target="_blank" aria-label="WhatsApp">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>
</div>

</body>
</html>
