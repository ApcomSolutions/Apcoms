<header class="header-gradient text-white py-4 px-6">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
        <!-- Title & Refresh Button Section -->
        <div class="flex flex-col md:flex-row items-center w-full md:w-auto mb-4 md:mb-0">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold">{{ $title ?? 'Admin Panel' }}</h1>
                <span class="ml-4 px-2 py-1 bg-white/20 rounded text-xs">{{ $subtitle ?? 'Dashboard' }}</span>
            </div>

            @if(isset($showRefreshButton) && $showRefreshButton)
                <div class="flex items-center mt-2 md:mt-0 md:ml-4">
                    <span class="status-dot active-dot"></span>
                    <span class="text-sm">Live Data</span>
                </div>
            @endif

            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-toggle" class="ml-auto md:hidden p-2 rounded-md text-white hover:bg-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <div id="nav-links" class="hidden md:flex flex-col md:flex-row w-full md:w-auto items-center space-y-4 md:space-y-0 md:space-x-4">
            @if(isset($showRefreshButton) && $showRefreshButton)
                <button id="refresh-btn" class="bg-white/20 hover:bg-white/30 rounded-full p-2 transition md:order-last">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endif

            <a href="/" class="flex items-center text-white hover:text-gray-200 w-full md:w-auto py-2 md:py-0 px-4 md:px-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Kembali ke Website
            </a>

            <a href="{{ route('admin.home') }}" class="flex items-center text-white hover:text-indigo-100 {{ request()->routeIs('admin.home') ? 'font-semibold' : '' }} w-full md:w-auto py-2 md:py-0 px-4 md:px-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Dashboard
            </a>

            <!-- Content Management dropdown (mobile friendly) -->
            <div class="relative w-full md:w-auto dropdown-container">
                <button class="flex items-center justify-between text-white hover:text-indigo-100 {{ request()->routeIs('admin.insights') || request()->routeIs('admin.news.*') || request()->routeIs('admin.categories') || request()->routeIs('admin.news.categories') ? 'font-semibold' : '' }} w-full md:w-auto py-2 md:py-0 px-4 md:px-0">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />
                            <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />
                        </svg>
                        Konten
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="dropdown-menu hidden bg-white md:absolute md:left-0 md:mt-2 w-full md:w-56 rounded-md shadow-lg py-1 z-10">
                    <div class="border-b border-gray-200 pb-2 mb-1">
                        <h3 class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Artikel</h3>
                        <a href="{{ route('admin.insights') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-100 hover:text-indigo-900 {{ request()->routeIs('admin.insights') ? 'bg-indigo-100 text-indigo-900' : '' }}">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                                Kelola Insights
                            </span>
                        </a>
                        <a href="{{ route('admin.categories') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-100 hover:text-indigo-900 {{ request()->routeIs('admin.categories') ? 'bg-indigo-100 text-indigo-900' : '' }}">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Kategori Insights
                            </span>
                        </a>
                    </div>
                    <div>
                        <h3 class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Berita</h3>
                        <a href="{{ route('admin.news.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-100 hover:text-indigo-900 {{ request()->routeIs('admin.news.index') ? 'bg-indigo-100 text-indigo-900' : '' }}">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                                Kelola Berita
                            </span>
                        </a>
                        <a href="{{ route('admin.news.categories') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-100 hover:text-indigo-900 {{ request()->routeIs('admin.news.categories') ? 'bg-indigo-100 text-indigo-900' : '' }}">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Kategori Berita
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Resource Management dropdown -->
            <div class="relative w-full md:w-auto dropdown-container">
                <button class="flex items-center justify-between text-white hover:text-indigo-100 {{ request()->routeIs('admin.teams') || request()->routeIs('admin.clients') || request()->routeIs('admin.gallery') ? 'font-semibold' : '' }} w-full md:w-auto py-2 md:py-0 px-4 md:px-0">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                        </svg>
                        Sumber Daya
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="dropdown-menu hidden bg-white md:absolute md:left-0 md:mt-2 w-full md:w-48 rounded-md shadow-lg py-1 z-10">
                    <a href="{{ route('admin.teams') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-100 hover:text-indigo-900 {{ request()->routeIs('admin.teams') ? 'bg-indigo-100 text-indigo-900' : '' }}">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3H5a3 3 0 00-3 3v2h5m10 0h2m-2-4v-2a3 3 0 00-3-3H9a3 3 0 00-3 3v2m9 2h2" />
                            </svg>
                            Tim
                        </span>
                    </a>
                    <a href="{{ route('admin.clients') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-100 hover:text-indigo-900 {{ request()->routeIs('admin.clients') ? 'bg-indigo-100 text-indigo-900' : '' }}">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Klien
                        </span>
                    </a>
                    <a href="{{ route('admin.gallery') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-100 hover:text-indigo-900 {{ request()->routeIs('admin.gallery') ? 'bg-indigo-100 text-indigo-900' : '' }}">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Galeri
                        </span>
                    </a>
                </div>
            </div>

            <!-- Logout Button -->
            <form action="{{ route('logout') }}" method="POST" class="w-full md:w-auto md:ml-4">
                @csrf
                <button type="submit" class="logout-btn flex items-center bg-red-500 hover:bg-red-600 text-white rounded-md py-1 px-3 transition-colors duration-200 w-full md:w-auto mt-4 md:mt-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Add necessary JavaScript for mobile menu toggle and dropdowns -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const navLinks = document.getElementById('nav-links');

        if (mobileMenuToggle && navLinks) {
            mobileMenuToggle.addEventListener('click', function() {
                navLinks.classList.toggle('hidden');
            });
        }

        // Handle dropdown toggles
        const dropdownContainers = document.querySelectorAll('.dropdown-container');

        dropdownContainers.forEach(container => {
            const button = container.querySelector('button');
            const menu = container.querySelector('.dropdown-menu');
            const arrow = container.querySelector('.dropdown-arrow');

            // Toggle dropdown on click for both mobile and desktop
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle current dropdown
                menu.classList.toggle('hidden');

                // Toggle arrow rotation
                if (arrow) {
                    arrow.classList.toggle('transform');
                    arrow.classList.toggle('rotate-180');
                }

                // Close other dropdowns
                dropdownContainers.forEach(otherContainer => {
                    if (otherContainer !== container) {
                        const otherMenu = otherContainer.querySelector('.dropdown-menu');
                        const otherArrow = otherContainer.querySelector('.dropdown-arrow');

                        if (otherMenu && !otherMenu.classList.contains('hidden')) {
                            otherMenu.classList.add('hidden');

                            if (otherArrow) {
                                otherArrow.classList.remove('transform');
                                otherArrow.classList.remove('rotate-180');
                            }
                        }
                    }
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            dropdownContainers.forEach(container => {
                if (!container.contains(e.target)) {
                    const menu = container.querySelector('.dropdown-menu');
                    const arrow = container.querySelector('.dropdown-arrow');

                    if (menu && !menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');

                        if (arrow) {
                            arrow.classList.remove('transform');
                            arrow.classList.remove('rotate-180');
                        }
                    }
                }
            });
        });
    });
</script>

<style>
    /* Add transition effects */
    .dropdown-menu {
        transition: all 0.2s ease-in-out;
    }

    .dropdown-arrow {
        transition: transform 0.2s ease-in-out;
    }

    /* Active link styles */
    .font-semibold {
        position: relative;
    }

    .font-semibold::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: white;
        border-radius: 2px;
    }

    /* Logout button hover effect */
    .logout-btn {
        position: relative;
        overflow: hidden;
    }

    .logout-btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%);
        transform-origin: 50% 50%;
    }

    .logout-btn:hover::after {
        animation: ripple 1s ease-out;
    }

    @keyframes ripple {
        0% {
            transform: scale(0, 0);
            opacity: 0.5;
        }
        100% {
            transform: scale(20, 20);
            opacity: 0;
        }
    }

    /* Mobile styles */
    @media (max-width: 768px) {
        .dropdown-menu {
            position: static;
            box-shadow: none;
            background-color: rgba(255, 255, 255, 0.1);
            margin-top: 0;
            margin-left: 24px;
            border-radius: 0;
        }

        .dropdown-menu a,
        .dropdown-menu h3 {
            color: white !important;
        }

        .dropdown-menu a:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }

        .dropdown-menu a.bg-indigo-100 {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }

        /* Logout button mobile style */
        .logout-btn {
            justify-content: flex-start;
            background-color: rgba(239, 68, 68, 0.8);
            padding-left: 1rem;
        }

        .logout-btn:hover {
            background-color: rgba(239, 68, 68, 1);
        }

        /* Border styling */
        .border-b {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
    }
</style>
