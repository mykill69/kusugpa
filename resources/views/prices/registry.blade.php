<!-- resources/views/prices/registry.blade.php -->
@extends('layouts.main')

@section('title', 'Quedan & Molasses Registry')

@section('content')
<div x-data="registryData()" class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
        <div class="flex items-center gap-3 mb-2">
            <i class="fas fa-list-ul text-2xl"></i>
            <h1 class="text-2xl sm:text-3xl font-bold">Quedan & Molasses Registry</h1>
        </div>
        <p class="text-primary-100 text-sm">Complete listing of all quedan and molasses records</p>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5">
        <button @click="activeTab = 'quedan'" 
            :class="activeTab === 'quedan' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
            class="flex-1 py-2.5 px-4 rounded-xl text-sm font-semibold transition-all">
            <i class="fas fa-qrcode mr-2"></i> Quedan Registry
        </button>
        <button @click="activeTab = 'molasses'" 
            :class="activeTab === 'molasses' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
            class="flex-1 py-2.5 px-4 rounded-xl text-sm font-semibold transition-all">
            <i class="fas fa-flask mr-2"></i> Molasses Registry
        </button>
    </div>

    <!-- Quedan Tab -->
    <div x-show="activeTab === 'quedan'" class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <input type="text" x-model="quedanSearch" @input="quedanCurrentPage = 1" placeholder="Search quedan..."
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-64">
                <span class="text-xs text-gray-500" x-text="'Showing ' + filteredQuedans.length + ' records'"></span>
            </div>
            <a href="{{ route('quedan-registry.export') }}" 
               class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-gray-50 transition">
                <i class="fas fa-file-pdf mr-2 text-red-500"></i> Export PDF
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto" style="max-height: 500px; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('crop_year')">Crop Year</th>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('week_no')">Week</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('planter_code')">Code</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('planter_name')">Planter Name</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">QDN No</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">TIN No</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('total_liens')">Total Liens</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('sugar_lkg')">Sugar Lkg</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('labor_lkg')">Labor Lkg</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="row in paginatedQuedans" :key="row.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-2 text-xs"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium" x-text="row.crop_year"></span></td>
                                <td class="px-3 py-2 text-xs text-center" x-text="row.week_no"></td>
                                <td class="px-3 py-2 text-xs font-mono" x-text="row.planter_code"></td>
                                <td class="px-3 py-2 text-sm font-medium text-gray-900" x-text="row.planter_name"></td>
                                <td class="px-3 py-2 text-xs" x-text="row.qdn_no || '—'"></td>
                                <td class="px-3 py-2 text-xs" x-text="row.tin_no || '—'"></td>
                                <td class="px-3 py-2 text-xs text-right" x-text="formatNum(row.total_liens, 3)"></td>
                                <td class="px-3 py-2 text-xs text-right" x-text="formatNum(row.sugar_lkg, 3)"></td>
                                <td class="px-3 py-2 text-xs text-right" x-text="formatNum(row.labor_lkg, 3)"></td>
                            </tr>
                        </template>
                        <tr x-show="filteredQuedans.length === 0">
                            <td colspan="9" class="px-4 py-12 text-center text-gray-500">No quedan records found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Quedan Pagination -->
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between" x-show="quedanTotalPages > 1">
                <button @click="quedanCurrentPage--" :disabled="quedanCurrentPage === 1" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Previous</button>
                <span class="text-sm text-gray-500" x-text="'Page ' + quedanCurrentPage + ' of ' + quedanTotalPages"></span>
                <button @click="quedanCurrentPage++" :disabled="quedanCurrentPage >= quedanTotalPages" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>

    <!-- Molasses Tab -->
    <div x-show="activeTab === 'molasses'" class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <input type="text" x-model="molassesSearch" @input="molassesCurrentPage = 1" placeholder="Search molasses..."
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-64">
                <span class="text-xs text-gray-500" x-text="'Showing ' + filteredMolasses.length + ' records'"></span>
            </div>
            <a href="{{ route('molasses-registry.export') }}" 
               class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-gray-50 transition">
                <i class="fas fa-file-pdf mr-2 text-red-500"></i> Export PDF
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto" style="max-height: 500px; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortMolasses('crop_year')">Crop Year</th>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortMolasses('week_no')">Week</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortMolasses('planter_code')">Code</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortMolasses('planter_name')">Planter Name</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">TIN No</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">MC No</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortMolasses('mol_net')">Mol Net</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="row in paginatedMolasses" :key="row.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-2 text-xs"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium" x-text="row.crop_year"></span></td>
                                <td class="px-3 py-2 text-xs text-center" x-text="row.week_no"></td>
                                <td class="px-3 py-2 text-xs font-mono" x-text="row.planter_code"></td>
                                <td class="px-3 py-2 text-sm font-medium text-gray-900" x-text="row.planter_name"></td>
                                <td class="px-3 py-2 text-xs" x-text="row.tin_no || '—'"></td>
                                <td class="px-3 py-2 text-xs" x-text="row.mc_no || '—'"></td>
                                <td class="px-3 py-2 text-xs text-right font-semibold" x-text="formatNum(row.mol_net, 3)"></td>
                            </tr>
                        </template>
                        <tr x-show="filteredMolasses.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">No molasses records found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Molasses Pagination -->
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between" x-show="molassesTotalPages > 1">
                <button @click="molassesCurrentPage--" :disabled="molassesCurrentPage === 1" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Previous</button>
                <span class="text-sm text-gray-500" x-text="'Page ' + molassesCurrentPage + ' of ' + molassesTotalPages"></span>
                <button @click="molassesCurrentPage++" :disabled="molassesCurrentPage >= molassesTotalPages" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function registryData() {
        return {
            activeTab: 'quedan',
            quedanSearch: '',
            molassesSearch: '',
            quedanSortField: 'planter_name',
            quedanSortDir: 'asc',
            molassesSortField: 'planter_name',
            molassesSortDir: 'asc',
            quedanCurrentPage: 1,
            molassesCurrentPage: 1,
            perPage: 25,
            allQuedans: @json($quedans),
            allMolasses: @json($molassesList),

            get filteredQuedans() {
                let data = [...this.allQuedans];
                if (this.quedanSearch) {
                    const s = this.quedanSearch.toLowerCase();
                    data = data.filter(d => (d.planter_name || '').toLowerCase().includes(s) || (d.planter_code || '').toLowerCase().includes(s) || (d.qdn_no || '').toLowerCase().includes(s));
                }
                data.sort((a, b) => {
                    let valA = a[this.quedanSortField] ?? '', valB = b[this.quedanSortField] ?? '';
                    if (typeof valA === 'string') valA = valA.toLowerCase();
                    if (typeof valB === 'string') valB = valB.toLowerCase();
                    if (!isNaN(valA)) valA = parseFloat(valA);
                    if (!isNaN(valB)) valB = parseFloat(valB);
                    if (valA < valB) return this.quedanSortDir === 'asc' ? -1 : 1;
                    if (valA > valB) return this.quedanSortDir === 'asc' ? 1 : -1;
                    return 0;
                });
                return data;
            },
            get paginatedQuedans() {
                const start = (this.quedanCurrentPage - 1) * this.perPage;
                return this.filteredQuedans.slice(start, start + this.perPage);
            },
            get quedanTotalPages() { return Math.ceil(this.filteredQuedans.length / this.perPage); },

            get filteredMolasses() {
                let data = [...this.allMolasses];
                if (this.molassesSearch) {
                    const s = this.molassesSearch.toLowerCase();
                    data = data.filter(d => (d.planter_name || '').toLowerCase().includes(s) || (d.planter_code || '').toLowerCase().includes(s));
                }
                data.sort((a, b) => {
                    let valA = a[this.molassesSortField] ?? '', valB = b[this.molassesSortField] ?? '';
                    if (typeof valA === 'string') valA = valA.toLowerCase();
                    if (typeof valB === 'string') valB = valB.toLowerCase();
                    if (!isNaN(valA)) valA = parseFloat(valA);
                    if (!isNaN(valB)) valB = parseFloat(valB);
                    if (valA < valB) return this.molassesSortDir === 'asc' ? -1 : 1;
                    if (valA > valB) return this.molassesSortDir === 'asc' ? 1 : -1;
                    return 0;
                });
                return data;
            },
            get paginatedMolasses() {
                const start = (this.molassesCurrentPage - 1) * this.perPage;
                return this.filteredMolasses.slice(start, start + this.perPage);
            },
            get molassesTotalPages() { return Math.ceil(this.filteredMolasses.length / this.perPage); },

            sortQuedan(field) {
                if (this.quedanSortField === field) { this.quedanSortDir = this.quedanSortDir === 'asc' ? 'desc' : 'asc'; }
                else { this.quedanSortField = field; this.quedanSortDir = 'asc'; }
            },
            sortMolasses(field) {
                if (this.molassesSortField === field) { this.molassesSortDir = this.molassesSortDir === 'asc' ? 'desc' : 'asc'; }
                else { this.molassesSortField = field; this.molassesSortDir = 'asc'; }
            },
            formatNum(num, decimals = 2) {
                return parseFloat(num || 0).toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            }
        }
    }
</script>
@endpush
@endsection