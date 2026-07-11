<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class NilaiTukarController extends Controller
{
    public function index()
    {
        $kurs = null;

        $response = Http::get(
            'https://api.frankfurter.app/latest',
            [
                'from' => 'USD',
                'to' => 'IDR,EUR,JPY'
            ]
        );

        if($response->successful()){

            $kurs = $response->json();

        }

        return view('currency.index', compact('kurs'));
    }
}