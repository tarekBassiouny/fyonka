let currentPage = 1;
let perPage = 10;
let totalPages = 1;
let filteredTransactions = [];
let allTransactions = [];
let types = [];
let subtypes = [];
let stores = [];

const choicesInstances = new Map();

export function setupTransactions(filters = {}) {
    loadTransactions(filters);
    document.getElementById('perPage')?.addEventListener('change', (e) => {
        perPage = parseInt(e.target.value, 10);
        currentPage = 1;
        loadTransactions(getFilterParams());
    });

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('bulk-checkbox')) {
            const all = document.querySelectorAll('.bulk-checkbox');
            const checked = document.querySelectorAll('.bulk-checkbox:checked');
            const header = document.querySelector('thead input[type="checkbox"]');
            if (header) {
                header.checked = all.length === checked.length;
            }
        }
    });
}

async function loadTransactions(filters = {}) {
    const query = new URLSearchParams({
        ...filters,
        page: currentPage,
        per_page: perPage
    }).toString();

    const res = await fetch(`/transactions/data?${query}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        credentials: 'include'
    });
    const data = await res.json();

    allTransactions = data.data;
    filteredTransactions = [...allTransactions];
    totalPages = data.meta.last_page;

    await fetchDropdowns();
    renderTable();
    renderPagination(data.meta);
}

async function fetchDropdowns() {
    const res = await fetch('/api/dropdowns');
    const data = await res.json();
    types = data.data.types;
    subtypes = data.data.subtypes;
    stores = data.data.stores;
}

function renderTable() {
    const tbody = document.getElementById('transactionBody');
    tbody.innerHTML = '';

    filteredTransactions.forEach((tx, index) => {
        const filteredSubtypes = subtypes.filter(s => s.transaction_type_id === tx.type?.id);

        const row = document.createElement('tr');
        row.className = 'bg-white border-b';

        const isEditable = tx.is_temp === 1;
        const id = tx.id;

        row.innerHTML = `
            <td class="px-2 py-2">
                <input type="checkbox" class="bulk-checkbox" data-id="${id}" />
            </td>

            <td class="px-4 py-2">
                <div class="flex items-center gap-1">
                    <span id="error-date-${id}" class="text-red-500 text-lg hidden">❗</span>
                    ${isEditable ? `<input type="date" value="${tx.date}" class="w-full border rounded px-2 py-1" id="date-${id}" />` : tx.date}
                </div>
            </td>

            <td class="px-4 py-2">
                <div class="flex items-center gap-1">
                    <span id="error-desc-${id}" class="text-red-500 text-lg hidden">❗</span>
                    ${isEditable ? `<input type="text" value="${tx.description ?? ''}" class="w-full border rounded px-2 py-1" id="desc-${id}" />` : tx.description ?? ''}
                </div>
            </td>

            <td class="px-4 py-2 ${!tx.amount ? 'text-black-600' : tx.amount < 0 ? 'text-red-600' : 'text-green-600'}">
                <div class="flex items-center gap-1">
                    <span id="error-amount-${id}" class="text-sm text-red-600 mt-1 block hidden">❗</span>
                    ${isEditable ? `<input type="number" value="${tx.amount}" class="w-full border rounded px-2 py-1" id="amount-${id}" />` : tx.amount}
                </div>
            </td>

            <td class="px-4 py-2">
                ${isEditable ? `
                <div class="flex items-center gap-1">
                    <span id="error-type-${id}" class="text-red-500 text-lg hidden">❗</span>
                    <select id="type-${id}" class="w-full border rounded px-2 py-1" onchange="onTypeChange('${id}')">
                        <option value=""></option>
                        ${types.map(t => `<option value="${t.id}" ${t.id === tx.type?.id ? 'selected' : ''}>${t.name}</option>`).join('')}
                    </select>` : (tx.type?.name || '-')}
                </div>
            </td>

            <td class="px-4 py-2">
                ${isEditable ? `
                <div class="flex items-center gap-1">
                    <span id="error-subtype-${id}" class="text-red-500 text-lg hidden">❗</span>
                    <select id="subtype-${id}" class="w-full border rounded px-2 py-1">
                        <option value="">${window.translations.select_value}</option>
                        ${filteredSubtypes.map(s => `<option value="${s.id}" ${s.id === tx.subtype?.id ? 'selected' : ''}>${s.name}</option>`).join('')}
                    </select>` : (tx.subtype?.name || '-')}
                </div>
            </td>

            <td class="px-4 py-2">
                ${isEditable ? `
                <div class="flex items-center gap-1">
                    <span id="error-store-${id}" class="text-red-500 text-lg hidden">❗</span>
                    <select id="store-${id}" class="w-full border rounded px-2 py-1">
                        <option value="">${window.translations.select_value}</option>
                        ${stores.map(s => `<option value="${s.id}" ${s.id === tx.store?.id ? 'selected' : ''}>${s.name}</option>`).join('')}
                    </select>` : (tx.store?.name || '-')}
                </div>
            </td>

            <td class="px-4 py-2">
                ${isEditable ? `
                    <div class="flex items-center justify-center gap-6 text-2xl leading-none">
                        <button onclick="approveTransaction('${id}')" class="text-green-600" aria-label="${window.translations.approve}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>  
                        </button>
                        <button onclick="rejectTransaction('${id}')" class="text-red-600" aria-label="${window.translations.reject}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                ` : `
                    <div class="flex items-center justify-center gap-4 text-sm font-medium">
                        <button onclick="editTransaction('${id}')" class="text-blue-600 hover:underline">
                            ${window.translations.edit}
                        </button>
                        <button onclick="deleteTransaction('${id}')" class="text-red-600 hover:underline">
                            ${window.translations.delete}
                        </button>
                    </div>
                `}
            </td>
        `;

        tbody.appendChild(row);

        // Apply Tom Select to editable rows
        if (isEditable) {
            ['type', 'subtype', 'store'].forEach(key => {
                const el = document.getElementById(`${key}-${id}`);
                if (el) {
                    const instance = new Choices(el, {
                        searchEnabled: true,
                        shouldSort: false,
                        itemSelectText: ''
                    });
                    choicesInstances.set(`${key}-${id}`, instance);
                }
            });
        }
    });
}

function renderPagination(meta) {
    const container = document.getElementById('paginationControls');
    container.innerHTML = '';

    meta.links.forEach(link => {
        const button = document.createElement('button');
        button.innerHTML = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
        button.disabled = link.url === null;
        button.className = `px-2 py-1 rounded ${link.active ? 'bg-blue-500 text-white' : 'hover:underline'}`;
        button.onclick = () => {
            if (link.url) {
                currentPage = getPageFromUrl(link.url);
                loadTransactions(getFilterParams());
            }
        };
        container.appendChild(button);
    });
}

function getPageFromUrl(url) {
    const params = new URLSearchParams(url.split('?')[1]);
    return parseInt(params.get('page') || '1', 10);
}

function onTypeChange(id) {
    const typeSelect = document.getElementById(`type-${id}`);
    const subtypeSelect = document.getElementById(`subtype-${id}`);
    const selectedTypeId = parseInt(typeSelect.value);

    // Exit if type is not selected
    if (!selectedTypeId || !subtypeSelect) return;

    // Filter subtypes for the selected type
    const filtered = subtypes.filter(s => s.transaction_type_id === selectedTypeId);

    // Rebuild the subtype dropdown
    subtypeSelect.innerHTML = `
        <option value="">${window.translations.select_value}</option>
        ${filtered.map(s => `<option value="${s.id}">${s.name}</option>`).join('')}
    `;

    // Get existing instance and clear it
    const instance = choicesInstances.get(`subtype-${id}`);
    if (instance) {
        instance.clearStore();
        instance.setChoices(
            filtered.map(s => ({ value:  s.id, label: s.name })),
            'value',
            'label',
            true
        );
    }
}

function applyErrorStyle(field, message) {
    const fieldId = field.id;
    const icon = document.getElementById(`error-${fieldId}`);

    if (icon) icon.classList.remove('hidden');
}

function validateTransactionFromDOM(id) {
    let valid = true;

    const requiredFields = [
        `date-${id}`,
        `amount-${id}`,
        `type-${id}`,
        `subtype-${id}`,
        `store-${id}`
    ];

    const typeId = document.getElementById(`type-${id}`)?.value;
    const needsSubtype = subtypes.some(s => s.transaction_type_id == typeId);
    if (needsSubtype) {
        requiredFields.push(`subtype-${id}`);
    }

    requiredFields.forEach(fieldId => {
        const el = document.getElementById(fieldId);
        if (!el) return;

        if (!el.value?.trim()) {
            applyErrorStyle(el);
            valid = false;
        } else {
            const icon = document.getElementById(`error-${fieldId}`);
            if (icon) icon.classList.add('hidden');
        }
    });

    return valid;
}

async function approveTransaction(id) {
    if (!validateTransactionFromDOM(id)) return;
    try {
        const payload = collectTransactionData(id);

        const url = id === 'new' ? '/transactions' : `/transactions/${id}/approve`;

        // only send request if all fields are valid
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            loadTransactions(getFilterParams());

            // ✅ Refresh dashboard visuals
            const filters = getFilterParams();
            setupCards(filters);
            setupCharts(filters);
        } else if (res.status === 422) {
            const result = await res.json();

            // Loop through each field and show the error
            Object.entries(result.errors).forEach(([key, messages]) => {
                // Dynamically get the element by ID
                const errorElement = document.getElementById(`error-${key}-${id}`);

                if (errorElement) {
                    errorElement.textContent = messages.join(', ');
                    errorElement.classList.remove('hidden'); // Optional: make visible if hidden
                }

                // Optionally style the related input field
                const inputElement = document.querySelector(`[name="${key}"]`);
                if (inputElement) {
                    applyErrorStyle(inputElement); // your custom function
                }
            });
        }

    } catch (err) {
        alert('Network error during approval');
    }
}

async function rejectTransaction(id) {

    const tx = allTransactions.find(t => t.id == id);

    // Handle inline "new" row → remove it
    if (id === 'new') {
        filteredTransactions = filteredTransactions.filter(t => t.id !== 'new');
        renderTable();
        return;
    }

    // Handle edited approved row → revert to non-edit mode
    if (tx.is_editing) {
        tx.is_temp = false;
        delete tx.is_editing;
        renderTable();
        return;
    }

    // Real temp transaction → confirm reject/delete
    try {
        showConfirmation({
            title: window.translations.confirm_title,
            body: window.translations.confirm_body,
            onConfirm: async () => {
                const res = await fetch(`/transactions/${id}/reject`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    loadTransactions(getFilterParams());

                    // ✅ Refresh dashboard visuals
                    const filters = getFilterParams();
                    setupCards(filters);
                    setupCharts(filters);
                }
            }
        });
    } catch (err) {
        alert('Network error during rejection');
    }
}

async function deleteTransaction(id) {
    try {
        showConfirmation({
            title: window.translations.confirm_title,
            body: window.translations.confirm_body,
            onConfirm: async () => {
                const res = await fetch(`/transactions/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    loadTransactions(getFilterParams());

                    // ✅ Refresh dashboard visuals
                    const filters = getFilterParams();
                    setupCards(filters);
                    setupCharts(filters);
                }
            }
        });
    } catch (err) {
        alert('Network error during rejection');
    }
}

function collectTransactionData(id) {
    return {
        id: id === 'new' ? null : parseInt(id),
        date: document.getElementById(`date-${id}`)?.value,
        amount: document.getElementById(`amount-${id}`)?.value,
        description: document.getElementById(`desc-${id}`)?.value,
        type_id: document.getElementById(`type-${id}`)?.value,
        subtype_id: document.getElementById(`subtype-${id}`)?.value || null,
        store_id: document.getElementById(`store-${id}`)?.value,
        is_temp: id !== 'new'
    };
}

function editTransaction(id) {
    const tx = allTransactions.find(t => t.id === parseInt(id));
    if (!tx) return;

    tx.is_editing = 1;       // store original temp flag
    tx.is_temp = 1;                  // force into editable mode
    renderTable();
}

function insertInlineAddRow() {
    // Prevent adding multiple
    if (filteredTransactions.some(t => t.id === 'new')) return;

    const tempRow = {
        id: 'new',
        date: '',
        amount: '',
        description: '',
        store: null,
        type: null,
        subtype: null,
        is_temp: 1
    };

    filteredTransactions.unshift(tempRow);
    renderTable();
}

function toggleAllSelection(sourceCheckbox) {
    const checkboxes = document.querySelectorAll('.bulk-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = sourceCheckbox.checked;
    });
}

async function bulkApprove() {
    const selectedIds = Array.from(document.querySelectorAll('.bulk-checkbox:checked'))
        .map(cb => cb.dataset.id);

    if (selectedIds.length === 0) return;

    const rowsToApprove = getValidatedApprovedRows(selectedIds);
    if (!rowsToApprove || rowsToApprove.length === 0) return;

    showConfirmation({
        title: window.translations.confirm_title,
        body: window.translations.confirm_body,
        onConfirm: async () => {
            const res = await fetch('/transactions/bulk-approve', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ transactions: rowsToApprove })
            });
            if (res.ok) {
                loadTransactions(getFilterParams());

                // ✅ Refresh dashboard visuals
                const filters = getFilterParams();
                setupCards(filters);
                setupCharts(filters);

                const headerCheckbox = document.querySelector('thead input[type="checkbox"]');
                if (headerCheckbox) headerCheckbox.checked = false;
            }
        }
    });
}

async function bulkReject() {
    const selectedIds = Array.from(document.querySelectorAll('.bulk-checkbox:checked'))
        .map(cb => cb.dataset.id);

    const tempIds = selectedIds.filter(id => {
        const tx = allTransactions.find(t => t.id == id);
        return tx && tx.is_temp;
    });

    if (tempIds.length === 0) return;

    showConfirmation({
        title: window.translations.confirm_title,
        body: window.translations.confirm_body,
        onConfirm: async () => {
            const res = await fetch('/transactions/bulk-reject', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ids: tempIds })
            });

            if (res.ok) {
                loadTransactions(getFilterParams());

                // ✅ Refresh dashboard visuals
                const filters = getFilterParams();
                setupCards(filters);
                setupCharts(filters);

                const headerCheckbox = document.querySelector('thead input[type="checkbox"]');
                if (headerCheckbox) headerCheckbox.checked = false;
            }
        }
    });
}

function getValidatedApprovedRows(ids) {
    const validRows = [];

    for (const id of ids) {
        if (id === 'new') continue;

        const tx = allTransactions.find(t => t.id == id);
        if (!tx || !tx.is_temp) continue;

        if (!validateTransactionFromDOM(id)) return null; // stop if any invalid

        const data = collectTransactionData(id);
        data.is_temp = false;
        validRows.push(data);
    }

    return validRows;
}


window.onTypeChange = onTypeChange;
window.approveTransaction = approveTransaction;
window.rejectTransaction = rejectTransaction;
window.deleteTransaction = deleteTransaction;
window.editTransaction = editTransaction;
window.insertInlineAddRow = insertInlineAddRow;
window.toggleAllSelection = toggleAllSelection;
window.bulkApprove = bulkApprove;
window.bulkReject = bulkReject;
