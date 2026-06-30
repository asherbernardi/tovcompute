<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Services\MatchingService;

class RunMatches extends Command
{
    protected $signature = 'match:run';
    protected $description = 'Run charity matching for all unmatched companies';

    public function handle(MatchingService $matcher): int
    {
        Cache::put('match_run:status', 'running', now()->addHours(24));

        $companies = $matcher->getUnmatchedCompanies();
        $saved = 0;

        if ($companies->isEmpty()) {
            $this->appendEvent(['type' => 'done', 'count' => 0]);
            Cache::put('match_run:status', 'done', now()->addHours(24));
            return 0;
        }

        foreach ($companies as $company) {
            if (Cache::get('match_run:stop')) {
                Cache::forget('match_run:stop');
                $this->appendEvent(['type' => 'stopped', 'count' => $saved]);
                Cache::put('match_run:status', 'stopped', now()->addHours(24));
                return 0;
            }

            $name = $matcher->filterString($company->name);
            $this->appendEvent(['type' => 'company', 'name' => $company->name, 'ticker' => $company->ticker]);

            foreach ($matcher->searchName($name) as $match) {
                $matcher->saveMatchRecommendation($company->id, $match['id'], $match['match_percentage']);
                $this->appendEvent([
                    'type'    => 'match',
                    'charity' => $match['name'],
                    'id'      => $match['id'],
                    'percent' => $match['match_percentage'],
                ]);
                $saved++;
            }
        }

        $this->appendEvent(['type' => 'done', 'count' => $saved]);
        Cache::put('match_run:status', 'done', now()->addHours(24));

        return 0;
    }

    private function appendEvent(array $event): void
    {
        $events = Cache::get('match_run:events', []);
        $events[] = $event;
        Cache::put('match_run:events', $events, now()->addHours(24));
    }
}
