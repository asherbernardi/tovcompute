<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResearchController extends Controller
{
    public function scores()
    {
        return view('research.scores');
    }

    public function prompts()
    {
        return view('research.prompts');
    }

    public function runs()
    {
        return view('research.runs');
    }
}
