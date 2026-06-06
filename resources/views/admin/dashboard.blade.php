@extends('layouts.admin')
@section('page_title', 'Панель администратора')

@section('admin_content')

{{-- Stats row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-left-primary">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Пользователей</div>
                    <div class="h3 mb-0 fw-bold">{{ $stats['users_total'] }}</div>
                    <small class="text-muted">Ф: {{ $stats['users_freelancer'] }} · З: {{ $stats['users_client'] }}</small>
                </div>
                <i class="fas fa-users fa-2x text-primary opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-left-success">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Опубликованных заказов</div>
                    <div class="h3 mb-0 fw-bold">{{ $stats['orders_published'] }}</div>
                    <small class="text-muted">В работе: {{ $stats['orders_in_progress'] }}</small>
                </div>
                <i class="fas fa-file-alt fa-2x text-success opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-left-warning">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">На модерации</div>
                    <div class="h3 mb-0 fw-bold text-warning">{{ $stats['orders_moderation'] }}</div>
                    <small class="text-muted">Завершено: {{ $stats['orders_completed'] }}</small>
                </div>
                <i class="fas fa-shield-alt fa-2x text-warning opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-left-info">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Внешних заказов</div>
                    <div class="h3 mb-0 fw-bold">{{ $stats['external_total'] }}</div>
                    <small class="text-muted">Новых: {{ $stats['external_new'] }}</small>
                </div>
                <i class="fas fa-globe fa-2x text-info opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Recent orders --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="fas fa-list me-2"></i>Последние заказы</span>
                <a href="{{ route('moderator.orders.index') }}" class="btn btn-sm btn-warning">
                    На модерации: {{ $stats['orders_moderation'] }}
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Название</th>
                            <th>Заказчик</th>
                            <th>Статус</th>
                            <th>Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td>{{ Str::limit($order->title, 35) }}</td>
                                <td class="small text-muted">{{ $order->client->name }}</td>
                                <td>
                                    @php
                                        $colors = ['draft'=>'secondary','on_moderation'=>'warning',
                                            'published'=>'success','in_progress'=>'primary',
                                            'completed'=>'info','cancelled'=>'danger','rejected'=>'danger'];
                                        $labels = ['draft'=>'Черновик','on_moderation'=>'Модерация',
                                            'published'=>'Опубликован','in_progress'=>'В работе',
                                            'completed'=>'Завершён','cancelled'=>'Отменён','rejected'=>'Отклонён'];
                                    @endphp
                                    <span class="badge bg-{{ $colors[$order->status] ?? 'secondary' }}">
                                        {{ $labels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $order->created_at->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar stats --}}
    <div class="col-lg-4">
        {{-- Crawler status --}}
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-robot me-2"></i>Краулер</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">Активных источников</small>
                    <span class="badge bg-success">{{ $stats['sources_active'] }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <small class="text-muted">Ошибок всего</small>
                    <span class="badge bg-{{ $stats['crawler_errors'] > 0 ? 'danger' : 'secondary' }}">
                        {{ $stats['crawler_errors'] }}
                    </span>
                </div>
                <div class="d-grid gap-2">
                    <form method="POST" action="{{ route('admin.crawler.runAll') }}">
                        @csrf
                        <button class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-play me-1"></i>Запустить все источники
                        </button>
                    </form>
                    <a href="{{ route('admin.crawler.logs') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-history me-1"></i>Просмотреть логи
                    </a>
                </div>
            </div>
        </div>

        {{-- Popular categories --}}
        <div class="card">
            <div class="card-header"><i class="fas fa-chart-bar me-2"></i>Популярные категории</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($popularCategories as $row)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <small>{{ $row->category?->name ?? '—' }}</small>
                            <span class="badge bg-primary">{{ $row->total }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection