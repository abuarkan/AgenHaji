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
        Schema::table('racing_programs', function (Blueprint $table) {
            if (!Schema::hasColumn('racing_programs', 'reward_type')) {
                $table->string('reward_type')->default('Paket Umrah')->after('min_portion_target');
            }
            if (!Schema::hasColumn('racing_programs', 'winner_quota')) {
                $table->integer('winner_quota')->default(6)->after('reward_type');
            }
            if (!Schema::hasColumn('racing_programs', 'announcement_date')) {
                $table->date('announcement_date')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('racing_programs', 'target_bps_bpih')) {
                $table->string('target_bps_bpih')->nullable()->after('announcement_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('racing_programs', function (Blueprint $table) {
            $table->dropColumn(['reward_type', 'winner_quota', 'announcement_date', 'target_bps_bpih']);
        });
    }
};
