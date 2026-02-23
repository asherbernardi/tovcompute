<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
Use Carbon\Carbon;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'ein',  'secID', 'ticker', 'formed', 'updated'];
    protected $casts = [
        'secID' => 'integer',
        'updated' => 'integer', // Unix timestamp
    ];

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

    public function charities(): HasMany
    {
        return $this->hasMany(Charity::class,"parent")->orderby("name","asc");
    }

    /*
    public function history(): HasManyThrough
    {
        return $this->hasManyThrough(
            charity::class,
            charity_giving::class,
            'charityID', // Foreign key on the giving table...
            'parent', // Foreign key on the charity table...
            'id', // Local key on the ? table...
            'id' // Local key on the ? table...
        );
    }
    */



    public function totals($companyID)
    {
        return CharityGiving::selectRaw("SUM(grants_paid) as sumgrants, SUM(total_contrib) as sumcontrib, SUM(net_assets) as sumassets, SUM(total_revenue) as sumrevenue")
            ->join("charity", "charity.id", "=", "charity_giving.charityID")
            ->where("charity.parent", $companyID)
            ->first();
    }


    public function history($companyID)
    {
        $totals = CharityGiving::selectRaw("tax_year, SUM(grants_paid) as sumgrants, SUM(total_contrib) as sumcontrib, SUM(net_assets) as sumassets, SUM(total_revenue) as sumrevenue")
            ->join("charity", "charity.id", "=", "charity_giving.charityID")
            ->where("charity.parent", $companyID)
            ->groupBy("tax_year")
            ->orderBy('tax_year', 'desc')
            ->get();

        return $totals;
    }

    public function history_export($companyID)
    {
        // Ensure $companyID is an array for whereIn
        $companyIDs = is_array($companyID) ? $companyID : [$companyID];
    
        $totals = CharityGiving::selectRaw("company.ticker,charity.name, tax_year, tax_period_end, total_assets, total_expenses, grants_paid, total_contrib, net_assets, total_revenue")
            ->join("charity", "charity.id", "=", "charity_giving.charityID")
            ->join("company", "charity.parent","=","company.id")
            ->whereIn("charity.parent", $companyIDs)
            ->orderBy('tax_year', 'desc')
            ->get();
    
        return $totals;
    }

    public function lists()
    {
        return $this->belongsToMany(ListModel::class, 'list_associate', 'company_id', 'list_id')
                    ->withPivot('id');
    }
        
    /*
    public function history()
    {
        $transactions = $this->charities()->with("history")->get()->pluck("history")->flatten()->groupBy("tax_year");

        //Calculate totals for each year
        $history = $transactions->map(function ($yearTransactions, $year) {
            return [
                'year' => $year,
                'sumgrants' => $yearTransactions->sum('grants_paid'),
                'sumcontrib' => $yearTransactions->sum('total_contrib'),
                'sumassets' => $yearTransactions->sum('net_assets'),
                'sumrevenue' => $yearTransactions->sum('total_revenue'),
                'count' => $yearTransactions->count()
            ];
        })->values()->all(); // Convert to indexed array

        return $history;
    }
        */
        
}
