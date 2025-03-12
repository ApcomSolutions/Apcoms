{{-- resources/views/Admin/Components/EditorModal.blade.php --}}
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
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="delete_image" name="delete_image" value="0">

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
                            <input type="file" id="image" name="image" accept="image/*" class="block py-2 px-3">
                            <div id="current-image-container" class="hidden">
                                <div class="flex items-center space-x-2">
                                    <img id="current-image" src="" alt="Current Image" class="h-12 w-auto object-cover rounded">
                                    <span class="text-xs text-gray-500">Current image</span>
                                    <button type="button" id="delete-image-btn" class="text-red-600 hover:text-red-800 text-sm flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Delete
                                    </button>
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
