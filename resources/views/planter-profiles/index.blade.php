<!-- resources/views/planter-profiles/index.blade.php -->
@extends('layouts.main')

@section('title', 'Planter Profiles')

@section('content')
    <div x-data="planterData()" class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-address-book text-2xl"></i>
                        <h1 class="text-2xl sm:text-3xl font-bold">Planter Profiles</h1>
                    </div>
                    <p class="text-primary-100 text-sm">Manage planter information and status</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center gap-2">
                    @if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager']) ||
                            auth()->user()->hasPermission('manage-planter-profiles'))
                        <button @click="openAddModal()"
                            class="bg-white text-primary-700 rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-primary-50 transition">
                            <i class="fas fa-plus-circle mr-2"></i> Add Planter
                        </button>
                        <button @click="syncPlanters()"
                            class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-white/30 transition">
                            <i class="fas fa-sync mr-2"></i> Sync
                        </button>
                    @endif
                    <a href="{{ route('planter-profiles.export') }}"
                        class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-white/30 transition">
                        <i class="fas fa-file-pdf mr-2"></i> Export
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500">Total Planters</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500">Active</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500">Inactive</p>
                <p class="text-2xl font-bold text-gray-400">{{ $stats['inactive'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500">New This Month</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['new_this_month'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" x-model="search" @input="applyFilters()" placeholder="Search name or code..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select x-model="statusFilter" @change="applyFilters()"
                        class="border border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <button @click="clearFilters()" class="text-gray-500 hover:text-gray-700 text-sm py-2">
                    <i class="fas fa-times mr-1"></i> Clear
                </button>
                <span class="text-xs text-gray-400 ml-auto"
                    x-text="'Showing ' + filteredPlanters.length + ' planters'"></span>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('planter_code')">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('planter_name')">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Area</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Cane</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="planter in paginatedPlanters" :key="planter.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900"
                                    x-text="planter.planter_code"></td>
                                <td class="px-4 py-3">
                                    <a :href="'/planter-profiles/' + planter.id"
                                        class="text-sm font-medium text-primary-600 hover:text-primary-700"
                                        x-text="planter.planter_name"></a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="planter.contact_number || '—'"></td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="planter.area_location || '—'"></td>
                                <td class="px-4 py-3 text-center">
    <div class="flex items-center justify-center gap-2">
        <!-- Status Badge with pulsing dot -->
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full border"
            :class="planter.status === 'active' 
                ? 'bg-green-50 text-green-700 border-green-200' 
                : planter.status === 'suspended' 
                    ? 'bg-red-50 text-red-700 border-red-200' 
                    : 'bg-gray-50 text-gray-600 border-gray-200'">
            
            <!-- Pulsing green dot for Active -->
            <span class="relative flex h-2 w-2" x-show="planter.status === 'active'">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            
            <!-- Static red dot for Suspended -->
            <span class="relative flex h-2 w-2" x-show="planter.status === 'suspended'">
                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
            </span>
            
            <!-- Static gray dot for Inactive -->
            <span class="relative flex h-2 w-2" x-show="planter.status === 'inactive'">
                <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-400"></span>
            </span>
            
            <span x-text="planter.status.charAt(0).toUpperCase() + planter.status.slice(1)"></span>
        </span>

        <!-- Toggle Button -->
        @if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager']) ||
                auth()->user()->hasPermission('manage-planter-profiles'))
            <button @click.stop="toggleStatus(planter)" 
                class="p-1.5 rounded-lg transition"
                :class="planter.status === 'active' ? 'text-green-600 hover:bg-green-50' : 'text-red-400 hover:bg-red-50'"
                :title="planter.status === 'active' ? 'Click to deactivate' : 'Click to activate'">
                <i class="fas fa-power-off text-xs"></i>
            </button>
        @endif
    </div>
</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold"
                                    x-text="formatNumber(getPlanterStat(planter.planter_code, 'total_cane'), 2) + ' tons'">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a :href="'/planter-profiles/' + planter.id"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><i
                                                class="fas fa-eye text-sm"></i></a>
                                        @if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager']) ||
                                                auth()->user()->hasPermission('manage-planter-profiles'))
                                            <button @click.stop="editPlanter(planter)"
                                                class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg"><i
                                                    class="fas fa-edit text-sm"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredPlanters.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">No planters found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between" x-show="totalPages > 1">
                <button @click="currentPage--" :disabled="currentPage === 1"
                    class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Previous</button>
                <span class="text-sm text-gray-500" x-text="'Page ' + currentPage + ' of ' + totalPages"></span>
                <button @click="currentPage++" :disabled="currentPage >= totalPages"
                    class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Next</button>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4" x-text="editingPlanter ? 'Edit Planter' : 'Add Planter'">
                </h3>
                <form @submit.prevent="savePlanter()" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" x-model="form.planter_code" placeholder="Planter Code *" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                        <input type="text" x-model="form.planter_name" placeholder="Planter Name *" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <input type="text" x-model="form.contact_number" placeholder="Contact Number"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    <input type="text" x-model="form.address" placeholder="Address"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" x-model="form.area_location" placeholder="Area Location"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                        <input type="number" x-model="form.total_area" placeholder="Total Area (ha)" step="0.01"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <select x-model="form.status"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        <input type="date" x-model="form.membership_date"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                        <input type="text" x-model="form.crop_year" placeholder="Crop Year"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    </div>
                    <textarea x-model="form.notes" placeholder="Notes" rows="2"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm"></textarea>
                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function planterData() {
                return {
                    allPlanters: @json($allPlanters),
                    planterStats: @json($planterStats),
                    search: '',
                    statusFilter: 'all',
                    sortField: 'planter_name',
                    sortDirection: 'asc',
                    currentPage: 1,
                    perPage: 25,
                    showModal: false,
                    editingPlanter: null,
                    form: {
                        planter_code: '',
                        planter_name: '',
                        contact_number: '',
                        address: '',
                        area_location: '',
                        total_area: '',
                        status: 'active',
                        membership_date: '',
                        crop_year: '',
                        notes: ''
                    },

                    get filteredPlanters() {
                        let planters = [...this.allPlanters];
                        if (this.search) {
                            const s = this.search.toLowerCase();
                            planters = planters.filter(p => (p.planter_name || '').toLowerCase().includes(s) || (p
                                .planter_code || '').toLowerCase().includes(s));
                        }
                        if (this.statusFilter !== 'all') {
                            planters = planters.filter(p => p.status === this.statusFilter);
                        }
                        planters.sort((a, b) => {
                            let valA = a[this.sortField] ?? '',
                                valB = b[this.sortField] ?? '';
                            if (typeof valA === 'string') valA = valA.toLowerCase();
                            if (typeof valB === 'string') valB = valB.toLowerCase();
                            if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                            if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                            return 0;
                        });
                        return planters;
                    },
                    get paginatedPlanters() {
                        const start = (this.currentPage - 1) * this.perPage;
                        return this.filteredPlanters.slice(start, start + this.perPage);
                    },
                    get totalPages() {
                        return Math.ceil(this.filteredPlanters.length / this.perPage);
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
                    },
                    clearFilters() {
                        this.search = '';
                        this.statusFilter = 'all';
                        this.applyFilters();
                    },
                    getPlanterStat(code, field) {
                        const stat = this.planterStats[code];
                        return stat ? parseFloat(stat[field] || 0) : 0;
                    },
                    formatNumber(num, decimals = 2) {
                        return parseFloat(num || 0).toLocaleString('en-US', {
                            minimumFractionDigits: decimals,
                            maximumFractionDigits: decimals
                        });
                    },
                    openAddModal() {
                        this.editingPlanter = null;
                        this.form = {
                            planter_code: '',
                            planter_name: '',
                            contact_number: '',
                            address: '',
                            area_location: '',
                            total_area: '',
                            status: 'active',
                            membership_date: '',
                            crop_year: '',
                            notes: ''
                        };
                        this.showModal = true;
                    },
                    editPlanter(planter) {
                        this.editingPlanter = planter;
                        this.form = {
                            ...planter
                        };
                        this.showModal = true;
                    },
                    async savePlanter() {
                        const url = this.editingPlanter ? '/planter-profiles/' + this.editingPlanter.id :
                            '/planter-profiles';
                        const method = this.editingPlanter ? 'PUT' : 'POST';
                        try {
                            const r = await fetch(url, {
                                method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(this.form)
                            });
                            const d = await r.json();
                            if (!r.ok) throw new Error(d.message);
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: d.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            this.showModal = false;
                            setTimeout(() => location.reload(), 1500);
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: e.message
                            });
                        }
                    },
                    async toggleStatus(planter) {
                        try {
                            const r = await fetch('/planter-profiles/' + planter.id + '/toggle-status', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const d = await r.json();

                            // Update the planter in the allPlanters array
                            const index = this.allPlanters.findIndex(p => p.id === planter.id);
                            if (index !== -1) {
                                this.allPlanters[index].status = d.status;
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: d.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: e.message
                            });
                        }
                    },
                    async syncPlanters() {
                        Swal.fire({
                            title: 'Syncing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        try {
                            const r = await fetch('/planter-profiles/sync', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const d = await r.json();
                            Swal.fire({
                                icon: 'success',
                                title: 'Synced!',
                                text: d.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1500);
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: e.message
                            });
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
