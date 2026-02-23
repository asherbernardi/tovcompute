<?php

namespace App\Http\Controllers;

use App\Models\charity_giving;
use Illuminate\Http\Request;

class CharityGivingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(charity_giving $charity_giving)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(charity_giving $charity_giving)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, charity_giving $charity_giving)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(charity_giving $charity_giving)
    {
        //
    }

    /**
     * Update charityID in charity_giving based on matching EINs
     */
    public function syncCharityIds()
    {
        try {
            $affectedRows = CharityGiving::syncCharityIds();

            $message = $affectedRows > 0 
                ? "Successfully synchronized $affectedRows charity IDs."
                : "No charity IDs were updated.";

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error("Error syncing charity IDs: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while syncing charity IDs.');
        }
    }
}
