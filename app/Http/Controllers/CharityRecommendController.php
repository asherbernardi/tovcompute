<?php
// app/Http/Controllers/CharityRecommendController.php

namespace App\Http\Controllers;

use App\Models\CharityRecommend;
use App\Models\Charity;
use Illuminate\Http\Request;

class CharityRecommendController extends Controller
{
    /**
     * Display all recommendations
     */
    public function index()
    {
        $recommendations = CharityRecommend::with(['charity', 'company'])
            ->where("status", "=", 0)
            ->where("match", ">=", 70)
            ->orderBy('match', 'desc')
            ->paginate(2500); // 2500 records per page
            

        return view('recommendations', compact('recommendations'));
    }

    /**
     * Approve a recommendation
     */
    public function approve(Request $request, CharityRecommend $recommendation)
    {
        $recommendation->update([
            'status' => 1, // Confirmed
            'confirm_date' => now()->timestamp, // Current Unix timestamp
        ]);

        // Update the charity's parent column with the companyID
        $charity = Charity::find($recommendation->charityID);
        if ($charity) {
            $charity->update([
                'parent' => $recommendation->companyID,
            ]);
        }

        return redirect()->route('recommendations.index')->with('success', 'Recommendation approved successfully.');
    }

    /**
     * Disapprove a recommendation
     */
    public function disapprove(Request $request, CharityRecommend $recommendation)
    {
        $recommendation->update([
            'status' => 2, // 2 = rejected
            'confirm_date' => now()->timestamp, // Reset confirmation
        ]);

        return redirect()->route('recommendations.index')->with('success', 'Recommendation disapproved successfully.');
    }
}
?>