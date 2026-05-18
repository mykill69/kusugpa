<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quedans', function (Blueprint $table) {
            $table->enum('status', ['pending', 'bought', 'rejected'])->default('pending')->after('labor_lkg');
            $table->timestamp('bought_at')->nullable()->after('status');
            $table->foreignId('bought_by')->nullable()->constrained('users')->after('bought_at');
        });

        Schema::table('molasses', function (Blueprint $table) {
            $table->enum('status', ['pending', 'bought', 'rejected'])->default('pending')->after('mol_net');
            $table->timestamp('bought_at')->nullable()->after('status');
            $table->foreignId('bought_by')->nullable()->constrained('users')->after('bought_at');
        });
    }

    public function down()
    {
        Schema::table('quedans', function (Blueprint $table) {
            $table->dropColumn(['status', 'bought_at', 'bought_by']);
        });
        Schema::table('molasses', function (Blueprint $table) {
            $table->dropColumn(['status', 'bought_at', 'bought_by']);
        });
    }
};