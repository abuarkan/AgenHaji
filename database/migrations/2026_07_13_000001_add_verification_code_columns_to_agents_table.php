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
        Schema::table('agents', function (Blueprint $table) {
            $table->string('email_verification_code')->nullable()->after('is_ktp_verified');
            $table->timestamp('email_verification_expires_at')->nullable()->after('email_verification_code');
            $table->string('whatsapp_verification_code')->nullable()->after('email_verification_expires_at');
            $table->timestamp('whatsapp_verification_expires_at')->nullable()->after('whatsapp_verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'email_verification_code',
                'email_verification_expires_at',
                'whatsapp_verification_code',
                'whatsapp_verification_expires_at'
            ]);
        });
    }
};
