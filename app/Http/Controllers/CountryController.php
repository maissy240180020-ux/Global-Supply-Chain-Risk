<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class CountryController extends Controller
{

    // ===============================
    // DAFTAR NEGARA
    // ===============================

    public function index(Request $request)
    {
        $search = $request->search;
        $region = $request->region;
        $risk_level = $request->risk_level;

        $countries = Country::when($search, function ($query) use ($search) {
            $query->where(function($q) use ($search) {
                $q->where('country_name', 'like', "%{$search}%")
                  ->orWhere('country_code', 'like', "%{$search}%")
                  ->orWhere('capital', 'like', "%{$search}%")
                  ->orWhere('currency', 'like', "%{$search}%");
            });
        })
        ->when($region, function ($query) use ($region) {
            $query->where('region', $region);
        })
        ->when($risk_level, function ($query) use ($risk_level) {
            $query->where('risk_level', $risk_level);
        })
        ->orderBy('country_name')
        ->paginate(15)
        ->withQueryString();

        $mapCountries = Country::all();
        $regions = Country::whereNotNull('region')->where('region', '!=', '-')->distinct()->pluck('region')->sort();
        
        $riskCounts = [
            'High' => Country::where('risk_level', 'High')->count(),
            'Medium' => Country::where('risk_level', 'Medium')->count(),
            'Low' => Country::where('risk_level', 'Low')->count()
        ];

        $userFavorites = auth()->check() ? auth()->user()->favorites()->pluck('country_id')->toArray() : [];

        if ($request->ajax()) {
            $html = view('countries.partials.rows', compact('countries', 'userFavorites'))->render();
            return response()->json([
                'html' => $html,
                'next_page' => $countries->nextPageUrl(),
                'has_more' => $countries->hasMorePages(),
                'current_count' => $countries->lastItem() ?? 0,
                'total' => $countries->total()
            ]);
        }

        return view('countries.index', compact('countries', 'mapCountries', 'regions', 'riskCounts', 'userFavorites'));
    }

    // ===============================
    // FORM TAMBAH NEGARA
    // ===============================

    public function create()
    {
        $apiCountries = (new \App\Services\CountryService)->getCountries();

        // Sort alphabetically by common name
        usort($apiCountries, function($a, $b) {
            return strcmp($a['name']['common'] ?? '', $b['name']['common'] ?? '');
        });

        return view('countries.create', compact('apiCountries'));
    }

    // ===============================
    // SIMPAN DATA NEGARA
    // ===============================

    public function store(Request $request)
    {

        $request->validate([

            'country_name' => 'required',
            'country_code' => 'required',
            'capital'      => 'required',
            'currency'     => 'required',

        ]);

        Country::create([

            // Informasi Negara
            'country_name' => $request->country_name,
            'country_code' => $request->country_code,
            'flag'         => $request->flag,
            'capital'      => $request->capital,

            // Ekonomi
            'currency'     => $request->currency,
            'gdp'          => $request->gdp,
            'inflation'    => $request->inflation,
            'population'   => $request->population,

            // Risiko
            'risk_score'   => $request->risk_score,
            'risk_level'   => $request->risk_level,

            // Cuaca
            'temperature'  => $request->temperature,
            'weather'      => $request->weather,

            // Koordinat
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,

        ]);

        return redirect()

            ->route('countries.index')

            ->with(

                'success',

                'Data negara berhasil ditambahkan.'

            );

    }

    // ===============================
    // DETAIL NEGARA
    // ===============================

    public function show(Country $country)
    {
        // Default additional data
        $extraData = [
            'region' => '-',
            'area' => '-',
            'language' => '-',
            'exchange_rate' => 1,
            'humidity' => '-',
            'wind_speed' => '-',
            'rainfall' => '-',
            'export' => 0,
            'import' => 0
        ];

        // MOCK Export Import based on GDP (Export ~25% of GDP, Import ~22% of GDP)
        $gdp = $country->gdp ?? 0;
        if ($gdp > 0) {
            $extraData['export'] = $gdp * 0.25;
            $extraData['import'] = $gdp * 0.22;
        }

        try {
            $responses = Http::pool(fn (Pool $pool) => [
                $pool->as('rest')->timeout(5)->withoutVerifying()->get("https://restcountries.com/v3.1/alpha/{$country->country_code}"),
                $pool->as('er')->timeout(5)->withoutVerifying()->get("https://open.er-api.com/v6/latest/USD"),
                $pool->as('meteo')->timeout(5)->withoutVerifying()->get("https://api.open-meteo.com/v1/forecast", [
                    'latitude' => $country->latitude ?? 0,
                    'longitude' => $country->longitude ?? 0,
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,precipitation'
                ])
            ]);

            // Parse REST Countries
            $restSuccess = false;
            if ($responses['rest']->successful()) {
                $restJson = $responses['rest']->json();
                if (!isset($restJson['errors'])) {
                    $restData = $restJson[0] ?? [];
                    if (!empty($restData)) {
                        $extraData['region'] = $restData['region'] ?? ($restData['subregion'] ?? '-');
                        $extraData['area'] = isset($restData['area']) ? number_format($restData['area']) : '-';
                        
                        if (isset($restData['languages']) && is_array($restData['languages'])) {
                            $extraData['language'] = implode(', ', array_values($restData['languages']));
                        }
                        $restSuccess = true;
                    }
                }
            }

            // Fallback for REST Countries
            if (!$restSuccess) {
                $localPath = database_path('data/countries_extended.json');
                if (file_exists($localPath)) {
                    $localData = json_decode(file_get_contents($localPath), true);
                    foreach ($localData as $item) {
                        if (isset($item['cca2']) && strtoupper($item['cca2']) === strtoupper($country->country_code)) {
                            $extraData['region'] = $item['region'] ?? ($item['subregion'] ?? '-');
                            $extraData['area'] = isset($item['area']) ? number_format($item['area']) : '-';
                            if (isset($item['languages']) && is_array($item['languages'])) {
                                $extraData['language'] = implode(', ', array_values($item['languages']));
                            }
                            break;
                        }
                    }
                }
            }

            // Parse Exchange Rate
            if ($responses['er']->successful()) {
                $erData = $responses['er']->json();
                $currencyCode = $country->currency ?? 'USD';
                if (isset($erData['rates'][$currencyCode])) {
                    $extraData['exchange_rate'] = $erData['rates'][$currencyCode];
                }
            }

            // Parse Open-Meteo
            if ($responses['meteo']->successful()) {
                $meteoData = $responses['meteo']->json();
                if (isset($meteoData['current'])) {
                    $curr = $meteoData['current'];
                    $extraData['humidity'] = $curr['relative_humidity_2m'] ?? '-';
                    $extraData['wind_speed'] = $curr['wind_speed_10m'] ?? '-';
                    $extraData['rainfall'] = $curr['precipitation'] ?? '-';
                    // Optional: update temp based on real-time api
                    if (isset($curr['temperature_2m'])) {
                        $country->temperature = $curr['temperature_2m'];
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fallback if APIs fail
        }

        return view('countries.show', compact('country', 'extraData'));
    }

    // ===============================
    // FORM UBAH NEGARA
    // ===============================

    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    // ===============================
    // UPDATE NEGARA
    // ===============================

    public function update(Request $request, Country $country)
    {

        $request->validate([

            'country_name' => 'required',
            'country_code' => 'required',
            'capital'      => 'required',
            'currency'     => 'required',

        ]);

        $country->update([

            // Informasi Negara
            'country_name' => $request->country_name,
            'country_code' => $request->country_code,
            'capital'      => $request->capital,

            // Ekonomi
            'currency'     => $request->currency,
            'gdp'          => $request->gdp,
            'inflation'    => $request->inflation,
            'population'   => $request->population,

            // Risiko
            'risk_score'   => $request->risk_score,
            'risk_level'   => $request->risk_level,

            // Cuaca
            'temperature'  => $request->temperature,
            'weather'      => $request->weather,

            // Koordinat
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,

        ]);

        return redirect()

            ->route('countries.index')

            ->with(

                'success',

                'Data negara berhasil diperbarui.'

            );

    }

    // ===============================
    // HAPUS NEGARA
    // ===============================

    public function destroy(Country $country)
    {

        $country->delete();

        return redirect()

            ->route('countries.index')

            ->with(

                'success',

                'Data negara berhasil dihapus.'

            );

    }

}