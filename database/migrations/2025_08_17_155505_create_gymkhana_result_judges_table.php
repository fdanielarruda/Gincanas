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
        Schema::create('gymkhana_result_judges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gymkhana_result_id')->constrained()->onDelete('cascade');
            $table->foreignId('judge_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gymkhana_result_judges');
    }
};
