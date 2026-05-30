@extends('layouts.main')

@section('title', 'CA #' . $cashAdvance->ca_number)

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('cash-advances.index') }}" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">CA #{{ $cashAdvance->ca_number }}</h1>
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                        @if ($cashAdvance->status == 'active') bg-green-100 text-green-700
                        @elseif($cashAdvance->status == 'pending') bg-yellow-100 text-yellow-700
                        @elseif($cashAdvance->status == 'approved') bg-blue-100 text-blue-700
                        @elseif($cashAdvance->status == 'completed') bg-gray-100 text-gray-700
                        @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($cashAdvance->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">{{ $cashAdvance->planter_name }} ({{ $cashAdvance->planter_code }})</p>
                </div>
                @php
                    $isAdmin = in_array(auth()->user()->role, ['Administrator', 'super_admin']);
                    $isManager = auth()->user()->role === 'manager';
                    $canApprove = $isAdmin || $isManager;
                    $canProcess = $isAdmin || $isManager || auth()->user()->hasPermission('create-cash-advances') || auth()->user()->hasPermission('process-cash-advance-payments');
                @endphp

                <div class="flex gap-2">
                    @if ($canApprove && $cashAdvance->status === 'pending')
                        <button onclick="approveCA()" class="bg-green-600 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-green-700">
                            <i class="fas fa-check mr-1"></i> Approve
                        </button>
                        <button onclick="rejectCA()" class="bg-red-600 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-red-700">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                    @endif
                    @if ($canApprove && $cashAdvance->status === 'approved')
                        <form action="{{ route('cash-advances.activate', $cashAdvance) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-600 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-blue-700">
                                <i class="fas fa-play mr-1"></i> Activate
                            </button>
                        </form>
                    @endif
                    @if ($canProcess && in_array($cashAdvance->status, ['pending', 'rejected']))
                        <form action="{{ route('cash-advances.destroy', $cashAdvance) }}" method="POST" onsubmit="return confirm('Delete this cash advance?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-gray-600 text-white rounded-xl px-4 py-2 text-sm font-semibold hover:bg-gray-700">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Cash Advance Details</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div><p class="text-xs text-gray-500">Amount</p><p class="text-sm font-semibold">₱{{ number_format($cashAdvance->amount, 2) }}</p></div>
                        <div><p class="text-xs text-gray-500">Interest Rate</p><p class="text-sm font-semibold">{{ $cashAdvance->interest_rate }}%</p></div>
                        <div><p class="text-xs text-gray-500">Term</p><p class="text-sm font-semibold">{{ $cashAdvance->term_months }} months</p></div>
                        <div><p class="text-xs text-gray-500">Monthly</p><p class="text-sm font-semibold">₱{{ number_format($cashAdvance->monthly_amortization, 2) }}</p></div>
                        <div><p class="text-xs text-gray-500">Total Amount</p><p class="text-sm font-semibold">₱{{ number_format($cashAdvance->total_amount, 2) }}</p></div>
                        <div><p class="text-xs text-gray-500">Balance</p><p class="text-sm font-semibold text-red-600">₱{{ number_format($cashAdvance->balance, 2) }}</p></div>
                        <div><p class="text-xs text-gray-500">Crop Year</p><p class="text-sm font-semibold">{{ $cashAdvance->crop_year }}</p></div>
                        <div><p class="text-xs text-gray-500">Start Date</p><p class="text-sm">{{ $cashAdvance->start_date ? $cashAdvance->start_date->format('M d, Y') : 'N/A' }}</p></div>
                    </div>
                    @if ($cashAdvance->purpose)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-500">Purpose</p><p class="text-sm">{{ $cashAdvance->purpose }}</p>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Amortization Schedule</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500">#</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500">Due Date</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 text-right">Amount Due</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 text-right">Paid</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 text-right">Balance</th>
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500 text-center">Status</th>
                                    @if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager', 'loan_officer']))
                                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 text-center">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($cashAdvance->amortizations as $amort)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-3 py-2 text-sm">{{ $amort->payment_number }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $amort->due_date->format('M d, Y') }}</td>
                                        <td class="px-3 py-2 text-sm text-right">₱{{ number_format($amort->amount_due, 2) }}</td>
                                        <td class="px-3 py-2 text-sm text-right text-green-600">₱{{ number_format($amort->amount_paid, 2) }}</td>
                                        <td class="px-3 py-2 text-sm text-right">₱{{ number_format($amort->balance_after, 2) }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                            @if ($amort->status == 'paid') bg-green-100 text-green-700
                                            @elseif($amort->status == 'partial') bg-blue-100 text-blue-700
                                            @elseif($amort->status == 'overdue') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-600 @endif">
                                                {{ ucfirst($amort->status) }}
                                            </span>
                                        </td>
                                        @if (in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager', 'loan_officer']) && $amort->status != 'paid')
                                            <td class="px-3 py-2 text-center">
                                                <button onclick="recordPayment({{ $amort->id }}, {{ $amort->amount_due }})" class="text-primary-600 hover:text-primary-700 text-xs font-medium">Pay</button>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No amortization schedule yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-sm text-gray-500">Applied</span><span class="text-sm font-semibold">{{ $cashAdvance->application_date->format('M d, Y') }}</span></div>
                        @if ($cashAdvance->approved_date)
                            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-sm text-gray-500">Approved</span><span class="text-sm font-semibold">{{ $cashAdvance->approved_date->format('M d, Y') }}</span></div>
                        @endif
                        @if ($cashAdvance->approvedByUser)
                            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-sm text-gray-500">Approved By</span><span class="text-sm font-semibold">{{ $cashAdvance->approvedByUser->fname }} {{ $cashAdvance->approvedByUser->lname }}</span></div>
                        @endif
                        <div class="flex justify-between py-2"><span class="text-sm text-gray-500">Created By</span><span class="text-sm font-semibold">{{ $cashAdvance->createdByUser->fname }} {{ $cashAdvance->createdByUser->lname }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentModal" style="display:none;" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Record Payment</h3>
            <form id="paymentForm" method="POST">
                @csrf
                <input type="hidden" name="amortization_id" id="amortizationId">
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount</label><input type="number" name="amount_paid" id="paymentAmount" step="0.01" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Week Number</label><input type="text" name="week_no" placeholder="e.g., 22" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm"></div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold">Save</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function approveCA() {
                Swal.fire({
                    title: 'Approve Cash Advance?',
                    html: '<input type="date" id="startDate" class="swal2-input" placeholder="Start Date">',
                    showCancelButton: true, confirmButtonText: 'Approve',
                    preConfirm: () => {
                        const startDate = document.getElementById('startDate').value;
                        if (!startDate) { Swal.showValidationMessage('Please select start date'); return false; }
                        const form = document.createElement('form');
                        form.method = 'POST'; form.action = '{{ route('cash-advances.approve', $cashAdvance) }}';
                        form.innerHTML = '@csrf<input type="hidden" name="start_date" value="' + startDate + '">';
                        document.body.appendChild(form); form.submit();
                    }
                });
            }

            function rejectCA() {
                Swal.fire({
                    title: 'Reject Cash Advance?', input: 'text', inputLabel: 'Remarks', inputPlaceholder: 'Reason for rejection',
                    showCancelButton: true, confirmButtonText: 'Reject', confirmButtonColor: '#dc2626'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST'; form.action = '{{ route('cash-advances.reject', $cashAdvance) }}';
                        form.innerHTML = '@csrf<input type="hidden" name="remarks" value="' + (result.value || '') + '">';
                        document.body.appendChild(form); form.submit();
                    }
                });
            }

            function recordPayment(amortizationId, amountDue) {
                document.getElementById('amortizationId').value = amortizationId;
                document.getElementById('paymentAmount').value = amountDue;
                document.getElementById('paymentForm').action = '{{ route('cash-advances.payment', $cashAdvance) }}';
                document.getElementById('paymentModal').style.display = 'flex';
            }

            function closePaymentModal() { document.getElementById('paymentModal').style.display = 'none'; }
        </script>
    @endpush
@endsection