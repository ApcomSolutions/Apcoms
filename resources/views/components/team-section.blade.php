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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug logging
        console.log('Team section initialized');

        const teamGrid = document.getElementById('team-members-grid');
        const loadingIndicator = teamGrid.querySelector('.team-loading');

        // Fetch team members from the API
        fetch('/api/teams/active')
            .then(response => {
                console.log('Team API response status:', response.status);
                if (!response.ok) {
                    throw new Error('Failed to load team members: ' + response.status);
                }
                return response.json();
            })
            .then(teamMembers => {
                console.log('Team members loaded:', teamMembers);

                // Remove loading indicator
                if (loadingIndicator) {
                    loadingIndicator.remove();
                }

                // Check if we have team members
                if (!teamMembers || teamMembers.length === 0) {
                    teamGrid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">No team members found</p>
                    </div>
                `;
                    return;
                }

                // Create HTML for each team member
                const teamHTML = teamMembers.map(member => `
                <div class="team-member bg-white rounded-lg shadow-lg overflow-hidden transition transform hover:-translate-y-2 hover:shadow-xl duration-300">
                    <div class="relative overflow-hidden">
                        <img src="${member.image_url || '/images/placeholder-person.jpg'}"
                             alt="${member.name}"
                             class="w-full h-64 object-cover transition duration-500 transform hover:scale-110">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">${member.name}</h3>
                        <p class="text-blue-600 font-medium mb-3">${member.position}</p>

                        <div class="flex space-x-3 mt-4">
                            ${member.linkedin ? `
                                <a href="${member.linkedin}" target="_blank" class="text-gray-500 hover:text-blue-600 transition">
                                    <i class="fab fa-linkedin text-lg"></i>
                                </a>
                            ` : ''}
                            ${member.twitter ? `
                                <a href="${member.twitter}" target="_blank" class="text-gray-500 hover:text-blue-400 transition">
                                    <i class="fab fa-twitter text-lg"></i>
                                </a>
                            ` : ''}
                            ${member.email ? `
                                <a href="mailto:${member.email}" class="text-gray-500 hover:text-red-500 transition">
                                    <i class="fas fa-envelope text-lg"></i>
                                </a>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');

                // Insert the team members into the grid
                teamGrid.innerHTML = teamHTML;
            })
            .catch(error => {
                console.error('Error loading team members:', error);

                // Remove loading indicator and show error
                if (loadingIndicator) {
                    loadingIndicator.remove();
                }

                teamGrid.innerHTML = `
                <div class="col-span-full text-center py-12 bg-red-50 rounded-lg">
                    <p class="text-red-600">Failed to load team members: ${error.message}</p>
                    <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                            onclick="location.reload()">Try Again</button>
                </div>
            `;
            });
    });
</script>
