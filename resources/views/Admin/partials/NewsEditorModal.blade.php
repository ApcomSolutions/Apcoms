{{-- resources/views/Admin/Partials/NewsEditorModal.blade.php --}}
<div id="news-editor-modal"
     class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div class="modal-container bg-white w-11/12 md:max-w-4xl mx-auto rounded shadow-lg z-50 overflow-y-auto max-h-[90vh]">
        <div class="modal-content py-4 text-left px-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800" id="editor-title">Add News Article</h3>
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
                <form id="news-form" class="space-y-4" enctype="multipart/form-data">
                    <input type="hidden" id="form-method" value="POST">
                    <input type="hidden" id="form-news-id" value="">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="delete_image" name="delete_image" value="0">

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" id="title" name="title"
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500"
                               required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                            <input type="text" id="author" name="author"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500"
                                   required>
                        </div>

                        <div>
                            <label for="news_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select id="news_category_id" name="news_category_id"
                                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Select Category</option>
                                <!-- Categories will be populated via JavaScript -->
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="publish_date" class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
                            <input type="date" id="publish_date" name="publish_date"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500"
                                   required>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status" name="status" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                        <input type="file" id="image" name="image" accept="image/*"
                               class="block w-full py-2 px-3 focus:outline-none">
                        <div id="current-image-container" class="hidden mt-2">
                            <div class="flex items-center space-x-2">
                                <img id="current-image" src="" alt="Current Image"
                                     class="h-24 object-cover rounded">
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

                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <input id="content-input" type="hidden" name="content">
                        <trix-editor input="content-input" class="trix-editor"></trix-editor>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-2 border-t">
                <button class="modal-close px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">
                    Cancel
                </button>
                <button id="save-news-btn" class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
