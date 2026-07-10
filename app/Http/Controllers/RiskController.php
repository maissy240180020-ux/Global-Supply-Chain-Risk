<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        $risk = $request->risk;

        $countries = Country::when($risk, function ($query) use ($risk) {

            $query->where('risk_level', $risk);

        })->orderByDesc('risk_score')->get();

        $highRisk = Country::where('risk_level', 'High')->count();

        $mediumRisk = Country::where('risk_level', 'Medium')->count();

        $lowRisk = Country::where('risk_level', 'Low')->count();

        return view('risk.index', compact(
            'countries',
            'highRisk',
            'mediumRisk',
            'lowRisk'
        ));
    }
}