@extends('layouts.master')

@section('title', __('nav.dashboard'))

@section('content')
    <!-- === Filter Section === -->
    <x-dashboard.filters />

    <!-- === Summary Cards === -->
    <div class="mt-6">
        <x-dashboard.cards />
    </div>

    <!-- === Charts Section === -->
    <div class="mt-6">
        <x-dashboard.charts />
    </div>

    <!-- === Action Buttons Section === -->
    <div class="mt-6">
        <x-dashboard.action-buttons />
    </div>

    <!-- === Transactions Table Section === -->
    <div id="dashboard-table" class="mt-6">
        <x-dashboard.transactions-table />
    </div>

    <!-- === Modals (conditionally rendered via JS) === -->
    <div id="dashboard-modals">
        <x-dashboard.modals />
    </div>
@endsection

@push('scripts')
    <script>
        window.translations = {
            income_label: @json(__('charts.income_label')),
            expense_label: @json(__('charts.expense_label')),
            select_file_error: @json(__('upload.select_file_error')),
            server_error: @json(__('upload.server_error')),
            add_store: @json(__('store.add_store')),
            add_subtype: @json(__('subtype.add_subtype')),
            edit: @json(__('transaction.edit')),
            delete: @json(__('transaction.delete')),
            select_all: @json(__('filters.select_all')),
            select_value: @json(__('transaction.select_value')),
            confirm_title: @json(__('generic.confirm_title')),
            confirm_body: @json(__('generic.confirm_body')),
        };
    </script>
    @vite('resources/js/dashboard/dashboard.js')
    @vite('resources/js/stores.js')
    @vite('resources/js/subtypes.js')
@endpush
