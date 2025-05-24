<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('financial-report') }}</title>
    <style>
        body {
            font-family: Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div style="background: #2b5c8a; color: white; text-align: center; padding: 10px;">
        <table width="100%">
            <tr>
                <td style="text-align: center;">
                    <div style="font-size: 14px; font-weight: bold; padding-bottom: 15px;">{{ __('store.store_name') }}</div>
                    <div style="font-size: 14px; font-weight: bold;">{{ ucfirst($storeName) }}</div>
                </td>
                <td style="text-align: center;">
                    <div style="font-size: 14px; font-weight: bold; padding-bottom: 15px;">{{ __('filters.date_from') }}</div>
                    <div style="font-size: 14px; font-weight: bold;">{{ $dateFrom }}</div>
                </td>
                <td style="text-align: center;">
                    <div style="font-size: 14px; font-weight: bold; padding-bottom: 15px;">{{ __('filters.date_to') }}</div>
                    <div style="font-size: 14px; font-weight: bold;">{{ $dateTo }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin: 30px 0;">
        <table width="100%">
            <tr>
                <!-- Logo aligned left -->
                <td style="width: 33%; text-align: center;">
                    <img src="{{ $storeImage }}" style="height: 80px;" alt="Logo">
                </td>
                <!-- Store name centered -->
                <td style="width: 34%; text-align: center;">
                    <div style="font-size: 45px;">{{ ucfirst($storeName) }}</div>
                </td>
                <!-- Spacer column to balance layout -->
                <td style="width: 33%;"></td>
            </tr>
        </table>
    </div>

    <table width="100%" style="text-align: center; margin-bottom: 30px;">
        <tr>
            <td>
                <div style="color: #666; font-weight: 700;">{{ __('generic.total_income') }}</div>
                <div style="font-size: 20px; font-weight: 600;">
                    {{ number_format($summary['revenue']['value'], 2) }}
                </div>
            </td>
            <td>
                <div style="color: #666; font-weight: 700;">{{ __('generic.gross_profit') }}</div>
                <div style="font-size: 20px; font-weight: 600; color: {{ $summary['gross_profit']['value'] >= 0 ? 'green' : 'red' }};">
                    {{ number_format($summary['gross_profit']['value'], 2) }}</div>
            </td>
            <td>
                <div style="color: #666; font-weight: 700;">{{ __('generic.net_margin') }}</div>
                <div style="font-size: 20px; font-weight: 600; color: {{ $summary['gross_profit']['value'] >= 0 ? 'green' : 'red' }};">
                    {{ $summary['net_margin']['value'] }}%
                </div>
            </td>
            <td>
                <div style="color: #666; font-weight: 700;">{{ __('generic.expenses') }}</div>
                <div style="font-size: 20px; font-weight: 600; color: {{ $summary['gross_profit']['value'] >= 0 ? 'green' : 'red' }};">
                    {{ number_format($summary['expenses']['value'], 2) }}</div>
            </td>
        </tr>
    </table>

    <table width="100%" style="border-collapse: collapse; margin-top: 30px;">
        <thead>
            <tr style="border-bottom: 1px solid #000;">
                <th style="padding: 8px; text-align: center; font-size: 14px; font-weight: bold;">{{ __('generic.date') }}</th>
                <th style="padding: 8px; text-align: center; font-size: 14px; font-weight: bold;">{{ __('generic.amount') }}</th>
                <th style="padding: 8px; text-align: center; font-size: 14px; font-weight: bold;">{{ __('generic.transaction_type') }}</th>
                <th style="padding: 8px; text-align: center; font-size: 14px; font-weight: bold;">{{ __('generic.transaction_subtype') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $txn)
                <tr>
                    <td style="padding: 6px; text-align: center; font-size: 14px;">
                        {{ \Carbon\Carbon::parse($txn->date)->format('M j, Y') }}
                    </td>
                    <td
                        style="padding: 6px; text-align: center; font-size: 14px; color: {{ $txn->transactionType?->name === 'income' ? 'green' : 'red' }};">
                        {{ number_format($txn->amount, 2) }}
                    </td>
                    <td style="padding: 6px; text-align: center; font-size: 14px;">
                        {{ __(ucfirst($txn->transactionType?->name)) }}
                    </td>
                    <td style="padding: 6px; text-align: center; font-size: 14px;">
                        {{ $txn->subtype?->name ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding: 10px; text-align: center; color: #999; font-style: italic;">
                        {{ __('generic.no_transactions_available') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
