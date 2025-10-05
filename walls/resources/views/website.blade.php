@extends('layouts.main')

@section('title', 'Купить обои в Алматы — качественные моющиеся современные коллекции | RAFY WALLS')

@section('meta')
<meta name="description" content="Магазин обоев в Алматы — качественные моющиеся флизелиновые обои и современные коллекции с широким выбором. Артекс, Maxdecor, Dilmax, российские, китайские, узбекские.">
@endsection

@section('content')

@include('sections.title')

@include('sections.about-products')

@include('sections.cards')

@include('sections.about-us')

@include('partials.footer')


@endsection