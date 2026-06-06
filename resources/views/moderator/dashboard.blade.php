@extends('layouts.app')
@section('title', 'Панель модератора')
@section('content')

<h4 class="mb-4"><i class="bi bi-shield-check me-2"></i>Панель модератора</h4>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="h2 fw-bold text-warning mb-0">{{ $stats['pending_orders'] }}</div>
                <small class="text-muted">Ожидают проверки</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="h2 fw-bold text-danger mb-0">{{ $stats['pending_complaints'] }}</div>
                <small class="text-muted">Жалоб на рассмотрении</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="h2 fw-bold text-success mb-0">{{ $stats['approved_today'] }}</div>
                <small class="text-muted">Одобрено сегодня</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <div class="h2 fw-bold text-secondary mb-0">{{ $stats['rejected_today'] }}</div>
                <small class="text-muted">Отклонено сегодня</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i>Заказы ожидающие модерации</span>
        <a href="{{ route('moderator.orders.index') }}" class="btn btn-sm btn-warning">
            Все заказы ({{ $stats['pending_orders'] }})
        </a>
    </div>
    @if($pendingOrders->isEmpty())
        <div class="card-body text-center py-5 text-success">
            <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
            Нет заказов для проверки.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Заказ</th>
                        <th>Заказчик</th>
                        <th>Категория</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('moderator.orders.show', $order) }}"
                                   class="text-decoration-none fw-semibold">
                                    {{ Str::limit($order->title, 45) }}
                                </a>
                            </td>
                            <td class="small text-muted">{{ $order->client->name }}</td>
                            <td><small>{{ $order->category?->name ?? '—' }}</small></td>
                            <td><small class="text-muted">{{ $order->created_at->format('d.m H:i') }}</small></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('moderator.orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('moderator.orders.approve', $order) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection