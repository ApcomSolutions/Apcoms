{{-- resources/views/Admin/NewsCategoryCrud.blade.php --}}
<x-layout>
    <x-slot:title>News Categories Management</x-slot:title>

    <!-- Add a script to prevent any wrong initialization -->
    <script>
        // This prevents any previous category.js from initializing
        window.insightCategoriesDisabled = true;
    </script>

    <!-- Header -->
{{--    <header class="header-gradient text-white py-4 px-6">--}}
{{--        <div class="container mx-auto flex justify-between items-center">--}}
{{--            <div class="flex items-center">--}}
{{--                <h1 class="text-2xl font-bold">News Categories Manager</h1>--}}
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
{{--                <a href="{{ route('admin.news.index') }}" class="flex items-center text-white hover:text-indigo-100">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"--}}
{{--                         fill="currentColor">--}}
{{--                        <path fill-rule="evenodd"--}}
{{--                              d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z"--}}
{{--                              clip-rule="evenodd"/>--}}
{{--                        <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>--}}
{{--                    </svg>--}}
{{--                    News--}}
{{--                </a>--}}
{{--                <a href="{{ route('admin.insights') }}" class="flex items-center text-white hover:text-indigo-100">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"--}}
{{--                         fill="currentColor">--}}
{{--                        <path fill-rule="evenodd"--}}
{{--                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"--}}
{{--                              clip-rule="evenodd"/>--}}
{{--                    </svg>--}}
{{--                    Insights--}}
{{--                </a>--}}
{{--                <a href="{{ route('admin.categories') }}" class="flex items-center text-white hover:text-indigo-100">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"--}}
{{--                         fill="currentColor">--}}
{{--                        <path fill-rule="evenodd"--}}
{{--                              d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z"--}}
{{--                              clip-rule="evenodd"/>--}}
{{--                    </svg>--}}
{{--                    Insight Categories--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </header>--}}
    @include('Admin.Partials.AdminHeader', [
        'title' => 'News Categories Manager',
        'subtitle' => 'Admin Panel'
    ])
    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-8">
        <div class="mb-8 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-800">News Categories</h2>
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
                <button id="new-category-btn"
                        class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    Add Category
                </button>
            </div>
        </div>

        <!-- Search -->
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
                    <input type="text" id="search" placeholder="Search categories..."
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-pink-500 focus:border-pink-500">
                </div>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="mb-8 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Slug</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Articles</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
                </thead>
                <tbody id="categories-table-body" class="divide-y divide-gray-200 bg-white">
                <!-- Categories will be populated via JavaScript -->
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-500">
                        <div class="flex justify-center">
                            <svg class="h-10 w-10 text-gray-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div class="mt-2">Loading categories...</div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Category Order -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Arrange Category Order</h3>
            <p class="text-gray-600 mb-4">Drag and drop categories to change their display order on the website.</p>

            <div class="card p-4">
                <div id="sortable-categories" class="space-y-2">
                    <!-- Sortable categories will be populated via JavaScript -->
                    <div class="flex justify-center items-center h-16 bg-gray-100 rounded-lg animate-pulse">
                        <span class="text-gray-500">Loading...</span>
                    </div>
                </div>

                <div class="mt-4 text-right">
                    <button id="save-order-btn"
                            class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Save Order
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Category Editor Modal -->
    @include('Admin.Partials.NewsCategoryEditorModal')

    <!-- Delete Confirmation Modal -->
    @include('Admin.Partials.NewsCategoryDeleteModal')

    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        @include('Admin.Styles.NewsCategoryCrud')
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    @endpush

    <!-- Load our script at the end to ensure it overrides any other scripts -->
    <script src="{{ asset('js/news-categories-standalone.js') }}"></script>
</x-layout>
