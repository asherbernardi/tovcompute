<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
Use Carbon\Carbon;

class Charity extends Model
{

    use HasFactory;

    protected $table = 'charity';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'parent', 'ein'];

    /**
     * @desc there is just something really gramatically bothersome about using updated_at ...
     */
    const CREATED_AT = 'updated';
    const UPDATED_AT = 'updated';
    
    /**
     * @desc use unix timestamp because your date format is dumb
     *
     * **/
    protected $dateFormat = 'U';

    protected function getUpdatedAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }


    public function history(): HasMany
    {
        return $this->hasMany(CharityGiving::class,"ein","ein")->orderby("tax_period_start","asc");
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class,"parent");
    }

    public function history_export($charityID)
    {
        // Ensure $charityID is an array for whereIn
        $charityIDs = is_array($charityID) ? $charityID : [$charityID];
    
        $totals = CharityGiving::selectRaw("tax_year, tax_period_end, total_assets, total_expenses, grants_paid, total_contrib, net_assets, total_revenue")
            ->join("charity", "charity.id", "=", "charity_giving.charityID")
            ->whereIn("charityID", $charityIDs)
            ->orderBy('tax_year', 'desc')
            ->get();
    
        return $totals;
    }

}
