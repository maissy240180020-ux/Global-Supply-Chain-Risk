<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CountryService
{
    public function getCountries()
    {
        $response = Http::get(
            'https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/country-list/records?limit=250'
        );

        if (!$response->successful()) {
            return [];
        }

        return $response->json()['results'] ?? [];
    }
}