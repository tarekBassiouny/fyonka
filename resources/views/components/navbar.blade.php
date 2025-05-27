@php
    function nav_class($route)
    {
        return request()->routeIs($route) ? 'text-blue-600 font-bold' : 'hover:text-blue-600';
    }
@endphp

<nav x-data="{ open: false }" class="bg-white shadow p-4 text-sm font-semibold text-gray-700">
    <!-- Mobile: Top row with logo + toggle -->
    <div class="flex items-center justify-between md:hidden">
        <img src="{{ asset('storage/fyonka.svg') }}" alt="Logo" class="h-10" />
        <button @click="open = !open" class="focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Desktop: Full nav bar -->
    <div :class="{ 'block': open, 'hidden': !open }"
        class="md:flex md:items-center md:justify-between mt-4 md:mt-0 hidden">
        <!-- Left: Logo + Nav Links (md+) -->
        <div class="flex items-center gap-6">
            <img src="{{ asset('storage/fyonka.svg') }}" alt="Logo" class="h-10 hidden md:block" />
            <ul class="flex flex-col md:flex-row gap-4 md:gap-6">
                <li><a href="{{ route('dashboard.index') }}"
                        class="{{ nav_class('dashboard.index') }}">{{ __('nav.dashboard') }}</a></li>
                <li><a href="{{ route('stores.index') }}" class="{{ nav_class('stores.*') }}">{{ __('nav.stores') }}</a>
                </li>
                <li><a href="{{ route('subtypes.index') }}"
                        class="{{ nav_class('subtypes.*') }}">{{ __('nav.transaction_subtype') }}</a></li>
                <li><a href="{{ route('convert.index') }}"
                        class="{{ nav_class('convert.*') }}">{{ __('nav.convert') }}</a></li>
            </ul>
        </div>

        <!-- Right: Logout -->
        <div class="mt-4 md:mt-0">
            <!-- Right: Logout + Language Switch -->
            <div class="flex items-center gap-6 mt-4 md:mt-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:underline">
                        {{ __('nav.logout') }}
                    </button>
                </form>

                <div class="flex items-center gap-2 text-sm text-gray-600">
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
            </div>

        </div>
    </div>
</nav>
