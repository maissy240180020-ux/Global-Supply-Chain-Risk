<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\DB;
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
        $ports = $portsQuery->orderBy('country_code')->orderBy('port_name')->get();

        // Total statistik
        $totalPorts = DB::table('ports')->count();
        $totalCountriesWithPorts = $countriesWithPorts->count();

        return view('ports.index', compact(
            'countries', 'ports', 'selectedCountryCode',
            'totalPorts', 'totalCountriesWithPorts'
        ));
    }
}