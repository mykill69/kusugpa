<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_advances', function (Blueprint $table) {
            $table->id();
            $table->string('ca_number')->unique();
            $table->string('planter_code');
            $table->string('planter_name');
            $table->decimal('amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->default(3);
            $table->integer('term_months')->default(3);
            $table->decimal('monthly_amortization', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->date('application_date');
            $table->date('approved_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->string('crop_year');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // database/migrations/xxxx_create_cash_advance_amortizations_table.php
        Schema::create('cash_advance_amortizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_advance_id');
            $table->integer('payment_number');
            $table->date('due_date');
            $table->decimal('amount_due', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('interest_paid', 12, 2)->default(0);
            $table->decimal('principal_paid', 12, 2)->default(0);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->date('paid_date')->nullable();
            $table->string('week_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('cash_advance_id')->references('id')->on('cash_advances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_advances');
    }
};
