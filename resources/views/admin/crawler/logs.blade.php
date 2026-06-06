@extends('layouts.admin')
@section('page_title', 'Логи краулера')
@section('page_actions')
    <form method="POST" action="{{ route('admin.crawler.runAll') }}" class="d-inline">
        @csrf
        <button class="btn btn-sm btn-primary">
            <i class="fas fa-play me-1"></i>Запустить все
        </button>
    </form>
@endsection

@section('admin_content')

{{-- Quick run per source --}}
<div class="card mb-4">
    <div class="card-header">Быстрый запуск по источнику</div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            @foreach($sources as $source)
                <form method="POST" action="{{ route('admin.crawler.run', $source) }}">
                    @csrf
                    <button class="btn btn-sm {{ $source->isActive() ? 'btn-outline-primary' : 'btn-outline-secondary' }}">
                        <i class="fas fa-play me-1"></i>{{ $source->name }}
                        @if(!$source->isActive())
                            <span class="badge bg-secondary ms-1">откл.</span>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Источник</th>
                    <th>Запуск</th>
                    <th>Найдено</th>
                    <th>Создано</th>
                    <th>Обновлено</th>
                    <th>Архив</th>
                    <th>Ошибок</th>
                    <th>Начало</th>
                    <th>Продолж.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr class="{{ $log->errors > 0 ? 'table-warning' : '' }}">
                        <td class="fw-semibold small">{{ $log->source?->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $log->trigger === 'manual' ? 'bg-primary' : 'bg-secondary' }}">
                                {{ $log->trigger === 'manual' ? 'Вручную' : 'По расписанию' }}
                            </span>
                        </td>
                        <td>{{ $log->found }}</td>
                        <td class="text-success">{{ $log->created }}</td>
                        <td class="text-primary">{{ $log->updated }}</td>
                        <td class="text-muted">{{ $log->archived }}</td>
                        <td class="{{ $log->errors > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $log->errors }}
                            @if($log->error_details)
                                <i class="fas fa-info-circle text-warning ms-1"
                                   title="{{ $log->error_details }}"
                                   data-bs-toggle="tooltip"></i>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $log->started_at->format('d.m H:i') }}</td>
                        <td class="small text-muted">
                            @if($log->finished_at)
                                {{ $log->started_at->diffInSeconds($log->finished_at) }}с
                            @else
                                <span class="text-warning">В процессе</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $logs->links() }}</div>
</div>
@endsection

@push('js')
<script>
    var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipEls.map(el => new bootstrap.Tooltip(el));
</script>
@endpush