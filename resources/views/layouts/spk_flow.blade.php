<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PickyNet - @yield('title', 'SPK')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @stack('scripts')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <div class="w-full h-2 bg-gray-200 relative">
        <div class="absolute inset-y-0 left-0 bg-red-500 transition-all duration-300" style="width: @yield('progress_width', '0%');"></div>
    </div>

    <div class="relative w-full bg-blue-500 py-4 px-6 flex items-center justify-center flex-shrink-0">
        <a href="@yield('back_link_route', '#')" class="absolute left-6 top-1/2 -translate-y-1/2 text-white hover:text-blue-200 transition">
            <img src="{{ asset('storage/images/back.png') }}" alt="Back" class="h-8 w-8">
        </a>
        <h1 class="text-white text-xl font-bold">@yield('page_header_title', 'Page Title')</h1>
    </div>

    <main class="flex-grow bg-blue-500 flex items-center justify-center p-6">
        @yield('content')
    </main>

    @include('partials._spk_bottom_nav', ['currentPageName' => Route::currentRouteName() ? explode('.', Route::currentRouteName())[1] : 'overview'])



</body>

</html>