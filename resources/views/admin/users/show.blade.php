@extends('layouts.admin')
@section('page_title', 'Профиль пользователя')

@section('admin_content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user me-2"></i>{{ $user->name }}
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted small">Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small">Роль</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                @csrf @method('PATCH')
                                <div class="input-group input-group-sm">
                                    <select name="role" class="form-select form-select-sm">
                                        @foreach(['freelancer','client','moderator','admin'] as $role)
                                            <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                                {{ ['freelancer'=>'Фрилансер','client'=>'Заказчик','moderator'=>'Модератор','admin'=>'Администратор'][$role] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-primary btn-sm">Сохранить</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted small">Статус</td>
                        <td>
                            @if($user->is_blocked)
                                <span class="badge bg-danger">Заблокирован</span>
                                <div class="small text-muted mt-1">{{ $user->block_reason }}</div>
                                <form method="POST" action="{{ route('admin.users.unblock', $user) }}" class="mt-2">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-success btn-sm">Разблокировать</button>
                                </form>
                            @else
                                <span class="badge bg-success">Активен</span>
                                <div class="mt-2" x-data="{ open: false }">
                                    <button class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#blockForm">
                                        Заблокировать
                                    </button>
                                    <div class="collapse mt-2" id="blockForm">
                                        <form method="POST" action="{{ route('admin.users.block', $user) }}" class="d-flex gap-2">
                                            @csrf @method('PATCH')
                                            <input type="text" name="reason" class="form-control form-control-sm"
                                                   placeholder="Причина...">
                                            <button type="submit" class="btn btn-danger btn-sm">ОК</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted small">Зарегистрирован</td>
                        <td>{{ $user->created_at->format('d.m.Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if($user->isFreelancer() && $user->freelancerProfile)
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-user-tie me-2"></i>Профиль фрилансера</div>
                <div class="card-body small">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <span class="text-muted">Специализация:</span>
                            {{ $user->freelancerProfile->specialization ?? '—' }}
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Ставка:</span>
                            {{ $user->freelancerProfile->hourly_rate ? number_format($user->freelancerProfile->hourly_rate,0,'.',' ') . ' ₽/ч' : '—' }}
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Рейтинг:</span>
                            ⭐ {{ number_format($user->freelancerProfile->rating, 1) }}
                            ({{ $user->freelancerProfile->reviews_count }} отзывов)
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Доступен:</span>
                            {{ $user->freelancerProfile->is_available ? 'Да' : 'Нет' }}
                        </div>
                    </div>
                    @if($user->freelancerProfile->skills->count())
                        <div class="mt-2">
                            <span class="text-muted">Навыки:</span>
                            @foreach($user->freelancerProfile->skills as $skill)
                                <span class="badge bg-light text-dark border">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($user->isClient() && $user->clientProfile)
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-building me-2"></i>Профиль заказчика</div>
                <div class="card-body small">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <span class="text-muted">Компания:</span>
                            {{ $user->clientProfile->company_name ?? '—' }}
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Рейтинг:</span>
                            ⭐ {{ number_format($user->clientProfile->rating, 1) }}
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Телефон:</span>
                            {{ $user->clientProfile->phone ?? '—' }}
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Контакт подтверждён:</span>
                            {{ $user->clientProfile->contact_verified ? '✓ Да' : '✗ Нет' }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-file-alt me-2"></i>Заказы ({{ $user->orders->count() }})</div>
            @if($user->orders->isEmpty())
                <div class="card-body text-muted small">Нет заказов.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Название</th><th>Статус</th><th>Дата</th></tr>
                        </thead>
                        <tbody>
                            @foreach($user->orders->take(10) as $order)
                                <tr>
                                    <td class="small">{{ Str::limit($order->title, 40) }}</td>
                                    <td><span class="badge bg-secondary">{{ $order->status }}</span></td>
                                    <td class="small text-muted">{{ $order->created_at->format('d.m.Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-paper-plane me-2"></i>Отклики ({{ $user->applications->count() }})</div>
            @if($user->applications->isEmpty())
                <div class="card-body text-muted small">Нет откликов.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Заказ ID</th><th>Статус</th><th>Дата</th></tr>
                        </thead>
                        <tbody>
                            @foreach($user->applications->take(10) as $app)
                                <tr>
                                    <td class="small">#{{ $app->order_id }}</td>
                                    <td><span class="badge bg-secondary">{{ $app->status }}</span></td>
                                    <td class="small text-muted">{{ $app->created_at->format('d.m.Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection