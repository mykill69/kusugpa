<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('consolidated_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('planter_code');
            $table->string('assn_code')->nullable();
            $table->string('planter_name');
            $table->string('assn_name')->nullable();
            $table->decimal('ta_wt', 10, 3)->default(0);
            $table->decimal('ta_amount', 12, 2)->default(0);
            $table->decimal('emi_wt', 10, 3)->default(0);
            $table->decimal('emi_amount', 12, 2)->default(0);
            $table->decimal('pat_wt', 10, 3)->default(0);
            $table->decimal('pat_amount', 12, 2)->default(0);
            $table->decimal('cci_fa_wt', 10, 3)->default(0);
            $table->decimal('cci_fa_amt', 12, 2)->default(0);
            $table->decimal('cci_fb_wt', 10, 3)->default(0);
            $table->decimal('cci_fb_amt', 12, 2)->default(0);
            $table->decimal('cci_fc_wt', 10, 3)->default(0);
            $table->decimal('cci_fc_amt', 12, 2)->default(0);
            $table->decimal('fuel_issuance_amt', 12, 2)->default(0);
            $table->decimal('rental_amt', 12, 2)->default(0);
            $table->decimal('underload_amt', 12, 2)->default(0);
            $table->decimal('mudpress_amt', 12, 2)->default(0);
            $table->decimal('adj_amt', 12, 2)->default(0);
            $table->string('user_id');
            $table->timestamps();
            
            $table->index('planter_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consolidated_uploads');
    }
};