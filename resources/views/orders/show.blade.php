@extends('layouts.app')
@section('title', $order->title)
@section('content')

<div class="row g-4">
    <div class="col-lg-8">

        {{-- Order card --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-secondary me-2">{{ $order->category?->name }}</span>
                    <span class="fw-semibold">{{ $order->title }}</span>
                </div>
                <span class="badge bg-success">Опубликован</span>
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{ $order->title }}</h5>
                <p class="text-muted">{{ $order->description }}</p>

                @if($order->links)
                    <div class="mb-3">
                        <span class="fw-semibold small">Ссылки:</span>
                        <a href="{{ $order->links }}" target="_blank" class="small">{{ $order->links }}</a>
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
                        <span>{{ ['fixed'=>'Фиксированный','hourly'=>'Почасовой','negotiable'=>'Договорной'][$order->payment_format] }}</span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Срок выполнения</small>
                        <span>{{ $order->deadline ? $order->deadline->format('d.m.Y') : 'Не указан' }}</span>
                    </div>
                </div>

                @if($order->skills->count())
                    <div class="mt-3">
                        <small class="text-muted d-block mb-1">Необходимые навыки</small>
                        @foreach($order->skills as $skill)
                            <span class="badge bg-light text-dark border me-1 mb-1">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Application form for freelancer --}}
        @auth
            @if(auth()->user()->isFreelancer())
                @if($userApplication)
                    <div class="card border-primary mb-4">
                        <div class="card-body">
                            <h6 class="text-primary"><i class="bi bi-send-check me-2"></i>Вы уже откликнулись</h6>
                            <p class="mb-1 small text-muted">Статус:
                                <strong>{{ ['sent'=>'Отправлен','viewed'=>'Просмотрен','accepted'=>'Принят','rejected'=>'Отклонён','withdrawn'=>'Отозван'][$userApplication->status] }}</strong>
                            </p>
                            @if(!$userApplication->isAccepted() && $userApplication->status !== 'withdrawn')
                                <form method="POST" action="{{ route('applications.withdraw', $userApplication) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm mt-2">Отозвать отклик</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-send me-2"></i>Отправить отклик
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('applications.store', $order) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Сопроводительное письмо <span class="text-danger">*</span></label>
                                    <textarea name="cover_letter" rows="5"
                                              class="form-control @error('cover_letter') is-invalid @enderror"
                                              placeholder="Расскажите почему вы подходите для этого заказа..." required>{{ old('cover_letter') }}</textarea>
                                    @error('cover_letter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold">Ваша стоимость (₽)</label>
                                        <input type="number" name="proposed_price" class="form-control"
                                               value="{{ old('proposed_price') }}" placeholder="0">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold">Срок выполнения (дней)</label>
                                        <input type="number" name="proposed_days" class="form-control"
                                               value="{{ old('proposed_days') }}" placeholder="0">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    <i class="bi bi-send me-1"></i>Отправить отклик
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endif
        @else
            <div class="alert alert-info">
                <a href="{{ route('login') }}">Войдите</a>, чтобы откликнуться на заказ.
            </div>
        @endauth
    </div>

    {{-- Sidebar: Client info --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-building me-2"></i>О заказчике</div>
            <div class="card-body">
                <p class="fw-semibold mb-1">{{ $order->client->clientProfile?->company_name ?? $order->client->name }}</p>
                <p class="text-muted small mb-2">{{ Str::limit($order->client->clientProfile?->description, 100) }}</p>
                <div class="d-flex align-items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= round($order->client->clientProfile?->rating ?? 0) ? '-fill text-warning' : ' text-muted' }}"></i>
                    @endfor
                    <small class="text-muted ms-1">{{ number_format($order->client->clientProfile?->rating ?? 0, 1) }}</small>
                </div>
                <small class="text-muted">
                    Заказов: {{ $order->client->orders()->where('status','completed')->count() }} завершено
                </small>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Информация</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Опубликован</span>
                    <span>{{ $order->published_at?->format('d.m.Y') }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Откликов</span>
                    <span>{{ $order->applications->count() }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Просмотров</span>
                    <span>—</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection