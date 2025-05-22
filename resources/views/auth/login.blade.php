<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/fyonka.svg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.login_title') }}</title>

    @vite('resources/css/app.css')

    @vite('resources/js/login.js')
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('storage/fyonka.svg') }}" alt="Company Logo" class="h-32">
        </div>

        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">{{ __('auth.login_title') }}</h2>

        <form id="loginForm" class="space-y-4">
            <div>
                <label for="username" class="block mb-1 text-gray-700">{{ __('auth.username') }}</label>
                <input type="text" id="username" name="username" autocomplete="username"
                    placeholder="{{ __('auth.username') }}"
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="password" class="block mb-1 text-gray-700">{{ __('auth.password') }}</label>
                <input type="password" id="password" name="password" autocomplete="current-password"
                    placeholder="{{ __('auth.password') }}"
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <label class="inline-flex items-center mb-3">
                <input type="checkbox" name="remember" value="1" class="form-checkbox text-blue-600">
                <span class="ml-2 text-sm text-gray-600">{{ __('auth.remember_me') }}</span>
            </label>

            <div id="errorMessage" class="text-red-500 text-sm">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-md transition duration-200">
                {{ __('auth.login_button') }}
            </button>

            <div class="text-center mt-4 text-sm text-gray-600">
                <a href="{{ route('lang.switch', 'en') }}"
                    class="{{ app()->getLocale() === 'en' ? 'text-blue-600 font-bold underline' : 'hover:underline' }}">
                    EN
                </a>
                <span>/</span>
                <a href="{{ route('lang.switch', 'de') }}"
                    class="{{ app()->getLocale() === 'de' ? 'text-blue-600 font-bold underline' : 'hover:underline' }}">
                    DE
                </a>
            </div>

        </form>
    </div>
</body>

</html>
