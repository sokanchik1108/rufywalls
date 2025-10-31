@extends('layouts.main')

{{-- 🔹 Title --}}
@section('title', 'Каталог обоев RAFY WALLS — купить флизелиновые, виниловые и моющиеся обои в Алматы и Казахстане')

{{-- 🔹 Meta Description --}}
@section('meta_description', 'Каталог обоев RAFY WALLS в Алматы и по Казахстану. Современные коллекции: флизелиновые, виниловые, моющиеся обои. Выбирайте стильные обои для интерьера — доставка по Казахстану.')

{{-- 🔹 Дополнительные мета-теги --}}
@section('meta')
<meta name="description" content="@yield('meta_description')" />
<meta name="keywords" content="каталог обоев, RAFY WALLS, флизелиновые обои, виниловые обои, моющиеся обои, купить обои Алматы, обои Казахстан">
<link rel="canonical" href="{{ url('/catalog') }}">

{{-- Open Graph --}}
<meta property="og:title" content="Каталог обоев RAFY WALLS — стильные обои для вашего интерьера">
<meta property="og:description" content="@yield('meta_description')">
<meta property="og:image" content="{{ asset('images/баннер8.webp') }}">
<meta property="og:url" content="{{ url('/catalog') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="RAFY WALLS">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Каталог обоев RAFY WALLS — стильные обои для интерьера">
<meta name="twitter:description" content="@yield('meta_description')">
<meta name="twitter:image" content="{{ asset('images/баннер8.webp') }}">
@endsection

@section('content')

<!-- 🔹 Скрытый H1 для SEO -->
<h1 style="position:absolute; left:-9999px; top:-9999px;">
    Каталог обоев RAFY WALLS — купить обои в Алматы и по Казахстану
</h1>



@include('sections.title')

<div class="catalog-container">
    @include('partials.filters')
    <div class="product-list" id="product-container">
        @include('partials.products')
    </div>
</div>

@include('partials.footer')

{{-- 🔹 Structured Data (Schema.org) --}}
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Store",
        "name": "RAFY WALLS",
        "image": "{{ asset('images/баннер8.webp') }}",
        "description": "Магазин обоев RAFY WALLS в Алматы — каталог флизелиновых, виниловых и моющихся обоев.",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "проспект Турара Рыскулова, 103/2, бутики 105 и 109",
            "addressLocality": "Алматы",
            "addressCountry": "KZ"
        },
        "url": "{{ url('/catalog') }}",
        "priceRange": "₸₸",
        "telephone": "+7 700 000 0000"
    }
</script>

@section('styles')
<link rel="stylesheet" href="{{ asset('css/catalog.min.css') }}?v=1.0.0">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    window.catalogRoutes = {
        catalog: "{{ route('catalog') }}",
        autocomplete: "{{ route('catalog.autocomplete') }}"
    };
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // ======================== Ленивые карусели ========================
        window.initLazyCarousel = function(carousels) {
            carousels.forEach(carousel => {
                carousel.addEventListener("slide.bs.carousel", function(event) {
                    const currentSlide = event.relatedTarget;
                    const nextSlide = currentSlide.nextElementSibling;
                    const prevSlide = currentSlide.previousElementSibling;

                    [currentSlide, nextSlide, prevSlide].forEach(slide => {
                        if (!slide) return;
                        const img = slide.querySelector("img.lazy-slide");
                        if (img && img.dataset.src && img.src !== img.dataset.src) {
                            img.src = img.dataset.src;
                        }
                    });
                });

                const first = carousel.querySelector(".carousel-item.active");
                const second = first?.nextElementSibling;

                [first, second].forEach(slide => {
                    if (!slide) return;
                    const img = slide.querySelector("img.lazy-slide");
                    if (img && img.dataset.src && img.src !== img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                });
            });
        };

    });
</script>



@endsection