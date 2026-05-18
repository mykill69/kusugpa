<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('consolidated_uploads', function (Blueprint $table) {
            $table->decimal('total_summary', 12, 2)->default(0)->after('adj_amt');
        });
    }

    public function down()
    {
        Schema::table('consolidated_uploads', function (Blueprint $table) {
            $table->dropColumn('total_summary');
        });
    }
};