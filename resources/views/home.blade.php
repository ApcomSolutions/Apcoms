<x-layout>
    <x-navbar></x-navbar>
    {{-- Hero Section --}}
    <div class="relative bg-cover bg-center bg-no-repeat text-white min-h-[80vh] flex items-center justify-center py-20 px-6 md:px-12"
        style="background-image: url('{{ asset('images/hero.png') }}');">

        <!-- Overlay for better text visibility -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Container -->
        <div class="container mx-auto flex flex-col md:flex-row items-center relative z-10">
            <!-- Text Content -->
            <div class="md:w-[60%] w-full mb-8 md:mb-0 p-6 md:p-8 bg-gray-900 bg-opacity-70 rounded-lg">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 gradient-text">
                    Mitra Strategis Reputasi Digital Anda
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl mb-6 font-medium">
                    Membangun dan Mempertahankan Reputasi Terbaik di Era Digital
                </p>
                <p class="text-sm sm:text-base md:text-lg mb-8 leading-relaxed">
                    Kami menggabungkan kreativitas, inovasi, dan pendekatan berkelanjutan untuk mendukung kebutuhan
                    komunikasi bisnis Anda. Dengan layanan unggulan mulai dari pendampingan humas kreatif hingga
                    publikasi ilmiah, kami siap membawa reputasi Anda ke tingkat yang lebih tinggi.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#"
                        class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-3 px-6 rounded-md shadow-md transition duration-300 text-center">
                        Konsultasi Sekarang
                    </a>
                    <a href="#"
                        class="bg-transparent border-2 border-gradient hover:bg-gradient-to-r hover:from-pink-500 hover:to-blue-500 hover:text-white text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-blue-500 font-medium py-3 px-6 rounded-md shadow-md transition duration-300 text-center">
                        Lihat Layanan Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
    <x-footer></x-footer>

    <style>
        .gradient-text {
            background-image: linear-gradient(to right, #ec4899, #3b82f6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }

        .border-gradient {
            border-image: linear-gradient(to right, #ec4899, #3b82f6) 1;
        }
    </style>
</x-layout>
