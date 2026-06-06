@extends('layouts.app')
@section('title', 'Профиль фрилансера')
@section('content')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-circle me-2"></i>Профиль фрилансера
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.freelancer.update') }}">
                    @csrf @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Отображаемое имя</label>
                            <input type="text" name="display_name" class="form-control"
                                   value="{{ old('display_name', $profile->display_name) }}"
                                   placeholder="Алексей — Laravel разработчик">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Специализация</label>
                            <input type="text" name="specialization" class="form-control"
                                   value="{{ old('specialization', $profile->specialization) }}"
                                   placeholder="Веб-разработка, UI/UX...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Описание опыта</label>
                        <textarea name="experience" rows="5" class="form-control"
                                  placeholder="Расскажите о своём опыте, проектах, достижениях...">{{ old('experience', $profile->experience) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Портфолио (ссылки)</label>
                        <textarea name="portfolio" rows="3" class="form-control"
                                  placeholder="https://github.com/username&#10;https://behance.net/username">{{ old('portfolio', $profile->portfolio) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Почасовая ставка (₽/ч)</label>
                            <input type="number" name="hourly_rate" class="form-control"
                                   value="{{ old('hourly_rate', $profile->hourly_rate) }}"
                                   placeholder="1500">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Телефон</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $profile->phone) }}"
                                   placeholder="+7 900 000-00-00">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Telegram</label>
                            <input type="text" name="telegram" class="form-control"
                                   value="{{ old('telegram', $profile->telegram) }}"
                                   placeholder="@username">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Сайт</label>
                        <input type="url" name="website" class="form-control"
                               value="{{ old('website', $profile->website) }}"
                               placeholder="https://my-portfolio.ru">
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_available" value="0">
                            <input type="checkbox" name="is_available" value="1"
                                   class="form-check-input" id="is_available"
                                   {{ old('is_available', $profile->is_available ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_available">
                                Доступен для новых заказов
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Навыки</label>
                        <div class="row g-1" style="max-height: 250px; overflow-y: auto;">
                            @foreach($skills as $skill)
                                <div class="col-sm-4 col-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="skills[]" value="{{ $skill->id }}"
                                               class="form-check-input" id="skill_{{ $skill->id }}"
                                               {{ in_array($skill->id, $selected) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="skill_{{ $skill->id }}">
                                            {{ $skill->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Сохранить профиль
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-star-fill me-2 text-warning"></i>Рейтинг</div>
            <div class="card-body text-center">
                <div class="display-4 fw-bold text-warning">
                    {{ number_format($profile->rating ?? 0, 1) }}
                </div>
                <div class="d-flex justify-content-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= round($profile->rating ?? 0) ? '-fill text-warning' : ' text-muted' }}"></i>
                    @endfor
                </div>
                <small class="text-muted">{{ $profile->reviews_count ?? 0 }} отзывов</small>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-graph-up me-2"></i>Активность</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Откликов отправлено</span>
                    <span>{{ auth()->user()->applications()->count() }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Принято откликов</span>
                    <span>{{ auth()->user()->applications()->where('status','accepted')->count() }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Завершено заказов</span>
                    <span>{{ auth()->user()->applications()->where('status','accepted')->count() }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-lightning me-2"></i>Быстрые ссылки</div>
            <div class="list-group list-group-flush">
                <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action small">
                    <i class="bi bi-search me-2"></i>Найти заказы
                </a>
                <a href="{{ route('applications.my') }}" class="list-group-item list-group-item-action small">
                    <i class="bi bi-send me-2"></i>Мои отклики
                </a>
                <a href="{{ route('saved-searches.index') }}" class="list-group-item list-group-item-action small">
                    <i class="bi bi-bookmark me-2"></i>Сохранённые поиски
                </a>
                <a href="{{ route('external.index') }}" class="list-group-item list-group-item-action small">
                    <i class="bi bi-globe me-2"></i>Внешние заказы
                </a>
            </div>
        </div>
    </div>
</div>
@endsection