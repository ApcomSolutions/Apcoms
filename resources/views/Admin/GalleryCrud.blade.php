{{-- resources/views/Admin/GalleryCrud.blade.php --}}
<x-layout>
    <x-slot:title>Gallery Management Dashboard</x-slot:title>

    <!-- Header -->
    <header class="header-gradient text-white py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold">Gallery Manager</h1>
                <span class="ml-4 px-2 py-1 bg-white/20 rounded text-xs">Admin Panel</span>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center text-white hover:text-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.insights') }}" class="flex items-center text-white hover:text-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z"
                              clip-rule="evenodd"/>
                        <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>
                    </svg>
                    Articles
                </a>
                <a href="{{ route('admin.teams') }}" class="flex items-center text-white hover:text-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path
                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                    </svg>
                    Team
                </a>
                <a href="{{ route('admin.clients') }}" class="flex items-center text-white hover:text-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Clients
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-8">
        <div class="mb-8 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-800">Gallery Management</h2>
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
                <button id="new-image-btn"
                        class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                              clip-rule="evenodd"/>
                    </svg>
                    Add Image
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
                    <input type="text" id="search" placeholder="Search gallery..."
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-pink-500 focus:border-pink-500">
                </div>
                <div class="flex space-x-2">
                    <select id="carousel-filter"
                            class="border border-gray-300 rounded-md px-4 py-2 focus:ring-pink-500 focus:border-pink-500">
                        <option value="">All Images</option>
                        <option value="carousel">Carousel Only</option>
                        <option value="non-carousel">Non-Carousel</option>
                    </select>
                    <select id="status-filter"
                            class="border border-gray-300 rounded-md px-4 py-2 focus:ring-pink-500 focus:border-pink-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Gallery Grid View -->
        <div class="mb-8">
            <div id="gallery-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Gallery items will be populated via JavaScript -->
                <div class="flex justify-center items-center h-64 bg-gray-100 rounded-lg animate-pulse">
                    <span class="text-gray-500">Loading images...</span>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium" id="page-start">1</span> to <span class="font-medium"
                                                                                            id="page-end">10</span> of
                        <span class="font-medium" id="total-images">--</span> results
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
        </div>

        <!-- Drag and Drop Order Section -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Arrange Gallery Order</h3>
            <p class="text-gray-600 mb-4">Drag and drop images to change their display order on the website.</p>

            <div class="card p-4">
                <div id="sortable-gallery" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Sortable gallery will be populated via JavaScript -->
                    <div class="flex justify-center items-center h-24 bg-gray-100 rounded-lg animate-pulse">
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

    <!-- Modals -->
    <!-- Editor Modal -->
    <div id="gallery-editor-modal"
         class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50">
            <div class="modal-content py-4 text-left px-6">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800" id="editor-title">Add Gallery Image</h3>
                    <button class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 18 18">
                            <path
                                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body - Form -->
                <div class="py-4">
                    <form id="gallery-form" class="space-y-4" enctype="multipart/form-data">
                        <input type="hidden" id="form-method" value="POST">
                        <input type="hidden" id="form-image-id" value="">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" id="title" name="title"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500"
                                   required>
                        </div>

                        <div>
                            <label for="description"
                                   class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500"></textarea>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                            <input type="file" id="image" name="image"
                                   class="block w-full py-2 px-3 focus:outline-none">
                            <div id="current-image-container" class="hidden mt-2">
                                <div class="flex items-center">
                                    <img id="current-image" src="" alt="Current Image"
                                         class="h-24 object-cover rounded">
                                    <span class="text-xs text-gray-500 ml-2">Current image</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_carousel" name="is_carousel"
                                   class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="is_carousel" class="ml-2 block text-sm text-gray-700">Show in Carousel</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active"
                                   class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end pt-2 border-t">
                    <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">
                        Cancel
                    </button>
                    <button id="save-image-btn" class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="preview-modal"
         class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-3xl mx-auto rounded shadow-lg z-50">
            <div class="modal-content py-4 text-left px-6">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800" id="preview-title">Image Preview</h3>
                    <button class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 18 18">
                            <path
                                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body - Image Preview -->
                <div class="py-4">
                    <div class="mb-4">
                        <img id="preview-image" src="" alt="Preview"
                             class="w-full h-auto max-h-96 object-contain mx-auto">
                    </div>
                    <div>
                        <h4 class="font-medium text-lg" id="preview-image-title">Image Title</h4>
                        <p class="text-gray-600 mt-2" id="preview-image-description">Description will appear here.</p>
                        <div class="mt-4 flex space-x-2">
                            <span id="preview-carousel-badge"
                                  class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                In Carousel
                            </span>
                            <span id="preview-status-badge"
                                  class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end pt-2 border-t space-x-2">
                    <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Close
                    </button>
                    <button id="preview-edit-btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Edit
                    </button>
                    <button id="preview-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal"
         class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50">
            <div class="modal-content py-4 text-left px-6">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Confirm Deletion</h3>
                    <button class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 18 18">
                            <path
                                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="py-4">
                    <p class="text-gray-700">Are you sure you want to delete "<span id="delete-image-title"
                                                                                    class="font-semibold"></span>"?</p>
                    <p class="text-red-600 text-sm mt-2">This action cannot be undone.</p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end pt-2 border-t">
                    <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">
                        Cancel
                    </button>
                    <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        @include('Admin.Styles.GalleryCrud')
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    @endpush

    @push('page_scripts')
        @vite(['resources/js/admin/gallery.js'])
    @endpush
</x-layout>
