<!-- resources/views/prices/registry.blade.php -->
@extends('layouts.main')

@section('title', 'Quedan & Molasses Registry')

@section('content')
    <div x-data="registryData()" x-init="initRegistry()" class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-list-ul text-2xl"></i>
                <h1 class="text-2xl sm:text-3xl font-bold">Quedan & Molasses Registry</h1>
            </div>
            <p class="text-primary-100 text-sm">Complete listing of all quedan and molasses records</p>
        </div>

        <!-- Tab Navigation -->
        <div class="flex gap-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5">
            <button @click="activeTab = 'quedan'; resetSelections()"
                :class="activeTab === 'quedan' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                class="flex-1 py-2.5 px-4 rounded-xl text-sm font-semibold transition-all">
                <i class="fas fa-qrcode mr-2"></i> Quedan Registry
            </button>
            <button @click="activeTab = 'molasses'; resetSelections()"
                :class="activeTab === 'molasses' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                class="flex-1 py-2.5 px-4 rounded-xl text-sm font-semibold transition-all">
                <i class="fas fa-flask mr-2"></i> Molasses Registry
            </button>
        </div>

        <!-- Quedan Tab -->
        <div x-show="activeTab === 'quedan'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input type="text" x-model="quedanSearch" @input="quedanCurrentPage = 1"
                        placeholder="Search quedan or planter name..." class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-64">
                    <span class="text-xs text-gray-500" x-text="'Showing ' + filteredQuedans.length + ' records'"></span>
                </div>
                <div class="flex items-center gap-2">
                    <button x-show="quedanSelectedIds.length > 0" @click="confirmDeleteSelected('quedan')"
                        class="bg-red-500 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-red-600 transition">
                        <i class="fas fa-trash mr-2"></i> Delete (<span x-text="quedanSelectedIds.length"></span>)
                    </button>
                    <button @click="showDeleteModal = true; deleteType = 'quedan'"
                        class="bg-red-100 text-red-700 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-red-200 transition">
                        <i class="fas fa-trash mr-2"></i> Delete Options
                    </button>
                    <a href="{{ route('quedan-registry.export') }}"
                        class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-gray-50 transition">
                        <i class="fas fa-file-pdf mr-2 text-red-500"></i> Export PDF
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto" style="max-height: 500px; overflow-y: auto;">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">
                                    <input type="checkbox" @click="toggleSelectAllQuedan" :checked="isAllQuedanSelected"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortQuedan('crop_year')">Crop Year</th>
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortQuedan('week_no')">Week</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortQuedan('planter_code')">Code</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortQuedan('planter_name')">Planter Name</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">QDN No</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">TIN No</th>
                                <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortQuedan('total_liens')">Total Liens</th>
                                <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortQuedan('sugar_lkg')">Sugar Lkg</th>
                                <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortQuedan('labor_lkg')">Labor Lkg</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="row in paginatedQuedans" :key="row.id">
                                <tr class="hover:bg-gray-50/50 transition-colors"
                                    :class="{ 'bg-primary-50': quedanSelectedIds.includes(row.id) }">
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox" :value="row.id" x-model="quedanSelectedIds"
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    </td>
                                    <td class="px-3 py-2 text-xs"><span
                                            class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium"
                                            x-text="row.crop_year"></span></td>
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
                                <td colspan="10" class="px-4 py-12 text-center text-gray-500">No quedan records found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between"
                    x-show="quedanTotalPages > 1">
                    <button @click="quedanCurrentPage--" :disabled="quedanCurrentPage === 1"
                        class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Previous</button>
                    <span class="text-sm text-gray-500"
                        x-text="'Page ' + quedanCurrentPage + ' of ' + quedanTotalPages"></span>
                    <button @click="quedanCurrentPage++" :disabled="quedanCurrentPage >= quedanTotalPages"
                        class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Next</button>
                </div>
            </div>
        </div>

        <!-- Molasses Tab -->
        <div x-show="activeTab === 'molasses'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input type="text" x-model="molassesSearch" @input="molassesCurrentPage = 1"
                        placeholder="Search molasses..." class="border border-gray-200 rounded-xl px-3 py-2 text-sm w-64">
                    <span class="text-xs text-gray-500" x-text="'Showing ' + filteredMolasses.length + ' records'"></span>
                </div>
                <div class="flex items-center gap-2">
                    <button x-show="molassesSelectedIds.length > 0" @click="confirmDeleteSelected('molasses')"
                        class="bg-red-500 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-red-600 transition">
                        <i class="fas fa-trash mr-2"></i> Delete (<span x-text="molassesSelectedIds.length"></span>)
                    </button>
                    <button @click="showDeleteModal = true; deleteType = 'molasses'"
                        class="bg-red-100 text-red-700 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-red-200 transition">
                        <i class="fas fa-trash mr-2"></i> Delete Options
                    </button>
                    <a href="{{ route('molasses-registry.export') }}"
                        class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-gray-50 transition">
                        <i class="fas fa-file-pdf mr-2 text-red-500"></i> Export PDF
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto" style="max-height: 500px; overflow-y: auto;">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">
                                    <input type="checkbox" @click="toggleSelectAllMolasses"
                                        :checked="isAllMolassesSelected"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortMolasses('crop_year')">Crop Year</th>
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortMolasses('week_no')">Week</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortMolasses('planter_code')">Code</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortMolasses('planter_name')">Planter Name</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">TIN No</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">MC No</th>
                                <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                    @click="sortMolasses('mol_net')">Mol Net</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="row in paginatedMolasses" :key="row.id">
                                <tr class="hover:bg-gray-50/50 transition-colors"
                                    :class="{ 'bg-primary-50': molassesSelectedIds.includes(row.id) }">
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox" :value="row.id" x-model="molassesSelectedIds"
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    </td>
                                    <td class="px-3 py-2 text-xs"><span
                                            class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium"
                                            x-text="row.crop_year"></span></td>
                                    <td class="px-3 py-2 text-xs text-center" x-text="row.week_no"></td>
                                    <td class="px-3 py-2 text-xs font-mono" x-text="row.planter_code"></td>
                                    <td class="px-3 py-2 text-sm font-medium text-gray-900" x-text="row.planter_name">
                                    </td>
                                    <td class="px-3 py-2 text-xs" x-text="row.tin_no || '—'"></td>
                                    <td class="px-3 py-2 text-xs" x-text="row.mc_no || '—'"></td>
                                    <td class="px-3 py-2 text-xs text-right font-semibold"
                                        x-text="formatNum(row.mol_net, 3)"></td>
                                </tr>
                            </template>
                            <tr x-show="filteredMolasses.length === 0">
                                <td colspan="8" class="px-4 py-12 text-center text-gray-500">No molasses records found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between"
                    x-show="molassesTotalPages > 1">
                    <button @click="molassesCurrentPage--" :disabled="molassesCurrentPage === 1"
                        class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Previous</button>
                    <span class="text-sm text-gray-500"
                        x-text="'Page ' + molassesCurrentPage + ' of ' + molassesTotalPages"></span>
                    <button @click="molassesCurrentPage++" :disabled="molassesCurrentPage >= molassesTotalPages"
                        class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Next</button>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-trash text-red-500 mr-2"></i> Delete Registry Records
                </h3>

                <div class="space-y-4">
                    <!-- Delete ALL (Both Quedan and Molasses) -->
                    <div class="border-2 border-red-300 rounded-xl p-4 bg-red-50">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                            <h4 class="font-semibold text-red-700">Delete ALL Records (Quedan & Molasses)</h4>
                        </div>
                        <p class="text-xs text-red-600 mb-3">This will permanently delete <strong>both</strong> quedan and
                            molasses records.</p>
                        <div class="flex gap-2 mb-3">
                            <span class="bg-red-200 text-red-800 text-xs px-2 py-1 rounded-full">
                                <strong x-text="totalQuedanCount"></strong> Quedan records
                            </span>
                            <span class="bg-red-200 text-red-800 text-xs px-2 py-1 rounded-full">
                                <strong x-text="totalMolassesCount"></strong> Molasses records
                            </span>
                        </div>
                        <button @click="confirmDeleteAll()"
                            class="w-full bg-red-600 text-white rounded-lg px-4 py-2.5 text-sm font-semibold hover:bg-red-700 transition">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Delete ALL Records (Quedan & Molasses)
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="flex items-center gap-3">
                        <div class="flex-1 border-t border-gray-200"></div>
                        <span class="text-xs text-gray-400 font-medium">OR DELETE BY TYPE</span>
                        <div class="flex-1 border-t border-gray-200"></div>
                    </div>

                    <!-- Delete Only Quedan -->
                    <div class="border border-orange-200 rounded-xl p-4 bg-orange-50">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-qrcode text-orange-600"></i>
                            <h4 class="font-semibold text-orange-700">Delete Quedan Records Only</h4>
                        </div>
                        <p class="text-xs text-orange-600 mb-3">Select options to delete specific quedan records. Molasses
                            records will not be affected.</p>

                        <!-- Delete ALL Quedan -->
                        <div class="mb-3 pb-3 border-b border-orange-200">
                            <button @click="confirmDeleteByType('quedan', 'all')"
                                class="w-full bg-orange-500 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-orange-600 transition">
                                <i class="fas fa-trash mr-2"></i> Delete ALL Quedan Records (<span
                                    x-text="totalQuedanCount"></span>)
                            </button>
                        </div>

                        <!-- Delete Quedan by Week -->
                        <div>
                            <p class="text-xs font-medium text-orange-700 mb-2">Or delete by Crop Year & Week:</p>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Crop Year</label>
                                    <select x-model="quedanDeleteForm.crop_year" @change="updateQuedanWeekOptions()"
                                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs">
                                        <option value="">Select</option>
                                        <template x-for="cy in quedanCropYears" :key="cy">
                                            <option :value="cy" x-text="cy"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Week No</label>
                                    <select x-model="quedanDeleteForm.week_no" :disabled="!quedanDeleteForm.crop_year"
                                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs disabled:opacity-50">
                                        <option value="">Select</option>
                                        <template x-for="wk in quedanWeeks" :key="wk.week_no">
                                            <option :value="wk.week_no"
                                                x-text="'Week ' + wk.week_no + ' (' + wk.count + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <button @click="confirmDeleteByType('quedan', 'week')"
                                :disabled="!quedanDeleteForm.crop_year || !quedanDeleteForm.week_no"
                                class="w-full bg-orange-400 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-orange-500 transition disabled:opacity-50">
                                <i class="fas fa-trash mr-2"></i> Delete Quedan by Week
                            </button>
                        </div>
                    </div>

                    <!-- Delete Only Molasses -->
                    <div class="border border-blue-200 rounded-xl p-4 bg-blue-50">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-flask text-blue-600"></i>
                            <h4 class="font-semibold text-blue-700">Delete Molasses Records Only</h4>
                        </div>
                        <p class="text-xs text-blue-600 mb-3">Select options to delete specific molasses records. Quedan
                            records will not be affected.</p>

                        <!-- Delete ALL Molasses -->
                        <div class="mb-3 pb-3 border-b border-blue-200">
                            <button @click="confirmDeleteByType('molasses', 'all')"
                                class="w-full bg-blue-500 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-blue-600 transition">
                                <i class="fas fa-trash mr-2"></i> Delete ALL Molasses Records (<span
                                    x-text="totalMolassesCount"></span>)
                            </button>
                        </div>

                        <!-- Delete Molasses by Week -->
                        <div>
                            <p class="text-xs font-medium text-blue-700 mb-2">Or delete by Crop Year & Week:</p>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Crop Year</label>
                                    <select x-model="molassesDeleteForm.crop_year" @change="updateMolassesWeekOptions()"
                                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs">
                                        <option value="">Select</option>
                                        <template x-for="cy in molassesCropYears" :key="cy">
                                            <option :value="cy" x-text="cy"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Week No</label>
                                    <select x-model="molassesDeleteForm.week_no" :disabled="!molassesDeleteForm.crop_year"
                                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs disabled:opacity-50">
                                        <option value="">Select</option>
                                        <template x-for="wk in molassesWeeks" :key="wk.week_no">
                                            <option :value="wk.week_no"
                                                x-text="'Week ' + wk.week_no + ' (' + wk.count + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <button @click="confirmDeleteByType('molasses', 'week')"
                                :disabled="!molassesDeleteForm.crop_year || !molassesDeleteForm.week_no"
                                class="w-full bg-blue-400 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-blue-500 transition disabled:opacity-50">
                                <i class="fas fa-trash mr-2"></i> Delete Molasses by Week
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <button @click="showDeleteModal = false; resetDeleteForms()"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function registryData() {
                return {
                    activeTab: 'quedan',
                    deleteType: 'quedan',
                    showDeleteModal: false,

                    // Delete forms
                    quedanDeleteForm: {
                        crop_year: '',
                        week_no: ''
                    },
                    molassesDeleteForm: {
                        crop_year: '',
                        week_no: ''
                    },

                    // Filter data
                    quedanCropYears: [],
                    quedanWeeks: [],
                    quedanAllWeeksData: [],
                    molassesCropYears: [],
                    molassesWeeks: [],
                    molassesAllWeeksData: [],

                    // Counts
                    totalQuedanCount: 0,
                    totalMolassesCount: 0,

                    // Quedan data
                    quedanSearch: '',
                    quedanSortField: 'null',
                    quedanSortDir: 'asc',
                    quedanCurrentPage: 1,
                    quedanSelectedIds: [],
                    perPage: 25,
                    allQuedans: @json($quedans),

                    // Molasses data
                    molassesSearch: '',
                    molassesSortField: 'null',
                    molassesSortDir: 'asc',
                    molassesCurrentPage: 1,
                    molassesSelectedIds: [],
                    allMolasses: @json($molassesList),

                    initRegistry() {
                        this.totalQuedanCount = this.allQuedans.length;
                        this.totalMolassesCount = this.allMolasses.length;
                        this.fetchAllFilters();
                    },

                    resetSelections() {
                        this.quedanSelectedIds = [];
                        this.molassesSelectedIds = [];
                    },

                    resetDeleteForms() {
                        this.quedanDeleteForm = {
                            crop_year: '',
                            week_no: ''
                        };
                        this.molassesDeleteForm = {
                            crop_year: '',
                            week_no: ''
                        };
                        this.quedanWeeks = [];
                        this.molassesWeeks = [];
                    },

                    async fetchAllFilters() {
                        try {
                            // Fetch quedan filters
                            const quedanRes = await fetch('{{ route('registry.filters') }}?type=quedan', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const quedanData = await quedanRes.json();
                            this.quedanCropYears = quedanData.crop_years || [];
                            this.quedanAllWeeksData = quedanData.weeks_data || [];

                            // Fetch molasses filters
                            const molassesRes = await fetch('{{ route('registry.filters') }}?type=molasses', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const molassesData = await molassesRes.json();
                            this.molassesCropYears = molassesData.crop_years || [];
                            this.molassesAllWeeksData = molassesData.weeks_data || [];
                        } catch (error) {
                            console.error('Error fetching filters:', error);
                        }
                    },

                    updateQuedanWeekOptions() {
                        this.quedanDeleteForm.week_no = '';
                        if (this.quedanDeleteForm.crop_year && this.quedanAllWeeksData.length > 0) {
                            this.quedanWeeks = this.quedanAllWeeksData
                                .filter(w => w.crop_year === this.quedanDeleteForm.crop_year)
                                .map(w => ({
                                    week_no: w.week_no,
                                    count: w.count
                                }))
                                .sort((a, b) => parseInt(a.week_no) - parseInt(b.week_no));
                        } else {
                            this.quedanWeeks = [];
                        }
                    },

                    updateMolassesWeekOptions() {
                        this.molassesDeleteForm.week_no = '';
                        if (this.molassesDeleteForm.crop_year && this.molassesAllWeeksData.length > 0) {
                            this.molassesWeeks = this.molassesAllWeeksData
                                .filter(w => w.crop_year === this.molassesDeleteForm.crop_year)
                                .map(w => ({
                                    week_no: w.week_no,
                                    count: w.count
                                }))
                                .sort((a, b) => parseInt(a.week_no) - parseInt(b.week_no));
                        } else {
                            this.molassesWeeks = [];
                        }
                    },

                    // Quedan computed
                    get filteredQuedans() {
                        let data = [...this.allQuedans];
                        if (this.quedanSearch) {
                            const s = this.quedanSearch.toLowerCase();
                            data = data.filter(d => (d.planter_name || '').toLowerCase().includes(s) || (d.planter_code ||
                                '').toLowerCase().includes(s) || (d.qdn_no || '').toLowerCase().includes(s));
                        }
                        // Only sort if user has clicked a column header
                        if (this.quedanSortField) {
                            data.sort((a, b) => {
                                let valA = a[this.quedanSortField] ?? '',
                                    valB = b[this.quedanSortField] ?? '';
                                if (typeof valA === 'string') valA = valA.toLowerCase();
                                if (typeof valB === 'string') valB = valB.toLowerCase();
                                if (!isNaN(valA) && valA !== '') valA = parseFloat(valA);
                                if (!isNaN(valB) && valB !== '') valB = parseFloat(valB);
                                if (valA < valB) return this.quedanSortDir === 'asc' ? -1 : 1;
                                if (valA > valB) return this.quedanSortDir === 'asc' ? 1 : -1;
                                return 0;
                            });
                        }
                        return data;
                    },
                    get paginatedQuedans() {
                        const start = (this.quedanCurrentPage - 1) * this.perPage;
                        return this.filteredQuedans.slice(start, start + this.perPage);
                    },
                    get quedanTotalPages() {
                        return Math.ceil(this.filteredQuedans.length / this.perPage);
                    },
                    get isAllQuedanSelected() {
                        return this.paginatedQuedans.length > 0 && this.paginatedQuedans.every(row => this.quedanSelectedIds
                            .includes(row.id));
                    },
                    toggleSelectAllQuedan() {
                        if (this.isAllQuedanSelected) {
                            const pageIds = this.paginatedQuedans.map(r => r.id);
                            this.quedanSelectedIds = this.quedanSelectedIds.filter(id => !pageIds.includes(id));
                        } else {
                            const pageIds = this.paginatedQuedans.map(r => r.id);
                            this.quedanSelectedIds = [...new Set([...this.quedanSelectedIds, ...pageIds])];
                        }
                    },

                    // Molasses computed
                    get filteredMolasses() {
                        let data = [...this.allMolasses];
                        if (this.molassesSearch) {
                            const s = this.molassesSearch.toLowerCase();
                            data = data.filter(d => (d.planter_name || '').toLowerCase().includes(s) || (d.planter_code ||
                                '').toLowerCase().includes(s));
                        }
                        // Only sort if user has clicked a column header
                        if (this.molassesSortField) {
                            data.sort((a, b) => {
                                let valA = a[this.molassesSortField] ?? '',
                                    valB = b[this.molassesSortField] ?? '';
                                if (typeof valA === 'string') valA = valA.toLowerCase();
                                if (typeof valB === 'string') valB = valB.toLowerCase();
                                if (!isNaN(valA) && valA !== '') valA = parseFloat(valA);
                                if (!isNaN(valB) && valB !== '') valB = parseFloat(valB);
                                if (valA < valB) return this.molassesSortDir === 'asc' ? -1 : 1;
                                if (valA > valB) return this.molassesSortDir === 'asc' ? 1 : -1;
                                return 0;
                            });
                        }
                        return data;
                    },
                    get paginatedMolasses() {
                        const start = (this.molassesCurrentPage - 1) * this.perPage;
                        return this.filteredMolasses.slice(start, start + this.perPage);
                    },
                    get molassesTotalPages() {
                        return Math.ceil(this.filteredMolasses.length / this.perPage);
                    },
                    get isAllMolassesSelected() {
                        return this.paginatedMolasses.length > 0 && this.paginatedMolasses.every(row => this
                            .molassesSelectedIds.includes(row.id));
                    },
                    toggleSelectAllMolasses() {
                        if (this.isAllMolassesSelected) {
                            const pageIds = this.paginatedMolasses.map(r => r.id);
                            this.molassesSelectedIds = this.molassesSelectedIds.filter(id => !pageIds.includes(id));
                        } else {
                            const pageIds = this.paginatedMolasses.map(r => r.id);
                            this.molassesSelectedIds = [...new Set([...this.molassesSelectedIds, ...pageIds])];
                        }
                    },

                    sortQuedan(field) {
                        if (this.quedanSortField === field) {
                            this.quedanSortDir = this.quedanSortDir === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.quedanSortField = field;
                            this.quedanSortDir = 'asc';
                        }
                    },
                    sortMolasses(field) {
                        if (this.molassesSortField === field) {
                            this.molassesSortDir = this.molassesSortDir === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.molassesSortField = field;
                            this.molassesSortDir = 'asc';
                        }
                    },

                    // Delete functions
                    async confirmDeleteAll() {
                        const result = await Swal.fire({
                            title: 'Delete ALL Records?',
                            text: `This will delete ${this.totalQuedanCount} quedan AND ${this.totalMolassesCount} molasses records. This cannot be undone!`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete ALL',
                            confirmButtonColor: '#dc2626',
                            cancelButtonText: 'Cancel'
                        });

                        if (!result.isConfirmed) return;

                        let errors = [];

                        // Delete all quedans
                        try {
                            const quedanRes = await fetch('{{ route('registry.quedan.delete-all') }}', {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const quedanData = await quedanRes.json();
                            if (!quedanRes.ok) errors.push('Quedan: ' + quedanData.message);
                        } catch (e) {
                            errors.push('Quedan: ' + e.message);
                        }

                        // Delete all molasses
                        try {
                            const molassesRes = await fetch('{{ route('registry.molasses.delete-all') }}', {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const molassesData = await molassesRes.json();
                            if (!molassesRes.ok) errors.push('Molasses: ' + molassesData.message);
                        } catch (e) {
                            errors.push('Molasses: ' + e.message);
                        }

                        if (errors.length > 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Errors occurred',
                                text: errors.join('\n')
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'All quedan and molasses records deleted.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                            this.showDeleteModal = false;
                            setTimeout(() => location.reload(), 1500);
                        }
                    },

                    async confirmDeleteByType(type, scope) {
                        let title, text, route, body = {};
                        const label = type === 'quedan' ? 'Quedan' : 'Molasses';

                        if (scope === 'all') {
                            title = `Delete ALL ${label} Records?`;
                            text = `This will delete all ${label} records. This cannot be undone!`;
                            route = type === 'quedan' ? '{{ route('registry.quedan.delete-all') }}' :
                                '{{ route('registry.molasses.delete-all') }}';
                        } else {
                            const form = type === 'quedan' ? this.quedanDeleteForm : this.molassesDeleteForm;
                            if (!form.crop_year || !form.week_no) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Required',
                                    text: 'Please select both crop year and week number.'
                                });
                                return;
                            }
                            title = `Delete ${label} Records?`;
                            text = `Delete ${label} records for ${form.crop_year} Week ${form.week_no}?`;
                            route = type === 'quedan' ? '{{ route('registry.quedan.delete-by-week') }}' :
                                '{{ route('registry.molasses.delete-by-week') }}';
                            body = {
                                crop_year: form.crop_year,
                                week_no: form.week_no
                            };
                        }

                        const result = await Swal.fire({
                            title: title,
                            text: text,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete',
                            confirmButtonColor: '#dc2626',
                            cancelButtonText: 'Cancel'
                        });

                        if (!result.isConfirmed) return;

                        try {
                            const response = await fetch(route, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: scope === 'week' ? JSON.stringify(body) : undefined
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
                            this.resetDeleteForms();
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

                    async confirmDeleteSelected(type) {
                        const ids = type === 'quedan' ? this.quedanSelectedIds : this.molassesSelectedIds;
                        if (ids.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Selection',
                                text: 'Please select records to delete.'
                            });
                            return;
                        }

                        const label = type === 'quedan' ? 'Quedan' : 'Molasses';
                        const route = type === 'quedan' ? '{{ route('registry.quedan.delete-selected') }}' :
                            '{{ route('registry.molasses.delete-selected') }}';

                        const result = await Swal.fire({
                            title: 'Delete Selected Records?',
                            text: `Delete ${ids.length} ${label} record(s)?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete',
                            confirmButtonColor: '#dc2626'
                        });

                        if (!result.isConfirmed) return;

                        try {
                            const response = await fetch(route, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    ids: ids
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
                            if (type === 'quedan') this.quedanSelectedIds = [];
                            else this.molassesSelectedIds = [];
                            setTimeout(() => location.reload(), 1500);
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message
                            });
                        }
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
