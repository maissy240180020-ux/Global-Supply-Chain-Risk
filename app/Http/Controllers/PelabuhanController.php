<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PelabuhanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua negara yang memiliki pelabuhan di database
        $countriesWithPorts = DB::table('ports')
            ->select('country_code')
            ->distinct()
            ->pluck('country_code');

        // Ambil model negara yang punya pelabuhan
        $countries = Country::whereIn('country_code', $countriesWithPorts)
            ->orderBy('country_name')
            ->get();

        // Filter berdasarkan negara yang dipilih (opsional)
        $selectedCountryCode = $request->input('country_code');

        // Ambil semua pelabuhan (atau filter berdasarkan negara)
        $portsQuery = DB::table('ports');
        if ($selectedCountryCode) {
            $portsQuery->where('country_code', $selectedCountryCode);
        }
        $ports = $portsQuery->select('id', 'port_name', 'country_code', 'latitude', 'longitude')
            ->orderBy('country_code')
            ->orderBy('port_name')
            ->get();

        // Total statistik
        $totalPorts = DB::table('ports')->count();
        $totalCountriesWithPorts = $countriesWithPorts->count();

        return view('ports.index', compact(
            'countries', 'ports', 'selectedCountryCode',
            'totalPorts', 'totalCountriesWithPorts'
        ));
    }

    public function searchGlobal(Request $request)
    {
        $query = $request->input('q');
        if (empty($query)) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'SIMRPG Global Supply Chain Risk Port Search'
            ])
            ->timeout(10)
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => 'port ' . $query,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 15
            ]);

            if ($response->successful()) {
                $results = $response->json();
                $formatted = [];

                foreach ($results as $item) {
                    $class = $item['class'] ?? '';
                    $type = $item['type'] ?? '';
                    
                    // Lakukan filter yang fleksibel agar pengguna bisa mencari pelabuhan
                    $isPort = str_contains(strtolower($item['display_name']), 'port') || 
                              str_contains(strtolower($item['display_name']), 'harbour') ||
                              str_contains(strtolower($item['display_name']), 'pelabuhan') ||
                              in_array($class, ['industrial', 'harbour', 'waterway', 'amenity']) ||
                              in_array($type, ['port', 'harbour', 'ferry_terminal']);

                    if ($isPort) {
                        $countryCode = strtoupper($item['address']['country_code'] ?? 'XX');
                        
                        // Ekstrak nama pendek yang rapi untuk pelabuhan
                        $displayName = $item['display_name'];
                        $parts = explode(',', $displayName);
                        $shortName = trim($parts[0]);
                        if (strtolower($shortName) == 'port' && isset($parts[1])) {
                            $shortName = 'Port of ' . trim($parts[1]);
                        }

                        $formatted[] = [
                            'display_name' => $displayName,
                            'port_name' => $shortName,
                            'country_code' => $countryCode,
                            'latitude' => (float)$item['lat'],
                            'longitude' => (float)$item['lon'],
                        ];
                    }
                }

                return response()->json($formatted);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data dari OpenStreetMap: ' . $e->getMessage()], 500);
        }

        return response()->json([]);
    }

    public function storeGlobalPort(Request $request)
    {
        $request->validate([
            'port_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $portName = ucwords(strtolower($request->input('port_name')));
        $countryCode = strtoupper($request->input('country_code'));
        $latitude = (float)$request->input('latitude');
        $longitude = (float)$request->input('longitude');

        // Buat model negara placeholder jika belum ada, agar relasi data tidak error
        $countryExists = Country::where('country_code', $countryCode)->exists();
        if (!$countryExists) {
            Country::create([
                'country_code' => $countryCode,
                'country_name' => $countryCode,
                'flag' => 'https://flagcdn.com/w320/' . strtolower($countryCode) . '.png',
                'capital' => '-',
                'currency' => 'USD',
                'population' => 0,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'gdp' => 0,
                'inflation' => 0,
                'risk_score' => 50,
                'risk_level' => 'Medium',
                'temperature' => 25,
                'weather' => 'Cerah'
            ]);
        }

        DB::table('ports')->updateOrInsert(
            [
                'port_name' => $portName,
                'latitude' => $latitude,
                'longitude' => $longitude
            ],
            [
                'country_code' => $countryCode,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $port = DB::table('ports')
            ->where('port_name', $portName)
            ->where('latitude', $latitude)
            ->where('longitude', $longitude)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Pelabuhan berhasil ditambahkan ke database.',
            'port' => $port
        ]);
    }
}