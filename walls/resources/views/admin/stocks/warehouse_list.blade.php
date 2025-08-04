@extends('layouts.app')

@section('title', 'Склады')

@section('content')
<div class="container py-4">
    <h2>Список складов</h2>

    <div class="mb-3">
        <a href="{{ route('admin.stocks.view_all') }}" class="btn btn-outline-dark">
            Все партии по всем складам
        </a>
    </div>

    <div class="row">
        @foreach ($warehouses as $warehouse)
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $warehouse->name }}</h5>
                    <p class="card-text">ID: {{ $warehouse->id }}</p>
                    <a href="{{ route('admin.stocks.batch_overview', ['id' => $warehouse->id]) }}" class="btn btn-sm btn-outline-primary">
                        Смотреть партии склада
                    </a>

                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection