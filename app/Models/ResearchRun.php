<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ResearchRun extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'prompt_id',
        'model',
        'mode',
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

    public function items(): HasMany
    {
        return $this->hasMany(ResearchRunItem::class, 'research_run_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'running'], true);
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
        $items = $this->items;
        $price = config("research.models.{$this->model}");

        // Runs from before the worker existed only have token totals on scores.
        if ($items->isEmpty() || !$price) {
            $scores = $this->scores;
            if ($scores->isEmpty()) return null;

            $in  = $price['input'] ?? 3;
            $out = $price['output'] ?? 15;
            return round(($scores->sum('input_tokens') * $in + $scores->sum('output_tokens') * $out) / 1_000_000, 2);
        }

        $tokens = $items->sum('input_tokens') * $price['input']
            + $items->sum('output_tokens') * $price['output']
            + $items->sum('cache_read_tokens') * $price['input'] * config('research.cache_read_multiplier')
            + $items->sum('cache_write_tokens') * $price['input'] * config('research.cache_write_multiplier');

        $cost = $tokens / 1_000_000;
        if ($this->mode === 'batch') {
            $cost *= config('research.batch_discount');
        }

        return round($cost + $items->sum('web_searches') * config('research.web_search_price'), 2);
    }

    /**
     * Append a timestamped line to the run log. Done in SQL so concurrent
     * workers don't overwrite each other's lines.
     */
    public function appendLog(string $line): void
    {
        $entry = date('Y-m-d H:i:s') . '  ' . $line . "\n";
        DB::update("UPDATE research_runs SET log = CONCAT(COALESCE(log, ''), ?) WHERE id = ?", [$entry, $this->id]);
    }

    /**
     * Mark the run finished once every item is terminal. Safe to call from
     * any worker after it finishes an item.
     */
    public function finalizeIfDone(): void
    {
        $this->refresh();
        if (!$this->isActive()) return;

        $open = $this->items()->whereNotIn('status', ResearchRunItem::TERMINAL)->count();
        if ($open > 0) return;

        $completed = $this->items()->where('status', 'completed')->count();
        $this->update([
            'status'       => $completed > 0 ? 'completed' : 'failed',
            'completed_at' => time(),
        ]);
        $this->appendLog("Run finished: {$completed}/{$this->items()->count()} companies completed.");
    }
}
