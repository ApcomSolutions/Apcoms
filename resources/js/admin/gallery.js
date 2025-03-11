document.addEventListener('DOMContentLoaded', function() {
    // State
    let images = [];
    let currentPage = 1;
    let itemsPerPage = 12;
    let totalPages = 1;
    let currentImageId = null;
    let sortableGallery = null;

    // CSRF token setup for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize
    fetchGallery();
    initSortable();

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

    // New Image Button
    document.getElementById('new-image-btn').addEventListener('click', () => {
        openEditorModal();
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
            document.getElementById('preview-image').src = image.image_url;
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
            alert('Failed to load image preview.');
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
            alert('Gallery order updated successfully');

        } catch (error) {
            console.error('Error saving gallery order:', error);
            alert(`Error: ${error.message || 'Failed to update gallery order'}`);
            saveButton.disabled = false;
        }
    }

    // Open Editor Modal
    async function openEditorModal(imageId = null) {
        const modal = document.getElementById('gallery-editor-modal');
        const form = document.getElementById('gallery-form');
        form.setAttribute('enctype', 'multipart/form-data');
        const editorTitle = document.getElementById('editor-title');
        const formMethod = document.getElementById('form-method');
        const currentImageContainer = document.getElementById('current-image-container');

        // Reset form
        form.reset();
        currentImageContainer.classList.add('hidden');

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
                }

                // Store current image ID for save operation
                currentImageId = imageId;

            } catch (error) {
                console.error('Error fetching image for editing:', error);
                alert('Failed to load image data for editing.');
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

    // Open Delete Confirmation Modal
    function openDeleteModal(imageId, imageTitle) {
        const modal = document.getElementById('delete-modal');
        const titleElement = document.getElementById('delete-image-title');

        titleElement.textContent = imageTitle;
        currentImageId = imageId;

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
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
            fetchGallery();

            // Show success message
            alert(result.message || 'Gallery image saved successfully');

        } catch (error) {
            console.error('Error saving gallery image:', error);
            alert(`Error: ${error.message || 'Failed to save gallery image'}`);
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
            alert(result.message || 'Gallery image deleted successfully');

        } catch (error) {
            console.error('Error deleting gallery image:', error);
            alert(`Error: ${error.message || 'Failed to delete gallery image'}`);
        }
    }

    // Close modal
    function closeModal(modal) {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0', 'pointer-events-none');
    }
});
