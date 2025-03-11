{{-- resources/views/components/team-section.blade.php --}}
<div class="container mx-auto py-16 px-6 md:px-12 lg:px-24">
    <!-- Team Members Badge & Heading -->
    <div class="text-center mb-16">
        <span class="inline-block bg-blue-100 text-black px-4 py-2 rounded-md font-bold mb-4">TEAM MEMBERS</span>
        <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-center max-w-4xl mx-auto">Meet the talented team from our company</h2>
    </div>

    <!-- Team Cards Grid -->
    <div id="team-members-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <!-- Team members will be loaded dynamically via JavaScript -->
        <div class="team-loading flex justify-center items-center col-span-full py-12">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-3 text-gray-600">Loading team members...</span>
        </div>
    </div>
</div>
