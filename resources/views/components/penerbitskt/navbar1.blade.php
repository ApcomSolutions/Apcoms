<!-- resources/views/components/navbar.blade.php -->
<nav class="bg-white/80 shadow-md fixed top-0 left-0 w-full z-50 backdrop-blur-md transition-all duration-300"
    x-data="{ mobileMenuOpen: false, serviceOpen: false, insightOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })" :class="{ 'shadow-lg bg-white/95': scrolled }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo & Teks -->
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <a href="#" class="flex items-center">
                        <img class="h-8 w-auto" src="{{ asset('images/icon.png') }}" alt="APCOM SOLUTIONS">
                    </a>
                </div>
                <span class="text-lg font-semibold text-gray-900">PT SOLUSI KOMUNIKASI TERAPAN</span>
            </div>

            <!-- Main navigation links - pushed to the right -->
            <div class="hidden sm:flex sm:items-center sm:space-x-8">
                <!-- Navigation Links -->
                <a href="{{ route('penerbitskt') }}"
                    class="border-transparent text-gray-700 hover:border-pink-400 hover:text-pink-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Home
                </a>
                <a href="penerbitskt/layanan"
                    class="border-transparent text-gray-700 hover:border-pink-400 hover:text-pink-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Layanan
                </a>
                <a href="penerbitskt/produk"
                    class="border-transparent text-gray-700 hover:border-pink-400 hover:text-pink-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Terkait Produk
                </a>
                <a href="https://wa.me/628125881289"
                    class="border-transparent text-gray-700 hover:border-pink-400 hover:text-pink-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Kirim Naskah
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <!-- Icon when menu is closed -->
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('penerbitskt') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">Home</a>
            <a href="penerbitskt/layanan"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">Layanan</a>
            <a href="penerbitskt/produk"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">Terkait
                Produk</a>
            <a href="penerbitskt/contact"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">Kirim
                Naskah</a>

        </div>
    </div>
</nav>
