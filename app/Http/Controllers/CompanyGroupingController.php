<?php

namespace App\Http\Controllers;

use App\Models\CompanyGrouping;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyGroupingController extends Controller
{
    public function index()
    {
        $groupings = CompanyGrouping::all();
        return view('company-groupings.index', compact('groupings'));
    }

    public function create()
    {
        return view('company-groupings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:15|unique:company_groupings,name',
        ]);

        CompanyGrouping::create([
            'name' => $request->name,
        ]);

        return redirect()->route('company-groupings.index')->with('success', 'Grouping created successfully.');
    }

    public function edit(CompanyGrouping $companyGrouping)
    {
        $companies = Company::all();
        return view('company-groupings.edit', compact('companyGrouping', 'companies'));
    }

    public function update(Request $request, CompanyGrouping $companyGrouping)
    {
        $request->validate([
            'name' => 'required|string|max:15|unique:company_groupings,name,' . $companyGrouping->id,
        ]);

        $companyGrouping->update([
            'name' => $request->name,
        ]);

        return redirect()->route('company-groupings.index')->with('success', 'Grouping updated successfully.');
    }

    public function destroy(CompanyGrouping $companyGrouping)
    {
        $companyGrouping->companies()->detach();
        $companyGrouping->delete();
        return redirect()->route('company-groupings.index')->with('success', 'Grouping deleted successfully.');
    }

    public function addCompanies(Request $request, CompanyGrouping $companyGrouping)
    {
        $request->validate([
            'company_ids' => 'required|array',
            'company_ids.*' => 'exists:company,id',
        ]);

        $companyGrouping->companies()->syncWithoutDetaching($request->company_ids);

        return redirect()->route('company-groupings.edit', $companyGrouping)->with('success', 'Company added to grouping successfully.');
    }

    public function removeCompany(CompanyGrouping $companyGrouping, $companyId)
    {
        $companyGrouping->companies()->detach($companyId);

        return redirect()->route('company-groupings.edit', $companyGrouping)->with('success', 'Company removed from grouping successfully.');
    }
}
