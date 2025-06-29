@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <h3 class="fw-semibold mb-0 text-primary-emphasis">👥 Пользователи</h3>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill"> Назад</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            {{ implode(', ', $errors->all()) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
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
                    <th class="text-end">Действие</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="text-center text-muted">{{ $user->id }}</td>
                        <td class="fw-medium">{{ $user->name }}</td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="badge rounded-pill {{ $user->is_admin ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $user->is_admin ? 'Админ' : 'Пользователь' }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.toggleAdmin', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill {{ $user->is_admin ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $user->is_admin ? 'Снять админа' : 'Сделать админом' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-muted fst-italic">Это вы</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Нет зарегистрированных пользователей.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('styles')
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
@endpush
@endsection
