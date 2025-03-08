<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/cart.js'])

    {{-- font awesome --}}
    <script src="https://kit.fontawesome.com/e20865611c.js" crossorigin="anonymous"></script>

    <title>Home</title>
</head>

<body class="h-full">
    <div class="min-h-full">
        <main class="bg-neutral-900">
            <!-- Your content -->
            {{ $slot }}
        </main>
    </div>
</body>

</html>
