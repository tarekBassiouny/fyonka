<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">{{ __('uploads.title') }}</h2>

        <form id="uploadCSVForm">
            <div class="p-2">
                <label for="csvFile" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('uploads.select_file') }}
                </label>
                <input type="file" name="uploadCSV" id="csvFile" accept=".csv,.CSV,.xlsx,.XLSX" required
                    class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div id="uploadError" class="text-red-600 text-sm mt-2 hidden"></div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeUploadModal()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                    {{ __('uploads.cancel') }}
                </button>
                <button type="submit" class="bg-green-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    {{ __('uploads.analyze') }}
                </button>
            </div>
        </form>
    </div>
</div>
