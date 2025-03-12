/**
 * News Management Module - Complete Version
 * Handles CRUD operations and Analytics for News
 */
axios.defaults.headers.common['X-Admin-Request'] = 'true';
document.addEventListener('DOMContentLoaded', function() {
    // State
    let news = [];
    let categories = [];
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalPages = 1;
    let currentNewsId = null;
    let currentNewsSlug = null;

    // Charts
    let activityChart = null;
    let readTimeChart = null;
    let deviceChart = null;

    // Global chart instances object
    window.chartInstances = {
        deviceChart: null,
        activityChart: null,
        readTimeChart: null
    };

    // CSRF token setup for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Initialize
    fetchNews();
    fetchCategories();
    initializeAnalytics();
    initializeTrixEditor();
    initializeFileUpload();

    // Event Listeners
    document.getElementById('refresh-btn')?.addEventListener('click', fetchNews);
    document.getElementById('search')?.addEventListener('input', filterNews);
    document.getElementById('category-filter')?.addEventListener('change', filterNews);
    document.getElementById('status-filter')?.addEventListener('change', filterNews);
    document.getElementById('prev-page')?.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderNews();
        }
    });
    document.getElementById('next-page')?.addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            renderNews();
        }
    });

    // Activity chart period selector
    document.getElementById('activity-period')?.addEventListener('change', function () {
        loadActivityData(this.value);
    });

    // Add News Button
    document.getElementById('new-news-btn')?.addEventListener('click', () => {
        openEditorModal();
    });

    // Save News Button
    document.getElementById('save-news-btn')?.addEventListener('click', saveNews);

    // Delete confirmation
    document.getElementById('confirm-delete-btn')?.addEventListener('click', () => {
        if (currentNewsSlug) {
            deleteNews(currentNewsSlug);
        }
    });

    // Preview edit button
    document.getElementById('preview-edit-btn')?.addEventListener('click', () => {
        // Close preview modal
        closeModal(document.getElementById('preview-modal'));

        // Open editor modal with current news
        if (currentNewsSlug) {
            openEditorModal(currentNewsSlug);
        }
    });

    // Preview delete button
    document.getElementById('preview-delete-btn')?.addEventListener('click', () => {
        // Close preview modal
        closeModal(document.getElementById('preview-modal'));

        // Open delete modal
        if (currentNewsSlug) {
            const newsItem = news.find(item => item.slug === currentNewsSlug);
            if (newsItem) {
                openDeleteModal(currentNewsSlug, newsItem.title);
            }
        }
    });

    // Delete Image Button
    document.getElementById('delete-image-btn')?.addEventListener('click', function() {
        // Set the hidden input value to 1 to indicate image should be deleted
        const deleteImageInput = document.getElementById('delete_image');
        if (deleteImageInput) {
            deleteImageInput.value = '1';
        }

        // Hide the current image container
        const currentImageContainer = document.getElementById('current-image-container');
        if (currentImageContainer) {
            currentImageContainer.classList.add('hidden');
        }

        // Reset the file input
        const imageInput = document.getElementById('image');
        if (imageInput) {
            imageInput.value = '';
        }
    });

    // Modal handling
    setupModalHandling();

    /**
     * Initialize Trix Editor with proper configuration
     */
    function initializeTrixEditor() {
        // Fix Trix editor issues by adding event listeners
        const trixEditor = document.querySelector('trix-editor');
        if (!trixEditor) return;

        // Handle attachments for images
        trixEditor.addEventListener('trix-attachment-add', function(event) {
            const attachment = event.attachment;
            if (attachment.file) {
                // Handle image uploads
                if (attachment.file.type.match(/image/)) {
                    console.log('Image file added:', attachment.file);
                    uploadTrixAttachment(attachment);
                } else {
                    // Non-image files not supported
                    attachment.remove();
                    alert('Only image files are supported for insertion in content.');
                }
            }
        });

        // Add event listener to ensure content is always updated properly
        trixEditor.addEventListener('trix-change', function() {
            const contentInput = document.getElementById('content-input');
            if (contentInput) {
                // Store clean content in the hidden input field for form submission
                contentInput.value = cleanTrixContent(this.editor.getDocument().toString());
            }
        });
    }


    /**
     * Clean Trix content by removing admin UI elements and metadata
     *
     * @param {string} content - The raw content from Trix editor
     * @return {string} - Cleaned content without admin UI elements
     */
    function cleanTrixContent(content) {
        // Create a temporary DOM element to manipulate the content
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = content;

        // Remove any admin UI elements that might be in the content
        const adminElements = tempDiv.querySelectorAll('.admin-ui, [data-admin-element], figcaption');
        adminElements.forEach(el => el.remove());

        // Clean up any file information or remove text
        const cleanedContent = tempDiv.innerHTML
            .replace(/Remove\s*MengAstronot\d+\.png\s*\d+\.\d+\s*KB/g, '')
            .replace(/Add a caption\.\.\./g, '')
            .replace(/Remove/g, '')
            .replace(/\d+\.\d+\s*KB/g, '');

        return cleanedContent;
    }

    /**
     * Upload attachment file from Trix editor
     */
    function uploadTrixAttachment(attachment) {
        const file = attachment.file;
        const form = new FormData();
        form.append('file', file);

        // Log for debugging
        console.log('Uploading file to Trix:', file.name, 'type:', file.type);

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.append('_token', token);

        // Show upload progress
        attachment.setUploadProgress(0);

        // Send file to dedicated Trix endpoint
        fetch('/api/trix-uploads', {
            method: 'POST',
            body: form,
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Admin-Request': 'true'
            }
        })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Upload failed with status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                // Log complete response
                console.log('Server response for Trix upload:', data);

                // Check if data has URL
                if (!data.url) {
                    console.error('Response missing URL:', data);
                    throw new Error('Server response missing URL');
                }

                // Log URL to be used
                console.log('Setting Trix attachment URL to:', data.url);

                // Update attachment with URL from server and add necessary attributes
                attachment.setAttributes({
                    url: data.url,
                    href: data.url
                });

                // Add a class to identify admin elements that should be removed during saving
                attachment.setAttributes({
                    'data-admin-element': 'true'
                });
            })
            .catch(error => {
                console.error('Error uploading file to Trix:', error);
                attachment.remove();
                alert('Failed to upload image: ' + error.message);
            })
            .finally(() => {
                attachment.setUploadProgress(100);
                console.log('Upload process complete');
            });
    }


    /**
     * Helper function to safely destroy and create charts
     */
    function safeInitChart(canvasId, chartKey, creationCallback) {
        const ctx = document.getElementById(canvasId);
        if (!ctx || typeof Chart === 'undefined') {
            console.log(`Chart creation failed: Canvas #${canvasId} not found or Chart is undefined`);
            return null;
        }

        try {
            // First check if there's an existing chart on this canvas
            if (typeof Chart.getChart === 'function') {
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    console.log(`Destroying existing chart on canvas: ${canvasId}`);
                    existingChart.destroy();
                }
            }

            // Make sure window.chartInstances exists
            if (!window.chartInstances) {
                console.log('Creating window.chartInstances object');
                window.chartInstances = {
                    deviceChart: null,
                    activityChart: null,
                    readTimeChart: null
                };
            }

            // Also check global reference and destroy if exists
            if (window.chartInstances[chartKey]) {
                console.log(`Destroying existing ${chartKey} from global reference`);
                window.chartInstances[chartKey].destroy();
                window.chartInstances[chartKey] = null;
            }

            // Now create the new chart
            console.log(`Creating new ${chartKey} chart`);
            const newChart = creationCallback(ctx);
            window.chartInstances[chartKey] = newChart;
            return newChart;
        } catch (error) {
            console.error(`Error creating ${chartKey}:`, error);
            return null;
        }
    }



    /**
     * Initialize drag and drop file upload
     */


    function initializeFileUpload() {
        const fileInput = document.getElementById('image');
        if (!fileInput) return;

        const dropZone = document.createElement('div');
        dropZone.className = 'drop-zone bg-gray-100 border-2 border-dashed border-gray-300 rounded-md p-6 text-center hover:bg-gray-200 transition cursor-pointer mt-2';
        dropZone.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <p class="mt-1 text-sm text-gray-600">Drag and drop your image here or click to browse</p>
            <p class="mt-1 text-xs text-gray-500">Max file size: 10MB</p>
        `;

        // Insert the drop zone after the file input
        if (fileInput.parentElement) {
            fileInput.parentElement.appendChild(dropZone);
            fileInput.style.display = 'none'; // Hide the original input
        }

        // Add event listeners for drag and drop
        dropZone.addEventListener('click', function() {
            fileInput.click();
        });

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-pink-500');
            this.classList.remove('border-gray-300');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-pink-500');
            this.classList.add('border-gray-300');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-pink-500');
            this.classList.add('border-gray-300');

            if (e.dataTransfer.files.length) {
                const file = e.dataTransfer.files[0];

                // Check file size (10MB limit)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size exceeds 10MB limit.');
                    return;
                }

                // Check if it's an image
                if (!file.type.match('image.*')) {
                    alert('Only image files are allowed.');
                    return;
                }

                fileInput.files = e.dataTransfer.files;

                // Reset delete_image flag when a new image is selected
                const deleteImageInput = document.getElementById('delete_image');
                if (deleteImageInput) {
                    deleteImageInput.value = '0';
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'mt-3';
                    preview.innerHTML = `
                        <div class="relative">
                            <img src="${e.target.result}" class="max-h-32 rounded border object-cover" />
                            <button type="button" class="remove-image absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    `;

                    // Remove existing preview
                    const existingPreview = dropZone.nextElementSibling;
                    if (existingPreview && existingPreview.classList.contains('mt-3')) {
                        existingPreview.remove();
                    }

                    dropZone.after(preview);

                    // Add event listener to remove button
                    preview.querySelector('.remove-image').addEventListener('click', function() {
                        fileInput.value = '';
                        preview.remove();
                    });
                };

                reader.readAsDataURL(file);
            }
        });

        // Handle file selection via the input
        fileInput.addEventListener('change', function() {
            if (this.files.length) {
                const file = this.files[0];

                // Check file size (10MB limit)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size exceeds 10MB limit.');
                    this.value = '';
                    return;
                }

                // Reset delete_image flag when a new image is selected
                const deleteImageInput = document.getElementById('delete_image');
                if (deleteImageInput) {
                    deleteImageInput.value = '0';
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'mt-3';
                    preview.innerHTML = `
                        <div class="relative">
                            <img src="${e.target.result}" class="max-h-32 rounded border object-cover" />
                            <button type="button" class="remove-image absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    `;

                    // Remove existing preview
                    const existingPreview = dropZone.nextElementSibling;
                    if (existingPreview && existingPreview.classList.contains('mt-3')) {
                        existingPreview.remove();
                    }

                    dropZone.after(preview);

                    // Add event listener to remove button
                    preview.querySelector('.remove-image').addEventListener('click', function(e) {
                        e.preventDefault();
                        fileInput.value = '';
                        preview.remove();
                    });
                };

                reader.readAsDataURL(file);
            }
        });
    }

    /**
     * Fetch all news from API
     */
    async function fetchNews() {
        try {
            const newsTable = document.getElementById('news-table-body');
            if (newsTable) {
                newsTable.innerHTML = `
            <tr>
                <td colspan="6" class="py-10 text-center text-gray-500">
                    <div class="flex justify-center">
                        <svg class="h-10 w-10 text-gray-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div class="mt-2">Loading news items...</div>
                </td>
            </tr>
        `;
            }

            // Add cache buster to prevent caching
            const timestamp = new Date().getTime();

            // Use admin endpoint to get all news including drafts
            const response = await fetch(`/api/admin/news/all?_=${timestamp}`);
            if (!response.ok) throw new Error('Failed to fetch news');

            const data = await response.json();
            console.log('Raw fetched news data:', data); // Debug log

            // Process and set global news array with status validation
            news = data.map(item => {
                return {
                    ...item,
                    // Ensure status is always defined with a default
                    status: item.status || 'published'
                };
            });

            console.log('Processed news data with status:', news); // Debug log

            renderNews();
        } catch (error) {
            console.error('Error fetching news:', error);
            if (newsTable) {
                newsTable.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-10">
                    <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-red-800">Error loading news</h3>
                    <p class="mt-1 text-gray-500">Please try again or refresh the page.</p>
                </td>
            </tr>
        `;
            }
        }
    }


    /**
     * Fetch categories for filter and form dropdown
     */
    async function fetchCategories() {
        try {
            const response = await fetch('/api/news-categories');
            if (!response.ok) throw new Error('Failed to fetch categories');

            categories = await response.json();

            // Populate category filters
            const categoryFilter = document.getElementById('category-filter');
            const categoryDropdown = document.getElementById('news_category_id');

            if (!categoryFilter || !categoryDropdown) return;

            // Clear existing options (except the first one)
            while (categoryFilter.options.length > 1) {
                categoryFilter.remove(1);
            }

            while (categoryDropdown.options.length > 1) {
                categoryDropdown.remove(1);
            }

            // Add categories to filter dropdown
            categories.forEach(category => {
                const filterOption = document.createElement('option');
                filterOption.value = category.id;
                filterOption.textContent = `${category.name} (${category.news_count || 0})`;
                categoryFilter.appendChild(filterOption);

                const formOption = document.createElement('option');
                formOption.value = category.id;
                formOption.textContent = category.name;
                categoryDropdown.appendChild(formOption);
            });

        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    }

    /**
     * Filter news based on search and filter inputs
     */
    function filterNews() {
        currentPage = 1;
        renderNews();
    }

    /**
     * Render news table with filtering and pagination
     */
    function renderNews() {
        const newsTable = document.getElementById('news-table-body');
        if (!newsTable) return;

        newsTable.innerHTML = '';

        const searchInput = document.getElementById('search');
        const categoryFilter = document.getElementById('category-filter');
        const statusFilter = document.getElementById('status-filter');

        // Apply filters
        const searchQuery = searchInput?.value?.toLowerCase() || '';
        const categoryValue = categoryFilter?.value || '';
        const statusValue = statusFilter?.value || '';

        let filteredNews = news.filter(item => {
            const titleMatch = (item.title || '').toLowerCase().includes(searchQuery);
            const authorMatch = (item.author || '').toLowerCase().includes(searchQuery);
            const contentMatch = (item.content || '').toLowerCase().includes(searchQuery);

            const categoryMatch = !categoryValue || item.news_category_id == categoryValue;

            // Get the status, ensuring a default if it doesn't exist
            const itemStatus = item.status || 'published';

            // Status match (published vs. draft)
            const statusMatch = !statusValue || statusValue === itemStatus;

            // Debug log for status matching
            console.log(`Item "${item.title}" - Status: ${itemStatus}, Filter: ${statusValue}, Match: ${statusMatch}`);

            return (titleMatch || authorMatch || contentMatch) && categoryMatch && statusMatch;
        });

        // Sort by publish date (newest first)
        filteredNews.sort((a, b) => new Date(b.publish_date || 0) - new Date(a.publish_date || 0));

        // Calculate pagination
        totalPages = Math.ceil(filteredNews.length / itemsPerPage);
        if (totalPages === 0) totalPages = 1;

        // Update pagination UI
        const totalNewsElement = document.getElementById('total-news');
        const pageStartElement = document.getElementById('page-start');
        const pageEndElement = document.getElementById('page-end');

        if (totalNewsElement) totalNewsElement.textContent = filteredNews.length;
        if (pageStartElement) pageStartElement.textContent = filteredNews.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        if (pageEndElement) pageEndElement.textContent = Math.min(currentPage * itemsPerPage, filteredNews.length);

        // Render pagination
        renderPagination();

        // Slice for current page
        const pageNews = filteredNews.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

        if (pageNews.length === 0) {
            newsTable.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-10">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No news found</h3>
                    <p class="mt-1 text-gray-500">Try adjusting your search or filter criteria.</p>
                </td>
            </tr>
        `;
            return;
        }

        // Render news items
        pageNews.forEach(item => {
            const row = document.createElement('tr');

            // Format date
            const publishDate = new Date(item.publish_date || new Date());
            const formattedDate = publishDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            // Explicitly determine status with fallback to 'published'
            const status = item.status || 'published';

            // Log for debugging
            console.log(`Rendering news item "${item.title}" with status: ${status}`);

            // Status badge class
            const statusClass = status === 'published' ? 'status-published' : 'status-draft';

            row.innerHTML = `
            <td class="py-4 pl-4 pr-3 text-sm sm:pl-6">
                <div class="font-medium text-gray-900 truncate max-w-xs">${item.title || 'Untitled'}</div>
                <div class="text-gray-500 truncate max-w-xs">${item.slug || ''}</div>
            </td>
            <td class="px-3 py-4 text-sm text-gray-500">
                ${item.category_name || '<span class="text-gray-400">Uncategorized</span>'}
            </td>
            <td class="px-3 py-4 text-sm text-gray-500">
                ${item.author || 'Unknown'}
            </td>
            <td class="px-3 py-4 text-sm text-gray-500">
                ${formattedDate}
            </td>
            <td class="px-3 py-4 text-sm text-gray-500">
                <span class="status-badge ${statusClass}">
                    ${status === 'published' ? 'Published' : 'Draft'}
                </span>
            </td>
            <td class="py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <div class="flex justify-end space-x-2">
                    <button class="action-btn view-btn" data-slug="${item.slug}" aria-label="View">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <button class="action-btn edit-btn" data-slug="${item.slug}" aria-label="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button class="action-btn delete-btn" data-slug="${item.slug}" data-title="${item.title || 'Untitled'}" aria-label="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </td>
        `;

            newsTable.appendChild(row);
        });

        // Add event listeners to buttons
        attachActionButtonListeners();
    }

    /**
     * Attach event listeners to action buttons
     */
    function attachActionButtonListeners() {
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', () => {
                const slug = button.getAttribute('data-slug');
                if (slug) openPreviewModal(slug);
            });
        });

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                const slug = button.getAttribute('data-slug');
                if (slug) openEditorModal(slug);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                const slug = button.getAttribute('data-slug');
                const title = button.getAttribute('data-title');
                if (slug) openDeleteModal(slug, title);
            });
        });
    }

    /**
     * Render pagination controls
     */
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
            button.className = `relative inline-flex items-center px-4 py-2 border ${currentPage === pageNum ? 'pagination-active' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'}`;
            button.textContent = pageNum;

            button.addEventListener('click', () => {
                currentPage = pageNum;
                renderNews();
            });

            paginationContainer.appendChild(button);
        }
    }

    /**
     * Open Preview Modal
     */
    async function openPreviewModal(slug) {
        const modal = document.getElementById('preview-modal');
        if (!modal) return;

        try {
            // Fetch the latest data for this news item
            const response = await fetch(`/api/news/${slug}`);
            if (!response.ok) throw new Error('Failed to fetch news data');

            const newsItem = await response.json();

            // Store current news ID and slug
            currentNewsId = newsItem.id;
            currentNewsSlug = slug;

            // Fill preview content
            document.getElementById('preview-title').textContent = 'News Preview: ' + (newsItem.title || 'Untitled');

            // Set image if exists
            const previewImage = document.getElementById('preview-image');
            if (previewImage) {
                if (newsItem.image_url) {
                    previewImage.src = newsItem.image_url;
                    previewImage.style.display = 'block';
                } else {
                    previewImage.style.display = 'none';
                }
            }

            const elements = {
                'preview-news-title': newsItem.title || 'Untitled',
                'preview-author': newsItem.author || 'Unknown',
                'preview-date': new Date(newsItem.publish_date || new Date()).toLocaleDateString('en-US', {year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }),
                'preview-category': newsItem.category_name || 'Uncategorized',
                'preview-content': newsItem.content || '<p>No content</p>'
            };

            // Update all elements with their respective content
            Object.keys(elements).forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    if (id === 'preview-content') {
                        element.innerHTML = elements[id];
                    } else {
                        element.textContent = elements[id];
                    }
                }
            });

            // Update status badge
            const statusBadge = document.getElementById('preview-status-badge');
            if (statusBadge) {
                const status = newsItem.status || 'published';
                if (status === 'published') {
                    statusBadge.textContent = 'Published';
                    statusBadge.className = 'px-2 py-1 text-xs rounded-full status-published';
                } else {
                    statusBadge.textContent = 'Draft';
                    statusBadge.className = 'px-2 py-1 text-xs rounded-full status-draft';
                }
            }

            // Show modal
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');

        } catch (error) {
            console.error('Error opening preview:', error);
            alert('Failed to load news preview.');
        }
    }

    /**
     * Open Editor Modal
     */
    async function openEditorModal(slug = null) {
        const modal = document.getElementById('news-editor-modal');
        if (!modal) return;

        const form = document.getElementById('news-form');
        const editorTitle = document.getElementById('editor-title');
        const formMethod = document.getElementById('form-method');
        const currentImageContainer = document.getElementById('current-image-container');
        const trixEditor = document.querySelector('trix-editor');

        // Reset delete image flag
        const deleteImageInput = document.getElementById('delete_image');
        if (deleteImageInput) {
            deleteImageInput.value = '0';
        }

        // Reset form
        if (form) form.reset();
        if (currentImageContainer) currentImageContainer.classList.add('hidden');
        if (trixEditor) trixEditor.editor.loadHTML('');

        if (slug) {
            // Edit mode - fetch data from API
            if (editorTitle) editorTitle.textContent = 'Edit News Article';
            if (formMethod) formMethod.value = 'PUT';

            try {
                // Add cache buster to prevent caching
                const timestamp = new Date().getTime();
                const response = await fetch(`/api/news/${slug}?_=${timestamp}`);
                if (!response.ok) throw new Error('News not found');

                const newsItem = await response.json();
                console.log('Editing news item:', newsItem); // Debug log

                const formNewsId = document.getElementById('form-news-id');
                const titleInput = document.getElementById('title');
                const authorInput = document.getElementById('author');
                const categorySelect = document.getElementById('news_category_id');
                const publishDateInput = document.getElementById('publish_date');
                const statusSelect = document.getElementById('status');
                const contentInput = document.getElementById('content-input');
                const currentImage = document.getElementById('current-image');

                // Fill form fields
                if (formNewsId) formNewsId.value = newsItem.id || '';
                if (titleInput) titleInput.value = newsItem.title || '';
                if (authorInput) authorInput.value = newsItem.author || '';
                if (categorySelect) categorySelect.value = newsItem.news_category_id || '';

                // Format date for input (YYYY-MM-DD)
                if (publishDateInput && newsItem.publish_date) {
                    const publishDate = new Date(newsItem.publish_date);
                    publishDateInput.value = publishDate.toISOString().split('T')[0];
                }

                // Set status dropdown with explicit value check
                if (statusSelect) {
                    // Get the status, default to 'published' if not specified
                    const itemStatus = newsItem.status || 'published';

                    // Log for debugging
                    console.log('News item status from API:', itemStatus);

                    // Make sure the status value is valid
                    statusSelect.value = ['published', 'draft'].includes(itemStatus)
                        ? itemStatus
                        : 'published';

                    // Additional logging to confirm
                    console.log('Status dropdown set to:', statusSelect.value);
                }

                // Set content in Trix editor
                if (trixEditor && contentInput) {
                    trixEditor.editor.loadHTML(newsItem.content || '');
                    contentInput.value = newsItem.content || '';
                }

                // Show current image if exists
                if (currentImage && currentImageContainer && newsItem.image_url) {
                    currentImage.src = newsItem.image_url;
                    currentImageContainer.classList.remove('hidden');
                }

                // Store current news slug
                currentNewsSlug = slug;

            } catch (error) {
                console.error('Error fetching news for editing:', error);
                alert('Failed to load news data for editing.');
                return;
            }
        } else {
            // Create mode
            if (editorTitle) editorTitle.textContent = 'Add News Article';
            if (formMethod) formMethod.value = 'POST';

            const formNewsId = document.getElementById('form-news-id');
            if (formNewsId) formNewsId.value = '';

            // IMPORTANT: Set default status to 'published' for new articles
            const statusSelect = document.getElementById('status');
            if (statusSelect) {
                statusSelect.value = 'published';
                console.log('New article, status defaulted to:', statusSelect.value);
            }

            // Set default publish date to today
            const publishDateInput = document.getElementById('publish_date');
            if (publishDateInput) {
                const today = new Date();
                const formattedDate = today.toISOString().split('T')[0];
                publishDateInput.value = formattedDate;
            }

            // Reset current news slug
            currentNewsSlug = null;
        }

        // Show modal
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');

        // Focus on title field
        setTimeout(() => {
            const titleInput = document.getElementById('title');
            if (titleInput) titleInput.focus();
        }, 100);
    }

    /**
     * Open Delete Confirmation Modal
     */
    function openDeleteModal(slug, title) {
        const modal = document.getElementById('delete-modal');
        if (!modal) return;

        const titleElement = document.getElementById('delete-news-title');
        if (titleElement) titleElement.textContent = title || 'this news item';

        currentNewsSlug = slug;

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
    }


    // /**
    //  * Save News (Create or Update) using API
    //  */
    // async function saveNews() {
    //     const form = document.getElementById('news-form');
    //     const formMethodElement = document.getElementById('form-method');
    //     if (!form || !formMethodElement) return;
    //
    //     const formData = new FormData(form);
    //     const method = formMethodElement.value;
    //
    //     // Get content from Trix Editor
    //     const trixEditor = document.querySelector('trix-editor');
    //     const contentInput = document.getElementById('content-input');
    //     if (trixEditor && contentInput) {
    //         // Make sure we have the latest content
    //         formData.set('content', trixEditor.editor.getDocument().toString());
    //     }
    //
    //     // IMPORTANT: Explicitly get the status value and add it to the form data
    //     const statusSelect = document.getElementById('status');
    //     if (statusSelect) {
    //         const statusValue = statusSelect.value || 'published';
    //         console.log('Status selected:', statusValue); // Debug log
    //         formData.set('status', statusValue);
    //     } else {
    //         // If for some reason the select element is missing, default to published
    //         formData.set('status', 'published');
    //     }
    //
    //     // Add CSRF token explicitly to the FormData
    //     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    //     formData.append('_token', csrfToken);
    //
    //     // Make sure delete_image is included
    //     const deleteImageInput = document.getElementById('delete_image');
    //     if (deleteImageInput) {
    //         console.log('Delete image flag:', deleteImageInput.value);
    //     }
    //
    //     // Log all form data for debugging
    //     console.log('Form data entries:');
    //     for (let pair of formData.entries()) {
    //         console.log(pair[0] + ': ' + pair[1]);
    //     }
    //
    //     try {
    //         let url = '/api/news';
    //         let requestMethod = 'POST';
    //
    //         if (method === 'PUT' && currentNewsSlug) {
    //             url = `/api/news/${currentNewsSlug}`;
    //             requestMethod = 'POST'; // Change to POST for file uploads with PUT intention
    //             formData.append('_method', 'PUT'); // Laravel will interpret this as PUT
    //         }
    //
    //         // Disable save button and show loading state
    //         const saveButton = document.getElementById('save-news-btn');
    //         if (saveButton) {
    //             saveButton.disabled = true;
    //             saveButton.innerHTML = `
    //             <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    //                 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    //                 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    //             </svg>
    //             Saving...
    //         `;
    //         }
    //
    //         const response = await fetch(url, {
    //             method: requestMethod,
    //             headers: {
    //                 'X-CSRF-TOKEN': csrfToken,
    //                 // Do NOT include Content-Type header when using FormData
    //             },
    //             body: formData
    //         });
    //
    //         // Re-enable save button
    //         if (saveButton) {
    //             saveButton.disabled = false;
    //             saveButton.innerHTML = 'Save';
    //         }
    //
    //         // Check if response is JSON
    //         const contentType = response.headers.get("content-type");
    //         let result;
    //
    //         if (contentType && contentType.includes("application/json")) {
    //             result = await response.json();
    //         } else {
    //             // Handle non-JSON response (likely an error)
    //             const text = await response.text();
    //             console.error("Server returned non-JSON response:", text.slice(0, 500));
    //             throw new Error(`Server error: ${response.status} ${response.statusText}`);
    //         }
    //
    //         if (!response.ok) {
    //             if (result.errors) {
    //                 // Format validation errors for better display
    //                 const errorMessages = Object.entries(result.errors)
    //                     .map(([field, msgs]) => `${field}: ${msgs.join(', ')}`)
    //                     .join('\n');
    //                 throw new Error(`Validation errors:\n${errorMessages}`);
    //             }
    //             throw new Error(result.message || `Error ${response.status}: Failed to save news article`);
    //         }
    //
    //         // Close modal and refresh list
    //         const editorModal = document.getElementById('news-editor-modal');
    //         if (editorModal) closeModal(editorModal);
    //
    //         // Force fetch news data again to refresh UI with fresh data
    //         await fetchNews();
    //
    //         // Show success message
    //         alert(result.message || 'News article saved successfully');
    //
    //     } catch (error) {
    //         console.error('Error saving news:', error);
    //         alert(`Error: ${error.message || 'Failed to save news article'}`);
    //
    //         // Re-enable save button if still disabled
    //         const saveButton = document.getElementById('save-news-btn');
    //         if (saveButton && saveButton.disabled) {
    //             saveButton.disabled = false;
    //             saveButton.innerHTML = 'Save';
    //         }
    //     }
    // }

    /**
     * Save news article with properly cleaned content
     */
    async function saveNews() {
        const form = document.getElementById('news-form');
        const formMethodElement = document.getElementById('form-method');
        if (!form || !formMethodElement) return;

        const formData = new FormData(form);
        const method = formMethodElement.value;

        // Get content from Trix Editor - FIXED
        const trixEditor = document.querySelector('trix-editor');
        const contentInput = document.getElementById('content-input');
        if (trixEditor && contentInput) {
            // Get content from Trix editor and ensure it's properly cleaned
            const editorContent = trixEditor.innerHTML;
            const cleanedContent = cleanTrixContent(editorContent);
            formData.set('content', cleanedContent);

            // Log for debugging
            console.log('Captured and cleaned Trix content for submission:', {
                length: cleanedContent.length,
                sample: cleanedContent.substring(0, 200) + (cleanedContent.length > 200 ? '...' : '')
            });
        }

        // IMPORTANT: Explicitly get the status value and add it to the form data
        const statusSelect = document.getElementById('status');
        if (statusSelect) {
            const statusValue = statusSelect.value || 'published';
            console.log('Status selected:', statusValue); // Debug log
            formData.set('status', statusValue);
        } else {
            // If for some reason the select element is missing, default to published
            formData.set('status', 'published');
        }

        // Add CSRF token explicitly to the FormData
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        formData.append('_token', csrfToken);

        // Make sure delete_image is included
        const deleteImageInput = document.getElementById('delete_image');
        if (deleteImageInput) {
            console.log('Delete image flag:', deleteImageInput.value);
        }

        try {
            let url = '/api/news';
            let requestMethod = 'POST';

            if (method === 'PUT' && currentNewsSlug) {
                url = `/api/news/${currentNewsSlug}`;
                requestMethod = 'POST'; // Change to POST for file uploads with PUT intention
                formData.append('_method', 'PUT'); // Laravel will interpret this as PUT
            }

            // Disable save button and show loading state
            const saveButton = document.getElementById('save-news-btn');
            if (saveButton) {
                saveButton.disabled = true;
                saveButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
            }

            const response = await fetch(url, {
                method: requestMethod,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    // Do NOT include Content-Type header when using FormData
                },
                body: formData
            });

            // Re-enable save button
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = 'Save';
            }

            // Check if response is JSON
            const contentType = response.headers.get("content-type");
            let result;

            if (contentType && contentType.includes("application/json")) {
                result = await response.json();
            } else {
                // Handle non-JSON response (likely an error)
                const text = await response.text();
                console.error("Server returned non-JSON response:", text.slice(0, 500));
                throw new Error(`Server error: ${response.status} ${response.statusText}`);
            }

            if (!response.ok) {
                if (result.errors) {
                    // Format validation errors for better display
                    const errorMessages = Object.entries(result.errors)
                        .map(([field, msgs]) => `${field}: ${msgs.join(', ')}`)
                        .join('\n');
                    throw new Error(`Validation errors:\n${errorMessages}`);
                }
                throw new Error(result.message || `Error ${response.status}: Failed to save news article`);
            }

            // Close modal and refresh list
            const editorModal = document.getElementById('news-editor-modal');
            if (editorModal) closeModal(editorModal);

            // Force fetch news data again to refresh UI with fresh data
            await fetchNews();

            // Show success message
            alert(result.message || 'News article saved successfully');

        } catch (error) {
            console.error('Error saving news:', error);
            alert(`Error: ${error.message || 'Failed to save news article'}`);

            // Re-enable save button if still disabled
            const saveButton = document.getElementById('save-news-btn');
            if (saveButton && saveButton.disabled) {
                saveButton.disabled = false;
                saveButton.innerHTML = 'Save';
            }
        }
    }

    /**
     * Delete News using API
     */
    async function deleteNews(slug) {
        if (!slug) return;

        try {
            // Disable delete button and show loading state
            const deleteButton = document.getElementById('confirm-delete-btn');
            if (deleteButton) {
                deleteButton.disabled = true;
                deleteButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Deleting...
                `;
            }

            const response = await fetch(`/api/news/${slug}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            // Re-enable delete button
            if (deleteButton) {
                deleteButton.disabled = false;
                deleteButton.innerHTML = 'Delete';
            }

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to delete news article');
            }

            const result = await response.json();

            // Close modal and refresh list
            const deleteModal = document.getElementById('delete-modal');
            if (deleteModal) closeModal(deleteModal);

            fetchNews();

            // Show success message
            alert(result.message || 'News article deleted successfully');

        } catch (error) {
            console.error('Error deleting news:', error);
            alert(`Error: ${error.message || 'Failed to delete news article'}`);

            // Re-enable delete button if still disabled
            const deleteButton = document.getElementById('confirm-delete-btn');
            if (deleteButton && deleteButton.disabled) {
                deleteButton.disabled = false;
                deleteButton.innerHTML = 'Delete';
            }
        }
    }

    /**
     * Initialize analytics charts and data with better error handling
     */
    function initializeAnalytics() {
        console.log('Initializing analytics...');

        // Make sure chartInstances exists
        if (!window.chartInstances) {
            window.chartInstances = {
                deviceChart: null,
                activityChart: null,
                readTimeChart: null
            };
        }

        // First destroy any existing charts to avoid conflicts
        destroyAllCharts();

        // Show initial loading states
        const deviceStatsContainer = document.getElementById('device-stats');
        const activityChartContainer = document.getElementById('activity-chart-container');
        const readTimeChartContainer = document.getElementById('read-time-chart-container');
        const topNewsTable = document.getElementById('top-news-table');

        // Set initial loading states for all containers
        if (deviceStatsContainer) {
            deviceStatsContainer.innerHTML = `
            <div class="animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-3"></div>
                <div class="h-4 bg-gray-200 rounded w-2/3 mb-3"></div>
                <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            </div>
        `;
        }

        if (activityChartContainer) {
            activityChartContainer.innerHTML = `
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-500 mt-2">Loading chart...</p>
                </div>
            </div>
            <canvas id="activity-chart" class="w-full h-full"></canvas>
        `;
        }

        if (readTimeChartContainer) {
            readTimeChartContainer.innerHTML = `
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-500 mt-2">Loading chart...</p>
                </div>
            </div>
            <canvas id="read-time-chart" class="w-full h-full"></canvas>
        `;
        }

        if (topNewsTable) {
            topNewsTable.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                    <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2">Loading top news...</p>
                </td>
            </tr>
        `;
        }

        // Load each component independently with proper error handling

        // Overall statistics (no chart)
        loadOverallStats().catch(error => {
            console.error('Error loading overall stats:', error);
            // Update the UI to show error for stats
            const statElements = ['stat-total-views', 'stat-unique-visitors', 'stat-avg-read-time', 'stat-completion-rate'];
            statElements.forEach(id => {
                updateElementText(id, 'Error');
            });
        });

        // Top news table
        loadTopNews().catch(error => {
            console.error('Error loading top news:', error);
            if (topNewsTable) {
                topNewsTable.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-red-800">Error loading data</h3>
                        <p class="mt-1 text-sm text-gray-500">Please try again later.</p>
                    </td>
                </tr>
            `;
            }
        });

        // Device chart
        loadDeviceBreakdown().catch(error => {
            console.error('Error loading device breakdown:', error);
            if (deviceStatsContainer) {
                deviceStatsContainer.innerHTML = `
                <div class="text-center py-4">
                    <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="mt-2 text-red-500">Error loading device data</p>
                </div>
            `;
            }
        });

        // Activity chart
        loadActivityData('month').catch(error => {
            console.error('Error loading activity data:', error);
            if (activityChartContainer) {
                activityChartContainer.innerHTML = `
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="mt-2 text-red-500">Error loading activity data</p>
                    </div>
                </div>
            `;
            }
        });

        // Read time chart
        loadReadTimeDistribution().catch(error => {
            console.error('Error loading read time distribution:', error);
            if (readTimeChartContainer) {
                readTimeChartContainer.innerHTML = `
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="mt-2 text-red-500">Error loading read time data</p>
                    </div>
                </div>
            `;
            }
        });

    }

    /**
     * Helper function to show error messages in chart containers
     */
    function showChartError(containerId, message) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = `
        <div class="flex items-center justify-center h-full">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="mt-2 text-red-500">${message}</p>
                <button class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                        onclick="initializeAnalytics()">
                    Try Again
                </button>
            </div>
        </div>
    `;
    }

    /**
     * Show "No Data" message for empty datasets
     */
    function showNoDataMessage(containerId, message = 'No data available yet') {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = `
        <div class="flex items-center justify-center h-full">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="mt-2 text-gray-500">${message}</p>
                <p class="mt-1 text-xs text-gray-400">Data will appear here as your news articles receive views.</p>
            </div>
        </div>
    `;
    }


    /**
     * Destroy all charts before re-initialization
     */
    function destroyAllCharts() {
        const chartCanvases = ['device-chart', 'activity-chart', 'read-time-chart'];

        // Loop through each canvas and destroy any charts attached to it
        chartCanvases.forEach(canvasId => {
            const canvas = document.getElementById(canvasId);
            if (canvas && typeof Chart === 'function' && typeof Chart.getChart === 'function') {
                const chart = Chart.getChart(canvas);
                if (chart) {
                    console.log(`Destroying existing chart on canvas: ${canvasId}`);
                    chart.destroy();
                }
            }
        });

        // Also clear direct references
        if (window.deviceChart) {
            window.deviceChart.destroy();
            window.deviceChart = null;
        }

        if (window.activityChart) {
            window.activityChart.destroy();
            window.activityChart = null;
        }

        if (window.readTimeChart) {
            window.readTimeChart.destroy();
            window.readTimeChart = null;
        }

        // Reset global chart references
        window.chartInstances = {
            deviceChart: null,
            activityChart: null,
            readTimeChart: null
        };

        console.log('All charts destroyed');
    }

    /**
     * Load overall statistics
     */
    async function loadOverallStats() {
        try {
            const response = await fetch('/api/admin/news/tracking/stats');
            if (!response.ok) throw new Error('Failed to fetch stats');

            const stats = await response.json();

            // Update stats displays
            updateElementText('stat-total-views', formatNumber(stats.total_views));
            updateElementText('stat-unique-visitors', formatNumber(stats.unique_devices));
            updateElementText('stat-avg-read-time', `${stats.avg_read_time} min`);
            updateElementText('stat-completion-rate', `${stats.completion_rate}%`);

            // Load trend data
            loadTrendData();

        } catch (error) {
            console.error('Error loading overall stats:', error);
        }
    }

    /**
     * Load trend data for percentage changes
     */
    async function loadTrendData() {
        try {
            const response = await fetch('/api/admin/news/tracking/trends');
            if (!response.ok) throw new Error('Failed to fetch trend data');

            const trends = await response.json();

            // Update trend indicators
            updateTrendIndicator('stat-views-change', trends.total_views_change);
            updateTrendIndicator('stat-visitors-change', trends.unique_viewers_change);
            updateTrendIndicator('stat-read-time-change', trends.avg_read_time_change);
            updateTrendIndicator('stat-completion-change', trends.completion_rate_change);

        } catch (error) {
            console.error('Error loading trend data:', error);
        }
    }

    /**
     * Update trend indicator with percentage change
     */
    function updateTrendIndicator(elementId, percentChange) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const isPositive = percentChange >= 0;
        const absChange = Math.abs(percentChange);

        element.innerHTML = `
            <span class="${isPositive ? 'text-green-600' : 'text-red-600'}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="${isPositive
            ? 'M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z'
            : 'M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z'}"
                        clip-rule="evenodd" />
                </svg>
                <span>${absChange}% ${isPositive ? 'increase' : 'decrease'}</span>
            </span>
        `;
    }

    /**
     * Load top performing news with no data handling
     */
    async function loadTopNews() {
        try {
            const response = await fetch('/api/admin/news/tracking/top');
            if (!response.ok) throw new Error('Failed to fetch top news');

            const topNews = await response.json();
            const tableBody = document.getElementById('top-news-table');
            if (!tableBody) return;

            if (!topNews || topNews.length === 0) {
                tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No data available yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Data will appear here once news articles are read by visitors.</p>
                    </td>
                </tr>
            `;
                return;
            }

            tableBody.innerHTML = '';

            topNews.slice(0, 5).forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-900 truncate max-w-xs font-medium">${item.title || 'Untitled'}</td>
                <td class="px-4 py-3 text-sm text-gray-500">${formatNumber(item.views)}</td>
                <td class="px-4 py-3 text-sm text-gray-500">${formatNumber(item.unique_viewers)}</td>
                <td class="px-4 py-3 text-sm text-gray-500">${item.avg_read_time} min</td>
                <td class="px-4 py-3 text-sm text-gray-500">${item.completion_rate}%</td>
            `;
                tableBody.appendChild(row);
            });

        } catch (error) {
            console.error('Error loading top news:', error);
            const tableBody = document.getElementById('top-news-table');
            if (tableBody) {
                tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-red-900">Error loading data</h3>
                        <p class="mt-1 text-sm text-gray-500">Please try again later.</p>
                    </td>
                </tr>
            `;
            }
        }
    }

// Modify loadDeviceBreakdown function
    async function loadDeviceBreakdown() {
        try {
            // Get container elements
            const statsContainer = document.getElementById('device-stats');
            const chartContainer = document.getElementById('device-chart-container');

            if (!statsContainer || !chartContainer) {
                console.error('Device stats container or chart container not found');
                return;
            }

            // Show loading state
            statsContainer.innerHTML = `
        <div class="animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-2/3 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
        </div>
    `;

            // Make sure the chart container has a canvas
            chartContainer.innerHTML = `<canvas id="device-chart" class="w-full h-64"></canvas>`;

            const response = await fetch('/api/admin/news/tracking/devices');
            if (!response.ok) throw new Error('Failed to fetch device data');

            const deviceData = await response.json();

            // Log the actual data received to debug
            console.log('Device data received:', deviceData);

            // Check if data is empty or invalid
            if (!deviceData || !Array.isArray(deviceData) || deviceData.length === 0) {
                console.log('No device data available');
                // Clear chart container
                chartContainer.innerHTML = `
                <div class="flex items-center justify-center h-64">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-2 text-gray-500">No device data available yet</p>
                    </div>
                </div>
            `;

                statsContainer.innerHTML = `
                <div class="text-center py-4">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2 text-gray-500">No device data available yet</p>
                    <p class="mt-1 text-xs text-gray-400">Data will appear as visitors view your news articles</p>
                </div>
            `;
                return;
            }

            // Make sure data is in the expected format
            const formattedData = deviceData.map(item => ({
                type: item.type || 'Unknown',
                percentage: parseFloat(item.percentage) || 0
            }));

            // Update device stats
            statsContainer.innerHTML = '';
            formattedData.forEach(device => {
                const deviceDiv = document.createElement('div');
                deviceDiv.className = 'flex items-center justify-between mb-2';
                deviceDiv.innerHTML = `
                <span class="text-gray-600">${device.type}</span>
                <span class="font-medium">${device.percentage}%</span>
            `;
                statsContainer.appendChild(deviceDiv);
            });

            // Create or update chart
            createDeviceChart(formattedData);

        } catch (error) {
            console.error('Error loading device breakdown:', error);

            // Show error message
            const statsContainer = document.getElementById('device-stats');
            const chartContainer = document.getElementById('device-chart-container');

            if (statsContainer) {
                statsContainer.innerHTML = `
                <div class="text-center py-4">
                    <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="mt-2 text-red-500">Error loading device data</p>
                </div>
            `;
            }

            if (chartContainer) {
                chartContainer.innerHTML = `
                <div class="flex items-center justify-center h-64">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="mt-2 text-red-500">Error loading chart</p>
                    </div>
                </div>
            `;
            }
        }
    }

    /**
     * Create device chart safely with improved error handling
     */
    function createDeviceChart(data) {
        try {
            const canvas = document.getElementById('device-chart');
            if (!canvas) {
                console.error('Device chart canvas element not found');
                return null;
            }

            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded');
                return null;
            }

            // Make sure data is valid
            if (!Array.isArray(data) || data.length === 0) {
                console.error('Invalid or empty device data:', data);
                return null;
            }

            // Ensure any existing chart is destroyed
            let existingChart;
            try {
                if (typeof Chart.getChart === 'function') {
                    existingChart = Chart.getChart(canvas);
                    if (existingChart) {
                        console.log('Destroying existing device chart');
                        existingChart.destroy();
                    }
                }
            } catch (err) {
                console.warn('Error checking for existing chart:', err);
            }

            // Also check for global reference
            if (window.deviceChart) {
                try {
                    console.log('Destroying global device chart reference');
                    window.deviceChart.destroy();
                } catch (err) {
                    console.warn('Error destroying global chart reference:', err);
                }
                window.deviceChart = null;
            }

            // Color palette
            const colors = [
                '#3b82f6', // Blue
                '#10b981', // Green
                '#f59e0b', // Yellow
                '#ef4444', // Red
                '#8b5cf6'  // Purple
            ];

            // Create new chart
            console.log('Creating new device chart with data:', data);
            const deviceChart = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.type),
                    datasets: [{
                        data: data.map(item => item.percentage),
                        backgroundColor: colors.slice(0, data.length),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false // We'll show the legend separately
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw}%`;
                                }
                            }
                        }
                    }
                }
            });

            // Store reference globally for cleanup
            window.deviceChart = deviceChart;

            // Also store in the chartInstances object
            if (window.chartInstances) {
                window.chartInstances.deviceChart = deviceChart;
            }

            console.log('Device chart created successfully');
            return deviceChart;
        } catch (error) {
            console.error('Error creating device chart:', error);

            // Show error in the chart container
            const chartContainer = document.getElementById('device-chart-container');
            if (chartContainer) {
                chartContainer.innerHTML = `
                <div class="flex items-center justify-center h-64">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="mt-2 text-red-500">Error creating chart</p>
                        <p class="mt-1 text-xs text-gray-500">Technical details: ${error.message}</p>
                    </div>
                </div>
            `;
            }

            return null;
        }
    }


    /**
     * Create activity chart safely
     */
    function createActivityChart(data, period) {
        const canvas = document.getElementById('activity-chart');
        if (!canvas || typeof Chart === 'undefined') {
            console.log('Cannot create activity chart: Canvas or Chart is undefined');
            return null;
        }

        // Ensure any existing chart is destroyed
        if (typeof Chart.getChart === 'function') {
            const existingChart = Chart.getChart(canvas);
            if (existingChart) {
                existingChart.destroy();
            }
        }

        try {
            // Format options based on period
            let timeUnit = 'day';
            let tooltipFormat = 'MMM D, YYYY';

            switch(period) {
                case 'day':
                    timeUnit = 'hour';
                    tooltipFormat = 'h:mm a';
                    break;
                case 'week':
                case 'month':
                    timeUnit = 'day';
                    tooltipFormat = 'MMM D, YYYY';
                    break;
                case 'all':
                    timeUnit = 'month';
                    tooltipFormat = 'MMM YYYY';
                    break;
            }

            activityChart = new Chart(canvas, {
                type: 'line',
                data: {
                    datasets: [
                        {
                            label: 'Views',
                            data: data.views,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.2
                        },
                        {
                            label: 'Read Time (min)',
                            data: data.readTime,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.2,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: timeUnit,
                                tooltipFormat: tooltipFormat,
                                displayFormats: {
                                    hour: 'h:mm a',
                                    day: 'MMM d',
                                    month: 'MMM yyyy'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Time Period'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Views'
                            },
                            beginAtZero: true
                        },
                        y1: {
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Read Time (min)'
                            },
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });

            // Store reference globally for cleanup
            window.activityChart = activityChart;
            return activityChart;
        } catch (error) {
            console.error('Error creating activity chart:', error);
            return null;
        }
    }

    /**
     * Load activity time series data with proper "no data" handling
     */
    async function loadActivityData(period) {
        try {
            // Get container element
            const chartContainer = document.getElementById('activity-chart-container');
            if (!chartContainer) return;

            // Show loading state (completely replace the content)
            chartContainer.innerHTML = `
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-500 mt-2">Loading chart...</p>
                </div>
            </div>
            <canvas id="activity-chart" class="w-full h-full"></canvas>
        `;

            const response = await fetch(`/api/admin/news/tracking/activity?period=${period}`);
            if (!response.ok) throw new Error('Failed to fetch activity data');

            const activityData = await response.json();

            // Check if data is empty or has no records - IMPORTANT: Completely replace the container content
            if (!activityData || !activityData.views || activityData.views.length === 0) {
                chartContainer.innerHTML = `
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-2 text-gray-500">No activity data available yet</p>
                        <p class="mt-1 text-xs text-gray-400">Data will appear as visitors view your news articles</p>
                    </div>
                </div>
            `;
                return; // Important: Exit the function here
            }

            // Ensure canvas exists and ONLY the canvas (replace any existing content)
            chartContainer.innerHTML = `<canvas id="activity-chart" class="w-full h-full"></canvas>`;

            // Create or update chart
            createActivityChart(activityData, period);

        } catch (error) {
            console.error('Error loading activity data:', error);

            // Show error message (completely replace the content)
            const chartContainer = document.getElementById('activity-chart-container');
            if (chartContainer) {
                chartContainer.innerHTML = `
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="mt-2 text-red-500">Error loading activity data</p>
                        <button class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                                onclick="loadActivityData('${period}')">
                            Try Again
                        </button>
                    </div>
                </div>
            `;
            }
        }
    }

    /**
     * Load read time distribution with proper "no data" handling
     */
    async function loadReadTimeDistribution() {
        try {
            // Get container element
            const chartContainer = document.getElementById('read-time-chart-container');
            if (!chartContainer) return;

            // Show loading state (completely replace the content)
            chartContainer.innerHTML = `
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-500 mt-2">Loading chart...</p>
                </div>
            </div>
            <canvas id="read-time-chart" class="w-full h-full"></canvas>
        `;

            const response = await fetch('/api/admin/news/tracking/read-time');
            if (!response.ok) throw new Error('Failed to fetch read time distribution');

            const data = await response.json();

            // Check if data is empty - IMPORTANT: Completely replace the container content
            if (!data || !data.labels || data.labels.length === 0 || !data.data || data.data.length === 0) {
                chartContainer.innerHTML = `
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-2 text-gray-500">No read time data available yet</p>
                        <p class="mt-1 text-xs text-gray-400">Data will appear as visitors read your news articles</p>
                    </div>
                </div>
            `;
                return; // Important: Exit the function here
            }

            // Ensure we have ONLY the canvas element (replace any existing content)
            chartContainer.innerHTML = `<canvas id="read-time-chart" class="w-full h-full"></canvas>`;

            // Create or update chart
            createReadTimeChart(data);

        } catch (error) {
            console.error('Error loading read time distribution:', error);

            // Show error message (completely replace the content)
            const chartContainer = document.getElementById('read-time-chart-container');
            if (chartContainer) {
                chartContainer.innerHTML = `
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="mt-2 text-red-500">Error loading read time data</p>
                        <button class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                                onclick="loadReadTimeDistribution()">
                            Try Again
                        </button>
                    </div>
                </div>
            `;
            }
        }
    }

    /**
     * Create read time chart safely
     */
    function createReadTimeChart(data) {
        const canvas = document.getElementById('read-time-chart');
        if (!canvas || typeof Chart === 'undefined') {
            console.log('Cannot create read time chart: Canvas or Chart is undefined');
            return null;
        }

        // Ensure any existing chart is destroyed
        if (typeof Chart.getChart === 'function') {
            const existingChart = Chart.getChart(canvas);
            if (existingChart) {
                existingChart.destroy();
            }
        }

        try {
            readTimeChart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Percentage of Readers',
                            data: data.data,
                            backgroundColor: [
                                'rgba(239, 68, 68, 0.7)',    // Red - <1 min
                                'rgba(245, 158, 11, 0.7)',   // Yellow - 1-3 min
                                'rgba(16, 185, 129, 0.7)',   // Green - 3-5 min
                                'rgba(59, 130, 246, 0.7)',   // Blue - 5-10 min
                                'rgba(139, 92, 246, 0.7)'    // Purple - >10 min
                            ],
                            borderWidth: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.raw}% of readers`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Percentage of Readers'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Store reference globally for cleanup
            window.readTimeChart = readTimeChart;
            return readTimeChart;
        } catch (error) {
            console.error('Error creating read time chart:', error);
            return null;
        }
    }
    /**
     * Set up modal handling
     */
    function setupModalHandling() {
        const modals = document.querySelectorAll('.modal');
        const modalCloseButtons = document.querySelectorAll('.modal-close');
        const modalOverlays = document.querySelectorAll('.modal-overlay');

        modalCloseButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('.modal');
                closeModal(modal);
            });
        });

        modalOverlays.forEach(overlay => {
            overlay.addEventListener('click', () => {
                const modal = overlay.closest('.modal');
                closeModal(modal);
            });
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                modals.forEach(modal => {
                    closeModal(modal);
                });
            }
        });
    }

    /**
     * Close modal
     */
    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0', 'pointer-events-none');

        // Reset delete_image value when news editor modal is closed
        if (modal.id === 'news-editor-modal') {
            const deleteImageInput = document.getElementById('delete_image');
            if (deleteImageInput) {
                deleteImageInput.value = '0';
            }
        }
    }

    /**
     * Update element text if element exists
     */
    function updateElementText(id, text) {
        const element = document.getElementById(id);
        if (element) element.textContent = text;
    }

    /**
     * Format number with commas for thousands
     */
    function formatNumber(num) {
        return num ? num.toLocaleString() : '0';
    }
});
