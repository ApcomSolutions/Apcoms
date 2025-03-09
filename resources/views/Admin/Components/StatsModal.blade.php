{{-- resources/views/Admin/Components/StatsModal.blade.php --}}
<div id="stats-modal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div class="modal-container bg-white w-11/12 md:max-w-3xl mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content py-4 text-left px-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800" id="modal-title">Article Statistics</h3>
                <button class="modal-close cursor-pointer z-50">
                    <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body - Stats Cards -->
            <div class="mt-5 space-y-6">
                <!-- Key Stats Cards -->
                <div id="insight-loading" class="py-8 text-center text-gray-500">
                    <svg class="animate-spin h-10 w-10 mx-auto mb-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p>Loading statistics...</p>
                </div>

                <div id="insight-stats" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="border rounded-lg p-4 bg-indigo-50 border-indigo-200">
                            <h4 class="text-sm font-medium text-indigo-800 mb-1">Total Views</h4>
                            <p class="text-2xl font-bold text-indigo-900" id="modal-total-views">-</p>
                            <p class="text-sm text-indigo-700 mt-1"><span id="modal-unique-viewers">-</span> unique visitors</p>
                        </div>

                        <div class="border rounded-lg p-4 bg-green-50 border-green-200">
                            <h4 class="text-sm font-medium text-green-800 mb-1">Average Read Time</h4>
                            <p class="text-2xl font-bold text-green-900" id="modal-avg-time">-</p>
                            <p class="text-sm text-green-700 mt-1">Min: <span id="modal-min-time">-</span>, Max: <span id="modal-max-time">-</span></p>
                        </div>

                        <div class="border rounded-lg p-4 bg-amber-50 border-amber-200">
                            <h4 class="text-sm font-medium text-amber-800 mb-1">Completion Rate</h4>
                            <p class="text-2xl font-bold text-amber-900" id="modal-completion-rate">-</p>
                            <div class="w-full bg-amber-200 rounded-full h-2.5 mt-2">
                                <div class="bg-amber-600 h-2.5 rounded-full" id="modal-completion-bar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="border rounded-lg p-4">
                            <h4 class="text-base font-medium text-gray-700 mb-4">Views Over Time</h4>
                            <div class="chart-container">
                                <canvas id="views-chart"></canvas>
                            </div>
                        </div>

                        <div class="border rounded-lg p-4">
                            <h4 class="text-base font-medium text-gray-700 mb-4">Device Breakdown</h4>
                            <div class="chart-container">
                                <canvas id="device-chart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h4 class="text-base font-medium text-gray-700 mb-2">Article Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <p class="text-sm text-gray-600"><strong>Author:</strong> <span id="modal-author">-</span></p>
                                <p class="text-sm text-gray-600"><strong>Category:</strong> <span id="modal-category">-</span></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Published:</strong> <span id="modal-date">-</span></p>
                                <p class="text-sm text-gray-600"><strong>Slug:</strong> <span id="modal-slug">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-4 border-t mt-5">
                <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Close</button>
                <a id="modal-edit-link" href="#" class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Edit Article</a>
            </div>
        </div>
    </div>
</div>
