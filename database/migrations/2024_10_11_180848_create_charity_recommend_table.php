<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charity_recommend', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('companyID');
            $table->foreign('companyID')->references('id')->on('company');
            $table->unsignedBigInteger('charityID');
            $table->foreign('charityID')->references('id')->on('charity');
            $table->decimal('match', 5, 2)->default(0);
            $table->decimal('ai_match', 5, 2)->nullable();
            $table->tinyInteger('status')->default(0); // 0=pending, 1=confirmed, 2=rejected
            $table->integer('suggested_date');
            $table->integer('confirm_date')->nullable();

            $table->unique(['companyID', 'charityID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charity_recommend');
    }
};
