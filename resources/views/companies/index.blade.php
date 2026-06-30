@extends('layout.app')
@section('title')All Companies @endsection
@section('content')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Companies</h1>
        
    <a href="{{ route('companies.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors mb-6">Add New Company</a>


        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="w-full md:w-1/2 mb-4 md:mb-0">
        <form method="GET" action="{{ route('companies.index') }}" class="flex space-x-2">
            <input type="text" name="keyword" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   value="{{ $keyword ?? '' }}" 
                   placeholder="Search by name or ticker...">
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Search
            </button>
        </form>
    </div>
    <div class="w-full md:w-1/2 mb-4 md:mb-0 flex flex-col md:flex-row md:justify-between">

            <div class="w-full md:w-1/2 mr-4">
            <form method="GET" action="{{ route('companies.index') }}" class="flex space-x-2">
                            <input type="hidden" name="keyword" value="{{ $keyword ?? '' }}">
                            <input type="hidden" name="list_id" value="{{ $selectedList ?? '' }}">
                            <select name="list_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    onchange="this.form.submit()">
                                <option value="" {{ !$selectedList ? 'selected' : '' }}>All Groupings</option>
                                @foreach ($groupings as $grouping)
                                    <option value="{{ $grouping->id }}" {{ $selectedList == $grouping->id ? 'selected' : '' }}>
                                        {{ $grouping->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
        </div>
        <div class="w-full md:w-1/2">
                        @if ($selectedList)
                        <form method="POST" action="{{ route('companies.export.charity-history') }}">
                            @csrf
                            <!-- Pass current filters as hidden inputs -->
                            <input type="hidden" name="keyword" value="{{ $keyword ?? '' }}">
                            <input type="hidden" name="list_id" value="{{ $selectedList ?? '' }}">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Export Charity History to CSV
                            </button>
                        </form>
                        @endif
        </div>
    </div>
</div>


        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-6">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-6">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <a href="{{ route('companies.index', array_merge($request->query(), [
                                'sort_by' => 'ticker',
                                'sort_dir' => $sortBy === 'ticker' && $sortDir === 'asc' ? 'desc' : 'asc'
                            ])) }}" 
                               class="hover:text-blue-600 {{ $sortBy === 'ticker' ? 'text-blue-600' : '' }}">
                                Ticker
                                @if ($sortBy === 'ticker')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('companies.index', array_merge($request->query(), [
                                'sort_by' => 'name',
                                'sort_dir' => $sortBy === 'name' && $sortDir === 'asc' ? 'desc' : 'asc'
                            ])) }}" 
                               class="hover:text-blue-600 {{ $sortBy === 'name' ? 'text-blue-600' : '' }}">
                                Name
                                @if ($sortBy === 'name')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('companies.index', array_merge($request->query(), [
                                'sort_by' => 'list_name',
                                'sort_dir' => $sortBy === 'list_name' && $sortDir === 'asc' ? 'desc' : 'asc'
                            ])) }}" 
                               class="hover:text-blue-600 {{ $sortBy === 'list_name' ? 'text-blue-600' : '' }}">
                                List Name
                                @if ($sortBy === 'list_name')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-700"><a href="{{ route('companies.show', $company->id) }}" class="text-blue-600 hover:text-blue-800">{{ $company->ticker }}</a></td>
                            <td class="px-6 py-4 text-gray-700">
                                <a href="{{ route('companies.show', $company->id) }}" class="text-blue-600 hover:text-blue-800">{{ $company->name }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $company->list_name ?? '' }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $company->updated }}</td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-gray-600 text-center">No companies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-4">
            {{ $companies->links() }}
        </div>
        </div>
    </div>
@endsection