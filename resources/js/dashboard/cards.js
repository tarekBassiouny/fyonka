import Chart from 'chart.js/auto';

const chartCache = {};

export function setupCards(filters) {
    loadCards(filters);
}

async function loadCards(filters) {
    try {
        const query = new URLSearchParams(filters).toString();
        const response = await fetch(`/cards?${query}`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });

        if (!response.ok) throw new Error('Failed to load cards');
        const { data } = await response.json();

        updateCard('Revenue', data.revenue);
        updateCard('GrossProfit', data.gross_profit);
        updateCard('NetProfitMargin', data.net_margin);
        updateCard('Expenses', data.expenses);

    } catch (err) {
        console.error('Error loading cards:', err.message || err);
    }
}

function updateCard(key, { value, change, trend }) {
    const valueEl = document.getElementById(`card${key}`);
    const changeEl = document.getElementById(`change${key}`);
    const sparkId = `spark${key}`;

    const isPositive = parseFloat(value) >= 0 ?? parseFloat(change) >= 0;

    if (valueEl) {
        valueEl.textContent = formatValue(key, value);
        valueEl.classList.remove('text-green-600', 'text-red-600');
        valueEl.classList.add(isPositive ? 'text-green-600' : 'text-red-600');
    }

    if (changeEl) {
        changeEl.classList.remove('text-green-500', 'text-red-500');
        changeEl.classList.add(isPositive ? 'text-green-500' : 'text-red-500');
        changeEl.innerHTML = `
            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor" style="transform: ${isPositive ? 'none' : 'rotate(180deg)'}">
                <path d="M5 11l5-5 5 5H5z" />
            </svg>
            ${isPositive ? '+' : ''}${parseFloat(change).toFixed(1)}%
        `;
    }

    renderSparkline(sparkId, trend, isPositive ? 'green' : 'red');
}

function renderSparkline(canvasId, data, color) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    // Destroy previous chart if exists
    if (chartCache[canvasId]) {
        chartCache[canvasId].destroy();
    }

    // Create and store the new chart
    chartCache[canvasId] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map((_, i) => i + 1),
            datasets: [{
                data,
                borderColor: color,
                backgroundColor: 'transparent',
                borderWidth: 2,
                tension: 0.4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            elements: { point: { radius: 0 } },
            plugins: { legend: { display: false } },
            scales: {
                x: { display: false },
                y: { display: false },
            },
        }
    });
}

function formatValue(key, num) {
    if (key === 'NetProfitMargin') {
        return `${parseFloat(num).toFixed(2)}%`;
    }
    return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR',
    }).format(num);
}
