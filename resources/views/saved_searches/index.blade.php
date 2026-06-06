@extends('layouts.app')
@section('title', 'Сохранённые поиски')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-bookmark-fill me-2"></i>Сохранённые поиски</h4>
    <a href="{{ route('saved-searches.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Новый поиск
    </a>
</div>

@if($searches->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-bookmark fs-1 d-block mb-3"></i>
            У вас нет сохранённых поисков.
            <div class="mt-3">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                    Найти заказы и сохранить поиск
                </a>
            </div>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($searches as $search)
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">
                            <i class="bi bi-bookmark me-2 text-primary"></i>{{ $search->name }}
                        </span>
                        <small class="text-muted">{{ $search->created_at->format('d.m.Y') }}</small>
                    </div>
                    <div class="card-body small">
                        @if($search->keywords)
                            <div class="mb-2">
                                <span class="text-muted">Ключевые слова:</span>
                                <strong>{{ $search->keywords }}</strong>
                            </div>
                        @endif
                        @if($search->budget_min || $search->budget_max)
                            <div class="mb-2">
                                <span class="text-muted">Бюджет:</span>
                                @if($search->budget_min)от {{ number_format($search->budget_min,0,'.',' ') }} ₽ @endif
                                @if($search->budget_max)до {{ number_format($search->budget_max,0,'.',' ') }} ₽ @endif
                            </div>
                        @endif
                        @if($search->categories)
                            <div class="mb-2">
                                <span class="text-muted">Категории:</span>
                                {{ \App\Models\Category::whereIn('id', $search->categories)->pluck('name')->join(', ') }}
                            </div>
                        @endif
                        @if($search->skills)
                            <div class="mb-2">
                                <span class="text-muted">Навыки:</span>
                                @foreach(\App\Models\Skill::whereIn('id', $search->skills)->get() as $skill)
                                    <span class="badge bg-light text-dark border">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if($search->source)
                            <div>
                                <span class="text-muted">Источник:</span>
                                {{ $search->source === 'internal' ? 'Только свои' : 'Только внешние' }}
                            </div>
                        @endif
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('orders.index', ['keywords' => $search->keywords, 'budget_min' => $search->budget_min, 'budget_max' => $search->budget_max]) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-search me-1"></i>Применить
                        </a>
                        <form method="POST" action="{{ route('saved-searches.destroy', $search) }}"
                              onsubmit="return confirm('Удалить?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection