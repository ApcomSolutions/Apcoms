{{-- resources/views/admin/components/TimePeriodSelector.blade.php --}}
<!-- Time Period Selector -->
<div class="mb-8 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
    <div class="flex space-x-2">
        <button class="time-period-btn px-4 py-2 text-sm font-medium rounded bg-indigo-100 text-indigo-700" data-period="day">Today</button>
        <button class="time-period-btn px-4 py-2 text-sm font-medium rounded text-gray-700 hover:bg-gray-100" data-period="week">This Week</button>
        <button class="time-period-btn px-4 py-2 text-sm font-medium rounded text-gray-700 hover:bg-gray-100" data-period="month">This Month</button>
        <button class="time-period-btn px-4 py-2 text-sm font-medium rounded text-gray-700 hover:bg-gray-100" data-period="all">All Time</button>
    </div>
</div>
