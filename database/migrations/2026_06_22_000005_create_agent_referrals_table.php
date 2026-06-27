<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_agent_id')->constrained('agents')->onDelete('cascade');
            $table->foreignId('child_agent_id')->constrained('agents')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_referrals');
    }
};
