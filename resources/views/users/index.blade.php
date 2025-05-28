@extends('layouts.master')

@section('title', __('user.title'))

@section('content')

    @include('users.partials.filter')

    <!-- === User List Box === -->
    <div class="bg-white shadow rounded-lg px-6 py-6 w-full">

        <!-- Top Bar: Title + Button -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">{{ __('user.title') }}</h2>
            <button onclick="openUserModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded">
                {{ __('user.add') }}
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-800">
                <thead class="text-xs font-semibold text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{{ __('user.name') }}</th>
                        <th class="px-4 py-3">{{ __('user.email') }}</th>
                        <th class="px-4 py-3">{{ __('user.username') }}</th>
                        <th class="px-4 py-3">{{ __('user.role') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('user.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $user->id }}</td>
                            <td class="px-4 py-3">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ $user->username }}</td>
                            <td class="px-4 py-3">{{ ucfirst($user->role) }}</td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <button onclick="editUser({{ $user }})"
                                    class="text-blue-600 hover:text-blue-800 font-semibold">
                                    {{ __('user.edit') }}
                                </button>
                                <button onclick="confirmDelete({{ $user->id }})"
                                    class="text-red-600 hover:text-red-800 font-semibold">
                                    {{ __('user.delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                                {{ __('user.no_data') }}
                            </td>
                        </tr>
                    @endforelse
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
                    @foreach (request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </select>
            </form>

            <!-- Pagination -->
            <div>
                {{ $users->links() }}
            </div>
        </div>

    </div>

    <!-- === End Users Section === -->

    @include('users.partials.form')
    @include('users.partials.delete')
@endsection

@push('scripts')
    <script>
        window.translations = {
            add_user: @json(__('user.add')),
            edit_user: @json(__('user.edit')),
            create: @json(__('store.create')),
            update: @json(__('store.update')),
        };
    </script>
    @vite('resources/js/users.js')
@endpush
