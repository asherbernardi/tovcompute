<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\QualitativeFrameworkScore;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScoreController extends Controller
{
    private function findCompany(string $ticker): ?Company
    {
        return Company::where('ticker', strtoupper($ticker))->first();
    }

    /**
     * List all qualitative framework scores for a company.
     *
     * GET /api/companies/{ticker}/scores
     *
     * Response 200:
     * [
     *   {
     *     "id": 1,
     *     "company_id": 42,
     *     "created_at": 1751234567,
     *     "source": "gpt-4o run 2026-06-30",
     *     "summary": "Strong governance with consistent charitable giving...",
     *     "red_flags": ["Key person risk", "Litigation pending"],
     *     "feature_scores": [
     *       {
     *         "id": 1,
     *         "qualitative_framework_score_id": 1,
     *         "category": "governance",
     *         "score": "7.5",
     *         "confidence": "HIGH",
     *         "evidence": ["Board has 8 independent directors", "Clawback policy in place"]
     *       }
     *     ]
     *   }
     * ]
     */
    public function index(string $ticker)
    {
        $company = $this->findCompany($ticker);
        if (!$company) {
            return response()->json(['error' => "Company not found: {$ticker}"], 404);
        }

        return response()->json(
            $company->qualitativeFrameworkScores()->with('featureScores')->get()
        );
    }

    /**
     * Get a single qualitative framework score by ID.
     *
     * GET /api/companies/{ticker}/scores/{id}
     *
     * Response 200: same shape as a single element from the index response.
     * Response 404: { "error": "Score not found" }
     */
    public function show(string $ticker, int $id)
    {
        $company = $this->findCompany($ticker);
        if (!$company) {
            return response()->json(['error' => "Company not found: {$ticker}"], 404);
        }

        $score = QualitativeFrameworkScore::with('featureScores')
            ->where('company_id', $company->id)
            ->find($id);

        if (!$score) {
            return response()->json(['error' => 'Score not found'], 404);
        }

        return response()->json($score);
    }

    /**
     * Create a new qualitative framework score for a company.
     *
     * POST /api/companies/{ticker}/scores
     * Content-Type: application/json
     *
     * Request body:
     * {
     *   "source": "gpt-4o run 2026-06-30",         // required — identifies who/what created this score
     *   "summary": "Multi-paragraph analysis...",   // required — full qualitative summary
     *   "red_flags": [                              // optional — list of concern strings
     *     "Key person risk",
     *     "Litigation pending"
     *   ],
     *   "feature_scores": [                         // required — at least one feature score
     *     {
     *       "category": "governance",               // required — name of the feature being scored
     *       "score": 7.5,                           // required — decimal 0.0–10.0, one decimal place
     *       "confidence": "HIGH",                   // required — one of: HIGH, MED, LOW
     *       "evidence": [                           // required — at least one evidence string
     *         "Board has 8 independent directors",
     *         "Clawback policy in place"
     *       ]
     *     }
     *   ]
     * }
     *
     * Response 201: the created score with all feature_scores.
     * Response 422: validation errors.
     */
    public function store(Request $request, string $ticker)
    {
        $company = $this->findCompany($ticker);
        if (!$company) {
            return response()->json(['error' => "Company not found: {$ticker}"], 404);
        }

        $data = $request->validate([
            'source'                         => 'required|string',
            'summary'                        => 'required|string',
            'red_flags'                      => 'nullable|array',
            'red_flags.*'                    => 'string',
            'feature_scores'                 => 'required|array|min:1',
            'feature_scores.*.category'      => 'required|string',
            'feature_scores.*.score'         => 'required|numeric|min:0|max:10',
            'feature_scores.*.confidence'    => ['required', Rule::in(['HIGH', 'MED', 'LOW'])],
            'feature_scores.*.evidence'      => 'required|array|min:1',
            'feature_scores.*.evidence.*'    => 'string',
        ]);

        $score = $company->qualitativeFrameworkScores()->create([
            'source'     => $data['source'],
            'summary'    => $data['summary'],
            'red_flags'  => $data['red_flags'] ?? null,
            'created_at' => time(),
        ]);

        foreach ($data['feature_scores'] as $fs) {
            $score->featureScores()->create($fs);
        }

        return response()->json($score->load('featureScores'), 201);
    }

    /**
     * Update an existing qualitative framework score.
     *
     * PUT /api/companies/{ticker}/scores/{id}
     * Content-Type: application/json
     *
     * All fields are optional — only include what you want to change.
     * If feature_scores is included, it REPLACES all existing feature scores entirely.
     *
     * Request body:
     * {
     *   "source": "updated source",                 // optional
     *   "summary": "Revised analysis...",           // optional
     *   "red_flags": ["New concern"],               // optional — pass null to clear
     *   "feature_scores": [                         // optional — replaces ALL existing feature scores
     *     {
     *       "category": "governance",
     *       "score": 8.0,
     *       "confidence": "HIGH",
     *       "evidence": ["Updated evidence"]
     *     }
     *   ]
     * }
     *
     * Response 200: the updated score with all feature_scores.
     * Response 404: { "error": "Score not found" }
     * Response 422: validation errors.
     */
    public function update(Request $request, string $ticker, int $id)
    {
        $company = $this->findCompany($ticker);
        if (!$company) {
            return response()->json(['error' => "Company not found: {$ticker}"], 404);
        }

        $score = QualitativeFrameworkScore::where('company_id', $company->id)->find($id);
        if (!$score) {
            return response()->json(['error' => 'Score not found'], 404);
        }

        $data = $request->validate([
            'source'                         => 'sometimes|string',
            'summary'                        => 'sometimes|string',
            'red_flags'                      => 'nullable|array',
            'red_flags.*'                    => 'string',
            'feature_scores'                 => 'sometimes|array|min:1',
            'feature_scores.*.category'      => 'required_with:feature_scores|string',
            'feature_scores.*.score'         => 'required_with:feature_scores|numeric|min:0|max:10',
            'feature_scores.*.confidence'    => ['required_with:feature_scores', Rule::in(['HIGH', 'MED', 'LOW'])],
            'feature_scores.*.evidence'      => 'required_with:feature_scores|array|min:1',
            'feature_scores.*.evidence.*'    => 'string',
        ]);

        $score->update(array_filter([
            'source'    => $data['source'] ?? null,
            'summary'   => $data['summary'] ?? null,
            'red_flags' => array_key_exists('red_flags', $data) ? $data['red_flags'] : $score->red_flags,
        ], fn($v) => $v !== null));

        if (isset($data['feature_scores'])) {
            $score->featureScores()->delete();
            foreach ($data['feature_scores'] as $fs) {
                $score->featureScores()->create($fs);
            }
        }

        return response()->json($score->load('featureScores'));
    }

    /**
     * Delete a qualitative framework score and all its feature scores.
     *
     * DELETE /api/companies/{ticker}/scores/{id}
     *
     * Response 204: no content.
     * Response 404: { "error": "Score not found" }
     */
    public function destroy(string $ticker, int $id)
    {
        $company = $this->findCompany($ticker);
        if (!$company) {
            return response()->json(['error' => "Company not found: {$ticker}"], 404);
        }

        $score = QualitativeFrameworkScore::where('company_id', $company->id)->find($id);
        if (!$score) {
            return response()->json(['error' => 'Score not found'], 404);
        }

        $score->featureScores()->delete();
        $score->delete();

        return response()->json(null, 204);
    }
}
