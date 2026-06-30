<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qualitative_framework_scores', function (Blueprint $table) {
            $table->integer('research_duration')->nullable()->comment('seconds');
            $table->integer('input_tokens')->nullable();
            $table->integer('output_tokens')->nullable();
            $table->mediumText('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('qualitative_framework_scores', function (Blueprint $table) {
            $table->dropColumn(['research_duration', 'input_tokens', 'output_tokens', 'notes']);
        });
    }
};
