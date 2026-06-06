@extends('layouts.app')
@section('title', $externalOrder->title)
@section('content')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-info me-2">Внешний заказ</span>
                    <span class="badge {{ $externalOrder->status === 'new' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ['new'=>'Новый','active'=>'Актуальный','archived'=>'Архив','error'=>'Ошибка'][$externalOrder->status] }}
                    </span>
                </div>
                <a href="{{ route('external.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Назад
                </a>
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{ $externalOrder->title }}</h5>

                @if($externalOrder->description)
                    <p class="text-muted">{{ $externalOrder->description }}</p>
                @endif

                <hr>
                <div class="row g-3">
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Бюджет</small>
                        <span class="fw-semibold text-success">
                            {{ $externalOrder->budget ? number_format($externalOrder->budget,0,'.',' ') . ' ₽' : 'Не указан' }}
                        </span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Срок</small>
                        <span>{{ $externalOrder->deadline?->format('d.m.Y') ?? 'Не указан' }}</span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Источник</small>
                        <span>{{ $externalOrder->source?->name }}</span>
                    </div>
                </div>

                @if($externalOrder->skills && count($externalOrder->skills))
                    <div class="mt-3">
                        <small class="text-muted d-block mb-1">Навыки</small>
                        @foreach($externalOrder->skills as $skill)
                            <span class="badge bg-light text-dark border me-1 mb-1">{{ $skill }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ $externalOrder->source_url }}" target="_blank"
                       class="btn btn-primary">
                        <i class="bi bi-box-arrow-up-right me-2"></i>Перейти к оригинальному заказу
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Информация</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Обнаружен</span>
                    <span>{{ $externalOrder->discovered_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Обновлён</span>
                    <span>{{ $externalOrder->last_updated_at?->format('d.m.Y H:i') ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Категория</span>
                    <span>{{ $externalOrder->category?->name ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection