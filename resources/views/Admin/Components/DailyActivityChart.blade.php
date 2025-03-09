{{-- resources/views/admin/components/DailyActivityChart.blade.php --}}
<!-- Daily Activity Chart -->
<div class="card p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Daily Activity</h3>
        <div class="flex space-x-2">
            <div class="tooltip">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <span class="tooltip-text">Shows the activity over the selected time period</span>
            </div>
        </div>
    </div>
    <div class="chart-container">
        <canvas id="activity-chart"></canvas>
    </div>
</div>
