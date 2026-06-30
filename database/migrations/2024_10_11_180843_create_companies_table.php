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
        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("ein");
            $table->integer("secID");
            $table->string("ticker");
            $table->integer("formed");
            $table->string("list");
            $table->integer("updated");            
        });
    }


};
