<?php
// File: resources/views/components/gallery-carousel.blade.php

use App\Services\GalleryService;
use Illuminate\Support\Facades\App;

// Get carousel images from the GalleryService
$galleryService = App::make(GalleryService::class);
$carouselImages = $galleryService->getCarouselImages();
?>

<div class="container mx-auto px-4">
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
