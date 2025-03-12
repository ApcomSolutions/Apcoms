    // resources/js/admin/dropzone-config.js

    /**
     * Initialize image dropzone for forms
     * This is a reusable utility function for setting up dropzone in different forms
     */
    export function initImageDropzone(
        formId,
        uploadUrl,
        csrfToken,
        currentImageId = null,
        currentImageContainerId = null,
        onSuccessCallback = null,
        dropzoneOptions = {}
    ) {
        // Get form element
        const form = document.getElementById(formId);
        if (!form) return null;

        // Find file input
        const fileInput = form.querySelector('input[type="file"]');
        if (!fileInput) return null;

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

        // Default options
        const defaultOptions = {
            url: uploadUrl,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            paramName: 'temp_image',
            maxFilesize: 5, // MB
            acceptedFiles: 'image/jpeg,image/png,image/webp,image/gif',
            addRemoveLinks: true,
            previewsContainer: previewContainer,
            createImageThumbnails: true,
            thumbnailWidth: 200,
            thumbnailHeight: 200,
            dictDefaultMessage: `
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-1">Drag images here or click to upload</p>
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, WebP, GIF up to 5MB</p>
                </div>
            `,
            dictInvalidFileType: "This file type is not allowed. Only images are permitted.",
            dictFileTooBig: "File is too large (@{{filesize}}MB). Max filesize: @{{maxFilesize}}MB.",
            dictResponseError: "Server responded with @{{statusCode}} code.",
            dictRemoveFile: "Remove",
            init: function() {
                // Event when file is added
                this.on("addedfile", function(file) {
                    // Remove all other files as we only want one file
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }

                    // Add "Preview" watermark
                    const watermark = document.createElement('div');
                    watermark.className = 'absolute top-1 right-1 bg-purple-500 text-white text-xs px-1 py-0.5 rounded';
                    watermark.textContent = 'Preview';
                    file.previewElement.appendChild(watermark);

                    // Add fullscreen functionality to the preview
                    const previewImage = file.previewElement.querySelector('[data-dz-thumbnail]');
                    if (previewImage) {
                        previewImage.addEventListener('click', function() {
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
                                    <img src="${file.dataURL || URL.createObjectURL(file)}" style="max-height: 90vh; max-width: 90vw; object-fit: contain;" />
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

                        // Add cursor pointer to show it's clickable
                        previewImage.style.cursor = 'pointer';
                    }

                    // Hide current image container if exists and if ID is provided
                    if (currentImageContainerId) {
                        const currentImageContainer = document.getElementById(currentImageContainerId);
                        if (currentImageContainer) {
                            currentImageContainer.classList.add('hidden');
                        }
                    }
                });

                // Event when file is successfully uploaded
                this.on("success", function(file, response) {
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

                    // Call custom success callback if provided
                    if (typeof onSuccessCallback === 'function') {
                        onSuccessCallback(response);
                    }
                });

                // Event when file is removed
                this.on("removedfile", function(file) {
                    // Remove hidden field for path
                    const hiddenInput = form.querySelector('[name="temp_image_path"]');
                    if (hiddenInput) {
                        // Send request to delete temp file if exists
                        if (hiddenInput.value) {
                            fetch('/api/temp-uploads/cancel', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ path: hiddenInput.value })
                            }).catch(error => {
                                console.error('Error cancelling upload:', error);
                            });
                        }

                        form.removeChild(hiddenInput);
                    }

                    // Show current image container if exists and if ID is provided
                    if (currentImageContainerId) {
                        const currentImageContainer = document.getElementById(currentImageContainerId);
                        if (currentImageContainer) {
                            currentImageContainer.classList.remove('hidden');
                        }
                    }
                });

                // Event when error occurs
                this.on("error", function(file, errorMessage) {
                    console.error("Upload error:", errorMessage);
                    // Display user-friendly error message
                    const errorElement = document.createElement('div');
                    errorElement.className = 'bg-red-100 text-red-700 p-2 mt-2 rounded text-sm';
                    errorElement.textContent = typeof errorMessage === 'string'
                        ? errorMessage
                        : 'An error occurred while uploading the file.';

                    file.previewElement.appendChild(errorElement);
                });
            }
        };

        // Merge default options with custom options
        const mergedOptions = {...defaultOptions, ...dropzoneOptions};

        // Initialize Dropzone
        const dropzone = new Dropzone(dropzoneContainer, mergedOptions);

        return dropzone;
    }

    /**
     * Set up form cancellation to clean up temporary files
     */
    export function setupFormCancellation(formId, closeButtonSelector, modalSelector) {
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                    modal.classList.remove('opacity-100');
                    modal.classList.add('opacity-0', 'pointer-events-none');
                }
            });
        });
    }

    // resources/js/admin/teams.js

    document.addEventListener('DOMContentLoaded', function() {
        // State
        let teams = [];
        let currentPage = 1;
        let itemsPerPage = 10;
        let totalPages = 1;
        let currentTeamId = null;
        let sortableTeam = null;
        let teamDropzone = null;

        // CSRF token setup for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Initialize
        fetchTeams();
        initSortable();

        // Initialize Dropzone when the modal opens
        document.getElementById('new-team-btn').addEventListener('click', () => {
            openEditorModal();

            // Initialize dropzone after modal is shown
            setTimeout(() => {
                initTeamDropzone();
            }, 200);
        });

        // Event Listeners
        document.getElementById('refresh-btn').addEventListener('click', fetchTeams);
        document.getElementById('search').addEventListener('input', filterTeams);
        document.getElementById('status-filter').addEventListener('change', filterTeams);
        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTeams();
            }
        });
        document.getElementById('next-page').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderTeams();
            }
        });

        // Save Team Button
        document.getElementById('save-team-btn').addEventListener('click', saveTeam);

        // Save Order Button
        document.getElementById('save-order-btn').addEventListener('click', saveOrder);

        // Delete confirmation
        document.getElementById('confirm-delete-btn').addEventListener('click', () => {
            if (currentTeamId) {
                deleteTeam(currentTeamId);
            }
        });

        // Setup form cancellation to clean up temp files
        setupFormCancellation(
            'team-form',
            '.modal-close',
            '#team-editor-modal'
        );

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

        // Open Editor Modal
        async function openEditorModal(teamId = null) {
            const modal = document.getElementById('team-editor-modal');
            const form = document.getElementById('team-form');
            form.setAttribute('enctype', 'multipart/form-data');
            const editorTitle = document.getElementById('editor-title');
            const formMethod = document.getElementById('form-method');
            const currentImageContainer = document.getElementById('current-image-container');

            // Reset form
            form.reset();
            currentImageContainer.classList.add('hidden');

            // Reset dropzone if exists
            if (teamDropzone) {
                teamDropzone.removeAllFiles(true);
            }

            if (teamId) {
                // Edit mode - fetch data from API
                editorTitle.textContent = 'Edit Team Member';
                formMethod.value = 'PUT';

                try {
                    const team = teams.find(t => t.id == teamId);
                    if (!team) {
                        throw new Error('Team member not found');
                    }

                    const formTeamId = document.getElementById('form-team-id');
                    const nameInput = document.getElementById('name');
                    const positionInput = document.getElementById('position');
                    const isActiveCheckbox = document.getElementById('is_active');
                    const currentImage = document.getElementById('current-image');

                    // Fill form fields
                    formTeamId.value = team.id;
                    nameInput.value = team.name;
                    positionInput.value = team.position;
                    isActiveCheckbox.checked = team.is_active;

                    // Show current image if exists
                    if (team.image_url) {
                        currentImage.src = team.image_url;
                        currentImageContainer.classList.remove('hidden');

                        // Add fullscreen functionality to current image
                        currentImage.addEventListener('click', function() {
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
                                    <img src="${team.image_url}" style="max-height: 90vh; max-width: 90vw; object-fit: contain;" />
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

                        // Add cursor pointer to show it's clickable
                        currentImage.style.cursor = 'pointer';
                    }

                    // Store current team ID for save operation
                    currentTeamId = teamId;

                } catch (error) {
                    console.error('Error fetching team for editing:', error);
                    alert('Failed to load team data for editing.');
                    return;
                }
            } else {
                // Create mode
                editorTitle.textContent = 'Add Team Member';
                formMethod.value = 'POST';
                document.getElementById('form-team-id').value = '';
                document.getElementById('is_active').checked = true;

                // Reset current team ID
                currentTeamId = null;
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
        function openDeleteModal(teamId, teamName) {
            const modal = document.getElementById('delete-modal');
            const nameElement = document.getElementById('delete-team-name');

            nameElement.textContent = teamName;
            currentTeamId = teamId;

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
        }

        // Save Team (Create or Update) using API
        async function saveTeam() {
            const form = document.getElementById('team-form');
            const formMethodElement = document.getElementById('form-method');

            const formData = new FormData(form);
            const method = formMethodElement.value;
            const teamId = document.getElementById('form-team-id').value;

            // Handle checkbox field
            if (!formData.has('is_active')) {
                formData.append('is_active', '0'); // Change to false
            } else {
                // Make sure the existing value is boolean-like
                formData.set('is_active', formData.get('is_active') === 'on' ? '1' : '0');
            }

            // Add CSRF token explicitly to the FormData
            formData.append('_token', csrfToken);

            try {
                let url = '/api/teams';
                let requestMethod = 'POST';

                if (method === 'PUT' && teamId) {
                    url = `/api/teams/${teamId}`;
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
                    throw new Error(result.message || 'Failed to save team member');
                }

                // Close modal and refresh list
                const editorModal = document.getElementById('team-editor-modal');
                closeModal(editorModal);

                // Reset dropzone if exists
                if (teamDropzone) {
                    teamDropzone.removeAllFiles(true);
                }

                fetchTeams();

                // Show success message
                alert(result.message || 'Team member saved successfully');

            } catch (error) {
                console.error('Error saving team member:', error);
                alert(`Error: ${error.message || 'Failed to save team member'}`);
            }
        }

        // Delete Team using API
        async function deleteTeam(teamId) {
            try {
                const response = await fetch(`/api/teams/${teamId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to delete team member');
                }

                // Close modal and refresh list
                const deleteModal = document.getElementById('delete-modal');
                closeModal(deleteModal);
                fetchTeams();

                // Show success message
                alert(result.message || 'Team member deleted successfully');

            } catch (error) {
                console.error('Error deleting team member:', error);
                alert(`Error: ${error.message || 'Failed to delete team member'}`);
            }
        }

        // Close modal
        function closeModal(modal) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
        }

        // Initialize Dropzone for Team Images
        function initTeamDropzone() {
            // Only initialize if it doesn't exist
            if (!teamDropzone) {
                teamDropzone = initImageDropzone(
                    'team-form',
                    '/api/temp-uploads',
                    csrfToken,
                    'current-image',
                    'current-image-container',
                    function(response) {
                        console.log('File uploaded successfully:', response);
                    },
                    {
                        acceptedFiles: 'image/jpeg,image/png,image/webp,image/gif',
                        maxFilesize: 5, // MB
                        thumbnailWidth: 120,
                        thumbnailHeight: 120,
                        createImageThumbnails: true,
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
                        `
                    }
                );

                // Add fullscreen button functionality
                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('dz-fullscreen')) {
                        e.preventDefault();

                        const previewElement = e.target.closest('.dz-preview');
                        if (previewElement) {
                            const imgElement = previewElement.querySelector('[data-dz-thumbnail]');
                            if (imgElement) {
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
                                        <img src="${imgElement.src}" style="max-height: 90vh; max-width: 90vw; object-fit: contain;" />
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
                            }
                        }
                    }
                });
            }

            return teamDropzone;
        }

        // Initialize Sortable
        function initSortable() {
            const sortableContainer = document.getElementById('sortable-team');
            if (sortableContainer) {
                sortableTeam = new Sortable(sortableContainer, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function() {
                        // Enable save button after sorting
                        document.getElementById('save-order-btn').disabled = false;
                    }
                });
            }
        }

        // Fetch Teams from API
        async function fetchTeams() {
            try {
                const response = await fetch('/api/teams');
                if (!response.ok) throw new Error('Failed to fetch teams');

                teams = await response.json();
                renderTeams();
                renderSortableTeams();
            } catch (error) {
                console.error('Error fetching teams:', error);
                document.getElementById('team-table-body').innerHTML =
                    '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading teams. Please try again.</td></tr>';
            }
        }

        // Filter Teams
        function filterTeams() {
            currentPage = 1;
            renderTeams();
        }

        // Render Teams Table
        function renderTeams() {
            const tableBody = document.getElementById('team-table-body');
            if (!tableBody) return;

            tableBody.innerHTML = '';

            const searchInput = document.getElementById('search');
            const statusFilter = document.getElementById('status-filter');

            // Apply filters
            const searchQuery = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;

            let filteredTeams = teams.filter(team => {
                const nameMatch = team.name.toLowerCase().includes(searchQuery);
                const positionMatch = team.position.toLowerCase().includes(searchQuery);
                const statusMatch = !statusValue ||
                    (statusValue === 'active' && team.is_active) ||
                    (statusValue === 'inactive' && !team.is_active);

                return (nameMatch || positionMatch) && statusMatch;
            });

            // Calculate pagination
            totalPages = Math.ceil(filteredTeams.length / itemsPerPage);
            if (totalPages === 0) totalPages = 1;

            // Update pagination UI
            document.getElementById('total-team-members').textContent = filteredTeams.length;
            document.getElementById('page-start').textContent = filteredTeams.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
            document.getElementById('page-end').textContent = Math.min(currentPage * itemsPerPage, filteredTeams.length);

            // Render pagination
            renderPagination();

            // Slice for current page
            const pageTeams = filteredTeams.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

            if (pageTeams.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No team members found matching your filters</td></tr>';
                return;
            }

            // Render teams
            pageTeams.forEach(team => {
                const row = document.createElement('tr');
                row.classList.add('hover:bg-gray-50');

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex-shrink-0 h-10 w-10">
                            ${team.image_url
                    ? `<img class="h-10 w-10 rounded-full object-cover" src="${team.image_url}" alt="${team.name}">`
                    : `<div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <span class="text-purple-500 font-medium text-xs">${team.name.split(' ').map(n => n[0]).join('')}</span>
                       </div>`
                }
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${team.name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">${team.position}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${team.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${team.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-900 edit-team" data-id="${team.id}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button class="text-red-600 hover:text-red-900 delete-team" data-id="${team.id}" data-name="${team.name}">
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
            document.querySelectorAll('.edit-team').forEach(button => {
                button.addEventListener('click', () => {
                    const teamId = button.getAttribute('data-id');
                    openEditorModal(teamId);
                });
            });

            document.querySelectorAll('.delete-team').forEach(button => {
                button.addEventListener('click', () => {
                    const teamId = button.getAttribute('data-id');
                    const teamName = button.getAttribute('data-name');
                    openDeleteModal(teamId, teamName);
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
                button.className = `relative inline-flex items-center px-4 py-2 border ${currentPage === pageNum ? 'border-purple-500 bg-purple-50 text-purple-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'}`;
                button.textContent = pageNum;

                button.addEventListener('click', () => {
                    currentPage = pageNum;
                    renderTeams();
                });

                paginationContainer.appendChild(button);
            }
        }

        // Save Team Order
        async function saveOrder() {
            const saveButton = document.getElementById('save-order-btn');
            saveButton.disabled = true;

            try {
                const sortableItems = document.querySelectorAll('.sortable-item');
                const orderedIds = Array.from(sortableItems).map(item => item.getAttribute('data-id'));

                const response = await fetch('/api/teams/update-order', {
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
                fetchTeams();
                alert('Team order updated successfully');

            } catch (error) {
                console.error('Error saving team order:', error);
                alert(`Error: ${error.message || 'Failed to update team order'}`);
                saveButton.disabled = false;
            }
        }

        // Render Sortable Teams
        function renderSortableTeams() {
            const sortableContainer = document.getElementById('sortable-team');
            if (!sortableContainer) return;

            sortableContainer.innerHTML = '';

            // Sort teams by order
            const sortedTeams = [...teams].sort((a, b) => a.order - b.order);

            if (sortedTeams.length === 0) {
                sortableContainer.innerHTML = '<div class="bg-gray-100 rounded-md p-4 text-center text-gray-500">No team members found</div>';
                return;
            }

            sortedTeams.forEach(team => {
                const itemElement = document.createElement('div');
                itemElement.className = 'sortable-item flex items-center justify-between bg-white border rounded-md p-4 cursor-grab';
                itemElement.setAttribute('data-id', team.id);

                // Build avatar HTML separately to avoid template nesting issues
                const avatarHtml = team.image_url
                    ? `<img class="h-10 w-10 rounded-full object-cover" src="${team.image_url}" alt="${team.name}">`
                    : `<div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <span class="text-purple-500 font-medium text-xs">${team.name.split(' ').map(n => n[0]).join('')}</span>
                   </div>`;

                // Use the avatar HTML in the main template
                itemElement.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 mr-4">
                        ${avatarHtml}
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${team.name}</div>
                        <div class="text-sm text-gray-500">${team.position}</div>
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
     // <-- This closing brace was missing

    // Initialize Dropzone for Team Images with fullscreen preview
        function initTeamDropzone() {
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
                thumbnailWidth: 120,
                thumbnailHeight: 120,
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
            const form = document.getElementById('team-form');
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
            if (teamDropzone) {
                teamDropzone.destroy();
            }

            teamDropzone = new Dropzone(dropzoneContainer, dropzoneConfig);

            // Setup events for the dropzone
            teamDropzone.on("addedfile", function(file) {
                // Remove all other files as we only want one file
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }

                // Add "Preview" watermark
                const watermark = document.createElement('div');
                watermark.className = 'absolute top-1 right-1 bg-purple-500 text-white text-xs px-1 py-0.5 rounded';
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
            teamDropzone.on("success", function(file, response) {
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
            teamDropzone.on("removedfile", function(file) {
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
            teamDropzone.on("error", function(file, errorMessage) {
                console.error("Upload error:", errorMessage);
                // Display user-friendly error message
                const errorElement = document.createElement('div');
                errorElement.className = 'bg-red-100 text-red-700 p-2 mt-2 rounded text-sm';
                errorElement.textContent = typeof errorMessage === 'string'
                    ? errorMessage
                    : 'An error occurred while uploading the file.';

                file.previewElement.appendChild(errorElement);
            });

            return teamDropzone;
        }

    // Add CSS styling for the Dropzone and fullscreen preview
        function addTeamDropzoneStyles() {
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
                min-height: 160px !important;
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
                border-color: #a78bfa;
                background: #ede9fe;
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
                width: 120px;
            }

            .dz-image-wrapper {
                position: relative;
            }

            .dz-image {
                width: 120px;
                height: 120px;
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
                background: #a78bfa;
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

            /* Current image container styling */
            #current-image-container {
                position: relative;
                display: inline-block;
            }

            #current-image-container img {
                cursor: pointer;
            }

            #current-image-container .fullscreen-button {
                position: absolute;
                top: 4px;
                right: 4px;
                background: rgba(0, 0, 0, 0.5);
                color: white;
                border: none;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                opacity: 0;
                transition: opacity 0.2s ease;
            }

            #current-image-container:hover .fullscreen-button {
                opacity: 1;
            }
        `;
            document.head.appendChild(style);
        }

    // Initialize styles on page load
        document.addEventListener('DOMContentLoaded', function() {
            addTeamDropzoneStyles();

            // Add fullscreen preview functionality to current image
            const currentImage = document.getElementById('current-image');
            if (currentImage) {
                // Create a fullscreen button
                const fullscreenButton = document.createElement('button');
                fullscreenButton.className = 'fullscreen-button';
                fullscreenButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                </svg>
            `;

                // Add button to container
                const container = document.getElementById('current-image-container');
                if (container) {
                    container.appendChild(fullscreenButton);

                    // Setup fullscreen functionality
                    fullscreenButton.addEventListener('click', function() {
                        const fullscreenView = document.createElement('div');
                        fullscreenView.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center';
                        fullscreenView.innerHTML = `
                        <div class="relative w-full h-full flex flex-col">
                            <div class="absolute top-4 right-4 flex space-x-2">
                                <button class="p-2 bg-white rounded-full text-gray-800 hover:bg-gray-300 close-fullscreen">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex-grow flex items-center justify-center p-4">
                                <img src="${currentImage.src}" class="max-h-full max-w-full object-contain" />
                            </div>
                        </div>
                    `;
                        document.body.appendChild(fullscreenView);
                        document.body.style.overflow = 'hidden';

                        // Add close handler
                        fullscreenView.querySelector('.close-fullscreen').addEventListener('click', function() {
                            document.body.removeChild(fullscreenView);
                            document.body.style.overflow = '';
                        });

                        // Close on ESC key
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
                }
            }

            // Add keyboard shortcut for Escape to close modals
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal:not(.opacity-0)').forEach(modal => {
                        const closeBtn = modal.querySelector('.modal-close');
                        if (closeBtn) closeBtn.click();
                    });
                }
            });
        });
    });

