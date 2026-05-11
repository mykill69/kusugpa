<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoanType;

class LoanTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'name' => 'Crop Production Loan',
                'description' => 'Loan for sugarcane crop production expenses including fertilizers, pesticides, and labor.',
                'default_interest_rate' => 5.00,
                'default_term_months' => 12,
                'max_amount' => 50000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Loan',
                'description' => 'Quick loan for emergency needs of planter members.',
                'default_interest_rate' => 3.00,
                'default_term_months' => 6,
                'max_amount' => 20000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Equipment Loan',
                'description' => 'Loan for purchasing farming equipment and machinery.',
                'default_interest_rate' => 8.00,
                'default_term_months' => 24,
                'max_amount' => 100000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Farm Improvement Loan',
                'description' => 'Loan for farm land improvement and development.',
                'default_interest_rate' => 6.00,
                'default_term_months' => 18,
                'max_amount' => 75000.00,
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            LoanType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}