<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="ZokMn5I_BU2juvru-gwsvFf6N1jPVzyeNN5T5oEbPcc" />

    {{-- 🔹 SEO: базовые мета-теги --}}
    <title>@yield('title', 'RAFY WALLS — магазин обоев в Алматы | Современные коллекции')</title>
    <meta name="description" content="@yield('description', 'RAFY WALLS — магазин современных обоев в Алматы. Флизелиновые, виниловые и моющиеся обои. Горячее тиснение, модные коллекции, доставка по Казахстану.')">
    <meta name="keywords" content="обои Алматы, rafy walls, RAFY WALLS, магазин обоев Алматы, купить обои в Алматы, моющиеся обои Алматы, флизелиновые обои Алматы, виниловые обои Алматы, горячее тиснение, интерьер Алматы, Kazakhstan, обои Казахстан">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- 🔹 Open Graph (для соцсетей и мессенджеров) --}}
    <meta property="og:title" content="@yield('title', 'RAFY WALLS — магазин обоев в Алматы')">
    <meta property="og:description" content="@yield('description', 'Современные коллекции обоев RAFY WALLS — модные оттенки, горячее тиснение, доставка по Казахстану.')">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ru_KZ">

    {{-- 🔹 Twitter Card (для X / Telegram превью) --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="RAFY WALLS — магазин обоев в Алматы">
    <meta name="twitter:description" content="Современные обои RAFY WALLS в Алматы. Модные коллекции, горячее тиснение, доставка по Казахстану.">
    <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">

    {{-- ✅ Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/лого1.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/лого1.png') }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('images/лого1.png') }}">

    @yield('meta')
    @yield('styles')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    @include('partials.navbar')
    @yield('content')

    {{-- 🔹 Schema.org JSON-LD (локальное SEO и карта Google) --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Store",
      "name": "RAFY WALLS",
      "alternateName": [
        "Rafy Walls",
        "rafywalls",
        "RAFYWALLS",
        "rafy walls"
      ],
      "url": "https://rafywalls.com",
      "logo": "https://rafywalls.com/images/лого1.png",
      "image": "https://rafywalls.com/images/og-image.jpg",
      "description": "Магазин современных обоев RAFY WALLS в Алматы. Горячее тиснение, флизелиновые и моющиеся обои. Доставка по всему Казахстану.",
      "address": [
        {
          "@type": "PostalAddress",
          "streetAddress": "Проспект Турара Рыскулова, 103/2, бутик 105",
          "addressLocality": "Алматы",
          "postalCode": "050000",
          "addressCountry": "Казахстан"
        },
        {
          "@type": "PostalAddress",
          "streetAddress": "Проспект Турара Рыскулова, 103/2, бутик 109",
          "addressLocality": "Алматы",
          "postalCode": "050000",
          "addressCountry": "Казахстан"
        }
      ],
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday",
          "Sunday"
        ],
        "opens": "09:00",
        "closes": "17:00"
      },
      "telephone": "+7 7121 255",
      "priceRange": "₸₸",
      "sameAs": [
        "https://www.instagram.com/rafy_walls"
      ],
      "keywords": [
        "RAFY WALLS",
        "rafywalls",
        "Rafy Walls",
        "купить обои в Алматы",
        "обои Алматы",
        "магазин обоев Алматы",
        "моющиеся обои Алматы",
        "виниловые обои Алматы",
        "флизелиновые обои Алматы",
        "современные обои Алматы",
        "обои Казахстан",
        "обои для спальни Алматы"
      ]
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
