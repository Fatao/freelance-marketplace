<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ФрилансМаркет — Биржа фриланса</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; }

        .hero {
            background: #0d1117;
            color: #fff;
            padding: 100px 0 80px;
            border-bottom: 3px solid #0d6efd;
        }
        .hero h1 {
            font-size: 3.2rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.15;
        }
        .hero h1 span { color: #0d6efd; }
        .hero p {
            font-size: 1.2rem;
            color: #adb5bd;
            max-width: 560px;
        }
        .hero-btns .btn { font-size: 1rem; padding: 12px 32px; }

        .stats-bar {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 28px 0;
        }
        .stat-item { text-align: center; }
        .stat-item .number {
            font-size: 2rem;
            font-weight: 800;
            color: #0d6efd;
            display: block;
        }
        .stat-item .label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .how-it-works { padding: 80px 0; }
        .step-icon {
            width: 64px; height: 64px;
            background: #e7f0ff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            color: #0d6efd;
            margin: 0 auto 16px;
        }

        .roles-section {
            background: #f8f9fa;
            padding: 80px 0;
            border-top: 1px solid #dee2e6;
        }
        .role-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 36px 28px;
            height: 100%;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .role-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 4px 20px rgba(13,110,253,0.1);
        }
        .role-card .icon {
            font-size: 2.4rem;
            margin-bottom: 16px;
            display: block;
        }
        .role-card h4 { font-weight: 700; margin-bottom: 12px; }
        .role-card ul { padding-left: 18px; color: #495057; }
        .role-card ul li { margin-bottom: 6px; font-size: 0.95rem; }

        .features { padding: 80px 0; }
        .feature-item { padding: 24px; border-radius: 8px; }
        .feature-item i { font-size: 2rem; color: #0d6efd; margin-bottom: 12px; display: block; }
        .feature-item h5 { font-weight: 700; }

        .cta-section {
            background: #0d6efd;
            color: #fff;
            padding: 70px 0;
        }
        .cta-section h2 { font-size: 2.2rem; font-weight: 800; }
        .cta-section p { font-size: 1.1rem; opacity: 0.9; }
        .cta-section .btn-light { font-weight: 600; padding: 12px 36px; }

        .navbar-brand { font-weight: 800; font-size: 1.3rem; letter-spacing: -0.5px; }
        footer { background: #0d1117; color: #6c757d; padding: 40px 0; }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand text-white" href="/">
            <i class="bi bi-briefcase-fill me-2 text-primary"></i>ФрилансМаркет
        </a>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Войти</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Регистрация</a>
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="mb-3">
                    <span class="badge bg-primary bg-opacity-25 text-primary px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-lightning-charge me-1"></i>Профессиональная биржа фриланса
                    </span>
                </div>
                <h1>
                    Найдите лучших<br>
                    <span>фрилансеров</span><br>
                    или заказы мечты
                </h1>
                <p class="mt-4 mb-4">
                    Размещайте проекты, откликайтесь на заказы, работайте с профессионалами.
                    Умный краулер собирает заказы с внешних площадок автоматически.
                </p>
                <div class="hero-btns d-flex gap-3 flex-wrap">
                    <a href="{{ route('orders.index') }}" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Найти заказы
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light">
                        <i class="bi bi-person-plus me-2"></i>Начать бесплатно
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="bg-dark border border-secondary rounded-3 p-4" style="border-color: #1f2937 !important;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width:12px;height:12px;border-radius:50%;background:#dc3545;"></div>
                        <div style="width:12px;height:12px;border-radius:50%;background:#ffc107;"></div>
                        <div style="width:12px;height:12px;border-radius:50%;background:#198754;"></div>
                        <small class="text-secondary ms-2">Новый заказ</small>
                    </div>
                    <div class="mb-3 p-3 rounded" style="background:#161b22;border:1px solid #30363d;">
                        <div class="text-white fw-semibold mb-1">Разработка Laravel API</div>
                        <div class="text-secondary small mb-2">Бэкенд · REST API · MySQL</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">80 000 – 120 000 ₽</span>
                            <span class="badge bg-success">Опубликован</span>
                        </div>
                    </div>
                    <div class="mb-3 p-3 rounded" style="background:#161b22;border:1px solid #30363d;">
                        <div class="text-white fw-semibold mb-1">UI/UX дизайн приложения</div>
                        <div class="text-secondary small mb-2">Дизайн · Figma · Прототипирование</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">35 000 – 55 000 ₽</span>
                            <span class="badge bg-success">Опубликован</span>
                        </div>
                    </div>
                    <div class="p-3 rounded" style="background:#161b22;border:1px solid #30363d;">
                        <div class="text-white fw-semibold mb-1">SEO-продвижение сайта</div>
                        <div class="text-secondary small mb-2">Маркетинг · SEO · Контент</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">от 20 000 ₽/мес</span>
                            <span class="badge bg-warning text-dark">На модерации</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="stats-bar">
    <div class="container">
        <div class="row g-4">
            @php
                $stats = [
                    ['Активных заказов',   \App\Models\Order::where('status','published')->count(),    'bi-file-text'],
                    ['Фрилансеров',        \App\Models\User::where('role','freelancer')->count(),       'bi-people'],
                    ['Заказчиков',         \App\Models\User::where('role','client')->count(),           'bi-building'],
                    ['Завершённых заказов',\App\Models\Order::where('status','completed')->count(),     'bi-check-circle'],
                ];
            @endphp
            @foreach($stats as [$label, $value, $icon])
                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <i class="bi {{ $icon }} text-primary mb-1 fs-4"></i>
                        <span class="number">{{ number_format($value) }}</span>
                        <span class="label">{{ $label }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="how-it-works">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-800" style="font-weight:800;">Как это работает</h2>
            <p class="text-muted">Три шага до успешного сотрудничества</p>
        </div>
        <div class="row g-4 text-center">
            @php
                $steps = [
                    ['bi-person-plus','Зарегистрируйтесь','Создайте аккаунт за 30 секунд. Выберите роль фрилансера или заполните профиль заказчика.'],
                    ['bi-search','Найдите друг друга','Заказчики публикуют проекты — фрилансеры откликаются. Краулер добавляет заказы с внешних площадок.'],
                    ['bi-handshake','Работайте и зарабатывайте','Обменивайтесь сообщениями, отслеживайте прогресс, оставляйте отзывы.'],
                ];
            @endphp
            @foreach($steps as $i => [$icon, $title, $desc])
                <div class="col-md-4">
                    <div class="step-icon"><i class="bi {{ $icon }}"></i></div>
                    <div class="badge bg-primary rounded-pill mb-2 px-3">Шаг {{ $i + 1 }}</div>
                    <h5 class="fw-bold">{{ $title }}</h5>
                    <p class="text-muted">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ROLES --}}
<section class="roles-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-weight:800;">Для кого эта платформа</h2>
            <p class="text-muted">Четыре роли — один инструмент</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="role-card">
                    <span class="icon">👨‍💻</span>
                    <h4>Фрилансер</h4>
                    <ul>
                        <li>Просматривает и ищет заказы</li>
                        <li>Откликается на проекты</li>
                        <li>Получает уведомления о новых заказах</li>
                        <li>Видит внешние заказы с краулера</li>
                        <li>Строит репутацию через отзывы</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="role-card">
                    <span class="icon">🏢</span>
                    <h4>Заказчик</h4>
                    <ul>
                        <li>Публикует проекты</li>
                        <li>Просматривает отклики</li>
                        <li>Принимает фрилансеров в работу</li>
                        <li>Ведёт переписку в системе</li>
                        <li>Оставляет отзывы</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="role-card">
                    <span class="icon">🛡</span>
                    <h4>Модератор</h4>
                    <ul>
                        <li>Проверяет заказы перед публикацией</li>
                        <li>Одобряет или отклоняет заказы</li>
                        <li>Рассматривает жалобы</li>
                        <li>Следит за качеством контента</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="role-card">
                    <span class="icon">⚙️</span>
                    <h4>Администратор</h4>
                    <ul>
                        <li>Управляет пользователями</li>
                        <li>Настраивает краулер</li>
                        <li>Просматривает статистику</li>
                        <li>Выгружает отчёты CSV/XLSX</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FEATURES --}}
<section class="features">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-weight:800;">Ключевые возможности</h2>
            <p class="text-muted">Всё необходимое для эффективной работы</p>
        </div>
        <div class="row g-4">
            @php
                $features = [
                    ['bi-robot','Веб-краулер','Автоматически собирает открытые заказы с внешних сайтов по расписанию или вручную.'],
                    ['bi-bell','Умные уведомления','Система уведомляет о новых заказах по сохранённым поискам — никогда не пропустите нужный проект.'],
                    ['bi-shield-check','Модерация','Каждый заказ проходит проверку перед публикацией. Безопасная платформа для всех участников.'],
                    ['bi-chat-dots','Встроенный чат','Переписка внутри платформы. Не нужны внешние мессенджеры для рабочей коммуникации.'],
                    ['bi-star-fill','Рейтинг и отзывы','Прозрачная система рейтингов на основе реальных завершённых заказов.'],
                    ['bi-file-earmark-excel','Отчёты и статистика','Выгрузка данных в CSV и XLSX. Полная аналитика для администраторов.'],
                ];
            @endphp
            @foreach($features as [$icon, $title, $desc])
                <div class="col-md-6 col-lg-4">
                    <div class="feature-item">
                        <i class="bi {{ $icon }}"></i>
                        <h5>{{ $title }}</h5>
                        <p class="text-muted mb-0">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-section">
    <div class="container text-center">
        <h2>Готовы начать?</h2>
        <p class="mt-2 mb-4">Регистрация бесплатна. Начните искать заказы прямо сейчас.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                <i class="bi bi-person-plus me-2"></i>Зарегистрироваться
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-light btn-lg">
                <i class="bi bi-search me-2"></i>Смотреть заказы
            </a>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="text-white fw-bold">
                    <i class="bi bi-briefcase-fill me-2 text-primary"></i>ФрилансМаркет
                </span>
                <p class="mt-1 mb-0 small">Профессиональная биржа фриланса</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('orders.index') }}" class="text-secondary text-decoration-none me-3 small">Заказы</a>
                <a href="{{ route('external.index') }}" class="text-secondary text-decoration-none me-3 small">Внешние заказы</a>
                <a href="{{ route('login') }}" class="text-secondary text-decoration-none small">Войти</a>
                <p class="mt-2 mb-0 small">&copy; {{ date('Y') }} ФрилансМаркет</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>