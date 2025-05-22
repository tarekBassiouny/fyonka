<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2 sm:gap-4">
        <h2 class="text-xl font-bold text-gray-700">{{ __('transaction.title') }}</h2>

        <div class="flex flex-wrap gap-2">
            <button onclick="bulkApprove()" class="bg-green-600 text-white px-4 py-2 rounded text-sm">
                {{ __('transaction.bulk_approve') }}
            </button>
            <button onclick="bulkReject()" class="bg-red-600 text-white px-4 py-2 rounded text-sm">
                {{ __('transaction.bulk_reject') }}
            </button>
            <button onclick="insertInlineAddRow()" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
                {{ __('transaction.add') }}
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700" id="transactionTable">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                <tr>
                    <th class="px-2 py-2"><input type="checkbox" onclick="toggleAllSelection(this)" /></th>
                    <th class="px-4 py-2">{{ __('transaction.date') }}</th>
                    <th class="px-4 py-2">{{ __('transaction.description') }}</th>
                    <th class="px-4 py-2">{{ __('transaction.amount') }}</th>
                    <th class="px-4 py-2">{{ __('transaction.type') }}</th>
                    <th class="px-4 py-2">{{ __('transaction.subtype') }}</th>
                    <th class="px-4 py-2">{{ __('transaction.store') }}</th>
                    <th class="px-4 py-2 text-center">{{ __('transaction.actions') }}</th>
                </tr>
            </thead>
            <tbody id="transactionBody">
                {{-- Rows will be rendered via JS --}}
            </tbody>
        </table>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4 gap-3">
            <div>
                <label for="perPage" class="mr-2 text-sm text-gray-600">{{ __('transaction.per_page') }}:</label>
                <select id="perPage" class="border rounded px-2 py-1 text-sm sm:w-auto">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <div id="paginationControls"
                class="flex flex-wrap gap-1 text-sm text-blue-600 justify-center sm:justify-end"></div>
        </div>
    </div>
</div>

<div id="confirmModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6">
        <h2 id="confirmModalTitle" class="text-gray-600 mb-6 font-bold">
            {{ __('generic.confirm_title') }}
        </h2>
        <p id="confirmModalBody" class="text-gray-600 mb-6">
            {{ __('generic.confirm_body') }}
        </p>
        <div class="flex justify-center gap-4">
            <button onclick="cancelConfirmation()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                {{ __('generic.cancel') }}
            </button>
            <button id="confirmActionBtn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                {{ __('generic.confirm') }}
            </button>
        </div>
    </div>
</div>


