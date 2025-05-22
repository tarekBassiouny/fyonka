@extends('layouts.master')

@section('title', __('subtype.subtypes'))

@section('content')

    @include('subtype.partials.filter')

    <!-- === Subtype List Box === -->
    <div class="bg-white shadow rounded-lg px-6 py-6 w-full">

        <!-- Top Bar: Title + Button -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">{{ __('subtype.subtypes') }}</h2>
            <button onclick="openSubtypeModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded">
                {{ __('subtype.add_subtype') }}
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-800">
                <thead class="text-xs font-semibold text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{{ __('subtype.name') }}</th>
                        <th class="px-4 py-3">{{ __('subtype.type') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('subtype.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subtypes as $subtype)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $subtype->id }}</td>
                            <td class="px-4 py-3">{{ $subtype->name }}</td>
                            <td class="px-4 py-3">{{ $subtype->type->name }}</td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <button onclick="editSubtype({{ $subtype }})"
                                    class="text-blue-600 hover:text-blue-800 font-semibold">
                                    {{ __('subtype.edit') }}
                                </button>
                                <button onclick="confirmSubtypeDelete({{ $subtype->id }})"
                                    class="text-red-600 hover:text-red-800 font-semibold">
                                    {{ __('subtype.delete') }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-4 py-2 flex justify-end items-center gap-4">

            <!-- Per Page Selector -->
            <form method="GET" class="flex items-center gap-2">
                <label for="perPage" class="text-sm text-gray-700">{{ __('subtype.per_page') }}</label>
                <select name="per_page" id="perPage" class="border rounded px-2 py-1 text-sm"
                    onchange="this.form.submit()">
                    @foreach ([10, 25, 50, 100] as $option)
                        <option value="{{ $option }}" @selected(request('per_page', 10) == $option)>
                            {{ $option }}
                        </option>
                    @endforeach

                    {{-- Preserve all filters except pagination --}}
                    @foreach (request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </select>
            </form>

            <!-- Pagination Links -->
            <div>
                {{ $subtypes->links() }}
            </div>
        </div>

    </div>

    <!-- === End Subtypes Section === -->

    @include('subtype.partials.form')
    @include('subtype.partials.delete')
@endsection

@push('scripts')
    <script>
        window.translations = {
            add_subtype: @json(__('subtype.add_subtype')),
            edit_subtype: @json(__('subtype.edit_subtype')),
            create: @json(__('subtype.create')),
            update: @json(__('subtype.update')),
        };
    </script>
    @vite('resources/js/subtypes.js')
@endpush
