<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->timestamps();
        });

        // Insert default settings
        DB::table('system_settings')->insert([
            ['key' => 'system_locked', 'value' => '0', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lock_reason', 'value' => 'System is temporarily locked by administrator.', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_message', 'value' => 'System is under maintenance. Please check back later.', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lock_start_date', 'value' => null, 'type' => 'date', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lock_end_date', 'value' => null, 'type' => 'date', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'subscription_status', 'value' => 'active', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'subscription_amount', 'value' => '0.00', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'subscription_due_date', 'value' => null, 'type' => 'date', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};