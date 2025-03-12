/**
 * News Categories Management Module
 * Handles CRUD operations for News Categories
 */

// In news-categories-standalone.js:
if (!window.location.pathname.includes('/admin/news/categories')) {
    console.log('Not on news categories page, skipping initialization');
    // Exit early to prevent any code from running
    throw new Error('Skipping script - not on news categories page');
}


document.addEventListener('DOMContentLoaded', function() {
    // Debug information
    console.log('News Categories JS loaded!');
    console.log('Page URL:', window.location.pathname);

    // State
    let newsCategories = []; // Changed variable name from categories to newsCategories
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalPages = 1;
    let currentCategoryId = null;

    // Initialize Sortable for drag and drop functionality
    let sortableInstance = null;

    // CSRF token setup for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    console.log('CSRF Token available:', !!csrfToken);



    // Initialize
    fetchNewsCategories(); // Changed function name
    initializeSortable();

    // Event Listeners
    const refreshBtn = document.getElementById('refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', fetchNewsCategories);
        console.log('Refresh button found and initialized');
    } else {
        console.warn('Refresh button not found');
    }

    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', filterNewsCategories);
        console.log('Search input found and initialized');
    } else {
        console.warn('Search input not found');
    }

    const newCategoryBtn = document.getElementById('new-category-btn');
    if (newCategoryBtn) {
        newCategoryBtn.addEventListener('click', () => {
            openEditorModal();
        });
        console.log('New category button found and initialized');
    } else {
        console.warn('New category button not found');
    }

    const saveCategoryBtn = document.getElementById('save-category-btn');
    if (saveCategoryBtn) {
        saveCategoryBtn.addEventListener('click', saveNewsCategory);
        console.log('Save category button found and initialized');
    } else {
        console.warn('Save category button not found');
    }

    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            if (currentCategoryId) {
                deleteNewsCategory(currentCategoryId);
            }
        });
        console.log('Confirm delete button found and initialized');
    } else {
        console.warn('Confirm delete button not found');
    }

    const saveOrderBtn = document.getElementById('save-order-btn');
    if (saveOrderBtn) {
        saveOrderBtn.addEventListener('click', saveOrder);
        console.log('Save order button found and initialized');
    } else {
        console.warn('Save order button not found');
    }

    // Modal handling
    setupModalHandling();

    /**
     * Fetch all news categories from API
     */
    async function fetchNewsCategories() {
        try {
            console.log('Fetching news categories...');
            const categoriesTable = document.getElementById('categories-table-body');
            if (categoriesTable) {
                categoriesTable.innerHTML = `
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-500">
                        <div class="flex justify-center">
                            <svg class="h-10 w-10 text-gray-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div class="mt-2">Loading categories...</div>
                    </td>
                </tr>
            `;
            } else {
                console.warn('Categories table body not found');
            }

            // Add cache buster to prevent caching
            const timestamp = new Date().getTime();
            // Use the news-categories endpoint, not categories
            const url = `/api/news-categories?_=${timestamp}`;
            console.log('Fetching from URL:', url);

            const response = await fetch(url);
            console.log('Response status:', response.status);

            if (!response.ok) throw new Error(`Failed to fetch news categories: ${response.status} ${response.statusText}`);

            newsCategories = await response.json();
            console.log('Fetched news categories:', newsCategories);

            // Sort news categories by name
            newsCategories.sort((a, b) => a.name.localeCompare(b.name));

            renderNewsCategories();
            renderSortableNewsCategories();
        } catch (error) {
            console.error('Error fetching news categories:', error);
            const categoriesTable = document.getElementById('categories-table-body');
            if (categoriesTable) {
                categoriesTable.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-10">
                        <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-red-800">Error loading news categories</h3>
                        <p class="mt-1 text-gray-500">${error.message}</p>
                    </td>
                </tr>
            `;
            }
        }
    }

    /**
     * Filter news categories based on search input
     */
    function filterNewsCategories() {
        console.log('Filtering news categories...');
        currentPage = 1;
        renderNewsCategories();
    }

    /**
     * Render news categories table with filtering and pagination
     */
    function renderNewsCategories() {
        const categoriesTable = document.getElementById('categories-table-body');
        if (!categoriesTable) {
            console.warn('Categories table body not found for rendering');
            return;
        }

        categoriesTable.innerHTML = '';

        const searchInput = document.getElementById('search');
        const searchQuery = searchInput?.value?.toLowerCase() || '';
        console.log('Search query:', searchQuery);

        // Apply filters
        let filteredCategories = newsCategories.filter(item => {
            const nameMatch = (item.name || '').toLowerCase().includes(searchQuery);
            const slugMatch = (item.slug || '').toLowerCase().includes(searchQuery);
            const descriptionMatch = (item.description || '').toLowerCase().includes(searchQuery);

            return nameMatch || slugMatch || descriptionMatch;
        });
        console.log('Filtered news categories count:', filteredCategories.length);

        // Calculate pagination
        totalPages = Math.ceil(filteredCategories.length / itemsPerPage);
        if (totalPages === 0) totalPages = 1;
        console.log('Total pages:', totalPages, 'Current page:', currentPage);

        // Slice for current page
        const pageCategories = filteredCategories.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

        if (pageCategories.length === 0) {
            categoriesTable.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-10">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No categories found</h3>
                    <p class="mt-1 text-gray-500">Try adjusting your search criteria.</p>
                </td>
            </tr>
        `;
            return;
        }

        // Render category items
        pageCategories.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';

            row.innerHTML = `
            <td class="py-4 pl-4 pr-3 text-sm sm:pl-6">
                <div class="font-medium text-gray-900 truncate max-w-xs">${item.name || 'Unnamed Category'}</div>
                <div class="text-gray-500 truncate max-w-xs">${item.description || ''}</div>
            </td>
            <td class="px-3 py-4 text-sm text-gray-500">
                ${item.slug || ''}
            </td>
            <td class="px-3 py-4 text-sm text-gray-500">
                <span class="px-2 py-1 text-xs rounded-full ${item.news_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                    ${item.news_count || 0} articles
                </span>
            </td>
            <td class="py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <div class="flex justify-end space-x-2">
                    <button class="action-btn edit-btn" data-id="${item.id}" aria-label="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button class="action-btn delete-btn" data-id="${item.id}" data-name="${item.name || 'Unnamed Category'}" aria-label="Delete" ${item.news_count > 0 ? 'disabled title="Cannot delete category with articles"' : ''}>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" ${item.news_count > 0 ? 'class="text-gray-400"' : ''}>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </td>
        `;

            categoriesTable.appendChild(row);
        });

        // Add event listeners to buttons
        attachActionButtonListeners();
    }

    /**
     * Render sortable news categories list
     */
    function renderSortableNewsCategories() {
        const sortableContainer = document.getElementById('sortable-categories');
        if (!sortableContainer) {
            console.warn('Sortable container not found');
            return;
        }

        // First, destroy any existing sortable instance
        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
            console.log('Existing sortable instance destroyed');
        }

        // Clear container
        sortableContainer.innerHTML = '';

        if (newsCategories.length === 0) {
            sortableContainer.innerHTML = `
            <div class="flex justify-center items-center h-16 bg-gray-100 rounded-lg">
                <span class="text-gray-500">No categories available</span>
            </div>
        `;
            return;
        }

        console.log('Rendering sortable news categories:', newsCategories.length);

        // Create sortable items
        newsCategories.forEach(category => {
            const item = document.createElement('div');
            item.className = 'bg-white border rounded-lg p-3 flex justify-between items-center cursor-move';
            item.setAttribute('data-id', category.id);

            item.innerHTML = `
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.20l-1 1H15a1 1 0 110 2H12.20l-1 1H17a1 1 0 110 2H10.20l-1 1H8v2H5a1 1 0 01-.707-1.707l7-7A1 1 0 0112 7v-1h2a1 1 0 100-2h-3V2a1 1 0 011-1h2a1 1 0 011 1v1h1a1 1 0 110 2h-1v1h3a1 1 0 110 2h-3v1h1a1 1 0 110 2h-1v2a1 1 0 11-2 0v-2H7v1a1 1 0 11-2 0v-1H4a1 1 0 110-2h1v-1H2a1 1 0 110-2h3v-1H4a1 1 0 110-2h1V4a1 1 0 011-1h1z" />
                </svg>
                <span class="font-medium">${category.name}</span>
            </div>
            <span class="px-2 py-1 text-xs rounded-full ${category.news_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                ${category.news_count || 0} articles
            </span>
        `;

            sortableContainer.appendChild(item);
        });

        // Initialize Sortable.js
        initializeSortable();
    }

    /**
     * Initialize Sortable.js for drag and drop
     */
    function initializeSortable() {
        const sortableContainer = document.getElementById('sortable-categories');
        if (!sortableContainer) {
            console.warn('Sortable container not found for initialization');
            return;
        }

        // Check if Sortable is available
        if (typeof Sortable === 'undefined') {
            console.error('Sortable.js is not loaded');
            return;
        }

        console.log('Initializing Sortable.js');
        sortableInstance = Sortable.create(sortableContainer, {
            animation: 150,
            ghostClass: 'bg-pink-50',
            handle: '.cursor-move',
            onEnd: function(evt) {
                // Enable save button when order changes
                const saveButton = document.getElementById('save-order-btn');
                if (saveButton) {
                    saveButton.disabled = false;
                    console.log('Save order button enabled');
                }
            }
        });
    }

    /**
     * Save the current category order
     */
    async function saveOrder() {
        try {
            console.log('Saving news categories order...');
            const sortableContainer = document.getElementById('sortable-categories');
            if (!sortableContainer) return;

            const saveButton = document.getElementById('save-order-btn');
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

            // Get the ordered IDs
            const orderedItems = sortableContainer.querySelectorAll('[data-id]');
            const orderedIds = Array.from(orderedItems).map(item => parseInt(item.getAttribute('data-id')));
            console.log('Ordered IDs:', orderedIds);

            // Use correct news-categories API endpoint
            const url = '/api/news-categories/order';
            console.log('Sending order to URL:', url);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ordered_ids: orderedIds
                })
            });
            console.log('Order save response status:', response.status);

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to save order');
            }

            const result = await response.json();
            console.log('Order save result:', result);

            // Reset save button
            if (saveButton) {
                saveButton.disabled = true;
                saveButton.innerHTML = 'Save Order';
            }

            // Show success message
            alert(result.message || 'Category order saved successfully');

            // Refresh categories to get latest order
            await fetchNewsCategories();

        } catch (error) {
            console.error('Error saving category order:', error);
            alert(`Error: ${error.message || 'Failed to save category order'}`);

            // Reset save button
            const saveButton = document.getElementById('save-order-btn');
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = 'Save Order';
            }
        }
    }


    /**
     * Attach event listeners to action buttons
     */
    function attachActionButtonListeners() {
        console.log('Attaching action button listeners');

        const editButtons = document.querySelectorAll('.edit-btn');
        console.log(`Found ${editButtons.length} edit buttons`);
        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                console.log('Edit button clicked for ID:', id);
                if (id) openEditorModal(id);
            });
        });

        const deleteButtons = document.querySelectorAll('.delete-btn');
        console.log(`Found ${deleteButtons.length} delete buttons`);
        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                console.log('Delete button clicked for ID:', id, 'Name:', name);
                if (id && !button.disabled) openDeleteModal(id, name);
            });
        });
    }

    /**
     * Open Editor Modal
     */
    /**
     * Open Editor Modal
     */
    async function openEditorModal(id = null) {
        console.log('Opening editor modal for ID:', id);
        const modal = document.getElementById('category-editor-modal');
        if (!modal) {
            console.error('Editor modal not found');
            return;
        }

        const form = document.getElementById('category-form');
        const editorTitle = document.getElementById('editor-title');
        const formMethod = document.getElementById('form-method');

        // Reset form
        if (form) {
            form.reset();
            console.log('Form reset');
        }

        if (id) {
            // Edit mode - fetch data from API
            if (editorTitle) editorTitle.textContent = 'Edit Category';
            if (formMethod) formMethod.value = 'PUT';
            console.log('Edit mode, method set to PUT');

            try {
                // Find category in local array first
                console.log('Looking for category in local array with ID:', id);
                let category = newsCategories.find(c => c.id == id);

                if (!category) {
                    // If not found in local array, fetch from API
                    console.log('Category not found in local array, fetching from API');
                    // Use correct news-categories API endpoint
                    const url = `/api/news-categories/${id}`;
                    console.log('Fetching category details from:', url);

                    const response = await fetch(url);
                    console.log('Category details response status:', response.status);

                    if (!response.ok) throw new Error('Category not found');

                    category = await response.json();
                    console.log('Fetched category details:', category);
                }

                const formCategoryId = document.getElementById('form-category-id');
                const nameInput = document.getElementById('name');
                const slugInput = document.getElementById('slug');
                const descriptionInput = document.getElementById('description');

                // Fill form fields
                if (formCategoryId) formCategoryId.value = category.id || '';
                if (nameInput) nameInput.value = category.name || '';
                if (slugInput) slugInput.value = category.slug || '';
                if (descriptionInput) descriptionInput.value = category.description || '';
                console.log('Form fields populated');

                // Store current category ID
                currentCategoryId = id;
                console.log('Current category ID set to:', currentCategoryId);

            } catch (error) {
                console.error('Error fetching category for editing:', error);
                alert('Failed to load category data for editing.');
                return;
            }
        } else {
            // Create mode
            if (editorTitle) editorTitle.textContent = 'Add Category';
            if (formMethod) formMethod.value = 'POST';
            console.log('Create mode, method set to POST');

            const formCategoryId = document.getElementById('form-category-id');
            if (formCategoryId) formCategoryId.value = '';

            // Reset current category ID
            currentCategoryId = null;
            console.log('Current category ID reset to null');
        }

        // Show modal
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
        console.log('Modal displayed');

        // Focus on name field
        setTimeout(() => {
            const nameInput = document.getElementById('name');
            if (nameInput) {
                nameInput.focus();
                console.log('Focus set on name input');
            }
        }, 100);
    }

    /**
     * Open Delete Confirmation Modal
     */
    function openDeleteModal(id, name) {
        console.log('Opening delete modal for ID:', id, 'Name:', name);
        const modal = document.getElementById('delete-modal');
        if (!modal) {
            console.error('Delete modal not found');
            return;
        }

        const nameElement = document.getElementById('delete-category-name');
        if (nameElement) {
            nameElement.textContent = name || 'this category';
            console.log('Delete modal name set to:', nameElement.textContent);
        }

        currentCategoryId = id;
        console.log('Current category ID set to:', currentCategoryId);

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
        console.log('Delete modal displayed');
    }

    /**
     * Save News Category (Create or Update) using API
     */
    async function saveNewsCategory() {
        console.log('Saving news category...');
        const form = document.getElementById('category-form');
        const formMethodElement = document.getElementById('form-method');
        if (!form || !formMethodElement) {
            console.error('Form or method element not found');
            return;
        }

        const formData = new FormData(form);
        const method = formMethodElement.value;
        console.log('Form method:', method);

        try {
            // Use correct news-categories API endpoint instead of categories
            let url = '/api/news-categories';
            let requestMethod = 'POST';

            if (method === 'PUT' && currentCategoryId) {
                url = `/api/news-categories/${currentCategoryId}`;
                requestMethod = 'PUT';
                console.log('Update URL:', url);
            } else {
                console.log('Create URL:', url);
            }

            // Convert FormData to object
            const categoryData = {};
            formData.forEach((value, key) => {
                categoryData[key] = value;
            });
            console.log('Category data to send:', categoryData);

            // Disable save button and show loading state
            const saveButton = document.getElementById('save-category-btn');
            if (saveButton) {
                saveButton.disabled = true;
                saveButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving...
            `;
                console.log('Save button disabled and showing loading state');
            }

            console.log('Sending request to:', url);
            console.log('Method:', requestMethod);
            console.log('Data:', categoryData);
            console.log('CSRF Token:', csrfToken);

            const response = await fetch(url, {
                method: requestMethod,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(categoryData)
            });
            console.log('Save response status:', response.status);

            // Re-enable save button
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = 'Save';
                console.log('Save button re-enabled');
            }

            if (!response.ok) {
                const responseText = await response.text();
                console.error('Error response text:', responseText);

                let errorData;
                try {
                    errorData = JSON.parse(responseText);
                } catch (e) {
                    throw new Error(`Server returned non-JSON response: ${responseText.substring(0, 100)}...`);
                }

                if (errorData.errors) {
                    // Format validation errors for better display
                    const errorMessages = Object.entries(errorData.errors)
                        .map(([field, msgs]) => `${field}: ${msgs.join(', ')}`)
                        .join('\n');
                    throw new Error(`Validation errors:\n${errorMessages}`);
                }
                throw new Error(errorData.message || `Error ${response.status}: Failed to save category`);
            }

            const responseText = await response.text();
            console.log('Response text:', responseText);

            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                throw new Error('Invalid response from server');
            }

            console.log('Save result:', result);

            // Close modal and refresh list
            const editorModal = document.getElementById('category-editor-modal');
            if (editorModal) {
                closeModal(editorModal);
                console.log('Editor modal closed');
            }

            // Refresh categories
            await fetchNewsCategories();

            // Show success message
            alert(result.message || 'Category saved successfully');

        } catch (error) {
            console.error('Error saving category:', error);
            alert(`Error: ${error.message || 'Failed to save category'}`);

            // Re-enable save button if still disabled
            const saveButton = document.getElementById('save-category-btn');
            if (saveButton && saveButton.disabled) {
                saveButton.disabled = false;
                saveButton.innerHTML = 'Save';
                console.log('Save button re-enabled after error');
            }
        }
    }

    /**
     * Delete News Category using API
     */
    async function deleteNewsCategory(id) {
        if (!id) {
            console.warn('No category ID provided for deletion');
            return;
        }

        console.log('Deleting news category with ID:', id);
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
                console.log('Delete button disabled and showing loading state');
            }

            // Use correct news-categories API endpoint
            const url = `/api/news-categories/${id}`;
            console.log('Sending delete request to:', url);

            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });
            console.log('Delete response status:', response.status);

            // Re-enable delete button
            if (deleteButton) {
                deleteButton.disabled = false;
                deleteButton.innerHTML = 'Delete';
                console.log('Delete button re-enabled');
            }

            if (!response.ok) {
                const responseText = await response.text();
                console.error('Error response text:', responseText);

                let errorData;
                try {
                    errorData = JSON.parse(responseText);
                } catch (e) {
                    throw new Error(`Server returned non-JSON response: ${responseText.substring(0, 100)}...`);
                }

                throw new Error(errorData.message || 'Failed to delete category');
            }

            const responseText = await response.text();
            console.log('Response text:', responseText);

            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                throw new Error('Invalid response from server');
            }

            console.log('Delete result:', result);

            // Close modal and refresh list
            const deleteModal = document.getElementById('delete-modal');
            if (deleteModal) {
                closeModal(deleteModal);
                console.log('Delete modal closed');
            }

            // Refresh categories
            await fetchNewsCategories();

            // Show success message
            alert(result.message || 'Category deleted successfully');

        } catch (error) {
            console.error('Error deleting category:', error);
            alert(`Error: ${error.message || 'Failed to delete category'}`);

            // Re-enable delete button if still disabled
            const deleteButton = document.getElementById('confirm-delete-btn');
            if (deleteButton && deleteButton.disabled) {
                deleteButton.disabled = false;
                deleteButton.innerHTML = 'Delete';
                console.log('Delete button re-enabled after error');
            }
        }
    }

    /**
     * Set up modal handling
     */
    function setupModalHandling() {
        console.log('Setting up modal handling');
        const modals = document.querySelectorAll('.modal');
        const modalCloseButtons = document.querySelectorAll('.modal-close');
        const modalOverlays = document.querySelectorAll('.modal-overlay');

        console.log(`Found ${modals.length} modals, ${modalCloseButtons.length} close buttons, ${modalOverlays.length} overlays`);

        modalCloseButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('.modal');
                console.log('Close button clicked for modal:', modal?.id);
                closeModal(modal);
            });
        });

        modalOverlays.forEach(overlay => {
            overlay.addEventListener('click', () => {
                const modal = overlay.closest('.modal');
                console.log('Overlay clicked for modal:', modal?.id);
                closeModal(modal);
            });
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                console.log('ESC key pressed, closing all modals');
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
        if (!modal) {
            console.warn('No modal provided to close');
            return;
        }
        console.log('Closing modal:', modal.id);
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0', 'pointer-events-none');
    }

    /**
     * Generate slug from name (for client-side convenience)
     */
    function generateSlug(name) {
        if (!name) return '';
        return name
            .toLowerCase()
            .replace(/[^\w\s-]/g, '') // Remove non-word chars
            .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
            .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
    }

    // Add event listener to generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    if (nameInput && slugInput) {
        console.log('Setting up name and slug input listeners');
        nameInput.addEventListener('input', function() {
            // Only auto-generate slug if the slug field is empty or untouched
            if (!slugInput.value || slugInput.getAttribute('data-auto-generated') === 'true') {
                const generatedSlug = generateSlug(this.value);
                console.log('Auto-generating slug:', generatedSlug);
                slugInput.value = generatedSlug;
                slugInput.setAttribute('data-auto-generated', 'true');
            }
        });

        // When user manually edits slug, stop auto-generating
        slugInput.addEventListener('input', function() {
            console.log('Slug manually edited, disabling auto-generation');
            slugInput.setAttribute('data-auto-generated', 'false');
        });
    } else {
        console.warn('Name or slug input not found');
    }
});
