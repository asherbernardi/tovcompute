<?php
namespace App\Http\Controllers;
use Illuminate\Support\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Html\FormFacade;
use Illuminate\View\View;
use App\Models\Charity;
use App\Models\Company; // Import the Company model
use App\Models\CharityGiving;

class CharityController extends Controller
{

    /**
     * @desc loads all charities, needs to be searchable/sortable
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('keyword');

        $charity = Charity::with('Company')
            ->where(function ($query) use ($searchTerm) {
                if ($searchTerm) {
                    $query->where('name', 'LIKE', "%$searchTerm%")
                          ->orWhereHas('Company', function ($q) use ($searchTerm) {
                              $q->where('ticker', 'LIKE', "%$searchTerm%");
                          });
                }
            })
            ->where('name', '!=', '') // Exclude empty names
            ->orderBy('name')
            ->paginate(1000);
 
        if ($request->header('hx-request') 
            && $request->header('hx-target') == '#table-container') {  
            return view('charities.partials.table', compact('charity'));  
        }  
        else {
        return view('charities.index', compact('charity'), [ 'keyword' => $searchTerm ]);
        }
        /*
        return view('charity.list', [
            'charity' => $charities
        ]);
        */
    }

    /*
    * @desc Show a specific charity, needs to show the charity info and their history/financials
     */
    public function show(int $id)
    {
        $charity = Charity::with(['history', 'company'])->findOrFail($id);
        $totals = CharityGiving::totals($id);
        return view('charities.show', ['charity' => $charity, 'totals' => $totals]);
    }

    /**
     * @desc this is the search request from the company side, to locate the charity to associate with the company
     */
    public function search(Request $r)
    {
        $searchTerm = $r->input('q');
        $company = $r->input("company");
        $charity = charity::where('name', 'LIKE', "%$searchTerm%")->with('company')->orderBy('name')->get();
        
        return view('charities.partials.associate', compact('charity'), [ 'searchTerm' => $searchTerm, "company" => $company ]);
    }


    /**
     * @desc this actually associates to the database the charity
     */
    public function associateCharity(int $companyID, int $charityID)
    {
        if($charityID != ""):
           charity::where("id", $charityID)->update([ "parent" => $companyID ]);
        endif;

        #return redirect("/company/".$companyID);
        #return redirect()->route("company");
        return back()->withInput();
        
    }

    /**
     * @desc this removes the association to a company
     */
    public function removeCharity(int $companyID, int $charityID)
    {
        if($charityID != ""):
           charity::where("id", $charityID)->update([ "parent" => null ]);
        endif;

        #return redirect("/company/".$companyID);
        #return redirect()->route("company");
        return back()->withInput();
        
    }

    /**
     * Remove the company association (sets parent to null)
     */
    public function removeCompany(Charity $charity)
    {
        $charity->update(['parent' => null]);

        return redirect()->route('charities.edit', $charity)->with('success', 'Company removed successfully.');
    }

    /**
     * Show the form for editing a charity
     */
    public function edit(Charity $charity)
    {
        return view('charities.edit', compact('charity'));
    }

    /**
     * Update the charity details
     */
    public function update(Request $request, Charity $charity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ein' => 'required|numeric|max:999999999',
        ]);

        $charity->update([
            'name' => $request->name,
            'ein'   => $request->ein,
            'updated' => time()
        ]);

        return redirect()->route('charities.edit', $charity)->with('success', 'Charity updated successfully.');
    }

    public function exportHistory($id)
    {
        $charity = Charity::findOrFail($id);
        $history = $charity->history_export($id);

        
        if ($history->isEmpty()) {
            return redirect()->back()->with('error', 'No history data available to export.');
        }

        // Set the CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"charity_{$id}_history.csv\"",
        ];

        // Create a stream for CSV output
        $callback = function () use ($history) {
            $file = fopen('php://output', 'w');

            // Add CSV header row
            fputcsv($file, ['Tax Year', 'Tax Period End', 'Total Assets', 'Total Expenses', 'Total Liabilities', 'Total Contributions', 'Total Revenue', 'Grants Paid', 'Net Assets',]);

            // Add data rows
            foreach ($history as $h) {
                fputcsv($file, [
                    $h->tax_year,
                    $h->tax_period_end,
                    $h->total_assets ?? '0',
                    $h->total_expenses ?? '0',
                    $h->total_liabilities ?? '0',
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

}
