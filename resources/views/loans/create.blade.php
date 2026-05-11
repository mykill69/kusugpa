<!-- resources/views/loans/create.blade.php -->
@extends('layouts.main')

@section('title', 'New Loan Application')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('loans.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">New Loan Application</h1>
        </div>

        <form action="{{ route('loans.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Planter</label>
                    <select name="planter_code" id="planter_select" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" onchange="updatePlanterName()">
                        <option value="">Select Planter</option>
                        @foreach($planters as $p)
                            <option value="{{ $p->planter_code }}" data-name="{{ $p->planter_name }}">{{ $p->planter_name }} ({{ $p->planter_code }})</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="planter_name" id="planter_name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loan Type</label>
                    <select name="loan_type_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                        <option value="">Select Type</option>
                        @foreach($loanTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->default_interest_rate }}%)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Principal Amount (₱)</label>
                    <input type="number" name="principal_amount" step="0.01" min="{{ $settings['min_amount'] }}" max="{{ $settings['max_amount'] }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Interest Rate (%)</label>
                    <input type="number" name="interest_rate" step="0.01" value="{{ $settings['default_interest'] }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term (Months)</label>
                    <input type="number" name="term_months" min="1" max="{{ $settings['max_term'] }}" value="12" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Crop Year</label>
                    <select name="crop_year" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                        @foreach($cropYears as $cy)
                            <option value="{{ $cy }}">{{ $cy }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                <textarea name="purpose" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm"></textarea>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('loans.index') }}" class="px-6 py-2.5 border border-gray-200 rounded-xl text-sm">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700">
                    <i class="fas fa-save mr-1"></i> Submit Application
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updatePlanterName() {
        const select = document.getElementById('planter_select');
        const selected = select.options[select.selectedIndex];
        document.getElementById('planter_name').value = selected.getAttribute('data-name') || '';
    }
</script>
@endsection