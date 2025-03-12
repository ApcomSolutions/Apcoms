{{-- resources/views/Admin/Styles/NewsCrud.blade.php --}}
<style>
    /* Global Styles */
    body {
        font-family: 'Inter', sans-serif;
    }

    /* Header Gradient */
    .header-gradient {
        background: linear-gradient(to right, #6366f1, #ec4899);
    }

    /* Card Styling */
    .card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    /* Table Row Hover */
    tbody tr:hover {
        background-color: #f9fafb;
    }

    /* Status Badge Styling */
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 500;
    }

    .status-published {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .status-draft {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    /* Modal Styling */
    .modal {
        transition: opacity 0.25s ease;
    }

    .modal-container {
        transition: all 0.25s ease;
    }

    .modal-close:hover {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 0.25rem;
    }

    /* Table Actions */
    .action-btn {
        padding: 0.375rem;
        border-radius: 0.375rem;
        transition: all 0.15s ease;
    }

    .action-btn:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .action-btn svg {
        width: 1.25rem;
        height: 1.25rem;
    }

    .view-btn {
        color: #3b82f6;
    }

    .edit-btn {
        color: #10b981;
    }

    .delete-btn {
        color: #ef4444;
    }

    /* Analytics Card Styling */
    .stat-card,
    .analytics-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover,
    .analytics-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Pagination Styling */
    .pagination-active {
        background-color: #ec4899;
        color: white;
        border-color: #ec4899;
    }

    /* Image Preview */
    #preview-image,
    #current-image {
        max-height: 300px;
        width: auto;
    }

    /* Drop Zone Styling */
    .drop-zone {
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: all 0.3s ease;
        border: 2px dashed #d1d5db;
        border-radius: 0.375rem;
        padding: 1.5rem;
        margin-top: 0.5rem;
        cursor: pointer;
    }

    .drop-zone:hover {
        background-color: #f3f4f6;
        border-color: #9ca3af;
    }

    .drop-zone.active {
        border-color: #ec4899;
        background-color: rgba(236, 72, 153, 0.05);
    }

    /* Fixed Trix Editor Styles */
    trix-editor {
        min-height: 300px;
        max-height: 500px;
        overflow-y: auto;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
        padding: 0.75rem;
        background-color: white;
        color: #111827;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    trix-editor:focus {
        outline: none;
        border-color: #ec4899;
        box-shadow: 0 0 0 2px rgba(236, 72, 153, 0.2);
    }

    trix-toolbar {
        padding: 0.5rem 0;
    }

    trix-toolbar .trix-button-group {
        margin-bottom: 0.25rem;
        border-color: #e5e7eb;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    trix-toolbar .trix-button {
        border: none;
        background-color: white;
        transition: background-color 0.15s ease;
    }

    trix-toolbar .trix-button:hover {
        background-color: #f9fafb;
    }

    trix-toolbar .trix-button.trix-active {
        background-color: #f3f4f6;
    }

    trix-toolbar .trix-button--icon {
        width: 2.5rem;
        height: 2.5rem;
    }

    trix-toolbar .trix-button--icon-bold::before,
    trix-toolbar .trix-button--icon-italic::before,
    trix-toolbar .trix-button--icon-strike::before,
    trix-toolbar .trix-button--icon-link::before,
    trix-toolbar .trix-button--icon-heading-1::before,
    trix-toolbar .trix-button--icon-quote::before,
    trix-toolbar .trix-button--icon-code::before,
    trix-toolbar .trix-button--icon-bullet-list::before,
    trix-toolbar .trix-button--icon-number-list::before,
    trix-toolbar .trix-button--icon-decrease-nesting-level::before,
    trix-toolbar .trix-button--icon-increase-nesting-level::before,
    trix-toolbar .trix-button--icon-undo::before,
    trix-toolbar .trix-button--icon-redo::before,
    trix-toolbar .trix-button--icon-attach::before {
        display: inline-block;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        opacity: 0.6;
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
    }

    /* Chart Legend */
    .chart-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }

    .legend-color {
        width: 1rem;
        height: 1rem;
        border-radius: 9999px;
        margin-right: 0.5rem;
    }

    /* Mobile optimizations */
    @media (max-width: 640px) {
        .modal-container {
            margin: 1rem;
            width: calc(100% - 2rem);
        }

        .stat-card,
        .analytics-card {
            padding: 1rem;
        }

        trix-toolbar {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 0.5rem;
        }
    }
</style>
