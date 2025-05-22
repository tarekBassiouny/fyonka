<!-- === Charts Section === -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Line Chart -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4">
            {{ __('charts.net_profit_by_store') }}
        </h2>
        <canvas id="lineChart" height="200"></canvas>
    </div>

    <!-- Column Chart -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-700 mb-4">
            {{ __('charts.income_vs_expense_by_store') }}
        </h2>
        <canvas id="barChart" height="200"></canvas>
    </div>

</div>
<!-- === End Charts Section === -->
