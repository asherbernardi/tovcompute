<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qualitative_framework_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->integer('created_at');
            $table->string('source');
            $table->text('summary');
            $table->json('red_flags')->nullable();
        });

        Schema::table('qualitative_framework_scores', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('company');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qualitative_framework_scores');
    }
};
