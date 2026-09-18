<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prospect_jemaahs', function (Blueprint $table) {
            if (!Schema::hasColumn('prospect_jemaahs', 'registration_channel')) {
                $table->string('registration_channel')->default('bpkh_apps')->after('registration_type');
            }
            if (!Schema::hasColumn('prospect_jemaahs', 'siskehat_sync_at')) {
                $table->timestamp('siskehat_sync_at')->nullable()->after('porsi_number');
            }
            if (!Schema::hasColumn('prospect_jemaahs', 'siskehat_reference_id')) {
                $table->string('siskehat_reference_id')->nullable()->after('siskehat_sync_at');
            }
            if (!Schema::hasColumn('prospect_jemaahs', 'is_porsi_bound')) {
                $table->boolean('is_porsi_bound')->default(true)->after('siskehat_reference_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_jemaahs', function (Blueprint $table) {
            $table->dropColumn(['registration_channel', 'siskehat_sync_at', 'siskehat_reference_id', 'is_porsi_bound']);
        });
    }
};
