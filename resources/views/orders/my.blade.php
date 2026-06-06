@extends('layouts.app')
@section('title', 'Мои заказы')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-list-ul me-2"></i>Мои заказы</h4>
    <a href="{{ route('orders.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i>Создать заказ
    </a>
</div>

@if($orders->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
            У вас пока нет заказов.
            <div class="mt-3">
                <a href="{{ route('orders.create') }}" class="btn btn-primary">Создать первый заказ</a>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Бюджет</th>
                        <th>Статус</th>
                        <th>Откликов</th>
                        <th>Создан</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="text-decoration-none fw-semibold">
                                    {{ Str::limit($order->title, 40) }}
                                </a>
                            </td>
                            <td><small>{{ $order->category?->name ?? '—' }}</small></td>
                            <td class="small">
                                @if($order->budget_max)
                                    до {{ number_format($order->budget_max,0,'.',' ') }} ₽
                                @else — @endif
                            </td>
                            <td>
                                @php
                                    $statusLabels = [
                                        'draft' => ['Черновик', 'secondary'],
                                        'on_moderation' => ['На модерации', 'warning'],
                                        'published' => ['Опубликован', 'success'],
                                        'in_progress' => ['В работе', 'primary'],
                                        'completed' => ['Завершён', 'info'],
                                        'cancelled' => ['Отменён', 'danger'],
                                        'rejected' => ['Отклонён', 'danger'],
                                    ];
                                    [$label, $color] = $statusLabels[$order->status] ?? ['—', 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $label }}</span>
                            </td>
                            <td>
                                @if($order->applications_count > 0)
                                    <a href="{{ route('applications.index', $order) }}" class="badge bg-primary text-decoration-none">
                                        {{ $order->applications_count }}
                                    </a>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $order->created_at->format('d.m.Y') }}</small></td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if(in_array($order->status, ['draft','rejected']))
                                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('orders.submit', $order) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-primary" title="На модерацию">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($order->status === 'published')
                                        <form method="POST" action="{{ route('orders.cancel', $order) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Отменить заказ?')">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
@endif
@endsection