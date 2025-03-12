{{-- resources/views/Admin/Partials/NewsCategoryEditorModal.blade.php --}}
<div id="category-editor-modal"
     class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content py-4 text-left px-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800" id="editor-title">Add Category</h3>
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
                <form id="category-form" class="space-y-4">
                    <input type="hidden" id="form-method" value="POST">
                    <input type="hidden" id="form-category-id" value="">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" id="name" name="name"
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500"
                               required>
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <div class="relative rounded-md shadow-sm">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">/</span>
                            <input type="text" id="slug" name="slug"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 pl-7 pr-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500"
                                   placeholder="auto-generated-from-name">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from name</p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea id="description" name="description" rows="3"
                                  class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500"></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-2 border-t">
                <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">
                    Cancel
                </button>
                <button id="save-category-btn" class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
