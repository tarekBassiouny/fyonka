document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('storeForm')?.addEventListener('submit', submitStoreForm);
    document.getElementById('deleteStoreForm')?.addEventListener('submit', submitDeleteForm);
});

window.openStoreModal = function (store = null) {
    const modal = document.getElementById('storeModal');
    const form = document.getElementById('storeForm');
    const errorEl = document.getElementById('storeFormErrors');

    form.reset();
    errorEl.innerHTML = '';
    errorEl.classList.add('hidden');
    form.store_id.value = '';

    document.getElementById('storeModalTitle').textContent = store
        ? window.translations?.edit_store || 'Edit Store'
        : window.translations?.add_store || 'Add Store';

    document.getElementById('storeSubmitLabel').textContent = store
        ? window.translations?.update || 'Update'
        : window.translations?.create || 'Create';

    if (store) {
        form.store_id.value = store.id;
        form.name.value = store.name;
    }

    modal.classList.remove('hidden');
};

window.closeStoreModal = function () {
    document.getElementById('storeModal').classList.add('hidden');
};

window.editStore = function (store) {
    openStoreModal(store);
};

window.confirmDelete = function (storeId) {
    const form = document.getElementById('deleteStoreForm');
    form.action = `/stores/${storeId}`;
    document.getElementById('storeDeleteModal').classList.remove('hidden');
};

window.closeDeleteModal = function () {
    document.getElementById('storeDeleteModal').classList.add('hidden');
};

async function submitStoreForm(e) {
    e.preventDefault();

    const form = e.target;
    const errorEl = document.getElementById('storeFormErrors');
    errorEl.innerHTML = '';
    errorEl.classList.add('hidden');

    const storeId = form.store_id.value;
    const formData = new FormData(form);
    const url = storeId ? `/stores/${storeId}` : '/stores';

    if (storeId) {
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

        window.closeStoreModal();
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

        window.closeStoreModal();
        setTimeout(() => { window.location.reload(); }, 1000);

    } catch (err) {
        alert('Server error.');
    }
}
