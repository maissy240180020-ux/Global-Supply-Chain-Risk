<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua negara untuk dropdown
        $countries = Country::orderBy('country_name')->get();

        // 2. Tentukan negara terpilih
        $selectedCountryId = $request->input('country_id');
        $selectedCountry = null;

        if ($selectedCountryId) {
            $selectedCountry = Country::find($selectedCountryId);
        }

        if (!$selectedCountry) {
            $selectedCountry = Country::where('country_code', 'ID')->first() ?? Country::first();
        }

        // 3. Ambil kata leksikon dari database
        $positiveWords = DB::table('positive_words')->pluck('word')->toArray();
        $negativeWords = DB::table('negative_words')->pluck('word')->toArray();

        $berita = [];
        $posCount = 0;
        $negCount = 0;
        $sentiment = 'Neutral';

        // 4. Ambil berita dari Google RSS berdasarkan negara terpilih
        try {
            $query = urlencode('supply chain ' . ($selectedCountry ? $selectedCountry->country_name : ''));
            $url = "https://news.google.com/rss/search?q={$query}&hl=en-US&gl=US&ceid=US:en";
            $rss = @simplexml_load_file($url);

            if ($rss && isset($rss->channel->item)) {
                $count = 0;
                foreach ($rss->channel->item as $item) {
                    $title = (string)$item->title;
                    $link  = (string)$item->link;
                    $pubDate = date('d M Y, H:i', strtotime((string)$item->pubDate));
                    $source = '';
                    if (isset($item->source)) {
                        $source = (string)$item->source;
                    }

                    // Lexicon-based sentiment analysis
                    $words = preg_split('/[^a-zA-Z]+/', strtolower($title));
                    $itemPos = 0;
                    $itemNeg = 0;
                    foreach ($words as $word) {
                        if (in_array($word, $positiveWords)) { $itemPos++; $posCount++; }
                        if (in_array($word, $negativeWords)) { $itemNeg++; $negCount++; }
                    }

                    $itemSentiment = 'Neutral';
                    if ($itemPos > $itemNeg) $itemSentiment = 'Positive';
                    elseif ($itemNeg > $itemPos) $itemSentiment = 'Negative';

                    $berita[] = [
                        'judul'     => $title,
                        'link'      => $link,
                        'tanggal'   => $pubDate,
                        'source'    => $source ?: 'Google News',
                        'sentiment' => $itemSentiment,
                    ];

                    if (++$count >= 10) break;
                }

                // Overall sentiment
                if ($posCount > $negCount) $sentiment = 'Positive';
                elseif ($negCount > $posCount) $sentiment = 'Negative';
            }
        } catch (\Exception $e) {
            $berita = [];
        }

        return view('news.index', compact(
            'countries', 'selectedCountry', 'berita',
            'posCount', 'negCount', 'sentiment'
        ));
    }
}