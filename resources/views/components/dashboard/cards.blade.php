<!-- === Summary Cards Section === -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 w-full">

    <!-- Revenue -->
    <div class="bg-white border shadow rounded-xl p-4 flex flex-col justify-between w-full">
        <div class="text-xs font-medium text-gray-500 uppercase">{{ __('cards.revenue') }}</div>
        <div class="flex items-end justify-between mt-2">
            <div id="cardRevenue" class="text-3xl font-bold">$0.00</div>
            <div id="changeRevenue" class="flex items-center text-xs">
                <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path d="M5 11l5-5 5 5H5z" /></svg>
                +0.0%
            </div>
        </div>
        <div class="mt-2 h-[60px]">
            <canvas id="sparkRevenue" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Gross Profit -->
    <div class="bg-white border shadow rounded-xl p-4 flex flex-col justify-between w-full">
        <div class="text-xs font-medium text-gray-500 uppercase">{{ __('cards.gross_profit') }}</div>
        <div class="flex items-end justify-between mt-2">
            <div id="cardGrossProfit" class="text-3xl font-bold">$0.00</div>
            <div id="changeGrossProfit" class="flex items-center text-xs">
                <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path d="M5 11l5-5 5 5H5z" /></svg>
                +0.0%
            </div>
        </div>
        <div class="mt-2 h-[60px]">
            <canvas id="sparkGrossProfit" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Net Profit Margin -->
    <div class="bg-white border shadow rounded-xl p-4 flex flex-col justify-between w-full">
        <div class="text-xs font-medium text-gray-500 uppercase">{{ __('cards.net_margin') }}</div>
        <div class="flex items-end justify-between mt-2">
            <div id="cardNetProfitMargin" class="text-3xl font-bold">0.00%</div>
            <div id="changeNetProfitMargin" class="flex items-center text-xs">
                <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path d="M5 11l5-5 5 5H5z" /></svg>
                +0.0%
            </div>
        </div>
        <div class="mt-2 h-[60px]">
            <canvas id="sparkNetProfitMargin" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Expenses -->
    <div class="bg-white border shadow rounded-xl p-4 flex flex-col justify-between w-full">
        <div class="text-xs font-medium text-gray-500 uppercase">{{ __('cards.expenses') }}</div>
        <div class="flex items-end justify-between mt-2">
            <div id="cardExpenses" class="text-3xl font-bold">$0.00</div>
            <div id="changeExpenses" class="flex items-center text-xs">
                <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor"><path d="M5 11l5-5 5 5H5z" /></svg>
                +0.0%
            </div>
        </div>
        <div class="mt-2 h-[60px]">
            <canvas id="sparkExpenses" class="w-full h-full"></canvas>
        </div>
    </div>

</div>
