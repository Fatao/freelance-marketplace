@extends('layouts.app')
@section('title', 'Профиль заказчика')
@section('content')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-building me-2"></i>Профиль заказчика
            </div>
            <div class="card-body p-4">
                @if(auth()->user()->isFreelancer())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Заполните профиль компании, чтобы получить роль <strong>Заказчика</strong> и начать публиковать заказы.
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.client.update') }}">
                    @csrf @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Название компании / Имя <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
                                   value="{{ old('company_name', $profile->company_name) }}" required>
                            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Сайт</label>
                            <input type="url" name="website" class="form-control"
                                   value="{{ old('website', $profile->website) }}"
                                   placeholder="https://company.ru">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Описание деятельности</label>
                        <textarea name="description" rows="4" class="form-control"
                                  placeholder="Чем занимается ваша компания...">{{ old('description', $profile->description) }}</textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Телефон <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $profile->phone) }}"
                                   placeholder="+7 900 000-00-00" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Telegram</label>
                            <input type="text" name="telegram" class="form-control"
                                   value="{{ old('telegram', $profile->telegram) }}"
                                   placeholder="@company">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Сохранить профиль
                    </button>
                </form>
            </div>
        </div>
    </div>

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

        <div class="card">
            <div class="card-header"><i class="bi bi-lightning me-2"></i>Быстрые ссылки</div>
            <div class="list-group list-group-flush">
                <a href="{{ route('orders.create') }}" class="list-group-item list-group-item-action small">
                    <i class="bi bi-plus-circle me-2"></i>Создать заказ
                </a>
                <a href="{{ route('orders.my') }}" class="list-group-item list-group-item-action small">
                    <i class="bi bi-list-ul me-2"></i>Мои заказы
                </a>
            </div>
        </div>
    </div>
</div>
@endsection