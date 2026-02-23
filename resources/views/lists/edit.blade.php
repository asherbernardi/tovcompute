<!-- resources/views/lists/edit.blade.php -->

@extends('layout.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-4">Edit List: {{ $list->name }}</h1>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Edit List Name -->
        <form method="POST" action="{{ route('lists.update', $list) }}" class="max-w-md mx-auto mb-8">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">List Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $list->name) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Update List Name
                </button>
            </div>
        </form>

        <!-- Add Companies Section -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Add Companies to List</h2>

            <!-- Search Input -->
            <div class="mb-4 max-w-md mx-auto">
                <input type="text" id="company-search" placeholder="Search companies by name or ticker..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Companies Table -->
            <div class="overflow-x-auto overflow-y-scroll" style="height:300px;">
                <table class="min-w-full bg-white border border-gray-200" id="companies-table">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticker</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($companies as $company)
                            <tr class="company-row" data-name="{{ strtolower($company->name) }}" data-ticker="{{ strtolower($company->ticker ?? '') }}">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $company->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $company->ticker ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($list->companies->contains($company->id))
                                        <span class="text-gray-500">Already Associated</span>
                                    @else
                                        <form action="{{ route('lists.add-companies', $list) }}" method="POST" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="company_ids[]" value="{{ $company->id }}">
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700">
                                                Associate
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="mb-10 mt-10" />

        <!-- Current Companies -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Companies Currently in List</h2>
            <!-- Search Input -->
            <div class="mb-4 max-w-md mx-auto">
                <input type="text" id="current-company-search" placeholder="Search companies by name or ticker..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="overflow-x-auto overflow-y-scroll" style="height:300px;">
                <table class="min-w-full bg-white border border-gray-200" id="current-companies-table">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticker</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($list->companies as $company)
                            <tr  class="company-row" data-name="{{ strtolower($company->name) }}" data-ticker="{{ strtolower($company->ticker ?? '') }}">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $company->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $company->ticker ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('lists.remove-company', [$list, $company->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('Remove {{ $company->name }} from this list?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No companies in this list.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br /><br />

    <!-- Client-side Search Script -->
    <script>
        document.getElementById('company-search').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#companies-table .company-row');

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const ticker = row.getAttribute('data-ticker');
                const matches = name.includes(searchTerm) || ticker.includes(searchTerm);
                row.style.display = matches ? '' : 'none';
            });
        });

        document.getElementById('current-company-search').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#current-companies-table .company-row');

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const ticker = row.getAttribute('data-ticker');
                const matches = name.includes(searchTerm) || ticker.includes(searchTerm);
                row.style.display = matches ? '' : 'none';
            });
        });
    </script>
@endsection