// resources/js/team.js
document.addEventListener('DOMContentLoaded', function() {
    // Check if the team section exists on the page
    const teamGrid = document.getElementById('team-members-grid');
    if (!teamGrid) return;

    // Fetch team members from API
    fetchTeamMembers();

    /**
     * Fetch active team members from the API
     */
    async function fetchTeamMembers() {
        try {
            const response = await fetch('/api/teams/active');

            if (!response.ok) {
                throw new Error('Failed to fetch team members');
            }

            const teamMembers = await response.json();
            renderTeamMembers(teamMembers);
        } catch (error) {
            console.error('Error fetching team members:', error);
            displayErrorMessage();
        }
    }

    /**
     * Render team members to the grid
     * @param {Array} teamMembers - Array of team member objects
     */
    function renderTeamMembers(teamMembers) {
        // Remove loading indicator
        const loadingElement = teamGrid.querySelector('.team-loading');
        if (loadingElement) {
            loadingElement.remove();
        }

        // If no team members found
        if (teamMembers.length === 0) {
            teamGrid.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">No team members found.</p>
                </div>
            `;
            return;
        }

        // Clear existing content
        teamGrid.innerHTML = '';

        // Render each team member
        teamMembers.forEach(member => {
            const memberCard = document.createElement('div');
            memberCard.className = 'team-card';

            // Default initials for avatar if no image
            const initials = member.name
                .split(' ')
                .map(n => n[0])
                .join('');

            memberCard.innerHTML = `
                <div class="rounded-lg overflow-hidden">
                    ${member.image_url
                ? `<img src="${member.image_url}" alt="${member.name}" class="w-full h-64 object-cover">`
                : `<div class="h-64 w-full bg-purple-100 flex items-center justify-center">
                            <span class="text-purple-500 font-bold text-2xl">${initials}</span>
                          </div>`
            }
                </div>
                <div class="mt-3">
                    <p class="text-gray-600 text-sm md:text-base">${member.position}</p>
                    <h3 class="text-lg md:text-xl lg:text-2xl font-bold">${member.name}</h3>
                </div>
            `;

            teamGrid.appendChild(memberCard);
        });
    }

    /**
     * Display error message if team members can't be loaded
     */
    function displayErrorMessage() {
        // Remove loading indicator
        const loadingElement = teamGrid.querySelector('.team-loading');
        if (loadingElement) {
            loadingElement.remove();
        }

        teamGrid.innerHTML = `
            <div class="col-span-full text-center py-8">
                <p class="text-red-500">Unable to load team members. Please try again later.</p>
            </div>
        `;
    }
});
