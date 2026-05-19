<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('consolidated_uploads', function (Blueprint $table) {
            if (!Schema::hasColumn('consolidated_uploads', 'crop_year')) {
                $table->string('crop_year')->nullable()->after('id');
            }
            if (!Schema::hasColumn('consolidated_uploads', 'week_no')) {
                $table->string('week_no')->nullable()->after('crop_year');
            }
        });
    }

    public function down()
    {
        Schema::table('consolidated_uploads', function (Blueprint $table) {
            $table->dropColumn(['crop_year', 'week_no']);
        });
    }
};