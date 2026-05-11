<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_number', 'planter_code', 'planter_name', 'loan_type_id',
        'principal_amount', 'interest_rate', 'term_months',
        'monthly_amortization', 'total_amount', 'balance',
        'status', 'application_date', 'approved_date', 'approved_by',
        'start_date', 'end_date', 'purpose', 'remarks',
        'crop_year', 'created_by'
    ];

    protected $casts = [
        'application_date' => 'date',
        'approved_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function attachments()
    {
        return $this->hasMany(LoanAttachment::class);
    }

    public function amortizations()
    {
        return $this->hasMany(LoanAmortization::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pendingAmortizations()
    {
        return $this->amortizations()->where('status', 'pending');
    }

    public static function generateLoanNumber()
    {
        $prefix = 'LN';
        $year = date('Y');
        $lastLoan = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $lastLoan ? intval(substr($lastLoan->loan_number, -4)) + 1 : 1;
        return $prefix . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function calculateAmortization()
    {
        $principal = $this->principal_amount;
        $monthlyRate = ($this->interest_rate / 100) / 12;
        $term = $this->term_months;

        if ($monthlyRate > 0) {
            $this->monthly_amortization = $principal * ($monthlyRate * pow(1 + $monthlyRate, $term)) / (pow(1 + $monthlyRate, $term) - 1);
        } else {
            $this->monthly_amortization = $principal / $term;
        }

        $this->total_amount = $this->monthly_amortization * $term;
        $this->balance = $this->total_amount;
    }
}