<div id="subtypeModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 id="subtypeModalTitle" class="text-lg font-bold mb-4">{{ __('subtype.add_subtype') }}</h2>

        <form id="subtypeForm">
            <input type="hidden" name="subtype_id">

            <div class="mb-4">
                <label for="type_id" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('subtype.type') }}
                </label>
                <select name="transaction_type_id" id="subtypeType" required
                    class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('subtype.type') }}...</option>
                    <!-- Options will be loaded via JS -->
                </select>
            </div>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('subtype.name') }}
                </label>
                <input type="text" name="name" id="subtypeName" required
                    class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div id="subtypeFormErrors" class="text-red-600 text-sm mb-2 hidden"></div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeSubtypeModal()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                    {{ __('subtype.cancel') }}
                </button>
                <button type="submit" id="subtypeSubmitLabel"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    {{ __('subtype.create') }}
                </button>
            </div>
        </form>
    </div>
</div>
