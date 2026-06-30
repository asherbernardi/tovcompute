<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qualitative_framework_score_id');
            $table->foreign('qualitative_framework_score_id')->references('id')->on('qualitative_framework_scores');
            $table->string('category');
            $table->decimal('score', 3, 1);
            $table->enum('confidence', ['HIGH', 'MED', 'LOW']);
            $table->json('evidence');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_scores');
    }
};
