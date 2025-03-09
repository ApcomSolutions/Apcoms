<!DOCTYPE html>
<html lang="en" class="h-full bg-white">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite([
        'resources/css/app.css',
        'resources/css/admin/insight-crud.css',
        'resources/js/app.js',
        'resources/js/cart.js',
        'resources/js/admin/insight-crud.js',
        'resources/js/admin/dashboard.js'
    ])
    {{-- font awesome --}}
    <script src="https://kit.fontawesome.com/e20865611c.js" crossorigin="anonymous"></script>

    <title>{{ $title ?? 'APCOM Solutions' }}</title>

    @stack('styles')
</head>

<body class="h-full bg-white">
<div class="min-h-full bg-white">
    <main class="bg-white">
        {{ $slot }}
    </main>
</div>

@stack('scripts')
</body>

</html>
