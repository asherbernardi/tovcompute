@extends('layout.app')

@section('title', 'Research Prompts')

@section('content')
<div class="container mx-auto px-6 pb-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">AI Research</h1>
    @include('research.partials.subnav')
    @livewire('research-prompts')
</div>
@endsection
