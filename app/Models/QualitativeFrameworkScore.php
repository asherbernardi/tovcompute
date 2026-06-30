<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QualitativeFrameworkScore extends Model
{
    protected $table = 'qualitative_framework_scores';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'created_at',
        'source',
        'summary',
        'red_flags',
    ];

    protected $casts = [
        'created_at' => 'integer',
        'red_flags'  => 'array',
    ];

    protected static function booting(): void
    {
        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = time();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function featureScores(): HasMany
    {
        return $this->hasMany(FeatureScore::class);
    }
}
