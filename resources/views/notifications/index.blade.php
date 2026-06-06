@extends('layouts.app')
@section('title', 'Уведомления')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-bell me-2"></i>Уведомления</h4>
    @if($notifications->total() > 0)
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button class="btn btn-outline-secondary btn-sm">Прочитать все</button>
        </form>
    @endif
</div>

@if($notifications->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
            Нет уведомлений.
        </div>
    </div>
@else
    @foreach($notifications as $n)
        <div class="card mb-2 {{ !$n->is_read ? 'border-primary' : '' }}">
            <div class="card-body py-2 d-flex justify-content-between align-items-start">
                <div>
                    @if(!$n->is_read)
                        <span class="notification-dot me-2"></span>
                    @endif
                    {{ $n->message }}
                    <div class="small text-muted mt-1">{{ $n->created_at->diffForHumans() }}</div>
                </div>
                @if(!$n->is_read)
                    <form method="POST" action="{{ route('notifications.read', $n) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-link text-muted p-0 ms-3">
                            <i class="bi bi-check2"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
    {{ $notifications->links() }}
@endif
@endsection