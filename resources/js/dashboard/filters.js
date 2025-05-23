export function setupFilters(onChange = () => {}) {
    loadInitialFilters().then(() => onChange());

    ['filterFrom', 'filterTo', 'filterType', 'filterSubtype', 'filterStore'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', () => onChange());
        }
    });

    document.getElementById('filterType')?.addEventListener('change', async () => {
        await handleTypeChange();
        onChange();
    });
}

async function apiFetch(url, options = {}) {
    return fetch(url, {
        ...options,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(options.headers || {})
        },
        credentials: 'include'
    }).then(res => res.json());
}

async function loadInitialFilters() {
    try {
        const [typesRes, storesRes] = await Promise.all([
            apiFetch('/api/type'),
            apiFetch('/api/store'),
        ]);

        const types = (await typesRes).data;
        const stores = (await storesRes).data;

        populateSelect('filterType', types, { valueKey: 'id', labelKey: 'name', includeAll: true });
        populateSelect('filterStore', stores, { valueKey: 'id', labelKey: 'name', includeAll: true });
    } catch (err) {
        console.error('Error loading filters:', err);
    }
}

async function handleTypeChange() {
    const typeId = document.getElementById('filterType').value;
    const subtypeSelect = document.getElementById('filterSubtype');

    if (!typeId || typeId === 'All') {
        subtypeSelect.innerHTML = '';
        subtypeSelect.disabled = true;
        return;
    }

    try {
        const res = await apiFetch(`/api/type/${typeId}/subtype`);
        const subtypes = (await res).data;
        populateSelect('filterSubtype', subtypes, {
            valueKey: 'id',
            labelKey: 'name',
            includeAll: true
        });
        subtypeSelect.disabled = false;
    } catch (err) {
        console.error('Error loading subtypes:', err);
    }
}

function populateSelect(id, items, { valueKey, labelKey, includeAll = false }) {
    const select = document.getElementById(id);
    if (!select) return;

    select.innerHTML = '';
    if (includeAll) {
        const allOpt = document.createElement('option');
        allOpt.value = '';
        allOpt.textContent = window.translations.select_all;
        select.appendChild(allOpt);
    }

    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item[valueKey];
        option.textContent = item[labelKey];
        select.appendChild(option);
    });
}

// function apiFetch(url) {
//     return fetch(url, {
//         headers: { 'Accept': 'application/json' },
//         credentials: 'same-origin',
//     });
// }

export function getFilterParams() {
    return {
        date_from: document.getElementById('filterFrom')?.value,
        date_to: document.getElementById('filterTo')?.value,
        type_id: document.getElementById('filterType')?.value ?? null,
        subtype_id: document.getElementById('filterSubtype')?.value ?? null,
        store_id: document.getElementById('filterStore')?.value ?? null,
    };
}
window.getFilterParams = getFilterParams;
