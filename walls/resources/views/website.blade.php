@extends('layouts.main')

{{-- 🔹 Title (ключевые слова + бренд + регион) --}}
@section('title', 'Обои в Алматы — купить современные моющиеся обои | RAFY WALLS магазин обоев')

{{-- 🔹 Meta Description (для SEO, Open Graph и Twitter) --}}
@section('meta_description', 'Купить обои в Алматы в магазине RAFY WALLS. Современные флизелиновые, виниловые и моющиеся обои. Новые коллекции Artex, Maxdecor, Dilmax и других брендов.')

{{-- 🔹 Дополнительные мета-теги --}}
@section('meta')
<meta name="description" content="@yield('meta_description')" />
<meta name="keywords" content="обои Алматы, купить обои Алматы, магазин обоев Алматы, моющиеся обои Алматы, флизелиновые обои Алматы, виниловые обои Алматы, RAFY WALLS, современные обои, Artex, Maxdecor, Dilmax">
<link rel="canonical" href="{{ url('/') }}">
@endsection

@section('content')

    {{-- 🔹 Скрытый H1 для SEO (доступен поисковикам, но не виден пользователям) --}}
    <h1 class="visually-hidden">
        Купить обои в Алматы — RAFY WALLS магазин современных моющихся обоев
    </h1>

    {{-- 🔹 Основной контент сайта --}}
    @include('sections.title')
    @include('sections.about-products')
    @include('sections.cards')
    @include('sections.about-us')
    @include('sections.categories')

    @include('partials.footer')

@endsection

{{-- 🔹 CSS для скрытия H1, но сохранения его доступности поисковикам --}}
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
