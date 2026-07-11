<?php

namespace App\Http\Controllers;

use App\Services\CountryService;

class ApiCountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index()
    {
        $countries = $this->countryService->getCountries();

        return view('countries.api', compact('countries'));
    }
}