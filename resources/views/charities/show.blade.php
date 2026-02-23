@extends('layout.app')
@section('title')
    {{$charity->name}} Charity
@endsection
@section('content')
<?php
    if($charity->parent != "")
    {
       // $parent = "Parent Company: <a href="{{ url('company/'.$charity->company->id) }}">" . $charity->company->name . "</a>";
       $parent = "Parent Company: ".$charity->company->name;
    }
    else { $parent = ""; }

?>
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold mb-4">Charity: {{ $charity->name }}</h1>
    @if($parent)<h2 class="text-xl font-semibold mb-4"><a href='{{ url("companies/" . $charity->parent) }}'>{{ $parent }}</a> </h2>@endif
    <h3 class="font-bold mb-4">EIN: {{ $charity->ein }} </h3>

    <div class="p-6">
    <a href="{{ route('charities.edit', ['charity' => $charity->id]) }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Edit Charity</a>
</div>
   
    @if(count($charity->history)>0)
    <table class="min-w-full bg-white border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Year</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grants Paid</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Assets</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Contributions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        @foreach($charity->history as $history)
            <tr class="company-row">
                <td class="px-6 py-4 whitespace-nowrap">{{ $history->tax_year }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ Number::format($history->grants_paid ?? 0)  }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ Number::currency($history->net_assets ?? 0) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ Number::currency($history->total_contrib ?? 0) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ Number::currency($history->total_revenue ?? 0) }}</td>
            </tr>
        @endforeach
            <tfoot>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap"><strong>TOTALS</strong></td>
                <td class="px-6 py-4 whitespace-nowrap"><strong>{{  $totals->sumgrants !== null ? Number::format($totals->sumgrants) : '' }}</strong></td>
                <td class="px-6 py-4 whitespace-nowrap"><strong>{{  $totals->sumassets !== null ? Number::currency($totals->sumassets) : '' }}</strong></td>
                <td class="px-6 py-4 whitespace-nowrap"><strong>{{  $totals->sumcontrib !== null ? Number::currency($totals->sumcontrib) : '' }}</strong></td>
                <td class="px-6 py-4 whitespace-nowrap"><strong>{{  $totals->sumrevenue !== null ? Number::currency($totals->sumrevenue) : ''  }}</strong></td>
            </tr>
            </tfoot>        
        </tbody>
    </table>

    <!-- Export to CSV Button -->
    <div class="mt-6 flex justify-center">
            @if ($charity->history)
                <form method="POST" action="{{ route('charities.export.history', $charity->id) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Export History to CSV
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>

@endsection