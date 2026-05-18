<!-- resources/views/prices/index.blade.php -->
@extends('layouts.main')

@section('title', 'Price Management')

@section('content')
    <div x-data="priceData()" class="space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-tags text-2xl"></i>
                <h1 class="text-2xl sm:text-3xl font-bold">Price Management</h1>
            </div>
            <p class="text-primary-100 text-sm">Manage Quedan and Molasses prices</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Quedan Prices Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="bg-green-100 rounded-lg p-2">
                                <i class="fas fa-tag text-green-600 text-sm"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900">Quedan Prices</h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <select x-model="quedanCropYearFilter" @change="filterQuedan()"
                                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-primary-500">
                                <option value="">All Years</option>
                                <template x-for="cy in cropYears" :key="cy.id">
                                    <option :value="cy.crop_year" x-text="cy.crop_year"></option>
                                </template>
                            </select>
                            <span class="text-xs text-gray-500" x-text="filteredQuedan.length + ' entries'"></span>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('crop_year')">
                                    Crop Year <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('week_no')">
                                    Week <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortQuedan('quedan_price')">
                                    Price <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="price in sortedQuedan" :key="price.id">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium" x-text="price.crop_year"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-semibold text-gray-900" x-text="'Week ' + price.week_no"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-bold text-gray-900" x-text="price.quedan_type || 'N/A'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm font-semibold text-gray-900">₱<span x-text="formatNumber(price.quedan_price)"></span></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="editQuedan(price)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button @click="deleteQuedan(price.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredQuedan.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-tag text-3xl text-gray-200 mb-2 block"></i>
                                    No quedan prices found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Molasses Prices Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="bg-blue-100 rounded-lg p-2">
                                <i class="fas fa-flask text-blue-600 text-sm"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900">Molasses Prices</h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <select x-model="molassesCropYearFilter" @change="filterMolasses()"
                                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-primary-500">
                                <option value="">All Years</option>
                                <template x-for="cy in cropYears" :key="cy.id">
                                    <option :value="cy.crop_year" x-text="cy.crop_year"></option>
                                </template>
                            </select>
                            <span class="text-xs text-gray-500" x-text="filteredMolasses.length + ' entries'"></span>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortMolasses('crop_year')">
                                    Crop Year <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortMolasses('week_no')">
                                    Week <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100" @click="sortMolasses('mol_price')">
                                    Price <i class="fas fa-sort text-gray-300 ml-1"></i>
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="price in sortedMolasses" :key="price.id">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium" x-text="price.crop_year"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-semibold text-gray-900" x-text="'Week ' + price.week_no"></span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm font-semibold text-gray-900">₱<span x-text="formatNumber(price.mol_price)"></span></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="editMolasses(price)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button @click="deleteMolasses(price.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredMolasses.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-flask text-3xl text-gray-200 mb-2 block"></i>
                                    No molasses prices found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Edit Quedan Modal -->
        <div x-show="showQuedanModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="showQuedanModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Quedan Price</h3>
                <form @submit.prevent="saveQuedan()" class="space-y-3">
                    <input type="text" x-model="quedanForm.quedan_type" placeholder="Quedan Type (e.g., A, B, C)" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    <input type="number" x-model="quedanForm.quedan_price" step="0.01" placeholder="Price" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    <input type="text" x-model="quedanForm.crop_year" placeholder="Crop Year" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    <input type="text" x-model="quedanForm.week_no" placeholder="Week Number" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showQuedanModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Molasses Modal -->
        <div x-show="showMolassesModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="showMolassesModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Molasses Price</h3>
                <form @submit.prevent="saveMolasses()" class="space-y-3">
                    <input type="number" x-model="molassesForm.mol_price" step="0.01" placeholder="Price" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    <input type="text" x-model="molassesForm.crop_year" placeholder="Crop Year" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    <input type="text" x-model="molassesForm.week_no" placeholder="Week Number" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showMolassesModal = false" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function priceData() {
            return {
                showQuedanModal: false,
                showMolassesModal: false,
                editingQuedanId: null,
                editingMolassesId: null,
                quedanForm: { quedan_type: '', quedan_price: '', crop_year: '', week_no: '' },
                molassesForm: { mol_price: '', crop_year: '', week_no: '' },
                quedanSortField: 'crop_year',
                quedanSortDir: 'asc',
                molassesSortField: 'crop_year',
                molassesSortDir: 'asc',
                quedanCropYearFilter: '',
                molassesCropYearFilter: '',
                cropYears: {!! json_encode($cropYearsData) !!},
                quedanPrices: {!! json_encode($quedanPricesData) !!},
                molassesPrices: {!! json_encode($molassesPricesData) !!},

                get filteredQuedan() {
                    if (!this.quedanCropYearFilter) return this.quedanPrices;
                    return this.quedanPrices.filter(p => p.crop_year === this.quedanCropYearFilter);
                },
                get sortedQuedan() {
                    return [...this.filteredQuedan].sort((a, b) => {
                        let valA = a[this.quedanSortField] ?? '';
                        let valB = b[this.quedanSortField] ?? '';
                        if (typeof valA === 'string') valA = valA.toLowerCase();
                        if (typeof valB === 'string') valB = valB.toLowerCase();
                        if (valA < valB) return this.quedanSortDir === 'asc' ? -1 : 1;
                        if (valA > valB) return this.quedanSortDir === 'asc' ? 1 : -1;
                        return 0;
                    });
                },
                get filteredMolasses() {
                    if (!this.molassesCropYearFilter) return this.molassesPrices;
                    return this.molassesPrices.filter(p => p.crop_year === this.molassesCropYearFilter);
                },
                get sortedMolasses() {
                    return [...this.filteredMolasses].sort((a, b) => {
                        let valA = a[this.molassesSortField] ?? '';
                        let valB = b[this.molassesSortField] ?? '';
                        if (typeof valA === 'string') valA = valA.toLowerCase();
                        if (typeof valB === 'string') valB = valB.toLowerCase();
                        if (valA < valB) return this.molassesSortDir === 'asc' ? -1 : 1;
                        if (valA > valB) return this.molassesSortDir === 'asc' ? 1 : -1;
                        return 0;
                    });
                },

                sortQuedan(field) {
                    if (this.quedanSortField === field) { this.quedanSortDir = this.quedanSortDir === 'asc' ? 'desc' : 'asc'; }
                    else { this.quedanSortField = field; this.quedanSortDir = 'asc'; }
                },
                sortMolasses(field) {
                    if (this.molassesSortField === field) { this.molassesSortDir = this.molassesSortDir === 'asc' ? 'desc' : 'asc'; }
                    else { this.molassesSortField = field; this.molassesSortDir = 'asc'; }
                },
                filterQuedan() {},
                filterMolasses() {},

                editQuedan(price) {
                    this.editingQuedanId = price.id;
                    this.quedanForm = { quedan_type: price.quedan_type, quedan_price: price.quedan_price, crop_year: price.crop_year, week_no: price.week_no };
                    this.showQuedanModal = true;
                },
                async saveQuedan() {
                    try {
                        const r = await fetch('/prices/quedan/' + this.editingQuedanId, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify(this.quedanForm)
                        });
                        const d = await r.json();
                        if (!r.ok) throw new Error(d.message);
                        Swal.fire({ icon: 'success', title: 'Success!', text: d.message, timer: 2000, showConfirmButton: false });
                        this.showQuedanModal = false;
                        setTimeout(() => location.reload(), 1500);
                    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.message }); }
                },
                async deleteQuedan(id) {
                    const result = await Swal.fire({ title: 'Delete?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes', confirmButtonColor: '#dc2626' });
                    if (!result.isConfirmed) return;
                    try {
                        const r = await fetch('/prices/quedan/' + id, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                        const d = await r.json();
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: d.message, timer: 2000, showConfirmButton: false });
                        setTimeout(() => location.reload(), 1500);
                    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.message }); }
                },

                editMolasses(price) {
                    this.editingMolassesId = price.id;
                    this.molassesForm = { mol_price: price.mol_price, crop_year: price.crop_year, week_no: price.week_no };
                    this.showMolassesModal = true;
                },
                async saveMolasses() {
                    try {
                        const r = await fetch('/prices/molasses/' + this.editingMolassesId, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify(this.molassesForm)
                        });
                        const d = await r.json();
                        if (!r.ok) throw new Error(d.message);
                        Swal.fire({ icon: 'success', title: 'Success!', text: d.message, timer: 2000, showConfirmButton: false });
                        this.showMolassesModal = false;
                        setTimeout(() => location.reload(), 1500);
                    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.message }); }
                },
                async deleteMolasses(id) {
                    const result = await Swal.fire({ title: 'Delete?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes', confirmButtonColor: '#dc2626' });
                    if (!result.isConfirmed) return;
                    try {
                        const r = await fetch('/prices/molasses/' + id, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                        const d = await r.json();
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: d.message, timer: 2000, showConfirmButton: false });
                        setTimeout(() => location.reload(), 1500);
                    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.message }); }
                },

                formatNumber(num) {
                    return parseFloat(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            };
        }
    </script>
@endsection