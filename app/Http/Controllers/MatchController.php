<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\MatchingService;

class MatchController extends Controller
{
    public function runMatches()
    {
        return view('matches.index');
    }

    public function status()
    {
        return response()->json([
            'status' => Cache::get('match_run:status', 'idle'),
            'count'  => count(Cache::get('match_run:events', [])),
        ]);
    }

    public function startMatches()
    {
        if (Cache::get('match_run:status') === 'running') {
            return response()->json(['status' => 'already_running']);
        }

        Cache::forget('match_run:stop');
        Cache::put('match_run:status', 'running', now()->addHours(24));
        Cache::put('match_run:events', [], now()->addHours(24));

        $artisan = base_path('artisan');
        exec("php {$artisan} match:run > /dev/null 2>&1 &");

        return response()->json(['status' => 'started']);
    }

    public function deduplicate(MatchingService $matcher)
    {
        $deleted = $matcher->deduplicateRecommendations();
        return response()->json(['deleted' => $deleted]);
    }

    public function stopMatches()
    {
        if (Cache::get('match_run:status') === 'running') {
            Cache::put('match_run:stop', true, now()->addHours(1));
        }
        return response()->json(['status' => 'stopping']);
    }

    public function streamMatches(Request $request)
    {
        $offset = (int) $request->query('offset', 0);

        return response()->stream(function () use ($offset) {
            $i = $offset;

            // Replay cached events from the requested offset
            $events = Cache::get('match_run:events', []);
            foreach (array_slice($events, $offset) as $event) {
                echo "id: {$i}\ndata: " . json_encode($event) . "\n\n";
                ob_flush();
                flush();
                $i++;
            }

            // Poll for new events while the job is still running
            while (Cache::get('match_run:status') === 'running') {
                usleep(500000);
                $events = Cache::get('match_run:events', []);
                foreach (array_slice($events, $i) as $event) {
                    echo "id: {$i}\ndata: " . json_encode($event) . "\n\n";
                    ob_flush();
                    flush();
                    $i++;
                }
            }

            // Send any events written after the final status check
            $events = Cache::get('match_run:events', []);
            foreach (array_slice($events, $i) as $event) {
                echo "id: {$i}\ndata: " . json_encode($event) . "\n\n";
                ob_flush();
                flush();
                $i++;
            }

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }
}
