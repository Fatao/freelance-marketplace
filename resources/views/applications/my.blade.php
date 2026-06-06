@extends('layouts.app')
@section('title', 'Мои отклики')
@section('content')

<h4 class="mb-4"><i class="bi bi-send me-2"></i>Мои отклики</h4>

@if($applications->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-send fs-1 d-block mb-3"></i>
            Вы ещё не откликались на заказы.
            <div class="mt-3">
                <a href="{{ route('orders.index') }}" class="btn btn-primary">Найти заказы</a>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Заказ</th>
                        <th>Заказчик</th>
                        <th>Предложена сумма</th>
                        <th>Срок</th>
                        <th>Статус отклика</th>
                        <th>Дата</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                        <tr>
                            <td>
                                <a href="{{ route('orders.show', $app->order) }}" class="text-decoration-none fw-semibold">
                                    {{ Str::limit($app->order->title, 40) }}
                                </a>
                            </td>
                            <td class="small text-muted">
                                {{ $app->order->client->clientProfile?->company_name ?? $app->order->client->name }}
                            </td>
                            <td class="small">
                                {{ $app->proposed_price ? number_format($app->proposed_price,0,'.',' ') . ' ₽' : '—' }}
                            </td>
                            <td class="small">
                                {{ $app->proposed_days ? $app->proposed_days . ' дн.' : '—' }}
                            </td>
                            <td>
                                @php
                                    $statusMap = [
                                        'sent'      => ['Отправлен',  'secondary'],
                                        'viewed'    => ['Просмотрен', 'info'],
                                        'accepted'  => ['Принят',     'success'],
                                        'rejected'  => ['Отклонён',   'danger'],
                                        'withdrawn' => ['Отозван',    'secondary'],
                                    ];
                                    [$sl, $sc] = $statusMap[$app->status] ?? ['—','secondary'];
                                @endphp
                                <span class="badge bg-{{ $sc }}">{{ $sl }}</span>
                            </td>
                            <td class="small text-muted">{{ $app->created_at->format('d.m.Y') }}</td>
                            <td>
                                @if($app->status === 'accepted')
                                    <a href="{{ route('messages.index', $app->order) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-chat-dots me-1"></i>Переписка
                                    </a>
                                @elseif(!in_array($app->status, ['rejected','withdrawn']))
                                    <form method="POST" action="{{ route('applications.withdraw', $app) }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Отозвать</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $applications->links() }}</div>
    </div>
@endif
@endsection