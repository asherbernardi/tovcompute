<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\CharityRecommend;

class MatchingService
{
    private array $skipWords = [
        'trust', 'fund', 'group', 'reit', 'index', 'etf', 'etp', 'lp', 'sp',
        'global', 'international', 'national', 'american', 'united', 'new',
        'corp', 'pharmaceuticals', 'foundation',
    ];

    public function filterString(string $input): string
    {
        $wordsToRemove = ['Inc', 'LTD', 'LLC', 'Co', 'Corp', 'Com', 'The', 'An', 'and'];
        $patternWords = '/\b(?:' . implode('|', $wordsToRemove) . ')\b/i';
        $input = preg_replace($patternWords, '', $input);
        $input = preg_replace('/[^\w\s]/u', '', $input);
        return trim($input);
    }

    public function searchName(string $searchName): array
    {
        $searchName = trim($searchName);
        $searchParts = explode(' ', $searchName);

        $query = DB::table('charity')->select('id', 'name', 'ein');
        $query->whereRaw('SOUNDEX(name) = SOUNDEX(?)', [$searchName]);

        foreach ($searchParts as $part) {
            $part = trim($part);
            if (!empty($part) && !in_array(strtolower($part), $this->skipWords)) {
                $query->orWhere('name', 'LIKE', "%{$part}%");
            }
        }

        $results = $query->limit(100)->get();

        // Build matches and deduplicate by EIN, keeping the highest match %
        $byEin = [];
        foreach ($results as $row) {
            $distance = levenshtein($searchName, $row->name);
            $maxLen = max(strlen($searchName), strlen($row->name));
            $pct = round(($maxLen - $distance) / $maxLen * 100, 2);

            if (!isset($byEin[$row->ein]) || $pct > $byEin[$row->ein]['match_percentage']) {
                $byEin[$row->ein] = [
                    'name'             => $row->name,
                    'id'               => $row->id,
                    'match_percentage' => $pct,
                ];
            }
        }

        return array_values($byEin);
    }

    public function saveMatchRecommendation(int $companyID, int $charityID, float $matchPercentage): void
    {
        CharityRecommend::firstOrCreate(
            ['companyID' => $companyID, 'charityID' => $charityID],
            ['match' => $matchPercentage, 'suggested_date' => time()]
        );
    }

    public function deduplicateRecommendations(): int
    {
        return DB::delete('
            DELETE cr FROM charity_recommend cr
            JOIN charity c ON cr.charityID = c.id
            WHERE cr.id NOT IN (
                SELECT min_id FROM (
                    SELECT MIN(cr2.id) AS min_id
                    FROM charity_recommend cr2
                    JOIN charity c2 ON cr2.charityID = c2.id
                    GROUP BY cr2.companyID, c2.ein
                ) AS keep_ids
            )
        ');
    }

    public function getUnmatchedCompanies()
    {
        return Company::orderBy('id', 'asc')
            ->whereNotIn('id', function ($query) {
                $query->select('companyID')->distinct()->from('charity_recommend');
            })->get();
    }
}
