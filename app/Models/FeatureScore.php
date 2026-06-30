<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureScore extends Model
{
    protected $table = 'feature_scores';
    public $timestamps = false;

    protected $fillable = [
        'qualitative_framework_score_id',
        'category',
        'score',
        'confidence',
        'evidence',
    ];

    protected $casts = [
        'score'    => 'decimal:1',
        'evidence' => 'array',
    ];

    public function qualitativeFrameworkScore(): BelongsTo
    {
        return $this->belongsTo(QualitativeFrameworkScore::class);
    }
}
