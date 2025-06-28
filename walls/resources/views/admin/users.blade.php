@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-semibold mb-0">👥 Пользователи</h3>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">⬅ Назад</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ implode(', ', $errors->all()) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif

    <div class="table-responsive shadow-sm rounded-4 overflow-hidden">
        <table class="table table-hover align-middle mb-0">
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
                        <td class="text-center">{{ $user->id }}</td>
                        <td class="fw-medium">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->is_admin ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->is_admin ? 'Админ' : 'Пользователь' }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.toggleAdmin', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $user->is_admin ? 'btn-outline-danger' : 'btn-outline-success' }}">
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
@endsection
