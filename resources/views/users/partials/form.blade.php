<div id="userModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 id="userModalTitle" class="text-lg font-semibold text-gray-800 mb-4">
            {{ __('user .add') }}
        </h2>

        <form id="userForm">
            @csrf
            <input type="hidden" name="user_id" id="userId">

            <!-- Name -->
            <div class="mb-4">
                <label for="userName" class="block text-sm font-medium text-gray-700">
                    {{ __('user.name') }}
                </label>
                <input type="text" id="userName" name="name" placeholder="{{ __('user.name') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="userEmail" class="block text-sm font-medium text-gray-700">
                    {{ __('user.email') }}
                </label>
                <input type="email" id="userEmail" name="email" placeholder="{{ __('user.email') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <!-- Username -->
            <div class="mb-4">
                <label for="userUsername" class="block text-sm font-medium text-gray-700">
                    {{ __('user.username') }}
                </label>
                <input type="text" id="userUsername" name="username" placeholder="{{ __('user.username') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="userRole" class="block text-sm font-medium text-gray-700">
                    {{ __('user.role') }}
                </label>
                <select id="userRole" name="role"
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required>
                    <option value="dashboard">Dashboard</option>
                    <option value="api">API</option>
                </select>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="userPassword" class="block text-sm font-medium text-gray-700">
                    {{ __('user.password') }}
                </label>
                <input type="password" id="userPassword" name="password" placeholder="{{ __('user.password') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="userPasswordConfirmation" class="block text-sm font-medium text-gray-700">
                    {{ __('user.confirm_password') }}
                </label>
                <input type="password" id="userPasswordConfirmation" name="password_confirmation"
                    placeholder="{{ __('user.confirm_password') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div id="userFormErrors" class="mb-4 text-sm text-red-600 space-y-1 hidden"></div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeUserModal()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                    {{ __('store.cancel') }}
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    <span id="userSubmitLabel">{{ __('store.create') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
