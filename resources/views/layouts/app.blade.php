<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lista de Presentes')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}"><!-- opcional, mas bom ter -->

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <x-header>
        @yield('header', 'Lucas e Nathália')
    </x-header>

    <main>
        @yield('content')
    </main>

    <!-- JS no final do body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- MUITO IMPORTANTE: injeta os scripts das views que usam @push('scripts') --}}
    @stack('scripts')
</body>
</html>
