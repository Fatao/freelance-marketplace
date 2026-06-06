@extends('layouts.app')
@section('title', 'Все заказы')
@section('content')

<div class="row g-4">

    {{-- FILTERS --}}
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-funnel me-1"></i> Фильтры
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('orders.index') }}">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Ключевые слова</label>
                        <input type="text" name="keywords" class="form-control form-control-sm"
                               value="{{ request('keywords') }}" placeholder="Название, описание...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Категория</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">Все категории</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Бюджет, ₽</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="budget_min" class="form-control"
                                   placeholder="от" value="{{ request('budget_min') }}">
                            <input type="number" name="budget_max" class="form-control"
                                   placeholder="до" value="{{ request('budget_max') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Формат оплаты</label>
                        <select name="payment_format" class="form-select form-select-sm">
                            <option value="">Любой</option>
                            <option value="fixed" {{ request('payment_format') === 'fixed' ? 'selected' : '' }}>Фиксированный</option>
                            <option value="hourly" {{ request('payment_format') === 'hourly' ? 'selected' : '' }}>Почасовой</option>
                            <option value="negotiable" {{ request('payment_format') === 'negotiable' ? 'selected' : '' }}>Договорной</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Срок до</label>
                        <input type="date" name="deadline_to" class="form-control form-control-sm"
                               value="{{ request('deadline_to') }}">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search me-1"></i>Найти
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">Сбросить</a>
                    </div>
                </form>

                @auth
                    @if(auth()->user()->isFreelancer())
                        <hr>
                        <a href="{{ route('saved-searches.create') }}?{{ request()->getQueryString() }}"
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-bookmark-plus me-1"></i>Сохранить поиск
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    {{-- ORDERS LIST --}}
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                Найдено заказов: <span class="text-primary">{{ $orders->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <select class="form-select form-select-sm w-auto"
                        onchange="location='?{{ count(request()->except(['sort','dir','page'])) ? http_build_query(request()->except(['sort','dir','page'])) . '&' : '' }}sort='+this.value">
                    <option value="published_at" {{ request('sort','published_at') === 'published_at' ? 'selected' : '' }}>По дате</option>
                    <option value="budget_max" {{ request('sort') === 'budget_max' ? 'selected' : '' }}>По бюджету</option>
                    <option value="deadline" {{ request('sort') === 'deadline' ? 'selected' : '' }}>По сроку</option>
                </select>
                @auth
                    @if(auth()->user()->isClient())
                        <a href="{{ route('orders.create') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Создать заказ
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        @forelse($orders as $order)
            <div class="card mb-3 hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title mb-1">
                                <a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-dark fw-semibold">
                                    {{ $order->title }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-tag me-1"></i>{{ $order->category?->name ?? 'Без категории' }}
                                &nbsp;·&nbsp;
                                <i class="bi bi-building me-1"></i>{{ $order->client->clientProfile?->company_name ?? $order->client->name }}
                                &nbsp;·&nbsp;
                                <i class="bi bi-clock me-1"></i>{{ $order->published_at?->diffForHumans() }}
                            </small>
                        </div>
                        <div class="text-end text-nowrap">
                            @if($order->budget_min || $order->budget_max)
                                <div class="fw-bold text-success">
                                    @if($order->budget_min && $order->budget_max)
                                        {{ number_format($order->budget_min, 0, '.', ' ') }} –
                                        {{ number_format($order->budget_max, 0, '.', ' ') }} ₽
                                    @elseif($order->budget_max)
                                        до {{ number_format($order->budget_max, 0, '.', ' ') }} ₽
                                    @else
                                        от {{ number_format($order->budget_min, 0, '.', ' ') }} ₽
                                    @endif
                                </div>
                            @else
                                <div class="text-muted small">Бюджет не указан</div>
                            @endif
                            <small class="text-muted">
                                {{ ['fixed'=>'Фиксированный','hourly'=>'Почасовой','negotiable'=>'Договорной'][$order->payment_format] }}
                            </small>
                        </div>
                    </div>

                    <p class="text-muted small mt-2 mb-2">
                        {{ Str::limit($order->description, 150) }}
                    </p>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @foreach($order->skills->take(4) as $skill)
                                <span class="badge bg-light text-dark border me-1">{{ $skill->name }}</span>
                            @endforeach
                            @if($order->skills->count() > 4)
                                <span class="badge bg-light text-muted border">+{{ $order->skills->count() - 4 }}</span>
                            @endif
                        </div>
                        <div class="small text-muted">
                            @if($order->deadline)
                                <i class="bi bi-calendar3 me-1"></i>до {{ $order->deadline->format('d.m.Y') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-search fs-1 d-block mb-3"></i>
                    По вашему запросу ничего не найдено.
                    <a href="{{ route('orders.index') }}">Сбросить фильтры</a>
                </div>
            </div>
        @endforelse

        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection