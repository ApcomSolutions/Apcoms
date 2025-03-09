{{-- resources/views/admin/components/KeyStatsCards.blade.php --}}
<!-- Key Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Views</h3>
        <p class="text-3xl font-bold text-gray-800" id="total-views">-</p>
        <div class="mt-2 flex items-center text-sm">
            <span class="text-green-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                </svg>
                <span id="total-views-change">12%</span>
            </span>
            <span class="text-gray-500 ml-2">from previous period</span>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Unique Viewers</h3>
        <p class="text-3xl font-bold text-gray-800" id="unique-viewers">-</p>
        <div class="mt-2 flex items-center text-sm">
            <span class="text-green-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                </svg>
                <span id="unique-viewers-change">8%</span>
            </span>
            <span class="text-gray-500 ml-2">from previous period</span>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Avg. Read Time</h3>
        <p class="text-3xl font-bold text-gray-800" id="avg-read-time">-</p>
        <div class="mt-2 flex items-center text-sm">
            <span class="text-green-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                </svg>
                <span id="avg-read-time-change">5%</span>
            </span>
            <span class="text-gray-500 ml-2">from previous period</span>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-1">Completion Rate</h3>
        <p class="text-3xl font-bold text-gray-800" id="completion-rate">-</p>
        <div class="mt-2 flex items-center text-sm">
            <span class="text-red-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                </svg>
                <span id="completion-rate-change">3%</span>
            </span>
            <span class="text-gray-500 ml-2">from previous period</span>
        </div>
    </div>
</div>
