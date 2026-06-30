<!-- resources/views/charities/edit.blade.php -->

@extends('layout.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-4">Edit Charity: {{ $charity->name }}</h1>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Edit Charity Details -->
        <form method="POST" action="{{ route('charities.update', $charity) }}" class="max-w-md mx-auto mb-8">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Charity Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $charity->name) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Charity EIN</label>
                <input type="number" name="ein" id="ein" value="{{ old('ein', $charity->ein) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('ein') border-red-500 @enderror">
                @error('ein')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Update Charity
                </button>
            </div>
        </form>

        
        <!-- Current Company -->
        @if ($charity->company)
        <div>
            <h2 class="text-xl font-semibold mb-4">Associated Company</h2>
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
                        
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $charity->company->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $charity->company->ticker ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('charities.remove-company', $charity) }}" method="POST" class="inline-block" onsubmit="return confirm('Remove {{ $charity->company->name }} from this charity?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
@endsection