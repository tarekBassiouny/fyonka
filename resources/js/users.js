document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('userForm')?.addEventListener('submit', submitUserForm);
    document.getElementById('deleteUserForm')?.addEventListener('submit', submitDeleteForm);
});

window.openUserModal = function (user = null) {
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const errorEl = document.getElementById('userFormErrors');

    form.reset();
    errorEl.innerHTML = '';
    errorEl.classList.add('hidden');
    form.user_id.value = '';

    document.getElementById('userModalTitle').textContent = user
        ? window.translations?.edit_user || 'Edit User'
        : window.translations?.add_user || 'Add User';

    document.getElementById('userSubmitLabel').textContent = user
        ? window.translations?.update || 'Update'
        : window.translations?.create || 'Create';

    if (user) {
        form.user_id.value = user.id;
        form.name.value = user.name;
        form.email.value = user.email;
        form.username.value = user.username;
        form.role.value = user.role;

        const roleSelect = form.role;
        [...roleSelect.options].forEach(option => {
            option.selected = option.value === user.role;
        });
    } else {
        form.role.value = '';
    }

    modal.classList.remove('hidden');
};

window.closeUserModal = function () {
    document.getElementById('userModal').classList.add('hidden');
};

window.editUser = function (user) {
    openUserModal(user);
};

window.confirmDelete = function (userId) {
    const form = document.getElementById('deleteUserForm');
    form.action = `/users/${userId}`;
    document.getElementById('userDeleteModal').classList.remove('hidden');
};

window.closeDeleteModal = function () {
    document.getElementById('userDeleteModal').classList.add('hidden');
};

async function submitUserForm(e) {
    e.preventDefault();

    const form = e.target;
    const errorEl = document.getElementById('userFormErrors');
    errorEl.innerHTML = '';
    errorEl.classList.add('hidden');

    const userId = form.user_id.value;
    const formData = new FormData(form);
    const url = userId ? `/users/${userId}` : '/users';

    if (userId) {
        formData.append('_method', 'PUT');
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData,
        });

        const result = await response.json();

        if (result.status === 'error' || !response.ok) {
            const messages = result.errors
                ? Object.values(result.errors).flat()
                : [result.message || 'Something went wrong'];

            errorEl.innerHTML = messages.map(msg => `<div>${msg}</div>`).join('');
            errorEl.classList.remove('hidden');
            return;
        }

        window.closeUserModal();
        setTimeout(() => { window.location.reload(); }, 1000);

    } catch (err) {
        errorEl.textContent = 'Server error.';
        errorEl.classList.remove('hidden');
    }
}

async function submitDeleteForm(e) {
    e.preventDefault();
    const form = e.target;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: new URLSearchParams({ _method: 'DELETE' }),
        });

        const result = await response.json();

        if (result.status === 'error' || !response.ok) {
            alert(result.message || 'Delete failed');
            return;
        }

        window.closeUserModal();
        setTimeout(() => { window.location.reload(); }, 1000);

    } catch (err) {
        alert('Server error.');
    }
}
