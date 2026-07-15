<?php

namespace App\Http\Controllers;

use App\Models\Country;

class ReportController extends Controller
{
    public function index()
    {
        $risks = Country::select('id', 'country_name', 'country_code', 'risk_score', 'risk_level', 'weather', 'temperature')
            ->orderByDesc('risk_score')
            ->get();
        return response()->json($risks);
    }
}
