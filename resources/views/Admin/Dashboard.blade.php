{{-- resources/views/admin/dashboard.blade.php --}}
<x-layout>
    <x-slot:title>InsightTime Dashboard</x-slot:title>

    <!-- Header -->
    <header class="wakatime-gradient text-white py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold">InsightTime</h1>
                <span class="ml-4 px-2 py-1 bg-white/20 rounded text-xs">Analytics</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <span class="status-dot active-dot"></span>
                    <span class="text-sm">Live Data</span>
                </div>
                <button id="refresh-btn" class="bg-white/20 hover:bg-white/30 rounded-full p-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

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
