<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class CuacaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua negara untuk dropdown selector
        $countries = Country::orderBy('country_name')->get();

        // 2. Tentukan negara terpilih (default Indonesia / negara pertama)
        $selectedCountryId = $request->input('country_id');
        $selectedCountry = null;

        if ($selectedCountryId) {
            $selectedCountry = Country::find($selectedCountryId);
        }

        if (!$selectedCountry) {
            $selectedCountry = Country::where('country_code', 'ID')->first() ?? Country::first();
        }

        $cuaca = null;

        if ($selectedCountry) {
            $latitude = $selectedCountry->latitude ?? -6.2088;
            $longitude = $selectedCountry->longitude ?? 106.8456;

            try {
                $response = Http::timeout(10)
                    ->retry(3, 500)
                    ->get(
                        'https://api.open-meteo.com/v1/forecast',
                        [
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m,wind_direction_10m',
                            'timezone' => 'auto'
                        ]
                    );

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['current'])) {
                        $cuaca = $data['current'];

                        // Terjemahkan kode cuaca & tentukan ikon/warna
                        $code = $cuaca['weather_code'] ?? 0;
                        $weatherInfo = $this->translateWeatherCode($code);
                        $cuaca['description'] = $weatherInfo['description'];
                        $cuaca['icon'] = $weatherInfo['icon'];
                        $cuaca['color'] = $weatherInfo['color'];

                        // Format waktu agar lebih mudah dibaca
                        if (isset($cuaca['time'])) {
                            $cuaca['formatted_time'] = date('d M Y, H:i', strtotime($cuaca['time']));
                        } else {
                            $cuaca['formatted_time'] = now()->format('d M Y, H:i');
                        }

                        // Sinkronisasi data terbaru ke database negara terpilih
                        $selectedCountry->update([
                            'temperature' => $cuaca['temperature_2m'],
                            'weather' => $weatherInfo['description']
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Fallback jika API gagal
                $weatherInfo = $this->translateWeatherCode(0);
                $cuaca = [
                    'temperature_2m' => $selectedCountry->temperature ?? 28,
                    'relative_humidity_2m' => 75,
                    'apparent_temperature' => $selectedCountry->temperature ?? 28,
                    'precipitation' => 0.0,
                    'weather_code' => 0,
                    'wind_speed_10m' => 12.0,
                    'wind_direction_10m' => 180,
                    'description' => $selectedCountry->weather ?? 'Cerah',
                    'icon' => 'bi-sun-fill',
                    'color' => '#f59e0b',
                    'formatted_time' => now()->format('d M Y, H:i') . ' (Fallback)'
                ];
            }
        }

        return view('cuaca.index', compact('countries', 'selectedCountry', 'cuaca'));
    }

    /**
     * Menerjemahkan kode cuaca WMO ke deskripsi bahasa Indonesia, ikon, dan warna representatif.
     */
    private function translateWeatherCode($code)
    {
        switch ($code) {
            case 0:
                return ['description' => 'Cerah', 'icon' => 'bi-sun-fill', 'color' => '#f59e0b'];
            case 1:
            case 2:
            case 3:
                return ['description' => 'Berawan', 'icon' => 'bi-cloud-sun-fill', 'color' => '#64748b'];
            case 45:
            case 48:
                return ['description' => 'Kabut', 'icon' => 'bi-cloud-fog-fill', 'color' => '#94a3b8'];
            case 51:
            case 53:
            case 55:
            case 56:
            case 57:
                return ['description' => 'Gerimis', 'icon' => 'bi-cloud-drizzle-fill', 'color' => '#38bdf8'];
            case 61:
            case 63:
            case 65:
            case 80:
            case 81:
            case 82:
                return ['description' => 'Hujan', 'icon' => 'bi-cloud-rain-fill', 'color' => '#0284c7'];
            case 66:
            case 67:
                return ['description' => 'Hujan Dingin', 'icon' => 'bi-cloud-sleet-fill', 'color' => '#38bdf8'];
            case 71:
            case 73:
            case 75:
            case 77:
            case 85:
            case 86:
                return ['description' => 'Hujan Salju', 'icon' => 'bi-cloud-snow-fill', 'color' => '#cbd5e1'];
            case 95:
            case 96:
            case 99:
                return ['description' => 'Badai Guntur', 'icon' => 'bi-cloud-lightning-rain-fill', 'color' => '#dc2626'];
            default:
                return ['description' => 'Cerah', 'icon' => 'bi-sun-fill', 'color' => '#f59e0b'];
        }
    }
}