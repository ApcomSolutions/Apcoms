<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>InsightTime Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.2.0/dist/chartjs-adapter-luxon.min.js"></script>
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
</head>
<body>
<div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="wakatime-gradient text-white py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold">InsightTime</h1>
                <span class="ml-4 px-2 py-1 bg-white/20 rounded text-xs">Analytics</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <span class="status-dot active-dot"></span>
                    <span class="text-sm">Live Data</span>
                </div>
                <button id="refresh-btn" class="bg-white/20 hover:bg-white/30 rounded-full p-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-8">
        <!-- Time Period Selector -->
        <div class="mb-8 flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
            <div class="flex space-x-2">
                <button class="time-period-btn px-4 py-2 text-sm font-medium rounded bg-indigo-100 text-indigo-700" data-period="day">Today</button>
                <button class="time-period-btn px-4 py-2 text-sm font-medium rounded text-gray-700 hover:bg-gray-100" data-period="week">This Week</button>
                <button class="time-period-btn px-4 py-2 text-sm font-medium rounded text-gray-700 hover:bg-gray-100" data-period="month">This Month</button>
                <button class="time-period-btn px-4 py-2 text-sm font-medium rounded text-gray-700 hover:bg-gray-100" data-period="all">All Time</button>
            </div>
        </div>

        <!-- Key Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Total Views</h3>
                <p class="text-3xl font-bold text-gray-800" id="total-views">-</p>
                <div class="mt-2 flex items-center text-sm">
                        <span class="text-green-500 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                            </svg>
                            <span id="total-views-change">12%</span>
                        </span>
                    <span class="text-gray-500 ml-2">from previous period</span>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Unique Viewers</h3>
                <p class="text-3xl font-bold text-gray-800" id="unique-viewers">-</p>
                <div class="mt-2 flex items-center text-sm">
                        <span class="text-green-500 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                            </svg>
                            <span id="unique-viewers-change">8%</span>
                        </span>
                    <span class="text-gray-500 ml-2">from previous period</span>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Avg. Read Time</h3>
                <p class="text-3xl font-bold text-gray-800" id="avg-read-time">-</p>
                <div class="mt-2 flex items-center text-sm">
                        <span class="text-green-500 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                            </svg>
                            <span id="avg-read-time-change">5%</span>
                        </span>
                    <span class="text-gray-500 ml-2">from previous period</span>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Completion Rate</h3>
                <p class="text-3xl font-bold text-gray-800" id="completion-rate">-</p>
                <div class="mt-2 flex items-center text-sm">
                        <span class="text-red-500 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                            </svg>
                            <span id="completion-rate-change">3%</span>
                        </span>
                    <span class="text-gray-500 ml-2">from previous period</span>
                </div>
            </div>
        </div>

        <!-- Daily Activity Chart -->
        <div class="card p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Daily Activity</h3>
                <div class="flex space-x-2">
                    <div class="tooltip">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span class="tooltip-text">Shows the activity over the selected time period</span>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="activity-chart"></canvas>
            </div>
        </div>

        <!-- Two Column Layout for Insights Table and Device/Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Top Insights Table -->
            <div class="card p-6 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Top Insights</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="insights-table-body">
                        <!-- Table rows will be populated via JavaScript -->
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Loading data...</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

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
        </div>

        <!-- Recent Activity -->
        <div class="card p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Recent Activity</h3>
            <div class="space-y-4" id="recent-views">
                <!-- Recent views will be populated via JavaScript -->
                <p class="text-gray-500">Loading recent activity...</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 px-6">
        <div class="container mx-auto text-center text-gray-500 text-sm">
            &copy; 2025 InsightTime. All data is updated in real-time.
        </div>
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        let activityChart = null;
        let deviceChart = null;
        let readTimeChart = null;

        // Current time period
        let currentPeriod = 'day';

        // Initialize the dashboard
        initializeDashboard();

        // Set up refresh button
        document.getElementById('refresh-btn').addEventListener('click', function() {
            refreshData();
        });

        // Set up time period buttons
        document.querySelectorAll('.time-period-btn').forEach(button => {
            button.addEventListener('click', function() {
                const period = this.getAttribute('data-period');
                setActivePeriod(period);
                refreshData();
            });
        });

        function setActivePeriod(period) {
            currentPeriod = period;
            // Update button styles
            document.querySelectorAll('.time-period-btn').forEach(btn => {
                if (btn.getAttribute('data-period') === period) {
                    btn.classList.add('bg-indigo-100', 'text-indigo-700');
                    btn.classList.remove('text-gray-700', 'hover:bg-gray-100');
                } else {
                    btn.classList.remove('bg-indigo-100', 'text-indigo-700');
                    btn.classList.add('text-gray-700', 'hover:bg-gray-100');
                }
            });
        }

        function initializeDashboard() {
            fetchOverallStats();
            fetchTopInsights();
            fetchRecentViews();
            fetchDeviceBreakdown();
            createActivityChart();
            createDeviceChart();
            createReadTimeChart();
        }

        function refreshData() {
            // Create "refreshing" animation
            document.getElementById('refresh-btn').classList.add('animate-spin');

            // Fetch fresh data
            fetchOverallStats();
            fetchTopInsights();
            fetchRecentViews();
            fetchDeviceBreakdown();
            updateActivityChart();

            // Stop spinning after 1 second
            setTimeout(() => {
                document.getElementById('refresh-btn').classList.remove('animate-spin');
            }, 1000);
        }

        // Fetch Overall Stats
        async function fetchOverallStats() {
            try {
                const response = await fetch('/api/admin/dashboard/stats');
                if (!response.ok) throw new Error('Failed to fetch stats');

                const data = await response.json();

                // Update UI with stats
                document.getElementById('total-views').textContent = formatNumber(data.total_views);
                document.getElementById('unique-viewers').textContent = formatNumber(data.unique_devices);
                document.getElementById('avg-read-time').textContent = `${data.avg_read_time} min`;
                document.getElementById('completion-rate').textContent = `${data.completion_rate}%`;

                // Update trend indicators (these would normally be calculated from real data)
                updateTrendIndicator('total-views-change', getRandomTrend(), true);
                updateTrendIndicator('unique-viewers-change', getRandomTrend(), true);
                updateTrendIndicator('avg-read-time-change', getRandomTrend(), true);
                updateTrendIndicator('completion-rate-change', getRandomTrend(), false);
            } catch (error) {
                console.error('Error fetching overall stats:', error);
                showErrorMessage('total-views', 'unique-viewers', 'avg-read-time', 'completion-rate');
            }
        }

        // Fetch Top Insights
        async function fetchTopInsights() {
            try {
                const response = await fetch('/api/admin/dashboard/top-articles');
                if (!response.ok) throw new Error('Failed to fetch top insights');

                const insights = await response.json();
                const tableBody = document.getElementById('insights-table-body');
                tableBody.innerHTML = '';

                if (!insights.length) {
                    tableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No insights data available</td></tr>';
                    return;
                }

                insights.forEach(insight => {
                    const row = document.createElement('tr');
                    row.classList.add('hover:bg-gray-50');
                    row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">${insight.judul}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${formatNumber(insight.views)}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${insight.avg_read_time} min</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="relative w-full">
                                    <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                        <div style="width:${insight.completion_rate}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">${insight.completion_rate}%</div>
                                </div>
                            </td>
                        `;
                    tableBody.appendChild(row);
                });
            } catch (error) {
                console.error('Error fetching top insights:', error);
                document.getElementById('insights-table-body').innerHTML =
                    '<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Error loading insights data</td></tr>';
            }
        }

        // Fetch Recent Views
        async function fetchRecentViews() {
            try {
                const response = await fetch('/api/admin/dashboard/recent-views');
                if (!response.ok) throw new Error('Failed to fetch recent views');

                const views = await response.json();
                const container = document.getElementById('recent-views');
                container.innerHTML = '';

                if (!views.length) {
                    container.innerHTML = '<p class="text-gray-500">No recent activity found</p>';
                    return;
                }

                views.forEach(view => {
                    const time = new Date(view.time).toLocaleString();
                    const item = document.createElement('div');
                    item.className = 'flex items-center p-3 border-l-4 border-indigo-500 bg-indigo-50 rounded';
                    item.innerHTML = `
                            <div class="mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">${view.title}</h4>
                                <p class="text-xs text-gray-500">Viewed at ${time} on ${view.device}</p>
                            </div>
                        `;
                    container.appendChild(item);
                });
            } catch (error) {
                console.error('Error fetching recent views:', error);
                document.getElementById('recent-views').innerHTML =
                    '<p class="text-red-500">Error loading recent activity data</p>';
            }
        }

        // Fetch Device Breakdown
        async function fetchDeviceBreakdown() {
            try {
                const response = await fetch('/api/admin/dashboard/device-breakdown');
                if (!response.ok) throw new Error('Failed to fetch device breakdown');

                const devices = await response.json();
                updateDeviceChart(devices);
            } catch (error) {
                console.error('Error fetching device breakdown:', error);
            }
        }

        // Create Activity Chart
        function createActivityChart() {
            const ctx = document.getElementById('activity-chart').getContext('2d');

            // Generate sample data based on time period
            const data = generateActivityData();

            activityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    datasets: [
                        {
                            label: 'Views',
                            data: data.views,
                            backgroundColor: 'rgba(79, 70, 229, 0.8)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Read Time (min)',
                            data: data.readTime,
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: getTimeUnit(),
                                displayFormats: {
                                    hour: 'HH:mm',
                                    day: 'MMM d',
                                    week: 'MMM d',
                                    month: 'MMM yyyy'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Time Period'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        }

        // Create Device Chart
        function createDeviceChart() {
            const ctx = document.getElementById('device-chart').getContext('2d');

            // Default data until real data is loaded
            const defaultData = [
                { type: 'Mobile', percentage: 65 },
                { type: 'Desktop', percentage: 25 },
                { type: 'Tablet', percentage: 8 },
                { type: 'Other', percentage: 2 }
            ];

            deviceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: defaultData.map(item => item.type),
                    datasets: [{
                        data: defaultData.map(item => item.percentage),
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw}%`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Create Read Time Chart
        function createReadTimeChart() {
            const ctx = document.getElementById('read-time-chart').getContext('2d');

            // Sample data for read time distribution
            const readTimeData = {
                labels: ['<1 min', '1-3 min', '3-5 min', '5-10 min', '>10 min'],
                data: [15, 28, 32, 18, 7]
            };

            readTimeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: readTimeData.labels,
                    datasets: [{
                        label: 'Percentage of Views',
                        data: readTimeData.data,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        borderRadius: 4
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
                                text: 'Percentage'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Update Activity Chart with new data
        function updateActivityChart() {
            if (!activityChart) return;

            const data = generateActivityData();

            activityChart.data.datasets[0].data = data.views;
            activityChart.data.datasets[1].data = data.readTime;

            // Update the time unit based on current period
            activityChart.options.scales.x.time.unit = getTimeUnit();

            activityChart.update();
        }

        // Update Device Chart with new data
        function updateDeviceChart(devices) {
            if (!deviceChart) return;

            deviceChart.data.labels = devices.map(device => device.type);
            deviceChart.data.datasets[0].data = devices.map(device => device.percentage);

            deviceChart.update();
        }

        // Generate activity data based on time period
        function generateActivityData() {
            const now = new Date();
            const data = { views: [], readTime: [] };

            let startDate, points, interval;

            switch(currentPeriod) {
                case 'day':
                    startDate = new Date(now);
                    startDate.setHours(0, 0, 0, 0);
                    points = 24;
                    interval = 1000 * 60 * 60; // 1 hour
                    break;
                case 'week':
                    startDate = new Date(now);
                    startDate.setDate(startDate.getDate() - 7);
                    points = 7;
                    interval = 1000 * 60 * 60 * 24; // 1 day
                    break;
                case 'month':
                    startDate = new Date(now);
                    startDate.setDate(1);
                    points = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate(); // Days in month
                    interval = 1000 * 60 * 60 * 24; // 1 day
                    break;
                case 'all':
                    startDate = new Date(now);
                    startDate.setMonth(startDate.getMonth() - 12);
                    points = 12;
                    interval = 1000 * 60 * 60 * 24 * 30; // ~1 month
                    break;
            }

            for (let i = 0; i < points; i++) {
                const date = new Date(startDate.getTime() + (i * interval));

                // Generate some random data (would be real data in production)
                const viewCount = Math.floor(Math.random() * 100) + 10;
                const readTimeAvg = (Math.random() * 5) + 1;

                data.views.push({
                    x: date,
                    y: viewCount
                });

                data.readTime.push({
                    x: date,
                    y: readTimeAvg
                });
            }

            return data;
        }

        // Get time unit based on current period
        function getTimeUnit() {
            switch(currentPeriod) {
                case 'day': return 'hour';
                case 'week': return 'day';
                case 'month': return 'day';
                case 'all': return 'month';
                default: return 'day';
            }
        }

        // Generate a random percentage for trend indicators
        function getRandomTrend() {
            return ((Math.random() * 20) - 10).toFixed(1);
        }

        // Update trend indicator UI
        function updateTrendIndicator(elementId, value, isPositiveGood) {
            const element = document.getElementById(elementId);
            if (!element) return;

            // Parse value to float
            const numValue = parseFloat(value);

            // Determine if the trend is positive or negative
            const isPositive = numValue > 0;

            // Set the text
            element.textContent = `${isPositive ? '+' : ''}${numValue}%`;

            // Set the color based on whether positive is good
            if (isPositiveGood) {
                element.classList.remove('text-red-500', 'text-gray-500');
                element.classList.add(isPositive ? 'text-green-500' : 'text-red-500');
            } else {
                element.classList.remove('text-green-500', 'text-gray-500');
                element.classList.add(isPositive ? 'text-red-500' : 'text-green-500');
            }

            // Update the icon
            const icon = element.previousElementSibling;
            if (icon && icon.tagName === 'SVG') {
                // Replace the icon with up or down arrow based on trend
                icon.innerHTML = isPositive ?
                    '<path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />' :
                    '<path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />';
            }
        }

        // Format numbers with commas
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Show error message in UI elements
        function showErrorMessage(...elementIds) {
            elementIds.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = 'Error';
                    element.classList.add('text-red-500');
                }
            });
        }
    });
</script>
</body>
</html>
