<!-- resources/views/reports/consolidated.blade.php -->
@extends('layouts.main')

@section('title', 'Consolidated Report')

@section('content')
    <div x-data="consolidatedData()" class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Consolidated Report</h1>
                    <p class="text-primary-100 text-sm mt-1">Summary of consolidated upload data</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center gap-2">
                    <a href="{{ route('consolidated-report.export') }}"
                        class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-white/30 transition">
                        <i class="fas fa-file-pdf mr-2"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Totals Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500">Total Records</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($totals['total_records']) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500">TA Amount</p>
                <p class="text-xl font-bold text-green-600">₱{{ number_format($totals['total_ta_amount'], 0) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500">EMI Amount</p>
                <p class="text-xl font-bold text-blue-600">₱{{ number_format($totals['total_emi_amount'], 0) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500">Fuel Amount</p>
                <p class="text-xl font-bold text-amber-600">₱{{ number_format($totals['total_fuel'], 0) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-xs text-gray-500">Total Summary</p>
                <p class="text-xl font-bold text-primary-600">₱{{ number_format($totals['total_summary'] ?? 0, 0) }}</p>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" x-model="search" @input="applyFilters()"
                        placeholder="Search planter name or code..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Per Page</label>
                    <select x-model="perPage" @change="applyFilters()"
                        class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <button @click="clearFilters()" class="text-gray-500 hover:text-gray-700 text-sm py-2">
                    <i class="fas fa-times mr-1"></i> Clear
                </button>
                <span class="text-xs text-gray-400 ml-auto"
                    x-text="'Showing ' + paginatedData.length + ' of ' + filteredData.length + ' records'"></span>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('planter_code')">
                                Code <i class="fas fa-sort text-gray-300 ml-1"></i>
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('planter_name')">
                                Planter Name <i class="fas fa-sort text-gray-300 ml-1"></i>
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Assn</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('ta_wt')">
                                TA Wt <i class="fas fa-sort text-gray-300 ml-1"></i>
                            </th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('ta_amount')">
                                TA Amt <i class="fas fa-sort text-gray-300 ml-1"></i>
                            </th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">EMI Wt</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">EMI Amt</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">PAT Wt</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">PAT Amt</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">CCI FA</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">CCI FB</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">CCI FC</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Fuel</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Rental</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Underload</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Mudpress</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Adj</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('total_summary')">
                                Total <i class="fas fa-sort text-gray-300 ml-1"></i>
                            </th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="row in paginatedData" :key="row.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-2 text-xs font-mono text-gray-900" x-text="row.planter_code"></td>
                                <td class="px-3 py-2 text-sm font-medium text-gray-900" x-text="row.planter_name"></td>
                                <td class="px-3 py-2 text-xs text-gray-600" x-text="row.assn_name || '—'"></td>
                                <td class="px-3 py-2 text-xs text-right text-gray-700" x-text="formatNum(row.ta_wt, 3)">
                                </td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.ta_amount)"></td>
                                <td class="px-3 py-2 text-xs text-right text-gray-700" x-text="formatNum(row.emi_wt, 3)">
                                </td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.emi_amount)"></td>
                                <td class="px-3 py-2 text-xs text-right text-gray-700" x-text="formatNum(row.pat_wt, 3)">
                                </td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.pat_amount)"></td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.cci_fa_amt)"></td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.cci_fb_amt)"></td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.cci_fc_amt)"></td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.fuel_issuance_amt)"></td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.rental_amt)"></td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.underload_amt)"></td>
                                <td class="px-3 py-2 text-xs text-right font-semibold text-gray-900"
                                    x-text="'₱' + formatNum(row.mudpress_amt)"></td>
                                <td class="px-3 py-2 text-xs text-right font-semibold"
                                    :class="parseFloat(row.adj_amt) < 0 ? 'text-red-600' : 'text-gray-900'"
                                    x-text="'₱' + formatNum(row.adj_amt)"></td>
                                <td class="px-3 py-2 text-xs text-right font-bold"
                                    :class="parseFloat(row.total_summary) < 0 ? 'text-red-600' : 'text-primary-700'"
                                    x-text="'₱' + formatNum(row.total_summary)"></td>
                            </tr>
                        </template>
                        <tr x-show="filteredData.length === 0">
                            <td colspan="18" class="px-4 py-12 text-center text-gray-500">No records found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between" x-show="totalPages > 1">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">Per page:</span>
                    <select x-model="perPage" @change="applyFilters()"
                        class="border border-gray-200 rounded-lg px-2 py-1 text-xs">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="flex items-center gap-1">
                    <button @click="currentPage = 1" :disabled="currentPage === 1"
                        class="px-2 py-1 text-xs border rounded disabled:opacity-50">««</button>
                    <button @click="currentPage--" :disabled="currentPage === 1"
                        class="px-3 py-1.5 text-sm border rounded disabled:opacity-50">«</button>

                    <template x-for="page in visiblePages" :key="page">
                        <button @click="currentPage = page"
                            :class="page === currentPage ? 'bg-primary-600 text-white' : ''"
                            class="px-3 py-1.5 text-sm border rounded" x-text="page"></button>
                    </template>

                    <button @click="currentPage++" :disabled="currentPage >= totalPages"
                        class="px-3 py-1.5 text-sm border rounded disabled:opacity-50">»</button>
                    <button @click="currentPage = totalPages" :disabled="currentPage >= totalPages"
                        class="px-2 py-1 text-xs border rounded disabled:opacity-50">»»</button>
                </div>
                <span class="text-xs text-gray-400" x-text="'Page ' + currentPage + ' of ' + totalPages"></span>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function consolidatedData() {
                return {
                    allData: @json($uploads instanceof \Illuminate\Pagination\LengthAwarePaginator ? $uploads->items() : $uploads),
                    search: '',
                    sortField: 'planter_name',
                    sortDirection: 'asc',
                    currentPage: 1,
                    perPage: 50,

                    get filteredData() {
                        let data = [...this.allData];

                        if (this.search) {
                            const s = this.search.toLowerCase();
                            data = data.filter(d =>
                                (d.planter_name || '').toLowerCase().includes(s) ||
                                (d.planter_code || '').toLowerCase().includes(s) ||
                                (d.assn_name || '').toLowerCase().includes(s)
                            );
                        }

                        data.sort((a, b) => {
                            let valA = a[this.sortField] ?? '';
                            let valB = b[this.sortField] ?? '';
                            if (typeof valA === 'string') valA = valA.toLowerCase();
                            if (typeof valB === 'string') valB = valB.toLowerCase();
                            if (!isNaN(valA) && !isNaN(valB)) {
                                valA = parseFloat(valA);
                                valB = parseFloat(valB);
                            }
                            if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                            if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                            return 0;
                        });

                        return data;
                    },

                    get paginatedData() {
                        const start = (this.currentPage - 1) * this.perPage;
                        return this.filteredData.slice(start, start + this.perPage);
                    },

                    get totalPages() {
                        return Math.max(1, Math.ceil(this.filteredData.length / this.perPage));
                    },

                    get visiblePages() {
                        const pages = [];
                        const maxVisible = 5;
                        let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
                        let end = Math.min(this.totalPages, start + maxVisible - 1);
                        if (end - start + 1 < maxVisible) {
                            start = Math.max(1, end - maxVisible + 1);
                        }
                        for (let i = start; i <= end; i++) {
                            pages.push(i);
                        }
                        return pages;
                    },

                    applyFilters() {
                        this.currentPage = 1;
                    },

                    sortBy(field) {
                        if (this.sortField === field) {
                            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.sortField = field;
                            this.sortDirection = 'asc';
                        }
                        this.currentPage = 1;
                    },

                    clearFilters() {
                        this.search = '';
                        this.currentPage = 1;
                    },

                    formatNum(num, decimals = 2) {
                        return parseFloat(num || 0).toLocaleString('en-US', {
                            minimumFractionDigits: decimals,
                            maximumFractionDigits: decimals
                        });
                    }
                }
            }
        </script>
    @endpush
@endsection
