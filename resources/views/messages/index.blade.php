@extends('layouts.app')
@section('title', 'Переписка по заказу')
@section('content')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-chat-dots me-2"></i>
                    <span class="fw-semibold">{{ $order->title }}</span>
                </div>
                @php
                    $workStatuses = ['in_progress'=>'В работе','on_review'=>'На проверке',
                                     'needs_revision'=>'Требует доработки','completed'=>'Завершён','cancelled'=>'Отменён'];
                    $workColors   = ['in_progress'=>'primary','on_review'=>'warning',
                                     'needs_revision'=>'danger','completed'=>'success','cancelled'=>'secondary'];
                    $work = $order->work;
                @endphp
                @if($work)
                    <span class="badge bg-{{ $workColors[$work->status] ?? 'secondary' }}">
                        {{ $workStatuses[$work->status] ?? $work->status }}
                    </span>
                @endif
            </div>

            {{-- Messages --}}
            <div class="card-body p-3" style="height:420px; overflow-y:auto;" id="messagesBox">
                @forelse($messages as $msg)
                    @php $mine = $msg->sender_id === auth()->id(); @endphp
                    <div class="d-flex {{ $mine ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                        <div style="max-width:75%">
                            <div class="small text-muted mb-1 {{ $mine ? 'text-end' : '' }}">
                                {{ $msg->sender->name }}
                                · {{ $msg->created_at->format('d.m H:i') }}
                            </div>
                            <div class="p-3 rounded {{ $mine ? 'bg-primary text-white' : 'bg-light border' }}">
                                {{ $msg->body }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-chat fs-1 d-block mb-2"></i>
                        Сообщений пока нет. Начните переписку.
                    </div>
                @endforelse
            </div>

            {{-- Send message --}}
            <div class="card-footer">
                <form method="POST" action="{{ route('messages.store', $order) }}">
                    @csrf
                    <div class="input-group">
                        <textarea name="body" class="form-control" rows="2"
                                  placeholder="Написать сообщение..." required
                                  style="resize:none">{{ old('body') }}</textarea>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar: order status + actions --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Управление заказом</div>
            <div class="card-body">
                @if($work)
                    <p class="small text-muted mb-3">Текущий статус выполнения:</p>
                    <form method="POST" action="{{ route('orders.work.status', $order) }}">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <select name="status" class="form-select form-select-sm">
                                @foreach($workStatuses as $val => $label)
                                    <option value="{{ $val }}" {{ $work->status === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-arrow-repeat me-1"></i>Обновить статус
                        </button>
                    </form>

                    <hr>
                    <div class="small">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Фрилансер</span>
                            <span>{{ $work->freelancer->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Начало</span>
                            <span>{{ $work->started_at?->format('d.m.Y') ?? '—' }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-muted small">Работа ещё не началась.</p>
                @endif
            </div>
        </div>

        {{-- Leave review if completed --}}
        @if($order->isCompleted())
            @php
                $alreadyReviewed = \App\Models\Review::where('order_id', $order->id)
                    ->where('author_id', auth()->id())->exists();
                $recipientId = auth()->id() === $order->client_id
                    ? $order->work?->freelancer_id
                    : $order->client_id;
            @endphp
            @if(!$alreadyReviewed && $recipientId)
                <div class="card">
                    <div class="card-header"><i class="bi bi-star me-2"></i>Оставить отзыв</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reviews.store', $order) }}">
                            @csrf
                            <input type="hidden" name="recipient_id" value="{{ $recipientId }}">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Оценка</label>
                                <div class="d-flex gap-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="form-check form-check-inline">
                                            <input type="radio" name="rating" value="{{ $i }}"
                                                   class="form-check-input" id="r{{ $i }}"
                                                   {{ $i === 5 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="r{{ $i }}">{{ $i }}</label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea name="comment" rows="3" class="form-control form-control-sm"
                                          placeholder="Ваш комментарий..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <i class="bi bi-star-fill me-1"></i>Отправить отзыв
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll to bottom of messages
    const box = document.getElementById('messagesBox');
    if (box) box.scrollTop = box.scrollHeight;
</script>
@endpush
@endsection