@extends('layouts.admin')
@section('page_title', 'Источники краулера')
@section('page_actions')
    <a href="{{ route('admin.sources.create') }}" class="btn btn-sm btn-success">
        <i class="fas fa-plus me-1"></i>Добавить источник
    </a>
@endsection

@section('admin_content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Название</th>
                    <th>URL</th>
                    <th>Статус</th>
                    <th>Заказов найдено</th>
                    <th>Периодичность</th>
                    <th>Последний запуск</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sources as $source)
                    <tr>
                        <td class="fw-semibold">{{ $source->name }}</td>
                        <td><a href="{{ $source->base_url }}" target="_blank" class="small">{{ Str::limit($source->base_url, 40) }}</a></td>
                        <td>
                            @php $sc = ['active'=>'success','disabled'=>'secondary','error'=>'danger'][$source->status] @endphp
                            <span class="badge bg-{{ $sc }}">{{ $source->status }}</span>
                        </td>
                        <td>{{ $source->external_orders_count }}</td>
                        <td><small>каждые {{ $source->frequency_minutes }} мин</small></td>
                        <td><small class="text-muted">{{ $source->last_run_at?->diffForHumans() ?? 'Никогда' }}</small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.sources.edit', $source) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.crawler.run', $source) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-primary" title="Запустить">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.sources.destroy', $source) }}"
                                      onsubmit="return confirm('Удалить источник?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection