<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SentimentController extends Controller
{
    public static function analyze($text)
    {
        // Ambil kata leksikon positif dan negatif dari database
        $positiveWords = DB::table('positive_words')->pluck('word')->toArray();
        $negativeWords = DB::table('negative_words')->pluck('word')->toArray();

        // Bersihkan teks dan pecah menjadi kata-kata tunggal
        $words = preg_split('/[^a-zA-Z]+/', strtolower($text));
        
        $pos = 0;
        $neg = 0;
        
        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $pos++;
            }
            if (in_array($word, $negativeWords)) {
                $neg++;
            }
        }

        // Tentukan label sentimen berdasarkan kalkulasi polaritas leksikon
        if ($pos > $neg) {
            return 'Positive';
        } elseif ($neg > $pos) {
            return 'Negative';
        }
        
        return 'Neutral';
    }
}
