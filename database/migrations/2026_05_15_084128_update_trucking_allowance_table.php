<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trucking_allowance', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('trucking_allowance', 'net_cane')) {
                $table->decimal('net_cane', 10, 3)->default(0)->after('planter_name');
            }
            if (!Schema::hasColumn('trucking_allowance', 'trans_code')) {
                $table->string('trans_code')->nullable()->after('trucking_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('trucking_allowance', function (Blueprint $table) {
            if (Schema::hasColumn('trucking_allowance', 'net_cane')) {
                $table->dropColumn('net_cane');
            }
            if (Schema::hasColumn('trucking_allowance', 'trans_code')) {
                $table->dropColumn('trans_code');
            }
        });
    }
};