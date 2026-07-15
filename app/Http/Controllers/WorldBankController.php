<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\Http;

class WorldBankController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('country_name')->get();
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
            
            // World Bank Indicators
            $indicators = [
                'gdp' => 'NY.GDP.MKTP.CD', // GDP (current USD)
                'inflation' => 'FP.CPI.TOTL.ZG', // Inflation (annual %)
                'population' => 'SP.POP.TOTL', // Population (total)
                'exports' => 'NE.EXP.GNFS.CD', // Exports (current USD)
                'imports' => 'NE.IMP.GNFS.CD' // Imports (current USD)
            ];

            // We fetch the latest 5 years of annual data to draw trend lines/tables
            try {
                $responseGdp = Http::timeout(5)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['gdp']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();
                
                $responseInflation = Http::timeout(5)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['inflation']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();

                $responsePopulation = Http::timeout(5)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['population']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();

                $responseExports = Http::timeout(5)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['exports']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();

                $responseImports = Http::timeout(5)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$indicators['imports']}", [
                    'format' => 'json',
                    'per_page' => 5
                ])->json();

                $gdp = isset($responseGdp[1]) ? $responseGdp[1] : null;
                $inflation = isset($responseInflation[1]) ? $responseInflation[1] : null;
                $population = isset($responsePopulation[1]) ? $responsePopulation[1] : null;
                $exports = isset($responseExports[1]) ? $responseExports[1] : null;
                $imports = isset($responseImports[1]) ? $responseImports[1] : null;

            } catch (\Exception $e) {
                // Fallback to empty values if World Bank API is offline
            }
        }

        return view('world-bank.index', compact(
            'countries',
            'selectedCountry',
            'gdp',
            'inflation',
            'population',
            'exports',
            'imports'
        ));
    }
}
