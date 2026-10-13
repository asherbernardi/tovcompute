<div @if ($polling) wire:poll.5s @endif>

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
                    @foreach ($models as $id => $model)
                        <option value="{{ $id }}">{{ $model['label'] }} (${{ number_format($model['input'], 0) }}/${{ number_format($model['output'], 0) }} per MTok)</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" wire:model="newMode" value="live"> Live
                    <span class="text-gray-400 text-xs">parallel, results as each finishes</span>
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" wire:model="newMode" value="batch"> Batch
                    <span class="text-gray-400 text-xs">50% cheaper, usually under an hour, up to 24h</span>
                </label>
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
            @error('newTickers') <p class="mt-1 text-xs text-red-600">Enter at least one ticker.</p> @enderror
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
            @error('newListId') <p class="mt-1 text-xs text-red-600">Select a list.</p> @enderror
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
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $run['model'] }} <span class="text-gray-400">· {{ $run['mode'] }}</span></td>
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
                                'cancelled' => 'bg-yellow-100 text-yellow-700',
                            ];
                            $statusColor = $statusColors[$run['status']] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ ucfirst($run['status']) }}
                        </span>
                        @if ($run['total'])
                            <span class="ml-1 text-xs text-gray-400">{{ $run['done'] }}/{{ $run['total'] }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ date('M j, Y', $run['created_at']) }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-right">
                        @if ($run['active'])
                        <button wire:click="cancelRun({{ $run['id'] }})"
                                wire:confirm="Cancel this run? Companies already in progress stop after their current turn."
                                class="text-xs text-red-600 hover:text-red-800 mr-3">
                            Cancel
                        </button>
                        @endif
                        <button wire:click="viewRun({{ $run['id'] }})"
                                class="text-xs text-indigo-600 hover:text-indigo-800">
                            {{ $viewingRunId === $run['id'] ? 'Hide' : 'Details' }}
                        </button>
                    </td>
                </tr>
                @if ($viewingRunId === $run['id'])
                <tr>
                    <td colspan="9" class="px-4 py-4 bg-gray-50">
                        @if ($run['items']->isNotEmpty())
                        <table class="w-full text-xs mb-4 bg-white border border-gray-200 rounded-lg">
                            <thead class="text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="text-left px-3 py-2">Ticker</th>
                                    <th class="text-left px-3 py-2">Status</th>
                                    <th class="text-right px-3 py-2">Turns</th>
                                    <th class="text-right px-3 py-2">Searches</th>
                                    <th class="text-right px-3 py-2">Tokens in / out</th>
                                    <th class="text-left px-3 py-2">Error</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($run['items'] as $item)
                                <tr>
                                    <td class="px-3 py-2 font-mono">{{ $item->ticker }}</td>
                                    <td class="px-3 py-2">{{ $item->status }}</td>
                                    <td class="px-3 py-2 text-right">{{ $item->iterations }}</td>
                                    <td class="px-3 py-2 text-right">{{ $item->web_searches }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($item->input_tokens + $item->cache_read_tokens + $item->cache_write_tokens) }} / {{ number_format($item->output_tokens) }}</td>
                                    <td class="px-3 py-2 text-red-600">{{ $item->error }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                        @if ($run['log'])
                        <pre class="text-xs text-green-400 bg-gray-900 rounded-lg p-4 font-mono whitespace-pre-wrap leading-relaxed max-h-96 overflow-y-auto">{{ $run['log'] }}</pre>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
