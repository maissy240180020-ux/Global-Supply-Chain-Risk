<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompareController extends Controller
{

    public function index()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('compare.index', compact('countries'));
    }

    public function compare(Request $request)
    {
        $countries = Country::orderBy('country_name')->get();

        $countryA = Country::find($request->country1);
        $countryB = Country::find($request->country2);

        if ($countryA) {
            $countryA->port_count = DB::table('ports')
                ->where('country_code', $countryA->country_code)
                ->count();
        }

        if ($countryB) {
            $countryB->port_count = DB::table('ports')
                ->where('country_code', $countryB->country_code)
                ->count();
        }

        return view('compare.index', compact(
            'countries',
            'countryA',
            'countryB'
        ));
    }
}