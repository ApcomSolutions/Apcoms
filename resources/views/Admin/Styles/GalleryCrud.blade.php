{{-- resources/views/Admin/Styles/GalleryCrud.blade.php --}}
<style>
    /* Gallery Manager Specific Styles */

    /* Header Styles */
    .header-gradient {
        background-image: linear-gradient(to right, #d946ef, #ec4899);
    }

    /* Thumbnail and Image Preview Animation */
    @keyframes imageZoomIn {
        from { transform: scale(0.95); opacity: 0.8; }
        to { transform: scale(1); opacity: 1; }
    }

    #preview-image, #current-image, .dz-image img {
        animation: imageZoomIn 0.2s ease-out;
    }

    /* Fullscreen Mode Transitions */
    .fullscreen-transition {
        transition: all 0.3s ease-in-out;
    }

    #fullscreen-preview-container.visible {
        animation: fadeIn 0.2s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Enhanced Drag and Drop Experience */
    .sortable-ghost {
        opacity: 0.6 !important;
        background-color: #fce7f3 !important;
        outline: 2px dashed #db2777 !important;
    }

    .sortable-drag {
        opacity: 0.9 !important;
        transform: scale(1.05) !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }

    /* Modal Animation Overrides for Fullscreen Experience */
    .modal {
        transition: opacity 0.2s ease-out;
    }

    .modal-container {
        transition: transform 0.3s ease-out;
        transform: scale(0.98);
    }

    .modal:not(.opacity-0) .modal-container {
        transform: scale(1);
    }

    /* Custom Scrollbar for Gallery Panels */
    .py-4.flex-grow.overflow-auto::-webkit-scrollbar {
        width: 8px;
    }

    .py-4.flex-grow.overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .py-4.flex-grow.overflow-auto::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }

    .py-4.flex-grow.overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #ec4899;
    }

    /* Full Screen Preview Controls */
    .fullscreen-controls {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        background-color: rgba(0, 0, 0, 0.5);
        padding: 8px 16px;
        border-radius: 50px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .preview-fullscreen:hover .fullscreen-controls,
    #fullscreen-preview-container:hover .fullscreen-controls {
        opacity: 1;
    }

    .fullscreen-control-btn {
        color: white;
        background: none;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .fullscreen-control-btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* Loading and Processing State Styles */
    .processing-overlay {
        position: absolute;
        inset: 0;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 50;
    }

    .processing-spinner {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 3px solid #f3f3f3;
        border-top-color: #ec4899;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Adjustments */
    @media (max-width: 640px) {
        .gallery-item .overlay {
            opacity: 1; /* Always show overlay on mobile */
        }

        #preview-image-container {
            position: relative;
            transition: all 0.3s ease;
        }
    }

    /* Add this to your styles to hide the browser's native fullscreen button */

    /* Hide the browser's fullscreen button in modal headers */
    .modal-header-fullscreen-button,
    [title="Toggle fullscreen"],
    [aria-label="Toggle fullscreen"],
    .fullscreen-button,
    .header-indicator {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    /* Make sure our custom fullscreen button is visible */
    #preview-fullscreen-toggle,
    #current-image-fullscreen {
        display: flex !important;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    /* Show our fullscreen button on hover */
    #preview-image-container:hover #preview-fullscreen-toggle,
    #current-image-container:hover #current-image-fullscreen {
        opacity: 0.8 !important;
    }

    /* Improve appearance on hover */
    #preview-fullscreen-toggle:hover,
    #current-image-fullscreen:hover {
        opacity: 1 !important;
        background-color: rgba(0, 0, 0, 0.7) !important;
    }

    /* Add this to your CSS to make the fullscreen button visible and clickable */

    /* Make our custom fullscreen button always visible (not just on hover) */
    #preview-fullscreen-toggle,
    #current-image-fullscreen {
        /* Position it in a fixed location that's easy to see and click */
        position: absolute;
        top: 10px;
        right: 10px;

        /* Make it visible */
        display: flex !important;
        opacity: 0.8 !important;
        visibility: visible !important;
        pointer-events: auto !important;

        /* Styling */
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 8px;
        border-radius: 50%;
        cursor: pointer;
        z-index: 100;
        border: none;

        /* Size */
        width: 36px;
        height: 36px;
        align-items: center;
        justify-content: center;
    }

    /* Improve hover appearance */
    #preview-fullscreen-toggle:hover,
    #current-image-fullscreen:hover {
        opacity: 1 !important;
        background-color: rgba(0, 0, 0, 0.7) !important;
        transform: scale(1.1);
    }

    /* Ensure the SVG icon is visible */
    #preview-fullscreen-toggle svg,
    #current-image-fullscreen svg {
        width: 18px;
        height: 18px;
        stroke: white;
        stroke-width: 2;
    }

    /* Ensure container is properly positioned for button placement */
    #preview-image-container {
        position: relative !important;
    }
</style>
