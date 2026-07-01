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
        'prompt_id',
        'research_run_id',
        'created_at',
        'source',
        'summary',
        'red_flags',
        'research_duration',
        'input_tokens',
        'output_tokens',
        'notes',
    ];

    protected $casts = [
        'created_at'        => 'integer',
        'red_flags'         => 'array',
        'research_duration' => 'integer',
        'input_tokens'      => 'integer',
        'output_tokens'     => 'integer',
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

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(ResearchPrompt::class, 'prompt_id');
    }

    public function researchRun(): BelongsTo
    {
        return $this->belongsTo(ResearchRun::class, 'research_run_id');
    }
}
