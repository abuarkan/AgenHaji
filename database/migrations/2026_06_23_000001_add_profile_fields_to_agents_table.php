<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Verifications
            $table->boolean('is_email_verified')->default(true);
            $table->boolean('is_whatsapp_verified')->default(true);
            $table->boolean('is_ktp_verified')->default(true);

            // Personal Info
            $table->string('full_name')->nullable();
            $table->date('birth_date')->nullable();

            // KTP Address
            $table->string('alamat_ktp')->nullable();
            $table->string('provinsi_ktp')->nullable();
            $table->string('kota_ktp')->nullable();
            $table->string('kecamatan_ktp')->nullable();
            $table->string('kelurahan_ktp')->nullable();

            // Domisili/Tinggal Address
            $table->string('alamat_tinggal')->nullable();
            $table->string('provinsi_tinggal')->nullable();
            $table->string('kota_tinggal')->nullable();
            $table->string('kecamatan_tinggal')->nullable();
            $table->string('kelurahan_tinggal')->nullable();

            // Files
            $table->string('foto_ktp')->nullable();
            $table->string('foto_diri')->nullable();
            $table->string('foto_bangunan')->nullable();

            // Bank Details
            $table->string('foto_buku_tabungan')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('cabang_bank')->nullable();

            // NPWP Details
            $table->string('nomor_npwp')->nullable();
            $table->string('foto_npwp')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'is_email_verified',
                'is_whatsapp_verified',
                'is_ktp_verified',
                'full_name',
                'birth_date',
                'alamat_ktp',
                'provinsi_ktp',
                'kota_ktp',
                'kecamatan_ktp',
                'kelurahan_ktp',
                'alamat_tinggal',
                'provinsi_tinggal',
                'kota_tinggal',
                'kecamatan_tinggal',
                'kelurahan_tinggal',
                'foto_ktp',
                'foto_diri',
                'foto_bangunan',
                'foto_buku_tabungan',
                'nama_bank',
                'nomor_rekening',
                'cabang_bank',
                'nomor_npwp',
                'foto_npwp',
            ]);
        });
    }
};
