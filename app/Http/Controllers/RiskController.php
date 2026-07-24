<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $risk = $request->risk;
        $region = $request->region;
        $sort = $request->sort;

        $query = Country::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('country_name', 'like', "%{$search}%")
                  ->orWhere('country_code', 'like', "%{$search}%");
            });
        }

        if ($risk) {
            $query->where('risk_level', $risk);
        }

        if ($region) {
            $query->where('region', $region);
        }

        if ($sort == 'terendah') {
            $query->orderBy('risk_score', 'asc');
        } else {
            $query->orderBy('risk_score', 'desc'); // default highest
        }

        $countries = $query->get();

        $highRisk = Country::where('risk_level', 'High')->count();
        $mediumRisk = Country::where('risk_level', 'Medium')->count();
        $lowRisk = Country::where('risk_level', 'Low')->count();

        $regions = Country::whereNotNull('region')->where('region', '!=', '-')->distinct()->pluck('region')->sort();

        $topHigh = Country::whereNotNull('risk_score')->orderByDesc('risk_score')->take(5)->get();
        $topLow = Country::whereNotNull('risk_score')->orderBy('risk_score')->take(5)->get();

        $totalCountries = Country::count();
        $avgRiskScore = $totalCountries > 0 ? round(Country::avg('risk_score'), 1) : 0;
        $highestRiskCountry = Country::orderByDesc('risk_score')->first();
        $lowestRiskCountry = Country::orderBy('risk_score')->first();

        $highRiskPct = $totalCountries > 0 ? round(($highRisk / $totalCountries) * 100, 1) : 0;
        $mediumRiskPct = $totalCountries > 0 ? round(($mediumRisk / $totalCountries) * 100, 1) : 0;
        $lowRiskPct = $totalCountries > 0 ? round(($lowRisk / $totalCountries) * 100, 1) : 0;

        return view('risk.index', compact(
            'countries',
            'highRisk',
            'mediumRisk',
            'lowRisk',
            'regions',
            'topHigh',
            'topLow',
            'totalCountries',
            'avgRiskScore',
            'highestRiskCountry',
            'lowestRiskCountry',
            'highRiskPct',
            'mediumRiskPct',
            'lowRiskPct'
        ));
    }
}