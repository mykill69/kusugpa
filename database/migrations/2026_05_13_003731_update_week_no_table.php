<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('week_no', function (Blueprint $table) {
            $table->dateTime('week_start_date')->change();
            $table->dateTime('week_end_date')->change();
        });
    }

    public function down()
    {
        Schema::table('week_no', function (Blueprint $table) {
            $table->string('week_start_date')->change();
            $table->string('week_end_date')->change();
        });
    }
};