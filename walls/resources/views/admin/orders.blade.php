@extends('layouts.app') 

@section('title', 'Заказы')

@section('content')
<div class="container py-5">
    <h1 class="fw-semibold mb-4">Заказы</h1>

    <div class="d-flex flex-column flex-md-row gap-3">
        <a href="{{ route('admin.orders.website') }}" class="btn btn-primary btn-lg rounded-pill px-4 py-3 text-center">
            Заказы с сайта
        </a>

        <a href="{{ route('admin.orders.seller') }}" class="btn btn-success btn-lg rounded-pill px-4 py-3 text-center">
            Заказы продавцов
        </a>
    </div>
</div>

<style>
    @media (max-width: 576px) {
        .btn-lg {
            font-size: 0.9rem;
            padding: 0.75rem 1.5rem;
        }
    }
</style>
@endsection