@extends('layout.app')
@section('title')Company {{ $company->name }} @endsection
@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        @if(!$company || !$company->id)
            <p class="text-red-600">Error: Company data is missing or invalid!</p>
        @else
            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $company->name }}</h1>
            
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-2xl text-gray-700"><strong class="font-semibold">Ticker:</strong> {{ $company->ticker }}</h2>
                    <p class="text-gray-700">@if($company->ein)<strong class="font-semibold">EIN:</strong> {{ $company->ein }} |@endif <strong class="font-semibold">SECID:</strong> {{ $company->secID }}</p>
                    <p class="text-gray-700"><strong class="font-semibold">Last Updated:</strong> {{ $company->updated }}</p>
                    <a href="{{ route('companies.edit', ['company' => $company->id]) }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Edit Company</a>
                    <form action="{{ route('companies.destroy', $company->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700 transition-colors" 
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                </div>
            </div>

            <!-- Associate Charities Section (always shown) -->
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Associate Charities</h3>
                    <div class="flex items-center space-x-2 mb-4">
                        <input type="text" id="charity-search" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Search for charities to associate...">
                        <button id="charity-search-btn" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Search</button>
                    </div>
                    <div id="search-results" class="mb-4 overflow-y-scroll" style="max-height:400px;"></div>
                </div>
            </div>

            <!-- Associated Charities Section (shown only if charities exist) -->
            @if($company->charities && !$company->charities->isEmpty())
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Associated Charities</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white shadow-md rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                                    <a href="{{ route('companies.show', ['company' => $company->id, 'sort' => 'name', 'direction' => ($sort === 'name' && $direction === 'asc') ? 'desc' : 'asc', 'search' => $search]) }}" class="hover:text-blue-600">
                                        Charity Name
                                        @if($sort === 'name')
                                            <span class="ml-1 inline-block">
                                                {{ $direction === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @endif
                                    </a>
                                </th>

                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                                    <a href="{{ route('companies.show', ['company' => $company->id, 'sort' => 'ein', 'direction' => ($sort === 'ein' && $direction === 'asc') ? 'desc' : 'asc', 'search' => $search]) }}" class="hover:text-blue-600">
                                        EIN
                                        @if($sort === 'ein')
                                            <span class="ml-1 inline-block">
                                                {{ $direction === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @endif
                                    </a>
                                </th>

                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                                    <a href="{{ route('companies.show', ['company' => $company->id, 'sort' => 'updated', 'direction' => ($sort === 'updated' && $direction === 'asc') ? 'desc' : 'asc', 'search' => $search]) }}" class="hover:text-blue-600">
                                        Last Updated
                                        @if($sort === 'updated')
                                            <span class="ml-1 inline-block">
                                                {{ $direction === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        @endif
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->charities as $charity)
                                <tr class="border-b last:border-0 hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-700"><a href="{{ url('charities/' . $charity->id) }}">{{ $charity->name }}</a></td>
                                    <td class="px-6 py-4 text-gray-700">{{ $charity->ein }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $charity->updated }}</td> 
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                
    <hr class="mb-10 mt-10" />
    @include("charities.partials.givingtotals")

    @endif            
            <a href="{{ url()->previous() }}" class="mt-6 inline-block bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">Back</a>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const $searchInput = $('#charity-search');
            const $searchBtn = $('#charity-search-btn');
            const $resultsDiv = $('#search-results');
            const companyId = {{ $company->id ?? 'null' }};
            const token = '{{ csrf_token() }}';

            if (!companyId) {
                console.error('Company ID is missing!');
                $resultsDiv.html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Error: Company ID is missing</div>');
                return;
            }

            $searchBtn.on('click', searchCharities);
            $searchInput.on('keypress', function(e) {
                if (e.which === 13) searchCharities();
            });

            function searchCharities() {
                const searchTerm = $searchInput.val().trim();
                if (!searchTerm) return;

                $.ajax({
                    url: '{{ route("companies.search-charity", [$company->id ?? "missing"]) }}',
                    method: 'POST',
                    data: JSON.stringify({ charity_name: searchTerm }),
                    contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': token },
                    success: function(data) {
                        displayResults(data.charities);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        $resultsDiv.html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Error searching charities</div>');
                    }
                });
            }

            function displayResults(charities) {
                if (!charities || charities.length === 0) {
                    $resultsDiv.html('<p class="text-gray-600">No charities found.</p>');
                    return;
                }

                let html = '<table class="min-w-full bg-white shadow-md rounded-lg"><thead class="bg-gray-100"><tr><th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Name</th><th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Current Company</th><th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Action</th></tr></thead><tbody>';
                $.each(charities, function(i, charity) {
                    html += `
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">${charity.name}</td>
                            <td class="px-4 py-3 text-gray-700">${charity.current_company || 'None'}</td>
                            <td class="px-4 py-3">
                                ${!charity.current_company ? 
                                    `<button class="associate-btn bg-green-600 text-white px-3 py-1 rounded-md hover:bg-green-700 transition-colors" 
                                            data-id="${charity.id}">Associate</button>` : 
                                    '<span class="text-gray-500">Already Assigned</span>'}
                            </td>
                        </tr>`;
                });
                html += '</tbody></table>';
                $resultsDiv.html(html);

                $('.associate-btn').on('click', function() {
                    associateCharity($(this).data('id'));
                });
            }

            function associateCharity(charityId) {
                $.ajax({
                    url: '{{ route("companies.associate-charity", [$company->id ?? "missing"]) }}',
                    method: 'POST',
                    data: JSON.stringify({ charity_id: charityId }),
                    contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': token },
                    success: function(data) {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Failed to associate charity');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        alert('Error associating charity');
                    }
                });
            }
        });
    </script>
@endsection