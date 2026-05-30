<div x-data="newCAForm()" x-show="showModal" x-on:open-ca-modal.window="showModal = true"
    x-on:close-ca-modal.window="showModal = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">New Cash Advance</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 p-2">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form @submit.prevent="submitCAForm()" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Planter</label>
                        <select name="planter_code" x-model="selectedPlanter" @change="updatePlanterName()" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            <option value="">Select Planter</option>
                            @foreach ($planters as $p)
                                <option value="{{ $p->planter_code }}" data-name="{{ $p->planter_name }}">
                                    {{ $p->planter_name }} ({{ $p->planter_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₱)</label>
                        <input type="number" name="amount" x-model="amount" step="0.01" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500"
                            placeholder="Enter amount">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interest Rate (%)</label>
                        <input type="number" name="interest_rate" x-model="interestRate" step="0.01" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Term (Months)</label>
                        <input type="number" name="term_months" x-model="termMonths" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Crop Year</label>
                        <select name="crop_year" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            @foreach ($cropYears as $cy)
                                <option value="{{ $cy }}">{{ $cy }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Planter Name</label>
                    <input type="text" name="planter_name" x-model="planterName" readonly
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-700">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <button type="button" @click="purpose = 'Emergency cash assistance'"
                            class="px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition">Emergency</button>
                        <button type="button" @click="purpose = 'Personal financial needs'"
                            class="px-3 py-1.5 text-xs bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">Personal</button>
                        <button type="button" @click="purpose = 'Medical expenses'"
                            class="px-3 py-1.5 text-xs bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition">Medical</button>
                        <button type="button" @click="purpose = 'Education expenses'"
                            class="px-3 py-1.5 text-xs bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition">Education</button>
                        <button type="button" @click="purpose = ''"
                            class="px-3 py-1.5 text-xs bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-times mr-1"></i> Clear
                        </button>
                    </div>
                    <textarea name="purpose" x-model="purpose" rows="2"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500"
                        placeholder="Enter purpose or select from presets above..."></textarea>
                </div>

                <div class="bg-gray-50 rounded-xl p-4" x-show="amount > 0 && interestRate > 0 && termMonths > 0">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Estimated Summary</h4>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div>
                            <p class="text-xs text-gray-500">Monthly Payment</p>
                            <p class="text-sm font-bold text-gray-900">₱<span
                                    x-text="formatNumber(calculateMonthly())"></span></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Interest</p>
                            <p class="text-sm font-bold text-amber-600">₱<span
                                    x-text="formatNumber(calculateTotalInterest())"></span></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Amount</p>
                            <p class="text-sm font-bold text-primary-700">₱<span
                                    x-text="formatNumber(calculateTotal())"></span></p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showModal = false; resetForm()"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" :disabled="submitting"
                        class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!submitting"><i class="fas fa-save mr-1"></i> Submit Application</span>
                        <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-1"></i> Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
                        text: 'Cash advance created successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    this.showModal = false;
                    this.resetForm();
                    window.location.reload();
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
