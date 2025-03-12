{{-- resources/views/Admin/Styles/NewsCategoryCrud.blade.php --}}
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

    .edit-btn {
        color: #10b981;
    }

    .delete-btn {
        color: #ef4444;
    }

    .delete-btn:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    /* Sortable Categories */
    #sortable-categories > div {
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    #sortable-categories > div:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background-color: #fdf2f8;
    }

    .sortable-ghost {
        opacity: 0.5;
        background-color: #f9fafb !important;
    }

    .cursor-move {
        cursor: move;
    }

    /* Mobile optimizations */
    @media (max-width: 640px) {
        .modal-container {
            margin: 1rem;
            width: calc(100% - 2rem);
        }
    }
</style>
