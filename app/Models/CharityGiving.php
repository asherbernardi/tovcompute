<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharityGiving extends Model
{
    use HasFactory;

    protected $table = 'charity_giving';
    protected $primaryKey = 'id';
    protected $fillable = ['charityID', 'ein']; 

    /**
     * get the charity this belongs to?  I guess?
     */
    public function charity(): BelongsTo
    {
        return $this->belongsTo(Charity::class, 'ein','ein');
    }

    /**
     * Get summed totals for a charity by tax year
     *
     * @param int $charityID
     * @return \Illuminate\Support\Collection
     */
    public static function totals($charityID)
    {
        $totals = self::selectRaw("COALESCE(SUM(grants_paid), 0) AS sumgrants, COALESCE(SUM(total_contrib), 0) AS sumcontrib, COALESCE(SUM(net_assets), 0) AS sumassets, COALESCE(SUM(total_revenue), 0) AS sumrevenue")
            ->where('charityID', $charityID)
            ->first();

        // Debug: Check the raw output
         //dd($totals);

        return $totals;
    }

    /**
     * Sync charityID with charity table based on EIN, only for NULL charityIDs
     *
     * @return int Number of affected rows
     * @throws \Exception
     */
    public static function syncCharityIds()
    {
        return DB::table('charity_giving')
            ->whereNull('charityID') // Only update rows where charityID is NULL
            ->update([
                'charityID' => DB::table('charity')
                    ->select('id')
                    ->whereColumn('charity.ein', 'charity_giving.ein')
                    ->limit(1) // Ensures only one match
            ]);
    }
}
