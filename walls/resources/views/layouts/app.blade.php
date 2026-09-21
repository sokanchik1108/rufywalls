<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Главная')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            padding: 0.5rem 1rem;
        }

        .navbar-brand {
            font-weight: 500;
            font-size: 1rem;
        }

        /* Десктоп: ссылки по центру */
        .navbar-nav.mx-auto>.nav-item {
            margin: 0 0.8rem;
        }

        .navbar-nav .nav-link {
            font-size: 0.95rem;
            padding: 0.4rem 0.6rem;
            color: #333;
            transition: color 0.2s, background-color 0.2s;
        }

        .navbar-nav .nav-link:hover {
            color: #007bff;
            background-color: rgba(0, 123, 255, 0.05);
            border-radius: 0.25rem;
        }

        /* Offcanvas — мобильная боковая панель */
        #mobileSidebar {
            width: 260px;
        }

        #mobileSidebar .offcanvas-body {
            padding: 1rem 0.5rem;
        }

        #mobileSidebar .nav-link {
            font-size: 0.93rem;
            padding: 0.45rem 0.6rem;
            margin-bottom: 0.3rem;
            border-radius: 0.25rem;
            color: #333;
            transition: background-color 0.2s, color 0.2s;
        }

        #mobileSidebar .nav-link:hover {
            background-color: rgba(0, 123, 255, 0.08);
            color: #007bff;
        }

        #mobileSidebar .offcanvas-header {
            padding: 0.75rem 0.5rem;
        }

        #mobileSidebar .offcanvas-title {
            font-size: 1rem;
            font-weight: 500;
        }

        /* Скрываем offcanvas на десктопе */
        @media (min-width: 768px) {
            #mobileSidebar {
                display: none;
            }
        }

        /* Кнопка меню мобильная */
        .btn-menu {
            border: none;
            background-color: transparent;
            font-weight: 500;
        }

        /* Контейнер мобильного меню */
        #mobileSidebar .offcanvas-body {
            display: flex;
            flex-direction: column;
        }

        /* Нижняя часть с кнопкой выхода */
        .mobile-logout {
            margin-top: auto;
            padding-top: 15px;
        }

        /* Кнопка выхода */
        .mobile-logout button {
            width: 100%;
            text-align: left;
            border: none;
            background: transparent;
            color: #333;
            font-size: 0.93rem;
            padding: 0.45rem 0.6rem;
            border-radius: 0.25rem;
            transition: background-color 0.2s, color 0.2s;
        }

        .mobile-logout button:hover {
            background-color: rgba(220, 53, 69, 0.08);
            color: #dc3545;
        }

        .mobile-logout hr {
            margin: 0 0 0.5rem 0;
        }
    </style>
</head>

<body>

    <div id="app">

        <!-- Верхняя навигация -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">

            <div class="container-fluid">

                <!-- Логотип / Главная -->
                <a class="navbar-brand" href="{{ url('/admin') }}">
                    Главная
                </a>

                <!-- Кнопка мобильного меню -->
                <button
                    class="btn btn-menu d-md-none"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#mobileSidebar"
                    aria-controls="mobileSidebar">
                    Меню
                </button>

                <!-- Десктопное меню -->
                <div class="collapse navbar-collapse">

                    <!-- Ссылки по центру -->
                    <ul class="navbar-nav mx-auto d-none d-md-flex">

                        @auth

                        @if(auth()->user()->is_admin)

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.orders') }}">
                                Заказы
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.database') }}">
                                База товаров
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.products.selectCreateForm') }}">
                                Добавить товар
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users') }}">
                                Пользователи
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.stocks.warehouses') }}">
                                Добавить на склад
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.warehouses.overview') }}">
                                Остатки
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.analytics.menu') }}">
                                Аналитика
                            </a>
                        </li>

                        @endif

                        @endauth

                    </ul>

                    <!-- Правая часть -->
                    <ul class="navbar-nav ms-auto">

                        @guest

                        @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                Login
                            </a>
                        </li>
                        @endif

                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                Register
                            </a>
                        </li>
                        @endif

                        @else

                        <!-- Профиль пользователя -->
                        <li class="nav-item dropdown d-none d-md-block">

                            <a
                                id="navbarDropdown"
                                class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                                v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div
                                class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="navbarDropdown">

                                <!-- Выход -->
                                <a
                                    class="dropdown-item"
                                    href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Выйти
                                </a>

                                <form
                                    id="logout-form"
                                    action="{{ route('logout') }}"
                                    method="POST"
                                    class="d-none">
                                    @csrf
                                </form>

                            </div>

                        </li>

                        @endguest

                    </ul>

                </div>

            </div>

        </nav>


        <!-- ================================================= -->
        <!-- МОБИЛЬНОЕ БОКОВОЕ МЕНЮ -->
        <!-- ================================================= -->

        <div
            class="offcanvas offcanvas-start"
            tabindex="-1"
            id="mobileSidebar"
            aria-labelledby="mobileSidebarLabel">

            <!-- Заголовок -->
            <div class="offcanvas-header">

                <h5
                    class="offcanvas-title"
                    id="mobileSidebarLabel">
                    Меню
                </h5>

                <button
                    type="button"
                    class="btn-close text-reset"
                    data-bs-dismiss="offcanvas"
                    aria-label="Закрыть"></button>

            </div>


            <!-- Содержимое меню -->
            <div class="offcanvas-body">

                <!-- Основные пункты -->
                <nav class="nav flex-column">

                    @auth

                    @if(auth()->user()->is_admin)

                    <a
                        class="nav-link"
                        href="{{ route('admin.orders') }}">
                        Заказы
                    </a>

                    <hr class="my-1">


                    <a
                        class="nav-link"
                        href="{{ route('admin.database') }}">
                        База товаров
                    </a>

                    <hr class="my-1">


                    <a
                        class="nav-link"
                        href="{{ route('admin.products.selectCreateForm') }}">
                        Добавить товар
                    </a>

                    <hr class="my-1">


                    <a
                        class="nav-link"
                        href="{{ route('admin.users') }}">
                        Пользователи
                    </a>

                    <hr class="my-1">


                    <a
                        class="nav-link"
                        href="{{ route('admin.sales.select_warehouse') }}">
                        Продажи
                    </a>

                    <hr class="my-1">


                    <!-- ПРИЁМКИ -->
                    <a
                        class="nav-link"
                        href="{{ route('admin.receipts.index') }}">
                        Приёмки
                    </a>

                    <hr class="my-1">


                    <!-- СПИСАНИЯ -->
                    <a
                        class="nav-link"
                        href="{{ route('admin.writeoffs.index') }}">
                        Списания
                    </a>

                    <hr class="my-1">


                    <a
                        class="nav-link"
                        href="{{ route('admin.warehouses.overview') }}">
                        Остатки товаров
                    </a>

                    <hr class="my-1">


                    <!-- АНАЛИТИКА ПРИЁМОК -->
                    <a
                        class="nav-link"
                        href="{{ route('admin.analytics.receipts') }}">
                        Аналитика приёмок
                    </a>

                    <hr class="my-1">


                    @if(auth()->user()->is_owner)

                    <a
                        class="nav-link"
                        href="{{ route('admin.analytics.menu') }}">
                        Аналитика
                    </a>

                    <hr class="my-1">

                    @endif


                    @endif

                    @endauth


                    <!-- Для неавторизованных -->
                    @guest

                    <a
                        class="nav-link"
                        href="{{ route('login') }}">
                        Login
                    </a>

                    <hr class="my-1">


                    @if (Route::has('register'))

                    <a
                        class="nav-link"
                        href="{{ route('register') }}">
                        Register
                    </a>

                    @endif

                    @endguest

                </nav>


                <!-- ================================================= -->
                <!-- КНОПКА ВЫХОДА — В САМОМ НИЗУ -->
                <!-- ================================================= -->

                @auth

                <div class="mobile-logout">

                    <hr>

                    <form
                        action="{{ route('logout') }}"
                        method="POST">

                        @csrf

                        <button type="submit">
                            Выйти
                        </button>

                    </form>

                </div>

                @endauth

            </div>

        </div>


        <!-- Основной контент -->
        <main class="py-4">
            @yield('content')
        </main>

    </div>


    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>