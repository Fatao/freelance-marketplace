@extends('layouts.admin')
@section('page_title', 'Заявки на смену роли')
@section('page_actions')
    @if($pendingCount > 0)
        <span class="badge bg-warning fs-6">{{ $pendingCount }} на рассмотрении</span>
    @endif
@endsection

@section('admin_content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Пользователь</th>
                    <th>Компания</th>
                    <th>Телефон</th>
                    <th>Причина</th>
                    <th>Статус</th>
                    <th>Дата</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr class="{{ $req->status === 'pending' ? 'table-warning' : '' }}">
                        <td>
                            <div class="fw-semibold">{{ $req->user->name }}</div>
                            <small class="text-muted">{{ $req->user->email }}</small>
                        </td>
                        <td>{{ $req->company_name }}</td>
                        <td>{{ $req->phone }}</td>
                        <td>
                            <small>{{ Str::limit($req->reason, 60) ?? '—' }}</small>
                        </td>
                        <td>
                            @php
                                $colors = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'];
                                $labels = ['pending'=>'На рассмотрении','approved'=>'Одобрена','rejected'=>'Отклонена'];
                            @endphp
                            <span class="badge bg-{{ $colors[$req->status] }}">
                                {{ $labels[$req->status] }}
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $req->created_at->format('d.m.Y H:i') }}</small></td>
                        <td>
                            @if($req->status === 'pending')
                                <div class="d-flex gap-1">
                                    <form method="POST"
                                          action="{{ route('admin.role-requests.approve', $req) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success"
                                                onclick="return confirm('Одобрить и назначить роль Заказчика?')">
                                            <i class="fas fa-check me-1"></i>Одобрить
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-danger"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#reject_{{ $req->id }}">
                                        <i class="fas fa-times me-1"></i>Отклонить
                                    </button>
                                </div>
                                <div class="collapse mt-2" id="reject_{{ $req->id }}">
                                    <form method="POST"
                                          action="{{ route('admin.role-requests.reject', $req) }}"
                                          class="d-flex gap-2">
                                        @csrf @method('PATCH')
                                        <input type="text" name="admin_note"
                                               class="form-control form-control-sm"
                                               placeholder="Причина отклонения...">
                                        <button type="submit" class="btn btn-danger btn-sm">ОК</button>
                                    </form>
                                </div>
                            @else
                                <small class="text-muted">
                                    {{ $req->admin_note ?? '—' }}
                                </small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox me-2"></i>Заявок нет
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $requests->links() }}</div>
</div>
@endsection