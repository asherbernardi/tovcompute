<?php

namespace App\Livewire;

use App\Models\QualitativeFrameworkScore;
use App\Models\ResearchPrompt;
use App\Models\WeightingPreset;
use Illuminate\Support\Collection;
use Livewire\Component;

class ScoreDashboard extends Component
{
    public array $weights = [
        'leadership_character'           => 5,
        'culture_strength'               => 5,
        'employee_outcomes'              => 5,
        'customer_trust'                 => 5,
        'incentive_pay_equity_alignment' => 5,
        'operational_excellence'         => 5,
        'governance_reputation'          => 5,
    ];

    public string $modelFilter    = '';
    public string $promptFilter   = '';
    public string $dateFrom       = '';
    public ?int   $selectedCompanyId = null;
    public string $presetName    = '';
    public bool   $showSaveModal = false;
    public string $activePresetId = '';

    private const CATEGORY_LABELS = [
        'leadership_character'           => 'Leadership & Character',
        'culture_strength'               => 'Culture Strength',
        'employee_outcomes'              => 'Employee Outcomes',
        'customer_trust'                 => 'Customer Trust',
        'incentive_pay_equity_alignment' => 'Incentive Pay & Equity',
        'operational_excellence'         => 'Operational Excellence',
        'governance_reputation'          => 'Governance & Reputation',
    ];

    private const SHORT_LABELS = [
        'leadership_character'           => 'Leadership',
        'culture_strength'               => 'Culture',
        'employee_outcomes'              => 'Employees',
        'customer_trust'                 => 'Customers',
        'incentive_pay_equity_alignment' => 'Incentives',
        'operational_excellence'         => 'Operations',
        'governance_reputation'          => 'Governance',
    ];

    private const MODEL_COLORS = [
        'bg-blue-100 text-blue-800',
        'bg-green-100 text-green-800',
        'bg-purple-100 text-purple-800',
        'bg-amber-100 text-amber-800',
        'bg-rose-100 text-rose-800',
        'bg-teal-100 text-teal-800',
        'bg-pink-100 text-pink-800',
        'bg-indigo-100 text-indigo-800',
    ];

    public function updatedActivePresetId($value): void
    {
        if ($value) {
            $this->loadPreset((int) $value);
        }
        $this->activePresetId = '';
    }

    public function selectCompany(int $companyId, string $ticker): void
    {
        if ($this->selectedCompanyId === $companyId) {
            $this->selectedCompanyId = null;
            $this->dispatch('hide-chart');
            return;
        }

        $this->selectedCompanyId = $companyId;

        $history = QualitativeFrameworkScore::with('featureScores')
            ->where('company_id', $companyId)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($qfs) => [
                'date'      => date('M j, Y', $qfs->created_at),
                'composite' => round($this->computeComposite($qfs->featureScores), 2),
                'source'    => $qfs->source,
            ])
            ->values()
            ->toArray();

        $this->dispatch('show-chart', ticker: $ticker, history: $history);
    }

    public function savePreset(): void
    {
        $name = trim($this->presetName);
        if (! $name) return;

        WeightingPreset::create(['name' => $name, 'weights' => $this->weights]);

        $this->presetName    = '';
        $this->showSaveModal = false;
    }

    public function loadPreset(int $id): void
    {
        $preset = WeightingPreset::find($id);
        if ($preset) {
            $this->weights = $preset->weights;
        }
    }

    public function deletePreset(int $id): void
    {
        WeightingPreset::destroy($id);
    }

    public function viewScoreDetail(int $companyId): void
    {
        $company = \App\Models\Company::find($companyId);

        $scores = QualitativeFrameworkScore::with('featureScores')
            ->where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($qfs) {
                return [
                    'date'              => date('M j, Y', $qfs->created_at),
                    'source'            => $qfs->source,
                    'badge_class'       => $this->badgeClass($qfs->source),
                    'summary'           => $qfs->summary,
                    'red_flags'         => $qfs->red_flags ?? [],
                    'notes'             => $qfs->notes,
                    'research_duration' => $qfs->research_duration,
                    'input_tokens'      => $qfs->input_tokens,
                    'output_tokens'     => $qfs->output_tokens,
                    'feature_scores'    => collect(self::CATEGORY_LABELS)->map(function ($label, $key) use ($qfs) {
                        $fs = $qfs->featureScores->firstWhere('category', $key);
                        return [
                            'key'        => $key,
                            'label'      => $label,
                            'score'      => $fs ? (float) $fs->score : 0.0,
                            'confidence' => $fs ? $fs->confidence : '',
                            'evidence'   => $fs ? ($fs->evidence ?? []) : [],
                        ];
                    })->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();

        $this->dispatch('show-score-detail',
            ticker:       $company->ticker,
            company_name: $company->name,
            scores:       $scores,
        );
    }

    public function resetWeights(): void
    {
        $this->weights = array_fill_keys(array_keys($this->weights), 5);
    }

    private function computeComposite(Collection $featureScores): float
    {
        $totalWeight = array_sum($this->weights);
        if ($totalWeight === 0) return 0.0;

        $weighted = 0.0;
        foreach ($this->weights as $category => $weight) {
            $fs       = $featureScores->firstWhere('category', $category);
            $weighted += ($fs ? (float) $fs->score : 0.0) * $weight;
        }

        return $weighted / $totalWeight;
    }

    private function badgeClass(string $source): string
    {
        return self::MODEL_COLORS[abs(crc32($source)) % count(self::MODEL_COLORS)];
    }

    private function getRankings(): Collection
    {
        return QualitativeFrameworkScore::with(['company', 'featureScores'])
            ->when($this->modelFilter,  fn ($q) => $q->where('source', $this->modelFilter))
            ->when($this->promptFilter, fn ($q) => $q->where('prompt_id', $this->promptFilter))
            ->when($this->dateFrom,     fn ($q) => $q->where('created_at', '>=', strtotime($this->dateFrom)))
            ->get()
            ->groupBy('company_id')
            ->filter(fn ($group) => $group->first()->company !== null)
            ->map(function ($group) {
                $company  = $group->first()->company;
                $latest   = $group->sortByDesc('created_at')->first();
                $sources  = $group->pluck('source')->unique()->values();

                $composite = round(
                    $group->avg(fn ($qfs) => $this->computeComposite($qfs->featureScores)),
                    2
                );

                $avgScores = collect(array_keys($this->weights))->mapWithKeys(function ($category) use ($group) {
                    $vals = $group->map(function ($qfs) use ($category) {
                        $fs = $qfs->featureScores->firstWhere('category', $category);
                        return $fs ? (float) $fs->score : null;
                    })->filter(fn ($v) => $v !== null);
                    return [$category => $vals->isNotEmpty() ? round($vals->avg(), 1) : null];
                })->toArray();

                return [
                    'company_id'   => $company->id,
                    'ticker'       => $company->ticker,
                    'company_name' => $company->name,
                    'composite'    => $composite,
                    'score_count'  => $group->count(),
                    'source'       => $sources->count() === 1 ? $sources->first() : $sources->count() . ' models',
                    'badge_class'  => $sources->count() === 1 ? $this->badgeClass($sources->first()) : 'bg-gray-100 text-gray-600',
                    'created_at'   => $latest->created_at,
                    'avg_scores'   => $avgScores,
                ];
            })
            ->sortByDesc('composite')
            ->values();
    }

    public function render()
    {
        return view('livewire.score-dashboard', [
            'rankings'       => $this->getRankings(),
            'models'         => QualitativeFrameworkScore::distinct()->orderBy('source')->pluck('source')->toArray(),
            'prompts'        => ResearchPrompt::orderBy('name')->get(),
            'presets'        => WeightingPreset::orderBy('name')->get(),
            'categoryLabels' => self::CATEGORY_LABELS,
            'shortLabels'    => self::SHORT_LABELS,
        ]);
    }
}
