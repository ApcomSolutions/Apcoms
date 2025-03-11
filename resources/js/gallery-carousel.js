// File path: resources/js/gallery-carousel.js
document.addEventListener('DOMContentLoaded', function() {
    // Get carousel elements
    const carousel = document.getElementById('activityCarousel');
    if (!carousel) return; // Exit if carousel doesn't exist on the page

    const items = carousel.querySelectorAll('.carousel-item');
    if (items.length <= 1) return; // Exit if there's only one or no images

    const indicatorButtons = carousel.querySelectorAll('[data-slide]');
    const prevButton = carousel.querySelector('.carousel-control-prev');
    const nextButton = carousel.querySelector('.carousel-control-next');
    let currentIndex = 0;
    let isAnimating = false; // Flag to prevent multiple clicks during animation

    // Function to show a specific slide with animation
    function showSlide(newIndex, direction) {
        if (isAnimating || newIndex === currentIndex) return;
        isAnimating = true;

        // Set direction (left or right)
        const currentSlide = items[currentIndex];
        const nextSlide = items[newIndex];

        // Default direction is 'next' (right to left)
        let currentExitTransform = 'translate-x-full';
        let nextEntryTransform = '-translate-x-full';

        if (direction === 'prev') {
            currentExitTransform = '-translate-x-full';
            nextEntryTransform = 'translate-x-full';
        }

        // Reset position of next slide
        nextSlide.classList.remove('translate-x-0', '-translate-x-full', 'translate-x-full');
        nextSlide.classList.add(nextEntryTransform);
        nextSlide.classList.remove('opacity-0');
        nextSlide.classList.add('opacity-100');

        // Small delay to ensure the browser registers the position
        setTimeout(() => {
            // Animate current slide out
            currentSlide.classList.remove('translate-x-0');
            currentSlide.classList.add(currentExitTransform);
            currentSlide.classList.remove('opacity-100');
            currentSlide.classList.add('opacity-0');

            // Animate next slide in
            nextSlide.classList.remove(nextEntryTransform);
            nextSlide.classList.add('translate-x-0');

            // Update indicators
            indicatorButtons.forEach((button, idx) => {
                if (idx === newIndex) {
                    button.classList.add('bg-opacity-100');
                    button.classList.add('active');
                } else {
                    button.classList.remove('bg-opacity-100');
                    button.classList.remove('active');
                }
            });

            // Update current index and release animation lock after animation completes
            setTimeout(() => {
                currentIndex = newIndex;
                isAnimating = false;
            }, 500); // Match this to the transition duration
        }, 10);
    }

    // Add click button animation
    function addButtonClickAnimation(button) {
        button.addEventListener('mousedown', function() {
            this.classList.add('scale-90');
        });

        button.addEventListener('mouseup', function() {
            this.classList.remove('scale-90');
        });

        button.addEventListener('mouseleave', function() {
            this.classList.remove('scale-90');
        });
    }

    // Next button
    if (nextButton) {
        nextButton.addEventListener('click', function() {
            const newIndex = (currentIndex + 1) % items.length;
            showSlide(newIndex, 'next');
        });
        addButtonClickAnimation(nextButton);
    }

    // Previous button
    if (prevButton) {
        prevButton.addEventListener('click', function() {
            const newIndex = (currentIndex - 1 + items.length) % items.length;
            showSlide(newIndex, 'prev');
        });
        addButtonClickAnimation(prevButton);
    }

    // Indicator buttons
    indicatorButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            const direction = index > currentIndex ? 'next' : 'prev';
            showSlide(index, direction);
        });

        // Add hover animation
        button.addEventListener('mouseenter', function() {
            this.classList.add('scale-125');
        });

        button.addEventListener('mouseleave', function() {
            this.classList.remove('scale-125');
        });
    });

    // Add touch swipe functionality
    let touchStartX = 0;
    let touchEndX = 0;

    carousel.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });

    carousel.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        if (touchEndX < touchStartX - swipeThreshold) {
            // Swipe left - next slide
            const newIndex = (currentIndex + 1) % items.length;
            showSlide(newIndex, 'next');
        } else if (touchEndX > touchStartX + swipeThreshold) {
            // Swipe right - previous slide
            const newIndex = (currentIndex - 1 + items.length) % items.length;
            showSlide(newIndex, 'prev');
        }
    }

    // Auto-play carousel
    let autoplayInterval = setInterval(function() {
        if (!document.hidden) {
            const newIndex = (currentIndex + 1) % items.length;
            showSlide(newIndex, 'next');
        }
    }, 5000); // Change slide every 5 seconds

    // Pause autoplay on hover
    carousel.addEventListener('mouseenter', function() {
        clearInterval(autoplayInterval);
    });

    carousel.addEventListener('mouseleave', function() {
        autoplayInterval = setInterval(function() {
            if (!document.hidden) {
                const newIndex = (currentIndex + 1) % items.length;
                showSlide(newIndex, 'next');
            }
        }, 5000);
    });
});
