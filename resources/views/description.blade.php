<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deskripsi PickyNet</title>
    @vite('resources/css/app.css')
    <style>
        .slide-hidden {
            display: none;
        }
        .slide-visible {
            display: flex;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="relative w-full h-screen bg-white shadow-xl overflow-hidden">

        {{-- Tombol Skip --}}
        <a href="{{ route('home') }}" class="absolute top-6 right-6 bg-white text-gray-800 px-6 py-2 rounded-full font-semibold shadow hover:bg-gray-200 z-10 transition duration-200">
            Skip
        </a>

        {{-- Container Slides --}}
        <div id="description-slides" class="relative w-full h-full overflow-hidden">

            {{-- Slide 1 --}}
            <div id="slide-1" class="absolute inset-0 slide-visible flex flex-col md:flex-row items-center bg-orange-500 text-white p-8 transition-all duration-500">
                <div class="md:w-1/2 flex justify-center">
                    <img src="{{ asset('storage/images/deskripsi1.png') }}" alt="Slide 1" class="max-w-full h-auto">
                </div>
                <div class="md:w-1/2 mt-6 md:mt-0 text-center md:text-left px-4">
                    <h2 class="text-4xl font-bold mb-4">PickyNet</h2>
                    <p class="text-lg leading-relaxed">
                        Pickynet adalah sebuah aplikasi Sistem Pendukung Keputusan (SPK) berbasis web yang dirancang untuk membantu pengguna dalam memilih penyedia layanan internet (WiFi) terbaik berdasarkan berbagai kriteria penilaian. 
                    </p>
                </div>
            </div>

            {{-- Slide 2 --}}
            <div id="slide-2" class="absolute inset-0 slide-hidden flex flex-col md:flex-row items-center bg-green-500 text-white p-8 transition-all duration-500">
                <div class="md:w-1/2 flex justify-center">
                    <img src="{{ asset('storage/images/deskripsi2.png') }}" alt="Slide 2" class="max-w-full h-auto">
                </div>
                <div class="md:w-1/2 mt-6 md:mt-0 text-center md:text-left px-4">
                    <h2 class="text-4xl font-bold mb-4">WASPAS</h2>
                    <p class="text-lg leading-relaxed">
                        Aplikasi ini memanfaatkan metode Weighted Aggregated Sum Product Assessment (WASPAS) untuk menghasilkan perhitungan yang akurat dan transparan dalam proses pengambilan keputusan.
                    </p>
                </div>
            </div>

            {{-- Slide 3 --}}
            <div id="slide-3" class="absolute inset-0 slide-hidden flex flex-col md:flex-row items-center bg-purple-700 text-white p-8 transition-all duration-500">
                <div class="md:w-1/2 flex justify-center">
                    <img src="{{ asset('storage/images/deskripsi3.png') }}" alt="Slide 3" class="max-w-full h-auto">
                </div>
                <div class="md:w-1/2 mt-6 md:mt-0 text-center md:text-left px-4">
                    <h2 class="text-4xl font-bold mb-4">Mengapa PickyNet?</h2>
                    <p class="text-lg leading-relaxed">
                        Di era digital, memilih provider internet terbaik bisa jadi membingungkan. Pickynet hadir sebagai solusi cerdas untuk membantu kamu membandingkan dan menilai provider WiFi berdasarkan kecepatan, harga, stabilitas, dan layanan — agar keputusanmu jadi lebih mudah dan tepat.
                </div>
            </div>
        </div>

        {{-- Navigasi Bawah --}}
        <div class="absolute bottom-0 w-full flex items-center justify-between px-8 py-4 bg-white">
            {{-- Tombol Previous --}}
            <button id="prev-slide" class="flex items-center text-gray-500 font-semibold transition duration-200 opacity-50 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Previous
            </button>

            {{-- Dots Pagination --}}
            <div class="flex space-x-3" id="pagination-dots">
                <span class="dot w-3 h-3 bg-gray-800 rounded-full cursor-pointer" data-slide="1"></span>
                <span class="dot w-3 h-3 bg-gray-300 rounded-full cursor-pointer" data-slide="2"></span>
                <span class="dot w-3 h-3 bg-gray-300 rounded-full cursor-pointer" data-slide="3"></span>
            </div>

            {{-- Tombol Next --}}
            <button id="next-slide" class="flex items-center text-gray-800 font-semibold transition duration-200">
                Next
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Script Slide Control --}}
    <script>
        const slides = document.querySelectorAll('#description-slides > div');
        const dots = document.querySelectorAll('.dot');
        const prevButton = document.getElementById('prev-slide');
        const nextButton = document.getElementById('next-slide');
        let currentSlideIndex = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('slide-visible', i === index);
                slide.classList.toggle('slide-hidden', i !== index);
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-gray-800', i === index);
                dot.classList.toggle('bg-gray-300', i !== index);
            });

            prevButton.classList.toggle('opacity-50', index === 0);
            prevButton.classList.toggle('cursor-not-allowed', index === 0);

            if (index === slides.length - 1) {
                nextButton.innerHTML = `<span class="font-semibold">Selesai</span>`;
                nextButton.onclick = () => window.location.href = "{{ route('home') }}";
            } else {
                nextButton.innerHTML = `Next <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>`;
                nextButton.onclick = nextSlide;
            }
        }

        function nextSlide() {
            if (currentSlideIndex < slides.length - 1) {
                currentSlideIndex++;
                showSlide(currentSlideIndex);
            }
        }

        function prevSlide() {
            if (currentSlideIndex > 0) {
                currentSlideIndex--;
                showSlide(currentSlideIndex);
            }
        }

        prevButton.addEventListener('click', prevSlide);
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                currentSlideIndex = parseInt(dot.dataset.slide) - 1;
                showSlide(currentSlideIndex);
            });
        });

        showSlide(currentSlideIndex);
    </script>

</body>
</html>
