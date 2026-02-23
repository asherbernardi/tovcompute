<?php
// app/Http/Controllers/ListController.php

namespace App\Http\Controllers;

use App\Models\ListModel;
use App\Models\Company;
use Illuminate\Http\Request;

class ListController extends Controller
{
    /**
     * Display a listing of all lists
     */
    public function index()
    {
        $lists = ListModel::all();
        return view('lists.index', compact('lists'));
    }

    /**
     * Show the form for creating a new list
     */
    public function create()
    {
        return view('lists.create');
    }

    /**
     * Store a newly created list
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:15|unique:list,name',
        ]);

        ListModel::create([
            'name' => $request->name,
        ]);

        return redirect()->route('lists.index')->with('success', 'List created successfully.');
    }

    /**
     * Show the form for editing a list and managing companies
     */
    public function edit(ListModel $list)
    {
        $companies = Company::all(); // All companies for selection
        return view('lists.edit', compact('list', 'companies'));
    }

    /**
     * Update the list details
     */
    public function update(Request $request, ListModel $list)
    {
        $request->validate([
            'name' => 'required|string|max:15|unique:list,name,' . $list->id,
        ]);

        $list->update([
            'name' => $request->name,
        ]);

        return redirect()->route('lists.index')->with('success', 'List updated successfully.');
    }

    /**
     * Delete a list
     */
    public function destroy(ListModel $list)
    {
        $list->companies()->detach(); // Remove all company associations
        $list->delete();
        return redirect()->route('lists.index')->with('success', 'List deleted successfully.');
    }

    /**
     * Add companies to a list
     */
    public function addCompanies(Request $request, ListModel $list)
    {
        $request->validate([
            'company_ids' => 'required|array',
            'company_ids.*' => 'exists:company,id',
        ]);
    
        $list->companies()->syncWithoutDetaching($request->company_ids);
    
        return redirect()->route('lists.edit', $list)->with('success', 'Company added to list successfully.');
    }

    /**
     * Remove companies from a list
     */
    public function removeCompany(ListModel $list, $companyId)
    {
        $list->companies()->detach($companyId);

        return redirect()->route('lists.edit', $list)->with('success', 'Company removed from list successfully.');
    }
}
?>