<h2 class="text-2xl font-semibold text-gray-900 mb-4">Giving History for All Charities Above</h2>
    <table class="border-collapse table-auto w-full text-left">
        <thead class="text-gray-700 uppercase bg-gray-100 ">
            <tr>
                <th>Tax Year</th>
                <th>Grants Paid</th>
                <th>Net Assets</th>
                <th>Total Contributions</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
        @foreach($history as $h)
            <tr>
                <td class="border-b p-2">{{ $h->tax_year }}</td>
                <td class="border-b p-2">{{ Number::currency($h->sumgrants ?? 0) }}</td>
                <td class="border-b p-2">{{ Number::currency($h->sumassets ?? 0) }}</td>
                <td class="border-b p-2">{{ Number::currency($h->sumcontrib ?? 0) }}</td>
                <td class="border-b p-2">{{ Number::currency($h->sumrevenue ?? 0) }}</td>
            </tr>
        @endforeach
        <tfoot>
            <tr>
                <td class="border-b p-2"><strong>TOTALS</strong></td>
                <td class="border-b p-2"><strong>{{  Number::currency($totals->sumgrants ?? 0) }}</strong></td>
                <td class="border-b p-2"><strong>{{  Number::currency($totals->sumassets ?? 0) }}</strong></td>
                <td class="border-b p-2"><strong>{{  Number::currency($totals->sumcontrib ?? 0) }}</strong></td>
                <td class="border-b p-2"><strong>{{  Number::currency($totals->sumrevenue ?? 0) }}</strong></td>
            </tr>
            </tfoot>
        </tbody>
    </table>

    <!-- Export to CSV Button -->
    <div class="mt-6 flex justify-center">
            @if ($history && $history->isNotEmpty())
                <form method="POST" action="{{ route('companies.export.history', $company->id) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Export History to CSV
                    </button>
                </form>
            @endif
        </div>
 