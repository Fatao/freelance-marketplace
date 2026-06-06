@extends('layouts.admin')
@section('page_title', 'Отчёты и статистика')

@section('admin_content')

{{-- Period selector --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-sm-3">
                <label class="form-label fw-semibold">Период с</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-sm-3">
                <label class="form-label fw-semibold">Период по</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Применить
                </button>
            </div>
            <div class="col-sm-4">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.export', ['from'=>$from,'to'=>$to,'format'=>'csv']) }}"
                       class="btn btn-outline-success w-100">
                        <i class="fas fa-file-csv me-1"></i>Скачать CSV
                    </a>
                    <a href="{{ route('admin.reports.export', ['from'=>$from,'to'=>$to,'format'=>'xlsx']) }}"
                       class="btn btn-outline-primary w-100">
                        <i class="fas fa-file-excel me-1"></i>Скачать XLSX
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Stats grid --}}
<div class="row g-3 mb-4">
    @php
        $tiles = [
            ['Новые пользователи',    $data['new_users'],          'fas fa-user-plus',   'primary'],
            ['Опубликовано заказов',  $data['orders_published'],   'fas fa-check-circle','success'],
            ['На модерации',          $data['orders_moderation'],  'fas fa-shield-alt',  'warning'],
            ['В работе',              $data['orders_in_progress'], 'fas fa-cog',         'info'],
            ['Завершено',             $data['orders_completed'],   'fas fa-flag',        'success'],
            ['Откликов',              $data['applications'],       'fas fa-paper-plane', 'primary'],
            ['Внешних заказов',       $data['external_found'],     'fas fa-globe',       'secondary'],
            ['Ошибок краулера',       $data['crawler_errors'],     'fas fa-exclamation-triangle', 'danger'],
        ];
    @endphp
    @foreach($tiles as [$label, $value, $icon, $color])
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="small text-muted">{{ $label }}</div>
                        <div class="h4 mb-0 fw-bold text-{{ $color }}">{{ $value }}</div>
                    </div>
                    <i class="{{ $icon }} fa-2x text-{{ $color }} opacity-50"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Orders by day chart --}}
<div class="card">
    <div class="card-header"><i class="fas fa-chart-line me-2"></i>Заказы по дням</div>
    <div class="card-body">
        <canvas id="ordersChart" height="80"></canvas>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($ordersByDay->pluck('date')) !!},
            datasets: [{
                label: 'Заказов создано',
                data: {!! json_encode($ordersByDay->pluck('total')) !!},
                borderColor: '#007bff',
                backgroundColor: 'rgba(0,123,255,0.1)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
@endpush
@endsection