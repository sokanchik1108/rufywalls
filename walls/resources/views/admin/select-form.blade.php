@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-5 text-center fw-semibold">Выберите тип формы добавления товара</h2>

    <div class="row justify-content-center g-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition">
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="bi bi-box-seam-fill fs-1 text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold">Обычный товар</h5>
                        <p class="card-text text-muted">Полная форма с изображениями, описанием и параметрами.</p>
                    </div>
                    <a href="{{ route('admin.form') }}" class="btn btn-primary w-100 mt-4">Добавить обычный товар</a>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100 hover-shadow transition">
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="bi bi-eye-slash-fill fs-1 text-secondary"></i>
                        </div>
                        <h5 class="card-title fw-bold">Скрытый товар</h5>
                        <p class="card-text text-muted">Минимальная форма с оттенками и артикулами. Не будет отображаться в каталоге.</p>
                    </div>
                    <a href="{{ route('admin.hidden.form') }}" class="btn btn-outline-secondary w-100 mt-4">Добавить скрытый товар</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1.2rem rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-2px);
        transition: all 0.2s ease-in-out;
    }
</style>
@endsection
