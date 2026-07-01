<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightingPreset extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'weights'];

    protected $casts = ['weights' => 'array'];
}
