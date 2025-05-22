document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('subtypeForm')?.addEventListener('submit', submitSubtypeForm);
    document.getElementById('deleteSubtypeForm')?.addEventListener('submit', submitDeleteSubtypeForm);
    loadTypes();
});

window.openSubtypeModal = function (subtype = null) {
    const modal = document.getElementById('subtypeModal');
    const form = document.getElementById('subtypeForm');
    const errorEl = document.getElementById('subtypeFormErrors');

    form.reset();
    errorEl.innerHTML = '';
    errorEl.classList.add('hidden');
    form.subtype_id.value = '';

    document.getElementById('subtypeModalTitle').textContent = subtype
        ? window.translations?.edit_subtype || 'Edit Subtype'
        : window.translations?.add_subtype || 'Add Subtype';

    document.getElementById('subtypeSubmitLabel').textContent = subtype
        ? window.translations?.update || 'Update'
        : window.translations?.create || 'Create';

    if (subtype) {
        form.subtype_id.value = subtype.id;
        form.name.value = subtype.name;
        form.transaction_type_id.value = subtype.transaction_type_id;
    }

    modal.classList.remove('hidden');
};

window.closeSubtypeModal = function () {
    document.getElementById('subtypeModal').classList.add('hidden');
};

window.editSubtype = function (subtype) {
    openSubtypeModal(subtype);
};

window.confirmSubtypeDelete = function (subtypeId) {
    const form = document.getElementById('deleteSubtypeForm');
    form.action = `/subtypes/${subtypeId}`;
    document.getElementById('subtypeDeleteModal').classList.remove('hidden');
};

window.closeDeleteModal = function () {
    document.getElementById('subtypeDeleteModal').classList.add('hidden');
};

async function submitSubtypeForm(e) {
    e.preventDefault();

    const form = e.target;
    const errorEl = document.getElementById('subtypeFormErrors');
    errorEl.innerHTML = '';
    errorEl.classList.add('hidden');

    const subtypeId = form.subtype_id.value;
    const formData = new FormData(form);
    const url = subtypeId ? `/subtypes/${subtypeId}` : '/subtypes';

    if (subtypeId) {
        formData.append('_method', 'PUT');
        console.log(subtypeId)
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

        window.closeSubtypeModal();
        setTimeout(() => { window.location.reload(); }, 1000);

    } catch (err) {
        errorEl.textContent = 'Server error.';
        errorEl.classList.remove('hidden');
    }
}

async function submitDeleteSubtypeForm(e) {
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

        window.closeDeleteModal();
        setTimeout(() => { window.location.reload(); }, 1000);

    } catch (err) {
        alert('Server error.');
    }
}

async function loadTypes() {
    const select = document.getElementById('subtypeType');
    if (!select) return;

    try {
        const response = await fetch('/api/type', {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });

        const types = (await response.json()).data;

        select.innerHTML = `<option value="">${window.translations?.type || 'Select Type'}...</option>`;
        types.forEach(type => {
            const opt = document.createElement('option');
            opt.value = type.id;
            opt.textContent = type.name;
            select.appendChild(opt);
        });
    } catch (err) {
        console.error('Failed to load types:', err);
    }
}
