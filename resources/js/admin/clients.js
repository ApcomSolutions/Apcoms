document.addEventListener('DOMContentLoaded', function() {
    // State
    let clients = [];
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalPages = 1;
    let currentClientId = null;
    let sortableClient = null;

    // CSRF token setup for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize
    fetchClients();
    initSortable();

    // Event Listeners
    document.getElementById('refresh-btn').addEventListener('click', fetchClients);
    document.getElementById('search').addEventListener('input', filterClients);
    document.getElementById('status-filter').addEventListener('change', filterClients);
    document.getElementById('prev-page').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderClients();
        }
    });
    document.getElementById('next-page').addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            renderClients();
        }
    });

    // New Client Button
    document.getElementById('new-client-btn').addEventListener('click', () => {
        openEditorModal();
    });

    // Save Client Button
    document.getElementById('save-client-btn').addEventListener('click', saveClient);

    // Save Order Button
    document.getElementById('save-order-btn').addEventListener('click', saveOrder);

    // Delete confirmation
    document.getElementById('confirm-delete-btn').addEventListener('click', () => {
        if (currentClientId) {
            deleteClient(currentClientId);
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
        const sortableContainer = document.getElementById('sortable-clients');
        if (sortableContainer) {
            sortableClient = new Sortable(sortableContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    // Enable save button after sorting
                    document.getElementById('save-order-btn').disabled = false;
                }
            });
        }
    }

    // Fetch Clients from API
    async function fetchClients() {
        try {
            const response = await fetch('/api/clients');
            if (!response.ok) throw new Error('Failed to fetch clients');

            clients = await response.json();
            renderClients();
            renderSortableClients();
        } catch (error) {
            console.error('Error fetching clients:', error);
            document.getElementById('client-table-body').innerHTML =
                '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading clients. Please try again.</td></tr>';
        }
    }

    // Filter Clients
    function filterClients() {
        currentPage = 1;
        renderClients();
    }

    // Render Clients Table
    function renderClients() {
        const tableBody = document.getElementById('client-table-body');
        if (!tableBody) return;

        tableBody.innerHTML = '';

        const searchInput = document.getElementById('search');
        const statusFilter = document.getElementById('status-filter');

        // Apply filters
        const searchQuery = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        let filteredClients = clients.filter(client => {
            const nameMatch = client.name.toLowerCase().includes(searchQuery);
            const positionMatch = client.position.toLowerCase().includes(searchQuery);
            const companyMatch = client.company ? client.company.toLowerCase().includes(searchQuery) : false;
            const locationMatch = client.location ? client.location.toLowerCase().includes(searchQuery) : false;

            const statusMatch = !statusValue ||
                (statusValue === 'active' && client.is_active) ||
                (statusValue === 'inactive' && !client.is_active);

            return (nameMatch || positionMatch || companyMatch || locationMatch) && statusMatch;
        });

        // Calculate pagination
        totalPages = Math.ceil(filteredClients.length / itemsPerPage);
        if (totalPages === 0) totalPages = 1;

        // Update pagination UI
        document.getElementById('total-clients').textContent = filteredClients.length;
        document.getElementById('page-start').textContent = filteredClients.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        document.getElementById('page-end').textContent = Math.min(currentPage * itemsPerPage, filteredClients.length);

        // Render pagination
        renderPagination();

        // Slice for current page
        const pageClients = filteredClients.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

        if (pageClients.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No clients found matching your filters</td></tr>';
            return;
        }

        // Render clients
        pageClients.forEach(client => {
            const row = document.createElement('tr');
            row.classList.add('hover:bg-gray-50');

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex-shrink-0 h-10 w-10">
                        ${client.image_url
                ? `<img class="h-10 w-10 rounded-full object-cover" src="${client.image_url}" alt="${client.name}">`
                : `<div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                <span class="text-yellow-500 font-medium text-xs">${client.name.split(' ').map(n => n[0]).join('')}</span>
                               </div>`
            }
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${client.name}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">${client.position}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">${client.company || '-'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">${client.location || '-'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${client.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${client.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-900 edit-client" data-id="${client.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                        <button class="text-red-600 hover:text-red-900 delete-client" data-id="${client.id}" data-name="${client.name}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </td>
            `;

            tableBody.appendChild(row);
        });

        // Add event listeners to buttons
        document.querySelectorAll('.edit-client').forEach(button => {
            button.addEventListener('click', () => {
                const clientId = button.getAttribute('data-id');
                openEditorModal(clientId);
            });
        });

        document.querySelectorAll('.delete-client').forEach(button => {
            button.addEventListener('click', () => {
                const clientId = button.getAttribute('data-id');
                const clientName = button.getAttribute('data-name');
                openDeleteModal(clientId, clientName);
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
            button.className = `relative inline-flex items-center px-4 py-2 border ${currentPage === pageNum ? 'border-yellow-500 bg-yellow-50 text-yellow-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'}`;
            button.textContent = pageNum;

            button.addEventListener('click', () => {
                currentPage = pageNum;
                renderClients();
            });

            paginationContainer.appendChild(button);
        }
    }

    // Render Sortable Clients
    function renderSortableClients() {
        const sortableContainer = document.getElementById('sortable-clients');
        if (!sortableContainer) return;

        sortableContainer.innerHTML = '';

        // Sort clients by order
        const sortedClients = [...clients].sort((a, b) => a.order - b.order);

        if (sortedClients.length === 0) {
            sortableContainer.innerHTML = '<div class="bg-gray-100 rounded-md p-4 text-center text-gray-500">No clients found</div>';
            return;
        }

        sortedClients.forEach(client => {
            const itemElement = document.createElement('div');
            itemElement.className = 'sortable-item flex items-center justify-between bg-white border rounded-md p-4 cursor-grab';
            itemElement.setAttribute('data-id', client.id);

            itemElement.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 mr-4">
                        ${client.image_url
                ? `<img class="h-10 w-10 rounded-full object-cover" src="${client.image_url}" alt="${client.name}">`
                : `<div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                <span class="text-yellow-500 font-medium text-xs">${client.name.split(' ').map(n => n[0]).join('')}</span>
                               </div>`
            }
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${client.name}</div>
                        <div class="text-sm text-gray-500">${client.position}${client.company ? ` at ${client.company}` : ''}</div>
                    </div>
                </div>
                <div class="text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
                    </svg>
                </div>
            `;

            sortableContainer.appendChild(itemElement);
        });

        // Disable save button initially
        document.getElementById('save-order-btn').disabled = true;
    }

    // Save Client Order
    async function saveOrder() {
        const saveButton = document.getElementById('save-order-btn');
        saveButton.disabled = true;

        try {
            const sortableItems = document.querySelectorAll('.sortable-item');
            const orderedIds = Array.from(sortableItems).map(item => item.getAttribute('data-id'));

            const response = await fetch('/api/clients/update-order', {
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
            fetchClients();
            window.ErrorHandler.showSuccess('Client order updated successfully');

            // Client order error
        } catch (error) {
            console.error('Error saving client order:', error);
            window.ErrorHandler.showError(`Error: ${error.message || 'Failed to update client order'}`);
            saveButton.disabled = false;
        }
    }

    // Open Editor Modal
    async function openEditorModal(clientId = null) {
        const modal = document.getElementById('client-editor-modal');
        const form = document.getElementById('client-form');
        form.setAttribute('enctype', 'multipart/form-data');
        const editorTitle = document.getElementById('editor-title');
        const formMethod = document.getElementById('form-method');
        const currentImageContainer = document.getElementById('current-image-container');

        // Reset form
        form.reset();
        currentImageContainer.classList.add('hidden');

        if (clientId) {
            // Edit mode - fetch data from API
            editorTitle.textContent = 'Edit Client';
            formMethod.value = 'PUT';

            try {
                const client = clients.find(c => c.id == clientId);
                if (!client) {
                    throw new Error('Client not found');
                }

                const formClientId = document.getElementById('form-client-id');
                const nameInput = document.getElementById('name');
                const positionInput = document.getElementById('position');
                const companyInput = document.getElementById('company');
                const locationInput = document.getElementById('location');
                const isActiveCheckbox = document.getElementById('is_active');
                const currentImage = document.getElementById('current-image');

                // Fill form fields
                formClientId.value = client.id;
                nameInput.value = client.name;
                positionInput.value = client.position;
                companyInput.value = client.company || '';
                locationInput.value = client.location || '';
                isActiveCheckbox.checked = client.is_active;

                // Show current image if exists
                if (client.image_url) {
                    currentImage.src = client.image_url;
                    currentImageContainer.classList.remove('hidden');
                }

                // Store current client ID for save operation
                currentClientId = clientId;

            } catch (error) {
                console.error('Error fetching client for editing:', error);
                alert('Failed to load client data for editing.');
                return;
            }
        } else {
            // Create mode
            editorTitle.textContent = 'Add Client';
            formMethod.value = 'POST';
            document.getElementById('form-client-id').value = '';
            document.getElementById('is_active').checked = true;

            // Reset current client ID
            currentClientId = null;
        }

        // Show modal
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');

        // Focus on name field
        setTimeout(() => {
            document.getElementById('name').focus();
        }, 100);
    }

    // Open Delete Confirmation Modal
    function openDeleteModal(clientId, clientName) {
        const modal = document.getElementById('delete-modal');
        const nameElement = document.getElementById('delete-client-name');

        nameElement.textContent = clientName;
        currentClientId = clientId;

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
    }

    // Save Client (Create or Update) using API
    async function saveClient() {
        const form = document.getElementById('client-form');
        const formMethodElement = document.getElementById('form-method');

        const formData = new FormData(form);
        const method = formMethodElement.value;
        const clientId = document.getElementById('form-client-id').value;

        // Handle checkbox field
        if (!formData.has('is_active')) {
            formData.append('is_active', '0');
        } else {
            // Make sure the existing value is boolean-like
            formData.set('is_active', formData.get('is_active') === 'on' ? '1' : '0');
        }

        // Add CSRF token explicitly to the FormData
        formData.append('_token', csrfToken);

        try {
            let url = '/api/clients';
            let requestMethod = 'POST';

            if (method === 'PUT' && clientId) {
                url = `/api/clients/${clientId}`;
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
                throw new Error(result.message || 'Failed to save client');
            }

            // Close modal and refresh list
            const editorModal = document.getElementById('client-editor-modal');
            closeModal(editorModal);
            fetchClients();

            // Show success message
            window.ErrorHandler.showSuccess(result.message || 'Client saved successfully');

        } catch (error) {
            console.error('Error saving client:', error);
            window.ErrorHandler.showError(`Error: ${error.message || 'Failed to save client'}`);
        }
    }

    // Delete Client using API
    async function deleteClient(clientId) {
        try {
            const response = await fetch(`/api/clients/${clientId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to delete client');
            }

            // Close modal and refresh list
            const deleteModal = document.getElementById('delete-modal');
            closeModal(deleteModal);
            fetchClients();


            // Show success message
            window.ErrorHandler.showSuccess(result.message || 'Client deleted successfully');

// Error deleting client
        } catch (error) {
            console.error('Error deleting client:', error);
            window.ErrorHandler.showError(`Error: ${error.message || 'Failed to delete client'}`);
        }
    }

    // Close modal
    function closeModal(modal) {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0', 'pointer-events-none');
    }
});
