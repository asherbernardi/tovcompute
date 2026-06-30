    <table id="charity-table" class="table-auto min-w-full bg-white shadow-md rounded-lg">
        <thead class="bg-gray-100">
            <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EIN</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
            </tr>
        </thead>
        <tbody id="charity-table-body" hx-get="{{ route('charities.index') }}" hx-trigger="loadContacts from:body">
        @foreach($charity as $c)
        <tr class="border-b last:border-0 hover:bg-gray-50">
        <td class="px-6 py-4 text-gray-700"><a href="{{ route('charities.show', $c->id) }}" class="text-blue-600 hover:text-blue-800">{{ $c->name }}</a></td>
        <td class="px-6 py-4 text-gray-700">{{ $c->ein }}</td>
        <td class="px-6 py-4 text-gray-700">{{ $c->company->ticker ?? '' }}</td>
        <td class="px-6 py-4 text-gray-700">{{ $c->updated }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>