<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class NilaiTukarController extends Controller
{
    public function index(Request $request)
    {
        $base = strtoupper($request->input('base', 'USD'));
        $kurs = null;
        $supported = [];
        $error = null;

        try {
            // Menggunakan open.er-api.com yang gratis dan mendukung ~160 mata uang seluruh dunia
            $response = Http::timeout(10)->get("https://open.er-api.com/v6/latest/{$base}");

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates'])) {
                    $kurs = [
                        'base' => $base,
                        'date' => date('Y-m-d H:i:s', $data['time_last_update_unix']),
                        'rates' => $data['rates']
                    ];
                    $supported = array_keys($data['rates']);
                }
            } else {
                throw new \Exception("Gagal menghubungi ExchangeRate API. Status: " . $response->status());
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
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

        // Jika request via AJAX, kembalikan JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => $error ? false : true,
                'kurs'    => $kurs,
                'error'   => $error,
                'supported' => $supported
            ]);
        }

        return view('currency.index', compact('kurs', 'supported', 'base', 'currencyMeta', 'error'));
    }
}