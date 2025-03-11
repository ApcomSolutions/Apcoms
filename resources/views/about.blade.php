{{-- resources/views/about.blade.php --}}
<x-layout>
    <x-navbar></x-navbar>

    <!-- About Us Section dengan padding tambahan di kiri dan kanan -->
    <div class="container mx-auto py-12 px-6 md:px-12 lg:px-24 mt-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-12">
            <!-- Bagian Kiri: About Us, Nama Perusahaan, dan Deskripsi -->
            <div class="w-full md:w-1/2 space-y-6">
                <!-- About Us Badge -->
                <span class="inline-block bg-blue-100 text-black px-4 py-2 rounded-md font-bold">ABOUT US</span>

                <!-- Nama Perusahaan -->
                <h1 class="text-5xl font-bold text-black mt-4 mb-6">PT. Solusi Komunikasi Terapan</h1>

                <!-- Deskripsi Perusahaan -->
                <p class="text-gray-700 text-lg">
                    Apcom Solution adalah mitra strategis anda dalam membangun dan
                    mempertahankan reputasi terbaik di era digital. Kami menggabungkan
                    kreativitas, inovasi, dan pendekatan berkelanjutan untuk mendukung
                    kebutuhan komunikasi bisnis anda. Dengan layanan unggulan seperti
                    pendampingan humas kreatif, corporate public relations, pembuatan
                    konten untuk F2B (fashion, food & beverage), riset dan analisis data,
                    hingga publikasi ilmiah dan pendidikan inovatif, kami siap membawa
                    reputasi anda ke tingkat yang lebih tinggi.
                </p>

                <!-- Space untuk YouTube Video - akan ditambahkan di bawah deskripsi -->
                <div class="mt-8">
                    <div class="bg-gray-100 rounded-lg overflow-hidden aspect-w-16 aspect-h-9 relative">
                        <!-- Placeholder untuk YouTube Video API -->
                        <div id="youtube-player" class="w-full h-64 flex items-center justify-center">
                            <i class="fas fa-play-circle text-6xl text-gray-500"></i>
                            <p class="ml-4 text-gray-500">Video Perusahaan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Kanan: Gambar dalam lingkaran dengan border biru -->
            <div class="w-full md:w-1/2 flex justify-center">
                <div class="rounded-image-container w-full max-w-xl">
                    <img src="{{ asset('images/about.png') }}" alt="Tim Kerja PT. Solusi Komunikasi Terapan"
                         class="w-full h-auto">
                </div>
            </div>
        </div>
    </div>

    <!-- Why Choose Us Section with blue-100 background -->
    <div class="bg-blue-100 py-16 px-6 md:px-12 lg:px-24">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row items-start justify-between gap-12">
                <!-- Bagian Kiri: Judul dan Deskripsi -->
                <div class="w-full md:w-1/2">
                    <!-- Why Choose Us Badge -->
                    <span class="inline-block bg-white text-black px-4 py-2 rounded-md font-bold mb-4">WHY CHOOSE
                        US</span>

                    <!-- Judul -->
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">Solusi Komunikasi yang Berorientasi pada
                        Keberlanjutan</h2>

                    <!-- Deskripsi -->
                    <p class="text-gray-700 text-lg">
                        Di Apcom Solution, kami percaya bahwa reputasi adalah aset yang paling berharga.
                        Dengan layanan unggulan di bidang humas kreatif, manajemen hubungan publik perusahaan,
                        pembuatan konten F2B (fashion, food & beverage), riset dan analisis data, hingga
                        publikasi ilmiah dan pendidikan inovatif, kami hadir untuk membantu anda
                        menciptakan solusi komunikasi yang terintegrasi, adaptif, dan berdampak.
                    </p>
                </div>

                <!-- Bagian Kanan: 3 Points Bertumpuk -->
                <div class="w-full md:w-1/2 space-y-6">
                    <!-- Point 1 -->
                    <div class="bg-white p-6 rounded-lg shadow-md flex items-start">
                        <div class="text-blue-600 mr-4">
                            <i class="fas fa-star text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Berorientasi Pada Reputasi</h3>
                            <p class="text-gray-700">
                                Fokus kami adalah membantu anda menciptakan citra dan hubungan yang kuat dengan para
                                pemangku kepentingan.
                            </p>
                        </div>
                    </div>

                    <!-- Point 2 -->
                    <div class="bg-white p-6 rounded-lg shadow-md flex items-start">
                        <div class="text-blue-600 mr-4">
                            <i class="fas fa-cogs text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Layanan Terintegrasi</h3>
                            <p class="text-gray-700">
                                Dari strategi hingga implementasi, kami memberikan solusi komunikasi yang disesuaikan
                                dengan kebutuhan anda.
                            </p>
                        </div>
                    </div>

                    <!-- Point 3 -->
                    <div class="bg-white p-6 rounded-lg shadow-md flex items-start">
                        <div class="text-blue-600 mr-4">
                            <i class="fas fa-chart-line text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Dampak Nyata</h3>
                            <p class="text-gray-700">
                                Kami bekerja untuk memberikan hasil yang relevan, terukur, dan berkelanjutan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section Component -->
    <x-team-section></x-team-section>

    <x-contact></x-contact>

    <!-- Script untuk YouTube API -->
    <script>
        // Load YouTube API
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var player;

        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtube-player', {
                height: '100%',
                width: '100%',
                videoId: 'VIDEO_ID', // Ganti dengan ID video YouTube
                playerVars: {
                    'autoplay': 0,
                    'rel': 0
                }
            });
        }
    </script>
    <x-footer></x-footer>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/team.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('js/team.js') }}"></script>
    @endpush
</x-layout>
