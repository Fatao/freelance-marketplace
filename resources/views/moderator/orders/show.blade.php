@extends('layouts.app')
@section('title', $order->title)
@section('content')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-secondary me-2">{{ $order->category?->name }}</span>
                    <span class="fw-semibold">{{ $order->title }}</span>
                </div>
                <span class="badge bg-warning text-dark">На модерации</span>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">{{ $order->description }}</p>

                @if($order->links)
                    <div class="mb-3">
                        <span class="fw-semibold small">Ссылки:</span>
                        <a href="{{ $order->links }}" target="_blank" class="small d-block">{{ $order->links }}</a>
                    </div>
                @endif

                <hr>

                <div class="row g-3">
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Бюджет</small>
                        <span class="fw-semibold text-success">
                            @if($order->budget_min && $order->budget_max)
                                {{ number_format($order->budget_min,0,'.',' ') }} – {{ number_format($order->budget_max,0,'.',' ') }} ₽
                            @elseif($order->budget_max)
                                до {{ number_format($order->budget_max,0,'.',' ') }} ₽
                            @elseif($order->budget_min)
                                от {{ number_format($order->budget_min,0,'.',' ') }} ₽
                            @else
                                Не указан
                            @endif
                        </span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Формат оплаты</small>
                        <span>{{ ['fixed'=>'Фиксированный','hourly'=>'Почасовой','negotiable'=>'Договорной'][$order->payment_format] ?? $order->payment_format }}</span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Срок выполнения</small>
                        <span>{{ $order->deadline ? $order->deadline->format('d.m.Y') : 'Не указан' }}</span>
                    </div>
                </div>

                @if($order->skills->count())
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Необходимые навыки</small>
                        @foreach($order->skills as $skill)
                            <span class="badge bg-light text-dark border me-1 mb-1">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-clipboard-check me-2"></i>Решение модератора
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <form method="POST" action="{{ route('moderator.orders.approve', $order) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>Одобрить
                        </button>
                    </form>

                    <button class="btn btn-danger" data-bs-toggle="collapse" data-bs-target="#reject_form">
                        <i class="bi bi-x-lg me-1"></i>Отклонить
                    </button>

                    <button class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#revise_form">
                        <i class="bi bi-pencil me-1"></i>На доработку
                    </button>
                </div>

                <div class="collapse" id="reject_form">
                    <form method="POST" action="{{ route('moderator.orders.reject', $order) }}" class="border rounded p-3 bg-light">
                        @csrf
                        @method('PATCH')
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Причина отклонения</label>
                            <input type="text" name="reason" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm">Подтвердить отклонение</button>
                    </form>
                </div>

                <div class="collapse" id="revise_form">
                    <form method="POST" action="{{ route('moderator.orders.revise', $order) }}" class="border rounded p-3 bg-light mt-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Что нужно доработать</label>
                            <input type="text" name="reason" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm">Отправить на доработку</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-building me-2"></i>Заказчик</div>
            <div class="card-body">
                <p class="fw-semibold mb-1">{{ $order->client?->clientProfile?->company_name ?? $order->client?->name ?? 'Не указан' }}</p>
                <p class="text-muted small mb-2">{{ $order->client?->clientProfile?->description }}</p>
                <small class="text-muted">E-mail: {{ $order->client?->email ?? 'Не указан' }}</small>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Информация</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Создан</span>
                    <span>{{ $order->created_at?->format('d.m.Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Статус</span>
                    <span>{{ $order->status }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Откликов</span>
                    <span>{{ $order->applications->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
