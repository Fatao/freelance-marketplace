@extends('layouts.app')
@section('title', 'Сохранить поиск')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bookmark-plus me-2"></i>Сохранить параметры поиска
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('saved-searches.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Название поиска <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', 'Мой поиск ' . now()->format('d.m')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ключевые слова</label>
                        <input type="text" name="keywords" class="form-control"
                               value="{{ old('keywords', $prefill['keywords'] ?? '') }}"
                               placeholder="Laravel, Python, дизайн...">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Бюджет от (₽)</label>
                            <input type="number" name="budget_min" class="form-control"
                                   value="{{ old('budget_min', $prefill['budget_min'] ?? '') }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Бюджет до (₽)</label>
                            <input type="number" name="budget_max" class="form-control"
                                   value="{{ old('budget_max', $prefill['budget_max'] ?? '') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Категории</label>
                        <div class="row g-1">
                            @foreach($categories as $cat)
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                               class="form-check-input" id="cat_{{ $cat->id }}"
                                               {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="cat_{{ $cat->id }}">{{ $cat->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Источник заказов</label>
                        <select name="source" class="form-select">
                            <option value="">Любой</option>
                            <option value="internal">Только внутренние</option>
                            <option value="external">Только внешние (краулер)</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-bookmark-check me-1"></i>Сохранить поиск
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection