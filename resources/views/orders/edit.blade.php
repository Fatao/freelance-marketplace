@extends('layouts.app')
@section('title', 'Редактировать заказ')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Редактировать заказ
            </div>
            <div class="card-body p-4">
                @if($order->rejection_reason)
                    <div class="alert alert-warning">
                        <strong>Причина отклонения:</strong> {{ $order->rejection_reason }}
                    </div>
                @endif
                <form method="POST" action="{{ route('orders.update', $order) }}">
                    @csrf @method('PUT')
                    @include('orders._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Сохранить
                        </button>
                        <form method="POST" action="{{ route('orders.submit', $order) }}" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send me-1"></i>Отправить на модерацию
                            </button>
                        </form>
                        <a href="{{ route('orders.my') }}" class="btn btn-outline-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection