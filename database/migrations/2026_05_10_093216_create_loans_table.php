<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('loan_types')) {
            Schema::create('loan_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('default_interest_rate', 5, 2)->default(0);
                $table->integer('default_term_months')->default(12);
                $table->decimal('max_amount', 12, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('loans')) {
            Schema::create('loans', function (Blueprint $table) {
                $table->id();
                $table->string('loan_number')->unique();
                $table->string('planter_code');
                $table->string('planter_name');
                $table->foreignId('loan_type_id')->constrained('loan_types');
                $table->decimal('principal_amount', 12, 2);
                $table->decimal('interest_rate', 5, 2);
                $table->integer('term_months');
                $table->decimal('monthly_amortization', 12, 2);
                $table->decimal('total_amount', 12, 2);
                $table->decimal('balance', 12, 2);
                $table->enum('status', ['pending', 'approved', 'active', 'completed', 'rejected', 'cancelled'])->default('pending');
                $table->date('application_date');
                $table->date('approved_date')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->text('purpose')->nullable();
                $table->text('remarks')->nullable();
                $table->string('crop_year');
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
                $table->index('planter_code');
            });
        }

        if (!Schema::hasTable('loan_amortizations')) {
            Schema::create('loan_amortizations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
                $table->integer('payment_number');
                $table->date('due_date');
                $table->decimal('amount_due', 12, 2);
                $table->decimal('amount_paid', 12, 2)->default(0);
                $table->decimal('interest_paid', 12, 2)->default(0);
                $table->decimal('principal_paid', 12, 2)->default(0);
                $table->decimal('balance_after', 12, 2);
                $table->enum('status', ['pending', 'paid', 'overdue', 'partial'])->default('pending');
                $table->date('paid_date')->nullable();
                $table->string('week_no')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('loan_settings')) {
            Schema::create('loan_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type')->default('string');
                $table->timestamps();
            });

            DB::table('loan_settings')->insert([
                ['key' => 'default_interest_rate', 'value' => '5.00', 'type' => 'decimal', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'max_loan_term_months', 'value' => '24', 'type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'min_loan_amount', 'value' => '1000.00', 'type' => 'decimal', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'max_loan_amount', 'value' => '100000.00', 'type' => 'decimal', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'auto_deduct', 'value' => '1', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('loan_amortizations');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('loan_types');
        Schema::dropIfExists('loan_settings');
    }
};