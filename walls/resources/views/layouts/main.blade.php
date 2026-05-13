<!DOCTYPE html>
<html lang="ru" prefix="og: https://ogp.me/ns#">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  {{-- ✅ Верификация Google --}}
  <meta name="google-site-verification" content="ZokMn5I_BU2juvru-gwsvFf6N1jPVzyeNN5T5oEbPcc" />
  <meta name="google-site-verification" content="q2udgjCcwKMXdDsI1eoT1oWY9KbRaAHoS0cg2iWp0Es" />

   {{-- ✅ Верификация Yandex --}}
   <meta name="yandex-verification" content="13f5f8faad2e928e" />

  {{-- 🔹 Базовые SEO-теги --}}
  <title>@yield('title', 'RAFY WALLS — магазин обоев в Алматы | Флизелиновые, виниловые и моющиеся обои')</title>
  <meta name="description" content="@yield('meta_description', 'RAFY WALLS — магазин современных обоев в Алматы. Флизелиновые, виниловые и моющиеся обои от ведущих брендов. Доставка по Казахстану!')">
  <meta name="keywords" content="@yield('meta_keywords', 'обои Алматы, купить обои в Алматы, магазин обоев RAFY WALLS, флизелиновые обои Алматы, виниловые обои Алматы, интерьер, дизайн, Казахстан, обои Казахстан, настенные покрытия')">
  <meta name="robots" content="@yield('meta_robots', 'index, follow')">
  <meta name="author" content="RAFY WALLS">

  {{-- 🔹 Canonical --}}
  <link rel="canonical" href="@yield('meta_canonical', url()->current())">

  {{-- 🔹 Open Graph --}}
  <meta property="og:site_name" content="RAFY WALLS">
  <meta property="og:title" content="@yield('og_title', 'RAFY WALLS — магазин обоев в Алматы')">
  <meta property="og:description" content="@yield('og_description', 'Купить обои в Алматы — RAFY WALLS. Современные флизелиновые, виниловые и моющиеся обои. Новые коллекции Artex, Maxdecor, Dilmax и других брендов!')">
  <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
  <meta property="og:url" content="@yield('og_url', url()->current())">
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:locale" content="ru_KZ">

  {{-- 🔹 Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('og_title', 'RAFY WALLS — магазин обоев в Алматы')">
  <meta name="twitter:description" content="@yield('og_description', 'Флизелиновые, виниловые и моющиеся обои в Алматы. RAFY WALLS — современный магазин настенных покрытий.')">
  <meta name="twitter:image" content="@yield('og_image', asset('images/og-image.jpg'))">

  {{-- ✅ Favicon --}}
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/kurbanovwalls logo.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/kurbanovwalls logo.png') }}">
  <meta name="theme-color" content="#ffffff">

  {{-- 🔹 Structured Data (Schema.org) --}}
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "HomeGoodsStore",
      "name": "RAFY WALLS",
      "image": "https://rafywalls.com/images/og-image.jpg",
      "logo": "https://rafywalls.com/images/KURBANOV WALLS LOGO.png",
      "url": "https://rafywalls.com",
      "description": "Магазин обоев RAFY WALLS в Алматы. Флизелиновые, виниловые и моющиеся обои. Доставка по Казахстану.",
      "telephone": "+7 707 712 12 55",
      "priceRange": "₸₸",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Проспект Турара Рыскулова, 103/2, бутик 105",
        "addressLocality": "Алматы",
        "postalCode": "050000",
        "addressCountry": "KZ"
      },
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
      "sameAs": [
        "https://www.instagram.com/rafy_walls",
        "https://maps.app.goo.gl/5j2JSVYFDz8gZyKq7"
      ]
    }
  </script>


  {{-- 🔹 Расширяемые мета и стили страниц --}}
  @yield('meta')
  @yield('styles')
</head>

<body>
  @include('partials.navbar')
  @yield('content')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>