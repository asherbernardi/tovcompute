<h1>Charity List @if($searchTerm) - matching "{{$searchTerm}}" @endif</h1>

<table id="charity-table" class="table-auto w-full">
        <thead>
            <tr>
                <th>Name</th>
                <th>EIN</th>
                <th>Parent</th>
            </tr>
        </thead>
        <tbody id="charity-table-body" hx-get="{{ route('charity.index') }}" hx-trigger="loadContacts from:body">
        @foreach($charity as $c)
            <tr>
                <td><a href="/associateCharity/{{ $company }}/{{ $c->id }}">{{ $c->name }}</a></td>
                <td>{{ $c->ein }}</td>
                <td>@if($c->parent) {{ $c->company->name }} @endif</td>
           </tr>
        @endforeach
        </tbody>
    </table>