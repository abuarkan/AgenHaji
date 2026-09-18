<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('system_settings')->insert([
            'key' => 'two_factor_enabled',
            'value' => '1', // '1' = Enabled, '0' = Disabled
            'is_encrypted' => false,
            'description' => 'Global Two-Factor Authentication (2FA) Status (1 = Enabled, 0 = Disabled)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->where('key', 'two_factor_enabled')->delete();
    }
};
