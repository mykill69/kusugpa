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
        Schema::create('quedan_price', function (Blueprint $table) {
            $table->id();
            $table->string('quedan_type'); // e.g., 'regular', 'premium'
            $table->string('quedan_price');
            $table->string('crop_year');
            $table->string('week_no');
            $table->string('user_id'); // User ID of the person who created the record
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quedan_price');
    }
};
