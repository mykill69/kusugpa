<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quedans', function (Blueprint $table) {
            $table->id();
            $table->string('crop_year');
            $table->string('week_no');
            $table->string('planter_code');
            $table->string('planter_name');
            $table->string('qdn_no')->nullable();
            $table->string('tin_no')->nullable();
            $table->decimal('total_liens', 10, 3)->default(0);
            $table->decimal('sugar_lkg', 10, 3)->default(0);
            $table->decimal('labor_lkg', 10, 3)->default(0);
            $table->string('user_id');
            $table->timestamps();
            
            $table->index('planter_code');
            $table->index('qdn_no');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quedans');
    }
};