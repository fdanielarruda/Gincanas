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
        Schema::create('gymkhana_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gymkhana_id')->constrained()->onDelete('cascade');
            $table->json('teams')->nullable();
            $table->json('phases')->nullable();
            $table->json('results')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gymkhana_results');
    }
};
