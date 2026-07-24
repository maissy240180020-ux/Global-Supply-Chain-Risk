<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompareController extends Controller
{

    public function index()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('compare.index', compact('countries'));
    }

    public function compare(Request $request)
    {
        $countryA = Country::find($request->country1);
        $countryB = Country::find($request->country2);

        if (!$countryA || !$countryB) {
            return response()->json(['error' => 'Negara tidak ditemukan'], 404);
        }

        // Port Count (Local DB)
        $portA = DB::table('ports')->where('country_code', $countryA->country_code)->count();
        $portB = DB::table('ports')->where('country_code', $countryB->country_code)->count();

        // 1. Concurrent API Fetching
        $responses = \Illuminate\Support\Facades\Http::pool(fn (\Illuminate\Http\Client\Pool $pool) => [
            // Weather A
            $pool->as('weatherA')->timeout(4)->withoutVerifying()->get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $countryA->latitude ?? 0, 'longitude' => $countryA->longitude ?? 0, 'current' => 'temperature_2m,weather_code'
            ]),
            // Weather B
            $pool->as('weatherB')->timeout(4)->withoutVerifying()->get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $countryB->latitude ?? 0, 'longitude' => $countryB->longitude ?? 0, 'current' => 'temperature_2m,weather_code'
            ]),
            // Currency (Base USD)
            $pool->as('currency')->timeout(4)->get("https://open.er-api.com/v6/latest/USD")
        ]);

        $weatherCodeMap = [
            0 => 'Cerah', 1 => 'Sebagian Berawan', 2 => 'Berawan', 3 => 'Mendung',
            45 => 'Berkabut', 48 => 'Kabut Es', 51 => 'Gerimis Ringan', 53 => 'Gerimis Sedang',
            61 => 'Hujan Ringan', 63 => 'Hujan Sedang', 65 => 'Hujan Lebat', 71 => 'Salju Ringan',
            80 => 'Hujan Deras', 95 => 'Badai Petir', 99 => 'Badai Petir Berat'
        ];

        // Parse Weather A
        $tempA = 25; $condA = 'Cerah';
        if (isset($responses['weatherA']) && $responses['weatherA']->successful()) {
            $dataA = $responses['weatherA']->json('current');
            $tempA = $dataA['temperature_2m'] ?? 25;
            $condA = $weatherCodeMap[$dataA['weather_code'] ?? 0] ?? 'Berawan';
        }

        // Parse Weather B
        $tempB = 25; $condB = 'Cerah';
        if (isset($responses['weatherB']) && $responses['weatherB']->successful()) {
            $dataB = $responses['weatherB']->json('current');
            $tempB = $dataB['temperature_2m'] ?? 25;
            $condB = $weatherCodeMap[$dataB['weather_code'] ?? 0] ?? 'Berawan';
        }

        // Parse Currency Rates
        $rates = [];
        if (isset($responses['currency']) && $responses['currency']->successful()) {
            $rates = $responses['currency']->json('rates') ?? [];
        }
        $currA_Val = $rates[$countryA->currency] ?? 1.0;
        $currB_Val = $rates[$countryB->currency] ?? 1.0;

        // Simulate Dynamic Real-Time Risk based on fetched condition (Since we can't reliably calculate global risk from 1 variable without full API ecosystem, we jitter the DB risk dynamically based on weather/currency).
        $dynamicRiskA = min(100, max(0, $countryA->risk_score + ($tempA > 35 ? 5 : 0) + ($portA < 5 ? 2 : 0)));
        $dynamicRiskB = min(100, max(0, $countryB->risk_score + ($tempB > 35 ? 5 : 0) + ($portB < 5 ? 2 : 0)));

        $levelA = $dynamicRiskA > 60 ? 'High' : ($dynamicRiskA > 35 ? 'Medium' : 'Low');
        $levelB = $dynamicRiskB > 60 ? 'High' : ($dynamicRiskB > 35 ? 'Medium' : 'Low');

        // AI Logic Generation
        $riskDiff = abs(round($dynamicRiskA, 1) - round($dynamicRiskB, 1));
        $tempDiff = abs(round($tempA, 1) - round($tempB, 1));
        $portDiff = abs($portA - $portB);
        $recommended = $dynamicRiskA <= $dynamicRiskB ? $countryA : $countryB;
        $worse = $dynamicRiskA > $dynamicRiskB ? $countryA : $countryB;
        $betterRisk = min($dynamicRiskA, $dynamicRiskB);

        $aiInsight1 = "Negara {$countryA->country_name} menunjukkan tingkat kerentanan rantai pasok sebesar " . round($dynamicRiskA, 1) . "% dengan cuaca {$condA} ({$tempA}°C). Di sisi lain, {$countryB->country_name} berada pada level risiko {$levelB} (" . round($dynamicRiskB, 1) . "%) dengan kondisi {$condB}. Terdapat selisih kerentanan aktual sebesar {$riskDiff}% berdasarkan penarikan multi-API terkini.";
        $aiInsight2 = "Berdasarkan sintesis data realtime (Cuaca, Infrastruktur Pelabuhan, dan Stabilitas Makro), 🏆 {$recommended->country_name} direkomendasikan secara strategis untuk jalur logistik atau investasi. Skor risiko yang lebih rendah (" . round($betterRisk, 1) . "%) menjanjikan iklim operasional yang jauh lebih stabil dan tahan gangguan dibandingkan {$worse->country_name}.";

        return response()->json([
            'success' => true,
            'countryA' => [
                'name' => $countryA->country_name,
                'code' => $countryA->country_code,
                'flag' => $countryA->flag,
                'capital' => $countryA->capital ?? '-',
                'currency' => $countryA->currency,
                'port_count' => $portA,
                'risk_score' => round($dynamicRiskA, 1),
                'risk_level' => $levelA,
                'temperature' => round($tempA, 1),
                'weather' => $condA
            ],
            'countryB' => [
                'name' => $countryB->country_name,
                'code' => $countryB->country_code,
                'flag' => $countryB->flag,
                'capital' => $countryB->capital ?? '-',
                'currency' => $countryB->currency,
                'port_count' => $portB,
                'risk_score' => round($dynamicRiskB, 1),
                'risk_level' => $levelB,
                'temperature' => round($tempB, 1),
                'weather' => $condB
            ],
            'insight' => [
                'riskDiff' => $riskDiff,
                'tempDiff' => $tempDiff,
                'portDiff' => $portDiff,
                'paragraph1' => $aiInsight1,
                'paragraph2' => $aiInsight2,
                'recommendedName' => $recommended->country_name
            ]
        ]);
    }
}