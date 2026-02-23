<?php
// app/Models/CharityRecommend.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharityRecommend extends Model
{
    protected $table = 'charity_recommend';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'charityID',
        'companyID',
        'suggested_date',
        'status',
        'match',
        'ai_match',
        'confirm_date',
    ];

    protected $casts = [
        'suggested_date' => 'integer',
        'status' => 'integer',
        'match' => 'decimal:2',
        'ai_match' => 'decimal:2',
        'confirm_date' => 'integer',
    ];

    /**
     * @desc there is just something really gramatically bothersome about using updated_at ...
     */
    const CREATED_AT = 'suggested_date';
    const UPDATED_AT = 'suggested_date';
    /**
     * @desc use unix timestamp because your date format is dumb
     *
     * **/
    protected $dateFormat = 'U';

    // Relationships
    public function charity()
    {
        return $this->belongsTo(Charity::class, 'charityID');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'companyID');
    }
}
?>