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
        Schema::table('research_prompts', function (Blueprint $table) {
            $table->text('comments')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('research_prompts', function (Blueprint $table) {
            $table->dropColumn('comments');
        });
    }
};
