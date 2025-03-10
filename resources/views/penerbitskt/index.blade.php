<x-penerbitskt.layout1>
    <x-penerbitskt.navbar1></x-penerbitskt.navbar1>

    <!-- Hero Section with Animated Background and Scroll Animations -->
    <section
        class="relative bg-gradient-to-br from-blue-100 via-white to-pink-100 text-black text-center py-60 px-6 mt-10  overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 z-0 opacity-40">
            <!-- Moving Circles -->
            <div class="absolute top-1/4 left-1/5 w-32 h-32 rounded-full bg-blue-200 animate-pulse"></div>
            <div class="absolute top-3/4 left-2/3 w-48 h-48 rounded-full bg-pink-200 animate-float"></div>
            <div class="absolute top-1/2 right-1/4 w-24 h-24 rounded-full bg-purple-200 animate-bounce"></div>

            <!-- Abstract Shapes -->
            <div class="absolute bottom-1/4 left-1/3 w-40 h-20 bg-yellow-200 rotate-45 animate-drift"></div>
            <div class="absolute top-1/3 right-1/3 w-20 h-20 bg-green-200 rotate-12 animate-spin-slow"></div>
        </div>

        <!-- Content with Scroll Animation -->
        <div class="max-w-3xl mx-auto relative z-10 fade-in-up">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4 fade-in-up" data-delay="200">A Commitment to Innovation and
                Sustainability</h2>
            <p class="text-lg sm:text-xl mb-6 fade-in-up" data-delay="400">
                Kami membentuk kemitraan yang solid antara penulis dan pembaca. Kami berkomitmen untuk berbagi pemahaman
                yang mendalam akan pentingnya mengaktualisasikan visi dan ambisi setiap penulis. Kami berkomitmen untuk
                menciptakan karya-karya yang berdampak dan berkelanjutan.
            </p>
            <a href="https://wa.me/628125881289"
                class="inline-block border-2 border-black text-black px-6 py-3 rounded-lg text-lg hover:bg-black hover:text-white transition fade-in-up"
                data-delay="600">
                Daftar Sekarang
            </a>
        </div>
    </section>

    <!-- Benefits Section (based on the image) -->
    <section class="py-16 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <!-- Section Title -->
            <div class="text-center mb-16 fade-in-up">
                <h2 class="text-4xl font-bold text-blue-900 mb-2">Benefit dan Keunggulannya</h2>
                <p class="text-lg text-gray-600">Penerbit Solusi Komunikasi Terapan</p>
            </div>

            <!-- Benefits Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Benefit 1: Kualitas -->
                <div class="border p-8 rounded-lg text-center slide-in-left" data-delay="200">
                    <div class="flex justify-center mb-4">
                        <i class="fas fa-award text-4xl text-blue-800"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">Kualitas</h3>
                    <p class="text-gray-600">
                        Kami menawarkan jaminan atas publikasi berkualitas tinggi dengan memastikan publikasi Anda
                        diedit dengan baik, didesain menarik, dan diproduksi dengan kualitas memukau sehingga
                        mencerminkan citra merek Anda dengan baik.
                    </p>
                </div>

                <!-- Benefit 2: Target -->
                <div class="border p-8 rounded-lg text-center slide-in-left" data-delay="400">
                    <div class="flex justify-center mb-4">
                        <i class="fas fa-bullseye text-4xl text-blue-800"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">Target</h3>
                    <p class="text-gray-600">
                        Anda dapat mencapai target pasar Anda dengan lebih efektif. Kami akan membantu Anda merumuskan
                        strategi distribusi dan pemasaran yang sesuai, sehingga publikasi Anda sampai tepat ke tangan
                        yang tepat.
                    </p>
                </div>

                <!-- Benefit 3: Efisiensi -->
                <div class="border p-8 rounded-lg text-center slide-in-left" data-delay="600">
                    <div class="flex justify-center mb-4">
                        <i class="fas fa-chart-line text-4xl text-blue-800"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">Efisiensi</h3>
                    <p class="text-gray-600">
                        Kami akan membantu Anda menghemat waktu dan tenaga dalam proses produksi. Tim kami yang
                        berpengalaman akan memastikan bahwa publikasi Anda diproses dengan cepat dan efisien, sehingga
                        Anda dapat fokus pada hal-hal lain dalam bisnis Anda.
                    </p>
                </div>

                <!-- Benefit 4: Berkelanjutan -->
                <div class="border p-8 rounded-lg text-center slide-in-left" data-delay="800">
                    <div class="flex justify-center mb-4">
                        <i class="fas fa-expand-arrows-alt text-4xl text-blue-800"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">Berkelanjutan</h3>
                    <p class="text-gray-600">
                        Sebagai bagian dari komitmen kami terhadap keberlanjutan, kami menawarkan layanan penerbitan
                        yang didasarkan pada konsistensi dan integritas dengan menjaga standar tertinggi dalam setiap
                        langkah proses penerbitan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Koleksi Terbit Section -->
    <section class="py-16 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <!-- Section Title -->
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-4xl font-bold text-blue-900 mb-6">Koleksi Terbit</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Kami sudah menerbitkan berbagai buku yang kini dapat Anda beli. Selain itu, kami siap
                    menerbitkan buku yang Anda inginkan dengan kualitas terbaik.
                </p>
            </div>

            <!-- Books Display - Always 3 columns, smaller cards -->
            <div class="grid grid-cols-3 gap-2 sm:gap-4 md:gap-6 lg:gap-8 mb-12">
                <!-- Book 1 -->
                <div class="text-center slide-in-left" data-delay="200">
                    <div class="mb-2 md:mb-4 overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                        <img src="{{ asset('images/potret.jpg') }}"
                            alt="Penggunaan Anggaran Pendidikan di Sekolah Dasar"
                            class="w-full h-auto object-cover transition hover:scale-105 duration-300" />
                    </div>
                    <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-semibold text-gray-700">
                        Penggunaan Anggaran Pendidikan</h3>
                </div>

                <!-- Book 2 -->
                <div class="text-center slide-in-left" data-delay="400">
                    <div class="mb-2 md:mb-4 overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                        <img src="{{ asset('images/potret.jpg') }}"
                            alt="Perencanaan Stratejik Operasional Lembaga Pendidikan"
                            class="w-full h-auto object-cover transition hover:scale-105 duration-300" />
                    </div>
                    <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-semibold text-gray-700">
                        Perencanaan Stratejik Operasional</h3>
                </div>

                <!-- Book 3 -->
                <div class="text-center slide-in-left" data-delay="600">
                    <div class="mb-2 md:mb-4 overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                        <img src="{{ asset('images/potret.jpg') }}" alt="Perencanaan Stratejik Pendidikan Inklusif"
                            class="w-full h-auto object-cover transition hover:scale-105 duration-300" />
                    </div>
                    <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-semibold text-gray-700">
                        Perencanaan Stratejik Pendidikan</h3>
                </div>
            </div>

            <!-- Quote Section -->
            <div class="text-center py-12 fade-in-up" data-delay="800">
                <p class="text-xl sm:text-2xl font-serif italic text-gray-800 max-w-4xl mx-auto">
                    "Setiap karya mewakili sesuatu mengenai penciptanya, baik itu emosi yang dirasakannya,
                    pengalaman hidup maupun impiannya."
                </p>
            </div>

            <!-- CTA Button -->
            <div class="text-center mt-8 fade-in-up" data-delay="800">
                <a href="#"
                    class="inline-block bg-blue-600 text-white px-6 py-2 sm:px-8 sm:py-3 rounded-lg text-base sm:text-lg font-semibold hover:bg-blue-900 transition">
                    Lihat Selengkapnya →
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section with Keyboard Background -->
    <section class="relative py-60 px-6 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero.png') }}" alt="Keyboard Background"
                class="w-full h-full object-cover brightness-50 " />
        </div>

        <!-- Blue Overlay -->
        <div class="absolute inset-0 bg-blue-100 opacity-50 z-10"></div>

        <!-- Content -->
        <div class="relative z-20 max-w-4xl mx-auto text-center text-gray-800">
            <h2 class="text-4xl sm:text-5xl font-bold mb-6 fade-in-up">Sudahkah Anda siap<br>untuk menerbitkan
                karya<br>terbaik Anda?</h2>

            <div class="mt-8 fade-in-up" data-delay="300">
                <a href="https://wa.me/628125881289"
                    class="inline-block bg-gray-700 text-blue-50 px-8 py-3 rounded-full text-lg font-semibold hover:bg-gray-900 transition">
                    Terbitkan Sekarang
                </a>
            </div>
        </div>
    </section>

    <x-penerbitskt.footer1></x-penerbitskt.footer1>
</x-penerbitskt.layout1>

<style>
    /* Custom Animation Classes for Background */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    @keyframes drift {
        0% {
            transform: translateX(0) rotate(45deg);
        }

        50% {
            transform: translateX(20px) rotate(60deg);
        }

        100% {
            transform: translateX(0) rotate(45deg);
        }
    }

    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-float {
        animation: float 8s ease-in-out infinite;
    }

    .animate-drift {
        animation: drift 12s ease-in-out infinite;
    }

    .animate-spin-slow {
        animation: spin-slow 20s linear infinite;
    }

    /* Scroll Animation Classes */
    .fade-in-up {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .fade-in-up.active {
        opacity: 1;
        transform: translateY(0);
    }

    .slide-in-left {
        opacity: 0;
        transform: translateX(-30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .slide-in-left.active {
        opacity: 1;
        transform: translateX(0);
    }
</style>

<script>
    // Scroll animation functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initial check on page load
        checkElements();

        // Check elements on scroll
        window.addEventListener('scroll', checkElements);

        function checkElements() {
            // Get all elements with animation classes
            const elements = document.querySelectorAll('.fade-in-up, .slide-in-left');

            elements.forEach(function(element) {
                // Check if element is in viewport
                const position = element.getBoundingClientRect();
                const windowHeight = window.innerHeight;

                // If element is in viewport
                if (position.top < windowHeight * 0.85) {
                    // Add delay if specified
                    const delay = element.getAttribute('data-delay') || 0;

                    setTimeout(function() {
                        element.classList.add('active');
                    }, delay);
                }
            });
        }
    });
</script>
