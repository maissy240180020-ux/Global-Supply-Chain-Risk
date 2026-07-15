<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function index()
    {
        try {
            $response = Http::timeout(5)->get('https://api.frankfurter.app/latest', [
                'from' => 'USD',
                'to' => 'IDR,EUR,JPY,AUD,CNY,INR,BRL,RUB,SGD'
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Exception $e) {
            // Fallback
        }

        return response()->json([
            'amount' => 1.0,
            'base' => 'USD',
            'date' => date('Y-m-d'),
            'rates' => [
                'IDR' => 16250.0,
                'EUR' => 0.92,
                'JPY' => 158.0,
                'AUD' => 1.50,
                'CNY' => 7.25,
                'INR' => 83.50,
                'BRL' => 5.40,
                'RUB' => 87.0,
                'SGD' => 1.35
            ]
        ]);
    }
}
