<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class CuacaController extends Controller
{
    public function index()
    {
        // Koordinat Jakarta
        $latitude = -6.2088;
        $longitude = 106.8456;

        $cuaca = null;

        try {

            $response = Http::timeout(20)
                ->retry(3, 1000)
                ->get(
                    'https://api.open-meteo.com/v1/forecast',
                    [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'current' => 'temperature_2m,wind_speed_10m,wind_direction_10m',
                        'timezone' => 'Asia/Jakarta'
                    ]
                );

            if ($response->successful()) {

                $data = $response->json();

                if (isset($data['current'])) {

                    $cuaca = $data['current'];

                }

            }

        } catch (\Exception $e) {

            // Data cadangan jika API gagal
            $cuaca = [
                'temperature_2m' => 29,
                'wind_speed_10m' => 14,
                'wind_direction_10m' => 180,
                'time' => now()->format('Y-m-d H:i')
            ];

        }

        return view('cuaca.index', compact('cuaca'));
    }
}