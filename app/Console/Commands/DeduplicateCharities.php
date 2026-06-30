<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeduplicateCharities extends Command
{
    protected $signature = 'charities:deduplicate';
    protected $description = 'Consolidate duplicate charity rows (one row per EIN), re-pointing all references';

    public function handle(): int
    {
        $this->info('Counting duplicates…');

        $total    = DB::table('charity')->count();
        $distinct = DB::select('SELECT COUNT(DISTINCT ein) AS c FROM charity')[0]->c;
        $dupes    = $total - $distinct;

        if ($dupes === 0) {
            $this->info('No duplicates found. Nothing to do.');
            return 0;
        }

        $this->info("Found {$dupes} duplicate rows across {$distinct} distinct EINs.");

        // Build a temporary table of canonical IDs (lowest id per EIN)
        $this->info('Building canonical ID map…');
        DB::statement('CREATE TEMPORARY TABLE canonical_charity AS
            SELECT ein, MIN(id) AS keep_id FROM charity GROUP BY ein');
        DB::statement('CREATE INDEX idx_cc_ein    ON canonical_charity (ein)');
        DB::statement('CREATE INDEX idx_cc_keepid ON canonical_charity (keep_id)');

        // Carry parent associations to the canonical row where the canonical row has none
        $this->info('Preserving parent associations…');
        DB::statement('UPDATE charity c_keep
            JOIN canonical_charity cc ON c_keep.id = cc.keep_id
            JOIN (
                SELECT c_dup.ein, MIN(c_dup.parent) AS parent
                FROM charity c_dup
                WHERE c_dup.parent IS NOT NULL
                GROUP BY c_dup.ein
            ) parents ON c_keep.ein = parents.ein
            SET c_keep.parent = parents.parent
            WHERE c_keep.parent IS NULL');

        // Re-point charity_giving rows to the canonical charity
        $this->info('Re-pointing charity_giving references…');
        $givingUpdated = DB::update('UPDATE charity_giving cg
            JOIN charity c ON cg.charityID = c.id
            JOIN canonical_charity cc ON c.ein = cc.ein
            SET cg.charityID = cc.keep_id
            WHERE cg.charityID != cc.keep_id');
        $this->line("  charity_giving rows updated: {$givingUpdated}");

        // Re-point charity_recommend: delete conflicts first, then update the rest
        $this->info('Re-pointing charity_recommend references…');
        $recsDeleted = DB::delete('DELETE cr FROM charity_recommend cr
            JOIN charity c ON cr.charityID = c.id
            JOIN canonical_charity cc ON c.ein = cc.ein
            WHERE cr.charityID != cc.keep_id
            AND EXISTS (
                SELECT 1 FROM (SELECT companyID, charityID FROM charity_recommend) cr2
                WHERE cr2.companyID = cr.companyID AND cr2.charityID = cc.keep_id
            )');
        $this->line("  charity_recommend conflicts removed: {$recsDeleted}");

        $recsUpdated = DB::update('UPDATE charity_recommend cr
            JOIN charity c ON cr.charityID = c.id
            JOIN canonical_charity cc ON c.ein = cc.ein
            SET cr.charityID = cc.keep_id
            WHERE cr.charityID != cc.keep_id');
        $this->line("  charity_recommend rows updated: {$recsUpdated}");

        // Delete the non-canonical charity rows
        $this->info('Deleting duplicate charity rows…');
        $deleted = DB::delete('DELETE c FROM charity c
            JOIN canonical_charity cc ON c.ein = cc.ein
            WHERE c.id != cc.keep_id');
        $this->line("  charity rows deleted: {$deleted}");

        $this->info('Done. Run `php artisan migrate` to add the unique constraint on ein.');

        return 0;
    }
}
