<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
<div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
    <div class="flex items-center text-red-500 mb-4">
        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h1 class="text-xl font-bold">Server Error</h1>
    </div>
    <p class="text-gray-700 mb-4">{{ $message }}</p>
    <div class="flex justify-between mt-6">
        <a href="{{ url('/') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            Kembali ke Beranda
        </a>
        <a href="{{ url()->previous() }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Kembali
        </a>
    </div>
</div>

<script src="{{ asset('js/CustomErrorHandler.js') }}"></script>
<script>
    // Init error handler
    document.addEventListener('DOMContentLoaded', function() {
        if (window.ErrorHandler && window.ErrorHandler.showError) {
            window.ErrorHandler.showError("{{ $message }}");
        }
    });
</script>
</body>
</html>
