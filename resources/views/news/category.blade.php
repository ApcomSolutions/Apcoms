{{-- resources/views/news/category.blade.php --}}
<x-layout>
    <x-slot:title>{{ $currentCategory }} - News</x-slot:title>

    <x-navbar />

    <div class="bg-white py-10">
        <div class="container mx-auto px-4">
            {{-- Category Header with Description --}}
            <div class="mb-8 mt-8">
                <h1 class="text-3xl font-bold text-center">Category: {{ $currentCategory }}</h1>
                @if(isset($category) && $category->description)
                    <p class="text-center text-gray-600 mt-2">{{ $category->description }}</p>
                @endif
            </div>

            {{-- Back to all news link --}}
            <div class="mb-6">
                <a href="{{ route('news.index') }}" class="inline-flex items-center text-pink-500 hover:text-pink-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to All News
                </a>
            </div>

            {{-- Search form --}}
            <div class="mb-8 max-w-md mx-auto">
                <form action="{{ route('news.search') }}" method="GET" class="flex gap-2">
                    <input
                        type="text"
                        name="query"
                        placeholder="Search articles in this category..."
                        class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    >
                    <input type="hidden" name="category" value="{{ isset($category) ? $category->slug : '' }}">
                    <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded-md hover:bg-pink-600 transition">
                        Search
                    </button>
                </form>
            </div>

            {{-- Category selector --}}
            <div class="mb-8">
                <div class="flex flex-wrap gap-2 justify-center">
                    <a href="{{ route('news.index') }}"
                       class="px-3 py-1 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                        All
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('news.category', $cat->slug) }}"
                           class="px-3 py-1 rounded-full {{ $currentCategory == $cat->name ? 'bg-pink-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition">
                            {{ $cat->name }} ({{ $cat->news_count }})
                        </a>
                    @endforeach
                </div>
            </div>

            @if(count($news) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($news as $article)
                        <div class="bg-white shadow-md rounded-lg overflow-hidden flex flex-col h-full">
                            {{-- Display image if exists --}}
                            @if (isset($article['image_url']) && $article['image_url'])
                                <img src="{{ $article['image_url'] }}" class="w-full h-48 object-cover"
                                     alt="{{ $article['title'] }}">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="p-4 flex flex-col flex-grow">
                                <h5 class="text-lg font-semibold mb-2">{{ $article['title'] }}</h5>
                                <p class="text-gray-700 text-sm mb-4 flex-grow">
                                    {{ Str::limit(strip_tags($article['content']), 100, '...') }}
                                </p>

                                <div class="mt-auto">
                                    <div class="flex justify-between text-gray-500 text-xs mb-3">
                                        <p>By: {{ $article['author'] }}</p>
                                        <p>{{ \Carbon\Carbon::parse($article['publish_date'])->format('d M Y') }}</p>
                                    </div>

                                    <a href="{{ route('news.show', $article['slug']) }}"
                                       class="block w-full bg-pink-500 text-white text-center py-2 rounded-md hover:bg-pink-600 transition">
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-600">No articles found in this category.</p>
                </div>
            @endif

            {{-- Pagination - styled using Tailwind --}}
            @if ($news instanceof \Illuminate\Pagination\LengthAwarePaginator && $news->hasPages())
                <div class="mt-8 flex justify-center">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        {{-- Previous Page Link --}}
                        @if ($news->onFirstPage())
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                <span class="sr-only">Previous</span>
                                <!-- Heroicon name: solid/chevron-left -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <a href="{{ $news->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <!-- Heroicon name: solid/chevron-left -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($news->getUrlRange(max(1, $news->currentPage() - 2), min($news->lastPage(), $news->currentPage() + 2)) as $page => $url)
                            @if ($page == $news->currentPage())
                                <span aria-current="page" class="relative inline-flex items-center px-4 py-2 border border-pink-500 bg-pink-50 text-sm font-medium text-pink-600">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($news->hasMorePages())
                            <a href="{{ $news->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <!-- Heroicon name: solid/chevron-right -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @else
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                <span class="sr-only">Next</span>
                                <!-- Heroicon name: solid/chevron-right -->
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>

    <x-footer />
</x-layout>
