<!-- resources/views/components/footer.blade.php -->
<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Brand/Logo Section -->
            <div>
                <div>
                    <img src="{{ asset('images/footer.png') }}" alt="APCOM Solutions" class="h-16 w-auto">
                </div>
                <p class="mt-4 text-gray-300">
                    PT. Solusi Komunikasi Terapan merupakan mitra yang komprehensif bagi individu/organisasi/lembaga
                    yang mencari solusi dalam beragam aspek komunikasi, riset, dan pendidikan.
                </p>
            </div>

            <!-- Contact Information -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <div class="space-y-2">
                    <p class="flex items-center">
                        <i class="fas fa-phone text-pink-500 w-5 mr-2"></i>
                        0812-5881-289
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-envelope text-pink-500 w-5 mr-2"></i>
                        adminapcoms@apcoms.co.id
                    </p>
                    <p class="flex items-start">
                        <i class="fas fa-map-marker-alt text-pink-500 w-5 mr-2 mt-1 text-sm"></i>
                        <span>Sanggar Kencana Utama No. 1C Sanggar Hurip Estate, Jatisari, Buahbatu, Soekarno Hatta,
                            Kota Bandung, Jawa Barat</span>
                    </p>
                </div>
                <div class="mt-4 flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-blue-400">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-pink-500">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-gray-400">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Google Maps Container -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Our Location</h3>
                <div class="bg-gray-800 w-full h-64 rounded-lg flex items-center justify-center">
                    <!-- This div will be replaced with Google Maps -->
                    <p class="text-gray-400">Google Maps will be displayed here</p>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-700">
            <p class="text-center text-gray-400">
                &copy; {{ date('Y') }} APCOM Solutions. All rights reserved.
            </p>
        </div>
    </div>
</footer>
