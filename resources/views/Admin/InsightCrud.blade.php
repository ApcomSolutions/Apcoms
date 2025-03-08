<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Insight Management Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Trix Editor -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        .header-gradient {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
        }
        .card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .modal {
            transition: opacity 0.25s ease;
        }
        .modal-container {
            transform: scale(0.9);
            transition: transform 0.25s ease;
        }
        .modal.active .modal-container {
            transform: scale(1);
        }
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }
        /* Trix Editor Customization */
        trix-toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.5rem;
        }
        trix-editor {
            min-height: 300px;
            padding: 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            overflow-y: auto;
            max-height: calc(100vh - 300px);
        }
        trix-editor:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Fullscreen Modal */
        .modal-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            z-index: 50;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background-color: white;
        }

        .modal-fullscreen-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 10;
        }

        .modal-fullscreen-content {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .modal-fullscreen-footer {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
            position: sticky;
            bottom: 0;
            background-color: white;
            z-index: 10;
        }

        /* Editor container with scrolling */
        .trix-editor-container {
            max-height: calc(100vh - 350px);
            overflow-y: auto;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: white;
        }

        /* Fix for editor scrolling */
        #insight-form {
            max-height: 100%;
            overflow: visible;
            display: flex;
            flex-direction: column;
        }

        /* Make the form elements properly spaced in fullscreen */
        .editor-form-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 0 1rem;
        }
    </style>
</head>
<body>
<div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="header-gradient text-white py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold">Insight Manager</h1>
                <span class="ml-4 px-2 py-1 bg-white/20 rounded text-xs">Admin Panel</span>
            </div>
            <div class="flex space-x-4">
                <a href="/admin/dashboard" class="flex items-center text-white hover:text-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Dashboard
                </a>
                <a href="/admin/insights" class="flex items-center text-white hover:text-indigo-100 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />
                        <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />
                    </svg>
                    Articles
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-8">
        <div class="mb-8 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-800">Insight Management</h2>
            <div class="flex space-x-2">
                <button id="refresh-btn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                    Refresh
                </button>
                <button id="new-insight-btn" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Insight
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="card p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="relative w-full md:w-1/3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="search" placeholder="Search articles..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex flex-wrap gap-2">
                    <select id="category-filter" class="border border-gray-300 rounded-md px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Categories</option>
                        <!-- Categories will be populated via JavaScript -->
                    </select>
                    <select id="date-filter" class="border border-gray-300 rounded-md px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Articles Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Read Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="articles-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- Articles will be populated via JavaScript -->
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading articles...</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium" id="page-start">1</span> to <span class="font-medium" id="page-end">10</span> of <span class="font-medium" id="total-articles">--</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button id="prev-page" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="pagination-numbers" class="flex">
                                <!-- Pagination numbers will be populated via JavaScript -->
                                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</button>
                            </div>
                            <button id="next-page" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 px-6">
        <div class="container mx-auto text-center text-gray-500 text-sm">
            &copy; 2025 Insight Manager. All analytics data is processed in real-time.
        </div>
    </footer>

    <!-- Statistics Modal -->
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

    <!-- Create/Edit Modal (Fullscreen) -->
    <div id="editor-modal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 z-50">
        <div class="modal-fullscreen">
            <!-- Modal Header -->
            <div class="modal-fullscreen-header">
                <h3 class="text-xl font-bold text-gray-800" id="editor-title">Create New Insight</h3>
                <button class="modal-close cursor-pointer z-50 p-2 hover:bg-gray-100 rounded-full">
                    <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body - Form -->
            <div class="modal-fullscreen-content">
                <div class="editor-form-container">
                    <form id="insight-form" class="space-y-6">
                        <input type="hidden" id="form-method" value="POST">
                        <input type="hidden" id="form-insight-id" value="">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" id="judul" name="judul" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                <input type="text" id="slug" name="slug" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="penulis" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                                <input type="text" id="penulis" name="penulis" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select id="category_id" name="category_id" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select a category</option>
                                    <!-- Categories will be populated via JavaScript -->
                                </select>
                            </div>
                            <div>
                                <label for="TanggalTerbit" class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
                                <input type="date" id="TanggalTerbit" name="TanggalTerbit" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                            <div class="flex items-center space-x-4">
                                <input type="file" id="image" name="image" class="block py-2 px-3">
                                <div id="current-image-container" class="hidden">
                                    <div class="flex items-center space-x-2">
                                        <img id="current-image" src="" alt="Current Image" class="h-12 w-auto object-cover rounded">
                                        <span class="text-xs text-gray-500">Current image</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="isi-editor" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                            <input id="isi" type="hidden" name="isi">
                            <div class="trix-editor-container">
                                <trix-editor id="isi-editor" input="isi" class="trix-content"></trix-editor>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-fullscreen-footer">
                <div class="editor-form-container flex justify-end">
                    <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">Cancel</button>
                    <button id="save-insight-btn" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save Insight</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50">
            <div class="modal-content py-4 text-left px-6">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Confirm Deletion</h3>
                    <button class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18">
                            <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="py-4">
                    <p class="text-gray-700">Are you sure you want to delete "<span id="delete-insight-title" class="font-semibold"></span>"?</p>
                    <p class="text-red-600 text-sm mt-2">This action cannot be undone.</p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end pt-2 border-t">
                    <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">Cancel</button>
                    <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // State
        let articles = [];
        let categories = [];
        let currentPage = 1;
        let itemsPerPage = 10;
        let totalPages = 1;
        let viewsChart = null;
        let deviceChart = null;
        let currentInsightSlug = null;

        // CSRF token setup for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Initialize
        fetchArticles();
        fetchCategories();

        // Event Listeners
        document.getElementById('refresh-btn').addEventListener('click', fetchArticles);
        document.getElementById('search').addEventListener('input', filterArticles);
        document.getElementById('category-filter').addEventListener('change', filterArticles);
        document.getElementById('date-filter').addEventListener('change', filterArticles);
        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderArticles();
            }
        });
        document.getElementById('next-page').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderArticles();
            }
        });

        // New Insight Button
        document.getElementById('new-insight-btn').addEventListener('click', () => {
            openEditorModal();
        });

        // Save Insight Button
        document.getElementById('save-insight-btn').addEventListener('click', saveInsight);

        // Generate slug from title
        document.getElementById('judul').addEventListener('blur', function() {
            const title = this.value;
            const slugInput = document.getElementById('slug');

            // Only generate slug if it's empty or user hasn't modified it
            if (!slugInput.value || slugInput.dataset.autogenerated === 'true') {
                slugInput.value = generateSlug(title);
                slugInput.dataset.autogenerated = 'true';
            }
        });

        document.getElementById('slug').addEventListener('input', function() {
            // User has manually edited the slug
            this.dataset.autogenerated = 'false';
        });

        // Delete confirmation
        document.getElementById('confirm-delete-btn').addEventListener('click', () => {
            if (currentInsightSlug) {
                deleteInsight(currentInsightSlug);
            }
        });

        // Modal handling
        const modals = document.querySelectorAll('.modal');
        const modalCloseButtons = document.querySelectorAll('.modal-close');
        const modalOverlays = document.querySelectorAll('.modal-overlay');

        modalCloseButtons.forEach(button => {
            button.addEventListener('click', () => {
                modals.forEach(modal => {
                    closeModal(modal);
                });
            });
        });

        modalOverlays.forEach(overlay => {
            overlay.addEventListener('click', () => {
                modals.forEach(modal => {
                    closeModal(modal);
                });
            });
        });

        // Fetch Articles
        async function fetchArticles() {
            try {
                document.getElementById('articles-table-body').innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading articles...</td></tr>';

                const response = await fetch('/api/insights');
                if (!response.ok) throw new Error('Failed to fetch articles');

                articles = await response.json();
                renderArticles();
            } catch (error) {
                console.error('Error fetching articles:', error);
                document.getElementById('articles-table-body').innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading articles. Please try again.</td></tr>';
            }
        }

        // Fetch Categories
        async function fetchCategories() {
            try {
                const response = await fetch('/api/categories');
                if (!response.ok) throw new Error('Failed to fetch categories');

                categories = await response.json();

                // Populate category filters
                const categoryFilter = document.getElementById('category-filter');
                const formCategorySelect = document.getElementById('category_id');

                // Clear existing options (except the first one)
                while (categoryFilter.options.length > 1) {
                    categoryFilter.remove(1);
                }

                while (formCategorySelect.options.length > 1) {
                    formCategorySelect.remove(1);
                }

                // Add new options
                categories.forEach(category => {
                    // For filter
                    const filterOption = document.createElement('option');
                    filterOption.value = category.id;
                    filterOption.textContent = category.name;
                    categoryFilter.appendChild(filterOption);

                    // For form
                    const formOption = document.createElement('option');
                    formOption.value = category.id;
                    formOption.textContent = category.name;
                    formCategorySelect.appendChild(formOption);
                });
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        }

        // Filter Articles
        function filterArticles() {
            // Reset to first page when filtering
            currentPage = 1;
            renderArticles();
        }

        // Render Articles Table
        function renderArticles() {
            const tableBody = document.getElementById('articles-table-body');
            tableBody.innerHTML = '';

            // Apply filters
            const searchQuery = document.getElementById('search').value.toLowerCase();
            const categoryFilter = document.getElementById('category-filter').value;
            const dateFilter = document.getElementById('date-filter').value;

            let filteredArticles = articles.filter(article => {
                const matchesSearch = article.judul.toLowerCase().includes(searchQuery) ||
                    article.penulis.toLowerCase().includes(searchQuery);
                const matchesCategory = !categoryFilter || article.category_id == categoryFilter;

                // Date filtering
                let matchesDate = true;
                if (dateFilter) {
                    const publishDate = new Date(article.TanggalTerbit);
                    const now = new Date();

                    switch(dateFilter) {
                        case 'today':
                            matchesDate = publishDate.toDateString() === now.toDateString();
                            break;
                        case 'week':
                            const weekAgo = new Date();
                            weekAgo.setDate(now.getDate() - 7);
                            matchesDate = publishDate >= weekAgo;
                            break;
                        case 'month':
                            matchesDate = publishDate.getMonth() === now.getMonth() &&
                                publishDate.getFullYear() === now.getFullYear();
                            break;
                        case 'year':
                            matchesDate = publishDate.getFullYear() === now.getFullYear();
                            break;
                    }
                }

                return matchesSearch && matchesCategory && matchesDate;
            });

            // Calculate pagination
            totalPages = Math.ceil(filteredArticles.length / itemsPerPage);
            if (totalPages === 0) totalPages = 1;

            // Update pagination UI
            document.getElementById('total-articles').textContent = filteredArticles.length;
            document.getElementById('page-start').textContent = filteredArticles.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
            document.getElementById('page-end').textContent = Math.min(currentPage * itemsPerPage, filteredArticles.length);

            // Render pagination numbers
            renderPagination();

            // Slice for current page
            const pageArticles = filteredArticles.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

            if (pageArticles.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No articles found matching your filters</td></tr>';
                return;
            }

            // Create an array of promises for getting stats
            const statsPromises = pageArticles.map(article =>
                fetchInsightStats(article.id)
                    .catch(() => ({ total_views: 0, avg_read_time: 0 })) // Default values if stats not available
            );

            // Wait for all stats to load
            Promise.all(statsPromises).then(statsResults => {
                // Render articles with their stats
                pageArticles.forEach((article, index) => {
                    const row = document.createElement('tr');
                    row.classList.add('hover:bg-gray-50');

                    const publishDate = new Date(article.TanggalTerbit);
                    const formattedDate = publishDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });

                    const categoryName = article.category_name || 'Uncategorized';

                    // Get real stats from the stats array
                    const stats = statsResults[index];
                    const viewCount = stats.total_views || 0;
                    const avgReadTime = stats.avg_read_time || 0;

                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">${article.judul}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                ${categoryName}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${article.penulis}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${formattedDate}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${formatNumber(viewCount)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${avgReadTime.toFixed(1)} min
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900 view-stats" data-id="${article.id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900 edit-insight" data-slug="${article.slug}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                                <button class="text-red-600 hover:text-red-900 delete-insight" data-slug="${article.slug}" data-title="${article.judul}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    `;

                    tableBody.appendChild(row);
                });

                // Add event listeners to buttons
                document.querySelectorAll('.view-stats').forEach(button => {
                    button.addEventListener('click', () => {
                        const articleId = button.getAttribute('data-id');
                        openStatsModal(articleId);
                    });
                });

                document.querySelectorAll('.edit-insight').forEach(button => {
                    button.addEventListener('click', () => {
                        const slug = button.getAttribute('data-slug');
                        openEditorModal(slug);
                    });
                });

                document.querySelectorAll('.delete-insight').forEach(button => {
                    button.addEventListener('click', () => {
                        const slug = button.getAttribute('data-slug');
                        const title = button.getAttribute('data-title');
                        openDeleteModal(slug, title);
                    });
                });
            });
        }

        // Render pagination
        function renderPagination() {
            const paginationContainer = document.getElementById('pagination-numbers');
            paginationContainer.innerHTML = '';

            // Determine page range to show
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);

            // Adjust if we're near the end
            if (endPage - startPage < 4 && startPage > 1) {
                startPage = Math.max(1, endPage - 4);
            }

            // Add first page if we're not starting at 1
            if (startPage > 1) {
                addPaginationButton(1);
                if (startPage > 2) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700';
                    ellipsis.textContent = '...';
                    paginationContainer.appendChild(ellipsis);
                }
            }

            // Add page numbers
            for (let i = startPage; i <= endPage; i++) {
                addPaginationButton(i);
            }

            // Add last page if we're not ending at totalPages
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700';
                    ellipsis.textContent = '...';
                    paginationContainer.appendChild(ellipsis);
                }
                addPaginationButton(totalPages);
            }

            function addPaginationButton(pageNum) {
                const button = document.createElement('button');
                button.className = `relative inline-flex items-center px-4 py-2 border ${currentPage === pageNum ? 'border-indigo-500 bg-indigo-50 text-indigo-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'}`;
                button.textContent = pageNum;

                button.addEventListener('click', () => {
                    currentPage = pageNum;
                    renderArticles();
                });

                paginationContainer.appendChild(button);
            }
        }

        // Fetch insight stats
        async function fetchInsightStats(insightId) {
            try {
                const response = await fetch(`/api/admin/dashboard/insight-stats/${insightId}`);
                if (!response.ok) {
                    if (response.status === 404) {
                        // No stats available yet, return defaults
                        return { total_views: 0, avg_read_time: 0 };
                    }
                    throw new Error('Failed to fetch stats');
                }
                return await response.json();
            } catch (error) {
                console.error(`Error fetching stats for insight ${insightId}:`, error);
                return { total_views: 0, avg_read_time: 0 };
            }
        }

        // Open Statistics Modal
        async function openStatsModal(articleId) {
            const modal = document.getElementById('stats-modal');
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'active');

            // Show loading state
            document.getElementById('insight-loading').classList.remove('hidden');
            document.getElementById('insight-stats').classList.add('hidden');

            try {
                // Find the article
                const article = articles.find(a => a.id == articleId);

                if (!article) {
                    throw new Error('Article not found');
                }

                // Set article info in modal
                document.getElementById('modal-title').textContent = article.judul;
                document.getElementById('modal-author').textContent = article.penulis;
                document.getElementById('modal-category').textContent = article.category_name || 'Uncategorized';
                document.getElementById('modal-date').textContent = new Date(article.TanggalTerbit).toLocaleDateString();
                document.getElementById('modal-slug').textContent = article.slug;
                document.getElementById('modal-edit-link').href = `/admin/insights/edit/${article.slug}`;

                // Fetch article stats
                const response = await fetch(`/api/admin/dashboard/insight-stats/${articleId}`);

                if (!response.ok) {
                    if (response.status === 404) {
                        // No stats yet, set zeros
                        document.getElementById('modal-total-views').textContent = '0';
                        document.getElementById('modal-unique-viewers').textContent = '0';
                        document.getElementById('modal-avg-time').textContent = '0 min';
                        document.getElementById('modal-min-time').textContent = '0 min';
                        document.getElementById('modal-max-time').textContent = '0 min';
                        document.getElementById('modal-completion-rate').textContent = '0%';
                        document.getElementById('modal-completion-bar').style.width = '0%';
                    } else {
                        throw new Error('Failed to fetch stats');
                    }
                } else {
                    const stats = await response.json();

                    // Set modal stats
                    document.getElementById('modal-total-views').textContent = formatNumber(stats.total_views || 0);
                    document.getElementById('modal-unique-viewers').textContent = formatNumber(stats.unique_viewers || 0);
                    document.getElementById('modal-avg-time').textContent = `${stats.avg_read_time || 0} min`;
                    document.getElementById('modal-min-time').textContent = `${stats.min_read_time || 0} min`;
                    document.getElementById('modal-max-time').textContent = `${stats.max_read_time || 0} min`;
                    document.getElementById('modal-completion-rate').textContent = `${stats.completion_rate || 0}%`;
                    document.getElementById('modal-completion-bar').style.width = `${stats.completion_rate || 0}%`;
                }

                // Get device breakdown
                const deviceResponse = await fetch('/api/admin/dashboard/device-breakdown');
                let deviceData = [];

                if (deviceResponse.ok) {
                    deviceData = await deviceResponse.json();
                }

                // Create charts
                createViewsChart(articleId);
                createDeviceChart(deviceData);

                // Show stats content
                document.getElementById('insight-loading').classList.add('hidden');
                document.getElementById('insight-stats').classList.remove('hidden');

            } catch (error) {
                console.error('Error loading article stats:', error);

                // Set default stats on error
                document.getElementById('modal-total-views').textContent = '0';
                document.getElementById('modal-unique-viewers').textContent = '0';
                document.getElementById('modal-avg-time').textContent = '0 min';
                document.getElementById('modal-min-time').textContent = '0 min';
                document.getElementById('modal-max-time').textContent = '0 min';
                document.getElementById('modal-completion-rate').textContent = '0%';
                document.getElementById('modal-completion-bar').style.width = '0%';

                document.getElementById('insight-loading').classList.add('hidden');
                document.getElementById('insight-stats').classList.remove('hidden');

                // Create simple charts with no data
                createViewsChart();
                createDeviceChart();
            }
        }

        // Open Editor Modal
        async function openEditorModal(slug = null) {
            const modal = document.getElementById('editor-modal');
            const form = document.getElementById('insight-form');
            const editorTitle = document.getElementById('editor-title');
            const formMethod = document.getElementById('form-method');
            const currentImageContainer = document.getElementById('current-image-container');
            const trixEditor = document.getElementById('isi-editor');

            // Reset form
            form.reset();
            currentImageContainer.classList.add('hidden');

            // Reset Trix editor
            const inputElement = document.getElementById('isi');
            inputElement.value = '';
            trixEditor.editor.loadHTML('');

            if (slug) {
                // Edit mode
                editorTitle.textContent = 'Edit Insight';
                formMethod.value = 'PUT';

                try {
                    const response = await fetch(`/api/insights/${slug}`);
                    if (!response.ok) throw new Error('Failed to fetch insight');

                    const insight = await response.json();

                    // Fill form fields
                    document.getElementById('form-insight-id').value = insight.id;
                    document.getElementById('judul').value = insight.judul;
                    document.getElementById('slug').value = insight.slug;
                    document.getElementById('slug').dataset.autogenerated = 'false';
                    document.getElementById('penulis').value = insight.penulis;

                    // Fill Trix editor with content
                    document.getElementById('isi').value = insight.isi;
                    trixEditor.editor.loadHTML(insight.isi);

                    // Format date for input
                    const publishDate = new Date(insight.TanggalTerbit);
                    const formattedDate = publishDate.toISOString().split('T')[0];
                    document.getElementById('TanggalTerbit').value = formattedDate;

                    // Set category
                    if (insight.category_id) {
                        document.getElementById('category_id').value = insight.category_id;
                    }

                    // Show current image if exists
                    if (insight.image_url) {
                        document.getElementById('current-image').src = insight.image_url;
                        currentImageContainer.classList.remove('hidden');
                    }

                } catch (error) {
                    console.error('Error fetching insight for editing:', error);
                    alert('Failed to load insight data for editing.');
                    return;
                }
            } else {
                // Create mode
                editorTitle.textContent = 'Create New Insight';
                formMethod.value = 'POST';
                document.getElementById('form-insight-id').value = '';
                document.getElementById('slug').dataset.autogenerated = 'true';

                // Set default date to today
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('TanggalTerbit').value = today;
            }

            // Show modal
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'active');

            // Focus on title field
            setTimeout(() => {
                document.getElementById('judul').focus();
            }, 100);
        }

        // Open Delete Confirmation Modal
        function openDeleteModal(slug, title) {
            const modal = document.getElementById('delete-modal');
            document.getElementById('delete-insight-title').textContent = title;
            currentInsightSlug = slug;

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'active');
        }

        // Create Views Chart
        async function createViewsChart(insightId) {
            if (viewsChart) {
                viewsChart.destroy();
            }

            const ctx = document.getElementById('views-chart').getContext('2d');

            // In a real application, you would fetch historical view data from an API
            // For this example, we'll use some simple data
            let labels = [];
            let data = [];

            const now = new Date();
            for (let i = 13; i >= 0; i--) {
                const date = new Date();
                date.setDate(now.getDate() - i);
                labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));

                // In a real app, this would be real data from your API
                // For now, we'll use zeros or small random numbers if no insight ID provided
                if (!insightId) {
                    data.push(0);
                } else {
                    data.push(Math.floor(Math.random() * 5));
                }
            }

            viewsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Views',
                        data: data,
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Create Device Chart
        function createDeviceChart(deviceData = []) {
            if (deviceChart) {
                deviceChart.destroy();
            }

            const ctx = document.getElementById('device-chart').getContext('2d');

            // Use actual data or default values
            let data = [];
            let labels = [];
            let colors = [];

            if (deviceData.length > 0) {
                // Use real data
                labels = deviceData.map(item => item.type);
                data = deviceData.map(item => item.percentage);

                // Generate colors
                const defaultColors = [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ];

                colors = deviceData.map((_, i) => defaultColors[i % defaultColors.length]);
            } else {
                // Default data
                labels = ['Desktop', 'Mobile', 'Tablet'];
                data = [0, 0, 0];
                colors = [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ];
            }

            deviceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }

        // Save Insight (Create or Update)
        async function saveInsight() {
            const form = document.getElementById('insight-form');
            const formData = new FormData(form);
            const method = document.getElementById('form-method').value;
            const slug = document.getElementById('slug').value;

            try {
                let url = '/api/insights';
                if (method === 'PUT') {
                    url = `/api/insights/${slug}`;
                }

                const response = await fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to save insight');
                }

                const result = await response.json();

                // Close modal and refresh list
                closeModal(document.getElementById('editor-modal'));
                fetchArticles();

                // Show success message
                alert(result.message || 'Insight saved successfully');

            } catch (error) {
                console.error('Error saving insight:', error);
                alert(`Error: ${error.message || 'Failed to save insight'}`);
            }
        }

        // Delete Insight
        async function deleteInsight(slug) {
            try {
                const response = await fetch(`/api/insights/${slug}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to delete insight');
                }

                // Close modal and refresh list
                closeModal(document.getElementById('delete-modal'));
                fetchArticles();

                // Show success message
                const result = await response.json();
                alert(result.message || 'Insight deleted successfully');

            } catch (error) {
                console.error('Error deleting insight:', error);
                alert(`Error: ${error.message || 'Failed to delete insight'}`);
            }
        }

        // Close modal
        function closeModal(modal) {
            modal.classList.remove('opacity-100', 'active');
            modal.classList.add('opacity-0', 'pointer-events-none');

            // Destroy charts to prevent memory leaks
            if (viewsChart && modal.id === 'stats-modal') {
                viewsChart.destroy();
                viewsChart = null;
            }

            if (deviceChart && modal.id === 'stats-modal') {
                deviceChart.destroy();
                deviceChart = null;
            }
        }

        // Format number with commas
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Generate slug from title
        function generateSlug(text) {
            return text
                .toString()
                .toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }
    });
</script>
</body>
</html>
