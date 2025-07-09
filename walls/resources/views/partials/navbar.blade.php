<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Минималистичная шапка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
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

        .cart-icon {
            position: relative;
            font-size: 1.4rem;
            color: #222;
            margin-left: 1rem;
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

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: #fff;
                padding: 0;
                border-top: none;
                box-shadow: none;
                transition: padding 0.3s ease;
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

            .cart-icon {
                margin-top: 10px;
                margin-left: 10px;
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
                <button class="navbar-toggler" type="button" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию" id="customToggler">
                    <span class="navbar-toggler-icon"></span>
                </button>


                <!-- Меню и кнопка -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto d-flex align-items-lg-center gap-3">
                        <li class="nav-item"><a class="nav-link" href="/address">Адрес</a></li>
                        <li class="nav-item"><a class="nav-link" href="/address">Контакты</a></li>
                        <li class="nav-item"><a class="nav-link" href="/delivery">Доставка</a></li>
                        <li class="nav-item"><a class="nav-link" href="/about-products">О нашей продукции</a></li>
                        <li class="nav-item"><a class="nav-link" href=" {{ route('how-to-order') }} ">Как оформить заказ</a></li>
                        <li class="nav-item"><a class="nav-link" href=" {{ route('calculator') }} ">Калькулятор обоев</a></li>
                    </ul>

                    <!-- Блок корзины и каталога -->
                    <div class="d-flex align-items-center mt-2 mt-lg-0 gap-3">
                        <!-- Иконка корзины -->
                        <a href="{{ route('cart') }}" class="cart-icon position-relative">
                            <i class="bi bi-bag"></i>
                            <span class="cart-count">{{ $cartCount }}</span>
                        </a>

                        <!-- Кнопка Каталога -->
                        <a href="{{ route('catalog') }}" class="catalog-btn">Каталог</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <script>
        function updateCartCount() {
            fetch('/cart/count')
                .then(response => response.json())
                .then(data => {
                    const countElement = document.querySelector('.cart-count');
                    if (countElement) {
                        countElement.textContent = data.count;
                    }
                })
                .catch(console.error);
        }

        window.addEventListener('storage', (e) => {
            if (e.key === 'cartUpdated') {
                updateCartCount();
            }
        });


        // Автообновление при загрузке
        document.addEventListener('DOMContentLoaded', updateCartCount);
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggler = document.getElementById('customToggler');
            const navbarCollapse = document.getElementById('navbarNav');
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: false
            });

            toggler.addEventListener('click', () => {
                if (navbarCollapse.classList.contains('show')) {
                    bsCollapse.hide();
                } else {
                    bsCollapse.show();
                }
            });
        });
    </script>


</body>

</html>