<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->foreignId('prospect_jemaah_id')->nullable()->constrained('prospect_jemaahs')->onDelete('set null');
            $table->string('type'); // credit, debit
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('status')->default('pending'); // pending, approved, disbursed, rejected
            $table->string('disbursement_reference')->nullable(); // payout gateway transaction reference
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_ledgers');
    }
};
