<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Insight Statistics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Insight Analytics Dashboard</h1>

    <!-- Hero Section with Overall Graph -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Overall Performance</h2>
        <div class="h-64">
            <canvas id="overall-chart"></canvas>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-2">Total Views</h2>
            <p class="text-4xl text-indigo-600 font-bold" id="total-views">-</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-2">Unique Devices</h2>
            <p class="text-4xl text-indigo-600 font-bold" id="unique-devices">-</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-2">Avg. Read Time</h2>
            <p class="text-4xl text-indigo-600 font-bold" id="avg-read-time">-</p>
            <p class="text-sm text-gray-500">minutes</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-2">Completion Rate</h2>
            <p class="text-4xl text-indigo-600 font-bold" id="completion-rate">-</p>
            <p class="text-sm text-gray-500">of readers finished</p>
        </div>
    </div>

    <!-- All Insights List -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-6">All Insights</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Viewers</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Read Time</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="insights-table-body">
                <!-- Table rows will be populated via JavaScript -->
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Loading data...</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-6">Recent Views</h2>
            <div id="recent-views" class="space-y-4">
                <!-- Recent views will be populated via JavaScript -->
                <p class="text-gray-500">Loading data...</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-6">Device Breakdown</h2>
            <div id="device-breakdown" class="h-64">
                <!-- Chart will be rendered here -->
                <p class="text-gray-500">Loading data...</p>
            </div>
        </div>
    </div>
</div>

<!-- Insight Detail Modal -->
<div id="insight-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-4xl w-full max-h-screen overflow-y-auto">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-2xl font-bold" id="modal-title">Insight Details</h2>
            <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-100 p-4 rounded">
                <h3 class="text-sm text-gray-500 mb-1">Total Views</h3>
                <p class="text-xl font-bold" id="modal-views">-</p>
            </div>
            <div class="bg-gray-100 p-4 rounded">
                <h3 class="text-sm text-gray-500 mb-1">Unique Viewers</h3>
                <p class="text-xl font-bold" id="modal-unique">-</p>
            </div>
            <div class="bg-gray-100 p-4 rounded">
                <h3 class="text-sm text-gray-500 mb-1">Average Read Time</h3>
                <p class="text-xl font-bold" id="modal-avg-time">-</p>
            </div>
            <div class="bg-gray-100 p-4 rounded">
                <h3 class="text-sm text-gray-500 mb-1">Completion Rate</h3>
                <p class="text-xl font-bold" id="modal-completion">-</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3">Read Time Distribution</h3>
            <div class="h-64">
                <canvas id="read-time-chart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize chart variables
        let overallChart = null;
        let readTimeChart = null;

        // Get modal elements
        const modal = document.getElementById('insight-modal');
        const closeModal = document.getElementById('close-modal');

        // Close modal when X is clicked
        closeModal.addEventListener('click', function() {
            modal.classList.add('hidden');
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });

        // Call all fetch functions when page loads
        fetchOverallStats();
        fetchAllInsights();
        fetchRecentViews();
        fetchDeviceBreakdown();
        createOverallChart();

        // 🔹 Create Overall Chart
        async function createOverallChart() {
            try {
                const response = await fetch('/api/admin/dashboard/top-articles');
                if (!response.ok) throw new Error('Failed to fetch data for chart');

                const data = await response.json();

                if (!data || data.length === 0) {
                    document.getElementById('overall-chart').innerHTML = '<p class="text-center text-gray-500">No data available for chart</p>';
                    return;
                }

                // Extract titles and views for chart
                const labels = data.map(item => item.judul);
                const views = data.map(item => item.views);
                const readTimes = data.map(item => item.avg_read_time);

                const ctx = document.getElementById('overall-chart').getContext('2d');
                overallChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Views',
                                data: views,
                                backgroundColor: 'rgba(79, 70, 229, 0.6)',
                                borderColor: 'rgba(79, 70, 229, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Avg. Read Time (min)',
                                data: readTimes,
                                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error creating overall chart:', error);
                document.getElementById('overall-chart').innerHTML = '<p class="text-center text-red-500">Error loading chart data</p>';
            }
        }

        // 🔹 Fetch Overall Stats
        async function fetchOverallStats() {
            try {
                const response = await fetch('/api/admin/dashboard/stats');
                if (!response.ok) throw new Error('Failed to fetch stats');

                const data = await response.json();
                updateElementText('total-views', formatNumber(data.total_views));
                updateElementText('unique-devices', formatNumber(data.unique_devices));
                updateElementText('avg-read-time', data.avg_read_time + " min");
                updateElementText('completion-rate', data.completion_rate + "%");
            } catch (error) {
                console.error('Error fetching overall stats:', error);
                showError('total-views');
            }
        }

        // 🔹 Fetch All Insights
        async function fetchAllInsights() {
            try {
                // Fetch all insights list
                const insightResponse = await fetch('/api/admin/insights');
                if (!insightResponse.ok) throw new Error('Failed to fetch insights');

                const insights = await insightResponse.json();

                // Fetch top articles to get stats
                const statsResponse = await fetch('/api/admin/dashboard/top-articles');
                if (!statsResponse.ok) throw new Error('Failed to fetch stats');

                const statsData = await statsResponse.json();

                // Combine data for display
                const tableBody = document.getElementById('insights-table-body');
                tableBody.innerHTML = '';

                if (!Array.isArray(insights) || insights.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-gray-500">No insights available</td></tr>';
                    return;
                }

                // Create a lookup for quick access to stats
                const statsLookup = {};
                statsData.forEach(stat => {
                    statsLookup[stat.id] = stat;
                });

                insights.forEach(insight => {
                    const stats = statsLookup[insight.id] || {
                        views: 0,
                        unique_viewers: 0,
                        avg_read_time: 0,
                        completion_rate: 0
                    };

                    const row = document.createElement('tr');
                    row.classList.add('hover:bg-gray-50', 'cursor-pointer');
                    row.dataset.id = insight.id;
                    row.innerHTML = `
                        <td class="px-6 py-4">${insight.judul}</td>
                        <td class="px-6 py-4">${formatNumber(stats.views || 0)}</td>
                        <td class="px-6 py-4">${formatNumber(stats.unique_viewers || 0)}</td>
                        <td class="px-6 py-4">${parseFloat(stats.avg_read_time || 0).toFixed(1)} min</td>
                        <td class="px-6 py-4">${parseFloat(stats.completion_rate || 0).toFixed(1)}%</td>
                        <td class="px-6 py-4">
                            <button class="view-details px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">View Details</button>
                        </td>
                    `;

                    // Add click event to row
                    row.addEventListener('click', function(e) {
                        // If the click was on the button, open the modal
                        if (e.target.classList.contains('view-details') || e.target.closest('.view-details')) {
                            openInsightModal(insight.id, insight.judul);
                        }
                    });

                    tableBody.appendChild(row);
                });

                // Add click handlers for view details buttons
                document.querySelectorAll('.view-details').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const row = this.closest('tr');
                        const insightId = row.dataset.id;
                        const insightTitle = row.querySelector('td:first-child').textContent;
                        openInsightModal(insightId, insightTitle);
                    });
                });

            } catch (error) {
                console.error('Error fetching insights:', error);
                document.getElementById('insights-table-body').innerHTML =
                    '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Error loading insights. Please try again.</td></tr>';
            }
        }

        // 🔹 Fetch Recent Views
        async function fetchRecentViews() {
            try {
                const response = await fetch('/api/admin/dashboard/recent-views');
                if (!response.ok) throw new Error('Failed to fetch recent views');

                const recentViews = await response.json();
                const recentViewsContainer = document.getElementById('recent-views');
                recentViewsContainer.innerHTML = '';

                if (!Array.isArray(recentViews) || recentViews.length === 0) {
                    recentViewsContainer.innerHTML = '<p class="text-gray-500">No recent views available</p>';
                    return;
                }

                recentViews.forEach(view => {
                    const item = document.createElement('div');
                    item.className = 'border-b pb-3';
                    item.innerHTML = `
                        <p class="font-medium">${view.title}</p>
                        <p class="text-sm text-gray-500">${view.time} • ${view.device}</p>
                    `;
                    recentViewsContainer.appendChild(item);
                });
            } catch (error) {
                console.error('Error fetching recent views:', error);
                showError('recent-views');
            }
        }

        // 🔹 Fetch Device Breakdown
        async function fetchDeviceBreakdown() {
            try {
                const response = await fetch('/api/admin/dashboard/device-breakdown');
                if (!response.ok) throw new Error('Failed to fetch device breakdown');

                const deviceData = await response.json();
                const deviceBreakdownContainer = document.getElementById('device-breakdown');
                deviceBreakdownContainer.innerHTML = '';

                if (!Array.isArray(deviceData) || deviceData.length === 0) {
                    deviceBreakdownContainer.innerHTML = '<p class="text-gray-500">No device data available</p>';
                    return;
                }

                // Create canvas for chart
                const canvas = document.createElement('canvas');
                canvas.id = 'device-chart';
                deviceBreakdownContainer.appendChild(canvas);

                // Extract data for chart
                const labels = deviceData.map(item => item.type);
                const values = deviceData.map(item => item.percentage);
                const colors = [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ];

                // Create chart
                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching device breakdown:', error);
                showError('device-breakdown');
            }
        }

        // 🔹 Open Insight Modal
        async function openInsightModal(insightId, title) {
            try {
                // Show loader in modal
                document.getElementById('modal-title').textContent = title;
                document.getElementById('modal-views').textContent = 'Loading...';
                document.getElementById('modal-unique').textContent = 'Loading...';
                document.getElementById('modal-avg-time').textContent = 'Loading...';
                document.getElementById('modal-completion').textContent = 'Loading...';

                // Show modal
                modal.classList.remove('hidden');

                // Fetch insight stats
                const response = await fetch(`/api/admin/dashboard/insight-stats/${insightId}`);
                if (!response.ok) throw new Error('Failed to fetch insight stats');

                const stats = await response.json();

                // Update modal content
                document.getElementById('modal-views').textContent = formatNumber(stats.total_views || 0);
                document.getElementById('modal-unique').textContent = formatNumber(stats.unique_viewers || 0);
                document.getElementById('modal-avg-time').textContent = `${parseFloat(stats.avg_read_time || 0).toFixed(1)} min`;
                document.getElementById('modal-completion').textContent = `${parseFloat(stats.completion_rate || 0).toFixed(1)}%`;

                // Create read time chart
                if (readTimeChart) {
                    readTimeChart.destroy();
                }

                // Sample data for read time distribution (you'll need to add an API endpoint for this)
                const readTimeData = {
                    labels: ['< 1 min', '1-3 min', '3-5 min', '5-10 min', '> 10 min'],
                    data: [
                        stats.min_read_time ? 10 : 0,
                        20,
                        stats.avg_read_time > 3 ? 40 : 20,
                        stats.avg_read_time > 5 ? 20 : 10,
                        stats.max_read_time > 10 ? 10 : 0
                    ] // Sample distribution, replace with actual data
                };

                const readTimeCtx = document.getElementById('read-time-chart').getContext('2d');
                readTimeChart = new Chart(readTimeCtx, {
                    type: 'bar',
                    data: {
                        labels: readTimeData.labels,
                        datasets: [{
                            label: 'Readers',
                            data: readTimeData.data,
                            backgroundColor: 'rgba(79, 70, 229, 0.6)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Readers'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Read Time'
                                }
                            }
                        }
                    }
                });

            } catch (error) {
                console.error('Error opening insight modal:', error);
                document.getElementById('modal-views').textContent = 'Error loading data';
                document.getElementById('modal-unique').textContent = 'Error loading data';
                document.getElementById('modal-avg-time').textContent = 'Error loading data';
                document.getElementById('modal-completion').textContent = 'Error loading data';
            }
        }

        // 🔹 Helper Function: Format Angka dengan Koma
        function formatNumber(num) {
            return Number(num).toLocaleString();
        }

        // 🔹 Helper Function: Tampilkan Error di UI
        function showError(containerId) {
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = '<p class="text-red-500">Error loading data. Please try again.</p>';
            }
        }

        // 🔹 Helper Function: Update Text Content di Elemen
        function updateElementText(id, text) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = text;
            }
        }
    });
</script>
</body>
</html>
