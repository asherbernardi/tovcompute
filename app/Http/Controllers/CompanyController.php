<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Html\FormFacade;
use Illuminate\Support\Facades\Response;
use App\Models\Company;
use App\Models\Charity;
use App\Models\CompanyGrouping;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function __construct(){
        $this->company = new Company();
    }

    /**
     * Display a listing of companies, needs to be sortable and searchable.
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $perPage = $request->input('per_page', 1000);
        $sortBy = $request->input('sort_by', 'name'); // Default sort by name
        $sortDir = $request->input('sort_dir', 'asc'); // Default ascending
        $selectedList = $request->input('list_id'); // Get selected list from dropdown

        // Fetch all groupings for the dropdown
        $groupings = CompanyGrouping::all();

        $companies = Company::select('company.*', 'company_groupings.name as list_name')
            ->leftJoin('company_grouping_associate', 'company.id', '=', 'company_grouping_associate.company_id')
            ->leftJoin('company_groupings', 'company_grouping_associate.company_grouping_id', '=', 'company_groupings.id')
            ->when($keyword, function ($query, $keyword) {
                return $query->where('company.name', 'like', "%{$keyword}%")
                            ->orWhere('company.ticker', 'like', "%{$keyword}%");
            })
            ->when($selectedList, function ($query, $selectedList) {
                return $query->where('company_grouping_associate.company_grouping_id', $selectedList);
            })
            ->orderBy($sortBy === 'list_name' ? 'company_groupings.name' : "company.{$sortBy}", $sortDir)
            ->distinct('company.id')->paginate($perPage);

        // Preserve all query parameters in pagination links
        $companies->appends([
            'keyword' => $keyword,
            'per_page' => $perPage,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
            'list_id' => $selectedList
        ]);

        return view('companies.index', compact('companies', 'keyword', 'sortBy', 'sortDir', 'request', 'groupings', 'selectedList'));
    }



    /*
    * @desc Show a specific company and their charities
     
    public function show(int $id)
    {
        #$totals = charity_giving::selectRaw("sum(grants_paid) as sumgrants,sum(total_contrib) as sumcontrib,sum(net_assets) as sumassets,sum(total_revenue) as sumrevenue")->where("charityID",$id)->first();
        $history = $this->company->history($id);
        $totals = $this->company->totals($id);

        return view('company.show', [
            'company' => company::with('charity')->findOrFail($id),
            'history' => $history,
            'totals' => $totals
        ]);
    }*/

    /*
    public function show(Request $request, $id)
    {

        $company = Company::findOrFail($id);

        $charities = $company->charities()
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->has('sort'), function ($query) use ($request) {
                $direction = $request->get('direction', 'asc');
                $query->orderBy($request->sort, $direction);
            }, function ($query) {
                $query->orderBy('name', 'asc');
            })
            ->paginate($perPage);

        $history = $company->history($id); // Optimize this method
        $totals = $company->totals($id);   // Optimize this method

        return view('companies.show', compact('company', 'charities', 'search', 'sort', 'direction', 'history', 'totals'));
    }
*/

    public function show(Request $request, $id)
    {
        $company = Company::findOrFail($id);


        $sort = $request->input('sort'); // Default sort by name
        $search = $request->input('search'); // Default ascending
        $direction = $request->get('direction', 'asc');

        $charities = $company->charities()
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->has('sort'), function ($query) use ($request) {
                $query->orderBy($request->sort, $direction);
            }, function ($query) {
                $query->orderBy('name', 'asc');
            });

        // Initialize $history and $totals as null
        $history = null;
        $totals = null;

        // Execute history() and totals() only if charities has results
        if ($charities->count() > 0) {
            $history = $company->history($id);
            $totals = $company->totals($id);
        }

        return view('companies.show', compact('company', 'charities', 'search', 'sort', 'direction', 'history', 'totals'));
    }

    /**
     * @desc this loads the add charity form so you can search for a charity
     */
    public function addCharity(int $id)
    {
        $company = company::find($id);
        return view("company.partials.addcharity", [ 'company' => $company ]);
    }


    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ticker' => 'required|string|max:8',
            'ein' => 'required|integer',
            'secID' => 'integer|min:0|max:2147483647',
            'formed' => 'nullable|date'
        ]);

        $formedTimestamp = ($t = strtotime($request->input('formed'))) !== false ? $t : null;

        $company = Company::create([
            'name' => $request->name,
            'ein' => $request->ein,
            'secID' => $request->input('secID', null),
            'ticker' => $request->ticker,
            'formed' => $formedTimestamp,
            'updated' => now()
        ]);

        return redirect()->route('companies.show', $company->id)
            ->with('success', 'Company created successfully');
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ein' => 'required|integer',
            'secID' => 'integer|min:0|max:2147483647',
            'formed' => 'nullable|date' // Validate as a date, allow null if optional
        ]);

        // Convert formed date to Unix timestamp if provided
        $formedTimestamp = ($t = strtotime($request->input('formed'))) !== false ? $t : null;

        $company->update([
            'name' => $request->name,
            'ein' => $request->ein,
            'secID' => $request->secID,
            'formed' => $formedTimestamp,
            'updated' => now()
        ]);

        return redirect()->route('companies.show', $company->id)
            ->with('success', 'Company updated successfully');
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully');
    }

    public function searchCharity(Request $request, $companyId)
    {
        $request->validate([
            'charity_name' => 'required|string|max:255'
        ]);

        $company = Company::findOrFail($companyId);
        
        $charities = Charity::where('name', 'like', '%' . $request->charity_name . '%')
            ->WhereNull('parent')
            ->get();

        return response()->json([
            'charities' => $charities->map(function ($charity) {
                return [
                    'id' => $charity->id,
                    'name' => $charity->name,
                    'current_company' => $charity->company?->name
                ];
            })
        ]);
    }

    public function associateCharity(Request $request, $companyId)
    {
        $request->validate([
            'charity_id' => 'required|exists:charity,id'
        ]);

        $company = Company::findOrFail($companyId);
        $charity = Charity::findOrFail($request->charity_id);

        if ($charity->company) {
            return response()->json(['success' => false, 'message' => 'Charity is already associated with a company'], 400);
        }

        $charity->update([
            'parent' => $company->id,
            'update_date' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Charity associated successfully']);
    }

    public function exportHistory($id)
    {
        $company = Company::findOrFail($id);
        $history = $company->history_export($id);

        if ($history->isEmpty()) {
            return redirect()->back()->with('error', 'No history data available to export.');
        }

        // Set the CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"company_{$id}_history.csv\"",
        ];

        // Create a stream for CSV output
        $callback = function () use ($history) {
            $file = fopen('php://output', 'w');

            // Add CSV header row
            fputcsv($file, ['Tax Year', 'Tax Period End', 'Charity', 'Total Assets', 'Total Expenses',  'Total Contributions', 'Total Revenue', 'Grants Paid', 'Net Assets']);

            // Add data rows
            foreach ($history as $h) {
                fputcsv($file, [
                    $h->tax_year,
                    $h->tax_period_end,
                    $h->name,
                    $h->total_assets ?? '0',
                    $h->total_expenses ?? '0',
                    $h->total_contrib ?? '0',
                    $h->total_revenue ?? '0',
                    $h->grants_paid ?? '0',
                    $h->net_assets ?? '0'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }


    public function exportCharityHistory(Request $request)
    {
        $keyword = $request->input('keyword') ?? '';
        $selectedList = $request->input('list_id');

        // Get company IDs using ListModel's get_companies
        $companyIDs = ListModel::get_companies($keyword, $selectedList)->toArray();

        if (empty($companyIDs)) {
            return redirect()->back()->with('error', 'No companies found to export charity history.');
        }
     
         
        // Use history_export from Company model
        $history = (new Company)->history_export($companyIDs);

        if ($history->isEmpty()) {
            return redirect()->back()->with('error', 'No charity history available to export.');
        }

        // Set the CSV headers
        $filename = $selectedList ? "list_{$selectedList}_charity_history" : "all_lists_charity_history";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}_" . now()->format('Y-m-d_H-i-s') . ".csv\"",
        ];

        // Create a stream for CSV output
        $callback = function () use ($history) {
            $file = fopen('php://output', 'w');

            // Add CSV header row
            fputcsv($file, ['Ticker','Charity Name', 'Tax Year', 'Tax Period End', 'Total Assets', 'Total Expenses', 'Grants Paid', 'Total Contributions', 'Net Assets', 'Total Revenue']);
            foreach ($history as $h) {
                fputcsv($file, [
                    $h->ticker,
                    $h->name,
                    $h->tax_year,
                    $h->tax_period_end,
                    $h->total_assets ?? '0',
                    $h->total_expenses ?? '0',
                    $h->grants_paid ?? '0',
                    $h->total_contrib ?? '0',
                    $h->net_assets ?? '0',
                    $h->total_revenue ?? '0',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Fetch JSON data and insert into the company table
     * @desc this file takes the ticker list from SEC and imports it into a database so we can do some matching with the CSV lists of stock tickers - the purpose is to associate the stock ticker with the SEC filing ID
    * @requires company_tickers.json to be at the SEC website.  This was previously written to accept the file in the same folder/base folder on the server.
    * @results should create new records if there are any new stock tickers; if not, just ignores existing tickers.  takes about 10 seconds to run on an empty database
     */
    public function fetchCompanies(Request $request)
    {
        // Define the URL (could be from .env or passed via request)
        $url = config('services.company_api.url', 'https://www.sec.gov/files/company_tickers.json'); // Default URL

        try {
            // Fetch JSON data using Laravel's HTTP client
            $response = Http::withHeaders([
                'User-Agent' => 'tovcompute/1.0 ' . config('mail.from.address', 'admin@example.com'),
            ])->get($url);

            if ($response->failed()) {
                return redirect()->route('companies.index')->with('error', 'Unable to fetch company data from the API. Status: ' . $response->status());
            }

            // Decode JSON into an array
            $data = $response->json();

            if (!is_array($data)) {
                return redirect()->back()->with('error', 'Invalid JSON data received.');
            }

            #$file = fopen('company_tickers.json','r');
            #$data = json_decode($jsonData, true);
            
            // Process each company
            foreach ($data as $value) {
                /*
                 * Expected JSON structure:
                 * [
                 *     'cik_str' => 320193,
                 *     'ticker' => 'AAPL',
                 *     'title' => 'Apple Inc.'
                 * ]
                 */
                $companyData = [
                    'secID' => $value['cik_str'],
                    'ticker' => $value['ticker'],
                    'name' => $value['title'],
                    'updated' => time(), // Current Unix timestamp
                ];

                // Insert or ignore using Eloquent (equivalent to INSERT IGNORE)
                Company::updateOrCreate(
                    ['secID' => $companyData['secID']], // Unique key to check for duplicates
                    $companyData
                );

                // Log success (optional, replaces echo)
                Log::info("{$companyData['name']} record created or updated successfully.");
            }

            return redirect()->route('companies.index')->with('success', 'Company data fetched and stored successfully.');
        } catch (\Exception $e) {
            // Log the error and return a user-friendly message
            Log::error("Error fetching companies: " . $e->getMessage());
            return redirect()->route('companies.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
