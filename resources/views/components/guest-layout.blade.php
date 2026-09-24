<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
        main { flex: 1; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand text-white fw-bold" href="{{ route('home') }}">
            <i class="bi bi-briefcase-fill me-2 text-primary"></i>ФрилансМаркет
        </a>
    </div>
</nav>
<main class="container my-5">
    {{ $slot }}
</main>
<footer class="bg-dark text-white py-3 mt-auto">
    <div class="container text-center">
        <small class="text-white-50">&copy; {{ date('Y') }} ФрилансМаркет</small>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>