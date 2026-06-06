@extends('adminlte::page')

@section('title', 'Администратор')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0">@yield('page_title', 'Панель администратора')</h4>
        @yield('page_actions')
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    @yield('admin_content')
@endsection

@push('css')
    <style>
        .card-header { font-weight: 600; }
        .table td { vertical-align: middle; }
    </style>
@endpush