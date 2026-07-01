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
        Schema::table('qualitative_framework_scores', function (Blueprint $table) {
            $table->foreignId('prompt_id')->nullable()->constrained('research_prompts')->nullOnDelete();
            $table->foreignId('research_run_id')->nullable()->constrained('research_runs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('qualitative_framework_scores', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prompt_id');
            $table->dropConstrainedForeignId('research_run_id');
        });
    }
};
