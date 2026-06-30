<?php
// app/Http/Controllers/ManualController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ManualController extends Controller
{
    public function index()
    {
        return view('manual');
    }

    public function setup()
    {
        return view('setup');
    }

    public function process()
    {
        return view('process');
    }

    public function runbackup()
    {
        exec("php artisan backup:run");
    }

    public function activatecharitymatching()
    {
        exec("php charity_matching.php");
    }

    public function importCharities(Request $request)
    {
        $csvPath = $request->input('csv_path')
            ?? base_path('data_sources/2025_07_10_All_Years_990_990ez_990pf_990n_Combined_DataMart.csv');

        Artisan::call('charities:import', [
            '--csv'        => $csvPath,
            '--background' => true,
        ]);

        return redirect()->route('manual.setup')
            ->with('success', 'Charity import started in the background. Check storage/logs/charity_import.log for progress.');
    }

    public function extractXmlGiving(Request $request)
    {
        $searchType = $request->input('search_type', 'company');
        $criteria   = $request->input('criteria');
        $fields     = $request->input('fields', 'all');

        $requiresCriteria = in_array($searchType, ['charity', 'company', 'list']);
        if ($requiresCriteria && empty($criteria)) {
            return redirect()->route('manual.setup')
                ->with('error', "Search type '{$searchType}' requires an ID.");
        }

        $options = [
            '--search-type' => $searchType,
            '--fields'      => $fields,
            '--background'  => true,
        ];
        if ($criteria) {
            $options['--criteria'] = $criteria;
        }

        Artisan::call('giving:extract-xml', $options);

        return redirect()->route('manual.setup')
            ->with('success', 'XML giving extraction started in the background. Check storage/logs/xml_giving_extract.log for progress.');
    }

    public function importGiving(Request $request)
    {
        $csvPath = $request->input('csv_path')
            ?? base_path('data_sources/2025_07_10_All_Years_990_990ez_990pf_990n_Combined_DataMart.csv');

        Artisan::call('giving:import', [
            '--csv'        => $csvPath,
            '--background' => true,
        ]);

        return redirect()->route('manual.setup')
            ->with('success', 'Giving import started in the background. Check storage/logs/giving_import.log for progress.');
    }
}
?>