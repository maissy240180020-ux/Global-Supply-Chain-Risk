<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/world_countries.json');
        
        if (!file_exists($file)) {
            $this->command->error('world_countries.json tidak ditemukan.');
            return;
        }

        $countries = json_decode(file_get_contents($file), true);
        
        $importedCount = 0;
        foreach ($countries as $item) {
            $code = $item['cca2'] ?? null;
            if (!$code) continue;

            $name = $item['name']['common'] ?? 'Unknown';
            $flag = $item['flags']['png'] ?? null;
            $capital = isset($item['capital'][0]) ? $item['capital'][0] : '-';
            
            $currency = 'USD';
            if (isset($item['currencies']) && is_array($item['currencies'])) {
                $currency = array_key_first($item['currencies']) ?? 'USD';
            }

            $population = $item['population'] ?? 0;
            $lat = isset($item['latlng'][0]) ? $item['latlng'][0] : 0.0;
            $lng = isset($item['latlng'][1]) ? $item['latlng'][1] : 0.0;

            // Standard initial risk settings
            $riskScore = rand(15, 75);
            $riskLevel = 'Low';
            if ($riskScore > 60) {
                $riskLevel = 'High';
            } elseif ($riskScore > 35) {
                $riskLevel = 'Medium';
            }

            Country::updateOrCreate(
                ['country_code' => $code],
                [
                    'country_name' => $name,
                    'flag' => $flag,
                    'capital' => $capital,
                    'currency' => $currency,
                    'population' => $population,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'gdp' => 0,
                    'inflation' => 0,
                    'risk_score' => $riskScore,
                    'risk_level' => $riskLevel,
                    'temperature' => 25,
                    'weather' => 'Cerah',
                ]
            );
            $importedCount++;
        }

        $this->command->info("Sukses mengimpor {$importedCount} negara dunia yang diakui secara resmi (Anggota PBB) dari dataset lokal.");
    }
}