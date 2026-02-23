<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\charity;
use App\Models\company;
use App\Models\CharityRecommend;

class MatchController extends Controller
{
    
    /**
     * @desc remove unnecessary strings from company name
    */
    private function filterString($input) {
    // Define the words to be removed (case insensitive)
    $wordsToRemove = ['Inc', 'LTD', 'LLC', 'Co', 'Corp', 'Com', 'The', 'An', 'and'];

    // Remove the words
    $patternWords = '/\b(?:' . implode('|', $wordsToRemove) . ')\b/i';
    $input = preg_replace($patternWords, '', $input);

    // Remove punctuation
    $input = preg_replace('/[^\w\s]/u', '', $input);

    // Optionally, trim excess whitespace
    $input = trim($input);

    return $input;
    }

    /**
     * @desc actually does the percent calculation
     */
    private function calculateMatchPercentage($searchName, $name) {
        // Use Levenshtein distance to determine similarity
        $distance = levenshtein($searchName, $name);
        $maxLen = max(strlen($searchName), strlen($name));
        $percentage = ($maxLen - $distance) / $maxLen * 100;
    
        return round($percentage, 2); // Return percentage with 2 decimal places
    }

    /**
     * @take the name parts we came up with and search the charity table for a match
     */
    private function searchName($searchName) {
        // Clean the search name
        $searchName = trim($searchName);
        
        // Split the search name by spaces
        $searchParts = explode(' ', $searchName);
        
        // Start building the query
        $query = DB::table('charity')->select('name','id');
        
        // Add a condition to match the SOUNDEX of the name
        $query->whereRaw('SOUNDEX(name) = SOUNDEX(?)', [$searchName]);
        
        // Loop through the search parts and build LIKE conditions
        foreach ($searchParts as $part) {
            $part = trim($part);
            if (!empty($part)) {
                $query->orWhere('name', 'LIKE', "%$part%");
            }
        }
        
        // Execute the query and get the results
        $results = $query->get();
        
        // Prepare the result set with match percentages
        $matches = [];
        foreach ($results as $row) {
            $similarity = $this->calculateMatchPercentage($searchName, $row->name);
            $matches[] = [
                'name' => $row->name,
                'id'    => $row->id,
                'match_percentage' => $similarity
            ];
        }
        
        return $matches;
    }

    /**
     * @desc the function to run the matches and put them in the database, currently also outputs to screen
     */
    public function runMatches($forceAll = false)
    {
        if($forceAll == true):
            // Fetch companies from the 'company' table, ordering by 'id'
            $companies = Company::orderBy('id', 'asc')->limit(40)->get();
        else:
            $companies = Company::orderBy('id', 'asc')
                ->limit(100)
                ->whereNotIn('id', function($query) {
                $query->select("companyID")->distinct()->from("charity_recommend");
                })->get();
        endif;

        if ($companies->isEmpty()) {
            echo "0 results";
        } else {
            // Loop through the fetched companies
            foreach ($companies as $company) {
                $name = $this->filterString($company->name); 

                echo "Company: " . $name . "<br />";

                // Call searchName function to get match results
                $out = $this->searchName($name);

                if ($out) {
                    foreach ($out as $o) {
                        echo $o['name'] . " (".$o['id'].")" . " = " . $o['match_percentage'] . "%<br />";

                        // Save the match to the charity_recommend table
                        //$this->saveMatchRecommendation($company->id, $o['id'], $o['match_percentage']);
                    }
                }
            }
        }
    }

    /**
     * @desc Save the match recommendation to the charity_recommend table.
     */
    private function saveMatchRecommendation($companyID, $charityID, $matchPercentage)
    {
        // Create a new record in the charity_recommend table
        //conditions to check first, if not found, then insert
        CharityRecommend::firstOrCreate([
            'companyID' => $companyID, 
            'charityID' => $charityID
        ],
        [
            'match' => $matchPercentage
        ]
         );
    }

}
