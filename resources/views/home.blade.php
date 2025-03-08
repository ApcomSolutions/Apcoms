<x-layout>
    <x-navbar></x-navbar>
    {{-- Hero Section --}}
    <div class="relative min-h-[80vh] flex items-center justify-center py-20 px-6 md:px-12 overflow-hidden">
        <!-- Pola latar belakang dengan gradient dan shapes -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-pink-50">
            <!-- Decorative shapes -->
            <div class="absolute top-5 left-10 w-64 h-64 rounded-full bg-pink-300 opacity-70 mix-blend-multiply parallax"
                data-speed="3"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 rounded-full bg-blue-300 opacity-70 mix-blend-multiply parallax"
                data-speed="2"></div>
            <div class="absolute top-1/4 right-1/4 w-40 h-40 rounded-full bg-indigo-400 opacity-60 mix-blend-multiply parallax"
                data-speed="4"></div>



            <!-- Subtle grid pattern overlay -->
            <div class="absolute inset-0 opacity-10"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%236b7280\' fill-opacity=\'0.2\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'1\'/%3E%3C/g%3E%3C/svg%3E');">
            </div>
        </div>

        <!-- Container -->
        <div class="container mx-auto flex flex-col md:flex-row items-center relative z-10">
            <!-- Text Content -->
            <div
                class="md:w-[60%] w-full mb-8 md:mb-0 p-6 md:p-8 bg-white bg-opacity-80 rounded-lg shadow-lg backdrop-blur-sm border border-gray-100">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-6 pb-2 gradient-text">
                    Mitra Strategis Reputasi Digital Anda
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl mb-6 font-medium text-gray-700">
                    Membangun dan Mempertahankan Reputasi Terbaik di Era Digital
                </p>
                <p class="text-sm sm:text-base md:text-lg mb-8 leading-relaxed text-gray-600">
                    Kami menggabungkan kreativitas, inovasi, dan pendekatan berkelanjutan untuk mendukung kebutuhan
                    komunikasi bisnis Anda. Dengan layanan unggulan mulai dari pendampingan humas kreatif hingga
                    publikasi ilmiah, kami siap membawa reputasi Anda ke tingkat yang lebih tinggi.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#"
                        class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-3 px-8 rounded-full shadow-md transition duration-300 text-center">
                        Konsultasi Sekarang
                    </a>
                    <a href="#"
                        class="border-gradient font-medium py-3 px-8 rounded-full shadow-md text-center transition duration-300">
                        <span
                            class="bg-gradient-to-r from-pink-500 to-blue-500 text-transparent bg-clip-text transition duration-300 hover:text-white">
                            Lihat Layanan Kami
                        </span>
                    </a>

                </div>


            </div>
        </div>
    </div>
    <x-footer></x-footer>



</x-layout>
<style>
    .gradient-text {
        background-image: linear-gradient(to right, #ec4899, #3b82f6);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        display: inline-block;
    }

    /* Improved gradient border button */
    .border-gradient {
        position: relative;
        border: none;
        background-color: white;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .border-gradient::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: inherit;
        padding: 2px;
        background: linear-gradient(to right, #ec4899, #3b82f6);
        -webkit-mask:
            linear-gradient(#fff 0 0) content-box,
            linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        z-index: -1;
    }

    /* Make sure the text remains visible with proper coloring */
    .border-gradient:hover {
        color: white;
        background-image: linear-gradient(to right, #ec4899, #3b82f6);
    }



    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .float-animation {
        animation: float 8s ease-in-out infinite;
    }
</style>
<script>
    function parallaxEffect() {
        let scrollY = window.scrollY;
        document.querySelectorAll(".parallax").forEach((el) => {
            let speed = el.getAttribute("data-speed");
            let xMove = (scrollY / speed) * 1.2; // Tambah efek horizontal
            let yMove = (scrollY / speed) * 2.5; // Tambah efek vertikal lebih kuat
            el.style.transform = `translate(${xMove}px, ${yMove}px)`;
        });
        requestAnimationFrame(parallaxEffect);
    }

    document.addEventListener("DOMContentLoaded", parallaxEffect);
</script>
