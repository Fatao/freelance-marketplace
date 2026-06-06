@extends('layouts.app')
@section('title', 'Модерация заказов')
@section('content')
<h4 class="mb-4"><i class="bi bi-shield-check me-2"></i>Заказы на модерации</h4>

@if($orders->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-success">
            <i class="bi bi-check-circle fs-1 d-block mb-3"></i>
            Нет заказов для проверки.
        </div>
    </div>
@else
    @foreach($orders as $order)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <span class="fw-semibold">{{ $order->title }}</span>
                    <span class="badge bg-secondary ms-2">{{ $order->category?->name }}</span>
                </div>
                <small class="text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</small>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">{{ Str::limit($order->description, 200) }}</p>
                <small class="text-muted">Заказчик: <strong>{{ $order->client->name }}</strong></small>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('moderator.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-1"></i>Просмотреть
                </a>
                <form method="POST" action="{{ route('moderator.orders.approve', $order) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-success">
                        <i class="bi bi-check-lg me-1"></i>Одобрить
                    </button>
                </form>
                <button class="btn btn-sm btn-danger" data-bs-toggle="collapse"
                        data-bs-target="#reject_{{ $order->id }}">
                    <i class="bi bi-x-lg me-1"></i>Отклонить
                </button>
                <button class="btn btn-sm btn-warning" data-bs-toggle="collapse"
                        data-bs-target="#revise_{{ $order->id }}">
                    <i class="bi bi-pencil me-1"></i>На доработку
                </button>
            </div>

            {{-- Reject form --}}
            <div class="collapse px-3 pb-3" id="reject_{{ $order->id }}">
                <form method="POST" action="{{ route('moderator.orders.reject', $order) }}" class="mt-2">
                    @csrf @method('PATCH')
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Причина отклонения</label>
                        <input type="text" name="reason" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-sm">Подтвердить отклонение</button>
                </form>
            </div>

            {{-- Revise form --}}
            <div class="collapse px-3 pb-3" id="revise_{{ $order->id }}">
                <form method="POST" action="{{ route('moderator.orders.revise', $order) }}" class="mt-2">
                    @csrf @method('PATCH')
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Что нужно доработать</label>
                        <input type="text" name="reason" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm">Отправить на доработку</button>
                </form>
            </div>
        </div>
    @endforeach
    {{ $orders->links() }}
@endif
@endsection