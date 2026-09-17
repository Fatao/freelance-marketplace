@extends('layouts.app')
@section('title', 'Поиск заказов')
@section('content')

<div class="row g-4">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header"><i class="bi bi-funnel me-1"></i>Поиск</div>
            <div class="card-body">
                <form method="GET" action="{{ route('search.index') }}">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Ключевые слова</label>
                        <input type="text" name="keywords" class="form-control form-control-sm"
                               value="{{ request('keywords') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Категория</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">Все</option>
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
                        <label class="form-label small fw-semibold">Источник</label>
                        <select name="source" class="form-select form-select-sm">
                            <option value="all" {{ request('source','all') === 'all' ? 'selected' : '' }}>Все</option>
                            <option value="internal" {{ request('source') === 'internal' ? 'selected' : '' }}>Внутренние</option>
                            <option value="external" {{ request('source') === 'external' ? 'selected' : '' }}>Внешние (краулер)</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search me-1"></i>Найти
                        </button>
                        <a href="{{ route('search.index') }}" class="btn btn-outline-secondary btn-sm">Сбросить</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <h5 class="mb-3">
            Результаты поиска: <span class="text-primary">{{ $total }}</span>
        </h5>

        @if($internalOrders->count())
            <h6 class="text-muted border-bottom pb-2 mb-3">
                <i class="bi bi-file-text me-2"></i>Заказы платформы ({{ $internalOrders->count() }})
            </h6>
            @foreach($internalOrders as $order)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1">
                                    <a href="{{ route('orders.show', $order) }}"
                                       class="text-decoration-none fw-semibold text-dark">
                                        {{ $order->title }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    {{ $order->category?->name }} · {{ $order->client->name }}
                                </small>
                            </div>
                            <div class="text-end">
                                @if($order->budget_max)
                                    <span class="fw-bold text-success">
                                        до {{ number_format($order->budget_max,0,'.',' ') }} ₽
                                    </span>
                                @endif
                            </div>
                        </div>
                        <p class="small text-muted mt-2 mb-0">{{ Str::limit($order->description, 120) }}</p>
                    </div>
                </div>
            @endforeach
        @endif

        @if($externalOrders->count())
            <h6 class="text-muted border-bottom pb-2 mb-3 mt-4">
                <i class="bi bi-globe me-2"></i>Внешние заказы ({{ $externalOrders->count() }})
            </h6>
            @foreach($externalOrders as $order)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1">
                                    <a href="{{ route('external.show', $order) }}"
                                       class="text-decoration-none fw-semibold text-dark">
                                        {{ $order->title }}
                                    </a>
                                    <span class="badge bg-info ms-2 small">Краулер</span>
                                </h6>
                                <small class="text-muted">{{ $order->source?->name }}</small>
                            </div>
                            <div class="text-end">
                                @if($order->budget)
                                    <span class="fw-bold text-success">
                                        {{ number_format($order->budget,0,'.',' ') }} ₽
                                    </span>
                                @endif
                            </div>
                        </div>
                        <p class="small text-muted mt-2 mb-0">{{ Str::limit($order->description, 120) }}</p>
                    </div>
                </div>
            @endforeach
        @endif

        @if($total === 0)
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-search fs-1 d-block mb-3"></i>
                    Ничего не найдено. Попробуйте изменить параметры поиска.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection