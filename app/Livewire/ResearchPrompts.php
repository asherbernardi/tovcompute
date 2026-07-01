<?php

namespace App\Livewire;

use App\Models\QualitativeFrameworkScore;
use App\Models\ResearchPrompt;
use Livewire\Component;

class ResearchPrompts extends Component
{
    public string $name    = '';
    public string $content = '';
    public bool $showForm  = false;

    public string $backfillPromptId = '';
    public bool $backfillDone       = false;
    public int $backfillCount        = 0;

    protected $rules = [
        'name'    => 'required|string|max:255|unique:research_prompts,name',
        'content' => 'required|string',
    ];

    public function save(): void
    {
        $this->validate();

        ResearchPrompt::create([
            'name'    => trim($this->name),
            'content' => trim($this->content),
        ]);

        $this->name      = '';
        $this->content   = '';
        $this->showForm  = false;
        $this->backfillDone = false;
    }

    public function backfill(): void
    {
        if (!$this->backfillPromptId) return;

        $this->backfillCount = QualitativeFrameworkScore::whereNull('prompt_id')
            ->update(['prompt_id' => $this->backfillPromptId]);

        $this->backfillDone = true;
    }

    public function render()
    {
        return view('livewire.research-prompts', [
            'prompts' => ResearchPrompt::orderByDesc('created_at')->get(),
        ]);
    }
}
