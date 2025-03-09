{{-- resources/views/Admin/Styles/InsightCrud.blade.php --}}
<style>
    .header-gradient {
        background: linear-gradient(135deg, #2563eb, #4f46e5);
    }
    .card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .modal {
        transition: opacity 0.25s ease;
    }
    .modal-container {
        transform: scale(0.9);
        transition: transform 0.25s ease;
    }
    .modal.active .modal-container {
        transform: scale(1);
    }
    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
    /* Trix Editor Customization */
    trix-toolbar {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: white;
        border-bottom: 1px solid #e5e7eb;
        padding: 0.5rem;
    }
    trix-editor {
        min-height: 300px;
        padding: 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        overflow-y: auto;
        max-height: calc(100vh - 300px);
    }
    trix-editor:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* Fullscreen Modal */
    .modal-fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        z-index: 50;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background-color: white;
    }

    .modal-fullscreen-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        background-color: white;
        z-index: 10;
    }

    .modal-fullscreen-content {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1.5rem;
    }

    .modal-fullscreen-footer {
        display: flex;
        justify-content: flex-end;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        position: sticky;
        bottom: 0;
        background-color: white;
        z-index: 10;
    }

    /* Editor container with scrolling */
    .trix-editor-container {
        max-height: calc(100vh - 350px);
        overflow-y: auto;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background-color: white;
    }

    /* Fix for editor scrolling */
    #insight-form {
        max-height: 100%;
        overflow: visible;
        display: flex;
        flex-direction: column;
    }

    /* Make the form elements properly spaced in fullscreen */
    .editor-form-container {
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
        padding: 0 1rem;
    }
</style>
