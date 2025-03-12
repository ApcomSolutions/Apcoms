{{-- resources/views/admin/dashboard.blade.php --}}
<x-layout>
    <x-slot:title>InsightTime Dashboard</x-slot:title>

    <!-- Header -->
    @include('Admin.Partials.AdminHeader', [
    'title' => 'InsightTime',
    'subtitle' => 'Analytics',
    'showRefreshButton' => true,
    'isDashboard' => true  // Flag khusus untuk dashboard
])
{{--    <header class="wakatime-gradient text-white py-4 px-6">--}}
{{--        <div class="container mx-auto flex justify-between items-center">--}}
{{--            <div class="flex items-center">--}}
{{--                <h1 class="text-2xl font-bold">InsightTime</h1>--}}
{{--                <span class="ml-4 px-2 py-1 bg-white/20 rounded text-xs">Analytics</span>--}}
{{--            </div>--}}
{{--            <div class="flex items-center space-x-4">--}}
{{--                <div class="flex items-center">--}}
{{--                    <span class="status-dot active-dot"></span>--}}
{{--                    <span class="text-sm">Live Data</span>--}}
{{--                </div>--}}
{{--                <button id="refresh-btn" class="bg-white/20 hover:bg-white/30 rounded-full p-2 transition">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">--}}
{{--                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />--}}
{{--                    </svg>--}}
{{--                </button>--}}
{{--                <div class="flex space-x-4">--}}
{{--                    <a href="/admin/dashboard" class="flex items-center text-white hover:text-indigo-100">--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">--}}
{{--                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />--}}
{{--                        </svg>--}}
{{--                        Dashboard--}}
{{--                    </a>--}}
{{--                    <a href="/admin/insights" class="flex items-center text-white hover:text-indigo-100">--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">--}}
{{--                            <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />--}}
{{--                            <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />--}}
{{--                        </svg>--}}
{{--                        Articles--}}
{{--                    </a>--}}
{{--                    <a href="/admin/categories" class="flex items-center text-white hover:text-indigo-100 font-semibold">--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">--}}
{{--                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />--}}
{{--                        </svg>--}}
{{--                        Categories--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </header>--}}

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-8">
        @include('admin.components.TimePeriodSelector')
        @include('admin.components.KeyStatsCards')
        @include('admin.components.DailyActivityChart')

        <!-- Two Column Layout for Insights Table and Device/Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @include('admin.components.TopInsightsTable')
            @include('admin.components.SideCharts')
        </div>

        @include('admin.components.RecentActivity')
    </main>

    <!-- Footer (simplified) -->
    <footer class="bg-white border-t border-gray-200 py-4 px-6">
        <div class="container mx-auto text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} InsightTime. All data is updated in real-time.
        </div>
    </footer>

    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        @include('admin.styles.Dashboard')
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.2.0/dist/chartjs-adapter-luxon.min.js"></script>
    @endpush

    @push('page_scripts')
        @vite(['resources/js/admin/dashboard.js'])
    @endpush
</x-layout>
