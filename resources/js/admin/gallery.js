// Modified gallery.js with full-screen modal and dropzone preview

document.addEventListener('DOMContentLoaded', function() {
    // State
    let images = [];
    let currentPage = 1;
    let itemsPerPage = 12;
    let totalPages = 1;
    let currentImageId = null;
    let sortableGallery = null;
    let galleryDropzone = null;

    // CSRF token setup for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize
    fetchGallery();
    initSortable();
    setupFullscreenModals();

    // Initialize Dropzone when the modal opens
    document.getElementById('new-image-btn').addEventListener('click', () => {
        openEditorModal();

        // Initialize dropzone after modal is shown
        setTimeout(() => {
            initGalleryDropzone();
        }, 200);
    });

    // Event Listeners
    document.getElementById('refresh-btn').addEventListener('click', fetchGallery);
    document.getElementById('search').addEventListener('input', filterGallery);
    document.getElementById('status-filter').addEventListener('change', filterGallery);
    document.getElementById('carousel-filter').addEventListener('change', filterGallery);
    document.getElementById('prev-page').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderGallery();
        }
    });
    document.getElementById('next-page').addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            renderGallery();
        }
    });

    // Save Image Button
    document.getElementById('save-image-btn').addEventListener('click', saveImage);

    // Save Order Button
    document.getElementById('save-order-btn').addEventListener('click', saveOrder);

    // Delete confirmation
    document.getElementById('confirm-delete-btn').addEventListener('click', () => {
        if (currentImageId) {
            deleteImage(currentImageId);
        }
    });

    // Preview edit button
    document.getElementById('preview-edit-btn').addEventListener('click', () => {
        // Close preview modal
        closeModal(document.getElementById('preview-modal'));

        // Open editor modal with current image
        if (currentImageId) {
            openEditorModal(currentImageId);

            // Initialize dropzone after modal is shown
            setTimeout(() => {
                initGalleryDropzone();
            }, 200);
        }
    });

    // Preview delete button
    document.getElementById('preview-delete-btn').addEventListener('click', () => {
        // Close preview modal
        closeModal(document.getElementById('preview-modal'));

        // Open delete modal
        if (currentImageId) {
            const image = images.find(img => img.id == currentImageId);
            if (image) {
                openDeleteModal(currentImageId, image.title);
            }
        }
    });

    // Setup form cancellation to clean up temp files
    setupFormCancellation(
        'gallery-form',
        '.modal-close',
        '#gallery-editor-modal'
    );

    // Setup fullscreen modals functionality
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

        // Setup fullscreen toggle for preview modal
        const fullscreenToggleBtn = document.createElement('button');
        fullscreenToggleBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
            </svg>
        `;
        fullscreenToggleBtn.className = 'p-2 bg-gray-200 rounded-full text-gray-700 hover:bg-gray-300 focus:outline-none absolute top-4 right-16 z-50 fullscreen-toggle';
        fullscreenToggleBtn.title = 'Toggle fullscreen';

        const previewModal = document.getElementById('preview-modal');
        if (previewModal) {
            previewModal.appendChild(fullscreenToggleBtn);

            // Add fullscreen preview toggle functionality
            fullscreenToggleBtn.addEventListener('click', () => {
                const previewContainer = document.getElementById('preview-image-container');
                previewContainer.classList.toggle('preview-fullscreen');

                if (previewContainer.classList.contains('preview-fullscreen')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        }

        // Add keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // ESC key to close modals
            if (e.key === 'Escape') {
                modals.forEach(closeModal);

                // Also exit fullscreen preview mode if active
                const previewContainer = document.getElementById('preview-image-container');
                if (previewContainer && previewContainer.classList.contains('preview-fullscreen')) {
                    previewContainer.classList.remove('preview-fullscreen');
                    document.body.style.overflow = '';
                }
            }

            // F key to toggle fullscreen
            if (e.key === 'f' && previewModal && !previewModal.classList.contains('opacity-0')) {
                fullscreenToggleBtn.click();
            }
        });
    }

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

    // Initialize Dropzone for Gallery Images with fullscreen preview
    function initGalleryDropzone() {
        // Configuration for dropzone
        const dropzoneConfig = {
            url: '/api/temp-uploads',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            paramName: 'temp_image',
            maxFilesize: 5, // MB
            acceptedFiles: 'image/jpeg,image/png,image/webp,image/gif',
            addRemoveLinks: true,
            createImageThumbnails: true,
            thumbnailWidth: 200,
            thumbnailHeight: 200,
            previewTemplate: `
                <div class="dz-preview dz-file-preview">
                    <div class="dz-image-wrapper">
                        <div class="dz-image">
                            <img data-dz-thumbnail />
                        </div>
                        <div class="dz-details">
                            <div class="dz-filename"><span data-dz-name></span></div>
                            <div class="dz-size"><span data-dz-size></span></div>
                        </div>
                        <div class="dz-success-mark"><span>✓</span></div>
                        <div class="dz-error-mark"><span>✗</span></div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    </div>
                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                    <div class="dz-toolbar">
                        <button class="dz-remove" data-dz-remove>Remove</button>
                        <button class="dz-fullscreen">Fullscreen</button>
                    </div>
                </div>
            `,
            dictDefaultMessage: `
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-1">Drag images here or click to upload</p>
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, WebP, GIF up to 5MB</p>
                </div>
            `
        };

        // Find the form and create the dropzone element
        const form = document.getElementById('gallery-form');
        if (!form) return;

        // Find file input
        const fileInput = form.querySelector('input[type="file"]');
        if (!fileInput) return;

        // Hide original file input
        fileInput.style.display = 'none';

        // Clear existing dropzone container if any
        const existingDropzone = form.querySelector('.dropzone-container');
        if (existingDropzone) {
            existingDropzone.remove();
        }

        const existingPreviews = form.querySelector('.dropzone-previews');
        if (existingPreviews) {
            existingPreviews.remove();
        }

        // Create dropzone container
        const dropzoneContainer = document.createElement('div');
        dropzoneContainer.className = 'dropzone-container mt-1';
        fileInput.parentNode.appendChild(dropzoneContainer);

        // Create preview container
        const previewContainer = document.createElement('div');
        previewContainer.className = 'dropzone-previews mt-2 flex flex-wrap gap-2';
        fileInput.parentNode.appendChild(previewContainer);

        // Initialize Dropzone
        if (galleryDropzone) {
            galleryDropzone.destroy();
        }

        galleryDropzone = new Dropzone(dropzoneContainer, dropzoneConfig);

        // Setup events for the dropzone
        galleryDropzone.on("addedfile", function(file) {
            // Remove all other files as we only want one file
            if (this.files.length > 1) {
                this.removeFile(this.files[0]);
            }

            // Add "Preview" watermark
            const watermark = document.createElement('div');
            watermark.className = 'absolute top-1 right-1 bg-pink-500 text-white text-xs px-1 py-0.5 rounded';
            watermark.textContent = 'Preview';
            file.previewElement.appendChild(watermark);

            // Add fullscreen functionality to the preview
            const fullscreenBtn = file.previewElement.querySelector('.dz-fullscreen');
            if (fullscreenBtn) {
                fullscreenBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Create fullscreen preview container if doesn't exist
                    let fullscreenPreview = document.getElementById('dropzone-fullscreen-preview');
                    if (!fullscreenPreview) {
                        fullscreenPreview = document.createElement('div');
                        fullscreenPreview.id = 'dropzone-fullscreen-preview';
                        fullscreenPreview.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center';
                        fullscreenPreview.innerHTML = `
                            <div class="relative w-full h-full flex flex-col">
                                <div class="absolute top-4 right-4 flex space-x-2">
                                    <button class="p-2 bg-white rounded-full text-gray-800 hover:bg-gray-300 close-fullscreen">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-grow flex items-center justify-center p-4">
                                    <img src="${file.dataURL || URL.createObjectURL(file)}" class="max-h-full max-w-full object-contain" />
                                </div>
                                <div class="bg-white bg-opacity-10 p-4">
                                    <div class="text-white text-lg">${file.name}</div>
                                    <div class="text-gray-300 text-sm">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(fullscreenPreview);

                        // Add event listener to close button
                        fullscreenPreview.querySelector('.close-fullscreen').addEventListener('click', function() {
                            fullscreenPreview.remove();
                            document.body.style.overflow = '';
                        });

                        // Also close on ESC key
                        document.addEventListener('keydown', function closeOnEsc(e) {
                            if (e.key === 'Escape') {
                                if (fullscreenPreview.parentNode) {
                                    fullscreenPreview.remove();
                                    document.body.style.overflow = '';
                                }
                                document.removeEventListener('keydown', closeOnEsc);
                            }
                        });

                        // Prevent scrolling while fullscreen is active
                        document.body.style.overflow = 'hidden';
                    }
                });
            }

            // Hide current image container if exists
            const currentImageContainer = document.getElementById('current-image-container');
            if (currentImageContainer) {
                currentImageContainer.classList.add('hidden');
            }
        });

        // Event when file is successfully uploaded
        galleryDropzone.on("success", function(file, response) {
            // Add hidden field to store uploaded file path
            const existingHiddenInput = form.querySelector('[name="temp_image_path"]');
            if (existingHiddenInput) {
                existingHiddenInput.value = response.path || '';
            } else {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'temp_image_path';
                hiddenInput.value = response.path || '';
                form.appendChild(hiddenInput);
            }

            // Set value for image field (for compatibility)
            fileInput.value = '';

            console.log('File uploaded successfully:', response);
        });

        // Event when file is removed
        galleryDropzone.on("removedfile", function(file) {
            // Remove hidden field for path
            const hiddenInput = form.querySelector('[name="temp_image_path"]');
            if (hiddenInput) {
                // Send request to delete temp file if exists
                if (hiddenInput.value) {
                    fetch('/api/temp-uploads/cancel', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ path: hiddenInput.value })
                    }).catch(error => {
                        console.error('Error cancelling upload:', error);
                    });
                }

                form.removeChild(hiddenInput);
            }

            // Show current image container if exists
            const currentImageContainer = document.getElementById('current-image-container');
            if (currentImageContainer) {
                currentImageContainer.classList.remove('hidden');
            }
        });

        // Event when error occurs
        galleryDropzone.on("error", function(file, errorMessage) {
            console.error("Upload error:", errorMessage);
            // Display user-friendly error message
            const errorElement = document.createElement('div');
            errorElement.className = 'bg-red-100 text-red-700 p-2 mt-2 rounded text-sm';
            errorElement.textContent = typeof errorMessage === 'string'
                ? errorMessage
                : 'An error occurred while uploading the file.';

            file.previewElement.appendChild(errorElement);
        });

        return galleryDropzone;
    }

    // Initialize Sortable
    function initSortable() {
        const sortableContainer = document.getElementById('sortable-gallery');
        if (sortableContainer) {
            sortableGallery = new Sortable(sortableContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    // Enable save button after sorting
                    document.getElementById('save-order-btn').disabled = false;
                }
            });
        }
    }

    // Fetch Gallery from API
    async function fetchGallery() {
        try {
            const galleryGrid = document.getElementById('gallery-grid');
            if (galleryGrid) {
                galleryGrid.innerHTML = `
                    <div class="col-span-full flex justify-center items-center h-64">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-pink-700"></div>
                    </div>
                `;
            }

            const response = await fetch('/api/gallery');
            if (!response.ok) throw new Error('Failed to fetch gallery');

            images = await response.json();
            renderGallery();
            renderSortableGallery();
        } catch (error) {
            console.error('Error fetching gallery:', error);
            if (galleryGrid) {
                galleryGrid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-red-800">Error loading gallery</h3>
                        <p class="mt-1 text-gray-500">Please try again or refresh the page.</p>
                    </div>
                `;
            }
        }
    }

    // Filter Gallery
    function filterGallery() {
        currentPage = 1;
        renderGallery();
    }

    // Render Gallery Grid
    function renderGallery() {
        const galleryGrid = document.getElementById('gallery-grid');
        if (!galleryGrid) return;

        galleryGrid.innerHTML = '';

        const searchInput = document.getElementById('search');
        const statusFilter = document.getElementById('status-filter');
        const carouselFilter = document.getElementById('carousel-filter');

        // Apply filters
        const searchQuery = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const carouselValue = carouselFilter.value;

        let filteredImages = images.filter(image => {
            const titleMatch = image.title.toLowerCase().includes(searchQuery);
            const descMatch = image.description ? image.description.toLowerCase().includes(searchQuery) : false;

            const statusMatch = !statusValue ||
                (statusValue === 'active' && image.is_active) ||
                (statusValue === 'inactive' && !image.is_active);

            const carouselMatch = !carouselValue ||
                (carouselValue === 'carousel' && image.is_carousel) ||
                (carouselValue === 'non-carousel' && !image.is_carousel);

            return (titleMatch || descMatch) && statusMatch && carouselMatch;
        });

        // Sort by order
        filteredImages.sort((a, b) => a.order - b.order);

        // Calculate pagination
        totalPages = Math.ceil(filteredImages.length / itemsPerPage);
        if (totalPages === 0) totalPages = 1;

        // Update pagination UI
        document.getElementById('total-images').textContent = filteredImages.length;
        document.getElementById('page-start').textContent = filteredImages.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        document.getElementById('page-end').textContent = Math.min(currentPage * itemsPerPage, filteredImages.length);

        // Render pagination
        renderPagination();

        // Slice for current page
        const pageImages = filteredImages.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

        if (pageImages.length === 0) {
            galleryGrid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No images found</h3>
                    <p class="mt-1 text-gray-500">Try adjusting your search or filter criteria.</p>
                </div>
            `;
            return;
        }

        // Render gallery items
        pageImages.forEach(image => {
            const galleryItem = document.createElement('div');
            galleryItem.className = 'gallery-item bg-white rounded-lg shadow-md overflow-hidden';
            galleryItem.setAttribute('data-id', image.id);

            galleryItem.innerHTML = `
                <div class="relative h-48">
                    <img src="${image.image_url}" alt="${image.title}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 overlay flex flex-col justify-end p-4">
                        <div class="text-white font-medium">${image.title}</div>
                        <div class="flex items-center space-x-2 mt-1">
                            ${image.is_carousel ?
                '<span class="px-2 py-1 text-xs rounded-full bg-indigo-500 bg-opacity-80 text-white">Carousel</span>' : ''}
                            <span class="px-2 py-1 text-xs rounded-full ${image.is_active ?
                'bg-green-500 bg-opacity-80 text-white' :
                'bg-red-500 bg-opacity-80 text-white'}">
                                ${image.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 truncate">${image.description || 'No description'}</p>
                    </div>
                    <div class="flex space-x-1">
                        <button class="p-1 text-blue-600 hover:text-blue-900 view-image" data-id="${image.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button class="p-1 text-red-600 hover:text-red-900 delete-image" data-id="${image.id}" data-title="${image.title}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            galleryGrid.appendChild(galleryItem);
        });

        // Add event listeners to buttons
        document.querySelectorAll('.view-image').forEach(button => {
            button.addEventListener('click', () => {
                const imageId = button.getAttribute('data-id');
                openPreviewModal(imageId);
            });
        });

        document.querySelectorAll('.delete-image').forEach(button => {
            button.addEventListener('click', () => {
                const imageId = button.getAttribute('data-id');
                const imageTitle = button.getAttribute('data-title');
                openDeleteModal(imageId, imageTitle);
            });
        });

        // Make entire gallery item clickable to view the image
        document.querySelectorAll('.gallery-item').forEach(item => {
            item.addEventListener('click', (e) => {
                // Only open preview if not clicking on a button
                if (!e.target.closest('button')) {
                    const imageId = item.getAttribute('data-id');
                    openPreviewModal(imageId);
                }
            });
        });
    }

    // Render pagination
    function renderPagination() {
        const paginationContainer = document.getElementById('pagination-numbers');
        if (!paginationContainer) return;

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
            button.className = `relative inline-flex items-center px-4 py-2 border ${currentPage === pageNum ? 'border-pink-500 bg-pink-50 text-pink-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'}`;
            button.textContent = pageNum;

            button.addEventListener('click', () => {
                currentPage = pageNum;
                renderGallery();
            });

            paginationContainer.appendChild(button);
        }
    }

    // Render Sortable Gallery
    function renderSortableGallery() {
        const sortableContainer = document.getElementById('sortable-gallery');
        if (!sortableContainer) return;

        sortableContainer.innerHTML = '';

        // Sort gallery by order
        const sortedImages = [...images].sort((a, b) => a.order - b.order);

        if (sortedImages.length === 0) {
            sortableContainer.innerHTML = `
                <div class="col-span-full flex justify-center items-center h-24">
                    <span class="text-gray-500">No gallery images found</span>
                </div>
            `;
            return;
        }

        sortedImages.forEach(image => {
            const itemElement = document.createElement('div');
            itemElement.className = 'sortable-item relative bg-white rounded-lg shadow-sm overflow-hidden';
            itemElement.setAttribute('data-id', image.id);

            itemElement.innerHTML = `
                <div class="flex items-center">
                    <div class="h-24 w-24 flex-shrink-0">
                        <img src="${image.image_url}" alt="${image.title}" class="h-full w-full object-cover">
                    </div>
                    <div class="px-3 py-2 flex-grow">
                        <div class="text-sm font-medium text-gray-900">${image.title}</div>
                        <div class="flex items-center space-x-2 mt-1">
                            ${image.is_carousel ?
                '<span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-800">Carousel</span>' : ''}
                            <span class="px-2 py-0.5 text-xs rounded-full ${image.is_active ?
                'bg-green-100 text-green-800' :
                'bg-red-100 text-red-800'}">
                                ${image.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                    <div class="p-2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
                        </svg>
                    </div>
                </div>
            `;

            sortableContainer.appendChild(itemElement);
        });

        // Disable save button initially
        document.getElementById('save-order-btn').disabled = true;
    }

    // Open Preview Modal
    // Function to open Preview Modal with a single fullscreen button
    function openPreviewModal(imageId) {
        const modal = document.getElementById('preview-modal');

        try {
            const image = images.find(img => img.id == imageId);
            if (!image) {
                throw new Error('Image not found');
            }

            // Store current image ID
            currentImageId = imageId;

            // Fill preview content
            document.getElementById('preview-title').textContent = 'Image Preview: ' + image.title;

            // Update preview image
            const previewImage = document.getElementById('preview-image');
            previewImage.src = image.image_url;
            previewImage.alt = image.title;

            // Add hover class to container
            const previewContainer = document.getElementById('preview-image-container');
            previewContainer.classList.add('group');

            // Make sure fullscreen button is properly set up
            const fullscreenBtn = document.getElementById('preview-fullscreen-toggle');
            if (fullscreenBtn) {
                // Remove existing event listeners to prevent duplication
                const newBtn = fullscreenBtn.cloneNode(true);
                fullscreenBtn.parentNode.replaceChild(newBtn, fullscreenBtn);

                // Add new event listener
                newBtn.addEventListener('click', function() {
                    toggleFullscreenPreview(image.image_url, image.title);
                });
            }

            // Update image details
            document.getElementById('preview-image-title').textContent = image.title;
            document.getElementById('preview-image-description').textContent = image.description || 'No description provided';

            // Update badges
            const carouselBadge = document.getElementById('preview-carousel-badge');
            if (image.is_carousel) {
                carouselBadge.classList.remove('hidden');
                carouselBadge.textContent = 'In Carousel';
                carouselBadge.className = 'px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800';
            } else {
                carouselBadge.classList.add('hidden');
            }

            const statusBadge = document.getElementById('preview-status-badge');
            if (image.is_active) {
                statusBadge.textContent = 'Active';
                statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800';
            } else {
                statusBadge.textContent = 'Inactive';
                statusBadge.className = 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800';
            }

            // Show modal
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');

        } catch (error) {
            console.error('Error opening preview:', error);
            window.ErrorHandler.showError(`Error: ${error.message || 'Failed to load image preview'}`);
        }
    }


    // Save Gallery Order
    async function saveOrder() {
        const saveButton = document.getElementById('save-order-btn');
        saveButton.disabled = true;

        try {
            const sortableItems = document.querySelectorAll('.sortable-item');
            const orderedIds = Array.from(sortableItems).map(item => item.getAttribute('data-id'));

            const response = await fetch('/api/gallery/update-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ ordered_ids: orderedIds })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to update order');
            }

            // Refetch to get updated order
            fetchGallery();
            window.ErrorHandler.showSuccess('Gallery order updated successfully');
        } catch (error) {
            console.error('Error saving gallery order:', error);
            window.ErrorHandler.showError(`Error: ${error.message || 'Failed to update gallery order'}`);
            saveButton.disabled = false;
        }
    }

    // Open Delete Confirmation Modal
    function openDeleteModal(imageId, imageTitle) {
        const modal = document.getElementById('delete-modal');
        const titleElement = document.getElementById('delete-image-title');

        // Make sure the delete modal is also properly fullscreen
        ensureModalFullscreen(modal);

        titleElement.textContent = imageTitle;
        currentImageId = imageId;

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
    }

    // Ensure a modal is properly set up for fullscreen
    function ensureModalFullscreen(modal) {
        if (!modal) return;

        // Add fullscreen class
        modal.classList.add('modal-fullscreen');

        // Make the modal container full width and height
        const modalContainer = modal.querySelector('.modal-container');
        if (modalContainer && !modalContainer.classList.contains('w-full')) {
            modalContainer.classList.remove('w-11/12', 'md:max-w-md', 'md:max-w-3xl');
            modalContainer.classList.add('w-full', 'h-full', 'max-w-none', 'mx-0', 'rounded-none');

            // Adjust content container to take full height
            const modalContent = modalContainer.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.add('h-full', 'flex', 'flex-col');

                // Adjust the body to take up available space
                const modalBody = modalContent.querySelector('.py-4:not(.border-t):not(.border-b)');
                if (modalBody) {
                    modalBody.classList.add('flex-grow', 'overflow-auto');
                }
            }
        }
    }

    // Open Editor Modal with fullscreen support
    async function openEditorModal(imageId = null) {
        const modal = document.getElementById('gallery-editor-modal');
        const form = document.getElementById('gallery-form');
        form.setAttribute('enctype', 'multipart/form-data');
        const editorTitle = document.getElementById('editor-title');
        const formMethod = document.getElementById('form-method');
        const currentImageContainer = document.getElementById('current-image-container');

        // Make the modal fullscreen if not already
        modal.classList.add('modal-fullscreen');
        const modalContainer = modal.querySelector('.modal-container');
        if (modalContainer && !modalContainer.classList.contains('w-full')) {
            modalContainer.classList.remove('w-11/12', 'md:max-w-md');
            modalContainer.classList.add('w-full', 'h-full', 'max-w-none', 'mx-0', 'rounded-none');

            // Adjust content container to take full height
            const modalContent = modalContainer.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.add('h-full', 'flex', 'flex-col');

                // Adjust the body to take up available space
                const modalBody = modalContent.querySelector('.py-4:not(.border-t):not(.border-b)');
                if (modalBody) {
                    modalBody.classList.add('flex-grow', 'overflow-auto');
                }
            }
        }

        // Reset form
        form.reset();
        currentImageContainer.classList.add('hidden');

        // Reset dropzone if exists
        if (galleryDropzone) {
            galleryDropzone.removeAllFiles(true);
        }

        if (imageId) {
            // Edit mode - fetch data from API
            editorTitle.textContent = 'Edit Gallery Image';
            formMethod.value = 'PUT';

            try {
                const image = images.find(img => img.id == imageId);
                if (!image) {
                    throw new Error('Image not found');
                }

                const formImageId = document.getElementById('form-image-id');
                const titleInput = document.getElementById('title');
                const descriptionInput = document.getElementById('description');
                const isCarouselCheckbox = document.getElementById('is_carousel');
                const isActiveCheckbox = document.getElementById('is_active');
                const currentImage = document.getElementById('current-image');

                // Fill form fields
                formImageId.value = image.id;
                titleInput.value = image.title;
                descriptionInput.value = image.description || '';
                isCarouselCheckbox.checked = image.is_carousel;
                isActiveCheckbox.checked = image.is_active;

                // Show current image if exists
                if (image.image_url) {
                    currentImage.src = image.image_url;
                    currentImageContainer.classList.remove('hidden');

                    // Add fullscreen button to current image
                    if (!currentImageContainer.querySelector('.fullscreen-toggle')) {
                        const fullscreenButton = document.createElement('button');
                        fullscreenButton.className = 'fullscreen-toggle absolute top-2 right-2 p-1 bg-gray-800 bg-opacity-50 rounded-full text-white hover:bg-opacity-70';
                        fullscreenButton.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                            </svg>
                        `;

                        const imageWrapper = document.createElement('div');
                        imageWrapper.className = 'relative';

                        // Move the current image into the wrapper
                        const currentImageElement = currentImageContainer.querySelector('img');
                        if (currentImageElement) {
                            currentImageElement.parentNode.insertBefore(imageWrapper, currentImageElement);
                            imageWrapper.appendChild(currentImageElement);
                            imageWrapper.appendChild(fullscreenButton);
                        }

                        // Add fullscreen functionality
                        fullscreenButton.addEventListener('click', () => {
                            const fullscreenContainer = document.createElement('div');
                            fullscreenContainer.className = 'fixed inset-0 bg-black bg-opacity-90 z-[9999] flex items-center justify-center';
                            fullscreenContainer.innerHTML = `
                                <div class="relative w-full h-full flex flex-col">
                                    <div class="absolute top-4 right-4 flex space-x-2">
                                        <button class="p-2 bg-white rounded-full text-gray-800 hover:bg-gray-300 close-fullscreen">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex-grow flex items-center justify-center p-4">
                                        <img src="${image.image_url}" class="max-h-full max-w-full object-contain" />
                                    </div>
                                </div>
                            `;
                            document.body.appendChild(fullscreenContainer);
                            document.body.style.overflow = 'hidden';

                            // Add close button functionality
                            fullscreenContainer.querySelector('.close-fullscreen').addEventListener('click', () => {
                                fullscreenContainer.remove();
                                document.body.style.overflow = '';
                            });

                            // Also close on ESC key
                            const escHandler = (e) => {
                                if (e.key === 'Escape') {
                                    fullscreenContainer.remove();
                                    document.body.style.overflow = '';
                                    document.removeEventListener('keydown', escHandler);
                                }
                            };
                            document.addEventListener('keydown', escHandler);
                        });
                    }
                }

                // Store current image ID for save operation
                currentImageId = imageId;

            } catch (error) {
                console.error('Error fetching image for editing:', error);
                window.ErrorHandler.showError(`Error: ${error.message || 'Failed to load image data for editing'}`);
                return;
            }
        } else {
            // Create mode
            editorTitle.textContent = 'Add Gallery Image';
            formMethod.value = 'POST';
            document.getElementById('form-image-id').value = '';
            document.getElementById('is_carousel').checked = false;
            document.getElementById('is_active').checked = true;

            // Reset current image ID
            currentImageId = null;
        }

        // Show modal
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');

        // Focus on title field
        setTimeout(() => {
            document.getElementById('title').focus();
        }, 100);
    }

    // Save Gallery Image (Create or Update) using API
    async function saveImage() {
        const form = document.getElementById('gallery-form');
        const formMethodElement = document.getElementById('form-method');

        const formData = new FormData(form);
        const method = formMethodElement.value;
        const imageId = document.getElementById('form-image-id').value;

        // Handle checkbox fields
        if (!formData.has('is_carousel')) {
            formData.append('is_carousel', '0');
        } else {
            formData.set('is_carousel', formData.get('is_carousel') === 'on' ? '1' : '0');
        }

        if (!formData.has('is_active')) {
            formData.append('is_active', '0');
        } else {
            formData.set('is_active', formData.get('is_active') === 'on' ? '1' : '0');
        }

        // Add CSRF token explicitly to the FormData
        formData.append('_token', csrfToken);

        try {
            let url = '/api/gallery';
            let requestMethod = 'POST';

            if (method === 'PUT' && imageId) {
                url = `/api/gallery/${imageId}`;
                requestMethod = 'POST'; // Change to POST for file uploads with PUT intention
                formData.append('_method', 'PUT'); // Laravel will interpret this as PUT
            }

            const response = await fetch(url, {
                method: requestMethod,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            });

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const text = await response.text();
                console.log("Server Response:", text);
                throw new Error(`Server returned non-JSON response: ${text.slice(0, 100)}...`);
            }

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to save gallery image');
            }

            // Close modal and refresh list
            const editorModal = document.getElementById('gallery-editor-modal');
            closeModal(editorModal);

            // Reset dropzone if exists
            if (galleryDropzone) {
                galleryDropzone.removeAllFiles(true);
            }

            fetchGallery();

            // Show success message
            window.ErrorHandler.showSuccess(result.message || 'Gallery image saved successfully');
        } catch (error) {
            console.error('Error saving gallery image:', error);
            window.ErrorHandler.showError(`Error: ${error.message || 'Failed to save gallery image'}`);
        }
    }

    // Delete Gallery Image using API
    async function deleteImage(imageId) {
        try {
            const response = await fetch(`/api/gallery/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to delete gallery image');
            }

            // Close modal and refresh list
            const deleteModal = document.getElementById('delete-modal');
            closeModal(deleteModal);
            fetchGallery();

            // Show success message
            window.ErrorHandler.showSuccess(result.message || 'Gallery image deleted successfully');
        } catch (error) {
            console.error('Error deleting gallery image:', error);
            window.ErrorHandler.showError(`Error: ${error.message || 'Failed to delete gallery image'}`);
        }
    }

    // Close modal
    function closeModal(modal) {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0', 'pointer-events-none');

        // Exit fullscreen preview mode if active
        const fullscreenContainer = document.getElementById('dropzone-fullscreen-preview');
        if (fullscreenContainer) {
            fullscreenContainer.remove();
            document.body.style.overflow = '';
        }

        // Remove any other fullscreen elements
        const previewContainer = document.getElementById('preview-image-container');
        if (previewContainer && previewContainer.classList.contains('fullscreen-preview')) {
            previewContainer.classList.remove('fullscreen-preview');
            previewContainer.style.position = '';
            previewContainer.style.top = '';
            previewContainer.style.left = '';
            previewContainer.style.width = '';
            previewContainer.style.height = '';
            previewContainer.style.backgroundColor = '';
            previewContainer.style.zIndex = '';
            document.body.style.overflow = '';
        }
    }

    // Setup form cancellation to clean up temp files
    function setupFormCancellation(formId, closeButtonSelector, modalSelector) {
        const form = document.getElementById(formId);
        if (!form) return;

        const closeButtons = document.querySelectorAll(closeButtonSelector);
        const modal = document.querySelector(modalSelector);

        closeButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const tempImagePath = form.querySelector('[name="temp_image_path"]');

                if (tempImagePath && tempImagePath.value) {
                    try {
                        await fetch('/api/temp-uploads/cancel', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ path: tempImagePath.value })
                        });
                    } catch (error) {
                        console.error('Error cancelling upload:', error);
                    }
                }

                // Reset form
                form.reset();

                // Close modal
                if (modal) {
                    closeModal(modal);
                }

                // Reset dropzone if exists
                if (galleryDropzone) {
                    galleryDropzone.removeAllFiles(true);
                }
            });
        });
    }
});

// Add the necessary CSS for fullscreen functionality
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
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

        /* Dropzone Fullscreen Preview */
        #dropzone-fullscreen-preview {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Improved Dropzone Styling */
        .dropzone-container {
            min-height: 250px !important;
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
            border-color: #f472b6;
            background: #fce7f3;
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
            width: 200px;
        }

        .dz-image-wrapper {
            position: relative;
        }

        .dz-image {
            width: 200px;
            height: 200px;
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
            background: #ec4899;
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

        .fullscreen-toggle {
            z-index: 9999;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .fullscreen-toggle:hover {
            opacity: 1;
        }
    `;
    document.head.appendChild(style);
});
// Add this to the end of your gallery.js file
document.addEventListener('DOMContentLoaded', function() {
    // Create a fixed visible fullscreen button function
    function addVisibleFullscreenButton() {
        // Check if the preview modal is open
        const previewModal = document.getElementById('preview-modal');
        if (!previewModal || previewModal.classList.contains('opacity-0')) {
            return; // Modal is not open
        }

        // Get the image container
        const previewContainer = document.getElementById('preview-image-container');
        if (!previewContainer) return;

        // Check if our fixed button already exists
        if (!document.getElementById('fixed-fullscreen-btn')) {
            // Create a new button that will be clearly visible
            const btn = document.createElement('button');
            btn.id = 'fixed-fullscreen-btn';
            btn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                </svg>
            `;

            // Style the button
            btn.style.cssText = `
                position: absolute;
                top: 10px;
                right: 10px;
                width: 36px;
                height: 36px;
                background: rgba(0, 0, 0, 0.6);
                color: white;
                border: none;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 100;
            `;

            // Add click handler
            btn.addEventListener('click', function() {
                const img = document.getElementById('preview-image');
                const title = document.getElementById('preview-image-title')?.textContent || '';

                // Create fullscreen view
                const fullscreenView = document.createElement('div');
                fullscreenView.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                `;

                fullscreenView.innerHTML = `
                    <div style="position: relative;">
                        <img src="${img.src}" style="max-height: 90vh; max-width: 90vw; object-fit: contain;" />
                        <button style="position: absolute; top: -40px; right: 0; background: white; border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                `;

                document.body.appendChild(fullscreenView);
                document.body.style.overflow = 'hidden';

                // Add close handler
                fullscreenView.querySelector('button').addEventListener('click', function() {
                    document.body.removeChild(fullscreenView);
                    document.body.style.overflow = '';
                });

                // Close on ESC
                const escHandler = function(e) {
                    if (e.key === 'Escape') {
                        if (document.body.contains(fullscreenView)) {
                            document.body.removeChild(fullscreenView);
                            document.body.style.overflow = '';
                        }
                        document.removeEventListener('keydown', escHandler);
                    }
                };
                document.addEventListener('keydown', escHandler);
            });

            // Add to container
            previewContainer.appendChild(btn);
        }
    }

    // Hide browser's native fullscreen buttons
    const style = document.createElement('style');
    style.textContent = `
        .modal-header-fullscreen-button,
        button[title="Toggle fullscreen"],
        [aria-label="Toggle fullscreen"],
        .fullscreen-button,
        .header-indicator {
            display: none !important;
        }
    `;
    document.head.appendChild(style);

    // Check periodically and when viewing images
    setInterval(addVisibleFullscreenButton, 1000);

    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-image')) {
            setTimeout(addVisibleFullscreenButton, 300);
        }
    });
});
