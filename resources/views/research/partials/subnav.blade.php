<nav class="flex space-x-1 mb-8 border-b border-gray-200">
    @php
        $tabs = [
            'research.scores'  => 'Scores',
            'research.prompts' => 'Prompts',
            'research.runs'    => 'Runs',
        ];
    @endphp
    @foreach ($tabs as $routeName => $label)
        <a href="{{ route($routeName) }}"
           class="px-5 py-2 text-sm font-medium rounded-t-lg border-b-2 transition-colors
                  {{ request()->routeIs($routeName)
                       ? 'border-indigo-600 text-indigo-700 bg-indigo-50'
                       : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            {{ $label }}
        </a>
    @endforeach
</nav>
