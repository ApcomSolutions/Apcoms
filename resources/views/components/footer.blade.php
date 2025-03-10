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
                        +62 812-5881-289
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-envelope text-pink-500 w-5 mr-2"></i>
                        adminapcoms@apcoms.co.id
                    </p>
                    <p class="flex items-start">
                        <i class="fas fa-map-marker-alt text-pink-500 w-5 mr-2"></i>
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
                    <div id="map" class="w-full h-full rounded-lg">
                        <!-- Fallback content jika peta gagal dimuat -->
                        <div id="map-fallback" class="hidden w-full h-full flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-map-marked-alt text-4xl mb-3"></i>
                            <p class="text-center">Sanggar Kencana Utama No. 1C Sanggar Hurip Estate, Jatisari, Buahbatu, Soekarno Hatta, Kota Bandung, Jawa Barat</p>
                        </div>
                    </div>
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

@push('scripts')
    <!-- Google Maps API -->
    <script>
        // Variabel global untuk melacak upaya memuat peta
        var mapLoadAttempted = false;
        var mapLoadFailed = false;

        document.addEventListener('DOMContentLoaded', function() {
            // Periksa apakah elemen map ada
            if (document.getElementById("map")) {
                initializeMap();
            }
        });

        function initializeMap() {
            // Hindari memuat ulang peta jika sudah gagal sebelumnya
            if (mapLoadFailed) {
                showFallbackContent();
                return;
            }

            // Hindari memuat ulang jika sudah dicoba sebelumnya
            if (mapLoadAttempted) {
                return;
            }

            mapLoadAttempted = true;

            // Set timeout untuk mendeteksi kegagalan memuat peta
            var mapLoadTimeout = setTimeout(function() {
                // Jika peta tidak dimuat dalam 5 detik, tampilkan fallback
                if (!window.google || !window.google.maps) {
                    console.log("Google Maps failed to load within timeout period");
                    mapLoadFailed = true;
                    showFallbackContent();
                }
            }, 5000);

            // Muat script Google Maps API
            var script = document.createElement('script');
            script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAwu4CGUgxRjUN4pahOIpTsKmKw35gWgN8&callback=initMap";
            script.async = true;
            script.defer = true;

            // Handler untuk kesalahan memuat script
            script.onerror = function() {
                console.log("Failed to load Google Maps API script");
                clearTimeout(mapLoadTimeout);
                mapLoadFailed = true;
                showFallbackContent();
            };

            document.head.appendChild(script);

            // Definisikan fungsi callback global
            window.initMap = function() {
                clearTimeout(mapLoadTimeout);

                try {
                    var location = { lat: -6.938139, lng: 107.666861 };

                    // Periksa apakah elemen map masih ada di DOM
                    var mapElement = document.getElementById("map");
                    if (!mapElement) {
                        console.log("Map element no longer exists");
                        return;
                    }

                    var map = new google.maps.Map(mapElement, {
                        zoom: 15,
                        center: location
                    });

                    var marker = new google.maps.Marker({
                        position: location,
                        map: map
                    });

                    // Sembunyikan fallback content jika peta berhasil dimuat
                    if (document.getElementById("map-fallback")) {
                        document.getElementById("map-fallback").classList.add("hidden");
                    }

                    console.log("Google Maps successfully initialized");
                } catch (error) {
                    console.error("Error initializing Google Maps:", error);
                    mapLoadFailed = true;
                    showFallbackContent();
                }
            };
        }

        function showFallbackContent() {
            // Tampilkan konten fallback jika memuat peta gagal
            var fallbackElement = document.getElementById("map-fallback");
            if (fallbackElement) {
                fallbackElement.classList.remove("hidden");
            }
        }
    </script>
@endpush
