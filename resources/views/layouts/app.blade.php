
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ФрилансМаркет')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        html {
            height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
            color: #212529;
        }

        main.container {
            flex: 1 0 auto;
        }

        footer {
            flex-shrink: 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.3px;
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #fff;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            color: #212529;
        }

        .badge-role-freelancer {
            background: #0d6efd;
        }

        .badge-role-client {
            background: #198754;
        }

        .badge-role-moderator {
            background: #fd7e14;
        }

        .badge-role-admin {
            background: #dc3545;
        }

        .notification-dot {
            width: 8px;
            height: 8px;
            background: #dc3545;
            border-radius: 50%;
            display: inline-block;
        }

        .table td,
        .table th {
            color: #212529;
        }

        .text-muted {
            color: #6c757d !important;
        }

        a {
            color: #0d6efd;
        }

        .form-label {
            color: #212529;
            font-weight: 500;
        }

        .alert {
            font-weight: 500;
        }
    </style>

    @stack('styles')
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">

        <a class="navbar-brand text-white" href="{{ route('home') }}">
            <i class="bi bi-briefcase-fill me-2 text-primary"></i>ФрилансМаркет
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

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('search.*') ? 'active' : '' }}"
                       href="{{ route('search.index') }}">
                        <i class="bi bi-search me-1"></i>Поиск
                    </a>
                </li>

            </ul>

            <ul class="navbar-nav ms-auto align-items-center">

                @auth

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

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                           href="#" data-bs-toggle="dropdown">

                            <i class="bi bi-person-circle fs-5"></i>

                            <span class="text-white">{{ auth()->user()->name }}</span>

                            <span class="badge badge-role-{{ auth()->user()->role }}">
                                {{ ['freelancer'=>'Фрилансер','client'=>'Заказчик','moderator'=>'Модератор','admin'=>'Администратор'][auth()->user()->role] }}
                            </span>

                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow">

                            @if(auth()->user()->isFreelancer())

                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.freelancer.edit') }}">
                                        <i class="bi bi-person me-2 text-primary"></i>Мой профиль
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('applications.my') }}">
                                        <i class="bi bi-send me-2 text-primary"></i>Мои отклики
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('saved-searches.index') }}">
                                        <i class="bi bi-bookmark me-2 text-primary"></i>Сохранённые поиски
                                    </a>
                                </li>

                            @endif

                            @if(auth()->user()->isClient())

                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.client.edit') }}">
                                        <i class="bi bi-building me-2 text-success"></i>Профиль компании
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('orders.my') }}">
                                        <i class="bi bi-list-ul me-2 text-success"></i>Мои заказы
                                    </a>
                                </li>

                            @endif

                            @if(auth()->user()->isModerator() || auth()->user()->isAdmin())

                                <li>
                                    <a class="dropdown-item" href="{{ route('moderator.dashboard') }}">
                                        <i class="bi bi-shield-check me-2 text-warning"></i>Модерация
                                    </a>
                                </li>

                            @endif

                            @if(auth()->user()->isAdmin())

                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-gear me-2 text-danger"></i>Администратор
                                    </a>
                                </li>

                            @endif

                            <li>
                                <hr class="dropdown-divider">
                            </li>

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
                        <a class="nav-link text-white" href="{{ route('login') }}">Войти</a>
                    </li>

                    <li class="nav-item ms-2">
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Регистрация</a>
                    </li>

                @endauth

            </ul>
        </div>
    </div>
</nav>

<div class="container mt-3">

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">

            <i class="bi bi-check-circle-fill me-2"></i>

            <span>{{ session('success') }}</span>

            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>

        </div>

    @endif

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center">

            <i class="bi bi-exclamation-circle-fill me-2"></i>

            <span>{{ session('error') }}</span>

            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>

        </div>

    @endif

    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <ul class="mb-0 mt-1">

                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach

            </ul>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    @endif

</div>

<main class="container my-4">
    @yield('content')
</main>

<footer class="bg-dark text-white py-4 mt-auto">

    <div class="container text-center">

        <div class="mb-2">
            <span class="fw-bold fs-5">
                <i class="bi bi-briefcase-fill me-2 text-primary"></i>ФрилансМаркет
            </span>
        </div>

        <div class="mb-2">

            <a href="{{ route('orders.index') }}"
               class="text-white-50 text-decoration-none me-3 small">
                Заказы
            </a>

            <a href="{{ route('external.index') }}"
               class="text-white-50 text-decoration-none me-3 small">
                Внешние заказы
            </a>

            <a href="{{ route('search.index') }}"
               class="text-white-50 text-decoration-none small">
                Поиск
            </a>

        </div>

        <div class="text-white-50 small mb-1">
            &copy; {{ date('Y') }} ФрилансМаркет. Все права защищены.
        </div>

        <div class="text-white-50 small">
            Разработано и создано:

            <span class="text-white fw-semibold">FATAO</span>
            &amp;&amp;
            <span class="text-white fw-semibold">SAVA</span>
        </div>

    </div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>
