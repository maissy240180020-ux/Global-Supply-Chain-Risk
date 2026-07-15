<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CountryService
{
    public function getCountries()
    {
        try {
            $response = Http::timeout(4)->get(
                'https://restcountries.com/v3.1/all?fields=name,cca2,capital,currencies,population,latlng,flags'
            );

            if ($response->successful() && !isset($response->json()['errors'])) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Log or ignore to trigger fallback
        }

        // Fallback ke dataset lokal yang murni berisi negara berdaulat resmi (Anggota PBB)
        $file = database_path('data/world_countries.json');
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true);
        }

        return [];
    }
}