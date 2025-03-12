// resources/js/admin/dropzone-config.js

/**
 * Initialize image dropzone with fullscreen preview capability
 *
 * @param {string} formId - The ID of the form containing the file input
 * @param {string} uploadUrl - The URL to upload files to
 * @param {string} csrfToken - CSRF token for the request
 * @param {string} currentImageId - ID of the element displaying the current image
 * @param {string} currentImageContainerId - ID of the container for the current image
 * @param {function} onSuccess - Callback function when upload succeeds
 * @param {object} additionalOptions - Additional options for Dropzone
 * @returns {Dropzone} The initialized Dropzone instance
 */
export function initImageDropzone(
    formId,
    uploadUrl,
    csrfToken,
    currentImageId = 'current-image',
    currentImageContainerId = 'current-image-container',
    onSuccess = null,
    additionalOptions = {}
) {
    // Configuration for dropzone
    const defaultOptions = {
        url: uploadUrl,
        headers: {
            'X-CSRF-TOKEN': csrfToken
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
                    <button type="button" class="dz-fullscreen">Fullscreen</button>
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
        `,
        dictInvalidFileType: "This file type is not allowed. Only images are permitted.",
        dictFileTooBig: "File is too large (@{{filesize}}MB). Maximum size: @{{maxFilesize}}MB.",
        dictResponseError: "Server error occurred. Code: @{{statusCode}}",
        dictRemoveFile: "Remove",
    };

    // Find the form and create the dropzone element
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

    // Merge options
    const dropzoneOptions = { ...defaultOptions, ...additionalOptions };

    // Initialize Dropzone
    const myDropzone = new Dropzone(dropzoneContainer, dropzoneOptions);

    // Setup events for the dropzone
    myDropzone.on("addedfile", function(file) {
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

                openFullscreenPreview(file);
            });
        }

        // Hide current image container if exists
        const currentImageContainer = document.getElementById(currentImageContainerId);
        if (currentImageContainer) {
            currentImageContainer.classList.add('hidden');
        }
    });

    // Event when file is successfully uploaded
    myDropzone.on("success", function(file, response) {
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

        // Call success callback if provided
        if (typeof onSuccess === 'function') {
            onSuccess(response);
        }
    });

    // Event when file is removed
    myDropzone.on("removedfile", function(file) {
        // Remove hidden field for path
        const hiddenInput = form.querySelector('[name="temp_image_path"]');
        if (hiddenInput) {
            // Send request to delete temp file if exists
            if (hiddenInput.value) {
                fetch(uploadUrl + '/cancel', {
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

        // Show current image container if exists
        const currentImageContainer = document.getElementById(currentImageContainerId);
        if (currentImageContainer) {
            currentImageContainer.classList.remove('hidden');
        }
    });

    // Event when error occurs
    myDropzone.on("error", function(file, errorMessage) {
        console.error("Upload error:", errorMessage);
        // Display user-friendly error message
        const errorElement = document.createElement('div');
        errorElement.className = 'bg-red-100 text-red-700 p-2 mt-2 rounded text-sm';
        errorElement.textContent = typeof errorMessage === 'string'
            ? errorMessage
            : 'An error occurred while uploading the file.';

        file.previewElement.appendChild(errorElement);
    });

    // Create fullscreen preview function
    function openFullscreenPreview(file) {
        // Get or create fullscreen container
        let fullscreenContainer = document.getElementById('fullscreen-preview-container');
        if (!fullscreenContainer) {
            fullscreenContainer = document.createElement('div');
            fullscreenContainer.id = 'fullscreen-preview-container';
            fullscreenContainer.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center';
            document.body.appendChild(fullscreenContainer);
        } else {
            fullscreenContainer.classList.remove('hidden');
        }

        // Clear previous content
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
                    <img src="${file.dataURL || URL.createObjectURL(file)}" class="max-h-full max-w-full object-contain" />
                </div>
                <div class="bg-white bg-opacity-10 p-4">
                    <div class="text-white text-lg">${file.name}</div>
                    <div class="text-gray-300 text-sm">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                </div>
            </div>
        `;

        // Add controls at the bottom
        const controlsContainer = document.createElement('div');
        controlsContainer.className = 'fullscreen-controls';
        controlsContainer.innerHTML = `
            <button class="fullscreen-control-btn close-fullscreen" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        fullscreenContainer.querySelector('.flex-grow').appendChild(controlsContainer);

        // Add event listener to close button
        fullscreenContainer.querySelectorAll('.close-fullscreen').forEach(btn => {
            btn.addEventListener('click', function() {
                fullscreenContainer.classList.add('hidden');
                document.body.style.overflow = '';
            });
        });

        // Also close on ESC key
        document.addEventListener('keydown', function closeOnEsc(e) {
            if (e.key === 'Escape') {
                if (fullscreenContainer.parentNode) {
                    fullscreenContainer.classList.add('hidden');
                    document.body.style.overflow = '';
                }
                document.removeEventListener('keydown', closeOnEsc);
            }
        });

        // Prevent scrolling while fullscreen is active
        document.body.style.overflow = 'hidden';
    }

    return myDropzone;
}

/**
 * Setup form cancellation to clean up temporary files
 *
 * @param {string} formId - The ID of the form
 * @param {string} closeButtonSelector - Selector for close buttons
 * @param {string} modalSelector - Selector for the modal
 */
export function setupFormCancellation(
    formId,
    closeButtonSelector,
    modalSelector
) {
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

            // Remove any fullscreen preview
            const fullscreenContainer = document.getElementById('fullscreen-preview-container');
            if (fullscreenContainer) {
                fullscreenContainer.classList.add('hidden');
            }

            // Allow scrolling again
            document.body.style.overflow = '';
        });
    });
}

/**
 * Create a fullscreen preview for an image
 *
 * @param {string} imageSrc - Source URL of the image
 * @param {string} title - Optional title for the image
 * @param {object} options - Additional options
 */
export function createFullscreenPreview(imageSrc, title = '', options = {}) {
    // Get or create fullscreen container
    let fullscreenContainer = document.getElementById('fullscreen-preview-container');
    if (!fullscreenContainer) {
        fullscreenContainer = document.createElement('div');
        fullscreenContainer.id = 'fullscreen-preview-container';
        fullscreenContainer.className = 'fixed inset-0 bg-black bg-opacity-90 z-[9999] flex items-center justify-center';
        document.body.appendChild(fullscreenContainer);
    } else {
        fullscreenContainer.classList.remove('hidden');
    }

    // Clear previous content
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
                <img src="${imageSrc}" class="max-h-[90vh] max-w-[90vw] object-contain" alt="${title}" />
            </div>
            ${title ? `
                <div class="bg-white bg-opacity-10 p-4">
                    <div class="text-white text-lg">${title}</div>
                </div>
            ` : ''}
        </div>
    `;

    // Add controls at the bottom
    const controlsContainer = document.createElement('div');
    controlsContainer.className = 'fullscreen-controls';
    controlsContainer.innerHTML = `
        <button class="fullscreen-control-btn close-fullscreen" title="Close">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    fullscreenContainer.querySelector('.flex-grow').appendChild(controlsContainer);

    // Add event listener to close button
    fullscreenContainer.querySelectorAll('.close-fullscreen').forEach(btn => {
        btn.addEventListener('click', function() {
            fullscreenContainer.classList.add('hidden');
            document.body.style.overflow = '';
        });
    });

    // Also close on ESC key
    document.addEventListener('keydown', function closeOnEsc(e) {
        if (e.key === 'Escape') {
            if (!fullscreenContainer.classList.contains('hidden')) {
                fullscreenContainer.classList.add('hidden');
                document.body.style.overflow = '';
                document.removeEventListener('keydown', closeOnEsc);
            }
        }
    });

    // Prevent scrolling while fullscreen is active
    document.body.style.overflow = 'hidden';

    return fullscreenContainer;
}
