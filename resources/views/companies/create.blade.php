<!-- resources/views/companies/create.blade.php -->
@extends('layout.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New Company</h1>
        
        <form method="POST" action="{{ route('companies.store') }}" class="bg-white shadow-md rounded-lg p-6 max-w-lg">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                       id="name" name="name" value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="ticker" class="block text-sm font-medium text-gray-700 mb-1">Stock Ticker</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ticker') border-red-500 @enderror" 
                       id="ticker" name="ticker" value="{{ old('ticker') }}">
                @error('ticker')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="ein" class="block text-sm font-medium text-gray-700 mb-1">EIN</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ein') border-red-500 @enderror" 
                       id="ein" name="ein" value="{{ old('ein') }}">
                @error('ein')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="secID" class="block text-sm font-medium text-gray-700 mb-1">SEC ID</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('secID') border-red-500 @enderror" 
                       id="secID" name="secID" value="{{ old('secID') }}">
                @error('secID')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="formed" class="block text-sm font-medium text-gray-700 mb-1">Formation Date</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('formed') border-red-500 @enderror" 
                id="formed" name="formed" value="{{ old('formed') }}">
                @error('formed')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Create</button>
                <a href="{{ route('companies.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
@endsection
