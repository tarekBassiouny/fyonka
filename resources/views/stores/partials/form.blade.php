<div id="storeModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 id="storeModalTitle" class="text-lg font-semibold text-gray-800 mb-4">
            {{ __('store.add_store') }}
        </h2>

        <form id="storeForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="store_id" id="storeId">

            <!-- Name -->
            <div class="mb-4">
                <label for="storeName" class="block text-sm font-medium text-gray-700">
                    {{ __('store.store_name') }}
                </label>
                <input type="text" id="storeName" name="name" placeholder="{{ __('store.store_name') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <!-- Image -->
            <div class="mb-4">
                <label for="storeImage" class="block text-sm font-medium text-gray-700">
                    {{ __('store.store_image') }}
                </label>
                <input type="file" id="storeImage" name="image" accept="image/*"
                    class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2">
            </div>

            <div id="storeFormErrors" class="mb-4 text-sm text-red-600 space-y-1 hidden"></div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeStoreModal()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                    {{ __('store.cancel') }}
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    <span id="storeSubmitLabel">{{ __('store.create') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
