{{-- resources/views/Admin/Components/CategoryModals.blade.php - Updated --}}
<!-- Editor Modal -->
<div id="category-editor-modal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50">
        <div class="modal-content py-4 text-left px-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800" id="editor-title">Create New Category</h3>
                <button class="modal-close cursor-pointer z-50">
                    <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body - Form -->
            <div class="py-4">
                <form id="category-form" class="space-y-4">
                    <input type="hidden" id="form-method" value="POST">
                    <input type="hidden" id="form-category-id" value="">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" id="name" name="name" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" id="slug" name="slug" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    <div id="parent-category-section">
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                        <div class="flex items-center">
                            <select id="parent_id" name="parent_id" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">None (Top-Level Category)</option>
                                <!-- Parent categories will be populated via JavaScript -->
                            </select>
                            <span id="parent-category-display" class="ml-2 text-sm text-gray-600"></span>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                            <input type="number" id="order" name="order" value="0" min="0" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex items-center py-4">
                            <input type="checkbox" id="is_active" name="is_active" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" checked>
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-2 border-t">
                <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">Cancel</button>
                <button id="save-category-btn" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Parent Category Selector Modal -->
<div id="parent-selector-modal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50">
        <div class="modal-content py-4 text-left px-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">Select Parent Category</h3>
                <button class="modal-close cursor-pointer z-50">
                    <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body - List of parent categories -->
            <div class="py-4">
                <p class="text-sm text-gray-600 mb-3">Select a parent category to add a subcategory to:</p>
                <div id="parent-category-list" class="max-h-60 overflow-y-auto border rounded-md">
                    <!-- Parent categories will be populated via JavaScript -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-2 border-t">
                <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</button>
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
                <p class="text-gray-700">Are you sure you want to delete "<span id="delete-category-name" class="font-semibold"></span>"?</p>
                <p class="text-red-600 text-sm mt-2">This action cannot be undone.</p>
                <div id="category-has-insights" class="hidden mt-3 p-3 bg-yellow-100 text-yellow-700 rounded-md">
                    <p class="font-semibold">Warning:</p>
                    <p>This category has articles associated with it. Deleting it may cause those articles to lose their categorization.</p>
                </div>
                <div id="category-has-children" class="hidden mt-3 p-3 bg-yellow-100 text-yellow-700 rounded-md">
                    <p class="font-semibold">Warning:</p>
                    <p>This category has subcategories. If deleted, subcategories will become top-level categories.</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-2 border-t">
                <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">Cancel</button>
                <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>
</div>
