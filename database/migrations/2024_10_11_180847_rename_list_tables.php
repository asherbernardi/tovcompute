<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('list', 'company_groupings');
        Schema::rename('list_associate', 'company_grouping_associate');

        Schema::table('company_grouping_associate', function ($table) {
            $table->renameColumn('list_id', 'company_grouping_id');
        });
    }

    public function down(): void
    {
        Schema::table('company_grouping_associate', function ($table) {
            $table->renameColumn('company_grouping_id', 'list_id');
        });

        Schema::rename('company_grouping_associate', 'list_associate');
        Schema::rename('company_groupings', 'list');
    }
};
