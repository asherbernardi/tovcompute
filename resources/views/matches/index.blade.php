@extends('layout.app')
@section('title', 'Run Matches')
@section('content')

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Run Matching</h1>
                <div class="flex items-center gap-4">
                    <span id="status-badge" class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                        Idle
                    </span>
                    <button id="stop-btn"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 hidden"
                        onclick="stopMatching()">
                        Stop
                    </button>
                    <button id="start-btn"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        onclick="startMatching()">
                        Start Matching
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <button onclick="deduplicate(this)"
                    class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 disabled:opacity-50">
                    Remove Duplicate Recommendations
                </button>
                <span id="dedup-result" class="ml-2 text-sm text-gray-500"></span>
            </div>

            <p class="text-sm text-gray-500 mb-6">
                Processes all companies that have not yet been matched. Results are saved to the recommendations table automatically.
                If you close this page, matching will continue running on the server until complete — reopen this page to reconnect.
            </p>

            <div id="results" class="space-y-1 font-mono text-sm max-h-[60vh] overflow-y-auto border border-gray-200 rounded-md p-4 bg-gray-50 hidden">
            </div>

            <div id="summary" class="mt-4 hidden">
            </div>

        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let eventSource = null;
let matchingDone = false;

const btn = document.getElementById('start-btn');
const stopBtn = document.getElementById('stop-btn');
const badge = document.getElementById('status-badge');
const results = document.getElementById('results');
const summary = document.getElementById('summary');

function setRunningState() {
    btn.disabled = true;
    stopBtn.classList.remove('hidden');
    badge.textContent = 'Running…';
    badge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700';
    results.classList.remove('hidden');
    summary.classList.add('hidden');
}

function setFinishedState(badgeText, badgeClass, summaryHtml) {
    matchingDone = true;
    btn.disabled = false;
    stopBtn.classList.add('hidden');
    badge.textContent = badgeText;
    badge.className = 'px-3 py-1 rounded-full text-sm font-medium ' + badgeClass;
    summary.innerHTML = summaryHtml;
    summary.classList.remove('hidden');
    if (eventSource) eventSource.close();
}

function setDoneState(count) {
    const msg = count === 0
        ? 'No unmatched companies found.'
        : count + ' recommendation' + (count !== 1 ? 's' : '') + ' saved.';
    setFinishedState('Done', 'bg-green-100 text-green-700',
        '<p class="text-sm font-medium text-green-700">' + msg +
        ' <a href="/recommendations" class="underline">View recommendations →</a></p>');
}

function setStoppedState(count) {
    const msg = 'Stopped. ' + count + ' recommendation' + (count !== 1 ? 's' : '') + ' saved so far.';
    setFinishedState('Stopped', 'bg-gray-100 text-gray-600',
        '<p class="text-sm font-medium text-gray-600">' + msg +
        ' <a href="/recommendations" class="underline">View recommendations →</a></p>');
}

function appendEvent(data) {
    if (data.type === 'company') {
        const el = document.createElement('div');
        el.className = 'mt-3 font-semibold text-gray-700';
        el.textContent = data.name + (data.ticker ? ' (' + data.ticker + ')' : '');
        results.appendChild(el);
        results.scrollTop = results.scrollHeight;
    }

    if (data.type === 'match') {
        const el = document.createElement('div');
        const pct = parseFloat(data.percent);
        const color = pct >= 80 ? 'text-green-700' : pct >= 60 ? 'text-yellow-700' : 'text-gray-500';
        el.className = 'ml-4 ' + color;
        el.textContent = '→ ' + data.charity + ' — ' + pct.toFixed(2) + '%';
        results.appendChild(el);
        results.scrollTop = results.scrollHeight;
    }

    if (data.type === 'done') {
        setDoneState(data.count);
    }

    if (data.type === 'stopped') {
        setStoppedState(data.count);
    }
}

function openStream(offset) {
    if (eventSource) eventSource.close();
    matchingDone = false;

    eventSource = new EventSource('{{ route("match.stream") }}?offset=' + offset);

    eventSource.onmessage = function (e) {
        appendEvent(JSON.parse(e.data));
    };

    eventSource.onerror = function () {
        if (matchingDone) return;
        eventSource.close();
        btn.disabled = false;
        badge.textContent = 'Error';
        badge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700';
        const el = document.createElement('div');
        el.className = 'text-red-600 mt-2';
        el.textContent = 'Connection lost. Check the Laravel log for details.';
        results.appendChild(el);
    };
}

async function deduplicate(btn) {
    btn.disabled = true;
    const result = document.getElementById('dedup-result');
    result.textContent = 'Running…';
    const res = await fetch('{{ route("match.deduplicate") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    const data = await res.json();
    result.textContent = data.deleted + ' duplicate' + (data.deleted !== 1 ? 's' : '') + ' removed.';
    btn.disabled = false;
}

async function stopMatching() {
    stopBtn.disabled = true;
    stopBtn.textContent = 'Stopping…';
    await fetch('{{ route("match.stop") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
}

async function startMatching() {
    setRunningState();

    const res = await fetch('{{ route("match.start") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    const data = await res.json();

    if (data.status === 'already_running') {
        // A run is in progress — connect to it
    }

    openStream(0);
}

// On page load, check if a run is already in progress or just completed
(async function init() {
    const res = await fetch('{{ route("match.status") }}');
    const data = await res.json();

    if (data.status === 'running') {
        setRunningState();
        results.classList.remove('hidden');
        openStream(0);
    } else if (data.status === 'done' || data.status === 'stopped') {
        results.classList.remove('hidden');
        openStream(0);
    }
})();
</script>
@endsection
