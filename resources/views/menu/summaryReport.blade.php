@extends('layouts.main')

@section('title', 'Summary Report')

@section('content')
    <div x-data="summaryReportData()" class="space-y-6" x-init="init()">
        <!-- Page Header -->
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Summary Report</h1>
                    <p class="mt-2 text-primary-100 text-sm sm:text-base">View and filter crop production summaries</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    <span class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 text-sm">
                        Total Records: <span class="font-bold" x-text="totalRecords"></span>
                    </span>
                    {{-- <span class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 text-sm">
                    Showing: <span class="font-bold" x-text="displayedRecords"></span>
                </span> --}}
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="bg-primary-100 rounded-lg p-2">
                    <i class="fas fa-filter text-primary-600 text-sm"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Filters</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Crop Year -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Crop
                        Year</label>
                    <select x-model="filters.cropYear" @change="onCropYearChange()"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                        <option value="">All Years</option>
                        @foreach ($cropYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Week From -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Week
                        From</label>
                    <select x-model="filters.weekFrom" @change="loadData(1)"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                        <option value="">All Weeks</option>
                        <template x-for="week in availableWeeks" :key="'from_' + week">
                            <option :value="week" x-text="'Week ' + week"></option>
                        </template>
                    </select>
                    <p x-show="availableWeeks.length === 0 && filters.cropYear" class="text-xs text-amber-600 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> No weeks found for this crop year
                    </p>
                </div>

                <!-- Week To -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Week To</label>
                    <select x-model="filters.weekTo" @change="loadData(1)"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                        <option value="">All Weeks</option>
                        <template x-for="week in availableWeeks" :key="'to_' + week">
                            <option :value="week" x-text="'Week ' + week"></option>
                        </template>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Show
                        Entries</label>
                    <select x-model="filters.perPage" @change="loadData(1)"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end gap-2">
                    <button @click="previewPDF()"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-primary-600 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button @click="downloadPDF()"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-red-500 text-white rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-red-600 transition-colors shadow-sm">
                        <i class="fas fa-download"></i> Download
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Table Header -->
            <div class="p-4 sm:p-6 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <div class="bg-blue-100 rounded-lg p-2">
                            <i class="fas fa-table text-blue-600 text-sm"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Records</h2>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" x-model="filters.search" @input.debounce.500ms="loadData(1)"
                            class="w-full sm:w-72 pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50"
                            placeholder="Search records...">
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div x-show="loading" class="flex items-center justify-center py-12">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                    <span class="text-gray-500">Loading records...</span>
                </div>
            </div>

            <!-- Table Body -->
            <div x-show="!loading" class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-100" id="summaryTable">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="sorting px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors"
                                @click="sortBy('id')">
                                # <i :class="sortField === 'id' ? (sortDirection === 'asc' ?
                                        'fas fa-sort-up text-primary-500' : 'fas fa-sort-down text-primary-500') :
                                    'fas fa-sort text-gray-300'"
                                    class="ml-1"></i>
                            </th>
                            <th class="sorting px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors"
                                @click="sortBy('crop_year')">
                                Crop Year <i
                                    :class="sortField === 'crop_year' ? (sortDirection === 'asc' ?
                                            'fas fa-sort-up text-primary-500' : 'fas fa-sort-down text-primary-500') :
                                        'fas fa-sort text-gray-300'"
                                    class="ml-1"></i>
                            </th>
                            <th class="sorting px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors"
                                @click="sortBy('week_no')">
                                Week No <i
                                    :class="sortField === 'week_no' ? (sortDirection === 'asc' ?
                                            'fas fa-sort-up text-primary-500' : 'fas fa-sort-down text-primary-500') :
                                        'fas fa-sort text-gray-300'"
                                    class="ml-1"></i>
                            </th>
                            <th class="sorting px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors"
                                @click="sortBy('planter_code')">
                                Planter Code <i
                                    :class="sortField === 'planter_code' ? (sortDirection === 'asc' ?
                                            'fas fa-sort-up text-primary-500' : 'fas fa-sort-down text-primary-500') :
                                        'fas fa-sort text-gray-300'"
                                    class="ml-1"></i>
                            </th>
                            <th class="sorting px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors"
                                @click="sortBy('planter_name')">
                                Planter Name <i
                                    :class="sortField === 'planter_name' ? (sortDirection === 'asc' ?
                                            'fas fa-sort-up text-primary-500' : 'fas fa-sort-down text-primary-500') :
                                        'fas fa-sort text-gray-300'"
                                    class="ml-1"></i>
                            </th>
                            <th class="sorting px-4 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors"
                                @click="sortBy('net_cane')">
                                Net Cane <i
                                    :class="sortField === 'net_cane' ? (sortDirection === 'asc' ?
                                            'fas fa-sort-up text-primary-500' : 'fas fa-sort-down text-primary-500') :
                                        'fas fa-sort text-gray-300'"
                                    class="ml-1"></i>
                            </th>
                            <th class="sorting px-4 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors"
                                @click="sortBy('net_amount')">
                                Net Amount <i
                                    :class="sortField === 'net_amount' ? (sortDirection === 'asc' ?
                                            'fas fa-sort-up text-primary-500' : 'fas fa-sort-down text-primary-500') :
                                        'fas fa-sort text-gray-300'"
                                    class="ml-1"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(summary, index) in summaries" :key="summary.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500"
                                    x-text="(currentPage - 1) * filters.perPage + index + 1"></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-medium"
                                        x-text="summary.crop_year"></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700" x-text="'Week ' + summary.week_no"></td>
                                <td class="px-4 py-3 text-sm text-gray-600 font-mono" x-text="summary.planter_code"></td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="summary.planter_name">
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right font-semibold"
                                    x-text="formatNumber(summary.net_cane, 3)"></td>
                                <td class="px-4 py-3 text-sm text-right">
                                    <span class="text-green-600 font-semibold"
                                        x-text="'₱' + formatNumber(summary.net_amount, 2)"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="summaries.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-200 mb-3"></i>
                                    <p class="text-gray-500">No summary data found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && totalPages > 1"
                class="px-4 sm:px-6 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button @click="loadData(1)" :disabled="currentPage === 1"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-angle-double-left"></i>
                    </button>
                    <button @click="loadData(currentPage - 1)" :disabled="currentPage === 1"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-angle-left"></i>
                    </button>

                    <template x-for="page in visiblePages" :key="page">
                        <button @click="loadData(page)" x-text="page"
                            :class="page === currentPage ? 'bg-primary-600 text-white' :
                                'border border-gray-200 hover:bg-gray-100'"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"></button>
                    </template>

                    <button @click="loadData(currentPage + 1)" :disabled="currentPage === totalPages"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-angle-right"></i>
                    </button>
                    <button @click="loadData(totalPages)" :disabled="currentPage === totalPages"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-angle-double-right"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-500">
                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                    (Total: <span x-text="totalRecords"></span> records)
                </p>
            </div>
        </div>

        <!-- PDF Modal -->
        <div x-show="showPdfModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showPdfModal = false">
                </div>
                <div
                    class="relative inline-block w-full max-w-6xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Summary Report PDF Preview</h3>
                        <button @click="showPdfModal = false"
                            class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <div style="height: 85vh;">
                        <iframe id="pdfFrame" src="" frameborder="0" style="width:100%; height:100%;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function summaryReportData() {
                return {
                    summaries: [],
                    totalRecords: 0,
                    currentPage: 1,
                    totalPages: 1,
                    loading: false,
                    showPdfModal: false,
                    sortField: 'id',
                    sortDirection: 'desc',
                    availableWeeks: [], // Dynamic weeks based on selected crop year
                    filters: {
                        cropYear: '',
                        weekFrom: '',
                        weekTo: '',
                        search: '',
                        perPage: 50,
                    },

                    init() {
                        this.loadData(1);
                        // Load initial weeks if a crop year is pre-selected
                        if (this.filters.cropYear) {
                            this.loadWeeks();
                        }
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

                    // Called when crop year changes
                    async onCropYearChange() {
                        // Reset week filters when crop year changes
                        this.filters.weekFrom = '';
                        this.filters.weekTo = '';

                        // Load weeks for the selected crop year
                        await this.loadWeeks();

                        // Reload data with new crop year filter
                        this.loadData(1);
                    },

                    // Load weeks based on selected crop year
                    async loadWeeks() {
                        if (!this.filters.cropYear) {
                            this.availableWeeks = [];
                            return;
                        }

                        try {
                            const response = await fetch(
                                `{{ route('summaryReport.weeks') }}?crop_year=${this.filters.cropYear}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                            if (!response.ok) throw new Error('Failed to load weeks');

                            const data = await response.json();
                            this.availableWeeks = data.weeks;
                        } catch (error) {
                            console.error('Error loading weeks:', error);
                            this.availableWeeks = [];
                        }
                    },

                    async loadData(page) {
                        this.loading = true;
                        this.currentPage = page;

                        try {
                            const params = new URLSearchParams({
                                page: page,
                                per_page: this.filters.perPage,
                                crop_year: this.filters.cropYear,
                                week_from: this.filters.weekFrom,
                                week_to: this.filters.weekTo,
                                search: this.filters.search,
                                sort_field: this.sortField,
                                sort_direction: this.sortDirection,
                            });

                            const response = await fetch(`{{ route('summaryReport.data') }}?${params.toString()}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (!response.ok) throw new Error('Failed to load');

                            const data = await response.json();
                            this.summaries = data.data;
                            this.totalRecords = data.total;
                            this.totalPages = data.last_page;
                        } catch (error) {
                            console.error('Error:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    sortBy(field) {
                        if (this.sortField === field) {
                            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.sortField = field;
                            this.sortDirection = 'asc';
                        }
                        this.loadData(1);
                    },

                    formatNumber(number, decimals = 2) {
                        if (number === null || number === undefined) return '0';
                        return parseFloat(number).toLocaleString('en-US', {
                            minimumFractionDigits: decimals,
                            maximumFractionDigits: decimals
                        });
                    },

                    previewPDF() {
                        if (!this.filters.cropYear) {
                            alert("Please select a Crop Year.");
                            return;
                        }
                        const url =
                            `{{ url('/summary/pdf-preview') }}?crop_year=${this.filters.cropYear}&week_from=${this.filters.weekFrom}&week_to=${this.filters.weekTo}`;
                        document.getElementById('pdfFrame').src = url;
                        this.showPdfModal = true;
                    },

                    downloadPDF() {
                        if (!this.filters.cropYear) {
                            alert("Please select a Crop Year.");
                            return;
                        }
                        const downloadUrl =
                            `{{ url('/summary/download-pdf') }}?crop_year=${this.filters.cropYear}&week_from=${this.filters.weekFrom}&week_to=${this.filters.weekTo}`;
                        window.open(downloadUrl, '_blank');
                    }
                }
            }
        </script>
    @endpush
@endsection
