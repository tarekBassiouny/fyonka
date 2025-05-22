<div id="storeDeleteModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            {{ __('store.confirm_delete_title') }}
        </h2>
        <p class="text-gray-600 mb-6">{{ __('store.confirm_delete_body') }}</p>

        <form id="deleteStoreForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                    {{ __('store.cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    {{ __('store.delete') }}
                </button>
            </div>
        </form>
    </div>
</div>
