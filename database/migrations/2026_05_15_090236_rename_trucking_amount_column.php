<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trucking_allowance', function (Blueprint $table) {
            $table->renameColumn('trucking_amount', 'ta_amount');
        });
    }

    public function down()
    {
        Schema::table('trucking_allowance', function (Blueprint $table) {
            $table->renameColumn('ta_amount', 'trucking_amount');
        });
    }
};