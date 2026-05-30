<!-- New Loan Modal -->
<div x-show="showNewLoanModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-data="newLoanForm()">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" @click="showNewLoanModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">New Loan Application</h3>
                <button @click="showNewLoanModal = false" class="text-gray-400 hover:text-gray-600 p-2">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form id="newLoanForm" @submit.prevent="submitLoanForm()" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Planter Selection -->
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

                    <!-- Loan Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loan Type</label>
                        <select name="loan_type_id" x-model="selectedLoanType" @change="updateLoanDetails()" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            <option value="">Select Type</option>
                            @foreach ($loanTypes as $type)
                                <option value="{{ $type->id }}" data-rate="{{ $type->default_interest_rate }}"
                                    data-term="{{ $type->default_term_months }}" data-max="{{ $type->max_amount }}">
                                    {{ $type->name }} ({{ $type->default_interest_rate }}% -
                                    {{ $type->default_term_months }} mos)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Principal Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Principal Amount (₱)</label>
                        <input type="number" name="principal_amount" x-model="principalAmount" step="0.01" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500"
                            placeholder="Enter loan amount">
                        <p class="text-xs text-gray-400 mt-1" x-show="maxAmount > 0">
                            Max: ₱<span x-text="formatNumber(maxAmount)"></span>
                        </p>
                    </div>

                    <!-- Interest Rate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interest Rate (%)</label>
                        <input type="number" name="interest_rate" x-model="interestRate" step="0.01" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>

                    <!-- Term -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Term (Months)</label>
                        <input type="number" name="term_months" x-model="termMonths" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>

                    <!-- Crop Year -->
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

                <!-- Planter Name (Auto-filled) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Planter Name</label>
                    <input type="text" name="planter_name" x-model="planterName" readonly
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-700">
                </div>

                <!-- Purpose with Default Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <button type="button"
                            @click="purpose = 'Crop production expenses (fertilizers, pesticides, labor)'"
                            class="px-3 py-1.5 text-xs bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100 transition">
                            Crop Production
                        </button>
                        <button type="button" @click="purpose = 'Emergency financial assistance'"
                            class="px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition">
                            Emergency
                        </button>
                        <button type="button" @click="purpose = 'Purchase of farming equipment and machinery'"
                            class="px-3 py-1.5 text-xs bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                            Equipment
                        </button>
                        <button type="button" @click="purpose = 'Farm land improvement and development'"
                            class="px-3 py-1.5 text-xs bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100 transition">
                            Farm Improvement
                        </button>
                        <button type="button" @click="purpose = ''"
                            class="px-3 py-1.5 text-xs bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-times mr-1"></i> Clear
                        </button>
                    </div>
                    <textarea name="purpose" x-model="purpose" rows="2"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500"
                        placeholder="Enter loan purpose or select from presets above..."></textarea>
                </div>

                <!-- Estimated Summary -->
                <div class="bg-gray-50 rounded-xl p-4"
                    x-show="principalAmount > 0 && interestRate > 0 && termMonths > 0">
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

                <!-- === DOCUMENT ATTACHMENT SECTION === -->
                <div class="border-t border-gray-100 pt-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-paperclip text-amber-600 text-sm"></i>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900">Proof of Identity / Supporting Documents</h4>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-3">
                        <p class="text-xs text-amber-700 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>Upload proof that the planter is applying for this loan. This can be a signed
                                agreement, valid ID, or any document that verifies the planter's identity. Accepted
                                formats: PDF, JPG, PNG, DOC (Max: 10MB)</span>
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Document
                                    Type *</label>
                                <select id="docType"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                    <option value="proof">Proof of Identity</option>
                                    <option value="agreement">Signed Agreement</option>
                                    <option value="id">Valid Government ID</option>
                                    <option value="other">Other Supporting Document</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Description</label>
                                <input type="text" id="docDesc" placeholder="e.g., Barangay Clearance, SSS ID"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Select
                                File *</label>
                            <div class="relative">
                                <input type="file" id="loanDocument" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                    required @change="previewFile($event)"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                            </div>
                        </div>

                        <!-- File Preview -->
                        <div id="filePreview" class="hidden p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center">
                                        <i class="fas fa-file text-primary-600"></i>
                                    </div>
                                    <div>
                                        <p id="previewFileName" class="text-sm font-medium text-gray-900"></p>
                                        <p id="previewFileSize" class="text-xs text-gray-500"></p>
                                    </div>
                                </div>
                                <button type="button" @click="removeFile()"
                                    class="text-red-400 hover:text-red-600 p-2">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showNewLoanModal = false; resetForm()"
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
