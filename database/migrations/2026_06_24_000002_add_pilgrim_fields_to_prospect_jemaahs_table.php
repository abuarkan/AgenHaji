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
            $table->string('bps_bpih')->nullable()->after('registration_type');
            $table->string('claim_status')->nullable()->default('-')->after('porsi_number');
            $table->string('saving_book_photo_path')->nullable();
            $table->string('npwp_photo_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_jemaahs', function (Blueprint $table) {
            $table->dropColumn([
                'bps_bpih', 
                'claim_status', 
                'saving_book_photo_path', 
                'npwp_photo_path'
            ]);
        });
    }
};
