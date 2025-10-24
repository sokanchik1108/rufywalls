<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Минималистичная шапка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        header {
            position: sticky;
            top: 0;
            z-index: 1030;
            /* поверх всего контента */
            background-color: #fff8f0;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
            padding: 0.5rem 1rem;
            max-width: 100%;
            overflow-x: hidden;
        }


        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            font-size: 1.7rem;
            color: #01142f;
            letter-spacing: 1px;
            user-select: none;
            line-height: 1;
            display: inline-block;
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

        .catalog-btn-sm {
            padding: 0.32rem 1.3rem;
            font-size: 15px;
            font-weight: 500;
            border-radius: 40px;
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

        .cart-icon {
            position: relative;
            font-size: 1.4rem;
            color: #222;
        }

        .cart-icon:hover {
            color: #007bff;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background: #dc3545;
            color: #fff;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 50%;
        }

        .custom-toggler .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(1,20,47,1)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }

        .navbar-toggler:focus {
            outline: none;
            box-shadow: none;
        }

        .custom-offcanvas {
            transition: transform 0.4s ease-in-out;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
        }

        .custom-offcanvas .nav-link {
            font-size: 1rem;
            font-weight: 500;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f1f1;
            color: #222;
            transition: color 0.3s ease;
        }

        .custom-offcanvas .nav-link:hover {
            color: #007bff;
        }

        .custom-offcanvas .offcanvas-title {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .custom-offcanvas .cart-icon {
            font-size: 1.5rem;
            color: #01142f;
            transition: color 0.3s ease;
        }

        .custom-offcanvas .cart-icon:hover {
            color: #007bff;
        }

        @media (max-width: 991px) {
            header .container-fluid {
                padding-left: 0.1rem;
                padding-right: 0.1rem;
            }

            .navbar-toggler {
                margin-right: -15px;
                margin-left: -8px;
            }
        }

        /* Планшеты: iPad, iPad Pro, iPad Air */
        @media (min-width: 768px) and (max-width: 1024px) {
            .d-lg-none.d-flex.align-items-center.ms-auto.gap-2 .catalog-btn-sm {
                font-size: 1rem;
                /* чуть меньше текста */
                padding: 0.4rem 1.2rem;
                /* меньше отступов */
                border-radius: 40px;
            }

            .d-lg-none.d-flex.align-items-center.ms-auto.gap-2 .navbar-toggler {
                padding: 0.4rem 0.5rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">RAFY WALLS</a>

                <div class="d-lg-none d-flex align-items-center ms-auto gap-2">
                    <a href="{{ route('catalog') }}" class="catalog-btn catalog-btn-sm">Каталог</a>
                    <button class="navbar-toggler border-0 custom-toggler" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse d-none d-lg-flex">
                    <ul class="navbar-nav mx-auto d-flex align-items-lg-center gap-3">
                        <li class="nav-item"><a class="nav-link" href="/address">Адрес</a></li>
                        <li class="nav-item"><a class="nav-link" href="/address">Контакты</a></li>
                        <li class="nav-item"><a class="nav-link" href="/delivery">Доставка</a></li>
                        <li class="nav-item"><a class="nav-link" href="/about-products">О нашей продукции</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('how-to-order') }}">Как оформить заказ</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('calculator') }}">Калькулятор обоев</a></li>
                    </ul>
                </div>

                <div class="d-none d-lg-flex align-items-center gap-3">
                    <a href="{{ route('cart') }}" class="cart-icon position-relative">
                        <i class="bi bi-bag"></i>
                        <span class="cart-count">{{ $cartCount }}</span>
                    </a>
                    <a href="{{ route('catalog') }}" class="catalog-btn">Каталог</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="offcanvas offcanvas-start custom-offcanvas" tabindex="-1" id="offcanvasMenu"
        data-bs-backdrop="false" data-bs-scroll="false" style="width: 90%;">
        <div class="offcanvas-header border-bottom">
            <a class="navbar-brand m-0 w-100" href="/" style="font-size: 1.7rem; color: #01142f; font-weight: 600;">
                RAFY WALLS
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <ul class="navbar-nav flex-grow-1">
                <li class="nav-item"><a class="nav-link" href="/address">Адрес</a></li>
                <li class="nav-item"><a class="nav-link" href="/address">Контакты</a></li>
                <li class="nav-item"><a class="nav-link" href="/delivery">Доставка</a></li>
                <li class="nav-item"><a class="nav-link" href="/about-products">О нашей продукции</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('how-to-order') }}">Как оформить заказ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('calculator') }}">Калькулятор обоев</a></li>
            </ul>
            <div class="border-top pt-3 mt-3">
                <a href="{{ route('cart') }}" class="cart-icon position-relative">
                    <i class="bi bi-bag"></i>
                    <span class="cart-count">{{ $cartCount }}</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const offcanvasEl = document.getElementById("offcanvasMenu");

            // Следим за закрытием Offcanvas и явно удаляем overflow, если оно осталось
            offcanvasEl.addEventListener('hidden.bs.offcanvas', function() {
                document.body.style.overflow = '';
            });

            // Закрытие Offcanvas при клике вне его области
            document.addEventListener("click", function(event) {
                const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (!offcanvasInstance) return;

                const isClickInside = offcanvasEl.contains(event.target);
                const isToggle = event.target.closest("[data-bs-toggle='offcanvas']");

                // Если клик вне Offcanvas и не по кнопке открытия — закрыть
                if (!isClickInside && !isToggle) {
                    offcanvasInstance.hide();

                    // Подстраховка: убираем overflow после анимации
                    setTimeout(() => {
                        document.body.style.overflow = '';
                    }, 400); // Время в мс, соответствующее длительности анимации Bootstrap
                }
            });

            // Обновление количества товаров в корзине
            function updateCartCount() {
                fetch('/cart/count')
                    .then(response => response.json())
                    .then(data => {
                        document.querySelectorAll('.cart-count').forEach(el => el.textContent = data.count);
                    })
                    .catch(console.error);
            }

            // Обновление корзины при изменении localStorage
            window.addEventListener('storage', (e) => {
                if (e.key === 'cartUpdated') updateCartCount();
            });

            updateCartCount(); // начальное обновление при загрузке
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>