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
        <style>
            .header-gradient {
                background: linear-gradient(135deg, #4f46e5, #7c3aed);
            }
        </style>
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
