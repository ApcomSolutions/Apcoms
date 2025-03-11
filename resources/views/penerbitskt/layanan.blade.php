<x-penerbitskt.layout1>
    <x-penerbitskt.navbar1></x-penerbitskt.navbar1>

    <!-- Hero Section with Animated Background -->
    <section
        class="relative bg-gradient-to-br from-blue-100 via-white to-pink-100 text-black py-40 px-6 mt-10 overflow-hidden">
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
        <div class="max-w-6xl mx-auto relative z-10">
            <div class="md:flex items-center">
                <div class="md:w-1/2 fade-in-up" data-delay="200">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4 text-blue-900">Layanan Kami</h1>
                    <p class="text-lg md:text-xl mb-6 text-gray-700">
                        Penerbit SKT menawarkan layanan penerbitan dan pemasaran buku yang komprehensif. Kami membantu
                        Anda mewujudkan karya terbaik dan memastikan karya tersebut mencapai target pembaca yang tepat
                        dengan strategi pemasaran yang efektif.
                    </p>
                </div>
                <div class="md:w-1/2 fade-in-up" data-delay="400">
                    <img src="{{ asset('images/layanan.PNG') }}" alt="Layanan Penerbitan"
                        class="rounded-lg mx-auto">
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Penerbitan Buku Section -->
    <section class="py-16 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <!-- Section Title -->
            <div class="text-center mb-16 fade-in-up">
                <h2 class="text-4xl font-bold text-blue-900 mb-2">Layanan Kami</h2>
                <p class="text-lg text-gray-600">Solusi lengkap untuk penerbitan dan pemasaran buku Anda</p>
            </div>

            <!-- Penerbitan Buku -->
            <div class="mb-20">
                <div class="flex flex-col md:flex-row items-center gap-10">
                    <div class="md:w-1/2 fade-in-up" data-delay="200">
                        <h3 class="text-3xl font-bold text-blue-800 mb-6">1. Penerbitan Buku</h3>
                        <p class="text-gray-600 mb-6">
                            Kami menawarkan layanan penerbitan buku yang komprehensif, mulai dari proses awal hingga
                            buku siap didistribusikan. Tim profesional kami akan membantu Anda menghasilkan karya
                            berkualitas tinggi yang sesuai dengan visi dan tujuan Anda.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="flex items-start">
                                <i class="fas fa-pen-fancy text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Pengeditan Naskah</h4>
                                    <p class="text-gray-600 text-sm">Penyuntingan substantif, copy-editing, dan
                                        proofreading oleh editor profesional.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <i class="fas fa-paint-brush text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Desain dan Layout</h4>
                                    <p class="text-gray-600 text-sm">Perancangan cover dan isi buku yang menarik dan
                                        sesuai dengan konten.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <i class="fas fa-print text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Pencetakan</h4>
                                    <p class="text-gray-600 text-sm">Produksi buku berkualitas tinggi dengan berbagai
                                        pilihan format dan kertas.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <i class="fas fa-id-card text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">ISBN dan Legal</h4>
                                    <p class="text-gray-600 text-sm">Pengurusan ISBN, hak cipta, dan persyaratan legal
                                        lainnya.</p>
                                </div>
                            </div>
                        </div>

                        <a href="https://wa.me/628125881289"
                            class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800 transition mt-4">
                            Konsultasi Penerbitan
                        </a>
                    </div>

                    <div class="md:w-1/2 fade-in-up" data-delay="400">
                        <img src="{{ asset('images/about.PNG') }}" alt="Penerbitan Buku"
                            class="rounded-lg mx-auto">
                    </div>
                </div>
            </div>

            <!-- Pemasaran dan Promosi Buku -->
            <div>
                <div class="flex flex-col md:flex-row-reverse items-center gap-10">
                    <div class="md:w-1/2 fade-in-up" data-delay="200">
                        <h3 class="text-3xl font-bold text-blue-800 mb-6">2. Pemasaran dan Promosi Buku</h3>
                        <p class="text-gray-600 mb-6">
                            Menerbitkan buku hanya setengah dari perjalanan. Kami menawarkan strategi pemasaran dan
                            promosi yang efektif untuk memastikan buku Anda mencapai target pembaca yang tepat dan
                            mendapatkan visibilitas maksimal di pasar.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="flex items-start">
                                <i class="fas fa-globe text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Pemasaran Digital</h4>
                                    <p class="text-gray-600 text-sm">Strategi SEO, media sosial, dan email marketing
                                        untuk mempromosikan buku Anda.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <i class="fas fa-book-open text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Peluncuran Buku</h4>
                                    <p class="text-gray-600 text-sm">Perencanaan dan pelaksanaan acara peluncuran buku
                                        yang berkesan.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <i class="fas fa-store text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Distribusi</h4>
                                    <p class="text-gray-600 text-sm">Pendistribusian buku ke toko buku fisik dan
                                        platform online terkemuka.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <i class="fas fa-bullhorn text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Hubungan Media</h4>
                                    <p class="text-gray-600 text-sm">Ulasan buku, wawancara, dan liputan media untuk
                                        meningkatkan visibilitas.</p>
                                </div>
                            </div>
                        </div>

                        <a href="https://wa.me/628125881289"
                            class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800 transition mt-4">
                            Konsultasi Pemasaran
                        </a>
                    </div>

                    <div class="md:w-1/2 fade-in-up" data-delay="400">
                        <img src="{{ asset('images/customer.PNG') }}" alt="Pemasaran dan Promosi Buku"
                            class="rounded-lg mx-auto">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section class="py-16 px-6 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-3xl font-bold text-blue-900 mb-2">Apa Kata Mereka</h2>
                <p class="text-lg text-gray-600">Pengalaman penulis yang telah menggunakan layanan kami</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-6 rounded-lg shadow-md fade-in-up" data-delay="200">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 italic mb-4">"Proses penerbitan bersama Penerbit SKT sangat profesional. Tim
                        editor membantu menyempurnakan naskah saya dan desain cover-nya luar biasa. Saya sangat puas
                        dengan hasilnya."</p>
                    <div class="flex items-center">
                        <div class="mr-4 w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Dr. Budi Santoso</h4>
                            <p class="text-sm text-gray-500">Penulis "Perencanaan Stratejik Pendidikan"</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white p-6 rounded-lg shadow-md fade-in-up" data-delay="400">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 italic mb-4">"Strategi pemasaran yang diimplementasikan oleh Penerbit SKT
                        membuat buku saya mencapai target pembaca yang tepat. Penjualan melampaui ekspektasi saya.
                        Sangat merekomendasikan!"</p>
                    <div class="flex items-center">
                        <div class="mr-4 w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Indah Permata</h4>
                            <p class="text-sm text-gray-500">Penulis "Penggunaan Anggaran Pendidikan"</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white p-6 rounded-lg shadow-md fade-in-up" data-delay="600">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 italic mb-4">"Peluncuran buku saya diorganisir dengan sangat baik oleh tim
                        Penerbit SKT. Mereka menangani setiap detail dan membuat acara tersebut menjadi pengalaman yang
                        tak terlupakan."</p>
                    <div class="flex items-center">
                        <div class="mr-4 w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold">Prof. Ahmad Wijaya</h4>
                            <p class="text-sm text-gray-500">Penulis "Perencanaan Stratejik Operasional"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-20 px-6 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero.png') }}" alt="Keyboard Background"
                class="w-full h-full object-cover brightness-50" />
        </div>

        <!-- Blue Overlay -->
        <div class="absolute inset-0 bg-blue-100 opacity-50 z-10"></div>

        <!-- Content -->
        <div class="relative z-20 max-w-4xl mx-auto text-center text-gray-800">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6 fade-in-up">Siap Mewujudkan Karya Terbaik Anda?</h2>
            <p class="text-lg mb-8 fade-in-up" data-delay="200">Konsultasikan kebutuhan penerbitan dan pemasaran buku
                Anda dengan tim profesional kami.</p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center fade-in-up" data-delay="300">
                <a href="https://wa.me/628125881289"
                    class="inline-block bg-blue-700 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-blue-900 transition">
                    Hubungi Kami
                </a>
                <a href="#"
                    class="inline-block border-2 border-gray-700 text-gray-800 px-8 py-3 rounded-full text-lg font-semibold hover:bg-gray-700 hover:text-white transition">
                    Lihat Portfolio
                </a>
            </div>
        </div>
    </section>

    <x-penerbitskt.footer1></x-penerbitskt.footer1>
</x-penerbitskt.layout1>
