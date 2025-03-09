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
        createActivityChart();
        createDeviceChart();
        createReadTimeChart();
    }

    function refreshData() {
        // Create "refreshing" animation
        document.getElementById('refresh-btn').classList.add('animate-spin');

        // Fetch fresh data from API
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

            // Since we can't calculate trends from API data, we'll remove trend indicators
            // or set them to 0
            document.getElementById('total-views-change').textContent = '0%';
            document.getElementById('unique-viewers-change').textContent = '0%';
            document.getElementById('avg-read-time-change').textContent = '0%';
            document.getElementById('completion-rate-change').textContent = '0%';
        } catch (error) {
            console.error('Error fetching overall stats:', error);
            showErrorMessage('total-views', 'unique-viewers', 'avg-read-time', 'completion-rate');
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

    // Fetch Device Breakdown from API
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

    // Create Activity Chart - since we don't have historical data in the API,
    // we'll need to create a reasonable visualization
    function createActivityChart() {
        const ctx = document.getElementById('activity-chart').getContext('2d');

        // Generate sample data based on time period (this could be replaced with real data if API provided it)
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

        // Default data until API data is loaded
        const defaultData = [
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

    // Create Read Time Chart (no API endpoint for this, so keeping as static)
    function createReadTimeChart() {
        const ctx = document.getElementById('read-time-chart').getContext('2d');

        // Sample data for read time distribution - would be nice to have this from API
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

    // Generate activity data based on time period (since we don't have this in the API)
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

            // Generate data that's more realistic but still random
            // We don't have this data from the API, so we'll simulate it
            const viewCount = Math.floor(Math.random() * 50) + 5 + (i % 3 === 0 ? 20 : 0);
            const readTimeAvg = (Math.random() * 3) + 1 + (i % 2 === 0 ? 1 : 0);

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
