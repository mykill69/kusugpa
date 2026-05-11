<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAmortization extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id', 'payment_number', 'due_date', 'amount_due',
        'amount_paid', 'interest_paid', 'principal_paid',
        'balance_after', 'status', 'paid_date', 'week_no', 'remarks'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}