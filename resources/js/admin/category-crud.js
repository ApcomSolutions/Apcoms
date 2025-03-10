/**
 * CategoryCrud.js - JavaScript for the Category Management Dashboard
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the categories management page
    const categoriesTableBody = document.getElementById('categories-table-body');

    // Only initialize if we're on the correct page
    if (!categoriesTableBody) {
        console.log('Not on the categories management page, skipping initialization');
        return; // Exit early if we're not on the right page
    }

    console.log('Initializing Category Management dashboard');

    // State
    let categories = [];
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalPages = 1;
    let currentCategoryId = null;
    let currentCategoryName = null;

    // CSRF token setup for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.warn('CSRF token not found. AJAX requests might fail.');
    }

    // Initialize
    fetchCategories();

    // Event Listeners
    const refreshBtn = document.getElementById('refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', fetchCategories);
    }

    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', filterCategories);
    }

    const prevPage = document.getElementById('prev-page');
    if (prevPage) {
        prevPage.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderCategories();
            }
        });
    }

    const nextPage = document.getElementById('next-page');
    if (nextPage) {
        nextPage.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderCategories();
            }
        });
    }

    // New Category Button
    const newCategoryBtn = document.getElementById('new-category-btn');
    if (newCategoryBtn) {
        newCategoryBtn.addEventListener('click', () => {
            openEditorModal();
        });
    }

    // Save Category Button
    const saveCategoryBtn = document.getElementById('save-category-btn');
    if (saveCategoryBtn) {
        saveCategoryBtn.addEventListener('click', saveCategory);
    }

    // Generate slug from name - on blur and on input
    const nameInput = document.getElementById('name');
    if (nameInput) {
        // Generate slug on input (real-time)
        nameInput.addEventListener('input', function() {
            const name = this.value;
            const slugInput = document.getElementById('slug');

            // Only generate slug if it's empty or user hasn't modified it
            if (slugInput && slugInput.dataset.autogenerated === 'true') {
                slugInput.value = generateSlug(name);
            }
        });

        // Also generate on blur for good measure
        nameInput.addEventListener('blur', function() {
            const name = this.value;
            const slugInput = document.getElementById('slug');

            // Only generate slug if it's empty or user hasn't modified it
            if (slugInput && (!slugInput.value || slugInput.dataset.autogenerated === 'true')) {
                slugInput.value = generateSlug(name);
                slugInput.dataset.autogenerated = 'true';
            }
        });
    }

    const slugInput = document.getElementById('slug');
    if (slugInput) {
        slugInput.addEventListener('input', function() {
            // User has manually edited the slug
            this.dataset.autogenerated = 'false';
        });
    }

    // Delete confirmation
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            if (currentCategoryId) {
                deleteCategory(currentCategoryId);
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

    // Fetch Categories from API
    async function fetchCategories() {
        try {
            if (!categoriesTableBody) return;

            categoriesTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading categories...</td></tr>';

            const response = await fetch('/api/categories');
            if (!response.ok) throw new Error('Failed to fetch categories');

            categories = await response.json();
            renderCategories();
        } catch (error) {
            console.error('Error fetching categories:', error);
            if (categoriesTableBody) {
                categoriesTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading categories. Please try again.</td></tr>';
            }
        }
    }

    // Filter Categories
    function filterCategories() {
        // Reset to first page when filtering
        currentPage = 1;
        renderCategories();
    }

    // Render Categories Table
    function renderCategories() {
        if (!categoriesTableBody) return;

        categoriesTableBody.innerHTML = '';

        const searchInput = document.getElementById('search');
        if (!searchInput) {
            console.warn('Search input not found');
            return;
        }

        // Apply filters
        const searchQuery = searchInput.value.toLowerCase();

        let filteredCategories = categories.filter(category => {
            const name = category.name || '';
            const slug = category.slug || '';
            const description = category.description || '';

            return name.toLowerCase().includes(searchQuery) ||
                slug.toLowerCase().includes(searchQuery) ||
                description.toLowerCase().includes(searchQuery);
        });

        // Calculate pagination
        totalPages = Math.ceil(filteredCategories.length / itemsPerPage);
        if (totalPages === 0) totalPages = 1;

        // Update pagination UI
        const totalCategoriesElement = document.getElementById('total-categories');
        const pageStartElement = document.getElementById('page-start');
        const pageEndElement = document.getElementById('page-end');

        if (totalCategoriesElement) {
            totalCategoriesElement.textContent = filteredCategories.length;
        }

        if (pageStartElement) {
            pageStartElement.textContent = filteredCategories.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        }

        if (pageEndElement) {
            pageEndElement.textContent = Math.min(currentPage * itemsPerPage, filteredCategories.length);
        }

        // Render pagination
        renderPagination();

        // Slice for current page
        const pageCategories = filteredCategories.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

        if (pageCategories.length === 0) {
            categoriesTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No categories found matching your filters</td></tr>';
            return;
        }

        // Render categories
        pageCategories.forEach(category => {
            const row = document.createElement('tr');
            row.classList.add('hover:bg-gray-50');

            const insightsCount = category.insights_count || 0;

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900">${category.name}</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${category.slug}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    ${category.description || 'No description'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${insightsCount}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-900 edit-category" data-id="${category.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                        <button class="text-red-600 hover:text-red-900 delete-category" data-id="${category.id}" data-name="${category.name}" data-insights="${insightsCount}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </td>
            `;

            categoriesTableBody.appendChild(row);
        });

        // Add event listeners to buttons
        document.querySelectorAll('.edit-category').forEach(button => {
            button.addEventListener('click', () => {
                const categoryId = button.getAttribute('data-id');
                openEditorModal(categoryId);
            });
        });

        document.querySelectorAll('.delete-category').forEach(button => {
            button.addEventListener('click', () => {
                const categoryId = button.getAttribute('data-id');
                const categoryName = button.getAttribute('data-name');
                const insightsCount = parseInt(button.getAttribute('data-insights') || '0');
                openDeleteModal(categoryId, categoryName, insightsCount);
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
            button.className = `relative inline-flex items-center px-4 py-2 border ${currentPage === pageNum ? 'border-indigo-500 bg-indigo-50 text-indigo-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'}`;
            button.textContent = pageNum;

            button.addEventListener('click', () => {
                currentPage = pageNum;
                renderCategories();
            });

            paginationContainer.appendChild(button);
        }
    }

    // Open Editor Modal
    async function openEditorModal(categoryId = null) {
        const modal = document.getElementById('category-editor-modal');
        const form = document.getElementById('category-form');
        const editorTitle = document.getElementById('editor-title');
        const formMethod = document.getElementById('form-method');

        if (!modal || !form || !editorTitle || !formMethod) {
            console.error('Required editor modal elements not found');
            return;
        }

        // Reset form
        form.reset();

        if (categoryId) {
            // Edit mode - fetch data from API
            editorTitle.textContent = 'Edit Category';
            formMethod.value = 'PUT';

            try {
                const category = categories.find(cat => cat.id == categoryId);
                if (!category) {
                    throw new Error('Category not found');
                }

                const formCategoryId = document.getElementById('form-category-id');
                const nameInput = document.getElementById('name');
                const slugInput = document.getElementById('slug');
                const descriptionInput = document.getElementById('description');

                if (!formCategoryId || !nameInput || !slugInput || !descriptionInput) {
                    console.error('Form elements not found');
                    throw new Error('Form elements not found');
                }

                // Fill form fields
                formCategoryId.value = category.id;
                nameInput.value = category.name || '';
                slugInput.value = category.slug || '';
                slugInput.dataset.autogenerated = 'false';
                descriptionInput.value = category.description || '';

                // Store current category ID for save operation
                currentCategoryId = categoryId;

            } catch (error) {
                console.error('Error fetching category for editing:', error);
                alert('Failed to load category data for editing.');
                return;
            }
        } else {
            // Create mode
            editorTitle.textContent = 'Create New Category';
            formMethod.value = 'POST';

            const formCategoryId = document.getElementById('form-category-id');
            const slugInput = document.getElementById('slug');

            if (formCategoryId) {
                formCategoryId.value = '';
            }

            if (slugInput) {
                slugInput.dataset.autogenerated = 'true';
            }

            // Reset current category ID
            currentCategoryId = null;
        }

        // Show modal
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');

        // Focus on name field
        setTimeout(() => {
            const nameInput = document.getElementById('name');
            if (nameInput) {
                nameInput.focus();
            }
        }, 100);
    }

    // Open Delete Confirmation Modal
    function openDeleteModal(categoryId, categoryName, insightsCount) {
        const modal = document.getElementById('delete-modal');
        const nameElement = document.getElementById('delete-category-name');
        const warningElement = document.getElementById('category-has-insights');

        if (!modal || !nameElement || !warningElement) {
            console.error('Delete modal elements not found');
            return;
        }

        nameElement.textContent = categoryName;
        currentCategoryId = categoryId;
        currentCategoryName = categoryName;

        // Show warning if category has insights
        if (insightsCount > 0) {
            warningElement.classList.remove('hidden');
        } else {
            warningElement.classList.add('hidden');
        }

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
    }

    // Save Category (Create or Update) using API
    async function saveCategory() {
        const form = document.getElementById('category-form');
        const formMethodElement = document.getElementById('form-method');

        if (!form || !formMethodElement) {
            console.error('Required form elements not found');
            return;
        }

        const formData = new FormData(form);
        const method = formMethodElement.value;
        const categoryId = document.getElementById('form-category-id').value;

        // Convert FormData to object
        const categoryData = {};
        formData.forEach((value, key) => {
            categoryData[key] = value;
        });

        try {
            let url = '/api/categories';
            let requestMethod = 'POST';

            if (method === 'PUT' && categoryId) {
                url = `/api/categories/${categoryId}`;
                requestMethod = 'PUT';
            }

            // Add debug to see exactly what's being sent
            console.log('Sending request to:', url);
            console.log('Method:', requestMethod);
            console.log('Data:', categoryData);
            console.log('CSRF Token:', csrfToken);

            const response = await fetch(url, {
                method: requestMethod,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify(categoryData)
            });

            // Better error handling
            const responseText = await response.text();
            console.log('Response text:', responseText);

            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Invalid JSON response:', responseText);
                throw new Error('Server returned an invalid response');
            }

            if (!response.ok) {
                throw new Error(result.message || 'Failed to save category');
            }

            // Close modal and refresh list
            const editorModal = document.getElementById('category-editor-modal');
            if (editorModal) {
                closeModal(editorModal);
            }
            fetchCategories();

            // Show success message
            alert(result.message || 'Category saved successfully');

        } catch (error) {
            console.error('Error saving category:', error);
            alert(`Error: ${error.message || 'Failed to save category'}`);
        }
    }

    // Delete Category using API
    async function deleteCategory(categoryId) {
        try {
            const response = await fetch(`/api/categories/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Content-Type': 'application/json'
                }
            });

            // Better error handling
            const responseText = await response.text();
            console.log('Delete response:', responseText);

            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Invalid JSON response:', responseText);
                throw new Error('Server returned an invalid response');
            }

            if (!response.ok) {
                throw new Error(result.message || 'Failed to delete category');
            }

            // Close modal and refresh list
            const deleteModal = document.getElementById('delete-modal');
            if (deleteModal) {
                closeModal(deleteModal);
            }
            fetchCategories();

            // Show success message
            alert('Category deleted successfully');

        } catch (error) {
            console.error('Error deleting category:', error);
            alert(`Error: ${error.message || 'Failed to delete category'}`);
        }
    }

    // Close modal
    function closeModal(modal) {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0', 'pointer-events-none');
    }

    // Generate slug from name
    function generateSlug(text) {
        return text
            .toString()
            .toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }
});
