@extends('adminlte::page')

@section('title', 'Администратор')

@push('css')
<style>
    .brand-image,
    .brand-image-xl,
    img.brand-image {
        display: none !important;
    }

    .brand-link {
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        color: #fff !important;
        text-decoration: none !important;
    }

    .brand-link:hover {
        color: #adb5bd !important;
    }

    .brand-link .brand-text {
        font-size: 1.1rem;
        font-weight: 700;
    }

    .brand-link b {
        color: #4e9af1;
    }

    .navbar-dark {
        background: #1a1a2e !important;
    }
</style>
@endpush

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