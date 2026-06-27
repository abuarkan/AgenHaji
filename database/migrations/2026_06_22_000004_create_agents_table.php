<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('agent_level_id')->constrained();
            $table->string('referral_code')->unique();
            $table->string('nik')->unique();
            $table->string('whatsapp_number');
            $table->string('type')->default('freelance'); // freelance, institution
            $table->string('status')->default('pending'); // pending, active, suspended
            $table->text('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
