@extends('layouts.admin')
@section('page_title', 'Категории')

@section('admin_content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Добавить категорию</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Название</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Добавить
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Название</th><th>Slug</th><th>Заказов</th><th>Статус</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                            <tr>
                                <td>{{ $cat->name }}</td>
                                <td><code class="small">{{ $cat->slug }}</code></td>
                                <td>{{ $cat->orders_count }}</td>
                                <td>
                                    <span class="badge bg-{{ $cat->is_active ? 'success' : 'secondary' }}">
                                        {{ $cat->is_active ? 'Активна' : 'Отключена' }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                          onsubmit="return confirm('Удалить?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection