<?php

namespace App\Http\Controllers;

use App\Models\Country;

class WeatherController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('country_name')->get();
        return response()->json($countries);
    }
}
