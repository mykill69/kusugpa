<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'default_interest_rate', 
        'default_term_months', 'max_amount', 'is_active'
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}