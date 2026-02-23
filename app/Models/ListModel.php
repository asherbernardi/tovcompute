<?php
// app/Models/ListModel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListModel extends Model
{
    protected $table = 'list'; // Match your table name
    // Define the primary key (assuming 'id' is the primary key as per your schema)
    protected $primaryKey = 'id';

    // Specify fillable fields for mass assignment (optional, depending on your use case)
    protected $fillable = ['name'];

    // Disable timestamps if your table doesn't have created_at/updated_at columns
    public $timestamps = false;

    /**
     * Define the many-to-many relationship with the Company model
     * through the list_associate pivot table
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'list_associate', 'list_id', 'company_id')
                    ->withPivot('id');
    }


    /**
     * Get company IDs associated with a list or all companies if no list is specified
     *
     * @param string|null $keyword Search term for name or ticker
     * @param int|null $listId Specific list ID to filter by (null for all lists)
     * @return \Illuminate\Support\Collection
     */
    public static function get_companies($keyword = null, $listId = null)
    {
        return Company::select('company.id')
            ->leftJoin('list_associate', 'company.id', '=', 'list_associate.company_id')
            ->when($keyword, function ($query, $keyword) {
                return $query->where('company.name', 'like', "%{$keyword}%")
                            ->orWhere('company.ticker', 'like', "%{$keyword}%");
            })
            ->when($listId, function ($query, $listId) {
                return $query->where('list_associate.list_id', $listId);
            })
            ->distinct('company.id')
            ->pluck('id');
    }
}

?>