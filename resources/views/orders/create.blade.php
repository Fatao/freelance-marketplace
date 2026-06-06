@extends('layouts.app')
@section('title', 'Создать заказ')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Новый заказ
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('orders.store') }}">
                    @csrf
                    @include('orders._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Сохранить как черновик
                        </button>
                        <a href="{{ route('orders.my') }}" class="btn btn-outline-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection