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
        Schema::create('charity_giving', function (Blueprint $table) {
            $table->id();
            $table->foreign('charityID')->references('id')->on('charity');
            $table->integer("tax_year");
            $table->date("tax_period_start");
            $table->date("tax_period_end");
            $table->integer("grants_paid");
            $table->integer("total_contrib");
            $table->integer("net_assets");
            $table->integer("total_revenue");
            $table->integer("date");
        });
    }

};
