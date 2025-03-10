<!-- resources/views/components/navbar.blade.php -->
<nav class="bg-white/80 shadow-md fixed top-0 left-0 w-full z-50 backdrop-blur-md transition-all duration-300"
    x-data="{ mobileMenuOpen: false, serviceOpen: false, insightOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })" :class="{ 'shadow-lg bg-white/95': scrolled }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <!-- Logo -->
                    <a href="#" class="flex items-center">
                        <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="APCOM SOLUTIONS">
                    </a>
                </div>
            </div>

            <!-- Main navigation links - pushed to the right -->
            <div class="hidden sm:flex sm:items-center sm:space-x-8">
                <!-- Navigation Links -->
                <a href="{{ route('home') }}"
                    class="border-transparent text-gray-700 hover:border-yellow-400 hover:text-yellow-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Home
                </a>
                <a href="#"
                    class="border-transparent text-gray-700 hover:border-yellow-400 hover:text-yellow-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    About Us
                </a>

                <!-- Service Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="border-transparent text-gray-700 hover:border-yellow-400 hover:text-yellow-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Service
                        <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Digital
                                Creative</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Corporate
                                Public
                                Relation</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Konten
                                Kreator Fashion Food & Beverage</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Penerbitan
                                Buku</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Publikasi
                                Ilmiah</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Riset
                                dan Data Analisis</a>
                        </div>
                    </div>
                </div>

                <!-- Insight & Berita Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="border-transparent text-gray-700 hover:border-yellow-400 hover:text-yellow-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Insight & News
                        <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 z-10 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-1">
                        <div class="py-1">
                            <a href="{{ route('insights.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Insight</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">News</a>
                        </div>
                    </div>
                </div>

                <!-- Contact button integrated into main nav -->
                <button type="button"
                    class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Contact Us
                </button>
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
            <a href="#"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">Home</a>
            <a href="#"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">About
                Us</a>

            <!-- Mobile Service Dropdown -->
            <div>
                <button @click="serviceOpen = !serviceOpen"
                    class="w-full flex justify-between items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
                    Service
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        :class="{ 'transform rotate-180': serviceOpen }">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="serviceOpen" class="pl-6">
                    <a href="#"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Digital
                        Creative</a>
                    <a href="#"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Corporate
                        Public Relation</a>
                    <a href="#"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Konten
                        Kreator Fashion Food & Beverage</a>
                    <a href="#"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Penerbitan
                        Buku</a>
                    <a href="#"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Publikasi
                        Ilmiah</a>
                    <a href="#"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Riset
                        dan Data Analisis</a>
                </div>
            </div>

            <!-- Mobile Insight & Berita Dropdown -->
            <div>
                <button @click="insightOpen = !insightOpen"
                    class="w-full flex justify-between items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
                    Insight & News
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        :class="{ 'transform rotate-180': insightOpen }">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="insightOpen" class="pl-6">
                    <a href="#"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">Insight</a>
                    <a href="#"
                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700">News</a>
                </div>
            </div>

            <!-- Mobile Contact Button -->
            <a href="#"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-pink-500 hover:bg-gray-50 hover:border-pink-300 hover:text-pink-700">
                Contact Now
            </a>
        </div>
    </div>
</nav>
