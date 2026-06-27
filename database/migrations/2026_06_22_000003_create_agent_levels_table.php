<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Silver, Gold, Platinum, Diamond
            $table->integer('target_prospects'); // e.g. 20, 26, 31, 51
            $table->decimal('commission_per_prospect', 15, 2); // e.g. 50000, 75000, 100000, 125000
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_levels');
    }
};
