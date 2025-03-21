{{-- resources/views/components/top-insights.blade.php --}}
<div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
    <div class="px-6 py-4 bg-gradient-to-r from-pink-500 to-pink-600 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-24 h-24 opacity-10">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-full h-full text-white">
                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
            </svg>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center relative z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
            </svg>
            Artikel Populer
        </h2>
    </div>

    <div id="top-insights-container" class="px-6 py-4">
        <div class="space-y-3 animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4 mt-4"></div>
            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4 mt-4"></div>
            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
        </div>
    </div>

    <div class="px-6 py-3 bg-gray-50 text-center border-t border-gray-100">
        <a href="{{ route('insights.index') }}" class="text-pink-500 hover:text-pink-700 text-sm font-medium inline-flex items-center transition-all hover:translate-x-1">
            Jelajahi artikel lainnya
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch top insights data directly from API
        fetch('/api/top-insights?limit=5')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Use the data directly without filtering
                displayTopInsights(data);
            })
            .catch(error => {
                console.error('Error fetching top insights:', error);
                displayError();
            });

        function displayTopInsights(insights) {
            const container = document.getElementById('top-insights-container');
            container.innerHTML = '';

            if (!insights || insights.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada artikel populer</p>';
                return;
            }

            insights.forEach((insight, index) => {
                const article = document.createElement('div');
                article.classList.add('group', 'mb-4', 'last:mb-0');

                article.innerHTML = `
                    <a href="/insights/${insight.slug}" class="flex items-start p-3 rounded-lg group-hover:bg-pink-50 transition-all duration-300">
                        <div class="flex-shrink-0 mr-3 relative">
                            <div class="bg-pink-100 rounded-full w-8 h-8 flex items-center justify-center z-10 relative">
                                <span class="text-pink-600 font-semibold text-sm">${index + 1}</span>
                            </div>
                            ${index < 3 ? `
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute top-0 right-0 h-3 w-3 text-pink-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            ` : ''}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-gray-800 font-medium mb-1.5 group-hover:text-pink-600 transition-colors duration-300 line-clamp-2">${insight.judul}</h3>
                            <div class="flex flex-wrap items-center text-xs text-gray-500 gap-x-3 gap-y-1">
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    ${formatNumber(insight.views)} dilihat
                                </span>
                                ${insight.category ? `
                                <span class="inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    ${insight.category}
                                </span>
                                ` : ''}
                            </div>
                        </div>
                    </a>
                    ${index < insights.length - 1 ? '<div class="h-px bg-gray-100 mx-3"></div>' : ''}
                `;

                container.appendChild(article);
            });
        }

        function displayError() {
            const container = document.getElementById('top-insights-container');
            container.innerHTML = `
                <div class="text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500">Tidak dapat memuat artikel populer</p>
                    <button id="retry-button" class="mt-2 text-pink-500 hover:text-pink-700 text-sm">Coba lagi</button>
                </div>
            `;

            document.getElementById('retry-button').addEventListener('click', function() {
                container.innerHTML = `
                    <div class="space-y-3 animate-pulse">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mt-4"></div>
                        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mt-4"></div>
                        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                    </div>
                `;

                // Try fetching data again
                fetch('/api/top-insights?limit=5')
                    .then(response => response.json())
                    .then(data => displayTopInsights(data))
                    .catch(error => {
                        console.error('Error fetching top insights:', error);
                        displayError();
                    });
            });
        }

        // Format numbers with commas
        function formatNumber(num) {
            if (!num) return "0";
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    });
</script>
