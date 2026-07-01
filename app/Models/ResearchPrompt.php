<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchPrompt extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'content', 'created_at'];

    protected $casts = ['created_at' => 'integer'];

    protected static function booting(): void
    {
        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = time();
            }
        });
    }

    public function scores(): HasMany
    {
        return $this->hasMany(QualitativeFrameworkScore::class, 'prompt_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ResearchRun::class, 'prompt_id');
    }
}
