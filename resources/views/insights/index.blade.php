{{-- resources/views/insights/index.blade.php --}}
<x-layout>
    <x-navbar />

    <div class="bg-white py-10">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-center mb-8 mt-8">Daftar Insights</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($insights as $insight)
                    <div class="bg-white shadow-md rounded-lg overflow-hidden flex flex-col h-full">
                        {{-- Tampilkan gambar jika ada --}}
                        @if (isset($insight['image_url']) && $insight['image_url'])
                            <img src="{{ $insight['image_url'] }}" class="w-full h-48 object-cover"
                                alt="{{ $insight['judul'] }}">
                        @endif

                        <div class="p-4 flex flex-col flex-grow">
                            <h5 class="text-lg font-semibold mb-2">{{ $insight['judul'] }}</h5>
                            <p class="text-gray-700 text-sm mb-4 flex-grow">
                                {{ Str::limit(strip_tags($insight['isi']), 100, '...') }}
                            </p>

                            <div class="mt-auto">
                                <div class="flex justify-between text-gray-500 text-xs mb-3">
                                    <p>Penulis: {{ $insight['penulis'] }}</p>
                                    <p>{{ \Carbon\Carbon::parse($insight['TanggalTerbit'])->format('d M Y') }}</p>
                                </div>

                                <a href="{{ route('insights.show', $insight['slug']) }}"
                                    class="block w-full bg-pink-500 text-white text-center py-2 rounded-md hover:bg-pink-600 transition">
                                    Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination - only include if $insights is a paginator instance --}}
            @if (method_exists($insights, 'links'))
                <div class="mt-8">
                    {{ $insights->links() }}
                </div>
            @endif
        </div>
    </div>

    <x-footer />
</x-layout>
