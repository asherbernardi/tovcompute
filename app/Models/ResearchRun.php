<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchRun extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'prompt_id',
        'model',
        'status',
        'target_type',
        'target_list_id',
        'target_tickers',
        'started_at',
        'completed_at',
        'log',
        'created_at',
    ];

    protected $casts = [
        'target_tickers' => 'array',
        'started_at'     => 'integer',
        'completed_at'   => 'integer',
        'created_at'     => 'integer',
    ];

    protected static function booting(): void
    {
        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = time();
            }
        });
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(ResearchPrompt::class, 'prompt_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(QualitativeFrameworkScore::class, 'research_run_id');
    }

    public function targetList(): BelongsTo
    {
        return $this->belongsTo(CompanyGrouping::class, 'target_list_id');
    }

    public function durationSeconds(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->completed_at - $this->started_at;
        }
        return null;
    }

    public function costEstimate(): ?float
    {
        $scores = $this->scores;
        if ($scores->isEmpty()) return null;

        $inputTokens  = $scores->sum('input_tokens');
        $outputTokens = $scores->sum('output_tokens');

        // Rough Claude Sonnet-class pricing: $3/MTok in, $15/MTok out
        return round(($inputTokens * 3 + $outputTokens * 15) / 1_000_000, 2);
    }
}
