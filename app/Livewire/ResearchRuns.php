<?php

namespace App\Livewire;

use App\Jobs\StartResearchRun;
use App\Models\CompanyGrouping;
use App\Models\ResearchPrompt;
use App\Models\ResearchRun;
use App\Models\ResearchRunItem;
use Livewire\Component;

class ResearchRuns extends Component
{
    public string $newPromptId    = '';
    public string $newModel       = '';
    public string $newMode        = 'live';
    public string $newTargetType  = 'tickers';
    public string $newListId      = '';
    public string $newTickers     = '';
    public bool $showForm         = false;

    public ?int $viewingRunId = null;

    protected function rules(): array
    {
        return [
            'newPromptId'   => 'required|exists:research_prompts,id',
            'newModel'      => 'required|in:' . implode(',', array_keys(config('research.models'))),
            'newMode'       => 'required|in:live,batch',
            'newTargetType' => 'required|in:list,tickers',
            'newListId'     => 'required_if:newTargetType,list',
            'newTickers'    => 'required_if:newTargetType,tickers',
        ];
    }

    public function mount(): void
    {
        $this->newModel = config('research.default_model');
    }

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

        $run = ResearchRun::create([
            'prompt_id'      => $this->newPromptId,
            'model'          => $this->newModel,
            'mode'           => $this->newMode,
            'status'         => 'pending',
            'target_type'    => $this->newTargetType,
            'target_list_id' => $listId,
            'target_tickers' => $tickers,
        ]);

        StartResearchRun::dispatch($run->id);

        $this->reset(['newPromptId', 'newMode', 'newTargetType', 'newListId', 'newTickers', 'showForm']);
        $this->newModel = config('research.default_model');
        $this->viewingRunId = $run->id;
    }

    public function cancelRun(int $id): void
    {
        $run = ResearchRun::find($id);
        if (!$run || !$run->isActive()) return;

        $run->update(['status' => 'cancelled', 'completed_at' => time()]);
        $run->appendLog('Run cancelled. Companies already in progress stop after their current turn.');

        // Items not yet with a worker or in a batch can be cancelled right away;
        // running and submitted ones are cancelled by their job.
        $run->items()->whereIn('status', ['pending', 'queued'])->get()
            ->each(fn (ResearchRunItem $item) => $item->finish('cancelled'));
    }

    public function viewRun(int $id): void
    {
        $this->viewingRunId = $this->viewingRunId === $id ? null : $id;
    }

    public function render()
    {
        $runs = ResearchRun::with(['prompt', 'scores', 'items'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($run) {
                return [
                    'id'          => $run->id,
                    'prompt_name' => optional($run->prompt)->name ?? '—',
                    'model'       => config("research.models.{$run->model}.label", $run->model),
                    'mode'        => $run->mode,
                    'status'      => $run->status,
                    'active'      => $run->isActive(),
                    'done'        => $run->items->filter->isTerminal()->count(),
                    'total'       => $run->items->count(),
                    'items'       => $run->items,
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
            'models'  => config('research.models'),
            'polling' => $runs->contains('active', true),
        ]);
    }
}
