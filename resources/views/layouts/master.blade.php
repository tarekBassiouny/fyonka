<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('storage/fyonka.svg') }}">
    <title>@yield('title', 'Dashboard - Finanzverfolgung')</title>

    <!-- Tailwind CSS -->
    @vite('resources/css/app.css')

    @vite('resources/js/app.js')

    @stack('head')
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- === Top Navbar === -->
    <x-navbar />

    <div class="p-6 space-y-6">
        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>
