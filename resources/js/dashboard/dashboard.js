import { setupFilters, getFilterParams } from './filters.js';
import { setupCards } from './cards.js';
import { setupCharts } from './charts.js';
import { setupUpload } from './upload.js';
import { setupTransactions } from './table.js';

document.addEventListener('DOMContentLoaded', () => {
    setupFilters(() => {
        const filters = getFilterParams();
        setupCards(filters);
        setupCharts(filters);
        setupTransactions(filters);
    });
    setupUpload();

    let confirmAction = null;

    function showConfirmation({ title, body, onConfirm }) {
        document.getElementById('confirmModalTitle').textContent = title;
        document.getElementById('confirmModalBody').textContent = body;
        document.getElementById('confirmModal').classList.remove('hidden');

        confirmAction = onConfirm;
    }

    function cancelConfirmation() {
        document.getElementById('confirmModal').classList.add('hidden');
        confirmAction = null;
    }

    document.getElementById('confirmActionBtn').addEventListener('click', () => {
        if (typeof confirmAction === 'function') {
            confirmAction();
            cancelConfirmation();
        }
    });

    // ✅ Make them globally accessible
    window.showConfirmation = showConfirmation;
    window.cancelConfirmation = cancelConfirmation;
    window.setupCards = setupCards;
    window.setupCharts = setupCharts;
});

