@extends('layouts.master')

@section('title', __('convert.title'))

@section('content')

    <div class="max-w-2xl mx-auto p-6 bg-white shadow rounded-xl">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">{{ __('convert.title') }}</h1>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded">
                <ul class="text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('convert.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('convert.file_label') }}
                </label>
                <input type="file" name="file" id="file" required
                    class="w-full border-gray-300 rounded shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('convert.date_label') }}
                </label>
                <input type="month" name="date" id="date" required
                    class="border-gray-300 rounded shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="pt-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium rounded">
                    {{ __('convert.submit') }}
                </button>
            </div>
        </form>
    </div>


@endsection
