{{-- resources/views/admin/components/SideCharts.blade.php --}}
<!-- Side Charts Column -->
<div class="space-y-8">
    <!-- Device Breakdown -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Device Breakdown</h3>
        <div style="height: 200px;">
            <canvas id="device-chart"></canvas>
        </div>
    </div>

    <!-- Completion Rate Chart -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Read Time Distribution</h3>
        <div style="height: 200px;">
            <canvas id="read-time-chart"></canvas>
        </div>
    </div>
</div>
