<div x-data="{ expanded: null }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">Prompts are immutable once created.</p>
        </div>
        <button wire:click="$set('showForm', true)"
                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            + New Prompt
        </button>
    </div>

    {{-- Create form --}}
    @if ($showForm)
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 mb-4">New Prompt</h3>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-gray-400 text-xs">(unique, permanent)</span></label>
            <input wire:model="name" type="text" placeholder="e.g. v1-qfs-research"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Prompt Content</label>
            <textarea wire:model="content" rows="10" placeholder="Enter the full prompt text..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button wire:click="save"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                Save Prompt
            </button>
            <button wire:click="$set('showForm', false)"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                Cancel
            </button>
        </div>
    </div>
    @endif

    {{-- Backfill section --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6">
        <h3 class="text-sm font-semibold text-amber-800 mb-2">Backfill Unlinked Scores</h3>
        <p class="text-xs text-amber-700 mb-3">Assign a prompt to all existing scores that have no prompt linked yet.</p>

        @if ($backfillDone)
            <p class="text-sm text-green-700 font-medium">Linked {{ $backfillCount }} score(s) to the selected prompt.</p>
        @else
            <div class="flex items-center gap-3">
                <select wire:model.live="backfillPromptId"
                        class="border border-amber-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-amber-500 focus:border-amber-500">
                    <option value="">— select prompt —</option>
                    @foreach ($prompts as $prompt)
                        <option value="{{ $prompt->id }}">{{ $prompt->name }}</option>
                    @endforeach
                </select>
                <button wire:click="backfill"
                        @if(!$backfillPromptId) disabled @endif
                        class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 disabled:opacity-40 disabled:cursor-not-allowed">
                    Backfill
                </button>
            </div>
        @endif
    </div>

    {{-- Prompts list --}}
    @forelse ($prompts as $prompt)
    <div class="bg-white border border-gray-200 rounded-xl mb-3 shadow-sm overflow-hidden">
        <button class="w-full text-left px-5 py-4 flex items-center justify-between hover:bg-gray-50"
                @click="expanded = (expanded === {{ $prompt->id }}) ? null : {{ $prompt->id }}">
            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold text-gray-900 font-mono">{{ $prompt->name }}</span>
                <span class="text-xs text-gray-400">{{ $prompt->scores()->count() }} score(s) linked</span>
                <span class="text-xs text-gray-400">{{ date('M j, Y', $prompt->created_at) }}</span>
            </div>
            <svg :class="expanded === {{ $prompt->id }} ? 'rotate-180' : ''"
                 class="w-4 h-4 text-gray-400 transition-transform"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="expanded === {{ $prompt->id }}" x-cloak class="border-t border-gray-100 px-5 py-4 bg-gray-50">
            <pre class="text-xs text-gray-700 whitespace-pre-wrap font-mono leading-relaxed">{{ $prompt->content }}</pre>
        </div>
    </div>
    @empty
        <p class="text-sm text-gray-500 text-center py-12">No prompts yet. Create one above.</p>
    @endforelse

</div>
