@extends('layouts.app')

@section('title', 'Выбор склада')

@section('content')
<div class="container py-4">
    <h5 class="mb-3">Выберите склад</h5>

    <div class="list-group">
        @foreach ($warehouses as $warehouse)
            <a href="{{ route('admin.sales.index', ['warehouse_id' => $warehouse->id]) }}" class="list-group-item list-group-item-action">
                {{ $warehouse->name }}
            </a>
        @endforeach
    </div>
</div>
@endsection
