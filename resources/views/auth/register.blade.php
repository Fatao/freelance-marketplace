@extends('layouts.app')
@section('title', 'Регистрация')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Регистрация</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle me-1"></i>
                    После регистрации вы получите роль <strong>Фрилансера</strong>.
                    Для роли Заказчика заполните профиль компании.
                </div>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Имя</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Пароль</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Подтвердите пароль</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                </form>
                <hr>
                <p class="text-center mb-0">
                    Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection