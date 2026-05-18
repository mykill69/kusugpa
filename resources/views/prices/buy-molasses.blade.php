<!-- resources/views/prices/buy-molasses.blade.php -->
@extends('layouts.main')

@section('title', 'Buy Molasses')

@section('content')
<div x-data="buyMolassesData()" class="space-y-6">
    <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
        <div class="flex items-center gap-3 mb-2">
            <i class="fas fa-cart-shopping text-2xl"></i>
            <h1 class="text-2xl sm:text-3xl font-bold">Buy Molasses</h1>
        </div>
        <p class="text-primary-100 text-sm">Select molasses to mark as bought</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Crop Year</label>
                <select x-model="filters.cropYear" @change="onCropYearChange()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    <option value="">Select Crop Year</option>
                    @foreach($cropYears as $cy)
                        <option value="{{ $cy }}">{{ $cy }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Week No</label>
                <select x-model="filters.weekNo" @change="loadData()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    <option value="">All Weeks</option>
                    <template x-for="w in availableWeeks" :key="w">
                        <option :value="w" x-text="'Week ' + w"></option>
                    </template>
                </select>
            </div>
            <button @click="loadData()" :disabled="!filters.cropYear" 
                class="bg-primary-600 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-primary-700 disabled:opacity-50">
                <i class="fas fa-search mr-1"></i> Load Molasses
            </button>
            <div class="flex-1"></div>
            <div class="flex items-center gap-2" x-show="stats.total > 0">
                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600" x-text="'Total: ' + stats.total"></span>
                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700" x-text="'Bought: ' + stats.bought"></span>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div x-show="selectedIds.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
        <span class="text-sm text-gray-600" x-text="selectedIds.length + ' selected'"></span>
        <button @click="bulkUpdate('bought')" class="bg-green-600 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-green-700">
            <i class="fas fa-check mr-1"></i> Mark as Bought
        </button>
        <button @click="bulkUpdate('pending')" class="bg-yellow-600 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-yellow-700">
            <i class="fas fa-undo mr-1"></i> Reset to Pending
        </button>
        <button @click="selectedIds = []" class="text-gray-500 hover:text-gray-700 text-sm">Clear</button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto" style="max-height: 500px; overflow-y: auto;">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-2.5 w-10"><input type="checkbox" @change="toggleAll($event)" :checked="isAllSelected" class="rounded border-gray-300"></th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Crop Year</th>
                        <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Week</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Planter Name</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Mol Net</th>
                        <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="row in paginatedData" :key="row.id">
                        <tr :class="row.status === 'bought' ? 'bg-green-50/50' : ''" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-3 py-2"><input type="checkbox" :checked="selectedIds.includes(row.id)" @change="toggleSelect(row.id)" class="rounded border-gray-300"></td>
                            <td class="px-3 py-2 text-xs"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full" x-text="row.crop_year"></span></td>
                            <td class="px-3 py-2 text-xs text-center" x-text="row.week_no"></td>
                            <td class="px-3 py-2 text-xs font-mono" x-text="row.planter_code"></td>
                            <td class="px-3 py-2 text-sm font-medium" x-text="row.planter_name"></td>
                            <td class="px-3 py-2 text-xs text-right font-semibold" x-text="formatNum(row.mol_net, 3)"></td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full"
                                    :class="row.status === 'bought' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                                    x-text="row.status"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="data.length === 0">
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">Select crop year and click Load Molasses</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between" x-show="totalPages > 1">
            <button @click="currentPage--" :disabled="currentPage === 1" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Previous</button>
            <span class="text-sm text-gray-500" x-text="'Page ' + currentPage + ' of ' + totalPages"></span>
            <button @click="currentPage++" :disabled="currentPage >= totalPages" class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Next</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function buyMolassesData() {
        return {
            filters: { cropYear: '', weekNo: '' },
            availableWeeks: [],
            data: [],
            stats: { total: 0, bought: 0, pending: 0 },
            selectedIds: [],
            currentPage: 1,
            perPage: 25,

            get paginatedData() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.data.slice(start, start + this.perPage);
            },
            get totalPages() { return Math.max(1, Math.ceil(this.data.length / this.perPage)); },
            get isAllSelected() {
                return this.paginatedData.length > 0 && this.paginatedData.every(r => this.selectedIds.includes(r.id));
            },

            async onCropYearChange() {
                this.filters.weekNo = '';
                if (this.filters.cropYear) {
                    const r = await fetch(`{{ route('registry.data') }}?type=molasses&crop_year=${this.filters.cropYear}`, { headers: { 'Accept': 'application/json' } });
                    const d = await r.json();
                    this.availableWeeks = d.weeks || [];
                } else { this.availableWeeks = []; }
            },

            async loadData() {
                if (!this.filters.cropYear) return;
                const params = new URLSearchParams({ type: 'molasses', crop_year: this.filters.cropYear, week_no: this.filters.weekNo });
                const r = await fetch(`{{ route('registry.data') }}?${params}`, { headers: { 'Accept': 'application/json' } });
                const d = await r.json();
                this.data = d.data || [];
                this.stats = d.stats || { total: 0, bought: 0, pending: 0 };
                this.selectedIds = [];
                this.currentPage = 1;
            },

            toggleSelect(id) {
                const idx = this.selectedIds.indexOf(id);
                idx > -1 ? this.selectedIds.splice(idx, 1) : this.selectedIds.push(id);
            },
            toggleAll(e) {
                if (e.target.checked) {
                    this.paginatedData.forEach(r => { if (!this.selectedIds.includes(r.id)) this.selectedIds.push(r.id); });
                } else {
                    this.selectedIds = this.selectedIds.filter(id => !this.paginatedData.map(r => r.id).includes(id));
                }
            },
            async bulkUpdate(status) {
                if (!this.selectedIds.length) return;
                const r = await fetch('{{ route('molasses.bulk-update') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ ids: this.selectedIds, status })
                });
                const d = await r.json();
                Swal.fire({ icon: 'success', title: 'Updated!', text: d.message, timer: 2000, showConfirmButton: false });
                this.selectedIds = [];
                await this.loadData();
            },
            formatNum(num, decimals = 2) {
                return parseFloat(num || 0).toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            }
        }
    }
</script>
@endpush
@endsection