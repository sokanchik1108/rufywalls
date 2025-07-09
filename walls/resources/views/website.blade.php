@extends('layouts.main')

@section('title', 'Главная - RAFY WALLS')

@section('meta')
    <meta name="description" content="RAFY WALLS — стильные обои с быстрым самовывозом и доставкой. Подчеркни характер интерьера с нашей коллекцией.">
@endsection


@section('content')

@include('sections.sale')

@include('sections.title')

@include('sections.about-products')

@include('sections.cards')

@include('sections.delivery')

@include('sections.contacts')

@include('partials.footer')


@endsection