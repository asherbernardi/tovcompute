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
        Schema::create('charity', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("ein");
            $table->integer("updated");
            $table->unsignedBigInteger('parent')->nullable();
            $table->foreign('parent')->references('id')->on('company');
        });
    }


};
