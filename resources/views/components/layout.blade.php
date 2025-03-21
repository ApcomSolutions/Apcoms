{{-- resources/views/components/layout.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <!-- SEO Tags -->
    {!! seo()->for($seoData ?? null) !!}

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Changed CDN for Dropzone from unpkg to cdnjs -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css"
        type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/team.css', 'resources/css/news-trix.css', 'resources/css/admin/insight-crud.css', 'resources/js/app.js', 'resources/js/CustomErrorHandler.js'])

    <!-- Load route-specific JavaScript files -->
    @if (request()->routeIs('admin.insights'))
        @vite(['resources/js/admin/insight-crud.js', 'resources/js/admin/dropzone-config.js'])
    @endif

    @if (request()->routeIs('admin.categories'))
        @vite(['resources/js/admin/category-crud.js'])
    @endif

    @if (request()->routeIs('admin.news.index'))
        @vite(['resources/js/news.js'])
    @endif

    @if (request()->routeIs('admin.news.categories'))
        @vite(['resources/js/news-categories-standalone.js'])
    @endif

    @if (request()->routeIs('admin.dashboard'))
        @vite(['resources/js/admin/dashboard.js'])
    @endif

    @if (request()->routeIs('admin.teams'))
        @vite(['resources/js/admin/teams.js', 'resources/js/team.js'])
    @endif

    @if (request()->routeIs('admin.clients'))
        @vite(['resources/js/admin/clients.js'])
    @endif

    @if (request()->routeIs('admin.gallery'))
        @vite(['resources/js/admin/gallery.js', 'resources/js/gallery-carousel.js'])
    @endif

    @if (request()->routeIs('home'))
        @vite(['resources/js/cart.js'])
    @endif

    {{-- font awesome --}}
    <script src="https://kit.fontawesome.com/e20865611c.js" crossorigin="anonymous"></script>

    <!-- Load AOS Library  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <title>{{ $title ?? 'ApCom Solutions - Membangun Reputasi Menciptakan Solusi' }}</title>

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @stack('styles')
</head>

<body class="h-full bg-white">
    <div class="min-h-full bg-white">
        <main class="bg-white zoom-responsive">
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/dist/chartjs-adapter-moment.min.js"></script>
</body>

</html>
