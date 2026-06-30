<!-- resources/views/company-groupings/index.blade.php -->

@extends('layout.app')
@section('title')All Company Groupings @endsection
@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Company Groupings</h1>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4">
            <a href="{{ route('company-groupings.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Create New Grouping
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($groupings as $grouping)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $grouping->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('company-groupings.edit', $grouping) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('company-groupings.destroy', $grouping) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure you want to delete this grouping?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-gray-500">No groupings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
