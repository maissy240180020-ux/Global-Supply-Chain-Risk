<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    /**
     * Menampilkan daftar negara yang dipantau (watchlist).
     */
    public function index()
    {
        // Admin tidak punya watchlist
        if (auth()->user()->isAdmin()) {
            abort(403, 'Halaman ini khusus untuk User.');
        }

        return view('watchlist.index');
    }

    /**
     * Meng-toggle status favorit/watchlist suatu negara.
     */
    public function toggle(Country $country)
    {
        // Gunakan pivot table country_user untuk toggle
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return response()->json(['error' => 'Admin tidak dapat menambahkan favorit'], 403);
        }

        $user->favorites()->toggle($country->id);
        
        $isFavorite = $user->favorites()->where('country_id', $country->id)->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'country_name' => $country->country_name
        ]);
    }

    /**
     * Endpoint API internal untuk fetch real-time data watchlist milik User
     */
    public function liveData()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $favorites = $user->favorites()->orderBy('country_name')->get();
        
        $stats = [
            'total' => $favorites->count(),
            'high_risk' => 0,
            'medium_risk' => 0,
            'low_risk' => 0
        ];

        if ($favorites->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'stats' => $stats
            ]);
        }

        // Fetch Weather Data Paralel for all favorite countries
        $poolFunc = function (\Illuminate\Http\Client\Pool $pool) use ($favorites) {
            $requests = [];
            foreach ($favorites as $country) {
                $requests[] = $pool->as('weather_' . $country->country_code)
                    ->timeout(5)
                    ->withoutVerifying()
                    ->get("https://api.open-meteo.com/v1/forecast", [
                        'latitude' => $country->latitude ?? 0, 
                        'longitude' => $country->longitude ?? 0, 
                        'current' => 'temperature_2m,weather_code'
                    ]);
            }
            return $requests;
        };

        $responses = \Illuminate\Support\Facades\Http::pool($poolFunc);

        $weatherCodeMap = [
            0 => ['Cerah', 'bi-sun', 'text-warning'], 
            1 => ['Sebagian Berawan', 'bi-cloud-sun', 'text-secondary'], 
            2 => ['Berawan', 'bi-cloud', 'text-secondary'], 
            3 => ['Mendung', 'bi-clouds', 'text-secondary'],
            45 => ['Berkabut', 'bi-cloud-haze', 'text-secondary'], 
            48 => ['Kabut Es', 'bi-cloud-haze', 'text-info'], 
            51 => ['Gerimis Ringan', 'bi-cloud-drizzle', 'text-primary'], 
            53 => ['Gerimis Sedang', 'bi-cloud-drizzle', 'text-primary'],
            61 => ['Hujan Ringan', 'bi-cloud-rain', 'text-primary'], 
            63 => ['Hujan Sedang', 'bi-cloud-rain', 'text-primary'], 
            65 => ['Hujan Lebat', 'bi-cloud-rain-heavy', 'text-primary'], 
            71 => ['Salju Ringan', 'bi-cloud-snow', 'text-info'],
            80 => ['Hujan Deras', 'bi-cloud-rain-heavy', 'text-primary'], 
            95 => ['Badai Petir', 'bi-cloud-lightning', 'text-danger'], 
            99 => ['Badai Petir Berat', 'bi-cloud-lightning-rain', 'text-danger']
        ];

        $results = [];

        foreach ($favorites as $country) {
            $key = 'weather_' . $country->country_code;
            
            $temp = $country->temperature ?? 25;
            $cond = 'Berawan';
            $icon = 'bi-cloud';
            $color = 'text-secondary';

            if (isset($responses[$key]) && $responses[$key]->successful()) {
                $data = $responses[$key]->json('current');
                $temp = $data['temperature_2m'] ?? $temp;
                $code = $data['weather_code'] ?? 2;
                if (isset($weatherCodeMap[$code])) {
                    $cond = $weatherCodeMap[$code][0];
                    $icon = $weatherCodeMap[$code][1];
                    $color = $weatherCodeMap[$code][2];
                }
            }

            // Hitung Risk Score (dinamis berdasarkan suhu)
            $dynamicRisk = min(100, max(0, $country->risk_score + ($temp > 35 ? 5 : 0)));
            $level = $dynamicRisk > 60 ? 'High' : ($dynamicRisk > 35 ? 'Medium' : 'Low');

            if ($level === 'High') $stats['high_risk']++;
            elseif ($level === 'Medium') $stats['medium_risk']++;
            else $stats['low_risk']++;

            $results[] = [
                'id' => $country->id,
                'code' => $country->country_code,
                'name' => $country->country_name,
                'capital' => $country->capital ?? '-',
                'currency' => $country->currency,
                'flag' => $country->flag,
                'risk_score' => round($dynamicRisk, 1),
                'risk_level' => $level,
                'temperature' => round($temp, 1),
                'weather_condition' => $cond,
                'weather_icon' => $icon,
                'weather_color' => $color
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'stats' => $stats
        ]);
    }
}