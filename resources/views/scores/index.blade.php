@extends('layout.app')

@section('title', 'Research Scores')

@section('content')
<div class="container mx-auto px-6 pb-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Research Scores</h1>
    @livewire('score-dashboard')
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection
