<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

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

        return view('compare.index', compact(
            'countries',
            'countryA',
            'countryB'
        ));

    }

}