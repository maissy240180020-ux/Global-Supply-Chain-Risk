<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            // Indonesia
            ['port_name' => 'Tanjung Priok', 'country_code' => 'ID', 'latitude' => -6.1022, 'longitude' => 106.8833],
            ['port_name' => 'Tanjung Perak', 'country_code' => 'ID', 'latitude' => -7.2023, 'longitude' => 112.7231],
            // Germany
            ['port_name' => 'Port of Hamburg', 'country_code' => 'DE', 'latitude' => 53.5244, 'longitude' => 9.9620],
            ['port_name' => 'Port of Bremen', 'country_code' => 'DE', 'latitude' => 53.5398, 'longitude' => 8.5670],
            // China
            ['port_name' => 'Port of Shanghai', 'country_code' => 'CN', 'latitude' => 30.6267, 'longitude' => 122.0642],
            ['port_name' => 'Port of Shenzhen', 'country_code' => 'CN', 'latitude' => 22.4833, 'longitude' => 113.8833],
            // Australia
            ['port_name' => 'Port of Sydney', 'country_code' => 'AU', 'latitude' => -33.8617, 'longitude' => 151.2108],
            ['port_name' => 'Port of Melbourne', 'country_code' => 'AU', 'latitude' => -37.8443, 'longitude' => 144.9377],
            // United States
            ['port_name' => 'Port of Los Angeles', 'country_code' => 'US', 'latitude' => 33.7287, 'longitude' => -118.2620],
            ['port_name' => 'Port of New York', 'country_code' => 'US', 'latitude' => 40.6720, 'longitude' => -74.0113],
            // Japan
            ['port_name' => 'Port of Tokyo', 'country_code' => 'JP', 'latitude' => 35.6167, 'longitude' => 139.7833],
            ['port_name' => 'Port of Yokohama', 'country_code' => 'JP', 'latitude' => 35.4500, 'longitude' => 139.6667],
            // Singapore
            ['port_name' => 'Port of Singapore', 'country_code' => 'SG', 'latitude' => 1.2740, 'longitude' => 103.8440],
            // Russia
            ['port_name' => 'Port of Vladivostok', 'country_code' => 'RU', 'latitude' => 43.1111, 'longitude' => 131.8735],
            ['port_name' => 'Port of St. Petersburg', 'country_code' => 'RU', 'latitude' => 59.9000, 'longitude' => 30.2167],
            // Brazil
            ['port_name' => 'Port of Santos', 'country_code' => 'BR', 'latitude' => -23.9608, 'longitude' => -46.2997],
            // India
            ['port_name' => 'Port of Nhava Sheva (Mumbai)', 'country_code' => 'IN', 'latitude' => 18.9500, 'longitude' => 72.9500]
        ];

        foreach ($ports as $port) {
            DB::table('ports')->updateOrInsert(
                ['port_name' => $port['port_name']],
                [
                    'country_code' => $port['country_code'],
                    'latitude' => $port['latitude'],
                    'longitude' => $port['longitude'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
