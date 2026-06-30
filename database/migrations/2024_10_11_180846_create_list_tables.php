<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list', function (Blueprint $table) {
            $table->id();
            $table->string('name', 15);
        });

        Schema::create('list_associate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('list_id');
            $table->unsignedBigInteger('company_id');
            $table->foreign('list_id')->references('id')->on('list');
            $table->foreign('company_id')->references('id')->on('company');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('list_associate');
        Schema::dropIfExists('list');
    }
};
