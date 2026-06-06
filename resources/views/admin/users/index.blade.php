@extends('layouts.admin')
@section('page_title', 'Пользователи')
@section('page_actions')
    <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-outline-info">
        <i class="fas fa-chart-bar me-1"></i>Отчёты
    </a>
@endsection

@section('admin_content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="row g-2">
            <div class="col-sm-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Имя или email..." value="{{ request('search') }}">
            </div>
            <div class="col-sm-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Все роли</option>
                    <option value="freelancer" {{ request('role') === 'freelancer' ? 'selected' : '' }}>Фрилансер</option>
                    <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Заказчик</option>
                    <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>Модератор</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Администратор</option>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="blocked" class="form-select form-select-sm">
                    <option value="">Все</option>
                    <option value="0" {{ request('blocked') === '0' ? 'selected' : '' }}>Активные</option>
                    <option value="1" {{ request('blocked') === '1' ? 'selected' : '' }}>Заблокированные</option>
                </select>
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i>Найти
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Сбросить</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Статус</th>
                    <th>Зарегистрирован</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="{{ $user->is_blocked ? 'table-danger' : '' }}">
                        <td class="small text-muted">{{ $user->id }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
                        </td>
                        <td class="small">{{ $user->email }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <select name="role" class="form-select form-select-sm d-inline-block w-auto"
                                        onchange="this.form.submit()">
                                    @foreach(['freelancer','client','moderator','admin'] as $role)
                                        <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                            {{ __('roles.' . $role) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td>
                            @if($user->is_blocked)
                                <span class="badge bg-danger">Заблокирован</span>
                            @else
                                <span class="badge bg-success">Активен</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $user->created_at->format('d.m.Y') }}</td>
                        <td>
                            @if($user->is_blocked)
                                <form method="POST" action="{{ route('admin.users.unblock', $user) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success">Разблокировать</button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#block_{{ $user->id }}">
                                    Заблокировать
                                </button>
                            @endif
                        </td>
                    </tr>
                    @if(!$user->is_blocked)
                        <tr class="collapse" id="block_{{ $user->id }}">
                            <td colspan="7" class="bg-light py-2 px-4">
                                <form method="POST" action="{{ route('admin.users.block', $user) }}"
                                      class="d-flex gap-2">
                                    @csrf @method('PATCH')
                                    <input type="text" name="reason" class="form-control form-control-sm"
                                           placeholder="Причина блокировки..." style="max-width:300px">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Подтвердить
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection