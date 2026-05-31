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

  {{-- ✅ Верификация Pinterest --}}
  <meta name="p:domain_verify" content="3d2e66743df2716675422c5a8487e8dd" />

  {{-- 🔹 SEO --}}
  <title>
    @yield('title', 'KURBANOV WALLS — магазин обоев в Алматы | Флизелиновые, виниловые и моющиеся обои')
  </title>

  <meta name="description" content="@yield('meta_description',
  'KURBANOV WALLS — магазин современных обоев в Алматы. Виниловые обои на флизелиновой основе, моющиеся обои, дизайнерские коллекции от ведущих производителей России, Узбекистана и Китая. Доставка по всему Казахстану.')">

  <meta name="keywords" content="@yield('meta_keywords',
  'обои Алматы, купить обои Алматы, RAFY WALLS, KURBANOV WALLS, Kurbanov Walls, kurbanovwalls, курбанов воллс, магазин обоев Алматы, виниловые обои, флизелиновые обои, моющиеся обои, обои Казахстан, настенные покрытия, дизайн интерьера')">

  <meta name="robots" content="@yield('meta_robots', 'index, follow')">
  <meta name="author" content="KURBANOV WALLS">

  {{-- 🔹 Canonical --}}
  <link rel="canonical" href="@yield('meta_canonical', url()->current())">

  {{-- 🔹 Open Graph --}}
  <meta property="og:site_name" content="KURBANOV WALLS ">
  <meta property="og:title" content="@yield('og_title', 'KURBANOVWALLS  — магазин обоев в Алматы')">
  <meta property="og:description" content="@yield('og_description',
  'Купить обои в Алматы. Виниловые обои на флизелиновой основе, моющиеся обои, современные коллекции от ведущих производителей. Доставка по Казахстану.')">
  <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
  <meta property="og:url" content="@yield('og_url', url()->current())">
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:locale" content="ru_KZ">

  {{-- 🔹 Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('og_title', 'KURBANOV WALLS — магазин обоев в Алматы')">
  <meta name="twitter:description" content="@yield('og_description',
  'Виниловые, флизелиновые и моющиеся обои в Алматы. KURBANOV WALLS. Доставка по Казахстану.')">
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
      "name": "KURBANOV WALLS",
      "alternateName": [
        "Kurbanov Walls",
        "kurbanovwalls",
        "Курбанов Воллс"
      ],
      "image": "https://kurbanovwalls.kz/images/og-image.jpg",
      "logo": "https://kurbanovwalls.kz/images/kurbanovwalls logo.png",
      "url": "https://kurbanovwalls.kz",
      "description": "KURBANOV WALLS — магазин обоев в Алматы. Виниловые обои на флизелиновой основе, моющиеся обои. Доставка по всему Казахстану.",
      "telephone": "+7 777 355 57 04",
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
        "https://www.instagram.com/kurbanovwalls.kz",
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