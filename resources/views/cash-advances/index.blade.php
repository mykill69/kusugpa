@extends('layouts.main')

@section('title', 'Cash Advances Management')

@section('content')
    <div x-data="cashAdvancesData()" x-init="init()" class="space-y-6">
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                        <h1 class="text-2xl sm:text-3xl font-bold">Cash Advances</h1>
                    </div>
                    <p class="text-primary-100 text-sm">Manage planter cash advances and amortizations</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    @if (auth()->user()->role === 'Administrator' ||
                            auth()->user()->role === 'super_admin' ||
                            auth()->user()->role === 'manager' ||
                            auth()->user()->hasPermission('create-cash-advances'))
                        {{-- The button dispatches an event --}}
                        <button @click="$dispatch('open-ca-modal')"
                            class="bg-white text-primary-700 rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-primary-50 transition">
                            <i class="fas fa-plus-circle mr-2"></i> New Cash Advance
                        </button>
                    @endif
                    @if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager']) ||
                            auth()->user()->hasPermission('manage-cash-advance-settings'))
                        <a href="{{ route('cash-advances.settings') }}"
                            class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-white/30 transition">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-green-100 rounded-lg p-2"><i class="fas fa-check-circle text-green-600"></i></div>
                    <span class="text-xs text-gray-500">Active</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_active'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-yellow-100 rounded-lg p-2"><i class="fas fa-clock text-yellow-600"></i></div>
                    <span class="text-xs text-gray-500">Pending</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pending'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-blue-100 rounded-lg p-2"><i class="fas fa-peso-sign text-blue-600"></i></div>
                    <span class="text-xs text-gray-500">Total Amount</span>
                </div>
                <p class="text-xl font-bold text-gray-900">₱{{ number_format($stats['total_amount'], 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-purple-100 rounded-lg p-2"><i class="fas fa-coins text-purple-600"></i></div>
                    <span class="text-xs text-gray-500">Collected</span>
                </div>
                <p class="text-xl font-bold text-gray-900">₱{{ number_format($stats['total_collected'], 0) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" x-model="search" @input="filterCA()"
                            placeholder="Search by name, code, or CA number..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <div>
                    <select x-model="statusFilter" @change="filterCA()"
                        class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <select x-model="cropYearFilter" @change="filterCA()"
                        class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm">
                        <option value="">All Years</option>
                        @foreach ($cropYears as $cy)
                            <option value="{{ $cy }}">{{ $cy }}</option>
                        @endforeach
                    </select>
                </div>
                <button @click="clearFilters()" class="text-gray-500 hover:text-gray-700 text-sm py-2">
                    <i class="fas fa-times mr-1"></i> Clear
                </button>
                <span class="text-xs text-gray-400 ml-auto" x-text="'Showing ' + filteredCA.length + ' records'"></span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('ca_number')">CA #</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('planter_name')">Planter</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('amount')">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Balance</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('application_date')">Date</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="ca in paginatedCA" :key="ca.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900" x-text="ca.ca_number">
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900" x-text="ca.planter_name"></p>
                                    <p class="text-xs text-gray-500" x-text="ca.planter_code"></p>
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold"
                                    x-text="'₱' + formatNumber(ca.amount)"></td>
                                <td class="px-4 py-3 text-sm text-right"
                                    :class="ca.balance > 0 ? 'text-red-600' : 'text-green-600'"
                                    x-text="'₱' + formatNumber(ca.balance)"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                        :class="statusClass(ca.status)"
                                        x-text="ca.status.charAt(0).toUpperCase() + ca.status.slice(1)"></span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500" x-text="formatDate(ca.application_date)"></td>
                                <td class="px-4 py-3 text-center">
                                    <a :href="'/cash-advances/' + ca.id"
                                        class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredCA.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-200 mb-3 block"></i>
                                No cash advance records found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between" x-show="totalPages > 1">
                <button @click="currentPage--" :disabled="currentPage === 1"
                    class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Previous</button>
                <span class="text-sm text-gray-500" x-text="'Page ' + currentPage + ' of ' + totalPages"></span>
                <button @click="currentPage++" :disabled="currentPage === totalPages"
                    class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Next</button>
            </div>
        </div>

         @include('modal.cashAdvanceModal')

    </div>

    @push('scripts')
        <script>
            function cashAdvancesData() {
                return {
                    search: '',
                    statusFilter: 'all',
                    cropYearFilter: '',
                    sortField: 'application_date',
                    sortDirection: 'desc',
                    currentPage: 1,
                    perPage: 15,
                    allCA: @json($cashAdvances->items()),
                    showModal: false,

                    init() {
                        this.filterCA();
                    },

                    get filteredCA() {
                        let data = [...this.allCA];
                        if (this.search) {
                            const s = this.search.toLowerCase();
                            data = data.filter(d => d.ca_number?.toLowerCase().includes(s) || d.planter_name?.toLowerCase()
                                .includes(s) || d.planter_code?.toLowerCase().includes(s));
                        }
                        if (this.statusFilter !== 'all') data = data.filter(d => d.status === this.statusFilter);
                        if (this.cropYearFilter) data = data.filter(d => d.crop_year === this.cropYearFilter);
                        data.sort((a, b) => {
                            let valA = a[this.sortField] ?? '',
                                valB = b[this.sortField] ?? '';
                            if (typeof valA === 'string') valA = valA.toLowerCase();
                            if (typeof valB === 'string') valB = valB.toLowerCase();
                            if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                            if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                            return 0;
                        });
                        return data;
                    },
                    get paginatedCA() {
                        const start = (this.currentPage - 1) * this.perPage;
                        return this.filteredCA.slice(start, start + this.perPage);
                    },
                    get totalPages() {
                        return Math.ceil(this.filteredCA.length / this.perPage);
                    },
                    filterCA() {
                        this.currentPage = 1;
                    },
                    sortBy(field) {
                        if (this.sortField === field) {
                            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.sortField = field;
                            this.sortDirection = 'asc';
                        }
                        this.filterCA();
                    },
                    clearFilters() {
                        this.search = '';
                        this.statusFilter = 'all';
                        this.cropYearFilter = '';
                        this.filterCA();
                    },
                    statusClass(status) {
                        const classes = {
                            active: 'bg-green-100 text-green-700',
                            pending: 'bg-yellow-100 text-yellow-700',
                            approved: 'bg-blue-100 text-blue-700',
                            completed: 'bg-gray-100 text-gray-700',
                            rejected: 'bg-red-100 text-red-700'
                        };
                        return classes[status] || 'bg-gray-100 text-gray-700';
                    },
                    formatNumber(num) {
                        return parseFloat(num || 0).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    },
                    formatDate(date) {
                        if (!date) return '';
                        return new Date(date).toLocaleDateString('en-US', {
                            month: 'short',
                            day: '2-digit',
                            year: 'numeric'
                        });
                    }
                };
            }

            function newCAForm() {
                return {
                    showModal: false,
                    selectedPlanter: '',
                    planterName: '',
                    amount: '',
                    interestRate: {{ $settings['default_interest'] ?? 3 }},
                    termMonths: 3,
                    purpose: '',
                    submitting: false,

                    updatePlanterName() {
                        const select = document.querySelector('select[name="planter_code"]');
                        const option = select?.options[select.selectedIndex];
                        this.planterName = option?.getAttribute('data-name') || '';
                    },
                    calculateMonthly() {
                        const p = parseFloat(this.amount) || 0;
                        const r = (parseFloat(this.interestRate) || 0) / 100 / 12;
                        const n = parseInt(this.termMonths) || 1;
                        if (p === 0) return 0;
                        if (r === 0) return p / n;
                        return p * (r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
                    },
                    calculateTotal() {
                        return this.calculateMonthly() * (parseInt(this.termMonths) || 1);
                    },
                    calculateTotalInterest() {
                        return this.calculateTotal() - (parseFloat(this.amount) || 0);
                    },
                    formatNumber(num) {
                        return parseFloat(num || 0).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    },

                    async submitCAForm() {
                        this.submitting = true;
                        try {
                            const formData = new FormData();
                            formData.append('planter_code', document.querySelector('select[name="planter_code"]').value);
                            formData.append('planter_name', this.planterName);
                            formData.append('amount', this.amount);
                            formData.append('interest_rate', this.interestRate);
                            formData.append('term_months', this.termMonths);
                            formData.append('crop_year', document.querySelector('select[name="crop_year"]').value);
                            formData.append('purpose', this.purpose);
                            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                            const response = await fetch('{{ route('cash-advances.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: formData
                            });
                            const data = await response.json();
                            if (!response.ok) throw new Error(data.message || 'Failed to create cash advance');

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Cash advance application submitted successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            this.$parent.showModal = false; // Close parent's modal
                            this.resetForm();
                            location.reload();
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Something went wrong'
                            });
                        } finally {
                            this.submitting = false;
                        }
                    },
                    resetForm() {
                        this.selectedPlanter = '';
                        this.planterName = '';
                        this.amount = '';
                        this.interestRate = {{ $settings['default_interest'] ?? 3 }};
                        this.termMonths = 3;
                        this.purpose = '';
                    }
                };
            }
        </script>
    @endpush
@endsection
