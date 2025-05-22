@extends('layouts.master')

@section('title', __('store.stores'))

@section('content')

    @include('stores.partials.filter')

    <!-- === Store List Box === -->
    <div class="bg-white shadow rounded-lg px-6 py-6 w-full">

        <!-- Top Bar: Title + Button -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">{{ __('store.stores') }}</h2>
            <button onclick="openStoreModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded">
                {{ __('store.add_store') }}
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-800">
                <thead class="text-xs font-semibold text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{{ __('store.name') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('store.image') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('store.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stores as $store)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $store->id }}</td>
                            <td class="px-4 py-3">{{ $store->name }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($store->image_path)
                                    <img src="{{ asset('storage/' . $store->image_path) }}" alt="{{ __('store.alt') }}"
                                        class="h-10 w-10 rounded-full object-cover mx-auto">
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <button onclick="editStore({{ $store }})"
                                    class="text-blue-600 hover:text-blue-800 font-semibold">
                                    {{ __('store.edit') }}
                                </button>
                                <button onclick="confirmDelete({{ $store->id }})"
                                    class="text-red-600 hover:text-red-800 font-semibold">
                                    {{ __('store.delete') }}
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
                <label for="perPage" class="text-sm text-gray-700">{{ __('store.per_page') }}</label>
                <select name="per_page" id="perPage" class="border rounded px-2 py-1 text-sm"
                    onchange="this.form.submit()">
                    @foreach ([10, 25, 50, 100] as $option)
                        <option value="{{ $option }}" @selected(request('per_page', 10) == $option)>
                            {{ $option }}
                        </option>
                    @endforeach

                    {{-- Preserve all other filters --}}
                    @foreach (request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </select>
            </form>

            <!-- Pagination Links -->
            <div>
                {{ $stores->links() }}
            </div>
        </div>

    </div>

    <!-- === End Stores Section === -->

    @include('stores.partials.form')
    @include('stores.partials.delete')
@endsection

@push('scripts')
    <script>
        window.translations = {
            add_store: @json(__('store.add_store')),
            edit_store: @json(__('store.edit_store')),
            create: @json(__('store.create')),
            update: @json(__('store.update')),
        };
    </script>
    @vite('resources/js/stores.js')
@endpush
