<?php

use App\Http\Controllers\Api\DocsController;
use App\Http\Controllers\Api\ScoreController;
use App\Http\Middleware\AgentApiKey;
use Illuminate\Support\Facades\Route;

Route::get('/docs', DocsController::class);

Route::middleware(AgentApiKey::class)->prefix('companies/{ticker}/scores')->group(function () {
    Route::get('/',        [ScoreController::class, 'index']);
    Route::post('/',       [ScoreController::class, 'store']);
    Route::get('/{id}',    [ScoreController::class, 'show']);
    Route::put('/{id}',    [ScoreController::class, 'update']);
    Route::delete('/{id}', [ScoreController::class, 'destroy']);
});
