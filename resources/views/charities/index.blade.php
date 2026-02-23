@extends('layout.app')
@section('title')
    All Charities
@endsection
@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
<h1 class="text-3xl font-bold text-gray-900 mb-6">Charities</h1>

<?php /*
<div>
#@if($charity)
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>EIN</th>
                <th>Parent</th>
                <th>Updated</th>
            </tr>
        </thead>
        <tbody>
        @foreach($charity as $c)
            <tr>
                <td><a href="/charity/{{ $c->id }}">{{ $c->name }}</a></td>
                <td>{{ $c->ein }}</td>
                <td>{{ $c->parent }}</td>
                <td>{{ date("Y-m-d",$c->updated) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @endif
</div>

*/ ?>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="w-full md:w-1/2 mb-4 md:mb-0">
        <form method="GET" action="{{ route('charities.index') }}" class="flex space-x-2">
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
</div>
<div id="table-container" hx-get="{{ route('charities.index') }}" hx-trigger="loadCharity from:body">
 
@include("charities.partials.table")
 
    <div id="pagination-links" class="p-3" 
        hx-boost="true" 
        hx-target="#table-container">  
        {{ $charity->appends(array("q" => Request::get('q')))->links()  }}  
    </div>  
 
</div>
@endsection
</div>