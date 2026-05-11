<!-- resources/views/loans/index.blade.php -->
@extends('layouts.main')

@section('title', 'Loans Management')

@section('content')
    <div x-data="loansData()" x-init="init()" class="space-y-6">
        <!-- Page Header -->
        <div
            class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-hand-holding-usd text-2xl"></i>
                        <h1 class="text-2xl sm:text-3xl font-bold">Loans Management</h1>
                    </div>
                    <p class="text-primary-100 text-sm">Manage planter loans and amortizations</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    @if (auth()->user()->role === 'Administrator' ||
                            auth()->user()->role === 'super_admin' ||
                            auth()->user()->role === 'manager' ||
                            auth()->user()->hasPermission('create-loans'))
                        <button @click="openNewLoanModal()"
                            class="bg-white text-primary-700 rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-primary-50 transition">
                            <i class="fas fa-plus-circle mr-2"></i> New Loan
                        </button>
                    @endif
                    @if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager']) ||
                            auth()->user()->hasPermission('manage-loan-settings'))
                        <a href="{{ route('loans.settings') }}"
                            class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-white/30 transition">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-green-100 rounded-lg p-2"><i class="fas fa-check-circle text-green-600"></i></div><span
                        class="text-xs text-gray-500">Active</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_active'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-yellow-100 rounded-lg p-2"><i class="fas fa-clock text-yellow-600"></i></div><span
                        class="text-xs text-gray-500">Pending</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pending'] }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-blue-100 rounded-lg p-2"><i class="fas fa-peso-sign text-blue-600"></i></div><span
                        class="text-xs text-gray-500">Total Amount</span>
                </div>
                <p class="text-xl font-bold text-gray-900">₱{{ number_format($stats['total_amount'], 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-purple-100 rounded-lg p-2"><i class="fas fa-coins text-purple-600"></i></div><span
                        class="text-xs text-gray-500">Collected</span>
                </div>
                <p class="text-xl font-bold text-gray-900">₱{{ number_format($stats['total_collected'], 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-red-100 rounded-lg p-2"><i class="fas fa-exclamation-triangle text-red-600"></i></div>
                    <span class="text-xs text-gray-500">Overdue</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['overdue_payments'] }}</p>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" x-model="search" @input="filterLoans()"
                            placeholder="Search by name, code, or loan number..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <div>
                    <select x-model="statusFilter" @change="filterLoans()"
                        class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <select x-model="cropYearFilter" @change="filterLoans()"
                        class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="">All Years</option>
                        @foreach ($cropYears as $cy)
                            <option value="{{ $cy }}">{{ $cy }}</option>
                        @endforeach
                    </select>
                </div>
                <button @click="clearFilters()" class="text-gray-500 hover:text-gray-700 text-sm py-2">
                    <i class="fas fa-times mr-1"></i> Clear
                </button>
                <span class="text-xs text-gray-400 ml-auto" x-text="'Showing ' + filteredLoans.length + ' loans'"></span>
            </div>
        </div>

        <!-- Loans Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100" id="loansTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('loan_number')">
                                Loan # <i class="fas fa-sort text-gray-300 ml-1"></i>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('planter_name')">
                                Planter <i class="fas fa-sort text-gray-300 ml-1"></i>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('principal_amount')">
                                Amount <i class="fas fa-sort text-gray-300 ml-1"></i>
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Balance</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                @click="sortBy('application_date')">
                                Date <i class="fas fa-sort text-gray-300 ml-1"></i>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Attachments</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="loan in paginatedLoans" :key="loan.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-900"
                                    x-text="loan.loan_number"></td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900" x-text="loan.planter_name"></p>
                                    <p class="text-xs text-gray-500" x-text="loan.planter_code"></p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700" x-text="loan.loan_type_name"></td>
                                <td class="px-4 py-3 text-sm text-right font-semibold"
                                    x-text="'₱' + formatNumber(loan.principal_amount)"></td>
                                <td class="px-4 py-3 text-sm text-right"
                                    :class="loan.balance > 0 ? 'text-red-600' : 'text-green-600'"
                                    x-text="'₱' + formatNumber(loan.balance)"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                        :class="statusClass(loan.status)"
                                        x-text="loan.status.charAt(0).toUpperCase() + loan.status.slice(1)"></span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500" x-text="formatDate(loan.application_date)">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="viewAttachments(loan)"
                                        class="relative inline-flex items-center justify-center p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors group"
                                        :title="'View Attachments'">
                                        <i class="fas fa-paperclip text-sm"></i>
                                        <span x-show="loan.attachments_count > 0"
                                            class="absolute -top-1 -right-1 bg-primary-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center"
                                            x-text="loan.attachments_count"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a :href="'/loans/' + loan.id"
                                        class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredLoans.length === 0">
                            <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-200 mb-3 block"></i>
                                No loan records found
                            </td>
                        </tr>
                    </tbody>
                </table>


            </div>
            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between" x-show="totalPages > 1">
                <button @click="currentPage--" :disabled="currentPage === 1"
                    class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Previous</button>
                <span class="text-sm text-gray-500" x-text="'Page ' + currentPage + ' of ' + totalPages"></span>
                <button @click="currentPage++" :disabled="currentPage === totalPages"
                    class="px-3 py-1.5 text-sm border rounded-lg disabled:opacity-50">Next</button>
            </div>
        </div>
        @include('modal/view-loan-attachment')
        @include('modal/loanModal')
    </div>

    @push('scripts')
        <script>
            // Main loans data component
            function loansData() {
                return {
                    search: '',
                    statusFilter: 'all',
                    cropYearFilter: '',
                    sortField: 'application_date',
                    sortDirection: 'desc',
                    currentPage: 1,
                    perPage: 15,
                    showNewLoanModal: false,
                    allLoans: @json($loans->items()),
                    showAttachmentsModal: false,
                    viewingLoan: null,
                    attachments: [],
                    loadingAttachments: false,
                    uploadingAttachment: false,

                    init() {
                        this.filterLoans();
                    },

                    get filteredLoans() {
                        let loans = [...this.allLoans];

                        if (this.search) {
                            const s = this.search.toLowerCase();
                            loans = loans.filter(l =>
                                l.loan_number?.toLowerCase().includes(s) ||
                                l.planter_name?.toLowerCase().includes(s) ||
                                l.planter_code?.toLowerCase().includes(s)
                            );
                        }

                        if (this.statusFilter !== 'all') {
                            loans = loans.filter(l => l.status === this.statusFilter);
                        }

                        if (this.cropYearFilter) {
                            loans = loans.filter(l => l.crop_year === this.cropYearFilter);
                        }

                        loans.sort((a, b) => {
                            let valA = a[this.sortField] ?? '';
                            let valB = b[this.sortField] ?? '';
                            if (typeof valA === 'string') valA = valA.toLowerCase();
                            if (typeof valB === 'string') valB = valB.toLowerCase();
                            if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                            if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                            return 0;
                        });

                        return loans;
                    },
                    async viewAttachments(loan) {
                        this.viewingLoan = loan;
                        this.showAttachmentsModal = true;
                        this.loadingAttachments = true;
                        this.attachments = [];

                        try {
                            const response = await fetch('/loans/' + loan.id + '/attachments', {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await response.json();
                            this.attachments = data.attachments || [];
                        } catch (error) {
                            console.error('Error loading attachments:', error);
                        } finally {
                            this.loadingAttachments = false;
                        }
                    },

                    async uploadNewAttachment() {
                        if (!this.viewingLoan) return;

                        const fileInput = document.getElementById('newAttachmentFile');
                        if (!fileInput.files.length) {
                            alert('Please select a file');
                            return;
                        }

                        this.uploadingAttachment = true;

                        try {
                            const formData = new FormData();
                            formData.append('document', fileInput.files[0]);
                            formData.append('document_type', document.getElementById('newDocType').value);
                            formData.append('description', document.getElementById('newDocDesc').value);
                            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                            const response = await fetch('/loans/' + this.viewingLoan.id + '/attachments', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: formData
                            });

                            if (!response.ok) throw new Error('Upload failed');

                            // Refresh attachments list
                            await this.viewAttachments(this.viewingLoan);

                            // Clear form
                            fileInput.value = '';
                            document.getElementById('newDocDesc').value = '';

                        } catch (error) {
                            alert('Failed to upload: ' + error.message);
                        } finally {
                            this.uploadingAttachment = false;
                        }
                    },

                    getFileIcon(mimeType) {
                        if (mimeType?.includes('pdf')) return 'fas fa-file-pdf text-red-500';
                        if (mimeType?.includes('image')) return 'fas fa-file-image text-blue-500';
                        if (mimeType?.includes('word') || mimeType?.includes('document'))
                        return 'fas fa-file-word text-blue-600';
                        return 'fas fa-file text-gray-500';
                    },

                    getFileBgColor(mimeType) {
                        if (mimeType?.includes('pdf')) return 'bg-red-100';
                        if (mimeType?.includes('image')) return 'bg-blue-100';
                        if (mimeType?.includes('word') || mimeType?.includes('document')) return 'bg-blue-50';
                        return 'bg-gray-100';
                    },

                    get paginatedLoans() {
                        const start = (this.currentPage - 1) * this.perPage;
                        return this.filteredLoans.slice(start, start + this.perPage);
                    },

                    get totalPages() {
                        return Math.ceil(this.filteredLoans.length / this.perPage);
                    },

                    filterLoans() {
                        this.currentPage = 1;
                    },

                    sortBy(field) {
                        if (this.sortField === field) {
                            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.sortField = field;
                            this.sortDirection = 'asc';
                        }
                        this.filterLoans();
                    },

                    clearFilters() {
                        this.search = '';
                        this.statusFilter = 'all';
                        this.cropYearFilter = '';
                        this.filterLoans();
                    },

                    openNewLoanModal() {
                        this.showNewLoanModal = true;
                    },

                    statusClass(status) {
                        const classes = {
                            active: 'bg-green-100 text-green-700',
                            pending: 'bg-yellow-100 text-yellow-700',
                            approved: 'bg-blue-100 text-blue-700',
                            completed: 'bg-gray-100 text-gray-700',
                            rejected: 'bg-red-100 text-red-700',
                            cancelled: 'bg-red-100 text-red-700',
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

            // Separate new loan form component for the modal
            function newLoanForm() {
                return {
                    selectedPlanter: '',
                    planterName: '',
                    selectedLoanType: '',
                    principalAmount: '',
                    interestRate: 5,
                    termMonths: 12,
                    maxAmount: 0,
                    purpose: '',
                    submitting: false,

                    updatePlanterName() {
                        const select = document.querySelector('select[name="planter_code"]');
                        const option = select?.options[select.selectedIndex];
                        this.planterName = option?.getAttribute('data-name') || '';
                    },

                    updateLoanDetails() {
                        const select = document.querySelector('select[name="loan_type_id"]');
                        const option = select?.options[select.selectedIndex];
                        if (option && option.value) {
                            this.interestRate = parseFloat(option.getAttribute('data-rate')) || 5;
                            this.termMonths = parseInt(option.getAttribute('data-term')) || 12;
                            this.maxAmount = parseFloat(option.getAttribute('data-max')) || 0;
                        }
                    },

                    previewFile(event) {
                        const file = event.target.files[0];
                        if (!file) return;

                        document.getElementById('previewFileName').textContent = file.name;
                        document.getElementById('previewFileSize').textContent = this.formatFileSize(file.size);
                        document.getElementById('filePreview').classList.remove('hidden');
                    },

                    removeFile() {
                        document.getElementById('loanDocument').value = '';
                        document.getElementById('filePreview').classList.add('hidden');
                    },

                    formatFileSize(bytes) {
                        if (bytes === 0) return '0 B';
                        const units = ['B', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(1024));
                        return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + units[i];
                    },

                    calculateMonthly() {
                        const p = parseFloat(this.principalAmount) || 0;
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
                        return this.calculateTotal() - (parseFloat(this.principalAmount) || 0);
                    },

                    formatNumber(num) {
                        return parseFloat(num || 0).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    },

                    async submitLoanForm() {
                        this.submitting = true;

                        try {
                            const formData = new FormData();
                            formData.append('planter_code', document.querySelector('select[name="planter_code"]').value);
                            formData.append('planter_name', this.planterName);
                            formData.append('loan_type_id', document.querySelector('select[name="loan_type_id"]').value);
                            formData.append('principal_amount', this.principalAmount);
                            formData.append('interest_rate', this.interestRate);
                            formData.append('term_months', this.termMonths);
                            formData.append('crop_year', document.querySelector('select[name="crop_year"]').value);
                            formData.append('purpose', this.purpose);
                            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                            const loanResponse = await fetch('{{ route('loans.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: formData
                            });

                            const loanData = await loanResponse.json();

                            if (!loanResponse.ok) {
                                throw new Error(loanData.message || 'Failed to create loan');
                            }

                            // Upload attachment if file is selected
                            const fileInput = document.getElementById('loanDocument');
                            if (fileInput && fileInput.files.length > 0 && loanData.loan) {
                                const attachForm = new FormData();
                                attachForm.append('document', fileInput.files[0]);
                                attachForm.append('document_type', document.getElementById('docType').value);
                                attachForm.append('description', document.getElementById('docDesc').value);
                                attachForm.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                                await fetch('/loans/' + loanData.loan.id + '/attachments', {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: attachForm
                                });
                            }

                            this.showNewLoanModal = false;
                            this.resetForm();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Loan application submitted successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Refresh the loans list
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
                        this.selectedLoanType = '';
                        this.principalAmount = '';
                        this.interestRate = 5;
                        this.termMonths = 12;
                        this.maxAmount = 0;
                        this.purpose = '';
                        document.getElementById('loanDocument').value = '';
                        document.getElementById('filePreview').classList.add('hidden');
                    }
                };
            }
        </script>
    @endpush
@endsection
