<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Insights</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold text-center mb-6">Daftar Insights</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($insights as $insight)
            <div class="bg-white shadow-md rounded-lg overflow-hidden flex flex-col">
                {{-- Tampilkan gambar jika ada --}}
                @if(isset($insight['image_url']) && $insight['image_url'])
                    <img src="{{ $insight['image_url'] }}" class="w-full h-48 object-cover" alt="{{ $insight['judul'] }}">
                @endif

                <div class="p-4 flex flex-col flex-grow">
                    <h5 class="text-lg font-semibold">{{ $insight['judul'] }}</h5>
                    <p class="text-gray-700 text-sm flex-grow">
                        {{ Str::limit(strip_tags($insight['isi']), 100, '...') }}
                    </p>
                    <p class="text-gray-500 text-xs mt-2">Penulis: {{ $insight['penulis'] }}</p>
                    <p class="text-gray-500 text-xs">Terbit: {{ \Carbon\Carbon::parse($insight['TanggalTerbit'])->format('Y-m-d') }}</p>

                    {{-- Pastikan tombol tetap di bawah --}}
                    <a href="{{ route('insights.show', $insight['slug']) }}"
                       class="mt-auto bg-blue-500 text-white text-center py-2 rounded-md hover:bg-blue-600 transition">
                        Baca Selengkapnya
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>
