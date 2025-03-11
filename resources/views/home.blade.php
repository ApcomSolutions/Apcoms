<x-layout>
    <x-navbar></x-navbar>
    {{-- Hero Section --}}
    <div class="relative min-h-[80vh] flex items-center mt-10 py-40 justify-center py-20 px-6 md:px-12 overflow-hidden">
        <!-- Pola latar belakang dengan gradient dan shapes -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-pink-50">
            <!-- Decorative shapes -->
            <div class="absolute top-5 left-10 w-64 h-64 rounded-full bg-pink-300 opacity-70 mix-blend-multiply parallax"
                data-speed="3"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 rounded-full bg-blue-300 opacity-70 mix-blend-multiply parallax"
                data-speed="2"></div>
            <div class="absolute top-1/4 right-1/4 w-40 h-40 rounded-full bg-indigo-400 opacity-60 mix-blend-multiply parallax"
                data-speed="4"></div>

            <!-- Subtle grid pattern overlay -->
            <div class="absolute inset-0 opacity-10"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%236b7280\' fill-opacity=\'0.2\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'1\'/%3E%3C/g%3E%3C/svg%3E');">
            </div>
        </div>

        <!-- Container -->
        <div class="container mx-auto flex flex-col md:flex-row items-center relative z-10">
            <!-- Text Content -->
            <div
                class="md:w-[60%] w-full mb-8 md:mb-0 p-6 md:p-8 bg-white bg-opacity-80 rounded-lg shadow-lg backdrop-blur-sm border border-gray-100">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-6 pb-2 gradient-text">
                    Mitra Strategis Reputasi Digital Anda
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl mb-6 font-medium text-gray-700">
                    Membangun dan Mempertahankan Reputasi Terbaik di Era Digital
                </p>
                <p class="text-sm sm:text-base md:text-lg mb-8 leading-relaxed text-gray-600">
                    Kami menggabungkan kreativitas, inovasi, dan pendekatan berkelanjutan untuk mendukung kebutuhan
                    komunikasi bisnis Anda. Dengan layanan unggulan mulai dari pendampingan humas kreatif hingga
                    publikasi ilmiah, kami siap membawa reputasi Anda ke tingkat yang lebih tinggi.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="https://wa.me/628125881289"
                        class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-3 px-8 rounded-full shadow-md transition duration-300 text-center">
                        Konsultasi Sekarang
                    </a>
                    <a href="#service"
                        class="border-gradient font-medium py-3 px-8 rounded-full shadow-md text-center transition duration-300">
                        <span
                            class="bg-gradient-to-r from-pink-500 to-blue-500 text-transparent bg-clip-text transition duration-300 hover:text-white">
                            Lihat Layanan Kami
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- About Us Section --}}
    <div class="py-30 px-4 relative overflow-hidden">
        <!-- Blue-500 background with fade effects -->
        <div class="absolute inset-0 bg-blue-100">
            <!-- TAMBAHAN: Bubble pudar putih bergerak horizontal -->
            <div class="floating-bubbles">
                <div class="bubble bubble-1"></div>
                <div class="bubble bubble-2"></div>
                <div class="bubble bubble-3"></div>
                <div class="bubble bubble-4"></div>
                <div class="bubble bubble-5"></div>
            </div>

            <!-- Animated wave effect at bottom -->
            <div class="absolute bottom-0 left-0 w-full overflow-hidden">
                <svg class="absolute bottom-0 w-full h-24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                    <path fill="#ffffff" fill-opacity="1"
                        d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,213.3C672,224,768,224,864,208C960,192,1056,160,1152,160C1248,160,1344,192,1392,208L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                    </path>
                </svg>
            </div>

            <!-- Animated floating circles background -->
            <div class="absolute inset-0">
                <div class="absolute top-1/4 left-1/4 w-20 h-20 rounded-full bg-blue-700 opacity-20 animate-pulse">
                </div>
                <div class="absolute top-3/4 left-1/3 w-16 h-16 rounded-full bg-blue-700 opacity-15 animate-pulse"
                    style="animation-delay: 1s;"></div>
                <div class="absolute top-1/3 right-1/4 w-24 h-24 rounded-full bg-blue-600 opacity-20 animate-pulse"
                    style="animation-delay: 2s;"></div>
                <div class="absolute bottom-1/4 right-1/3 w-12 h-12 rounded-full bg-blue-600 opacity-15 animate-pulse"
                    style="animation-delay: 0.5s;"></div>
            </div>

            <!-- Subtle pattern overlay -->
            <div class="absolute inset-0 opacity-5"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.2\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'1\'/%3E%3C/g%3E%3C/svg%3E');">
            </div>
        </div>

        <div class="container mx-auto relative z-10">
            <!-- Decorative elements above heading -->
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2">
                        <svg width="80" height="20" viewBox="0 0 80 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 10H80" stroke="white" stroke-width="2" stroke-dasharray="6 4" />
                            <circle cx="40" cy="10" r="6" fill="white" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Main content card with enhanced styling -->
            <div
                class="max-w-4xl mx-auto bg-white bg-opacity-90 rounded-xl shadow-xl p-8 border border-white transform transition-all duration-500 hover:scale-[1.01] hover:shadow-2xl">
                <!-- Decorative header element -->
                <div class="flex justify-center mb-6 relative">
                    <div class="absolute -top-6">
                        <svg width="40" height="12" viewBox="0 0 40 12" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 0L40 12H0L20 0Z" fill="#3B82F6" fill-opacity="0.5" />
                        </svg>
                    </div>
                </div>

                <!-- Company logo or icon -->
                <div class="flex justify-center mb-6">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold mb-2 text-gray-800 inline-block relative">
                            About Us
                        </h2>
                        <div class="w-24 h-1 bg-blue-300 mx-auto mt-2 rounded-full relative overflow-hidden">
                            <!-- Animated gradient effect -->
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-white via-blue-300 to-white animate-shimmer">
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-gray-800 leading-relaxed text-lg text-center mb-6 relative">
                    <span class="absolute -left-2 top-0 text-4xl text-blue-500 opacity-20">"</span>
                    Apcom Solution adalah mitra strategis anda dalam membangun dan mempertahankan
                    reputasi terbaik di era digital. Kami menggabungkan kreativitas, inovasi, dan pendekatan
                    berkelanjutan untuk mendukung kebutuhan komunikasi bisnis anda.
                    <span class="absolute -right-2 bottom-0 text-4xl text-blue-500 opacity-20">"</span>
                </p>

                <!-- Animated divider dots -->
                <div class="my-6 flex justify-center">
                    <div class="flex space-x-4">
                        <div class="w-2 h-2 rounded-full bg-blue-500 opacity-60 animate-pulse"></div>
                        <div class="w-2 h-2 rounded-full bg-blue-500 opacity-80 animate-pulse"
                            style="animation-delay: 0.3s;"></div>
                        <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse" style="animation-delay: 0.6s;">
                        </div>
                    </div>
                </div>

                <p class="text-gray-800 leading-relaxed text-lg text-center">
                    Dengan layanan unggulan seperti pendampingan humas kreatif, corporate public relations, pembuatan
                    konten F2B (fashion, food & beverage), riset dan analisis data, hingga publikasi ilmiah
                    dan pendidikan inovatif, kami siap membawa reputasi anda ke tingkat yang lebih tinggi.
                </p>

                <!-- Service icons -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-8">
                    <div
                        class="flex flex-col items-center p-3 bg-blue-50 rounded-lg border border-blue-200 transform transition hover:scale-105">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                            <i class="fas fa-paint-brush text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-blue-700 text-xs text-center font-medium">Digital Creative</span>
                    </div>
                    <div
                        class="flex flex-col items-center p-3 bg-blue-50 rounded-lg border border-blue-200 transform transition hover:scale-105">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                            <i class="fas fa-handshake text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-blue-700 text-xs text-center font-medium">Corporate Public Relation</span>
                    </div>
                    <div
                        class="flex flex-col items-center p-3 bg-blue-50 rounded-lg border border-blue-200 transform transition hover:scale-105">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                            <i class="fas fa-utensils text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-blue-700 text-xs text-center font-medium">Konten Kreator Fashion Food &
                            Beverage</span>
                    </div>
                    <div
                        class="flex flex-col items-center p-3 bg-blue-50 rounded-lg border border-blue-200 transform transition hover:scale-105">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                            <i class="fas fa-book text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-blue-700 text-xs text-center font-medium">Penerbitan Buku</span>
                    </div>
                    <div
                        class="flex flex-col items-center p-3 bg-blue-50 rounded-lg border border-blue-200 transform transition hover:scale-105">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                            <i class="fas fa-microscope text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-blue-700 text-xs text-center font-medium">Publikasi Ilmiah</span>
                    </div>
                    <div
                        class="flex flex-col items-center p-3 bg-blue-50 rounded-lg border border-blue-200 transform transition hover:scale-105">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                            <i class="fas fa-chart-bar text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-blue-700 text-xs text-center font-medium">Riset dan Data Analisis</span>
                    </div>
                </div>

                <!-- Call to action button -->
                <div class="mt-8 flex justify-center">
                    <button
                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-full shadow-lg transform transition hover:scale-105 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <a href="contact">Hubungi Kami</a>
                    </button>
                </div>
            </div>

            <!-- Decorative element below the card -->
            <div class="flex justify-center mt-12">
                <svg width="120" height="20" viewBox="0 0 120 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M60 20L120 0H0L60 20Z" fill="white" fill-opacity="0.2" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Our Process Section --}}
    <div class="py-16 px-6 relative overflow-hidden">
        <div class="absolute inset-0 bg-blue-50">
            <!-- Subtle grid pattern overlay -->
            <div class="absolute inset-0 opacity-5"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%236b7280\' fill-opacity=\'0.2\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'1\'/%3E%3C/g%3E%3C/svg%3E');">
            </div>
        </div>

        <div class="container mx-auto relative z-10">
            <div class="text-center mb-12" data-aos="fade-down" data-aos-duration="800">
                <h2 class="text-3xl font-bold mb-2 gradient-text inline-block">Our Process</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-pink-500 to-blue-500 mx-auto mt-2"></div>
                <p class="text-xl font-medium mt-4 text-gray-700">"Membangun Reputasi, Menciptakan Solusi"</p>
            </div>

            <div class="max-w-5xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Process 1 -->
                    <div class="relative" data-aos="fade-up-right" data-aos-duration="800" data-aos-delay="100">
                        <!-- Number badge -->
                        <div
                            class="absolute -top-5 -left-5 w-14 h-14 rounded-full bg-gradient-to-br from-pink-500 to-pink-400 flex items-center justify-center text-white font-bold text-xl shadow-lg z-20">
                            01</div>

                        <!-- Card -->
                        <div
                            class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-xl p-6 pt-10 border border-gray-100 h-full relative z-10 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                            <h3 class="text-xl font-bold mb-4 text-gray-800">Creative</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Menghadirkan ide segar yang inovatif dan relevan. Kami mengubah tantangan menjadi
                                peluang melalui solusi komunikasi yang berdampak, unik, dan berkelanjutan.
                            </p>
                            <div class="absolute bottom-0 right-0 w-16 h-16 opacity-10">
                                <i class="fas fa-paint-brush text-6xl text-pink-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Process 2 -->
                    <div class="relative mt-8 md:mt-16" data-aos="fade-up-right" data-aos-duration="800"
                        data-aos-delay="300">
                        <!-- Number badge -->
                        <div
                            class="absolute -top-5 -left-5 w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-400 flex items-center justify-center text-white font-bold text-xl shadow-lg z-20">
                            02</div>

                        <!-- Card -->
                        <div
                            class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-xl p-6 pt-10 border border-gray-100 h-full relative z-10 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                            <h3 class="text-xl font-bold mb-4 text-gray-800">Innovative</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Menciptakan terobosan baru dalam strategi komunikasi. Kami menghadirkan solusi yang
                                adaptif, relevan, dan efektif untuk menjawab dinamika perubahan di setiap lini kebutuhan
                                Anda.
                            </p>
                            <div class="absolute bottom-0 right-0 w-16 h-16 opacity-10">
                                <i class="fas fa-lightbulb text-6xl text-indigo-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Process 3 -->
                    <div class="relative mt-8 md:mt-32" data-aos="fade-up-right" data-aos-duration="800"
                        data-aos-delay="500">
                        <!-- Number badge -->
                        <div
                            class="absolute -top-5 -left-5 w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-400 flex items-center justify-center text-white font-bold text-xl shadow-lg z-20">
                            03</div>

                        <!-- Card -->
                        <div
                            class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-xl p-6 pt-10 border border-gray-100 h-full relative z-10 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                            <h3 class="text-xl font-bold mb-4 text-gray-800">Sustainable</h3>
                            <p class="text-gray-700 leading-relaxed">
                                Membangun strategi komunikasi yang berdampak jangka panjang. Kami fokus pada solusi yang
                                tidak hanya efektif saat ini, tetapi juga menjaga relevansi dan nilai bagi masa depan.
                            </p>
                            <div class="absolute bottom-0 right-0 w-16 h-16 opacity-10">
                                <i class="fas fa-seedling text-6xl text-blue-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Our Services Section --}}
    <div class="py-16 px-6 relative overflow-hidden">
        <div class="absolute inset-0 bg-white">
            <!-- Subtle grid pattern overlay -->
            <div class="absolute inset-0 opacity-5"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%236b7280\' fill-opacity=\'0.2\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'1\'/%3E%3C/g%3E%3C/svg%3E');">
            </div>
        </div>

        <div class="container mx-auto relative z-10">
            <div class="text-center mb-12" data-aos="fade-down" data-aos-duration="800">
                <h2 class="text-3xl font-bold mb-2 gradient-text inline-block" id="service">Our Services</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-pink-500 to-blue-500 mx-auto mt-2"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="bg-white rounded-lg shadow-md p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg"
                    data-aos="flip-left" data-aos-duration="800" data-aos-delay="100">
                    <div class="text-center mb-4">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-pink-100 text-pink-500 mb-4">
                            <i class="fas fa-paint-brush text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Digital Creative</h3>
                    </div>
                    <p class="text-gray-600 text-center">Menghadirkan solusi kreatif digital yang inovatif dengan
                        penguasaan terkini dalam desain visual, konten interaktif, dan strategi digital yang mengikuti
                        perkembangan tren.</p>
                </div>

                <!-- Service 2 -->
                <div class="bg-white rounded-lg shadow-md p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg"
                    data-aos="flip-left" data-aos-duration="800" data-aos-delay="200">
                    <div class="text-center mb-4">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-500 mb-4">
                            <i class="fas fa-handshake text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Corporate Public Relation</h3>
                    </div>
                    <p class="text-gray-600 text-center">Membangun dan menjaga citra perusahaan melalui komunikasi
                        strategis, pengelolaan krisis, dan pengembangan hubungan yang berkelanjutan dengan stakeholder.
                    </p>
                </div>

                <!-- Service 3 -->
                <div class="bg-white rounded-lg shadow-md p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg"
                    data-aos="flip-left" data-aos-duration="800" data-aos-delay="300">
                    <div class="text-center mb-4">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-500 mb-4">
                            <i class="fas fa-utensils text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Konten Kreator Fashion Food & Beverage</h3>
                    </div>
                    <p class="text-gray-600 text-center">Menciptakan konten visual dan naratif yang menginspirasi di
                        bidang fashion dan F&B, dengan pendekatan yang memadukan estetika, tren, dan storytelling yang
                        menarik.</p>
                </div>

                <!-- Service 4 -->
                <div class="bg-white rounded-lg shadow-md p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg"
                    data-aos="flip-left" data-aos-duration="800" data-aos-delay="100">
                    <div class="text-center mb-4">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-500 mb-4">
                            <i class="fas fa-book text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Penerbitan Buku</h3>
                    </div>
                    <p class="text-gray-600 text-center">Menyediakan layanan penerbitan profesional mulai dari
                        penyuntingan, desain, hingga distribusi, dengan fokus pada kualitas dan nilai konten yang
                        bertahan lama.</p>
                </div>

                <!-- Service 5 -->
                <div class="bg-white rounded-lg shadow-md p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg"
                    data-aos="flip-left" data-aos-duration="800" data-aos-delay="200">
                    <div class="text-center mb-4">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-100 text-yellow-500 mb-4">
                            <i class="fas fa-microscope text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Publikasi Ilmiah</h3>
                    </div>
                    <p class="text-gray-600 text-center">Mendukung penyusunan dan penerbitan karya ilmiah berkualitas
                        tinggi dengan standar akademik internasional, termasuk riset, penulisan, dan proses review.</p>
                </div>

                <!-- Service 6 -->
                <div class="bg-white rounded-lg shadow-md p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg"
                    data-aos="flip-left" data-aos-duration="800" data-aos-delay="300">
                    <div class="text-center mb-4">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-500 mb-4">
                            <i class="fas fa-chart-bar text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Riset dan Data Analisis</h3>
                    </div>
                    <p class="text-gray-600 text-center">Mengolah data menjadi wawasan berharga melalui metodologi
                        riset yang komprehensif dan teknik analisis data mutakhir untuk pengambilan keputusan yang
                        tepat.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Documentation --}}
    <div class="bg-blue-50 py-12">
        <x-gallery-carousel></x-gallery-carousel>
    </div>

    {{-- Get In Touch Section --}}
    <div class="bg-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-center mb-8">Get In Touch</h1>

            <div class="max-w-6xl mx-auto">
                <p class="text-gray-700 text-center mb-10">
                    Siap untuk membawa bisnis Anda ke level berikutnya? Kami siap membantu. <br>
                    Baik Anda memiliki pertanyaan tentang layanan kami? <br>
                    ingin mendiskusikan proyek potensial, atau sekadar ingin berbincang <br>
                    tentang strategi digital Anda, kami sangat senang mendengar dari Anda.
                </p>

                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <!-- Customer Service Image Section -->
                    <div class="w-full md:w-2/5 flex flex-col items-center">
                        <img src="{{ asset('images/customer.png') }}" alt="Customer Service"
                            class="rounded-lg shadow-lg mb-8 w-full max-w-sm object-cover">

                        <!-- Contact Information with Icons -->
                        <div class="w-full max-w-sm">

                            <div class="flex items-center mb-4">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-pink-100 mr-4">
                                    <i class="fas fa-phone-alt text-pink-500"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium">Phone</h3>
                                    <p class="text-gray-600">+62 812-5881-289</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-pink-100 mr-4">
                                    <i class="fas fa-envelope text-pink-500"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium">Email</h3>
                                    <p class="text-gray-600">adminapcoms@apcoms.co.id</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Section -->
                    <div class="w-full md:w-3/5 bg-gray-50 p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-semibold mb-6 flex items-center">
                            <i class="fas fa-paper-plane text-pink-500 mr-3"></i>
                            Send Us a Message
                        </h2>

                        <form>
                            <div class="flex flex-col md:flex-row gap-4 mb-4">
                                <div class="w-full md:w-1/2">
                                    <label for="name" class="block text-gray-700 font-medium mb-2">
                                        <i class="fas fa-user text-pink-500 mr-2"></i>
                                        Name
                                    </label>
                                    <input type="text" id="name" name="name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>

                                <div class="w-full md:w-1/2">
                                    <label for="email" class="block text-gray-700 font-medium mb-2">
                                        <i class="fas fa-envelope text-pink-500 mr-2"></i>
                                        Email Address
                                    </label>
                                    <input type="email" id="email" name="email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="message" class="block text-gray-700 font-medium mb-2">
                                    <i class="fas fa-comment-alt text-pink-500 mr-2"></i>
                                    Your Message
                                </label>
                                <textarea id="message" name="message" rows="6"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"></textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit"
                                    class="bg-pink-500 text-white py-3 px-8 rounded-md hover:bg-pink-600 transition flex items-center mx-auto">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <x-footer></x-footer>
</x-layout>

<style>
    .gradient-text {
        background-image: linear-gradient(to right, #ec4899, #3b82f6);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        display: inline-block;
    }

    /* Improved gradient border button */
    .border-gradient {
        position: relative;
        border: none;
        background-color: white;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .border-gradient::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: inherit;
        padding: 2px;
        background: linear-gradient(to right, #ec4899, #3b82f6);
        -webkit-mask:
            linear-gradient(#fff 0 0) content-box,
            linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        z-index: -1;
    }

    /* Make sure the text remains visible with proper coloring */
    .border-gradient:hover {
        color: white;
        background-image: linear-gradient(to right, #ec4899, #3b82f6);
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    @keyframes expand {
        0% {
            width: 0;
        }

        100% {
            width: 100%;
        }
    }

    .animate-shimmer {
        animation: shimmer 2s infinite;
    }

    .animate-expand {
        animation: expand 2s ease-out forwards;
    }

    .float-animation {
        animation: float 8s ease-in-out infinite;
    }

    /* Bubble Animation untuk About Us Section */
    .floating-bubbles {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 1;
    }

    .bubble {
        position: absolute;
        background-color: rgba(141, 221, 255, 0.079);
        border-radius: 50%;
        pointer-events: none;
    }

    .bubble-1 {
        width: 150px;
        height: 150px;
        top: 20%;
        left: -100px;
        animation: bubble-move 25s linear infinite;
    }

    .bubble-2 {
        width: 100px;
        height: 100px;
        top: 50%;
        left: -80px;
        animation: bubble-move 20s linear infinite;
        animation-delay: 2s;
    }

    .bubble-3 {
        width: 80px;
        height: 80px;
        top: 35%;
        left: -60px;
        animation: bubble-move 18s linear infinite;
        animation-delay: 5s;
    }

    .bubble-4 {
        width: 120px;
        height: 120px;
        top: 70%;
        left: -90px;
        animation: bubble-move 22s linear infinite;
        animation-delay: 8s;
    }

    .bubble-5 {
        width: 70px;
        height: 70px;
        top: 10%;
        left: -50px;
        animation: bubble-move 15s linear infinite;
        animation-delay: 12s;
    }

    @keyframes bubble-move {
        0% {
            transform: translateX(0) rotate(0);
            opacity: 0;
        }

        10% {
            opacity: 0.1;
        }

        80% {
            opacity: 0.2;
        }

        100% {
            transform: translateX(calc(100vw + 150px)) rotate(360deg);
            opacity: 0;
        }
    }
</style>

<script>
    // Add smooth scrolling for better transition between sections
    document.addEventListener('DOMContentLoaded', function() {
        // Original parallax effect
        function parallaxEffect() {
            let scrollY = window.scrollY;
            document.querySelectorAll(".parallax").forEach((el) => {
                let speed = el.getAttribute("data-speed");
                let xMove = (scrollY / speed) * 1.2; // Horizontal effect
                let yMove = (scrollY / speed) * 2.5; // Stronger vertical effect
                el.style.transform = `translate(${xMove}px, ${yMove}px)`;
            });
            requestAnimationFrame(parallaxEffect);
        }

        parallaxEffect();

        // Inisialisasi AOS (Animate On Scroll)
        // Pastikan Anda menambahkan library AOS di header HTML
        AOS.init({
            // Global settings
            duration: 400, // Durasi default untuk animasi
            once: false, // Apakah animasi hanya berjalan sekali atau setiap kali di-scroll
            mirror: false, // Apakah elemen harus dianimasikan kembali saat di-scroll ke atas
            offset: 120, // Offset (dalam px) dari posisi elemen asli untuk memicu animasi
            delay: 0, // Nilai default untuk delay animasi
            easing: 'ease-in-out-sine', // Default easing untuk animasi AOS
        });
    });

</script>
