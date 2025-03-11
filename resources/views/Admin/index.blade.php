{{-- resources/views/Admin/Dashboard.blade.php --}}
<x-layout>
    <x-slot:title>Admin Dashboard</x-slot:title>

    <!-- Header -->
    <header class="header-gradient text-white py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold">APCOM Admin</h1>
                <span class="ml-4 px-2 py-1 bg-white/20 rounded text-xs">Dashboard</span>
            </div>
            <div class="flex space-x-4">
                <a href="/" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Back to Site
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Admin Dashboard</h2>
            <p class="text-gray-600 mt-2">Welcome to APCOM Admin. Manage your website content here.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Insights Stats Card -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Insights</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['insights'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.insights') }}" class="text-blue-500 hover:text-blue-700 text-sm font-medium">Manage Insights →</a>
                </div>
            </div>

            <!-- Categories Stats Card -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Categories</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['categories'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.categories') }}" class="text-green-500 hover:text-green-700 text-sm font-medium">Manage Categories →</a>
                </div>
            </div>

            <!-- Team Stats Card -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Team Members</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['teams'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.teams') }}" class="text-purple-500 hover:text-purple-700 text-sm font-medium">Manage Team →</a>
                </div>
            </div>

            <!-- Clients Stats Card -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Clients</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['clients'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.clients') }}" class="text-yellow-500 hover:text-yellow-700 text-sm font-medium">Manage Clients →</a>
                </div>
            </div>

            <!-- Gallery Stats Card -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-pink-500">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Gallery Images</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['gallery'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-pink-100 text-pink-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.gallery') }}" class="text-pink-500 hover:text-pink-700 text-sm font-medium">Manage Gallery →</a>
                </div>
            </div>

            <!-- Carousel Stats Card -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Carousel Images</h3>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['carousel'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.gallery') }}?filter=carousel" class="text-indigo-500 hover:text-indigo-700 text-sm font-medium">Manage Carousel →</a>
                </div>
            </div>
        </div>

        <!-- Recent Items -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Insights -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Recent Insights</h3>
                <div class="divide-y">
                    @forelse($recentInsights as $insight)
                        <div class="py-3">
                            <h4 class="font-medium text-gray-700">{{ $insight->judul }}</h4>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($insight->TanggalTerbit)->format('M d, Y') }} by {{ $insight->penulis }}</p>
                        </div>
                    @empty
                        <p class="py-3 text-gray-500">No insights found</p>
                    @endforelse
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.insights') }}" class="text-blue-500 hover:text-blue-700 text-sm font-medium">View All →</a>
                </div>
            </div>

            <!-- Recent Team Members -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Recent Team Members</h3>
                <div class="divide-y">
                    @forelse($recentTeams as $team)
                        <div class="py-3">
                            <h4 class="font-medium text-gray-700">{{ $team->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $team->position }}</p>
                        </div>
                    @empty
                        <p class="py-3 text-gray-500">No team members found</p>
                    @endforelse
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.teams') }}" class="text-purple-500 hover:text-purple-700 text-sm font-medium">View All →</a>
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Recent Clients</h3>
                <div class="divide-y">
                    @forelse($recentClients as $client)
                        <div class="py-3">
                            <h4 class="font-medium text-gray-700">{{ $client->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $client->position }}{{ $client->company ? ' at ' . $client->company : '' }}</p>
                        </div>
                    @empty
                        <p class="py-3 text-gray-500">No clients found</p>
                    @endforelse
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.clients') }}" class="text-yellow-500 hover:text-yellow-700 text-sm font-medium">View All →</a>
                </div>
            </div>

            <!-- Recent Gallery Images -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Recent Gallery Images</h3>
                <div class="divide-y">
                    @forelse($recentGallery as $image)
                        <div class="py-3">
                            <h4 class="font-medium text-gray-700">{{ $image->title }}</h4>
                            <p class="text-sm text-gray-500">{{ $image->is_carousel ? 'In Carousel' : 'Gallery Only' }}</p>
                        </div>
                    @empty
                        <p class="py-3 text-gray-500">No gallery images found</p>
                    @endforelse
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.gallery') }}" class="text-pink-500 hover:text-pink-700 text-sm font-medium">View All →</a>
                </div>
            </div>
        </div>
    </main>

    @push('styles')
        <style>
            .header-gradient {
                background: linear-gradient(135deg, #4f46e5, #7c3aed);
            }
        </style>
    @endpush
</x-layout>
