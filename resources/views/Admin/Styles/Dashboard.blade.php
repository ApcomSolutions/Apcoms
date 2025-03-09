{{-- resources/views/admin/styles/Dashboard.blade.php --}}
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f9fafb;
    }
    .wakatime-gradient {
        background: linear-gradient(135deg, #2563eb, #4f46e5);
    }
    .card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .status-dot {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .active-dot {
        background-color: #10b981;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .tooltip {
        position: relative;
        display: inline-block;
    }
    .tooltip .tooltip-text {
        visibility: hidden;
        width: 200px;
        background-color: #333;
        color: white;
        text-align: center;
        border-radius: 4px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>
