<div>
    {{-- Top controls bar --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">

        {{-- Model filter --}}
        <select wire:model.live="modelFilter"
                class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">All Models</option>
            @foreach($models as $model)
                <option value="{{ $model }}">{{ $model }}</option>
            @endforeach
        </select>

        {{-- Prompt filter --}}
        @if($prompts->count())
        <select wire:model.live="promptFilter"
                class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">All Prompts</option>
            @foreach($prompts as $prompt)
                <option value="{{ $prompt->id }}">{{ $prompt->name }}</option>
            @endforeach
        </select>
        @endif

        {{-- Date filter --}}
        <div class="flex items-center gap-1.5">
            <label class="text-sm text-gray-500 whitespace-nowrap">Scores from</label>
            <input type="date" wire:model.live="dateFrom"
                   class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
            @if($dateFrom)
                <button wire:click="$set('dateFrom', '')" class="text-gray-400 hover:text-gray-600 text-lg leading-none" title="Clear">&times;</button>
            @endif
        </div>

        {{-- Preset loader --}}
        @if($presets->count())
        <select wire:model.live="activePresetId"
                class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">Load preset…</option>
            @foreach($presets as $preset)
                <option value="{{ $preset->id }}">{{ $preset->name }}</option>
            @endforeach
        </select>
        @endif

        <button wire:click="resetWeights"
                class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-50">
            Reset Weights
        </button>

        <button wire:click="$set('showSaveModal', true)"
                class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
            Save Preset
        </button>

        {{-- Preset count badge --}}
        @if($presets->count())
        <div class="flex gap-1">
            @foreach($presets as $preset)
            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                {{ $preset->name }}
                <button wire:click="deletePreset({{ $preset->id }})"
                        class="text-gray-400 hover:text-red-500 leading-none"
                        title="Delete preset">×</button>
            </span>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Save preset modal --}}
    @if($showSaveModal)
    <div class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-80">
            <h3 class="font-semibold text-gray-900 mb-4">Save Weighting Preset</h3>
            <input wire:model="presetName"
                   type="text"
                   placeholder="Preset name"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400"
                   autofocus />
            <div class="flex gap-2 justify-end">
                <button wire:click="$set('showSaveModal', false)"
                        class="px-4 py-2 text-sm border border-gray-300 rounded hover:bg-gray-50">
                    Cancel
                </button>
                <button wire:click="savePreset"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                    Save
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Main layout: sliders left, table right --}}
    <div class="flex gap-6 items-start">

        {{-- Weight sliders --}}
        <div class="w-64 shrink-0 bg-white shadow-md rounded-lg p-5">
            <h3 class="font-semibold text-gray-800 mb-4 text-sm uppercase tracking-wide">Category Weights</h3>

            @foreach($categoryLabels as $key => $label)
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700">{{ $label }}</span>
                    <span class="font-medium text-gray-900">{{ $weights[$key] }}</span>
                </div>
                <input type="range" min="0" max="10" step="1"
                       wire:model.lazy="weights.{{ $key }}"
                       class="w-full accent-blue-600" />
            </div>
            @endforeach

            <div class="mt-2 pt-3 border-t border-gray-100 text-xs text-gray-500 text-center">
                Composite = weighted average (0–10)
            </div>
        </div>

        {{-- Rankings table --}}
        <div class="flex-1 overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-3 text-left text-gray-600 font-medium">#</th>
                        <th class="px-3 py-3 text-left text-gray-600 font-medium">Ticker</th>
                        <th class="px-3 py-3 text-left text-gray-600 font-medium">Score</th>
                        @foreach($shortLabels as $key => $label)
                        <th class="px-3 py-3 text-left text-gray-600 font-medium whitespace-nowrap">{{ $label }}</th>
                        @endforeach
                        <th class="px-3 py-3 text-left text-gray-600 font-medium">Model</th>
                        <th class="px-3 py-3 text-left text-gray-600 font-medium">Date</th>
                        <th class="px-3 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rankings as $i => $row)
                    <tr wire:click="selectCompany({{ $row['company_id'] }}, '{{ $row['ticker'] }}')"
                        class="cursor-pointer hover:bg-blue-50 transition-colors {{ $selectedCompanyId === $row['company_id'] ? 'bg-blue-50' : '' }}">
                        <td class="px-3 py-2 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-3 py-2 font-medium text-gray-900" title="{{ $row['company_name'] }}">
                            {{ $row['ticker'] }}
                        </td>
                        <td class="px-3 py-2 font-semibold text-blue-700">
                            {{ number_format($row['composite'], 1) }}
                        </td>
                        @foreach($shortLabels as $key => $label)
                        <td class="px-3 py-2 text-gray-600">
                            {{ $row['avg_scores'][$key] !== null ? number_format($row['avg_scores'][$key], 1) : '—' }}
                        </td>
                        @endforeach
                        <td class="px-3 py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $row['badge_class'] }}">
                                {{ $row['source'] }}
                            </span>
                            @if($row['score_count'] > 1)
                            <span class="text-xs text-gray-400 ml-1">({{ $row['score_count'] }})</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-500 whitespace-nowrap">
                            {{ date('M j, Y', $row['created_at']) }}
                        </td>
                        <td class="px-3 py-2" @click.stop>
                            <button @click.stop="$wire.viewScoreDetail({{ $row['company_id'] }})"
                                    title="View score details"
                                    class="text-gray-300 hover:text-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 5 + count($shortLabels) }}" class="px-3 py-8 text-center text-gray-400">
                            No scores found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Score detail modal --}}
    <div wire:ignore
         x-data="{
             visible: false,
             ticker: '',
             companyName: '',
             scores: [],
             idx: 0,
             notesOpen: false,
             pricing: [
                 { match: 'claude-opus-4',      input: 15,   output: 75   },
                 { match: 'claude-sonnet-4',    input: 3,    output: 15   },
                 { match: 'claude-haiku-4',     input: 0.8,  output: 4    },
                 { match: 'gpt-4o-mini',        input: 0.15, output: 0.6  },
                 { match: 'gpt-4o',             input: 2.5,  output: 10   },
                 { match: 'gpt-4',              input: 10,   output: 30   },
                 { match: 'o3-mini',            input: 1.1,  output: 4.4  },
                 { match: 'o1',                 input: 15,   output: 60   },
                 { match: 'o3',                 input: 10,   output: 40   },
             ],
             costFor(source, inputTokens, outputTokens) {
                 const s = (source || '').toLowerCase();
                 const tier = this.pricing.find(p => s.includes(p.match));
                 if (!tier || (!inputTokens && !outputTokens)) return null;
                 return ((inputTokens * tier.input + outputTokens * tier.output) / 1_000_000).toFixed(2);
             },
             get current() {
                 return this.scores[this.idx] || { source: '', badge_class: '', date: '', summary: '', red_flags: [], notes: '', research_duration: 0, input_tokens: 0, output_tokens: 0, feature_scores: [] };
             },
             show(detail) {
                 this.ticker       = detail.ticker;
                 this.companyName  = detail.company_name;
                 this.scores       = detail.scores;
                 this.idx          = 0;
                 this.notesOpen    = false;
                 this.visible      = true;
             }
         }"
         x-on:show-score-detail.window="show($event.detail)"
         x-on:keydown.escape.window="visible = false">

        <div x-show="visible"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="visible = false"
             class="fixed inset-0 bg-black bg-opacity-40 z-40"></div>

        <div x-show="visible"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-start justify-center p-6 pointer-events-none">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl pointer-events-auto flex flex-col my-6"
                 style="max-height: calc(100vh - 3rem)">

                {{-- Header --}}
                <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100 shrink-0">
                    <div>
                        <h3 class="font-bold text-xl text-gray-900" x-text="ticker"></h3>
                        <p class="text-sm text-gray-500 mt-0.5" x-text="companyName"></p>
                    </div>
                    <div class="flex items-center gap-3 ml-4">
                        <select x-show="scores.length > 1"
                                @change="idx = +$event.target.value"
                                class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <template x-for="(s, i) in scores" :key="i">
                                <option :value="i" :selected="i === idx" x-text="s.date + ' — ' + s.source"></option>
                            </template>
                        </select>
                        <button @click="visible = false"
                                class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                    </div>
                </div>

                {{-- Scrollable body --}}
                <div x-show="scores.length > 0" class="overflow-y-auto px-6 py-5 space-y-6">

                    {{-- Meta row --}}
                    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                              :class="current.badge_class"
                              x-text="current.source"></span>
                        <span x-text="current.date"></span>
                        <span>·</span>
                        <span x-text="Math.round(current.research_duration) + 's'"></span>
                        <span>·</span>
                        <span x-text="current.input_tokens.toLocaleString() + ' in / ' + current.output_tokens.toLocaleString() + ' out tokens'"></span>
                        <template x-if="costFor(current.source, current.input_tokens, current.output_tokens)">
                            <span x-text="'· ~$' + costFor(current.source, current.input_tokens, current.output_tokens)"></span>
                        </template>
                    </div>

                    {{-- Summary --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2 text-sm uppercase tracking-wide">Summary</h4>
                        <p class="text-gray-700 text-sm leading-relaxed" x-text="current.summary"></p>
                    </div>

                    {{-- Red flags --}}
                    <div x-show="current.red_flags && current.red_flags.length > 0">
                        <h4 class="font-semibold text-red-700 mb-2 text-sm uppercase tracking-wide">🚩 Red Flags</h4>
                        <ul class="space-y-1">
                            <template x-for="flag in current.red_flags" :key="flag">
                                <li class="flex gap-2 text-sm text-red-600">
                                    <span class="shrink-0">•</span>
                                    <span x-text="flag"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    {{-- Category scores --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 text-sm uppercase tracking-wide">Category Scores</h4>
                        <div class="space-y-3">
                            <template x-for="fs in current.feature_scores" :key="fs.key">
                                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-medium text-gray-800 text-sm" x-text="fs.label"></span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                                  :class="{
                                                      'bg-green-100 text-green-700': fs.confidence === 'HIGH',
                                                      'bg-yellow-100 text-yellow-700': fs.confidence === 'MEDIUM' || fs.confidence === 'MED',
                                                      'bg-red-100 text-red-700': fs.confidence === 'LOW',
                                                      'bg-gray-100 text-gray-500': !['HIGH','MEDIUM','MED','LOW'].includes(fs.confidence)
                                                  }"
                                                  x-text="fs.confidence"></span>
                                            <span class="font-bold text-blue-700 text-sm" x-text="fs.score.toFixed(1)"></span>
                                        </div>
                                    </div>
                                    <ul x-show="fs.evidence && fs.evidence.length > 0" class="mt-2 space-y-1">
                                        <template x-for="(item, itemIdx) in fs.evidence" :key="itemIdx">
                                            <li class="flex gap-2 text-xs text-gray-600">
                                                <span class="text-gray-400 shrink-0">•</span>
                                                <span x-text="item"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Full notes (collapsible) --}}
                    <div x-show="current.notes">
                        <button @click="notesOpen = !notesOpen"
                                class="flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-gray-900">
                            <span x-text="notesOpen ? '▼' : '▶'" class="text-xs"></span>
                            Full Research Notes
                        </button>
                        <pre x-show="notesOpen"
                             class="mt-3 text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-x-auto whitespace-pre-wrap leading-relaxed"
                             x-text="current.notes"></pre>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- Time series chart — wire:ignore keeps Alpine state across Livewire re-renders --}}
    <div wire:ignore
         x-data="{
             visible: false,
             ticker: '',
             chart: null,
             showChart(detail) {
                 this.visible = true;
                 this.ticker = detail.ticker;
                 this.$nextTick(() => {
                     const ctx = document.getElementById('scoreChart');
                     if (!ctx) return;
                     if (this.chart) this.chart.destroy();
                     this.chart = new Chart(ctx, {
                         type: 'line',
                         data: {
                             labels: detail.history.map(d => d.date),
                             datasets: [{
                                 label: 'Composite Score',
                                 data: detail.history.map(d => d.composite),
                                 borderColor: '#3b82f6',
                                 backgroundColor: 'rgba(59,130,246,0.08)',
                                 borderWidth: 2,
                                 pointRadius: 5,
                                 pointBackgroundColor: '#3b82f6',
                                 tension: 0.3,
                                 fill: true,
                             }]
                         },
                         options: {
                             responsive: true,
                             scales: {
                                 y: { min: 0, max: 10, ticks: { stepSize: 1 } }
                             },
                             plugins: {
                                 legend: { display: false },
                                 tooltip: { callbacks: {
                                     label: ctx => 'Score: ' + ctx.parsed.y.toFixed(1)
                                 }}
                             }
                         }
                     });
                 });
             }
         }"
         x-on:show-chart.window="showChart($event.detail)"
         x-on:hide-chart.window="visible = false"
         x-on:keydown.escape.window="visible = false">

        {{-- Backdrop --}}
        <div x-show="visible"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="visible = false"
             class="fixed inset-0 bg-black bg-opacity-40 z-40"></div>

        {{-- Modal --}}
        <div x-show="visible"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center p-6 pointer-events-none">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl pointer-events-auto">
                <div class="flex justify-between items-center px-6 pt-5 pb-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 text-lg"
                        x-text="ticker + ' — Score History'"></h3>
                    <button @click="visible = false"
                            class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                </div>
                <div class="p-6">
                    <canvas id="scoreChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
