@extends('layouts.admin')
@section('page_title', $source->exists ? 'Редактировать источник' : 'Новый источник')

@section('admin_content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST"
                      action="{{ $source->exists ? route('admin.sources.update', $source) : route('admin.sources.store') }}">
                    @csrf
                    @if($source->exists) @method('PUT') @endif

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Название</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', $source->name) }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Базовый URL</label>
                            <input type="url" name="base_url" class="form-control"
                                   value="{{ old('base_url', $source->base_url) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Правила обхода (JSON)</label>
                            <textarea name="crawl_rules" rows="6" class="form-control font-monospace small" required>{{ old('crawl_rules', $source->exists ? json_encode($source->crawl_rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : json_encode([
                                'item_selector' => 'article.order-card',
                                'max_pages' => 3,
                                'pagination_pattern' => 'https://site.ru/orders?page={page}'
                            ], JSON_PRETTY_PRINT)) }}</textarea>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Правила извлечения (JSON)</label>
                            <textarea name="extract_rules" rows="6" class="form-control font-monospace small" required>{{ old('extract_rules', $source->exists ? json_encode($source->extract_rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : json_encode([
                                'title_selector' => 'h2.title',
                                'description_selector' => 'p.description',
                                'url_selector' => 'a.order-link',
                                'budget_selector' => 'span.budget',
                                'skills_selector' => 'div.skills'
                            ], JSON_PRETTY_PRINT)) }}</textarea>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Периодичность (мин)</label>
                            <input type="number" name="frequency_minutes" class="form-control"
                                   value="{{ old('frequency_minutes', $source->frequency_minutes ?? 360) }}"
                                   min="5" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Статус</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $source->status) === 'active' ? 'selected' : '' }}>Активен</option>
                                <option value="disabled" {{ old('status', $source->status) === 'disabled' ? 'selected' : '' }}>Отключён</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Сохранить
                        </button>
                        <a href="{{ route('admin.sources.index') }}" class="btn btn-outline-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection