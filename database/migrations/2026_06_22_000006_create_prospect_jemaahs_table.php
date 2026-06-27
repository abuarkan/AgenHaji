<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_jemaahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('nik')->unique();
            $table->text('address')->nullable();
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->string('ktp_photo_path')->nullable(); // AWS S3 private path
            $table->string('email')->nullable();
            $table->string('phone_number');
            $table->string('registration_type')->default('Reguler'); // Reguler, Khusus
            $table->string('bank_id')->nullable(); // Bank Penyalur
            $table->string('status_pendaftaran')->default('Draft'); // Draft, Pending_Verification, Verified, Canceled
            $table->string('porsi_number')->nullable(); // Synced from Kemenag
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_jemaahs');
    }
};
