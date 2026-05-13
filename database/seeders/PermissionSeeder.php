<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'Set Quedan Price',
                'slug' => 'set-quedan-price',
                'description' => 'Can set quedan prices for crop years and weeks',
            ],
            [
                'name' => 'Set Molasses Price',
                'slug' => 'set-molasses-price',
                'description' => 'Can set molasses prices for crop years and weeks',
            ],
            [
                'name' => 'Set Crop Year',
                'slug' => 'set-crop-year',
                'description' => 'Can create and manage crop years',
            ],
            [
                'name' => 'Set Week Number',
                'slug' => 'set-week-number',
                'description' => 'Can create and manage week numbers',
            ],
            [
                'name' => 'Upload Summary',
                'slug' => 'upload-summary',
                'description' => 'Can upload summary CSV files',
            ],
            [
                'name' => 'Upload Trucking Allowance',
                'slug' => 'upload-trucking',
                'description' => 'Can upload trucking allowance CSV files',
            ],
            [
                'name' => 'Upload Mudpress',
                'slug' => 'upload-mudpress',
                'description' => 'Can upload mudpress CSV files',
            ],
                        [
                'name' => 'Upload Fresh Cane Incentive',
                'slug' => 'upload-fci',
                'description' => 'Can upload fresh cane incentive CSV files',
            ],
            [
                'name' => 'Upload Fuel Allowance',
                'slug' => 'upload-fuel',
                'description' => 'Can upload fuel allowance CSV files',
            ],
            [
                'name' => 'Upload Rentals',
                'slug' => 'upload-rentals',
                'description' => 'Can upload rental allowance CSV files',
            ],
            [
                'name' => 'Upload Underload',
                'slug' => 'upload-underload',
                'description' => 'Can upload underload allowance CSV files',
            ],
            [
                'name' => 'Upload Transloading',
                'slug' => 'upload-transloading',
                'description' => 'Can upload transloading allowance CSV files',
            ],
            [
                'name' => 'View Reports',
                'slug' => 'view-reports',
                'description' => 'Can view summary and other reports',
            ],
            [
                'name' => 'Print Vouchers',
                'slug' => 'print-vouchers',
                'description' => 'Can print cheque vouchers',
            ],
            [
                'name' => 'View Loans',
                'slug' => 'view-loans',
                'description' => 'Can view loan records and details',
            ],
            [
                'name' => 'Create Loans',
                'slug' => 'create-loans',
                'description' => 'Can create new loan applications',
            ],
            [
                'name' => 'Approve Loans',
                'slug' => 'approve-loans',
                'description' => 'Can approve or reject loan applications',
            ],
            [
                'name' => 'Process Loan Payments',
                'slug' => 'process-loan-payments',
                'description' => 'Can record loan payments and amortizations',
            ],
            [
                'name' => 'Manage Loan Settings',
                'slug' => 'manage-loan-settings',
                'description' => 'Can configure loan types, interest rates, and settings',
            ],
        ];

        // Use updateOrCreate to avoid duplicate key errors
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']], // Search by slug
                $permission // Values to update or create
            );
        }
        
        // Output success message in console
        $this->command->info('Permissions seeded successfully!');
        $this->command->info('Total permissions processed: ' . count($permissions));
    }
}
