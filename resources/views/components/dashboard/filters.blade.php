<div class="bg-white shadow rounded-lg p-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Date From -->
        <div>
            <label for="filterFrom" class="block text-sm font-medium text-gray-700">{{ __('filters.date_from') }}</label>
            <input type="date" id="filterFrom" class="w-full border rounded px-3 py-2" />
        </div>

        <!-- Date To -->
        <div>
            <label for="filterTo" class="block text-sm font-medium text-gray-700">{{ __('filters.date_to') }}</label>
            <input type="date" id="filterTo" class="w-full border rounded px-3 py-2" />
        </div>

        <!-- Type (dynamically loaded) -->
        <div>
            <label for="filterType" class="block text-sm font-medium text-gray-700">{{ __('filters.type') }}</label>
            <select id="filterType" class="w-full border rounded px-3 py-2">
                <option value="">{{ __('filters.select_all') }}</option> <!-- Populated via JS -->
            </select>
        </div>

        <!-- Subtype (handled next step) -->
        <div id="filterOutcomeTypeWrapper">
            <label for="filterSubtype" class="block text-sm font-medium text-gray-700">{{ __('filters.subtype') }}</label>
            <select id="filterSubtype" class="w-full border rounded px-3 py-2" disabled>
                <option value="">{{ __('filters.select_all') }}</option> <!-- Populated based on Type -->
            </select>
        </div>

        <!-- Store (dynamically loaded) -->
        <div>
            <label for="filterStore" class="block text-sm font-medium text-gray-700">{{ __('filters.store') }}</label>
            <select id="filterStore" class="w-full border rounded px-3 py-2">
                <option value="">{{ __('filters.select_all') }}</option> <!-- Populated via JS -->
            </select>
        </div>
    </div>
</div>
