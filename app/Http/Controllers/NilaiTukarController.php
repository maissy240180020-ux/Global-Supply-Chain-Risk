<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class NilaiTukarController extends Controller
{
    public function index(Request $request)
    {
        $base = strtoupper($request->input('base', 'USD'));
        
        $supported = ['USD', 'IDR', 'EUR', 'JPY', 'GBP', 'AUD', 'SGD', 'CNY', 'INR', 'BRL', 'RUB', 'CAD', 'CHF', 'MYR', 'PHP', 'THB'];
        
        if (!in_array($base, $supported)) {
            $base = 'USD';
        }

        $targets = array_diff($supported, [$base]);
        $targetStr = implode(',', $targets);

        $kurs = null;

        try {
            $response = Http::timeout(10)->get(
                'https://api.frankfurter.app/latest',
                [
                    'from' => $base,
                    'to' => $targetStr
                ]
            );

            if ($response->successful()) {
                $kurs = $response->json();
            }
        } catch (\Exception $e) {
            // Abaikan error untuk fallback
        }

        if (!$kurs) {
            // Mock dynamic fallback rates based on standard baseline USD rates
            $usdRates = [
                'USD' => 1.0,
                'IDR' => 16250.0,
                'EUR' => 0.92,
                'JPY' => 158.0,
                'GBP' => 0.78,
                'AUD' => 1.50,
                'SGD' => 1.35,
                'CNY' => 7.25,
                'INR' => 83.50,
                'BRL' => 5.40,
                'RUB' => 87.0,
                'CAD' => 1.37,
                'CHF' => 0.89,
                'MYR' => 4.67,
                'PHP' => 58.50,
                'THB' => 36.40,
            ];

            $rates = [];
            $baseUsdRate = $usdRates[$base] ?? 1.0;
            foreach ($usdRates as $curr => $usdRate) {
                if ($curr !== $base) {
                    $rates[$curr] = $usdRate / $baseUsdRate;
                }
            }

            $kurs = [
                'amount' => 1.0,
                'base' => $base,
                'date' => now()->format('Y-m-d') . ' (Fallback)',
                'rates' => $rates
            ];
        }

        // Data pendukung nama mata uang dan bendera negara
        $currencyMeta = [
            'USD' => ['name' => 'Dolar Amerika Serikat', 'flag' => '🇺🇸'],
            'IDR' => ['name' => 'Rupiah Indonesia', 'flag' => '🇮🇩'],
            'EUR' => ['name' => 'Euro Uni Eropa', 'flag' => '🇪🇺'],
            'JPY' => ['name' => 'Yen Jepang', 'flag' => '🇯🇵'],
            'GBP' => ['name' => 'Pound Sterling Inggris', 'flag' => '🇬🇧'],
            'AUD' => ['name' => 'Dolar Australia', 'flag' => '🇦🇺'],
            'SGD' => ['name' => 'Dolar Singapura', 'flag' => '🇸🇬'],
            'CNY' => ['name' => 'Yuan China', 'flag' => '🇨🇳'],
            'INR' => ['name' => 'Rupee India', 'flag' => '🇮🇳'],
            'BRL' => ['name' => 'Real Brasil', 'flag' => '🇧🇷'],
            'RUB' => ['name' => 'Ruble Rusia', 'flag' => '🇷🇺'],
            'CAD' => ['name' => 'Dolar Kanada', 'flag' => '🇨🇦'],
            'CHF' => ['name' => 'Franc Swiss', 'flag' => '🇨🇭'],
            'MYR' => ['name' => 'Ringgit Malaysia', 'flag' => '🇲🇾'],
            'PHP' => ['name' => 'Peso Filipina', 'flag' => '🇵🇭'],
            'THB' => ['name' => 'Baht Thailand', 'flag' => '🇹🇭'],
        ];

        return view('currency.index', compact('kurs', 'supported', 'base', 'currencyMeta'));
    }
}