<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Support\Facades\Http;

class ApiCountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    // ==========================================
    // Unified API & Integrasi Dashboard
    // ==========================================
    public function index()
    {
        // View dashboard API admin
        return view('admin.api');
    }

    /**
     * Endpoint internal API untuk mengecek latensi semua eksternal API
     */
    public function ping()
    {
        $apis = [
            'world_bank' => ['name' => 'World Bank API', 'url' => 'http://api.worldbank.org/v2/country/id/indicator/NY.GDP.MKTP.CD?format=json'],
            'rest_countries' => ['name' => 'REST Countries', 'url' => 'https://restcountries.com/v3.1/alpha/id'],
            'exchange_rate' => ['name' => 'ExchangeRate', 'url' => 'https://open.er-api.com/v6/latest/USD'],
            'open_meteo' => ['name' => 'Open-Meteo', 'url' => 'https://api.open-meteo.com/v1/forecast?latitude=-6.2088&longitude=106.8456&current=temperature_2m'],
            'gnews' => ['name' => 'GNews API', 'url' => 'https://gnews.io/api/v4/search?q=logistics&max=1&apikey=dummy'], // Just pinging endpoint
            'local_api' => ['name' => 'Local REST API', 'url' => url('/api/countries')]
        ];

        $results = [];

        foreach ($apis as $key => $api) {
            $start = microtime(true);
            try {
                // Timeout singkat 3 detik
                $response = Http::timeout(3)->withoutVerifying()->get($api['url']);
                $end = microtime(true);
                $latency = round(($end - $start) * 1000); // ms

                $results[$key] = [
                    'name' => $api['name'],
                    'status' => $response->successful() || $response->status() === 401 || $response->status() === 403 ? 'Connected' : 'Disconnected',
                    'latency' => $latency . ' ms',
                    'success' => $response->successful() || $response->status() === 401 || $response->status() === 403
                ];
            } catch (\Exception $e) {
                $results[$key] = [
                    'name' => $api['name'],
                    'status' => 'Disconnected',
                    'latency' => 'Timeout',
                    'success' => false
                ];
            }
        }

        return response()->json($results);
    }

    // ==========================================
    // Import Seluruh Negara
    // ==========================================
    public function import()
    {
        $apiCountries = $this->countryService->getCountries();

        if (empty($apiCountries)) {
            // Fallback ke CSV jika API REST Countries offline
            $file = database_path('data/countries.csv');
            if (!file_exists($file)) {
                return redirect()
                    ->route('countries.index')
                    ->with('error', 'API gagal diakses dan countries.csv tidak ditemukan.');
            }

            $handle = fopen($file, 'r');
            fgetcsv($handle);
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                Country::updateOrCreate(
                    ['country_code' => $row[1]],
                    [
                        'country_name' => $row[0],
                        'flag' => $row[2],
                        'capital' => $row[3],
                        'currency' => $row[4],
                        'population' => $row[5],
                        'latitude' => $row[6],
                        'longitude' => $row[7],
                        'gdp' => $row[8] ?? 0,
                        'inflation' => $row[9] ?? 0,
                        'risk_score' => $row[10] ?? 30,
                        'risk_level' => $row[11] ?? 'Low',
                        'temperature' => $row[12] ?? 25,
                        'weather' => $row[13] ?? 'Cerah',
                    ]
                );
            }
            fclose($handle);

            return redirect()
                ->route('countries.index')
                ->with('success', 'Data negara berhasil diimport dari file CSV cadangan.');
        }

        // Import dari REST Countries API langsung!
        foreach ($apiCountries as $item) {
            $code = $item['cca2'] ?? null;
            if (!$code) continue;

            $name = $item['name']['common'] ?? 'Unknown';
            $flag = $item['flags']['png'] ?? null;
            $capital = isset($item['capital'][0]) ? $item['capital'][0] : '-';
            
            $currency = 'USD';
            if (isset($item['currencies']) && is_array($item['currencies'])) {
                $currency = array_key_first($item['currencies']) ?? 'USD';
            }

            $population = $item['population'] ?? 0;
            $lat = isset($item['latlng'][0]) ? $item['latlng'][0] : 0.0;
            $lng = isset($item['latlng'][1]) ? $item['latlng'][1] : 0.0;

            // kalkulasi tingkat risiko awal
            $riskScore = rand(15, 75);
            $riskLevel = 'Low';
            if ($riskScore > 60) {
                $riskLevel = 'High';
            } elseif ($riskScore > 35) {
                $riskLevel = 'Medium';
            }

            Country::updateOrCreate(
                ['country_code' => $code],
                [
                    'country_name' => $name,
                    'flag' => $flag,
                    'capital' => $capital,
                    'currency' => $currency,
                    'population' => $population,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'gdp' => 0, // Akan di-update via World Bank API secara realtime
                    'inflation' => 0,
                    'risk_score' => $riskScore,
                    'risk_level' => $riskLevel,
                    'temperature' => 25,
                    'weather' => 'Cerah',
                ]
            );
        }

        return redirect()
            ->route('countries.index')
            ->with('success', 'Berhasil mengimpor seluruh negara dunia secara dinamis dari REST Countries API.');
    }
}