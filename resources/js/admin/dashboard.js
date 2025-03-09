/**
 * Dashboard.js - JavaScript for the Analytics Dashboard
 */
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
        fetchActivityData();
        fetchReadTimeDistribution();

        // Initialize charts with empty data until the API responses arrive
        createActivityChart([]);
        createDeviceChart([]);
        createReadTimeChart({ labels: [], data: [] });
    }

    function refreshData() {
        // Create "refreshing" animation
        document.getElementById('refresh-btn').classList.add('animate-spin');

        // Fetch fresh data from API
        fetchOverallStats();
        fetchTopInsights();
        fetchRecentViews();
        fetchDeviceBreakdown();
        fetchActivityData();
        fetchReadTimeDistribution();
        fetchTrendData();

        // Stop spinning after 1 second
        setTimeout(() => {
            document.getElementById('refresh-btn').classList.remove('animate-spin');
        }, 1000);
    }

    // Fetch Overall Stats from API
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

            // Fetch trend data separately to update change percentages
            fetchTrendData();
        } catch (error) {
            console.error('Error fetching overall stats:', error);
            showErrorMessage('total-views', 'unique-viewers', 'avg-read-time', 'completion-rate');
        }
    }

    // Fetch trend data to show changes over time
    async function fetchTrendData() {
        try {
            const response = await fetch(`/api/admin/dashboard/trend-data?period=${currentPeriod}`);
            if (!response.ok) throw new Error('Failed to fetch trend data');

            const trendData = await response.json();

            // Update trend indicators
            updateTrendIndicator('total-views-change', trendData.total_views_change);
            updateTrendIndicator('unique-viewers-change', trendData.unique_viewers_change);
            updateTrendIndicator('avg-read-time-change', trendData.avg_read_time_change);
            updateTrendIndicator('completion-rate-change', trendData.completion_rate_change);
        } catch (error) {
            console.error('Error fetching trend data:', error);
            // Reset trend indicators to 0% if there's an error
            document.getElementById('total-views-change').textContent = '0%';
            document.getElementById('unique-viewers-change').textContent = '0%';
            document.getElementById('avg-read-time-change').textContent = '0%';
            document.getElementById('completion-rate-change').textContent = '0%';
        }
    }

    // Update a trend indicator with proper formatting and color
    function updateTrendIndicator(elementId, value) {
        const element = document.getElementById(elementId);
        if (!element) return;

        // Format the value
        const formattedValue = `${value >= 0 ? '+' : ''}${value}%`;
        element.textContent = formattedValue;

        // Set color based on value (positive = green, negative = red)
        if (value > 0) {
            element.classList.remove('text-red-500');
            element.classList.add('text-green-500');
        } else if (value < 0) {
            element.classList.remove('text-green-500');
            element.classList.add('text-red-500');
        } else {
            element.classList.remove('text-green-500', 'text-red-500');
            element.classList.add('text-gray-500');
        }
    }

    // Fetch Top Insights from API
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
                        <div>
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

    // Fetch Recent Views from API
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
                item.className = 'flex items-center p-3 border-l-4 border-indigo-500 bg-indigo-50 rounded mb-2';
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

    // Fetch Device Breakdown from API
    async function fetchDeviceBreakdown() {
        try {
            const response = await fetch('/api/admin/dashboard/device-breakdown');
            if (!response.ok) throw new Error('Failed to fetch device breakdown');

            const devices = await response.json();
            updateDeviceChart(devices);
        } catch (error) {
            console.error('Error fetching device breakdown:', error);
            updateDeviceChart([{ type: 'No Data', percentage: 100 }]);
        }
    }

    // Fetch Activity Time Series Data from API
    async function fetchActivityData() {
        try {
            const response = await fetch(`/api/admin/dashboard/activity-time-series?period=${currentPeriod}`);
            if (!response.ok) throw new Error('Failed to fetch activity data');

            const data = await response.json();
            updateActivityChart(data);
        } catch (error) {
            console.error('Error fetching activity data:', error);
            // If there's an error, use empty data
            updateActivityChart({ views: [], readTime: [] });
        }
    }

    // Fetch Read Time Distribution from API
    async function fetchReadTimeDistribution() {
        try {
            const response = await fetch('/api/admin/dashboard/read-time-distribution');
            if (!response.ok) throw new Error('Failed to fetch read time distribution');

            const data = await response.json();
            updateReadTimeChart(data);
        } catch (error) {
            console.error('Error fetching read time distribution:', error);
            // If there's an error, use empty data
            updateReadTimeChart({ labels: [], data: [] });
        }
    }

    // Create Activity Chart
    function createActivityChart(initialData = { views: [], readTime: [] }) {
        const ctx = document.getElementById('activity-chart').getContext('2d');

        activityChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: [
                    {
                        label: 'Views',
                        data: initialData.views || [],
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Read Time (min)',
                        data: initialData.readTime || [],
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
    function createDeviceChart(initialData = []) {
        const ctx = document.getElementById('device-chart').getContext('2d');

        // Default data until API data is loaded
        const defaultData = initialData.length > 0 ? initialData : [
            { type: 'Loading...', percentage: 100 }
        ];

        deviceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: defaultData.map(item => item.type),
                datasets: [{
                    data: defaultData.map(item => item.percentage),
                    backgroundColor: ['rgba(156, 163, 175, 0.5)'], // Gray placeholder
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
    function createReadTimeChart(initialData = { labels: [], data: [] }) {
        const ctx = document.getElementById('read-time-chart').getContext('2d');

        readTimeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: initialData.labels,
                datasets: [{
                    label: 'Percentage of Views',
                    data: initialData.data,
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

    // Update Activity Chart with new data from API
    function updateActivityChart(data) {
        if (!activityChart) return;

        activityChart.data.datasets[0].data = data.views;
        activityChart.data.datasets[1].data = data.readTime;

        // Update the time unit based on current period
        activityChart.options.scales.x.time.unit = getTimeUnit();

        activityChart.update();
    }

    // Update Device Chart with real data from API
    function updateDeviceChart(devices) {
        if (!deviceChart) return;

        const colors = [
            'rgba(79, 70, 229, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(6, 182, 212, 0.8)'
        ];

        deviceChart.data.labels = devices.map(device => device.type);
        deviceChart.data.datasets[0].data = devices.map(device => device.percentage);
        deviceChart.data.datasets[0].backgroundColor = devices.map((_, i) =>
            colors[i % colors.length]
        );

        deviceChart.update();
    }

    // Update Read Time Chart with data from API
    function updateReadTimeChart(data) {
        if (!readTimeChart) return;

        readTimeChart.data.labels = data.labels;
        readTimeChart.data.datasets[0].data = data.data;

        readTimeChart.update();
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

    // Format numbers with commas
    function formatNumber(num) {
        if (!num) return "0";
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
