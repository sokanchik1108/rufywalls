@extends('layouts.app')

@section('title', 'Склады')

@section('content')
<div class="container pt-5">
    <h4 class="mb-4">Склады</h4>

    {{-- Уведомления --}}
    @if(session('success'))
        <div class="alert alert-success small">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger small">{{ session('error') }}</div>
    @endif

    {{-- Форма добавления склада --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h6 class="mb-3">Добавить склад</h6>
            <form method="POST" action="{{ route('warehouses.store') }}">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label for="name" class="form-label small mb-1">Название склада</label>
                        <input type="text" name="name" id="name"
                               class="form-control form-control-sm @error('name') is-invalid @enderror"
                               placeholder="Введите название склада" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <label for="address" class="form-label small mb-1">Адрес (необязательно)</label>
                        <input type="text" name="address" id="address"
                               class="form-control form-control-sm"
                               placeholder="Например, ул. Центральная, 12" value="{{ old('address') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark btn-sm w-100">➕ Добавить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Список складов --}}
    @if($warehouses->isEmpty())
        <p class="text-muted">Склады ещё не добавлены.</p>
    @else
        <div class="row">
            @foreach($warehouses as $warehouse)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-2">{{ $warehouse->name }}</h5>
                            @if($warehouse->address)
                                <p class="card-text text-muted small">{{ $warehouse->address }}</p>
                            @endif
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.stocks.edit_warehouse', $warehouse->id) }}"
                                   class="btn btn-dark btn-sm">
                                    Перейти к складу
                                </a>
                                <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST"
                                      onsubmit="return confirm('Удалить склад {{ $warehouse->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                        ✖
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
