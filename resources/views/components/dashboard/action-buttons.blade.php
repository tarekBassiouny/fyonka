<div class="flex flex-wrap gap-4 justify-start">

    <!-- Upload CSV -->
    <button onclick="openUploadModal()"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold">
        {{ __('dashboard.upload_csv') }}
    </button>

    <!-- Add Store -->
    <button onclick="openStoreModal()"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-semibold">
        {{ __('store.add_store') }}
    </button>

    <!-- Add Subtype -->
    <button onclick="openSubtypeModal()"
        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm font-semibold">
        {{ __('subtype.add_subtype') }}
    </button>

    <!-- Generate PDF -->
    <button id="generatePDF"
        class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded text-sm font-semibold">
        {{ __('dashboard.generate_pdf') }}
    </button>

</div>
