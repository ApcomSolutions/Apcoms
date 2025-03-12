{{-- resources/views/Admin/Partials/NewsPreviewModal.blade.php --}}
<div id="preview-modal"
     class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div class="modal-container bg-white w-11/12 md:max-w-3xl mx-auto rounded shadow-lg z-50 overflow-y-auto max-h-[90vh]">
        <div class="modal-content py-4 text-left px-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800" id="preview-title">News Preview</h3>
                <button class="modal-close cursor-pointer z-50">
                    <svg class="fill-current text-gray-500 hover:text-gray-800" xmlns="http://www.w3.org/2000/svg"
                         width="24" height="24" viewBox="0 0 18 18">
                        <path
                            d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body - News Preview -->
            <div class="py-4">
                <div class="mb-4">
                    <img id="preview-image" src="" alt="Preview"
                         class="w-full h-auto max-h-80 object-cover rounded-lg">
                </div>
                <div>
                    <h4 class="font-medium text-2xl mb-1" id="preview-news-title">News Title</h4>
                    <div class="flex items-center text-gray-500 text-sm mb-4">
                        <span class="flex items-center mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span id="preview-author">Author Name</span>
                        </span>
                        <span class="flex items-center mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span id="preview-date">Publication Date</span>
                        </span>
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span id="preview-category">Category</span>
                        </span>
                    </div>
                    <div class="mt-4 prose max-w-none" id="preview-content">
                        Content will appear here.
                    </div>
                    <div class="mt-4 flex space-x-2">
                        <span id="preview-status-badge"
                              class="px-2 py-1 text-xs rounded-full">
                            Status
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
