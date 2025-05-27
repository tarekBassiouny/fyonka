<div class="bg-white shadow rounded-lg p-4 mb-4">
    <form method="GET">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <!-- Name Filter -->
            <div>
                <input type="text" name="name" value="{{ request('name') }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    placeholder="{{ __('user.name') }}">
            </div>

            <!-- Email Filter -->
            <div>
                <input type="text" name="email" value="{{ request('email') }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    placeholder="{{ __('user.email') }}">
            </div>

            <!-- Username Filter -->
            <div>
                <input type="text" name="username" value="{{ request('username') }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    placeholder="{{ __('user.username') }}">
            </div>

            <!-- Role Filter -->
            <div>
                <select name="role"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                    <option value="">{{ __('user.role') }}</option>
                    <option value="dashboard" @selected(request('role') === 'dashboard')>Dashboard</option>
                    <option value="api" @selected(request('role') === 'api')>API</option>
                </select>
            </div>

            <!-- Filter + Reset Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    {{ __('store.filter') }}
                </button>
                <a href="{{ route('users.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm inline-block">
                    {{ __('store.reset') }}
                </a>
            </div>
        </div>
    </form>
</div>
