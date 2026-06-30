<!-- resources/views/lists/create.blade.php -->

@extends('layout.app')
@section('title')Create a New List @endsection
@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-4">Create New List</h1>

        <form method="POST" action="{{ route('lists.store') }}" class="max-w-md mx-auto">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">List Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Create List
                </button>
            </div>
        </form>
    </div>
@endsection