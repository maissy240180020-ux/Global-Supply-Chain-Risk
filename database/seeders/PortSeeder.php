<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing ports
        DB::table('ports')->truncate();

        $file = database_path('data/ports.json');
        if (!file_exists($file)) {
            $this->command->error('ports.json tidak ditemukan.');
            return;
        }

        $data = json_decode(file_get_contents($file), true);
        $ports = $data['ports'] ?? [];

        // Load countries from database to build lookup map
        $countries = Country::all();
        $countryByName = [];
        $countryByCode = [];
        foreach ($countries as $c) {
            $countryByName[strtolower($c->country_name)] = $c->country_code;
            $countryByCode[strtolower($c->country_code)] = $c->country_code;
        }

        // Manual mapping for country name variants in the ports dataset
        $manualMap = [
            'united states' => 'US',
            'united kingdom' => 'GB',
            'south korea' => 'KR',
            'north korea' => 'KP',
            'russia' => 'RU',
            'vietnam' => 'VN',
            'iran' => 'IR',
            'syria' => 'SY',
            'venezuela' => 'VE',
            'taiwan' => 'TW',
            'laos' => 'LA',
            'bolivia' => 'BO',
            'brunei' => 'BN',
            'tanzania' => 'TZ',
            'moldova' => 'MD',
            'macedonia' => 'MK',
            'palestine' => 'PS',
            'vatican' => 'VA',
            'micronesia' => 'FM',
            'macau' => 'MO',
            'hong kong' => 'HK',
            'cook islands' => 'CK',
            'falkland islands' => 'FK',
            'faeroe islands' => 'FO',
            'french polynesia' => 'PF',
            'netherlands antilles' => 'NL',
            'turks and caicos islands' => 'TC',
            'virgin islands' => 'VI',
            'saint helena' => 'SH',
            'saint lucia' => 'LC',
            'saint kitts and nevis' => 'KN',
            'saint vincent and the grenadines' => 'VC',
            'wallis and futuna' => 'WF',
            'northern mariana islands' => 'MP',
        ];

        $records = [];
        foreach ($ports as $port) {
            $name = $port['wpi_port_name'] ?: ($port['point_of_interest'] ?: 'Unknown Port');
            // Normalize name to title case
            $name = ucwords(strtolower($name));

            $countryName = trim($port['country'] ?? '');
            $countryCode = 'US'; // fallback default

            if ($countryName) {
                $lowerName = strtolower($countryName);
                if (isset($countryByName[$lowerName])) {
                    $countryCode = $countryByName[$lowerName];
                } elseif (isset($manualMap[$lowerName])) {
                    $countryCode = $manualMap[$lowerName];
                } else {
                    if (strlen($countryName) === 2 && isset($countryByCode[$lowerName])) {
                        $countryCode = $countryByCode[$lowerName];
                    } else {
                        // Check if it's already a country code in upper/lowercase
                        $matched = false;
                        foreach ($countryByName as $nameKey => $code) {
                            if (str_contains($nameKey, $lowerName) || str_contains($lowerName, $nameKey)) {
                                $countryCode = $code;
                                $matched = true;
                                break;
                            }
                        }
                        if (!$matched) {
                            // If not matched, try to match by first word
                            $firstWord = explode(' ', $lowerName)[0] ?? '';
                            if ($firstWord && strlen($firstWord) > 2) {
                                foreach ($countryByName as $nameKey => $code) {
                                    if (str_contains($nameKey, $firstWord)) {
                                        $countryCode = $code;
                                        $matched = true;
                                        break;
                                    }
                                }
                            }
                        }
                        if (!$matched) {
                            // Try to clean the country name and use first two chars
                            $cleanCountry = preg_replace('/[^A-Za-z]/', '', $countryName);
                            if (strlen($cleanCountry) >= 2) {
                                $countryCode = strtoupper(substr($cleanCountry, 0, 2));
                            } else {
                                $countryCode = 'XX';
                            }
                        }
                    }
                }
            }

            // Final safety filter for country_code
            $countryCode = preg_replace('/[^A-Za-z]/', '', $countryCode);
            $countryCode = strtoupper(substr($countryCode, 0, 2));
            if (strlen($countryCode) < 2) {
                $countryCode = 'XX';
            }

            $lat = isset($port['latitude']) ? (float)$port['latitude'] : 0.0;
            $lng = isset($port['longitude']) ? (float)$port['longitude'] : 0.0;

            $records[] = [
                'port_name' => $name,
                'country_code' => $countryCode,
                'latitude' => $lat,
                'longitude' => $lng,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert in chunks of 500 records
        foreach (array_chunk($records, 500) as $chunk) {
            DB::table('ports')->insert($chunk);
        }

        $this->command->info('Sukses mengimpor ' . count($records) . ' data pelabuhan global.');
    }
}

