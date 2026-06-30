<!-- resources/views/lists/edit.blade.php -->

@extends('layout.app')
@section('title')Edit List {{ $list->name }} @endsection
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
                    Update List
                </button>
            </div>
        </form>

        <!-- Add Companies -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Add Companies to List</h2>
            <form method="POST" action="{{ route('lists.add-companies', $list) }}" class="max-w-md mx-auto">
                @csrf

                <div class="mb-4">
                    <label for="company_ids" class="block text-sm font-medium text-gray-700">Select Companies</label>
                    <select name="company_ids[]" id="company_ids" multiple
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('company_ids') border-red-500 @enderror">
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" {{ $list->companies->contains($company->id) ? 'disabled' : '' }}>
                                {{ $company->name }} ({{ $company->ticker ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('company_ids')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Add Companies
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Companies -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Companies in List</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticker</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($list->companies as $company)
                            <tr>
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

    <a href="{{ url()->previous() }}" class="mt-6 inline-block bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">Back</a>
@endsection