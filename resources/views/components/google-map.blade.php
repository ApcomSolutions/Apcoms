{{-- File: resources/views/components/google-map.blade.php --}}

<div class="google-map-wrapper w-full rounded-lg overflow-hidden" style="height: {{ $height }}">
    <div id="{{ $id }}" class="google-map-container w-full h-full">
        {{-- Fallback content --}}
        <div class="map-fallback hidden w-full h-full flex flex-col items-center justify-center bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 p-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="text-center text-sm">{{ $address }}</p>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .google-map-container {
                transition: opacity 0.3s ease;
            }
            .google-map-loading {
                opacity: 0.7;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function() {
                // Cek apakah skrip sudah dimuat
                if (window.googleMapsInitialized) return;

                // Setup objek global untuk menangani Maps
                window.GoogleMapsManager = {
                    apiLoaded: false,
                    apiLoading: false,
                    apiKey: '{{ config('google-maps.api_key', 'AIzaSyAwu4CGUgxRjUN4pahOIpTsKmKw35gWgN8') }}',
                    mapInstances: {},
                    mapConfigs: {},

                    // Memuat Google Maps API
                    loadApi: function() {
                        if (this.apiLoaded || this.apiLoading) return;

                        this.apiLoading = true;

                        // Set callback global untuk API
                        window.initGoogleMapsApi = function() {
                            GoogleMapsManager.apiLoaded = true;
                            GoogleMapsManager.apiLoading = false;
                            GoogleMapsManager.initializePendingMaps();
                        };

                        // Buat script element dengan loading=async
                        const script = document.createElement('script');
                        script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}&callback=initGoogleMapsApi&loading=async`;
                        script.async = true;
                        script.defer = true;

                        // Handler error
                        script.onerror = function() {
                            console.error('Gagal memuat Google Maps API');
                            GoogleMapsManager.apiLoading = false;
                            GoogleMapsManager.showAllFallbacks();
                        };

                        document.head.appendChild(script);

                        // Set timeout keamanan
                        setTimeout(() => {
                            if (!this.apiLoaded) {
                                console.error('Google Maps API tidak dimuat dalam waktu yang ditentukan');
                                this.apiLoading = false;
                                this.showAllFallbacks();
                            }
                        }, 10000);
                    },

                    // Registrasi peta baru
                    registerMap: function(id, config) {
                        this.mapConfigs[id] = config;

                        // Jika API sudah dimuat, inisialisasi peta langsung
                        if (this.apiLoaded) {
                            this.initializeMap(id);
                        } else if (!this.apiLoading) {
                            // Jika API belum dimuat dan belum dalam proses loading, muat API
                            this.loadApi();
                        }
                    },

                    // Inisialisasi peta yang menunggu
                    initializePendingMaps: function() {
                        for (const id in this.mapConfigs) {
                            if (!this.mapInstances[id]) {
                                this.initializeMap(id);
                            }
                        }
                    },

                    // Inisialisasi peta tertentu
                    initializeMap: function(id) {
                        const config = this.mapConfigs[id];
                        if (!config) return;

                        const mapElement = document.getElementById(id);
                        if (!mapElement) return;

                        try {
                            // Buat objek LatLng
                            const center = new google.maps.LatLng(config.lat, config.lng);

                            // Opsi peta
                            const mapOptions = {
                                zoom: config.zoom,
                                center: center,
                                mapTypeControl: false,
                                streetViewControl: false,
                                fullscreenControl: true,
                                zoomControl: true,
                                zoomControlOptions: {
                                    position: google.maps.ControlPosition.RIGHT_BOTTOM
                                }
                            };

                            // Buat instance peta
                            const map = new google.maps.Map(mapElement, mapOptions);

                            // Gunakan AdvancedMarkerElement alih-alih Marker biasa
                            if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
                                // Gunakan AdvancedMarkerElement (recommended)
                                const markerView = new google.maps.marker.AdvancedMarkerElement({
                                    map: map,
                                    position: center,
                                    title: config.title
                                });

                                // Tambahkan info window
                                if (config.address) {
                                    const infoWindow = new google.maps.InfoWindow({
                                        content: `<div class="p-2"><strong>${config.title}</strong><p class="mt-1">${config.address}</p></div>`
                                    });

                                    markerView.addListener('click', function() {
                                        infoWindow.open(map, markerView);
                                    });
                                }

                                // Simpan instance peta
                                this.mapInstances[id] = {
                                    map: map,
                                    marker: markerView
                                };
                            } else {
                                // Fallback ke Marker biasa jika AdvancedMarkerElement tidak tersedia
                                const marker = new google.maps.Marker({
                                    position: center,
                                    map: map,
                                    title: config.title,
                                    animation: google.maps.Animation.DROP
                                });

                                if (config.address) {
                                    const infoWindow = new google.maps.InfoWindow({
                                        content: `<div class="p-2"><strong>${config.title}</strong><p class="mt-1">${config.address}</p></div>`
                                    });

                                    marker.addListener('click', function() {
                                        infoWindow.open(map, marker);
                                    });
                                }

                                // Simpan instance peta
                                this.mapInstances[id] = {
                                    map: map,
                                    marker: marker
                                };
                            }

                            // Sembunyikan fallback
                            const fallback = mapElement.querySelector('.map-fallback');
                            if (fallback) {
                                fallback.classList.add('hidden');
                            }

                            // Hapus class loading jika ada
                            mapElement.classList.remove('google-map-loading');

                            console.log(`Peta berhasil diinisialisasi: ${id}`);
                        } catch (error) {
                            console.error(`Error inisialisasi peta ${id}:`, error);
                            this.showFallback(id);
                        }
                    },

                    // Tampilkan fallback untuk peta tertentu
                    showFallback: function(id) {
                        const mapElement = document.getElementById(id);
                        if (!mapElement) return;

                        mapElement.classList.remove('google-map-loading');

                        const fallback = mapElement.querySelector('.map-fallback');
                        if (fallback) {
                            fallback.classList.remove('hidden');
                        }
                    },

                    // Tampilkan semua fallback
                    showAllFallbacks: function() {
                        document.querySelectorAll('.map-fallback').forEach(function(fallback) {
                            fallback.classList.remove('hidden');
                        });

                        document.querySelectorAll('.google-map-container').forEach(function(container) {
                            container.classList.remove('google-map-loading');
                        });
                    }
                };
            })();

            // Fungsi untuk meregistrasi peta dari komponen Blade
            function registerGoogleMap(id, lat, lng, zoom, title, address) {
                // Tambahkan class loading
                const mapElement = document.getElementById(id);
                if (mapElement) {
                    mapElement.classList.add('google-map-loading');
                }

                // Registrasi peta ke manager
                if (window.GoogleMapsManager) {
                    window.GoogleMapsManager.registerMap(id, {
                        lat: lat,
                        lng: lng,
                        zoom: zoom,
                        title: title,
                        address: address
                    });
                } else {
                    console.error('Google Maps Manager tidak tersedia');

                    // Tampilkan fallback jika manager tidak tersedia
                    if (mapElement) {
                        const fallback = mapElement.querySelector('.map-fallback');
                        if (fallback) {
                            fallback.classList.remove('hidden');
                        }
                        mapElement.classList.remove('google-map-loading');
                    }
                }
            }

            // Registrasi peta saat dokumen dimuat
            document.addEventListener('DOMContentLoaded', function() {
                // Registrasi peta spesifik ini
                registerGoogleMap(
                    '{{ $id }}',
                    {{ $lat }},
                    {{ $lng }},
                    {{ $zoom }},
                    '{{ $title }}',
                    '{{ $address }}'
                );
            });
        </script>
    @endpush
@endonce
