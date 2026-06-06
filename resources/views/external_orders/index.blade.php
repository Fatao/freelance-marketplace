@extends('layouts.app')
@section('title', 'Внешние заказы')
@section('content')

<div class="row g-4">
    {{-- Filters --}}
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header"><i class="bi bi-funnel me-1"></i>Фильтры</div>
            <div class="card-body">
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Ключевые слова</label>
                        <input type="text" name="keywords" class="form-control form-control-sm"
                               value="{{ request('keywords') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Источник</label>
                        <select name="source_id" class="form-select form-select-sm">
                            <option value="">Все источники</option>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}" {{ request('source_id') == $source->id ? 'selected' : '' }}>
                                    {{ $source->name }}
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
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search me-1"></i>Найти
                        </button>
                        <a href="{{ route('external.index') }}" class="btn btn-outline-secondary btn-sm">Сбросить</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- List --}}
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="bi bi-globe me-2"></i>Внешние заказы
                <span class="text-primary ms-1">{{ $orders->total() }}</span>
            </h5>
            @auth
                @if(auth()->user()->isFreelancer())
                    <a href="{{ route('saved-searches.create', ['source' => 'external', 'keywords' => request('keywords')]) }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-bookmark-plus me-1"></i>Сохранить поиск
                    </a>
                @endif
            @endauth
        </div>

        @forelse($orders as $order)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                <a href="{{ route('external.show', $order) }}"
                                   class="text-decoration-none text-dark fw-semibold">
                                    {{ $order->title }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-globe2 me-1"></i>{{ $order->source?->name }}
                                &nbsp;·&nbsp;
                                <i class="bi bi-clock me-1"></i>{{ $order->discovered_at->diffForHumans() }}
                                @if($order->status === 'new')
                                    &nbsp;·&nbsp;<span class="badge bg-success">Новый</span>
                                @endif
                            </small>
                        </div>
                        <div class="text-end text-nowrap">
                            @if($order->budget)
                                <div class="fw-bold text-success">
                                    {{ number_format($order->budget, 0, '.', ' ') }} ₽
                                </div>
                            @else
                                <small class="text-muted">Бюджет не указан</small>
                            @endif
                        </div>
                    </div>

                    @if($order->description)
                        <p class="text-muted small mt-2 mb-2">
                            {{ Str::limit($order->description, 150) }}
                        </p>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            @if($order->skills)
                                @foreach(array_slice($order->skills, 0, 4) as $skill)
                                    <span class="badge bg-light text-dark border me-1">{{ $skill }}</span>
                                @endforeach
                                @if(count($order->skills) > 4)
                                    <span class="badge bg-light text-muted border">+{{ count($order->skills) - 4 }}</span>
                                @endif
                            @endif
                        </div>
                        <a href="{{ $order->source_url }}" target="_blank"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Открыть оригинал
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-globe fs-1 d-block mb-3"></i>
                    Внешних заказов пока нет.<br>
                    <small>Администратор может запустить краулер для сбора заказов.</small>
                </div>
            </div>
        @endforelse

        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection