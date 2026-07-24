<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VisualizationController extends Controller
{
    public function index()
    {
        // Ambil jumlah pelabuhan per negara
        $portCounts = DB::table('ports')
            ->select('country_code', DB::raw('count(*) as count'))
            ->groupBy('country_code')
            ->pluck('count', 'country_code')
            ->toArray();

        // 1. Ambil semua negara untuk selector profil dengan port_count terlampir
        $countries = Country::orderBy('country_name')->get()->map(function ($c) use ($portCounts) {
            $c->port_count = $portCounts[$c->country_code] ?? 0;
            return $c;
        });

        // 2. Distribusi Level Risiko
        $riskCounts = [
            'High' => Country::where('risk_level', 'High')->count(),
            'Medium' => Country::where('risk_level', 'Medium')->count(),
            'Low' => Country::where('risk_level', 'Low')->count(),
        ];

        // 3. Top 10 Negara dengan Risiko Rantai Pasok Tertinggi
        $topRisk = Country::orderByDesc('risk_score')
            ->take(10)
            ->get(['country_name', 'risk_score', 'flag', 'country_code']);

        // 4. Top 10 Negara dengan Pelabuhan Terbanyak
        $topPorts = DB::table('ports')
            ->select('country_code', DB::raw('count(*) as total'))
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Gabungkan nama negara dan benderanya ke data pelabuhan
        $topPorts = $topPorts->map(function ($item) {
            $country = Country::where('country_code', $item->country_code)->first();
            $item->country_name = $country ? $country->country_name : $item->country_code;
            $item->flag = $country ? $country->flag : null;
            return $item;
        });

        // 5. Data Korelasi Inflasi vs Skor Risiko (Bubble Chart)
        $correlationData = Country::all()->map(function ($c) {
            // Skala radius gelembung berdasarkan populasi (min 4px, max 20px)
            $radius = 5;
            if ($c->population > 1000000000) {
                $radius = 20;
            } elseif ($c->population > 500000000) {
                $radius = 15;
            } elseif ($c->population > 100000000) {
                $radius = 10;
            } elseif ($c->population > 10000000) {
                $radius = 7;
            }

            return [
                'x' => (float)($c->inflation ?? 0.0),
                'y' => (float)($c->risk_score ?? 0.0),
                'r' => $radius,
                'country' => $c->country_name
            ];
        });

        // 6. Rata-rata Global untuk Perbandingan Radar
        $globalAverages = [
            'risk_score' => round(Country::avg('risk_score') ?? 0.0, 2),
            'inflation' => round(Country::avg('inflation') ?? 0.0, 2),
            'temperature' => round(Country::avg('temperature') ?? 0.0, 2),
            'population' => round(Country::avg('population') ?? 0.0, 2),
            'port_count' => round(DB::table('ports')->count() / (Country::count() ?: 1), 2)
        ];

        // Total Statistik Utama
        $totalCountries = Country::count();
        $avgGlobalRisk = round(Country::avg('risk_score') ?? 0.0, 1);
        $highRiskCount = $riskCounts['High'];
        $totalPorts = DB::table('ports')->count();

        return view('visualisasi.index', compact(
            'countries', 
            'riskCounts', 
            'topRisk', 
            'topPorts', 
            'correlationData',
            'globalAverages',
            'totalCountries',
            'avgGlobalRisk',
            'highRiskCount',
            'totalPorts'
        ));
    }

    /**
     * Endpoint JSON real-time untuk polling data visualisasi.
     */
    public function liveData(): JsonResponse
    {
        // Jumlah pelabuhan per negara
        $portCounts = DB::table('ports')
            ->select('country_code', DB::raw('count(*) as count'))
            ->groupBy('country_code')
            ->pluck('count', 'country_code')
            ->toArray();

        // Distribusi level risiko
        $riskCounts = [
            'High'   => Country::where('risk_level', 'High')->count(),
            'Medium' => Country::where('risk_level', 'Medium')->count(),
            'Low'    => Country::where('risk_level', 'Low')->count(),
        ];

        // Top 10 negara risiko tertinggi
        $topRisk = Country::orderByDesc('risk_score')
            ->take(10)
            ->get(['country_name', 'risk_score', 'flag', 'country_code']);

        // Top 10 negara dengan pelabuhan terbanyak
        $topPorts = DB::table('ports')
            ->select('country_code', DB::raw('count(*) as total'))
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->take(10)
            ->get()
            ->map(function ($item) {
                $country = Country::where('country_code', $item->country_code)->first();
                $item->country_name = $country ? $country->country_name : $item->country_code;
                $item->flag = $country ? $country->flag : null;
                return $item;
            });

        // Data korelasi bubble chart
        $correlationData = Country::all()->map(function ($c) {
            $radius = 5;
            if ($c->population > 1000000000)      { $radius = 20; }
            elseif ($c->population > 500000000)   { $radius = 15; }
            elseif ($c->population > 100000000)   { $radius = 10; }
            elseif ($c->population > 10000000)    { $radius = 7; }

            return [
                'x'       => (float)($c->inflation ?? 0.0),
                'y'       => (float)($c->risk_score ?? 0.0),
                'r'       => $radius,
                'country' => $c->country_name,
            ];
        });

        // Global averages untuk radar
        $globalAverages = [
            'risk_score'  => round(Country::avg('risk_score') ?? 0.0, 2),
            'inflation'   => round(Country::avg('inflation') ?? 0.0, 2),
            'temperature' => round(Country::avg('temperature') ?? 0.0, 2),
            'population'  => round(Country::avg('population') ?? 0.0, 2),
            'port_count'  => round(DB::table('ports')->count() / (Country::count() ?: 1), 2),
        ];

        // KPI summary
        $totalCountries = Country::count();
        $avgGlobalRisk  = round(Country::avg('risk_score') ?? 0.0, 1);
        $highRiskCount  = $riskCounts['High'];
        $totalPorts     = DB::table('ports')->count();

        // Data semua negara untuk profil
        $countries = Country::orderBy('country_name')->get()->map(function ($c) use ($portCounts) {
            $c->port_count = $portCounts[$c->country_code] ?? 0;
            return $c;
        });

        return response()->json([
            'kpi' => [
                'totalCountries' => $totalCountries,
                'avgGlobalRisk'  => $avgGlobalRisk,
                'highRiskCount'  => $highRiskCount,
                'totalPorts'     => $totalPorts,
            ],
            'riskCounts'      => $riskCounts,
            'topRisk'         => $topRisk,
            'topPorts'        => $topPorts,
            'correlationData' => $correlationData,
            'globalAverages'  => $globalAverages,
            'countries'       => $countries->keyBy('country_code'),
            'lastUpdated'     => now()->toIso8601String(),
        ]);
    }
}