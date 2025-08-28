<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Веб-сайт')</title>
    @yield('meta')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    @yield('styles')

    {{-- ✅ Favicon для всех устройств --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/лого1.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/лого1.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/лого1.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/лого1.png') }}" type="image/x-icon">

    {{-- Для Android Chrome (цвет вкладки и иконка) --}}
    <meta name="theme-color" content="#ffffff">

    {{-- Для Windows плитки (если добавляют сайт) --}}
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('images/лого1.png') }}">
</head>

<body>

    @include('partials.navbar')

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
