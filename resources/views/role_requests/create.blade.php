@extends('layouts.app')
@section('title', 'Заявка на роль Заказчика')
@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">

        @if($existing)
            <div class="card border-warning mb-4">
                <div class="card-body text-center py-4">
                    <i class="bi bi-hourglass-split fs-1 text-warning d-block mb-3"></i>
                    <h5 class="fw-bold">Заявка на рассмотрении</h5>
                    <p class="text-muted">
                        Ваша заявка отправлена {{ $existing->created_at->diffForHumans() }}.
                        Администратор рассмотрит её в ближайшее время.
                    </p>
                    <div class="mt-2 small">
                        <span class="text-muted">Компания:</span> {{ $existing->company_name }}<br>
                        <span class="text-muted">Телефон:</span> {{ $existing->phone }}
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-badge me-2"></i>Заявка на роль Заказчика
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info small mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Заполните данные компании. Администратор рассмотрит заявку
                        и переведёт вас в роль <strong>Заказчика</strong>.
                        После этого вы сможете публиковать заказы.
                    </div>

                    <form method="POST" action="{{ route('role-request.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Название компании / Имя <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="company_name"
                                   class="form-control @error('company_name') is-invalid @enderror"
                                   value="{{ old('company_name') }}"
                                   placeholder="ООО «Моя компания» или Иван Иванов"
                                   required>
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Контактный телефон <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}"
                                   placeholder="+7 900 000-00-00"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Зачем вам роль Заказчика?
                            </label>
                            <textarea name="reason" rows="4"
                                      class="form-control"
                                      placeholder="Опишите кратко — что планируете заказывать, для какого проекта...">{{ old('reason') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Отправить заявку
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection