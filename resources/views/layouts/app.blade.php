<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Фриланс Маркетплейс')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar-brand { font-weight: 700; font-size: 1.3rem; }
        .badge-role-freelancer { background: #0d6efd; }
        .badge-role-client     { background: #198754; }
        .badge-role-moderator  { background: #fd7e14; }
        .badge-role-admin      { background: #dc3545; }
        .card { border: 1px solid #dee2e6; border-radius: 8px; }
        .card-header { background: #fff; border-bottom: 1px solid #dee2e6; font-weight: 600; }
        .status-draft         { color: #6c757d; }
        .status-on_moderation { color: #fd7e14; }
        .status-published     { color: #198754; }
        .status-in_progress   { color: #0d6efd; }
        .status-completed     { color: #20c997; }
        .status-cancelled     { color: #dc3545; }
        .status-rejected      { color: #dc3545; }
        .notification-dot { width: 8px; height: 8px; background: #dc3545;
                            border-radius: 50%; display: inline-block; }
    </style>
    @stack('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-briefcase-fill me-1"></i> ФрилансМаркет
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}"
                       href="{{ route('orders.index') }}">Заказы</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('external.*') ? 'active' : '' }}"
                       href="{{ route('external.index') }}">Внешние заказы</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                @auth
                    {{-- Notifications --}}
                    @php
                        $unread = \App\Models\Notification::where('user_id', auth()->id())
                            ->where('is_read', false)->count();
                    @endphp
                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="{{ route('notifications.index') }}">
                            <i class="bi bi-bell fs-5"></i>
                            @if($unread > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $unread > 9 ? '9+' : $unread }}
                                </span>
                            @endif
                        </a>
                    </li>

                    {{-- User dropdown --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                           href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                            {{ auth()->user()->name }}
                            <span class="badge badge-role-{{ auth()->user()->role }} ms-1">
                                {{ __('roles.' . auth()->user()->role) }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(auth()->user()->isFreelancer())
                                <li><a class="dropdown-item" href="{{ route('profile.freelancer.edit') }}">
                                    <i class="bi bi-person me-2"></i>Мой профиль</a></li>
                                <li><a class="dropdown-item" href="{{ route('applications.my') }}">
                                    <i class="bi bi-send me-2"></i>Мои отклики</a></li>
                                <li><a class="dropdown-item" href="{{ route('saved-searches.index') }}">
                                    <i class="bi bi-bookmark me-2"></i>Сохранённые поиски</a></li>
                            @endif
                            @if(auth()->user()->isClient())
                                <li><a class="dropdown-item" href="{{ route('profile.client.edit') }}">
                                    <i class="bi bi-building me-2"></i>Профиль компании</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.my') }}">
                                    <i class="bi bi-list-ul me-2"></i>Мои заказы</a></li>
                            @endif
                            @if(auth()->user()->isModerator() || auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('moderator.dashboard') }}">
                                    <i class="bi bi-shield-check me-2"></i>Модерация</a></li>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-gear me-2"></i>Панель администратора</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Выйти
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Войти</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Регистрация</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- ALERTS --}}
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- CONTENT --}}
<main class="container my-4">
    @yield('content')
</main>

<footer class="bg-dark text-secondary py-4 mt-5">
    <div class="container text-center">
        <small>&copy; {{ date('Y') }} ФрилансМаркет. Все права защищены.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>