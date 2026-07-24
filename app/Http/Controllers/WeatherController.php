<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        $countryCode = $request->input('country', 'world');
        $error = null;
        $weatherData = [];
        $stats = [
            'avg_temp' => 0,
            'max_wind' => 0,
            'max_wind_loc' => '-',
            'extreme_count' => 0
        ];

        try {
            // 1. Tentukan target lokasi (Kordinat) dari Database
            if ($countryCode === 'world') {
                // Ambil max 25 negara secara acak/penting untuk mode Global
                $targets = DB::table('countries')
                            ->whereNotNull('latitude')
                            ->whereNotNull('longitude')
                            ->take(25)
                            ->get();
            } else {
                $targets = DB::table('countries')
                            ->where('country_code', $countryCode)
                            ->whereNotNull('latitude')
                            ->whereNotNull('longitude')
                            ->get();
            }

            if ($targets->isEmpty()) {
                throw new \Exception("Tidak ada data lokasi (Kordinat) ditemukan untuk filter tersebut.");
            }

            // 2. Siapkan parameter array untuk Batch Request Open-Meteo
            $lats = [];
            $lons = [];
            foreach ($targets as $target) {
                $lats[] = $target->latitude;
                $lons[] = $target->longitude;
            }

            $latStr = implode(',', $lats);
            $lonStr = implode(',', $lons);

            // 3. Tarik API Open-Meteo secara Batch (sekaligus)
            $response = Http::withoutVerifying()->timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude'  => $latStr,
                'longitude' => $lonStr,
                'current'   => 'temperature_2m,relative_humidity_2m,precipitation,weather_code,wind_speed_10m',
                'timezone'  => 'auto'
            ]);

            if ($response->successful()) {
                $apiResult = $response->json();
                
                // Jika hanya 1 kordinat, open-meteo mengembalikan object tunggal, bukan array of objects
                // Kita normalisasi agar selalu berbentuk array of objects
                $resultsArray = is_array($apiResult) && !isset($apiResult['latitude']) ? $apiResult : [$apiResult];
                
                $totalTemp = 0;
                
                foreach ($targets as $index => $target) {
                    $res = $resultsArray[$index] ?? null;
                    if (!$res || !isset($res['current'])) continue;

                    $current = $res['current'];
                    $code = $current['weather_code'];
                    
                    // Interpretasi Kode Cuaca (WMO)
                    $weatherInfo = $this->interpretWeatherCode($code);
                    
                    // Hitung Tingkat Risiko
                    $risk = $this->calculateRisk($current['wind_speed_10m'], $current['precipitation'], $code);

                    $weatherData[] = [
                        'country_name' => $target->country_name,
                        'country_code' => $target->country_code,
                        'capital'      => $target->capital,
                        'lat'          => $target->latitude,
                        'lng'          => $target->longitude,
                        'temp'         => $current['temperature_2m'],
                        'humidity'     => $current['relative_humidity_2m'],
                        'wind'         => $current['wind_speed_10m'],
                        'rain'         => $current['precipitation'],
                        'condition'    => $weatherInfo['text'],
                        'icon'         => $weatherInfo['icon'],
                        'color'        => $weatherInfo['color'],
                        'risk_level'   => $risk['level'],
                        'risk_color'   => $risk['color'],
                    ];

                    // Hitung statistik
                    $totalTemp += $current['temperature_2m'];
                    if ($current['wind_speed_10m'] > $stats['max_wind']) {
                        $stats['max_wind'] = $current['wind_speed_10m'];
                        $stats['max_wind_loc'] = $target->country_name;
                    }
                    if ($risk['level'] === 'High' || $risk['level'] === 'Extreme') {
                        $stats['extreme_count']++;
                    }
                }

                if (count($weatherData) > 0) {
                    $stats['avg_temp'] = round($totalTemp / count($weatherData), 1);
                }

            } else {
                throw new \Exception("Gagal mengambil data dari Open-Meteo API. Status: " . $response->status());
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        // Dropdown data
        $allCountries = DB::table('countries')->orderBy('country_name')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => $weatherData,
                'stats'   => $stats,
                'error'   => $error
            ]);
        }

        return view('cuaca.index', compact('weatherData', 'stats', 'allCountries', 'countryCode', 'error'));
    }

    private function interpretWeatherCode($code)
    {
        // WMO Weather interpretation codes
        if ($code == 0) return ['text' => 'Cerah (Clear)', 'icon' => 'bi-sun-fill', 'color' => 'text-warning'];
        if ($code >= 1 && $code <= 3) return ['text' => 'Berawan (Cloudy)', 'icon' => 'bi-cloud-sun-fill', 'color' => 'text-secondary'];
        if ($code >= 45 && $code <= 48) return ['text' => 'Berkabut (Fog)', 'icon' => 'bi-cloud-fog-fill', 'color' => 'text-secondary'];
        if ($code >= 51 && $code <= 69) return ['text' => 'Hujan (Rain)', 'icon' => 'bi-cloud-rain-fill', 'color' => 'text-info'];
        if ($code >= 71 && $code <= 79) return ['text' => 'Salju (Snow)', 'icon' => 'bi-snow', 'color' => 'text-primary'];
        if ($code >= 80 && $code <= 82) return ['text' => 'Hujan Lebat (Heavy Rain)', 'icon' => 'bi-cloud-rain-heavy-fill', 'color' => 'text-primary'];
        if ($code >= 95) return ['text' => 'Badai Petir (Thunderstorm)', 'icon' => 'bi-cloud-lightning-fill', 'color' => 'text-danger'];
        
        return ['text' => 'Tidak Diketahui', 'icon' => 'bi-cloud', 'color' => 'text-muted'];
    }

    private function calculateRisk($wind, $rain, $code)
    {
        if ($code >= 95 || $wind > 60 || $rain > 20) {
            return ['level' => 'Extreme', 'color' => 'danger'];
        }
        if ($code >= 80 || $wind > 40 || $rain > 10) {
            return ['level' => 'High', 'color' => 'warning'];
        }
        if ($code >= 51 || $wind > 20 || $rain > 2) {
            return ['level' => 'Medium', 'color' => 'primary'];
        }
        return ['level' => 'Low', 'color' => 'success'];
    }
}
