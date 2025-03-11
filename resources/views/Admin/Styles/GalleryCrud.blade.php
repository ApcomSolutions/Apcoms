<style>
    .header-gradient {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
    }
    .card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    /* Modal Styles */
    .modal {
        transition: opacity 0.25s ease;
    }
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-container {
        max-height: 90vh;
        overflow-y: auto;
    }

    /* Gallery item styles */
    .gallery-item {
        position: relative;
        transition: transform 0.2s;
        cursor: pointer;
    }
    .gallery-item:hover {
        transform: translateY(-5px);
    }
    .gallery-item .overlay {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 70%);
    }

    /* Sortable styles */
    .sortable-item {
        cursor: grab;
    }
    .sortable-item:active {
        cursor: grabbing;
    }
    .sortable-ghost {
        opacity: 0.5;
    }
</style>
