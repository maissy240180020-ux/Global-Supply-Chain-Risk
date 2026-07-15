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
        // 1. Ambil data dari REST Countries API (PDF Halaman 3)
        $restCountries = $this->countryService->getCountries();

        // 2. Ambil data lokal untuk pemilih negara World Bank API (PDF Halaman 2)
        $dbCountries = Country::orderBy('country_name')->get();
        
        $selectedCountryId = request('country_id');
        $selectedCountry = null;

        if ($selectedCountryId) {
            $selectedCountry = Country::find($selectedCountryId);
        }

        if (!$selectedCountry) {
            $selectedCountry = Country::where('country_code', 'ID')->first() ?? Country::first();
        }

        // Fetch data from World Bank API for the selected country
        $gdp = null;
        $inflation = null;
        $population = null;
        $exports = null;
        $imports = null;

        if ($selectedCountry) {
            $code = strtolower($selectedCountry->country_code);
            $indicators = [
                'gdp' => 'NY.GDP.MKTP.CD',
                'inflation' => 'FP.CPI.TOTL.ZG',
                'population' => 'SP.POP.TOTL',
                'exports' => 'NE.EXP.GNFS.CD',
                'imports' => 'NE.IMP.GNFS.CD'
            ];

            try {
                $responseGdp = Http::timeout(4)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['gdp']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();
                
                $responseInflation = Http::timeout(4)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['inflation']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();

                $responsePopulation = Http::timeout(4)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['population']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();

                $responseExports = Http::timeout(4)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['exports']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();

                $responseImports = Http::timeout(4)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['imports']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();

                $gdp = isset($responseGdp[1]) ? $responseGdp[1] : null;
                $inflation = isset($responseInflation[1]) ? $responseInflation[1] : null;
                $population = isset($responsePopulation[1]) ? $responsePopulation[1] : null;
                $exports = isset($responseExports[1]) ? $responseExports[1] : null;
                $imports = isset($responseImports[1]) ? $responseImports[1] : null;

            } catch (\Exception $e) {
                // API fallback handled in view
            }
        }

        return view('countries.api', compact(
            'restCountries',
            'dbCountries',
            'selectedCountry',
            'gdp',
            'inflation',
            'population',
            'exports',
            'imports'
        ));
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