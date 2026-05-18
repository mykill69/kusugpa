<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('planter_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('planter_code')->unique();
            $table->string('planter_name');
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->string('area_location')->nullable();
            $table->decimal('total_area', 10, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->date('membership_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('crop_year')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('planter_profiles');
    }
};