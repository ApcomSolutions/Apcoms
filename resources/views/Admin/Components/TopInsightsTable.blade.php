{{-- resources/views/admin/components/TopInsightsTable.blade.php --}}
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
