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
        Schema::create('research_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prompt_id')->constrained('research_prompts');
            $table->string('model');
            $table->string('status')->default('pending');
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_list_id')->nullable();
            $table->json('target_tickers')->nullable();
            $table->unsignedInteger('started_at')->nullable();
            $table->unsignedInteger('completed_at')->nullable();
            $table->longText('log')->nullable();
            $table->unsignedInteger('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_runs');
    }
};
