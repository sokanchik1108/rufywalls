@extends('layouts.main')

{{-- 🔹 Title --}}
@section('title', 'Обои в Алматы — купить современные моющиеся обои | KURBANOV WALLS магазин обоев')

{{-- 🔹 Meta Description --}}
@section('meta_description', 'Купить обои в Алматы в магазине KURBANOV WALLS. Современные флизелиновые, виниловые и моющиеся обои. Новые коллекции Artex, Maxdecor, Dilmax и других брендов.')

{{-- 🔹 Open Graph / Twitter / Canonical --}}
@section('meta')
<link rel="canonical" href="{{ url()->current() }}">
<meta name="keywords" content="обои Алматы, купить обои Алматы, магазин обоев Алматы, моющиеся обои Алматы, флизелиновые обои Алматы, виниловые обои Алматы, KURBANOV WALLS, современные обои, Artex, Maxdecor, Dilmax">

{{-- Open Graph --}}
<meta property="og:title" content="@yield('title')">
<meta property="og:description" content="@yield('meta_description')">
<meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">

{{-- Twitter Card --}}
<meta name="twitter:title" content="@yield('title')">
<meta name="twitter:description" content="@yield('meta_description')">
<meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')

    {{-- 🔹 Скрытый H1 для SEO --}}
    <h1 class="visually-hidden">
        Купить обои в Алматы — KURBANOV WALLS магазин современных моющихся обоев
    </h1>

    {{-- 🔹 Основной контент --}}
    @include('sections.about-products')
    @include('sections.cards')
    @include('sections.about-us')
    @include('sections.categories')

    @include('partials.footer')

@endsection

@push('styles')
<style>
.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0 0 0 0);
    white-space: nowrap;
    border: 0;
}
</style>
@endpush
