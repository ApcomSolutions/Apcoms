<!DOCTYPE html>
<html lang="en" class="h-full bg-white">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css','resources/css/team.css', 'resources/css/admin/insight-crud.css', 'resources/js/app.js',
            'resources/js/cart.js', 'resources/js/admin/insight-crud.js', 'resources/js/admin/dashboard.js',
             'resources/js/admin/teams.js', 'resources/js/admin/clients.js', 'resources/js/admin/gallery.js',
             'resources/js/admin/category-crud.js', 'resources/js/gallery-carousel.js','resources/js/team.js',])
    {{-- font awesome --}}
    <script src="https://kit.fontawesome.com/e20865611c.js" crossorigin="anonymous"></script>

    <!-- Load AOS Library  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <title>{{ $title ?? 'APCOM Solutions' }}</title>

    <style>
        html {
            scroll-behavior: smooth;
        }

        @media screen and (min-width: 1024px) {
            .zoom responsive {
                zoom: 0.8;
            }
        }

        @media screen and (min-width: 1280px) {
            .zoom responsive {
                zoom: 1;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="h-full bg-white">
    <div class="min-h-full bg-white">
        <main class="bg-white zoom-responsive">
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>

</html>
