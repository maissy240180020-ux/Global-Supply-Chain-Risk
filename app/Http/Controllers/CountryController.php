<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{

    // ===============================
    // DAFTAR NEGARA
    // ===============================

    public function index(Request $request)
    {
        $search = $request->search;

        $countries = Country::when($search, function ($query) use ($search) {

            $query->where('country_name', 'like', "%{$search}%")
                  ->orWhere('country_code', 'like', "%{$search}%")
                  ->orWhere('capital', 'like', "%{$search}%")
                  ->orWhere('currency', 'like', "%{$search}%");

        })
        ->orderBy('country_name')
        ->paginate(15)
        ->withQueryString();

        if ($request->ajax()) {
            $html = view('countries.partials.rows', compact('countries'))->render();
            return response()->json([
                'html' => $html,
                'next_page' => $countries->nextPageUrl(),
                'has_more' => $countries->hasMorePages(),
                'current_count' => $countries->lastItem() ?? 0,
                'total' => $countries->total()
            ]);
        }

        $mapCountries = Country::all();

        return view('countries.index', compact('countries', 'mapCountries'));
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
        return view('countries.show', compact('country'));
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