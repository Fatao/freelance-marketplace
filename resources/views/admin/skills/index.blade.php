@extends('layouts.admin')
@section('page_title', 'Навыки')

@section('admin_content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Добавить навык</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.skills.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Название</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Категория</label>
                        <select name="category_id" class="form-select">
                            <option value="">Без категории</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
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
                        <tr><th>Навык</th><th>Категория</th><th>Slug</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($skills as $skill)
                            <tr>
                                <td>{{ $skill->name }}</td>
                                <td class="small text-muted">{{ $skill->category?->name ?? '—' }}</td>
                                <td><code class="small">{{ $skill->slug }}</code></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}"
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
            <div class="card-footer">{{ $skills->links() }}</div>
        </div>
    </div>
</div>
@endsection