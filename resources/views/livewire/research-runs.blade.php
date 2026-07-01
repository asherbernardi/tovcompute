<div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Track agent research executions.</p>
        <button wire:click="$set('showForm', true)"
                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            + New Run
        </button>
    </div>

    {{-- Create run form --}}
    @if ($showForm)
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 mb-4">New Research Run</h3>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prompt</label>
                <select wire:model="newPromptId"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— select —</option>
                    @foreach ($prompts as $prompt)
                        <option value="{{ $prompt->id }}">{{ $prompt->name }}</option>
                    @endforeach
                </select>
                @error('newPromptId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                <select wire:model="newModel"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="claude-sonnet-4-6">Claude Sonnet 4.6</option>
                    <option value="claude-opus-4-8">Claude Opus 4.8</option>
                    <option value="claude-haiku-4-5-20251001">Claude Haiku 4.5</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Target Type</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" wire:model="newTargetType" value="tickers"> Tickers (manual)
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" wire:model="newTargetType" value="list"> Saved List
                </label>
            </div>
        </div>

        @if ($newTargetType === 'tickers')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tickers <span class="text-gray-400 text-xs">(comma-separated)</span></label>
            <input wire:model="newTickers" type="text" placeholder="e.g. ODFL, EXPD, MOG-A"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        @else
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">List</label>
            <select wire:model="newListId"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">— select list —</option>
                @foreach ($lists as $list)
                    <option value="{{ $list->id }}">{{ $list->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="flex gap-3">
            <button wire:click="createRun"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                Create Run
            </button>
            <button wire:click="$set('showForm', false)"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                Cancel
            </button>
        </div>
    </div>
    @endif

    {{-- Runs table --}}
    @if ($runs->isEmpty())
        <p class="text-sm text-gray-500 text-center py-12">No runs yet.</p>
    @else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">ID</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Prompt</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Model</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Target</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Scores</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Cost</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($runs as $run)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 text-xs">#{{ $run['id'] }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $run['prompt_name'] }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $run['model'] }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate">{{ $run['target'] ?: '—' }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">{{ $run['score_count'] }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">
                        {{ $run['cost'] !== null ? '$' . number_format($run['cost'], 2) : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $statusColors = [
                                'pending'   => 'bg-gray-100 text-gray-600',
                                'running'   => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'failed'    => 'bg-red-100 text-red-700',
                            ];
                            $statusColor = $statusColors[$run['status']] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ ucfirst($run['status']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ date('M j, Y', $run['created_at']) }}</td>
                    <td class="px-4 py-3">
                        @if ($run['log'])
                        <button wire:click="viewRun({{ $run['id'] }})"
                                class="text-xs text-indigo-600 hover:text-indigo-800">
                            {{ $viewingRunId === $run['id'] ? 'Hide' : 'Log' }}
                        </button>
                        @endif
                    </td>
                </tr>
                @if ($viewingRunId === $run['id'])
                <tr>
                    <td colspan="9" class="px-4 py-4 bg-gray-900">
                        <pre class="text-xs text-green-400 font-mono whitespace-pre-wrap leading-relaxed max-h-96 overflow-y-auto">{{ $run['log'] }}</pre>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
