<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class DocsController extends Controller
{
    public function __invoke()
    {
        $base = rtrim(config('app.url'), '/') . '/api/companies/{ticker}/scores';

        return response()->json([
            'description' => 'Anson Analytics Score API. Allows an AI agent to create, read, update, and delete qualitative framework scores for companies. All requests must include an Authorization header with a Bearer token.',
            'authentication' => [
                'type'   => 'Bearer token',
                'header' => 'Authorization: Bearer <token>',
            ],
            'notes' => [
                'Companies are identified by ticker symbol (case-insensitive).',
                'Scores include one or more feature_scores as nested objects.',
                'created_at is a Unix integer timestamp, set automatically on create.',
                'score is a decimal between 0.0 and 10.0 with one decimal place.',
                'confidence must be exactly one of: HIGH, MED, LOW.',
                'evidence is a JSON array of strings supporting the score.',
                'red_flags is an optional array of strings. Omit or pass null to have none.',
                'Updating feature_scores replaces all existing feature scores for that framework score.',
            ],
            'endpoints' => [
                [
                    'method'      => 'GET',
                    'url'         => $base,
                    'description' => 'List all qualitative framework scores for a company, including their feature scores.',
                    'request'     => null,
                    'response'    => [
                        'status' => 200,
                        'body'   => [
                            [
                                'id'         => 1,
                                'company_id' => 42,
                                'created_at' => 1751234567,
                                'source'     => 'gpt-4o run 2026-06-30',
                                'summary'    => 'Strong governance with consistent charitable giving history. Leadership has been stable for over a decade.',
                                'red_flags'  => ['Key person risk'],
                                'feature_scores' => [
                                    [
                                        'id'                             => 1,
                                        'qualitative_framework_score_id' => 1,
                                        'category'                       => 'governance',
                                        'score'                          => '7.5',
                                        'confidence'                     => 'HIGH',
                                        'evidence'                       => ['Board has 8 independent directors', 'Clawback policy in place'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'method'      => 'POST',
                    'url'         => $base,
                    'description' => 'Create a new qualitative framework score with feature scores for a company.',
                    'request'     => [
                        'source'     => 'gpt-4o run 2026-06-30',
                        'summary'    => 'Strong governance with consistent charitable giving history. Leadership has been stable for over a decade.',
                        'red_flags'  => ['Key person risk'],
                        'feature_scores' => [
                            [
                                'category'   => 'governance',
                                'score'      => 7.5,
                                'confidence' => 'HIGH',
                                'evidence'   => ['Board has 8 independent directors', 'Clawback policy in place'],
                            ],
                            [
                                'category'   => 'charitable_giving',
                                'score'      => 9.0,
                                'confidence' => 'HIGH',
                                'evidence'   => ['Gave $4.2M in 2024', 'Giving has grown 12% YoY for 5 years'],
                            ],
                            [
                                'category'   => 'esg',
                                'score'      => 6.0,
                                'confidence' => 'MED',
                                'evidence'   => ['Published sustainability report', 'No third-party audit yet'],
                            ],
                        ],
                    ],
                    'response' => [
                        'status' => 201,
                        'body'   => [
                            'id'         => 1,
                            'company_id' => 42,
                            'created_at' => 1751234567,
                            'source'     => 'gpt-4o run 2026-06-30',
                            'summary'    => 'Strong governance with consistent charitable giving history. Leadership has been stable for over a decade.',
                            'red_flags'  => ['Key person risk'],
                            'feature_scores' => [
                                [
                                    'id'                             => 1,
                                    'qualitative_framework_score_id' => 1,
                                    'category'                       => 'governance',
                                    'score'                          => '7.5',
                                    'confidence'                     => 'HIGH',
                                    'evidence'                       => ['Board has 8 independent directors', 'Clawback policy in place'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'method'      => 'GET',
                    'url'         => $base . '/{id}',
                    'description' => 'Get a single qualitative framework score by its ID.',
                    'request'     => null,
                    'response'    => [
                        'status' => 200,
                        'body'   => [
                            'id'         => 1,
                            'company_id' => 42,
                            'created_at' => 1751234567,
                            'source'     => 'gpt-4o run 2026-06-30',
                            'summary'    => 'Strong governance with consistent charitable giving history.',
                            'red_flags'  => ['Key person risk'],
                            'feature_scores' => [
                                [
                                    'id'                             => 1,
                                    'qualitative_framework_score_id' => 1,
                                    'category'                       => 'governance',
                                    'score'                          => '7.5',
                                    'confidence'                     => 'HIGH',
                                    'evidence'                       => ['Board has 8 independent directors'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'method'      => 'PUT',
                    'url'         => $base . '/{id}',
                    'description' => 'Update an existing score. All fields are optional — only include what you want to change. If feature_scores is provided, it replaces ALL existing feature scores.',
                    'request'     => [
                        'summary'    => 'Revised analysis following Q2 earnings.',
                        'red_flags'  => ['Key person risk', 'Margin compression in Q2'],
                        'feature_scores' => [
                            [
                                'category'   => 'governance',
                                'score'      => 8.0,
                                'confidence' => 'HIGH',
                                'evidence'   => ['Board has 8 independent directors', 'New CFO hire strengthens team'],
                            ],
                        ],
                    ],
                    'response' => [
                        'status' => 200,
                        'body'   => [
                            'id'         => 1,
                            'company_id' => 42,
                            'created_at' => 1751234567,
                            'source'     => 'gpt-4o run 2026-06-30',
                            'summary'    => 'Revised analysis following Q2 earnings.',
                            'red_flags'  => ['Key person risk', 'Margin compression in Q2'],
                            'feature_scores' => [
                                [
                                    'id'                             => 3,
                                    'qualitative_framework_score_id' => 1,
                                    'category'                       => 'governance',
                                    'score'                          => '8.0',
                                    'confidence'                     => 'HIGH',
                                    'evidence'                       => ['Board has 8 independent directors', 'New CFO hire strengthens team'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'method'      => 'DELETE',
                    'url'         => $base . '/{id}',
                    'description' => 'Delete a qualitative framework score and all its feature scores.',
                    'request'     => null,
                    'response'    => [
                        'status' => 204,
                        'body'   => null,
                    ],
                ],
            ],
            'error_responses' => [
                ['status' => 401, 'body' => ['error' => 'Unauthorized'],    'reason' => 'Missing or invalid Bearer token'],
                ['status' => 404, 'body' => ['error' => 'Company not found: ACME'], 'reason' => 'Ticker does not exist in the database'],
                ['status' => 404, 'body' => ['error' => 'Score not found'], 'reason' => 'Score ID does not exist or belongs to a different company'],
                ['status' => 422, 'body' => ['message' => 'The score field must be between 0 and 10.', 'errors' => ['feature_scores.0.score' => ['The score field must be between 0 and 10.']]], 'reason' => 'Validation failure — check the errors object for field-level detail'],
            ],
        ]);
    }
}
