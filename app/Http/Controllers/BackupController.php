<?php
// app/Http/Controllers/BackupController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    /**
     * Trigger the backup process in the background.
     */
    public function run(Request $request)
    {
        try {
            // Run the backup:run Artisan command in the background
            Artisan::queue('backup:run'); // Queues the command for background processing
            
            return response()->json([
                'status' => 'success',
                'message' => 'Backup process started in the background.',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Manual backup failed: {$e->getMessage()}");
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to start backup process. Check logs for details.',
            ], 500);
        }
    }
}