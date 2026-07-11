<?php

namespace App\Http\Controllers;

class BeritaController extends Controller
{
    public function index()
    {
        $url = "https://news.google.com/rss/search?q=supply+chain&hl=en-US&gl=US&ceid=US:en";

        $berita = [];

        try {

            $rss = simplexml_load_file($url);

            foreach ($rss->channel->item as $item) {

                $berita[] = [

                    'judul' => (string) $item->title,

                    'link' => (string) $item->link,

                    'tanggal' => date(
                        'd M Y H:i',
                        strtotime($item->pubDate)
                    ),

                ];

                if(count($berita) == 5){
                    break;
                }

            }

        } catch (\Exception $e) {

            $berita = [];

        }

        return view('news.index', compact('berita'));
    }
}