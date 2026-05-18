<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('molasses', function (Blueprint $table) {
            $table->id();
            $table->string('crop_year');
            $table->string('week_no');
            $table->string('planter_code');
            $table->string('planter_name');
            $table->string('tin_no')->nullable();
            $table->string('mc_no')->nullable();
            $table->decimal('mol_net', 10, 3)->default(0);
            $table->string('user_id');
            $table->timestamps();
            
            $table->index('planter_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('molasses');
    }
};