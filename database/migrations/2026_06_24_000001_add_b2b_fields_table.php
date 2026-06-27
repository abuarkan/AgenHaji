<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('legal_document')->nullable();
            $table->string('npwp')->nullable();
            $table->string('bank_account')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->boolean('is_institution_admin')->default(false);
            $table->string('bukti_pekerja')->nullable();
            $table->string('sk_pengangkatan')->nullable();
            $table->string('nip')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['legal_document', 'npwp', 'bank_account', 'latitude', 'longitude']);
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn(['is_institution_admin', 'bukti_pekerja', 'sk_pengangkatan', 'nip']);
        });
    }
};
