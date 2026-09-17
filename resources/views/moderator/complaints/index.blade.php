@extends('layouts.app')
@section('title', 'Жалобы')
@section('content')

<h4 class="mb-4"><i class="bi bi-flag me-2"></i>Жалобы</h4>

@if($complaints->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-success">
            <i class="bi bi-check-circle fs-1 d-block mb-3"></i>
            Жалоб нет.
        </div>
    </div>
@else
    @foreach($complaints as $complaint)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <span class="fw-semibold">{{ $complaint->author->name }}</span>
                    <span class="text-muted ms-2 small">пожаловался на
                        {{ $complaint->complainable_type === 'App\Models\Order' ? 'заказ' : 'пользователя' }}
                        #{{ $complaint->complainable_id }}
                    </span>
                </div>
                @php
                    $sc = ['pending'=>'warning','reviewed'=>'info','resolved'=>'success','dismissed'=>'secondary'];
                    $sl = ['pending'=>'На рассмотрении','reviewed'=>'Просмотрена','resolved'=>'Решена','dismissed'=>'Отклонена'];
                @endphp
                <span class="badge bg-{{ $sc[$complaint->status] }}">{{ $sl[$complaint->status] }}</span>
            </div>
            <div class="card-body">
                <p class="mb-2">{{ $complaint->reason }}</p>
                @if($complaint->resolution)
                    <div class="alert alert-light py-2 small mb-0">
                        <strong>Решение:</strong> {{ $complaint->resolution }}
                    </div>
                @endif
            </div>
            @if($complaint->status === 'pending')
                <div class="card-footer d-flex gap-2">
                    <form method="POST" action="{{ route('moderator.complaints.resolve', $complaint) }}" class="d-flex gap-2 w-100">
                        @csrf @method('PATCH')
                        <input type="text" name="resolution" class="form-control form-control-sm"
                               placeholder="Решение (необязательно)">
                        <button class="btn btn-success btn-sm">Решить</button>
                    </form>
                    <form method="POST" action="{{ route('moderator.complaints.dismiss', $complaint) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-secondary btn-sm">Отклонить</button>
                    </form>
                </div>
            @endif
        </div>
    @endforeach
    {{ $complaints->links() }}
@endif
@endsection