{{-- resources/views/Admin/NewsCrud.blade.php --}}
<x-layout>
    <x-slot:title>News Management Dashboard</x-slot:title>

    <!-- Header -->
    @include('Admin.Partials.AdminHeader', [
    'title' => 'News Manager',
    'subtitle' => 'Admin Panel'
])
{{--    <header class="header-gradient text-white py-4 px-6">--}}
{{--        <div class="container mx-auto flex justify-between items-center">--}}
{{--            <div class="flex items-center">--}}
{{--                <h1 class="text-2xl font-bold">News Manager</h1>--}}
{{--                <span class="ml-4 px-2 py-1 bg-white/20 rounded text-xs">Admin Panel</span>--}}
{{--            </div>--}}
{{--            <div class="flex space-x-4">--}}
{{--                <a href="{{ route('admin.dashboard') }}" class="flex items-center text-white hover:text-indigo-100">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"--}}
{{--                         fill="currentColor">--}}
{{--                        <path--}}
{{--                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>--}}
{{--                    </svg>--}}
{{--                    Dashboard--}}
{{--                </a>--}}
{{--                <a href="{{ route('admin.gallery') }}" class="flex items-center text-white hover:text-indigo-100">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"--}}
{{--                         fill="currentColor">--}}
{{--                        <path fill-rule="evenodd"--}}
{{--                              d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"--}}
{{--                              clip-rule="evenodd"/>--}}
{{--                    </svg>--}}
{{--                    Gallery--}}
{{--                </a>--}}
{{--                <a href="{{ route('admin.teams') }}" class="flex items-center text-white hover:text-indigo-100">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"--}}
{{--                         fill="currentColor">--}}
{{--                        <path--}}
{{--                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>--}}
{{--                    </svg>--}}
{{--                    Team--}}
{{--                </a>--}}
{{--                <a href="{{ route('admin.clients') }}" class="flex items-center text-white hover:text-indigo-100">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"--}}
{{--                         fill="currentColor">--}}
{{--                        <path--}}
{{--                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>--}}
{{--                    </svg>--}}
{{--                    Clients--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </header>--}}

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-8">
        <div class="mb-8 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-800">News Management</h2>
            <div class="flex space-x-2">
                <button id="refresh-btn"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                              clip-rule="evenodd"/>
                    </svg>
                    Refresh
                </button>
                <a href="{{ route('admin.news.categories') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z"
                              clip-rule="evenodd"/>
                        <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>
                    </svg>
                    Manage Categories
                </a>
                <button id="new-news-btn"
                        class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    Add News
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="card p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="relative w-full md:w-1/3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                             fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <input type="text" id="search" placeholder="Search news..."
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-pink-500 focus:border-pink-500">
                </div>
                <div class="flex space-x-2">
                    <select id="category-filter"
                            class="border border-gray-300 rounded-md px-4 py-2 focus:ring-pink-500 focus:border-pink-500">
                        <option value="">All Categories</option>
                        <!-- Categories will be populated via JavaScript -->
                    </select>
                    <select id="status-filter"
                            class="border border-gray-300 rounded-md px-4 py-2 focus:ring-pink-500 focus:border-pink-500">
                        <option value="">All Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- News Data Table -->
        <div class="mb-8 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Title</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Category</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Author</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
                </thead>
                <tbody id="news-table-body" class="divide-y divide-gray-200 bg-white">
                <!-- News items will be populated via JavaScript -->
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-500">
                        <div class="flex justify-center">
                            <svg class="h-10 w-10 text-gray-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div class="mt-2">Loading news items...</div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium" id="page-start">1</span> to <span class="font-medium" id="page-end">10</span> of
                    <span class="font-medium" id="total-news">--</span> results
                </p>
            </div>
            <div class="flex space-x-1">
                <button id="prev-page"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                         aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
                <div id="pagination-numbers" class="flex">
                    <!-- Pagination numbers will be populated via JavaScript -->
                    <button
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        1
                    </button>
                </div>
                <button id="next-page"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                         aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Analytics Dashboard Section -->
        <div class="mt-12">
            <h3 class="text-lg font-medium text-gray-800 mb-4">News Analytics</h3>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card bg-white p-4 rounded-lg shadow">
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Total Views</h4>
                    <p class="text-2xl font-bold" id="stat-total-views">--</p>
                    <div class="text-xs flex items-center mt-1" id="stat-views-change">
                        <span class="text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                            </svg>
                            <span>Loading...</span>
                        </span>
                    </div>
                </div>
                <div class="stat-card bg-white p-4 rounded-lg shadow">
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Unique Visitors</h4>
                    <p class="text-2xl font-bold" id="stat-unique-visitors">--</p>
                    <div class="text-xs flex items-center mt-1" id="stat-visitors-change">
                        <span class="text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                            </svg>
                            <span>Loading...</span>
                        </span>
                    </div>
                </div>
                <div class="stat-card bg-white p-4 rounded-lg shadow">
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Avg. Read Time</h4>
                    <p class="text-2xl font-bold" id="stat-avg-read-time">--</p>
                    <div class="text-xs flex items-center mt-1" id="stat-read-time-change">
                        <span class="text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                            </svg>
                            <span>Loading...</span>
                        </span>
                    </div>
                </div>
                <div class="stat-card bg-white p-4 rounded-lg shadow">
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Completion Rate</h4>
                    <p class="text-2xl font-bold" id="stat-completion-rate">--</p>
                    <div class="text-xs flex items-center mt-1" id="stat-completion-change">
                        <span class="text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                            </svg>
                            <span>Loading...</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Activity Chart -->
                <div class="analytics-card bg-white p-4 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-medium text-gray-800">Activity Over Time</h4>
                        <select id="activity-period" class="text-sm border-gray-300 rounded">
                            <option value="day">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>
                    <div class="h-64 relative" id="activity-chart-container">
                        <!-- Initial loading state will be completely replaced by JavaScript -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-500 mt-2">Loading chart...</p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Read Time Distribution -->
                <div class="analytics-card bg-white p-4 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-medium text-gray-800">Read Time Distribution</h4>
                    </div>
                    <div class="h-64 relative" id="read-time-chart-container">
                        <!-- Initial loading state will be completely replaced by JavaScript -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-500 mt-2">Loading chart...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular News Table -->
            <div class="analytics-card bg-white p-4 rounded-lg shadow mb-6">
                <h4 class="font-medium text-gray-800 mb-4">Top Performing News</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Viewers</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Read Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                        </tr>
                        </thead>
                        <tbody id="top-news-table" class="divide-y divide-gray-200">
                        <!-- Top news will be populated via JavaScript -->
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                <svg class="animate-spin h-5 w-5 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="mt-1">Loading top news...</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Device Breakdown -->
            <div class="analytics-card bg-white p-4 rounded-lg shadow">
                <h4 class="font-medium text-gray-800 mb-4">Device Breakdown</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="h-48 relative" id="device-chart-container">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-500 mt-2">Loading chart...</p>
                            </div>
                        </div>
                        <canvas id="device-chart" class="w-full h-full"></canvas>
                    </div>
                    <div id="device-stats" class="flex flex-col justify-center">
                        <!-- Device stats will be populated via JavaScript -->
                        <div class="animate-pulse">
                            <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/2 mb-3"></div>
                            <div class="h-4 bg-gray-200 rounded w-2/3 mb-3"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- News Editor Modal -->
    @include('Admin.Partials.NewsEditorModal')

    <!-- News Preview Modal -->
    @include('Admin.Partials.NewsPreviewModal')

    <!-- Delete Confirmation Modal -->
    @include('Admin.Partials.NewsDeleteModal')

    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        @include('Admin.Styles.NewsCrud')
        <style>
            /* Rich Text Editor Styles */
            .trix-editor {
                min-height: 300px;
                max-height: 500px;
                overflow-y: auto;
                border-radius: 0.375rem;
                border-color: #e5e7eb;
                padding: 0.75rem;
            }
            .trix-editor:focus {
                border-color: #ec4899;
                box-shadow: 0 0 0 1px #ec4899;
            }
            .trix-button-group {
                border-color: #e5e7eb;
            }
            .trix-button {
                border-bottom: none;
            }
            .trix-button.trix-active {
                background: #f9fafb;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
    @endpush

    @push('page_scripts')
        @vite(['resources/js/admin/news.js'])
    @endpush
</x-layout>
