<!-- resources/views/cash-advances/settings.blade.php -->
@extends('layouts.main')

@section('title', 'Cash Advance Settings')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
        <h1 class="text-2xl font-bold">Cash Advance Settings</h1>
        <p class="text-primary-100 text-sm mt-1">Configure cash advance parameters and default rates</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Default Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-sliders-h text-primary-600 mr-2"></i>Default Settings
            </h3>
            <form action="{{ route('cash-advances.settings.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Interest Rate (%)</label>
                    <input type="number" name="ca_default_interest_rate" step="0.01" value="{{ $settings['ca_default_interest_rate'] }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    <p class="text-xs text-gray-400 mt-1">Standard interest rate for cash advances</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Term (Months)</label>
                    <input type="number" name="ca_max_term_months" value="{{ $settings['ca_max_term_months'] }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    <p class="text-xs text-gray-400 mt-1">Maximum repayment period in months</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Amount (₱)</label>
                        <input type="number" name="ca_min_amount" step="0.01" value="{{ $settings['ca_min_amount'] }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Amount (₱)</label>
                        <input type="number" name="ca_max_amount" step="0.01" value="{{ $settings['ca_max_amount'] }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div>
                        <span class="text-sm font-medium">Auto-deduct from weekly vouchers</span>
                        <p class="text-xs text-gray-400 mt-0.5">Automatically deduct amortizations during voucher generation</p>
                    </div>
                    <input type="hidden" name="ca_auto_deduct" value="0">
                    <input type="checkbox" name="ca_auto_deduct" value="1" {{ ($settings['ca_auto_deduct'] ?? false) ? 'checked' : '' }} class="w-5 h-5 text-primary-600 rounded focus:ring-primary-500">
                </div>
                <button type="submit" class="w-full bg-primary-600 text-white rounded-xl px-6 py-3 text-sm font-semibold hover:bg-primary-700 transition">
                    <i class="fas fa-save mr-2"></i> Save Settings
                </button>
            </form>
        </div>

        <!-- Quick Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Cash Advance Information
            </h3>
            
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-money-bill-wave text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">What are Cash Advances?</p>
                            <p class="text-xs text-gray-600 mt-1">Short-term financial assistance for planters with smaller amounts and shorter terms compared to regular loans.</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Current Configuration</p>
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <div>
                                    <p class="text-xs text-gray-500">Interest Rate</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $settings['ca_default_interest_rate'] }}%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Max Term</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $settings['ca_max_term_months'] }} months</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Min Amount</p>
                                    <p class="text-sm font-bold text-gray-900">₱{{ number_format($settings['ca_min_amount'], 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Max Amount</p>
                                    <p class="text-sm font-bold text-gray-900">₱{{ number_format($settings['ca_max_amount'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-lightbulb text-amber-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Tips</p>
                            <ul class="text-xs text-gray-600 mt-1 space-y-1">
                                <li>• Cash advances are ideal for emergency or short-term needs</li>
                                <li>• Keep interest rates competitive to encourage repayment</li>
                                <li>• Shorter terms help planters clear their obligations faster</li>
                                <li>• Changes apply to new applications only</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection