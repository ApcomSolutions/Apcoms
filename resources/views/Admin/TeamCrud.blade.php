{{-- resources/views/Admin/TeamCrud.blade.php --}}
<x-layout>
    <x-slot:title>Team Management Dashboard</x-slot:title>

    <!-- Header -->
    <header class="header-gradient text-white py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold">Team Manager</h1>
                <span class="ml-4 px-2 py-1 bg-white/20 rounded text-xs">Admin Panel</span>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center text-white hover:text-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.insights') }}" class="flex items-center text-white hover:text-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />
                        <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />
                    </svg>
                    Articles
                </a>
                <a href="{{ route('admin.categories') }}" class="flex items-center text-white hover:text-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                    </svg>
                    Categories
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-8">
        <div class="mb-8 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-800">Team Management</h2>
            <div class="flex space-x-2">
                <button id="refresh-btn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                    Refresh
                </button>
                <button id="new-team-btn" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    New Team Member
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="card p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="relative w-full md:w-1/3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="search" placeholder="Search team members..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="flex space-x-2">
                    <select id="status-filter" class="border border-gray-300 rounded-md px-4 py-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Team Members Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="team-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- Team members will be populated via JavaScript -->
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading team members...</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium" id="page-start">1</span> to <span class="font-medium" id="page-end">10</span> of <span class="font-medium" id="total-team-members">--</span> results
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

        <!-- Drag and Drop Order Section -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Arrange Team Order</h3>
            <p class="text-gray-600 mb-4">Drag and drop team members to change their display order on the website.</p>

            <div class="card p-4">
                <div id="sortable-team" class="space-y-2">
                    <!-- Sortable team members will be populated via JavaScript -->
                    <div class="bg-gray-100 rounded-md p-4 text-center text-gray-500">
                        Loading team members for sorting...
                    </div>
                </div>

                <div class="mt-4 text-right">
                    <button id="save-order-btn" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Save Order
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    <!-- Editor Modal -->
    <div id="team-editor-modal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50">
            <div class="modal-content py-4 text-left px-6">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800" id="editor-title">Add Team Member</h3>
                    <button class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18">
                            <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body - Form -->
                <div class="py-4">
                    <form id="team-form" class="space-y-4" enctype="multipart/form-data">
                        <input type="hidden" id="form-method" value="POST">
                        <input type="hidden" id="form-team-id" value="">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500" required>
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <input type="text" id="position" name="position" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500" required>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Profile Image</label>
                            <input type="file" id="image" name="image" class="block w-full py-2 px-3 focus:outline-none">
                            <div id="current-image-container" class="hidden mt-2">
                                <div class="flex items-center space-x-2">
                                    <img id="current-image" src="" alt="Current Image" class="h-12 w-12 object-cover rounded-full">
                                    <span class="text-xs text-gray-500">Current image</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end pt-2 border-t">
                    <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">Cancel</button>
                    <button id="save-team-btn" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Save</button>
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
                    <p class="text-gray-700">Are you sure you want to delete "<span id="delete-team-name" class="font-semibold"></span>"?</p>
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

    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        @include('Admin.Styles.TeamCrud')
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    @endpush

    @push('page_scripts')
        @vite(['resources/js/admin/teams.js'])
    @endpush

</x-layout>
