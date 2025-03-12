{{-- resources/views/Admin/TeamCrud.blade.php --}}
<x-layout>
    <x-slot:title>Team Management Dashboard</x-slot:title>

    <!-- Header -->
    @include('Admin.Partials.AdminHeader', [
        'title' => 'Team Manager',
        'subtitle' => 'Admin Panel'
    ])

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

        <!-- Search and Filters -->
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

        <div class="modal-container bg-white w-full h-full mx-auto rounded-none shadow-lg z-50">
            <div class="modal-content py-4 text-left px-6 h-full flex flex-col">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800" id="editor-title">Add Team Member</h3>
                    <div class="flex items-center space-x-2">
                        <div class="hidden md:block text-xs text-gray-500">
                            Press <span class="key-hint">ESC</span> to close
                        </div>
                        <button class="modal-close cursor-pointer z-50">
                            <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg"
                                 width="24" height="24" viewBox="0 0 18 18">
                                <path
                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body - Form -->
                <div class="py-4 flex-grow overflow-auto">
                    <form id="team-form" class="space-y-4 md:max-w-3xl md:mx-auto" enctype="multipart/form-data">
                        <input type="hidden" id="form-method" value="POST">
                        <input type="hidden" id="form-team-id" value="">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                   required>
                        </div>

                        <div>
                            <label for="position"
                                   class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <input type="text" id="position" name="position"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                   required>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Profile Image</label>
                            <input type="file" id="image" name="image"
                                   class="block w-full py-2 px-3 focus:outline-none">
                            <div id="current-image-container" class="hidden mt-2">
                                <div class="relative inline-block">
                                    <img id="current-image" src="" alt="Current Image"
                                         class="h-24 object-cover rounded">
                                    <button type="button" class="absolute top-1 right-1 p-1 bg-gray-800 bg-opacity-50 rounded-full text-white hover:bg-opacity-70 focus:outline-none" id="current-image-fullscreen">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                                        </svg>
                                    </button>
                                    <span class="text-xs text-gray-500 ml-2">Current image</span>
                                </div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500">
                                <p>Press <span class="key-hint">F</span> while viewing an image to toggle fullscreen</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end pt-2 border-t">
                    <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">
                        Cancel
                    </button>
                    <button id="save-team-btn" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal"
         class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-full h-full md:w-1/3 md:h-auto mx-auto rounded-none md:rounded shadow-lg z-50">
            <div class="modal-content py-4 text-left px-6 h-full flex flex-col">
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
                <div class="py-4 flex-grow overflow-auto">
                    <div class="bg-red-50 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Are you sure you want to delete "<span id="delete-team-name"
                                                                           class="font-semibold"></span>"?
                                </p>
                                <p class="mt-2 text-xs text-red-700">
                                    This action cannot be undone. This will permanently delete the team member from the server.
                                </p>
                            </div>
                        </div>
                    </div>
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

    <!-- Fullscreen Preview Container for dynamic creation -->
    <div id="fullscreen-preview-container" class="hidden fixed inset-0 bg-black z-[9999] flex items-center justify-center">
        <div class="absolute top-4 right-4 flex space-x-2">
            <button class="p-2 bg-white rounded-full text-gray-800 hover:bg-gray-300 close-fullscreen">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="flex-grow flex items-center justify-center p-4">
            <img id="fullscreen-preview-image" src="" class="max-h-[90vh] max-w-[90vw] object-contain" />
        </div>
    </div>

    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet">
        @include('Admin.Styles.TeamCrud')
        <style>
            /* Fullscreen Modal Styles */
            .modal-fullscreen .modal-container {
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .modal-fullscreen .modal-content {
                height: 100% !important;
                display: flex !important;
                flex-direction: column !important;
            }

            .modal-fullscreen .py-4:not(.border-t):not(.border-b) {
                flex-grow: 1 !important;
                overflow: auto !important;
            }

            /* Responsive modals for mobile/desktop */
            @media (min-width: 768px) {
                .modal-container {
                    transition: all 0.3s ease;
                }

                /* Allow delete modal to be smaller on desktop */
                #delete-modal .modal-container {
                    width: 32rem;
                    height: auto;
                    border-radius: 0.5rem;
                }
            }

            /* Dropzone Custom Styling */
            .dropzone-container {
                min-height: 160px !important;
                border: 2px dashed #e5e7eb;
                background: #f9fafb;
                border-radius: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
                cursor: pointer;
                transition: border-color 0.3s ease;
            }

            .dropzone-container:hover {
                border-color: #d1d5db;
            }

            .dropzone-container.dz-drag-hover {
                border-color: #a78bfa;
                background: #ede9fe;
            }

            .dropzone-previews {
                margin-top: 1rem;
            }

            .dz-preview {
                position: relative;
                display: inline-block;
                margin: 0.5rem;
                border-radius: 0.5rem;
                overflow: hidden;
                background: #f3f4f6;
                border: 1px solid #e5e7eb;
                width: 120px;
            }

            .dz-image-wrapper {
                position: relative;
            }

            .dz-image {
                width: 120px;
                height: 120px;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .dz-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .dz-details {
                padding: 0.5rem;
                font-size: 0.75rem;
            }

            .dz-progress {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 80%;
                height: 6px;
                background: rgba(255, 255, 255, 0.8);
                border-radius: 3px;
                overflow: hidden;
            }

            .dz-upload {
                display: block;
                height: 100%;
                width: 0;
                background: #a78bfa;
                transition: width 0.3s ease;
            }

            .dz-success-mark, .dz-error-mark {
                position: absolute;
                top: 10px;
                right: 10px;
                display: none;
                background: rgba(255, 255, 255, 0.8);
                border-radius: 50%;
                width: 24px;
                height: 24px;
                text-align: center;
                line-height: 24px;
            }

            .dz-success-mark {
                background: rgba(34, 197, 94, 0.8);
                color: white;
            }

            .dz-error-mark {
                background: rgba(239, 68, 68, 0.8);
                color: white;
            }

            .dz-error-message {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(239, 68, 68, 0.8);
                color: white;
                padding: 0.25rem;
                font-size: 0.75rem;
                text-align: center;
            }

            .dz-toolbar {
                display: flex;
                justify-content: space-between;
                padding: 0.25rem;
                background: #f9fafb;
                border-top: 1px solid #e5e7eb;
            }

            .dz-remove, .dz-fullscreen {
                background: none;
                border: none;
                font-size: 0.75rem;
                cursor: pointer;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                transition: background-color 0.2s;
            }

            .dz-remove {
                color: #ef4444;
            }

            .dz-remove:hover {
                background-color: #fee2e2;
            }

            .dz-fullscreen {
                color: #3b82f6;
            }

            .dz-fullscreen:hover {
                background-color: #dbeafe;
            }

            /* Preview Fullscreen Styling */
            .preview-fullscreen {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                background-color: rgba(0, 0, 0, 0.9) !important;
                z-index: 9999 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .preview-fullscreen img {
                max-height: 90vh !important;
                max-width: 90vw !important;
                object-fit: contain !important;
            }

            /* Hide modal scrollbars when in fullscreen */
            body.fullscreen-active {
                overflow: hidden;
            }

            /* Keyboard shortcut styling */
            .key-hint {
                display: inline-block;
                background-color: #f3f4f6;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                padding: 2px 6px;
                margin: 0 2px;
                font-size: 0.75rem;
                font-family: monospace;
                color: #4b5563;
            }

            /* Header Styling */
            .header-gradient {
                background: linear-gradient(to right, #a78bfa, #7c3aed);
            }

            /* Card Styling */
            .card {
                background-color: white;
                border-radius: 0.5rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }

            /* Sortable Item Styling */
            .sortable-item {
                cursor: grab;
                transition: background-color 0.2s;
            }

            .sortable-item:hover {
                background-color: #f9fafb;
            }

            .sortable-ghost {
                background-color: #f3f4f6;
                opacity: 0.8;
            }

            /* Current image container styling */
            #current-image-container {
                position: relative;
                display: inline-block;
            }

            #current-image-container img {
                cursor: pointer;
            }

            #current-image-container .fullscreen-button {
                position: absolute;
                top: 4px;
                right: 4px;
                background: rgba(0, 0, 0, 0.5);
                color: white;
                border: none;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                opacity: 0;
                transition: opacity 0.2s ease;
            }

            #current-image-container:hover .fullscreen-button {
                opacity: 1;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    @endpush

    @push('page_scripts')
        <script>
            // Initialize keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // ESC key to close modals
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal:not(.opacity-0)').forEach(modal => {
                        const closeBtn = modal.querySelector('.modal-close');
                        if (closeBtn) closeBtn.click();
                    });

                    // Also close any fullscreen previews
                    const fullscreenContainer = document.getElementById('fullscreen-preview-container');
                    if (fullscreenContainer && !fullscreenContainer.classList.contains('hidden')) {
                        fullscreenContainer.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                }

                // F key to toggle fullscreen
                if (e.key === 'f' || e.key === 'F') {
                    // Check if we're editing an image
                    const editorModal = document.getElementById('team-editor-modal');
                    if (editorModal && !editorModal.classList.contains('opacity-0')) {
                        // Toggle fullscreen on current image if visible
                        const currentImageFullscreen = document.getElementById('current-image-fullscreen');
                        if (currentImageFullscreen && !document.getElementById('current-image-container').classList.contains('hidden')) {
                            currentImageFullscreen.click();
                        }
                    }
                }
            });

            // Convert modals to fullscreen
            function setupFullscreenModals() {
                // Convert existing modals to fullscreen
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    // Add fullscreen class
                    modal.classList.add('modal-fullscreen');

                    // Make the modal container full width and height
                    const modalContainer = modal.querySelector('.modal-container');
                    if (modalContainer) {
                        modalContainer.classList.remove('w-11/12', 'md:max-w-md', 'md:max-w-3xl');
                        modalContainer.classList.add('w-full', 'h-full', 'max-w-none', 'mx-0', 'rounded-none');
                    }

                    // Adjust content container to take full height
                    const modalContent = modal.querySelector('.modal-content');
                    if (modalContent) {
                        modalContent.classList.add('h-full', 'flex', 'flex-col');

                        // Adjust the body to take up available space
                        const modalBody = modalContent.querySelector('.py-4:not(.border-t):not(.border-b)');
                        if (modalBody) {
                            modalBody.classList.add('flex-grow', 'overflow-auto');
                        }
                    }
                });
            }

            // Add fullscreen functionality to current image
            document.addEventListener('DOMContentLoaded', function() {
                setupFullscreenModals();

                // Add fullscreen functionality to current image
                const currentImageBtn = document.getElementById('current-image-fullscreen');
                if (currentImageBtn) {
                    currentImageBtn.addEventListener('click', function() {
                        const currentImage = document.getElementById('current-image');
                        if (currentImage) {
                            const fullscreenContainer = document.getElementById('fullscreen-preview-container');
                            const fullscreenImage = document.getElementById('fullscreen-preview-image');

                            fullscreenImage.src = currentImage.src;
                            fullscreenContainer.classList.remove('hidden');
                            document.body.style.overflow = 'hidden';

                            // Add close handler
                            const closeBtn = fullscreenContainer.querySelector('.close-fullscreen');
                            if (closeBtn) {
                                closeBtn.addEventListener('click', function() {
                                    fullscreenContainer.classList.add('hidden');
                                    document.body.style.overflow = '';
                                });
                            }
                        }
                    });
                }
            });
        </script>
        <script src="{{ asset('js/admin/teams.js') }}"></script>
    @endpush
</x-layout>
