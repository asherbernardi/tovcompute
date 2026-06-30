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