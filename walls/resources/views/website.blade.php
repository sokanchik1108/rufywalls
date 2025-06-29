@extends('layouts.main')

@section('title', 'Веб-сайт')

@section('content')

@include('sections.sale')

@include('sections.title')

@include('sections.about-products')

@include('sections.cards')

@include('sections.delivery')

@include('sections.contacts')

@include('partials.footer')


@endsection