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
                    <button @click="showDeleteModal = true"
                        class="bg-red-500/80 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-red-600/80 transition">
                        <i class="fas fa-trash mr-2"></i> Delete Records
                    </button>
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
                <p class="text-xl font-bold text-gray-900" x-text="formatNum(totalRecords, 0)">
                    {{ number_format($totals['total_records']) }}</p>
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

        <!-- Action Bar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-3 items-center">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <input type="text" x-model="search" @input="applyFilters()"
                            placeholder="Search planter name or code..."
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Per Page</label>
                        <select x-model="perPage" @change="loadPage(1)"
                            class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <button @click="clearFilters()" class="text-gray-500 hover:text-gray-700 text-sm py-2">
                        <i class="fas fa-times mr-1"></i> Clear
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400"
                        x-text="'Showing ' + allData.length + ' of ' + totalRecords + ' records'"></span>
                    <button x-show="selectedIds.length > 0" @click="deleteSelected()"
                        class="bg-red-500 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-red-600 transition">
                        <i class="fas fa-trash mr-2"></i> Delete Selected (<span x-text="selectedIds.length"></span>)
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">
                                <input type="checkbox" @click="toggleSelectAll" :checked="isAllSelected"
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            </th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('crop_year')">CRP YR</th>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('week_no')">WK No</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('planter_code')">Code</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('planter_name')">Planter Name</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Assn</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('ta_wt')">TA Wt</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('ta_amount')">TA Amt</th>
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
                                @click="sortBy('total_summary')">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="row in allData" :key="row.id">
                            <tr class="hover:bg-gray-50/50 transition-colors"
                                :class="{ 'bg-primary-50': selectedIds.includes(row.id) }">
                                <td class="px-3 py-2 text-center">
                                    <input type="checkbox" :value="row.id" x-model="selectedIds"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </td>
                                <td class="px-3 py-2 text-xs text-center font-semibold" x-text="row.crop_year || '—'">
                                </td>
                                <td class="px-3 py-2 text-xs text-center font-semibold" x-text="row.week_no || '—'"></td>
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
                        <tr x-show="loading">
                            <td colspan="22" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                                <p>Loading data...</p>
                            </td>
                        </tr>
                        <tr x-show="!loading && allData.length === 0">
                            <td colspan="22" class="px-4 py-12 text-center text-gray-500">No records found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400"
                        x-text="'Page ' + currentPage + ' of ' + totalPages + ' (' + totalRecords + ' records)'"></span>
                </div>
                <div class="flex items-center gap-1" x-show="totalPages > 1">
                    <button @click="loadPage(1)" :disabled="currentPage === 1"
                        class="px-2.5 py-1.5 text-xs border rounded-lg disabled:opacity-50 hover:bg-gray-50 transition">First</button>
                    <button @click="loadPage(currentPage - 1)" :disabled="currentPage === 1"
                        class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50 hover:bg-gray-50 transition">Previous</button>

                    <template x-for="page in visiblePages" :key="page">
                        <button @click="loadPage(page)"
                            :class="page === currentPage ? 'bg-primary-600 text-white shadow-sm' : 'hover:bg-gray-50'"
                            class="px-3 py-1.5 text-sm border rounded-lg transition" x-text="page"></button>
                    </template>

                    <button @click="loadPage(currentPage + 1)" :disabled="currentPage >= totalPages"
                        class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50 hover:bg-gray-50 transition">Next</button>
                    <button @click="loadPage(totalPages)" :disabled="currentPage >= totalPages"
                        class="px-2.5 py-1.5 text-xs border rounded-lg disabled:opacity-50 hover:bg-gray-50 transition">Last</button>
                </div>
                <div></div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-trash text-red-500 mr-2"></i> Delete Consolidated Records
                </h3>

                <!-- Delete Options -->
                <div class="space-y-4">
                    <!-- Delete All -->
                    <div class="border border-red-200 rounded-xl p-4 bg-red-50">
                        <h4 class="font-semibold text-red-700 mb-2">Delete ALL Records</h4>
                        <p class="text-xs text-red-600 mb-3">This will permanently delete all consolidated upload records.
                        </p>
                        <button @click="confirmDeleteAll()"
                            class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 transition">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Delete All Records
                        </button>
                    </div>

                    <!-- Delete by Crop Year & Week -->
                    <div class="border border-amber-200 rounded-xl p-4 bg-amber-50">
                        <h4 class="font-semibold text-amber-700 mb-2">Delete by Crop Year & Week</h4>
                        <p class="text-xs text-amber-600 mb-3">Delete records for a specific crop year and week number.</p>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Crop Year</label>
                                <select x-model="deleteForm.crop_year" @change="updateWeekOptions()"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                    <option value="">Select Crop Year</option>
                                    <template x-for="cy in availableCropYears" :key="cy">
                                        <option :value="cy" x-text="cy"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Week No</label>
                                <select x-model="deleteForm.week_no" :disabled="!deleteForm.crop_year"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm disabled:opacity-50">
                                    <option value="">Select Week</option>
                                    <template x-for="wk in availableWeeks" :key="wk">
                                        <option :value="wk" x-text="'Week ' + wk"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div x-show="deleteForm.crop_year && deleteForm.week_no" class="mb-3">
                            <p class="text-xs text-amber-600">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span
                                    x-text="'Will delete ' + getWeekRecordCount() + ' record(s) for ' + deleteForm.crop_year + ' Week ' + deleteForm.week_no"></span>
                            </p>
                        </div>
                        <button @click="confirmDeleteByWeek()" :disabled="!deleteForm.crop_year || !deleteForm.week_no"
                            class="bg-amber-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-amber-700 transition disabled:opacity-50">
                            <i class="fas fa-trash mr-2"></i> Delete by Week
                        </button>
                    </div>

                    <!-- Delete Selected -->
                    <div class="border border-blue-200 rounded-xl p-4 bg-blue-50">
                        <h4 class="font-semibold text-blue-700 mb-2">Delete Selected Records</h4>
                        <p class="text-xs text-blue-600 mb-3">Select records using checkboxes in the table, then delete
                            them.</p>
                        <button @click="confirmDeleteSelected()" :disabled="selectedIds.length === 0"
                            class="bg-blue-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-blue-700 transition disabled:opacity-50">
                            <i class="fas fa-trash mr-2"></i> Delete Selected (<span x-text="selectedIds.length"></span>)
                        </button>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <button @click="showDeleteModal = false"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function consolidatedData() {
                return {
                    allData: @json($uploads->items()),
                    totalRecords: {{ $uploads->total() }},
                    currentPage: {{ $uploads->currentPage() }},
                    totalPages: {{ $uploads->lastPage() }},
                    search: '',
                    sortField: 'planter_name',
                    sortDirection: 'asc',
                    perPage: {{ $uploads->perPage() }},
                    loading: false,
                    selectedIds: [],
                    showDeleteModal: false,
                    deleteForm: {
                        crop_year: '',
                        week_no: ''
                    },
                    // Get unique crop years from all data (not just current page)
                    availableCropYears: [],
                    availableWeeks: [],
                    allWeeksData: [], // Store all weeks data for record count

                    init() {
                        this.fetchAvailableFilters();
                    },

                    async fetchAvailableFilters() {
                        try {
                            // Fetch all distinct crop years and weeks
                            const response = await fetch('{{ route('consolidated-report.filters') }}', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const data = await response.json();
                            this.availableCropYears = data.crop_years || [];
                            this.allWeeksData = data.weeks_data || [];
                        } catch (error) {
                            console.error('Error fetching filters:', error);
                            // Fallback: use data from current page
                            this.availableCropYears = [...new Set(this.allData.map(row => row.crop_year))].filter(Boolean)
                                .sort();
                        }
                    },

                    updateWeekOptions() {
                        this.deleteForm.week_no = '';
                        if (this.deleteForm.crop_year && this.allWeeksData.length > 0) {
                            // Filter weeks for selected crop year
                            this.availableWeeks = this.allWeeksData
                                .filter(w => w.crop_year === this.deleteForm.crop_year)
                                .map(w => w.week_no)
                                .sort((a, b) => parseInt(a) - parseInt(b));
                        } else {
                            this.availableWeeks = [];
                        }
                    },

                    getWeekRecordCount() {
                        if (!this.deleteForm.crop_year || !this.deleteForm.week_no) return 0;
                        // Count from allWeeksData
                        const weekData = this.allWeeksData.find(
                            w => w.crop_year === this.deleteForm.crop_year && w.week_no == this.deleteForm.week_no
                        );
                        return weekData ? weekData.count : 0;
                    },

                    get isAllSelected() {
                        return this.allData.length > 0 && this.selectedIds.length === this.allData.length;
                    },

                    toggleSelectAll() {
                        if (this.isAllSelected) {
                            this.selectedIds = [];
                        } else {
                            this.selectedIds = this.allData.map(row => row.id);
                        }
                    },

                    get visiblePages() {
                        const pages = [];
                        const maxVisible = 5;
                        let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
                        let end = Math.min(this.totalPages, start + maxVisible - 1);
                        if (end - start + 1 < maxVisible) start = Math.max(1, end - maxVisible + 1);
                        for (let i = start; i <= end; i++) pages.push(i);
                        return pages;
                    },

                    async loadPage(page) {
                        this.loading = true;
                        this.selectedIds = [];
                        try {
                            const params = new URLSearchParams({
                                page: page,
                                per_page: this.perPage,
                                search: this.search,
                                sort_field: this.sortField,
                                sort_direction: this.sortDirection,
                            });
                            const response = await fetch(`{{ route('consolidated-report') }}?${params.toString()}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const data = await response.json();
                            this.allData = data.data;
                            this.currentPage = data.current_page;
                            this.totalPages = data.last_page;
                            this.totalRecords = data.total;
                        } catch (error) {
                            console.error('Error:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    applyFilters() {
                        this.loadPage(1);
                    },

                    sortBy(field) {
                        if (this.sortField === field) {
                            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.sortField = field;
                            this.sortDirection = 'asc';
                        }
                        this.loadPage(1);
                    },

                    clearFilters() {
                        this.search = '';
                        this.loadPage(1);
                    },

                    // Delete All
                    async confirmDeleteAll() {
                        const result = await Swal.fire({
                            title: 'Delete ALL Records?',
                            text: 'This will permanently delete ALL consolidated upload records. This cannot be undone!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete all',
                            confirmButtonColor: '#dc2626',
                            cancelButtonText: 'Cancel'
                        });

                        if (!result.isConfirmed) return;

                        try {
                            const response = await fetch('{{ route('consolidated-report.delete-all') }}', {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const data = await response.json();

                            if (!response.ok) throw new Error(data.message);

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false
                            });

                            this.showDeleteModal = false;
                            setTimeout(() => location.reload(), 1500);
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message
                            });
                        }
                    },

                    // Delete by Week
                    async confirmDeleteByWeek() {
                        if (!this.deleteForm.crop_year || !this.deleteForm.week_no) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Required',
                                text: 'Please select both crop year and week number.'
                            });
                            return;
                        }

                        const recordCount = this.getWeekRecordCount();
                        const result = await Swal.fire({
                            title: 'Delete Records?',
                            text: `Delete ${recordCount} record(s) for ${this.deleteForm.crop_year} Week ${this.deleteForm.week_no}?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete',
                            confirmButtonColor: '#dc2626',
                            cancelButtonText: 'Cancel'
                        });

                        if (!result.isConfirmed) return;

                        try {
                            const response = await fetch('{{ route('consolidated-report.delete-by-week') }}', {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(this.deleteForm)
                            });
                            const data = await response.json();

                            if (!response.ok) throw new Error(data.message);

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false
                            });

                            this.deleteForm = {
                                crop_year: '',
                                week_no: ''
                            };
                            this.availableWeeks = [];
                            this.showDeleteModal = false;
                            setTimeout(() => location.reload(), 1500);
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message
                            });
                        }
                    },

                    // Delete Selected
                    async confirmDeleteSelected() {
                        if (this.selectedIds.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Selection',
                                text: 'Please select records to delete using the checkboxes.'
                            });
                            return;
                        }

                        const result = await Swal.fire({
                            title: 'Delete Selected Records?',
                            text: `Delete ${this.selectedIds.length} selected record(s)? This cannot be undone!`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete',
                            confirmButtonColor: '#dc2626',
                            cancelButtonText: 'Cancel'
                        });

                        if (!result.isConfirmed) return;

                        try {
                            const response = await fetch('{{ route('consolidated-report.delete-selected') }}', {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    ids: this.selectedIds
                                })
                            });
                            const data = await response.json();

                            if (!response.ok) throw new Error(data.message);

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false
                            });

                            this.selectedIds = [];
                            this.showDeleteModal = false;
                            setTimeout(() => location.reload(), 1500);
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message
                            });
                        }
                    },

                    async deleteSelected() {
                        await this.confirmDeleteSelected();
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
