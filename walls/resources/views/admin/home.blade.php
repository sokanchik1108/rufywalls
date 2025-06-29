@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-dark fw-semibold">{{ __('Панель администратора') }}</h4>

                    {{-- Кнопка перехода на сайт --}}
                    <a href="{{ route('website') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                        🌐 Перейти на сайт
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
                        </div>
                    @endif

                    <p class="text-muted">Вы вошли как <strong>{{ Auth::user()->email }}</strong></p>

                    @if(auth()->user()->is_admin)
                        <div class="row g-3 mt-4">
                            <div class="col-md-3">
                                <a href="{{ route('admin.orders') }}" class="btn btn-outline-primary w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">
                                    <span class="me-2">📦</span> <span>Заказы</span>
                                </a>
                            </div>

                            <div class="col-md-3">
                                <a href="{{ route('admin.database') }}" class="btn btn-outline-secondary w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">
                                    <span class="me-2">📋</span> <span>База товаров</span>
                                </a>
                            </div>

                            <div class="col-md-3">
                                <a href="{{ route('admin.form') }}" class="btn btn-outline-success w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">
                                    <span class="me-2">➕</span> <span>Добавить товар</span>
                                </a>
                            </div>

                            <div class="col-md-3">
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-dark w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">
                                    <span class="me-2">👥</span> <span>Пользователи</span>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mt-4">
                            У вас нет прав администратора.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
