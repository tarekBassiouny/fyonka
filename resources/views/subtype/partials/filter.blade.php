<div class="bg-white shadow rounded-lg p-4 mb-4">
    <form method="GET">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
            <!-- Name Filter -->
            <div>
                <input type="text" name="name" id="filterSubtypeName" value="{{ request('name') }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    placeholder="{{ __('subtype.name') }}">
            </div>

            <!-- Type Filter -->
            <div>
                <select name="type_id" id="filterSubtypeType" class="w-full border rounded px-3 py-2">
                    <option value="">{{ __('filters.type') }}</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" @selected(request('type_id') == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit + Reset -->
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    {{ __('subtype.filter') }}
                </button>
                <a href="{{ route('subtypes.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm inline-block">
                    {{ __('subtype.reset') }}
                </a>
            </div>
        </div>
    </form>
</div>
