@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-dark fw-semibold">
                        {{ __('Панель администратора') }}
                    </h4>
                </div>

                <div class="card-body">

                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Закрыть"></button>
                    </div>
                    @endif


                    <p class="text-muted">
                        Вы вошли как
                        <strong>{{ Auth::user()->email }}</strong>
                    </p>




                    @if(
                    !auth()->user()->is_owner &&
                    !auth()->user()->is_admin &&
                    !auth()->user()->can_view_analytics
                    )

                    <div class="row g-3 mt-4">

                        {{-- Стать владельцем --}}
                        <div class="col-md-4">
                            <a href="{{ route('admin.make.me.owner') }}"
                                class="btn btn-outline-warning w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">
                                <span class="me-2">👑</span>
                                <span>Стать владельцем</span>
                            </a>
                        </div>

                        {{-- Стать администратором --}}
                        <div class="col-md-4">
                            <a href="{{ route('admin.make.me.admin') }}"
                                class="btn btn-outline-success w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">
                                <span class="me-2">🛡️</span>
                                <span>Стать администратором</span>
                            </a>
                        </div>

                        {{-- Стать аналитиком --}}
                        <div class="col-md-4">
                            <a href="{{ route('admin.make.me.can.view.analytics') }}"
                                class="btn btn-outline-primary w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">
                                <span class="me-2">📊</span>
                                <span>Стать аналитиком</span>
                            </a>
                        </div>

                    </div>

                    @endif


                    {{-- Основные функции администратора --}}
                    @if(auth()->user()->is_admin)

                    <div class="row g-3 mt-4">

                        <div class="col-md-3">
                            <a href="{{ route('admin.orders') }}"
                                class="btn btn-outline-primary w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">

                                <span class="me-2">📦</span>
                                <span>Заказы</span>

                            </a>
                        </div>


                        <div class="col-md-3">
                            <a href="{{ route('admin.database') }}"
                                class="btn btn-outline-secondary w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">

                                <span class="me-2">📋</span>
                                <span>База товаров</span>

                            </a>
                        </div>


                        <div class="col-md-3">
                            <a href="{{ route('admin.products.selectCreateForm') }}"
                                class="btn btn-outline-success w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">

                                <span class="me-2">➕</span>
                                <span>Добавить товар</span>

                            </a>
                        </div>


                        {{-- Пользователи --}}
                        @if(auth()->user()->is_owner)
                        <div class="col-md-3">
                            <a href="{{ route('admin.users') }}"
                                class="btn btn-outline-dark w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">

                                <span class="me-2">👥</span>
                                <span>Пользователи</span>

                            </a>
                        </div>
                        @endif

                        {{-- Склады --}}
                        <div class="col-md-3">
                            <a href="{{ route('admin.stocks.warehouses') }}"
                                class="btn btn-outline-info w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">

                                <span class="me-2">🏬</span>
                                <span>Добавить товар на склад</span>

                            </a>
                        </div>


                        {{-- Остатки --}}
                        <div class="col-md-3">
                            <a href="{{ route('admin.warehouses.overview') }}"
                                class="btn btn-outline-secondary w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">

                                <span class="me-2">📂</span>
                                <span>Остатки товаров</span>

                            </a>
                        </div>


                        {{-- Аналитика --}}
                        @if(auth()->user()->is_owner)

                        <div class="col-md-3">
                            <a href="{{ route('admin.analytics.menu') }}"
                                class="btn btn-outline-primary w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center">

                                <span class="me-2">📊</span>
                                <span>Аналитика</span>

                            </a>
                        </div>

                        @endif

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