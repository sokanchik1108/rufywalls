@extends('layouts.app')

@section('title', 'Пользователи')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <h3 class="fw-semibold mb-0 text-primary-emphasis">
            👥 Пользователи
        </h3>

        <a href="{{ route('home') }}"
           class="btn btn-outline-secondary rounded-pill">
            Назад
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm"
             role="alert">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Закрыть"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm"
             role="alert">

            {{ implode(', ', $errors->all()) }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Закрыть"></button>
        </div>
    @endif


    <div class="table-responsive rounded-4 border shadow-sm">

        <table class="table table-hover table-sm align-middle mb-0 text-nowrap">

            <thead class="table-light">
                <tr>
                    <th class="text-center">#</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Точка продаж</th>
                    <th class="text-end">Действие</th>
                </tr>
            </thead>

            <tbody>

                @forelse($users as $user)

                    <tr>

                        {{-- ID --}}
                        <td class="text-center text-muted">
                            {{ $user->id }}
                        </td>


                        {{-- Имя --}}
                        <td class="fw-medium">
                            {{ $user->name }}
                        </td>


                        {{-- Email --}}
                        <td class="text-muted">
                            {{ $user->email }}
                        </td>


                        {{-- Роль --}}
                        <td>

                            @if($user->is_owner)

                                <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                                    Владелец
                                </span>

                            @elseif($user->is_admin)

                                <span class="badge rounded-pill bg-success-subtle text-success">
                                    Админ
                                </span>

                            @else

                                <span class="badge rounded-pill bg-secondary-subtle text-secondary">
                                    Пользователь
                                </span>

                            @endif

                        </td>


                        {{-- Точка продаж --}}
                        <td>

                            @if($user->id === auth()->id())

                                <span class="text-muted">
                                    {{ $user->pointOfSale->name ?? 'Не назначена' }}
                                </span>

                            @else

                                <form action="{{ route('admin.users.pointOfSale', $user) }}"
                                      method="POST"
                                      class="d-flex gap-2 align-items-center">

                                    @csrf

                                    <select name="point_of_sale_id"
                                            class="form-select form-select-sm point-of-sale-select">

                                        <option value="">
                                            Не назначена
                                        </option>

                                        @foreach($pointsOfSale as $point)

                                            <option value="{{ $point->id }}"
                                                {{ (int) $user->point_of_sale_id === (int) $point->id ? 'selected' : '' }}>

                                                {{ $point->name }}

                                            </option>

                                        @endforeach

                                    </select>

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-primary rounded-pill">

                                        Сохранить

                                    </button>

                                </form>

                            @endif

                        </td>


                        {{-- Действия --}}
                        <td class="text-end">

                            {{-- Владелец --}}
                            @if($user->is_owner)

                                <span class="text-warning-emphasis fw-semibold">
                                    Владелец
                                </span>


                            {{-- Текущий пользователь --}}
                            @elseif($user->id === auth()->id())

                                <span class="text-muted fst-italic">
                                    Это вы
                                </span>


                            {{-- Остальные пользователи --}}
                            @else

                                <div class="d-inline-flex gap-2">

                                    {{-- Сделать / снять админа --}}
                                    <form action="{{ route('admin.toggleAdmin', $user) }}"
                                          method="POST">

                                        @csrf

                                        <button type="submit"
                                                class="btn btn-sm rounded-pill
                                                {{ $user->is_admin
                                                    ? 'btn-outline-danger'
                                                    : 'btn-outline-success' }}">

                                            {{ $user->is_admin
                                                ? 'Снять админа'
                                                : 'Сделать админом' }}

                                        </button>

                                    </form>


                                    {{-- Удалить пользователя --}}
                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                          method="POST"
                                          onsubmit="return confirm('Вы уверены, что хотите удалить пользователя {{ $user->name }}?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-pill">

                                            Удалить

                                        </button>

                                    </form>

                                </div>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6"
                            class="text-center text-muted py-4">

                            Нет зарегистрированных пользователей.

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>



<style>

    body {
        background-color: #f8f9fa;
    }

    h3 {
        font-size: 1.5rem;
    }

    .table thead th {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .table td {
        font-size: 0.92rem;
    }

    .badge {
        font-weight: 600;
        padding: 0.45em 0.75em;
    }

    .point-of-sale-select {
        min-width: 190px;
    }

    @media (max-width: 576px) {

        h3 {
            font-size: 1.25rem;
        }

        .btn {
            font-size: 0.85rem;
            padding: 0.45rem 1rem;
        }

        .table td,
        .table th {
            font-size: 0.82rem;
        }

        .point-of-sale-select {
            min-width: 160px;
        }

    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table th,
    .table td {
        white-space: nowrap;
    }

</style>



@endsection