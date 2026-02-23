<?php
// app/Http/Controllers/ManualController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
?>