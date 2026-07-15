<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function index()
    {
        $url = "https://news.google.com/rss/search?q=supply+chain&hl=en-US&gl=US&ceid=US:en";
        $berita = [];

        try {
            $rss = @simplexml_load_file($url);
            if ($rss && isset($rss->channel->item)) {
                $positiveWords = DB::table('positive_words')->pluck('word')->toArray();
                $negativeWords = DB::table('negative_words')->pluck('word')->toArray();

                $count = 0;
                foreach ($rss->channel->item as $item) {
                    $title = (string)$item->title;
                    $link = (string)$item->link;
                    $pubDate = date('d M Y H:i', strtotime((string)$item->pubDate));

                    $words = preg_split('/[^a-zA-Z]+/', strtolower($title));
                    $pos = 0;
                    $neg = 0;
                    foreach ($words as $word) {
                        if (in_array($word, $positiveWords)) $pos++;
                        if (in_array($word, $negativeWords)) $neg++;
                    }

                    $sentiment = 'Neutral';
                    if ($pos > $neg) $sentiment = 'Positive';
                    elseif ($neg > $pos) $sentiment = 'Negative';

                    $berita[] = [
                        'title' => $title,
                        'link' => $link,
                        'published_at' => $pubDate,
                        'sentiment' => $sentiment
                    ];

                    $count++;
                    if ($count >= 10) break;
                }
            }
        } catch (\Exception $e) {
            $berita = [];
        }

        return response()->json($berita);
    }
}
