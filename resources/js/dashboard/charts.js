import Chart from 'chart.js/auto';

let chartCache = {
    line: null,
    bar: null
};

export function setupCharts(filters) {
    loadCharts(filters);
}

async function loadCharts(filters) {
    try {
        const query = new URLSearchParams(filters).toString();
        const response = await fetch(`/charts?${query}`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });

        const { data } = await response.json();

        renderLineChart(data.months, data.net_income);
        renderBarChart(data.months, data.income, data.outcome);

    } catch (err) {
        console.error('Error loading charts:', err.message || err);
    }
}

function renderLineChart(labels, datasetsByStore) {
    if (chartCache.line) chartCache.line.destroy();

    const datasets = Object.entries(datasetsByStore).map(([store, values]) => ({
        label: store,
        data: values,
        borderWidth: 2,
        tension: 0.4
    }));

    chartCache.line = new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: { beginAtZero: true },
            }
        }
    });
}

function renderBarChart(labels, incomeData, outcomeData) {
    if (chartCache.bar) chartCache.bar.destroy();

    const t = window.translations?.charts || {};

    const datasets = Object.keys(incomeData).map(store => ({
        label: window.translations.income_label.replace(':store', store),
        data: incomeData[store],
        backgroundColor: 'rgba(34,197,94,0.6)', // green
    })).concat(Object.keys(outcomeData).map(store => ({
        label: window.translations.expense_label.replace(':store', store),
        data: outcomeData[store],
        backgroundColor: 'rgba(239,68,68,0.6)', // red
    })));

    chartCache.bar = new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: { labels, datasets },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: { beginAtZero: true },
                x: { stacked: true },
            }
        }
    });
}
