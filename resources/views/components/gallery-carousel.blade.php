<?php
// File: resources/views/components/gallery-carousel.blade.php

use App\Services\GalleryService;
use Illuminate\Support\Facades\App;

// Get carousel images from the GalleryService
try {
    $galleryService = App::make(GalleryService::class);
    $carouselImages = $galleryService->getCarouselImages();
} catch (\Exception $e) {
    // Log the error
    \Illuminate\Support\Facades\Log::error('Error loading carousel images: ' . $e->getMessage());
    $carouselImages = [];
}
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-8">Team Activity Documentation</h1>

    <!-- Carousel Container -->
    <div class="relative max-w-5xl mx-auto">
        <!-- Carousel Slides -->
        <div id="activityCarousel" class="relative overflow-hidden rounded-lg shadow-lg">
            <!-- Slides Container -->
            <div class="carousel-inner relative w-full h-96">
                @if(count($carouselImages) > 0)
                    @foreach($carouselImages as $index => $image)
                        <div class="carousel-item absolute w-full h-full opacity-{{ $index === 0 ? '100' : '0' }} transition-all duration-500 ease-in-out transform {{ $index === 0 ? 'translate-x-0' : 'translate-x-full' }}">
                            <div class="relative h-full">
                                <img src="{{ $image->image_url }}" alt="{{ $image->title }}" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 p-4">
                                    <h3 class="text-white text-xl font-bold">{{ $image->title }}</h3>
                                    <p class="text-gray-200">{{ $image->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback content if no images are available -->
                    <div class="carousel-item absolute w-full h-full opacity-100 transition-all duration-500 ease-in-out transform translate-x-0">
                        <div class="relative h-full">
                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                <p class="text-gray-600 text-lg">No documentation images available</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if(count($carouselImages) > 1)
                <!-- Navigation Arrows with Animation Effect -->
                <button class="carousel-control-prev absolute top-1/2 left-4 transform -translate-y-1/2 z-10 bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-70 transition active:scale-90">
                    <i class="fas fa-chevron-left text-white text-xl"></i>
                </button>

                <button class="carousel-control-next absolute top-1/2 right-4 transform -translate-y-1/2 z-10 bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-70 transition active:scale-90">
                    <i class="fas fa-chevron-right text-white text-xl"></i>
                </button>

                <!-- Indicators/Dots -->
                <div class="absolute bottom-28 left-0 right-0 flex justify-center space-x-2 z-10">
                    @foreach($carouselImages as $index => $image)
                        <button class="w-3 h-3 rounded-full bg-white bg-opacity-{{ $index === 0 ? '100' : '50' }} {{ $index === 0 ? 'active' : '' }} transition-all duration-300 hover:scale-125" data-slide="{{ $index }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug logging
        console.log('Gallery carousel initialized');
        @if(count($carouselImages) > 0)
        console.log('Loaded {{count($carouselImages)}} carousel images');
        @else
        console.log('No carousel images found');
        @endif

        const carousel = document.getElementById('activityCarousel');
        if (!carousel) return;

        const items = carousel.querySelectorAll('.carousel-item');
        const indicators = carousel.querySelectorAll('[data-slide]');
        const prevBtn = carousel.querySelector('.carousel-control-prev');
        const nextBtn = carousel.querySelector('.carousel-control-next');

        let currentIndex = 0;
        const totalItems = items.length;

        if (totalItems <= 1) return; // No need for controls if only one item

        // Function to show specific slide
        function showSlide(index) {
            // Normalize index
            if (index >= totalItems) index = 0;
            if (index < 0) index = totalItems - 1;

            // Update current index
            currentIndex = index;

            // Update carousel items
            items.forEach((item, i) => {
                if (i === index) {
                    item.classList.remove('opacity-0', 'translate-x-full', '-translate-x-full');
                    item.classList.add('opacity-100', 'translate-x-0');
                } else if (i < index) {
                    item.classList.remove('opacity-100', 'translate-x-0', 'translate-x-full');
                    item.classList.add('opacity-0', '-translate-x-full');
                } else {
                    item.classList.remove('opacity-100', 'translate-x-0', '-translate-x-full');
                    item.classList.add('opacity-0', 'translate-x-full');
                }
            });

            // Update indicators
            indicators.forEach((indicator, i) => {
                if (i === index) {
                    indicator.classList.add('active', 'bg-opacity-100');
                    indicator.classList.remove('bg-opacity-50');
                } else {
                    indicator.classList.remove('active', 'bg-opacity-100');
                    indicator.classList.add('bg-opacity-50');
                }
            });
        }

        // Event handlers for controls
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                showSlide(currentIndex - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                showSlide(currentIndex + 1);
            });
        }

        // Event handlers for indicators
        indicators.forEach((indicator) => {
            indicator.addEventListener('click', () => {
                const slideIndex = parseInt(indicator.getAttribute('data-slide'));
                showSlide(slideIndex);
            });
        });

        // Auto-advance the carousel
        let interval = setInterval(() => {
            showSlide(currentIndex + 1);
        }, 5000);

        // Pause auto-advance on hover
        carousel.addEventListener('mouseenter', () => {
            clearInterval(interval);
        });

        carousel.addEventListener('mouseleave', () => {
            interval = setInterval(() => {
                showSlide(currentIndex + 1);
            }, 5000);
        });
    });
</script>
