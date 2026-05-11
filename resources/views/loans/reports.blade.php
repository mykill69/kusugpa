<!-- resources/views/loans/reports.blade.php -->
@extends('layouts.main')

@section('title', 'Loan Reports')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
        <h1 class="text-2xl font-bold">Loan Reports</h1>
        <p class="text-primary-100 text-sm mt-1">Generate and download loan reports</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        
        <!-- Amortization Schedule -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" 
            x-data="searchableSelect({ loans: {{ json_encode(\App\Models\Loan::whereIn('status', ['approved', 'active'])->orderBy('created_at', 'desc')->get()->toArray()) }}, reportType: 'amortization' })">
            <div class="flex items-start gap-4 mb-4">
                <div class="bg-blue-100 rounded-xl p-3"><i class="fas fa-calendar-alt text-blue-600 text-xl"></i></div>
                <div>
                    <h3 class="font-bold text-gray-900">Amortization Schedule</h3>
                    <p class="text-xs text-gray-500 mt-1">Print payment schedule for a specific loan</p>
                </div>
            </div>
            <div class="relative" @click.away="open = false">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" x-model="search" @click="open = true" @input="open = true"
                        placeholder="Search planter name or loan number..."
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 mb-1">
                    <button x-show="search" @click="search = ''; selectedLoan = null; open = false" 
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div x-show="open" x-transition
                    class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto mt-1">
                    <template x-for="loan in filteredLoans" :key="loan.id">
                        <div @click="selectLoan(loan)" 
                            :class="{'bg-primary-50': selectedLoan?.id === loan.id}"
                            class="px-4 py-2.5 text-sm cursor-pointer hover:bg-gray-50 transition-colors flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900" x-text="loan.loan_number"></p>
                                <p class="text-xs text-gray-500" x-text="loan.planter_name + ' (' + loan.planter_code + ')'"></p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full" 
                                :class="loan.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'"
                                x-text="loan.status"></span>
                        </div>
                    </template>
                    <div x-show="filteredLoans.length === 0" class="px-4 py-3 text-sm text-gray-500 text-center">No loans found</div>
                </div>
            </div>
            <div x-show="selectedLoan" class="mt-2 p-3 bg-blue-50 rounded-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900" x-text="selectedLoan.loan_number"></p>
                        <p class="text-xs text-gray-600" x-text="selectedLoan.planter_name"></p>
                    </div>
                    <button @click="selectedLoan = null; search = ''" class="text-gray-400 hover:text-red-500"><i class="fas fa-times-circle"></i></button>
                </div>
            </div>
            <button @click="generateReport()" :disabled="!selectedLoan"
                class="w-full mt-3 bg-blue-600 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                <i class="fas fa-file-pdf mr-1"></i> Generate PDF
            </button>
        </div>

        <!-- Loan Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" 
            x-data="searchableSelect({ loans: {{ json_encode(\App\Models\Loan::orderBy('created_at', 'desc')->get()->toArray()) }}, reportType: 'details' })">
            <div class="flex items-start gap-4 mb-4">
                <div class="bg-green-100 rounded-xl p-3"><i class="fas fa-file-invoice text-green-600 text-xl"></i></div>
                <div>
                    <h3 class="font-bold text-gray-900">Loan Details</h3>
                    <p class="text-xs text-gray-500 mt-1">Print complete loan information</p>
                </div>
            </div>
            <div class="relative" @click.away="open = false">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" x-model="search" @click="open = true" @input="open = true"
                        placeholder="Search planter name or loan number..."
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 mb-1">
                    <button x-show="search" @click="search = ''; selectedLoan = null; open = false" 
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
                </div>
                <div x-show="open" x-transition
                    class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto mt-1">
                    <template x-for="loan in filteredLoans" :key="loan.id">
                        <div @click="selectLoan(loan)" :class="{'bg-primary-50': selectedLoan?.id === loan.id}"
                            class="px-4 py-2.5 text-sm cursor-pointer hover:bg-gray-50 transition-colors flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900" x-text="loan.loan_number"></p>
                                <p class="text-xs text-gray-500" x-text="loan.planter_name + ' (' + loan.planter_code + ')'"></p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full" :class="getStatusClass(loan.status)" x-text="loan.status"></span>
                        </div>
                    </template>
                    <div x-show="filteredLoans.length === 0" class="px-4 py-3 text-sm text-gray-500 text-center">No loans found</div>
                </div>
            </div>
            <div x-show="selectedLoan" class="mt-2 p-3 bg-green-50 rounded-xl">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm font-semibold text-gray-900" x-text="selectedLoan.loan_number"></p><p class="text-xs text-gray-600" x-text="selectedLoan.planter_name"></p></div>
                    <button @click="selectedLoan = null; search = ''" class="text-gray-400 hover:text-red-500"><i class="fas fa-times-circle"></i></button>
                </div>
            </div>
            <button @click="generateReport()" :disabled="!selectedLoan"
                class="w-full mt-3 bg-green-600 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                <i class="fas fa-file-pdf mr-1"></i> Generate PDF
            </button>
        </div>

        <!-- Statement of Account -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" 
            x-data="searchableSelect({ loans: {{ json_encode(\App\Models\Loan::whereIn('status', ['active', 'completed'])->orderBy('created_at', 'desc')->get()->toArray()) }}, reportType: 'soa' })">
            <div class="flex items-start gap-4 mb-4">
                <div class="bg-purple-100 rounded-xl p-3"><i class="fas fa-file-alt text-purple-600 text-xl"></i></div>
                <div>
                    <h3 class="font-bold text-gray-900">Statement of Account</h3>
                    <p class="text-xs text-gray-500 mt-1">Print SOA with payment history</p>
                </div>
            </div>
            <div class="relative" @click.away="open = false">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" x-model="search" @click="open = true" @input="open = true"
                        placeholder="Search planter name or loan number..."
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 mb-1">
                    <button x-show="search" @click="search = ''; selectedLoan = null; open = false" 
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
                </div>
                <div x-show="open" x-transition
                    class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto mt-1">
                    <template x-for="loan in filteredLoans" :key="loan.id">
                        <div @click="selectLoan(loan)" :class="{'bg-primary-50': selectedLoan?.id === loan.id}"
                            class="px-4 py-2.5 text-sm cursor-pointer hover:bg-gray-50 transition-colors flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900" x-text="loan.loan_number"></p>
                                <p class="text-xs text-gray-500" x-text="loan.planter_name + ' (' + loan.planter_code + ')'"></p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full" :class="getStatusClass(loan.status)" x-text="loan.status"></span>
                        </div>
                    </template>
                    <div x-show="filteredLoans.length === 0" class="px-4 py-3 text-sm text-gray-500 text-center">No loans found</div>
                </div>
            </div>
            <div x-show="selectedLoan" class="mt-2 p-3 bg-purple-50 rounded-xl">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm font-semibold text-gray-900" x-text="selectedLoan.loan_number"></p><p class="text-xs text-gray-600" x-text="selectedLoan.planter_name"></p></div>
                    <button @click="selectedLoan = null; search = ''" class="text-gray-400 hover:text-red-500"><i class="fas fa-times-circle"></i></button>
                </div>
            </div>
            <button @click="generateReport()" :disabled="!selectedLoan"
                class="w-full mt-3 bg-purple-600 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                <i class="fas fa-file-pdf mr-1"></i> Generate PDF
            </button>
        </div>

        <!-- Monthly Report -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start gap-4 mb-4">
                <div class="bg-orange-100 rounded-xl p-3"><i class="fas fa-chart-line text-orange-600 text-xl"></i></div>
                <div>
                    <h3 class="font-bold text-gray-900">Monthly Report</h3>
                    <p class="text-xs text-gray-500 mt-1">Monthly collections and new loans</p>
                </div>
            </div>
            <form action="{{ route('loans.monthly-report-pdf') }}" method="GET">
                <input type="month" name="month" value="{{ date('Y-m') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm mb-2">
                <select name="crop_year" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm mb-2">
                    <option value="">All Crop Years</option>
                    @foreach($cropYears as $cy)
                        <option value="{{ $cy }}">{{ $cy }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-orange-600 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-orange-700">
                    <i class="fas fa-file-pdf mr-1"></i> Generate PDF
                </button>
            </form>
        </div>

        <!-- Active Loans Report -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start gap-4 mb-4">
                <div class="bg-red-100 rounded-xl p-3"><i class="fas fa-list-ul text-red-600 text-xl"></i></div>
                <div>
                    <h3 class="font-bold text-gray-900">Active Loans Report</h3>
                    <p class="text-xs text-gray-500 mt-1">Summary of all active loans</p>
                </div>
            </div>
            <form action="{{ route('loans.active-loans-pdf') }}" method="GET">
                <select name="crop_year" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm mb-2">
                    <option value="">All Crop Years</option>
                    @foreach($cropYears as $cy)
                        <option value="{{ $cy }}">{{ $cy }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-red-600 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> Generate PDF
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function searchableSelect(config) {
        return {
            loans: config.loans || [],
            selectedLoan: config.selectedLoan || null,
            reportType: config.reportType || '',
            search: '',
            open: false,

            get filteredLoans() {
                if (!this.search) return this.loans;
                const s = this.search.toLowerCase();
                return this.loans.filter(l => 
                    (l.loan_number || '').toLowerCase().includes(s) ||
                    (l.planter_name || '').toLowerCase().includes(s) ||
                    (l.planter_code || '').toLowerCase().includes(s)
                );
            },

            selectLoan(loan) {
                this.selectedLoan = loan;
                this.search = '';
                this.open = false;
            },

            generateReport() {
                if (!this.selectedLoan) return;
                const id = this.selectedLoan.id;
                const urls = {
                    'amortization': '/loans/' + id + '/amortization-pdf',
                    'details': '/loans/' + id + '/details-pdf',
                    'soa': '/loans/' + id + '/soa-pdf',
                };
                window.open(urls[this.reportType] || '', '_blank');
            },

            getStatusClass(status) {
                const classes = {
                    active: 'bg-green-100 text-green-700',
                    pending: 'bg-yellow-100 text-yellow-700',
                    approved: 'bg-blue-100 text-blue-700',
                    completed: 'bg-gray-100 text-gray-700',
                    rejected: 'bg-red-100 text-red-700',
                };
                return classes[status] || 'bg-gray-100 text-gray-700';
            }
        };
    }
</script>
@endpush
@endsection