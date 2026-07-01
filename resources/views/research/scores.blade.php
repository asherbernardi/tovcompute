@extends('layout.app')

@section('title', 'Research Scores')

@section('content')
<div class="container mx-auto px-6 pb-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">AI Research</h1>
    @include('research.partials.subnav')
    @livewire('score-dashboard')
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection
