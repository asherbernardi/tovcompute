<?php

namespace App\Livewire;

use App\Models\CompanyGrouping;
use App\Models\ResearchPrompt;
use App\Models\ResearchRun;
use Livewire\Component;

class ResearchRuns extends Component
{
    public string $newPromptId    = '';
    public string $newModel       = 'claude-sonnet-4-6';
    public string $newTargetType  = 'tickers';
    public string $newListId      = '';
    public string $newTickers     = '';
    public bool $showForm         = false;

    public ?int $viewingRunId = null;

    protected $rules = [
        'newPromptId'   => 'required|exists:research_prompts,id',
        'newModel'      => 'required|string',
        'newTargetType' => 'required|in:list,tickers',
    ];

    public function createRun(): void
    {
        $this->validate();

        $tickers = null;
        $listId  = null;

        if ($this->newTargetType === 'tickers') {
            $tickers = array_filter(array_map('trim', explode(',', $this->newTickers)));
        } else {
            $listId = $this->newListId ?: null;
        }

        ResearchRun::create([
            'prompt_id'      => $this->newPromptId,
            'model'          => $this->newModel,
            'status'         => 'pending',
            'target_type'    => $this->newTargetType,
            'target_list_id' => $listId,
            'target_tickers' => $tickers,
        ]);

        $this->reset(['newPromptId', 'newModel', 'newTargetType', 'newListId', 'newTickers', 'showForm']);
        $this->newModel = 'claude-sonnet-4-6';
        $this->newTargetType = 'tickers';
    }

    public function viewRun(int $id): void
    {
        $this->viewingRunId = $this->viewingRunId === $id ? null : $id;
    }

    public function render()
    {
        $runs = ResearchRun::with(['prompt', 'scores'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($run) {
                return [
                    'id'          => $run->id,
                    'prompt_name' => optional($run->prompt)->name ?? '—',
                    'model'       => $run->model,
                    'status'      => $run->status,
                    'target'      => $run->target_type === 'list'
                        ? ('List #' . $run->target_list_id)
                        : implode(', ', $run->target_tickers ?? []),
                    'score_count' => $run->scores->count(),
                    'cost'        => $run->costEstimate(),
                    'duration'    => $run->durationSeconds(),
                    'log'         => $run->log,
                    'created_at'  => $run->created_at,
                ];
            });

        return view('livewire.research-runs', [
            'runs'    => $runs,
            'prompts' => ResearchPrompt::orderByDesc('created_at')->get(),
            'lists'   => CompanyGrouping::orderBy('name')->get(),
        ]);
    }
}
