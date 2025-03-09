{{-- resources/views/insights/detail.blade.php --}}
<x-layout>
    <x-slot:title>{{ $insight->judul }}</x-slot:title>

    <x-navbar />

    <div class="bg-white w-full">
        <div class="container mx-auto px-4 py-8">
            <!-- Hidden tracking ID for JavaScript -->
            <input type="hidden" id="tracking-id" value="{{ $tracking->id }}">

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Main Content -->
                <div class="md:w-2/3">
                    <h1 class="text-3xl font-bold mb-4 text-gray-800">{{ $insight->judul }}</h1>
                    <p class="text-gray-600 mb-6">Penulis: {{ $insight->penulis }} | Terbit: {{ \Carbon\Carbon::parse($insight->TanggalTerbit)->format('d M Y') }}</p>

                    @if($insight->image_url)
                        <img src="{{ $insight->image_url }}" class="w-full h-auto rounded-lg mb-8 shadow-md" alt="{{ $insight->judul }}">
                    @endif

                    <div class="prose max-w-none mb-8 bg-white p-6 rounded-lg shadow-md">
                        {!! $insight->isi !!}
                    </div>

                    <div class="mb-8">
                        <a href="{{ route('insights.index') }}" class="inline-block px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-200">Kembali</a>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="md:w-1/3">
                    <!-- Search Section -->
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6"
                         x-data="{
                        query: '',
                        results: [],
                        searching: false,
                        debounceTimer: null,

                        handleSearch() {
                            this.query = this.$refs.searchInput.value.trim();

                            // Clear previous timeout
                            clearTimeout(this.debounceTimer);

                            if (this.query.length > 0) {
                                this.searching = true;
                                this.results = [];
                                this.$refs.searchResults.classList.remove('hidden');
                            } else {
                                this.searching = false;
                                this.results = [];
                                this.$refs.searchResults.classList.add('hidden');
                                return;
                            }

                            // Debounce search requests
                            this.debounceTimer = setTimeout(() => {
                                if (this.query.length < 2) return;

                                fetch(`/api/insights/search?query=${encodeURIComponent(this.query)}`)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Network response was not ok');
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        this.searching = false;
                                        this.results = data.data || [];
                                    })
                                    .catch(error => {
                                        console.error('Search error:', error);
                                        this.searching = false;
                                        this.results = [];
                                    });
                            }, 300);
                        },

                        formatDate(dateString) {
                            return new Date(dateString).toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                            });
                        }
                     }">
                        <h2 class="text-xl font-semibold mb-4">Search</h2>
                        <div class="border-t-2 border-indigo-500 w-16 mb-6"></div>
                        <div class="relative">
                            <input type="text" x-ref="searchInput" id="search-input" placeholder="Search..."
                                   class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   @input="handleSearch()">
                            <span class="absolute right-2 top-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        </div>
                        <div x-ref="searchResults" id="search-results" class="mt-4 hidden">
                            <template x-if="searching">
                                <p class="text-gray-500">Searching...</p>
                            </template>
                            <template x-if="results.length === 0 && !searching">
                                <p class="text-gray-500">No results found</p>
                            </template>
                            <div class="space-y-3" x-show="results.length > 0">
                                <template x-for="item in results.slice(0, 5)" :key="item.id">
                                    <div class="border-b pb-2">
                                        <a :href="`/insights/${item.slug}`" class="text-gray-700 hover:text-indigo-600" x-text="item.judul"></a>
                                        <p class="text-xs text-gray-500" x-text="formatDate(item.TanggalTerbit)"></p>
                                    </div>
                                </template>
                                <template x-if="results.length > 5">
                                    <div class="text-center mt-2">
                                        <a :href="`/insights?query=${encodeURIComponent(query)}`" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            View all <span x-text="results.length"></span> results
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Section -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-xl font-semibold mb-4">Categories</h2>
                        <div class="border-t-2 border-indigo-500 w-16 mb-6"></div>
                        <ul class="space-y-4">
                            @foreach($categories as $category)
                                <li class="border-b pb-2">
                                    <a href="{{ route('insights.category', $category->slug) }}" class="flex justify-between items-center text-gray-700 hover:text-indigo-600 transition">
                                        <span>{{ $category->name }}</span>
                                        <span class="text-gray-500">({{ $category->insights_count }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <x-footer />

        @push('scripts')
            <script>
                // Reading tracking script
                document.addEventListener('DOMContentLoaded', function() {
                    // Get tracking ID from the page
                    const trackingId = document.getElementById('tracking-id').value;
                    if (!trackingId) return;

                    // Variables to track reading behavior
                    let readTimeInSeconds = 0;
                    let readingTimerStarted = Date.now();
                    let isActive = true;
                    let readingTimer;
                    let scrollDepth = 0;
                    let maxScrollDepth = 0;
                    let hasReachedEnd = false;

                    // Start the reading timer
                    startReadingTimer();

                    // Detect if user is active on the page
                    function checkActivity() {
                        document.addEventListener('mousemove', resetInactivity);
                        document.addEventListener('keydown', resetInactivity);
                        document.addEventListener('scroll', handleScroll);

                        // Set up inactivity timer
                        let inactivityTimer = setTimeout(setInactive, 60000); // 60 seconds without activity

                        function resetInactivity() {
                            clearTimeout(inactivityTimer);
                            inactivityTimer = setTimeout(setInactive, 60000);

                            if (!isActive) {
                                isActive = true;
                                startReadingTimer();
                            }
                        }

                        function setInactive() {
                            isActive = false;
                            clearInterval(readingTimer);
                            // Save current read time
                            sendReadingTime(false);
                        }
                    }

                    // Track scroll depth
                    function handleScroll() {
                        const scrollPosition = window.scrollY;
                        const documentHeight = Math.max(
                            document.body.scrollHeight,
                            document.body.offsetHeight,
                            document.documentElement.clientHeight,
                            document.documentElement.scrollHeight,
                            document.documentElement.offsetHeight
                        );
                        const windowHeight = window.innerHeight;

                        // Calculate scroll depth as percentage
                        const currentScrollDepth = (scrollPosition / (documentHeight - windowHeight)) * 100;
                        scrollDepth = Math.min(Math.round(currentScrollDepth), 100);

                        if (scrollDepth > maxScrollDepth) {
                            maxScrollDepth = scrollDepth;
                        }

                        // Check if user has reached the end (or close to it)
                        if (scrollDepth >= 90 && !hasReachedEnd) {
                            hasReachedEnd = true;
                            // Send reading time with "completed" flag
                            sendReadingTime(true);
                        }
                    }

                    // Timer function to track reading time
                    function startReadingTimer() {
                        readingTimerStarted = Date.now();
                        clearInterval(readingTimer);

                        readingTimer = setInterval(function() {
                            if (isActive) {
                                readTimeInSeconds++;

                                // Send update every 30 seconds
                                if (readTimeInSeconds % 30 === 0) {
                                    sendReadingTime(hasReachedEnd);
                                }
                            }
                        }, 1000);
                    }

                    // Send reading time to server
                    function sendReadingTime(completed = false) {
                        const data = {
                            tracking_id: trackingId,
                            seconds: readTimeInSeconds,
                            completed: completed
                        };

                        fetch('/api/tracking/read-time', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        })
                            .catch(error => {
                                console.error('Error updating read time:', error);
                            });
                    }

                    // Save reading time when user leaves the page
                    window.addEventListener('beforeunload', function() {
                        sendReadingTime(hasReachedEnd);
                    });

                    // Initialize activity tracking
                    checkActivity();
                });

                // Close search results when clicking outside
                document.addEventListener('click', function(event) {
                    const searchInput = document.getElementById('search-input');
                    const searchResults = document.getElementById('search-results');

                    if (searchInput && searchResults) {
                        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                            searchResults.classList.add('hidden');
                        }
                    }
                });
            </script>
    @endpush
</x-layout>
