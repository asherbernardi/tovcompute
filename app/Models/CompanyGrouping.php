<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyGrouping extends Model
{
    protected $table = 'company_groupings';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];
    public $timestamps = false;

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_grouping_associate', 'company_grouping_id', 'company_id')
                    ->withPivot('id');
    }

    public static function get_companies($keyword = null, $groupingId = null)
    {
        return Company::select('company.id')
            ->leftJoin('company_grouping_associate', 'company.id', '=', 'company_grouping_associate.company_id')
            ->when($keyword, function ($query, $keyword) {
                return $query->where('company.name', 'like', "%{$keyword}%")
                            ->orWhere('company.ticker', 'like', "%{$keyword}%");
            })
            ->when($groupingId, function ($query, $groupingId) {
                return $query->where('company_grouping_associate.company_grouping_id', $groupingId);
            })
            ->distinct('company.id')
            ->pluck('id');
    }
}
