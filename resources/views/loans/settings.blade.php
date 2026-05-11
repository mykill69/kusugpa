<!-- resources/views/loans/settings.blade.php -->
@extends('layouts.main')

@section('title', 'Loan Settings')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
        <h1 class="text-2xl font-bold">Loan Settings</h1>
        <p class="text-primary-100 text-sm mt-1">Configure loan parameters and interest rates</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Default Settings</h3>
            <form action="{{ route('loans.settings.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Interest Rate (%)</label>
                    <input type="number" name="default_interest_rate" step="0.01" value="{{ $settings['default_interest_rate'] }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Loan Term (Months)</label>
                    <input type="number" name="max_loan_term_months" value="{{ $settings['max_loan_term_months'] }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Amount (₱)</label>
                        <input type="number" name="min_loan_amount" step="0.01" value="{{ $settings['min_loan_amount'] }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Amount (₱)</label>
                        <input type="number" name="max_loan_amount" step="0.01" value="{{ $settings['max_loan_amount'] }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-sm">Auto-deduct from weekly vouchers</span>
                    <input type="hidden" name="auto_deduct" value="0">
                    <input type="checkbox" name="auto_deduct" value="1" {{ $settings['auto_deduct'] ? 'checked' : '' }} class="w-5 h-5 text-primary-600 rounded">
                </div>
                <button type="submit" class="w-full bg-primary-600 text-white rounded-xl px-6 py-3 text-sm font-semibold hover:bg-primary-700">Save Settings</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Loan Types</h3>
            <div class="space-y-3 mb-4">
                @foreach($loanTypes as $type)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div>
                        <p class="text-sm font-semibold">{{ $type->name }}</p>
                        <p class="text-xs text-gray-500">{{ $type->default_interest_rate }}% | {{ $type->default_term_months }} months</p>
                    </div>
                    <form action="{{ route('loans.types.toggle', $type) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs {{ $type->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            <form action="{{ route('loans.types.store') }}" method="POST" class="space-y-3 border-t pt-4">
                @csrf
                <input type="text" name="name" placeholder="Type Name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <div class="grid grid-cols-3 gap-2">
                    <input type="number" name="default_interest_rate" step="0.01" placeholder="Rate %" required class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    <input type="number" name="default_term_months" placeholder="Term" required class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    <input type="number" name="max_amount" step="0.01" placeholder="Max ₱" required class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <textarea name="description" placeholder="Description" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm"></textarea>
                <button type="submit" class="w-full bg-primary-600 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-primary-700">Add Loan Type</button>
            </form>
        </div>
    </div>
</div>
@endsection