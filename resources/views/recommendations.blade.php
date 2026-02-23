<!-- resources/views/recommendations.blade.php -->

@extends('layout.app')
@section('title')Charity Recommendations @endsection
@section('content')

    <div class="">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Charity Association Recommendations</h1>
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Pending Recommendations</h3>
                    @if ($recommendations->isEmpty())
                        <p class="text-gray-600">No recommendations available at this time.</p>
                    @else

                    <!-- Search Input -->
                    <div class="mb-4 max-w-md">
                        <input type="text" id="recommendation-search" placeholder="Search by charity, company, ticker, or date..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200" id="recommendations-table">
                                <thead class="bg-gray-100">
                                    <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company (ticker)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Charity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suggested Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match %</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($recommendations as $recommendation)
                                        <tr class="recommendation-row" 
                                            data-charity="{{ strtolower($recommendation->charity->name ?? 'Unknown Charity') }}" 
                                            data-company="{{ strtolower($recommendation->company->name ?? 'Unknown Company') }}" 
                                            data-date="{{ strtolower(date('Y-m-d', $recommendation->suggested_date)) }}"
                                            data-ticker="{{ strtolower($recommendation->company->ticker) }}">
                                             <td class="px-6 py-4">{{ $recommendation->company->name ?? 'Unknown Company' }} ({{ $recommendation->company->ticker ?? '' }})</td>
                                             <td class="px-6 py-4">{{ $recommendation->charity->name ?? 'Unknown Charity' }}</td>
                                           
                                             <td class="px-6 py-4 text-s">{{ date('Y-m-d', $recommendation->suggested_date) }}</td>
                                            <td class="px-6 py-4">{{ number_format($recommendation->match, 2) }}%</td>
                                            
                                            <td class="px-6 py-4 flex space-x-2">
                                                @if (!$recommendation->confirm_date)
                                                    <form action="{{ route('recommendations.approve', $recommendation) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('recommendations.disapprove', $recommendation) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700">
                                                            Disapprove
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-500">Action Taken</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Links -->
                        <div class="mt-6">
                            {{ $recommendations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side Search Script -->
    <script>
        document.getElementById('recommendation-search').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#recommendations-table .recommendation-row');

            rows.forEach(row => {
                const charity = row.getAttribute('data-charity');
                const company = row.getAttribute('data-company');
                const date = row.getAttribute('data-date');
                const ticker = row.getAttribute('data-ticker');

                const matches = charity.includes(searchTerm) || company.includes(searchTerm) || ticker.includes(searchTerm) || date.includes(searchTerm);
                row.style.display = matches ? '' : 'none';
            });
        });
    </script>
@endsection