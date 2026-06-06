@extends('layouts.app')
@section('title', 'Отклики на заказ')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Отклики на заказ</h4>
        <small class="text-muted">{{ $order->title }}</small>
    </div>
    <a href="{{ route('orders.my') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Назад
    </a>
</div>

@if($applications->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3"></i>Откликов пока нет.
        </div>
    </div>
@else
    @foreach($applications as $app)
        <div class="card mb-3 {{ $app->status === 'accepted' ? 'border-success' : '' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-person-circle fs-4 text-secondary"></i>
                    <div>
                        <span class="fw-semibold">{{ $app->freelancer->name }}</span>
                        <div class="small text-muted">{{ $app->freelancer->freelancerProfile?->specialization }}</div>
                    </div>
                </div>
                @php
                    $statusMap = ['sent'=>['Отправлен','secondary'],'viewed'=>['Просмотрен','info'],
                                  'accepted'=>['Принят','success'],'rejected'=>['Отклонён','danger'],'withdrawn'=>['Отозван','secondary']];
                    [$sl, $sc] = $statusMap[$app->status] ?? ['—','secondary'];
                @endphp
                <span class="badge bg-{{ $sc }}">{{ $sl }}</span>
            </div>
            <div class="card-body">
                <p class="mb-3">{{ $app->cover_letter }}</p>
                <div class="row g-2 small text-muted">
                    @if($app->proposed_price)
                        <div class="col-auto">
                            <i class="bi bi-currency-exchange me-1"></i>
                            <strong class="text-success">{{ number_format($app->proposed_price,0,'.',' ') }} ₽</strong>
                        </div>
                    @endif
                    @if($app->proposed_days)
                        <div class="col-auto">
                            <i class="bi bi-calendar3 me-1"></i>{{ $app->proposed_days }} дней
                        </div>
                    @endif
                    <div class="col-auto">
                        <i class="bi bi-clock me-1"></i>{{ $app->created_at->diffForHumans() }}
                    </div>
                </div>

                @if($app->freelancer->freelancerProfile)
                    <div class="mt-2 small">
                        <i class="bi bi-star-fill text-warning me-1"></i>
                        {{ number_format($app->freelancer->freelancerProfile->rating, 1) }}
                        ({{ $app->freelancer->freelancerProfile->reviews_count }} отзывов)
                        · {{ $app->freelancer->freelancerProfile->specialization }}
                    </div>
                @endif
            </div>
            @if($app->status === 'sent' || $app->status === 'viewed')
                <div class="card-footer d-flex gap-2">
                    <form method="POST" action="{{ route('applications.accept', $app) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-success btn-sm">
                            <i class="bi bi-check-lg me-1"></i>Принять
                        </button>
                    </form>
                    <form method="POST" action="{{ route('applications.reject', $app) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-danger btn-sm">Отклонить</button>
                    </form>
                </div>
            @endif
        </div>
    @endforeach
@endif
@endsection