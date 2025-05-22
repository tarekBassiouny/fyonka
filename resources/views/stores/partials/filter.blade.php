<div class="bg-white shadow rounded-lg p-4 mb-4">
    <form method="GET">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <!-- Store Name Filter -->
            <div>
                <input type="text" name="name" id="filterStoreName" value="{{ request('name') }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    placeholder="{{ __('store.name') }}">
            </div>

            <!-- Filter Button -->
            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    {{ __('store.filter') }}
                </button>
                <a href="{{ route('stores.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm inline-block">
                    {{ __('store.reset') }}
                </a>
            </div>
        </div>
    </form>
</div>
