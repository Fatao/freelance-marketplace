@extends('layouts.app')
@section('title', 'Сброс пароля')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h5 class="mb-0"><i class="bi bi-key me-2"></i>Сброс пароля</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Введите email — мы отправим ссылку для сброса пароля.
                </p>
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        Отправить ссылку для сброса
                    </button>
                </form>
                <hr>
                <p class="text-center mb-0 small">
                    <a href="{{ route('login') }}">← Вернуться к входу</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection