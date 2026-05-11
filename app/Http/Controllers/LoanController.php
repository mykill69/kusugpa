<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\LoanAmortization;
use App\Models\LoanSetting;
use App\Models\Summary;
use App\Models\CropYear;
use App\Models\User;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Get the authenticated user with proper type hint.
     */
    private function user(): User
    {
        /** @var User */
        return auth()->user();
    }

    /**
     * Get current user's role.
     */
    private function currentRole(): string
    {
        return $this->user()->role ?? '';
    }

    /**
     * Check if user can approve/reject loans (Admin, Super Admin, Manager).
     */
    private function canApproveLoans(): bool
{
    $role = $this->currentRole();
    if (in_array($role, ['Administrator', 'super_admin'])) {
        return true;
    }
    if ($role === 'manager') {
        return true; // Manager always can approve
    }
    return $this->user()->hasPermission('approve-loans');
}

    /**
     * Check if user can process loans (Admin, Super Admin, Manager, Loan Officer).
     */
   private function canProcessLoans(): bool
{
    $role = $this->currentRole();
    if (in_array($role, ['Administrator', 'super_admin', 'manager'])) {
        return true;
    }
    return $this->user()->hasPermission('create-loans') || 
           $this->user()->hasPermission('process-loan-payments');
}

    /**
     * Check if user can manage loan settings (Admin, Super Admin, Manager).
     */
   private function canManageSettings(): bool
{
    $role = $this->currentRole();
    if (in_array($role, ['Administrator', 'super_admin', 'manager'])) {
        return true;
    }
    return $this->user()->hasPermission('manage-loan-settings');
}

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($this->currentRole() === 'Viewer') {
                if (!in_array($request->route()->getName(), ['loans.index', 'loans.show'])) {
                    return redirect()->route('loans.index')
                        ->with('error', 'You only have view access to loans.');
                }
            }
            return $next($request);
        });
    }

    public function index(Request $request)
{
    $query = Loan::with(['loanType', 'approvedByUser'])->withCount('attachments');

    if ($request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('loan_number', 'like', "%{$search}%")
              ->orWhere('planter_name', 'like', "%{$search}%")
              ->orWhere('planter_code', 'like', "%{$search}%");
        });
    }

    if ($request->status && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    if ($request->crop_year) {
        $query->where('crop_year', $request->crop_year);
    }

    $loans = $query->orderBy('created_at', 'desc')->paginate(20);

    // Add loan_type_name and attachments_count to each item
    $loans->getCollection()->transform(function ($loan) {
        $loan->loan_type_name = $loan->loanType->name ?? 'N/A';
        return $loan;
    });

    $loanTypes = LoanType::where('is_active', true)->get();
    $cropYears = CropYear::pluck('crop_year');
    $planters = Summary::select('planter_code', 'planter_name')
        ->distinct()
        ->orderBy('planter_name')
        ->get();
    $stats = $this->getLoanStats();

    $userPermissions = [
        'canCreate' => $this->canProcessLoans(),
        'canApprove' => $this->canApproveLoans(),
        'canManageSettings' => $this->canManageSettings(),
    ];

    return view('loans.index', compact('loans', 'loanTypes', 'cropYears', 'planters', 'stats', 'userPermissions'));
}

    public function create()
    {
        $loanTypes = LoanType::where('is_active', true)->get();
        $cropYears = CropYear::pluck('crop_year');
        $planters = Summary::select('planter_code', 'planter_name')
            ->distinct()
            ->orderBy('planter_name')
            ->get();
        $settings = [
            'default_interest' => LoanSetting::get('default_interest_rate', 5),
            'max_term' => LoanSetting::get('max_loan_term_months', 24),
            'min_amount' => LoanSetting::get('min_loan_amount', 1000),
            'max_amount' => LoanSetting::get('max_loan_amount', 100000),
        ];

        return view('loans.create', compact('loanTypes', 'cropYears', 'planters', 'settings'));
    }

    public function store(Request $request)
    {
        if (!$this->canProcessLoans()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'planter_code' => 'required|string',
            'planter_name' => 'required|string',
            'loan_type_id' => 'required|exists:loan_types,id',
            'principal_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_months' => 'required|integer|min:1|max:60',
            'crop_year' => 'required|string',
            'purpose' => 'nullable|string',
        ]);

        $loan = new Loan($request->all());
        $loan->loan_number = Loan::generateLoanNumber();
        $loan->application_date = now();
        $loan->status = 'pending';
        $loan->created_by = auth()->id();
        $loan->calculateAmortization();
        $loan->save();

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Loan application #' . $loan->loan_number . ' created successfully.',
                'loan' => $loan->load('loanType')
            ], 201);
        }

        return redirect()->route('loans.index')
            ->with('success', 'Loan application #' . $loan->loan_number . ' created successfully.');
    }

    public function show(Loan $loan)
    {
        $loan->load(['loanType', 'amortizations' => function($q) {
            $q->orderBy('payment_number');
        }, 'approvedByUser', 'createdByUser']);

        $userPermissions = [
            'canApprove' => $this->canApproveLoans() && $loan->status === 'pending',
            'canActivate' => $this->canApproveLoans() && $loan->status === 'approved',
            'canReject' => $this->canApproveLoans() && $loan->status === 'pending',
            'canRecordPayment' => $this->canProcessLoans() && $loan->status === 'active',
            'canDelete' => $this->canProcessLoans() && in_array($loan->status, ['pending', 'rejected']),
        ];

        return view('loans.show', compact('loan', 'userPermissions'));
    }

    public function approve(Request $request, Loan $loan)
    {
        if (!$this->canApproveLoans()) {
            abort(403, 'Unauthorized. Only managers and administrators can approve loans.');
        }

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loans can be approved.');
        }

        $request->validate(['start_date' => 'required|date']);

        $loan->status = 'approved';
        $loan->approved_by = auth()->id();
        $loan->approved_date = now();
        $loan->start_date = $request->start_date;
        $loan->end_date = Carbon::parse($request->start_date)->addMonths($loan->term_months);
        $loan->save();

        $this->generateAmortizationSchedule($loan);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan #' . $loan->loan_number . ' approved successfully.');
    }

    public function activate(Loan $loan)
    {
        if (!$this->canApproveLoans()) {
            abort(403, 'Unauthorized');
        }

        if ($loan->status !== 'approved') {
            return back()->with('error', 'Only approved loans can be activated.');
        }

        $loan->status = 'active';
        $loan->save();

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan #' . $loan->loan_number . ' is now active.');
    }

    public function reject(Request $request, Loan $loan)
    {
        if (!$this->canApproveLoans()) {
            abort(403, 'Unauthorized. Only managers and administrators can reject loans.');
        }

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loans can be rejected.');
        }

        $loan->status = 'rejected';
        $loan->remarks = $request->remarks;
        $loan->save();

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan #' . $loan->loan_number . ' rejected.');
    }

    public function recordPayment(Request $request, Loan $loan)
    {
        if (!$this->canProcessLoans()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'amortization_id' => 'required|exists:loan_amortizations,id',
            'amount_paid' => 'required|numeric|min:0',
            'week_no' => 'nullable|string',
        ]);

        $amortization = LoanAmortization::findOrFail($request->amortization_id);

        $amountPaid = $request->amount_paid;
        $interestPaid = min($amountPaid, $amortization->interest_paid);
        $principalPaid = $amountPaid - $interestPaid;

        $amortization->amount_paid = $amountPaid;
        $amortization->interest_paid = $interestPaid;
        $amortization->principal_paid = $principalPaid;
        $amortization->balance_after = $loan->balance - $amountPaid;
        $amortization->status = $amountPaid >= $amortization->amount_due ? 'paid' : 'partial';
        $amortization->paid_date = now();
        $amortization->week_no = $request->week_no;
        $amortization->save();

        $loan->balance = $loan->amortizations()->sum('balance_after');

        $pendingPayments = $loan->amortizations()->whereIn('status', ['pending', 'partial', 'overdue'])->count();
        if ($pendingPayments === 0) {
            $loan->status = 'completed';
        }

        $loan->save();

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Payment recorded successfully.');
    }

    public function destroy(Loan $loan)
    {
        if (!$this->canProcessLoans()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($loan->status, ['pending', 'rejected'])) {
            return back()->with('error', 'Only pending or rejected loans can be deleted.');
        }

        $loan->amortizations()->delete();
        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Loan deleted successfully.');
    }

    public function settings()
    {
        if (!$this->canManageSettings()) {
            abort(403, 'Unauthorized. Only managers and administrators can access loan settings.');
        }

        $settings = [
            'default_interest_rate' => LoanSetting::get('default_interest_rate', 5),
            'max_loan_term_months' => LoanSetting::get('max_loan_term_months', 24),
            'min_loan_amount' => LoanSetting::get('min_loan_amount', 1000),
            'max_loan_amount' => LoanSetting::get('max_loan_amount', 100000),
            'auto_deduct' => LoanSetting::get('auto_deduct', true),
        ];

        $loanTypes = LoanType::all();

        return view('loans.settings', compact('settings', 'loanTypes'));
    }

    public function updateSettings(Request $request)
    {
        if (!$this->canManageSettings()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'default_interest_rate' => 'required|numeric|min:0|max:100',
            'max_loan_term_months' => 'required|integer|min:1',
            'min_loan_amount' => 'required|numeric|min:0',
            'max_loan_amount' => 'required|numeric|min:0',
            'auto_deduct' => 'nullable|boolean',
        ]);

        foreach ($request->except('_token') as $key => $value) {
            LoanSetting::set($key, $value);
        }

        return back()->with('success', 'Loan settings updated successfully.');
    }

    public function storeLoanType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_interest_rate' => 'required|numeric|min:0|max:100',
            'default_term_months' => 'required|integer|min:1',
            'max_amount' => 'required|numeric|min:0',
        ]);

        LoanType::create($request->all());

        return back()->with('success', 'Loan type created successfully.');
    }

    public function toggleLoanType(LoanType $loanType)
    {
        $loanType->is_active = !$loanType->is_active;
        $loanType->save();

        return back()->with('success', 'Loan type updated.');
    }

    private function generateAmortizationSchedule(Loan $loan)
    {
        $startDate = Carbon::parse($loan->start_date);
        $balance = $loan->total_amount;

        for ($i = 1; $i <= $loan->term_months; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);
            $monthlyInterest = ($balance * ($loan->interest_rate / 100)) / 12;
            $monthlyPrincipal = $loan->monthly_amortization - $monthlyInterest;

            LoanAmortization::create([
                'loan_id' => $loan->id,
                'payment_number' => $i,
                'due_date' => $dueDate,
                'amount_due' => $loan->monthly_amortization,
                'interest_paid' => $monthlyInterest,
                'principal_paid' => $monthlyPrincipal,
                'balance_after' => $balance - $loan->monthly_amortization,
                'status' => 'pending',
            ]);

            $balance -= $loan->monthly_amortization;
        }
    }

    private function getLoanStats()
    {
        return [
            'total_active' => Loan::where('status', 'active')->count(),
            'total_pending' => Loan::where('status', 'pending')->count(),
            'total_amount' => Loan::whereIn('status', ['active', 'approved'])->sum('principal_amount'),
            'total_collected' => LoanAmortization::where('status', 'paid')->sum('amount_paid'),
            'overdue_payments' => LoanAmortization::where('status', 'overdue')->count(),
        ];
    }

    public function getActiveDeductions($planterCode, $cropYear, $weekNo)
    {
        return LoanAmortization::whereHas('loan', function($q) use ($planterCode, $cropYear) {
            $q->where('planter_code', $planterCode)
              ->where('crop_year', $cropYear)
              ->whereIn('status', ['active', 'approved']);
        })
        ->where('status', 'pending')
        ->where('week_no', $weekNo)
        ->sum('amount_due');
    }
}
