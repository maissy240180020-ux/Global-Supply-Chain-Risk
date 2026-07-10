<?php

namespace App\Http\Controllers;

use App\Models\Country;

class VisualizationController extends Controller
{
    public function index()
    {
        $countries = Country::all();

        return view(
            'visualisasi.index',
            compact('countries')
        );
    }
}