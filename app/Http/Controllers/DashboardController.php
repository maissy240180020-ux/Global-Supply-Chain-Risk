<?php

namespace App\Http\Controllers;

use App\Models\Country;

class DashboardController extends Controller
{
    public function index()
    {
        $countries = Country::all();

        $totalCountries = $countries->count();

        $highRisk = $countries->where('risk_level', 'High')->count();

        $mediumRisk = $countries->where('risk_level', 'Medium')->count();

        $lowRisk = $countries->where('risk_level', 'Low')->count();

        $averageRisk = round($countries->avg('risk_score'), 1);

        $topRiskCountries = Country::orderByDesc('risk_score')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalCountries',
            'highRisk',
            'mediumRisk',
            'lowRisk',
            'averageRisk',
            'topRiskCountries'
        ));
    }
}