<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================
        // Data Database
        // ==========================

        $countries = Country::all();

        $totalCountries = $countries->count();
        $highRisk = $countries->where('risk_level', 'High')->count();
        $mediumRisk = $countries->where('risk_level', 'Medium')->count();
        $lowRisk = $countries->where('risk_level', 'Low')->count();
        $averageRisk = round($countries->avg('risk_score'), 1);

        $topRiskCountries = Country::orderByDesc('risk_score')
            ->take(5)
            ->get();

        // ==========================
        // Cuaca Realtime
        // ==========================

        $cuaca = null;

        try {

            $response = Http::timeout(20)
                ->retry(3, 1000)
                ->get(
                    'https://api.open-meteo.com/v1/forecast',
                    [
                        'latitude' => -6.2088,
                        'longitude' => 106.8456,
                        'current' => 'temperature_2m,wind_speed_10m',
                        'timezone' => 'Asia/Jakarta'
                    ]
                );

            if ($response->successful()) {

                $data = $response->json();

                if (isset($data['current'])) {

                    $cuaca = $data['current'];

                }

            }

        } catch (\Exception $e) {

            // Data cadangan jika API gagal
            $cuaca = [
                'temperature_2m' => 29,
                'wind_speed_10m' => 14
            ];

        }

        return view('dashboard.index', compact(
            'totalCountries',
            'highRisk',
            'mediumRisk',
            'lowRisk',
            'averageRisk',
            'topRiskCountries',
            'cuaca'
        ));
    }
}