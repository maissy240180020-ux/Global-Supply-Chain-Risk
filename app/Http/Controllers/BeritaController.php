<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

        // 3. Tentukan kategori terpilih (logistik, trade, shipping, ekonomi)
        $category = $request->input('category', 'logistik');
        if (!in_array($category, ['logistik', 'trade', 'shipping', 'ekonomi'])) {
            $category = 'logistik';
        }

        // 4. Ambil kata leksikon dari database untuk analisis sentimen
        $positiveWords = DB::table('positive_words')->pluck('word')->toArray();
        $negativeWords = DB::table('negative_words')->pluck('word')->toArray();

        $berita = [];
        $posCount = 0;
        $negCount = 0;
        $sentiment = 'Neutral';

        // 5. Load real-time news
        $countryName = $selectedCountry ? $selectedCountry->country_name : 'Indonesia';
        $queryTerm = $this->getQueryTermForCategory($category, $countryName);
        
        $apiKey = env('GNEWS_API_KEY');
        $success = false;

        // Coba pakai GNews API dulu jika API Key ada
        if (!empty($apiKey) && $apiKey !== '00000000000000000000000000000000') {
            try {
                $response = Http::withoutVerifying()->timeout(5)->get("https://gnews.io/api/v4/search", [
                    'q' => $queryTerm,
                    'lang' => 'en',
                    'apikey' => $apiKey,
                    'max' => 10
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['articles']) && is_array($data['articles']) && count($data['articles']) > 0) {
                        foreach ($data['articles'] as $article) {
                            $title = $article['title'];
                            $link = $article['url'];
                            $pubDate = date('d M Y, H:i', strtotime($article['publishedAt']));
                            $source = $article['source']['name'] ?? 'GNews';
                            $image = $article['image'] ?? '';
                            $description = $article['description'] ?? '';

                            // Analisis Sentimen Leksikon
                            $words = preg_split('/[^a-zA-Z]+/', strtolower($title . ' ' . $description));
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
                                'deskripsi' => $description,
                                'link'      => $link,
                                'tanggal'   => $pubDate,
                                'source'    => $source,
                                'sentiment' => $itemSentiment,
                                'image'     => $image
                            ];
                        }
                        $success = true;
                    }
                }
            } catch (\Exception $e) {
                // Gagal GNews, lanjut ke Google RSS
            }
        }

        // Jika GNews tidak ada key atau gagal, gunakan Google News RSS
        if (!$success) {
            try {
                $rssQuery = urlencode($queryTerm);
                $url = "https://news.google.com/rss/search?q={$rssQuery}&hl=en-US&gl=US&ceid=US:en";
                
                $context = stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ]);

                $xmlContent = @file_get_contents($url, false, $context);
                
                if ($xmlContent) {
                    $rss = @simplexml_load_string($xmlContent);

                    if ($rss && isset($rss->channel->item)) {
                        $count = 0;
                        
                        // Kumpulan Gambar Premium Unsplash yang Bervariasi per Kategori (Fallback)
                        $imagesPool = [
                            'logistik' => [
                                'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1553413719-87587abb8930?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=500&auto=format&fit=crop&q=60',
                            ],
                            'trade' => [
                                'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1521898284481-a5ec348cb555?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1422493757035-1e5e03968f95?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&auto=format&fit=crop&q=60',
                            ],
                            'shipping' => [
                                'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1509316975850-ff9c5edd0cd9?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1494412687025-a113f8073a41?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1542362567-b07eac790947?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1473445712615-577bf7f50fdc?w=500&auto=format&fit=crop&q=60',
                            ],
                            'ekonomi' => [
                                'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=500&auto=format&fit=crop&q=60',
                                'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=500&auto=format&fit=crop&q=60',
                            ],
                        ];
                        
                        $categoryImages = $imagesPool[$category] ?? $imagesPool['logistik'];

                        foreach ($rss->channel->item as $item) {
                            $title = (string)$item->title;
                            // Decode URL Google Redirect ke URL Asli Media Berita
                            $link  = $this->resolveOriginalUrl((string)$item->link);
                            $pubDate = date('d M Y, H:i', strtotime((string)$item->pubDate));
                            $source = isset($item->source) ? (string)$item->source : 'Google News';
                            $description = strip_tags((string)$item->description);

                            $itemImage = $categoryImages[$count % count($categoryImages)];

                            // Analisis Sentimen Leksikon
                            $words = preg_split('/[^a-zA-Z]+/', strtolower($title . ' ' . $description));
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
                                'deskripsi' => $description,
                                'link'      => $link,
                                'tanggal'   => $pubDate,
                                'source'    => $source,
                                'sentiment' => $itemSentiment,
                                'image'     => $itemImage
                            ];

                            if (++$count >= 10) break;
                        }
                        if (count($berita) > 0) {
                            $success = true;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fail silently
            }
        }

        // Jika semua koneksi API & RSS gagal atau hasilnya kosong, tampilkan data mock
        if (!$success || count($berita) === 0) {
            $berita = $this->generateMockNews($category, $countryName, $positiveWords, $negativeWords, $posCount, $negCount);
        }

        // Tentukan sentimen keseluruhan berdasarkan jumlah total kata positif/negatif
        if ($posCount > $negCount) $sentiment = 'Positive';
        elseif ($negCount > $posCount) $sentiment = 'Negative';

        // Tentukan $country untuk kompatibilitas dengan view news.index yang baru
        $country = $selectedCountry ? $selectedCountry->country_name : 'world';

        return view('news.index', compact(
            'countries', 'selectedCountry', 'berita',
            'posCount', 'negCount', 'sentiment', 'category', 'country'
        ));
    }

    private function getQueryTermForCategory($category, $countryName)
    {
        switch ($category) {
            case 'logistik':
                return "logistics \"{$countryName}\"";
            case 'trade':
                return "trade \"{$countryName}\"";
            case 'shipping':
                return "shipping \"{$countryName}\"";
            case 'ekonomi':
                return "economy \"{$countryName}\"";
            default:
                return "news \"{$countryName}\"";
        }
    }

    private function resolveOriginalUrl($googleUrl)
    {
        if (str_contains($googleUrl, 'news.google.com')) {
            // Coba ikuti HTTP redirect untuk mendapatkan URL artikel asli
            try {
                $context = stream_context_create([
                    'http' => [
                        'method'          => 'GET',
                        'follow_location' => 0,   // Jangan ikuti redirect otomatis
                        'timeout'         => 3,
                        'user_agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    ],
                    'ssl' => [
                        'verify_peer'      => false,
                        'verify_peer_name' => false,
                    ]
                ]);

                $headers = @get_headers($googleUrl, 1, $context);

                if ($headers) {
                    // Ambil header Location dari redirect (bisa array jika multi-redirect)
                    $location = null;
                    if (isset($headers['Location'])) {
                        $loc = $headers['Location'];
                        $location = is_array($loc) ? end($loc) : $loc;
                    }

                    if ($location && str_starts_with($location, 'http') && !str_contains($location, 'news.google.com')) {
                        return $location;
                    }
                }
            } catch (\Exception $e) {
                // Lanjut ke fallback
            }

            // Jika gagal resolve, ambil judul dari query string jika ada, atau arahkan ke Google News
            $queryStr = parse_url($googleUrl, PHP_URL_QUERY);
            parse_str($queryStr ?? '', $queryParams);
            $searchTerm = $queryParams['q'] ?? 'supply chain news';
            return 'https://news.google.com/search?q=' . urlencode($searchTerm) . '&hl=en-US&gl=US&ceid=US:en';
        }

        return $googleUrl;
    }

    private function getKeywordsFromTitle($title, $category)
    {
        $stopWords = ['in', 'to', 'and', 'the', 'a', 'of', 'for', 'with', 'on', 'at', 'by', 'from', 'is', 'are', 'was', 'were', 'that', 'this', 'these', 'those', 'global', 'supply', 'chain'];
        $words = preg_split('/[^a-zA-Z]+/', strtolower($title));
        $filteredWords = [];
        foreach ($words as $word) {
            if (strlen($word) > 3 && !in_array($word, $stopWords)) {
                $filteredWords[] = $word;
            }
        }
        
        $keywords = array_slice($filteredWords, 0, 2);
        
        $flickrCategory = 'cargo';
        if ($category === 'logistik') {
            $flickrCategory = 'logistics,warehouse';
        } elseif ($category === 'trade') {
            $flickrCategory = 'trade,business';
        } elseif ($category === 'shipping') {
            $flickrCategory = 'shipping,port';
        } elseif ($category === 'ekonomi') {
            $flickrCategory = 'economy,finance';
        }

        if (empty($keywords)) {
            return $flickrCategory;
        }
        
        return $flickrCategory . ',' . implode(',', $keywords);
    }

    private function generateMockNews($category, $countryName, $positiveWords, $negativeWords, &$posCount, &$negCount)
    {
        $mockArticles = [];
        
        $images = [
            'logistik' => [
                'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=500&auto=format&fit=crop&q=60',
                'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=500&auto=format&fit=crop&q=60',
                'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=500&auto=format&fit=crop&q=60',
            ],
            'trade' => [
                'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=500&auto=format&fit=crop&q=60',
                'https://images.unsplash.com/photo-1521898284481-a5ec348cb555?w=500&auto=format&fit=crop&q=60',
                'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=500&auto=format&fit=crop&q=60',
            ],
            'shipping' => [
                'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=500&auto=format&fit=crop&q=60',
                'https://images.unsplash.com/photo-1509316975850-ff9c5edd0cd9?w=500&auto=format&fit=crop&q=60',
                'https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?w=500&auto=format&fit=crop&q=60',
            ],
            'ekonomi' => [
                'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=500&auto=format&fit=crop&q=60',
                'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=500&auto=format&fit=crop&q=60',
                'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=500&auto=format&fit=crop&q=60',
            ],
        ];

        $templates = [
            'logistik' => [
                [
                    'title' => "Modern Warehouse Expansion in {$countryName} Expected to Boost Logistics Capacity",
                    'desc' => "New warehouse facilities are being built to resolve regional delays and increase general distribution speed across {$countryName}.",
                    'source' => 'Logistics Portal',
                    'sentiment' => 'Positive'
                ],
                [
                    'title' => "Logistics Bottlenecks at {$countryName} Borders Raise Supply Chain Alarms",
                    'desc' => "Delays in cargo verification have created a backlog of trucks, causing raw material shortages for manufacturing.",
                    'source' => 'Global Supply Chain Insights',
                    'sentiment' => 'Negative'
                ],
                [
                    'title' => "{$countryName} Launches Green Supply Chain Corridor to Support Low-Emission Transport",
                    'desc' => "A new joint venture aims to implement electric truck fleets for domestic distribution networks in {$countryName}.",
                    'source' => 'Sustainable Shipping',
                    'sentiment' => 'Positive'
                ],
                [
                    'title' => "Logistics Costs in {$countryName} Increase by 12% Amid Truck Driver Shortages",
                    'desc' => "Transport companies warn of rising container rates as domestic supply chain companies struggle to find qualified labor.",
                    'source' => 'Freight Waves',
                    'sentiment' => 'Negative'
                ],
                [
                    'title' => "{$countryName} to Adopt AI-Driven Logistics Management for Key Industrial Zones",
                    'desc' => "A pilot program will integrate predictive logistics software to optimize warehouse layouts and delivery route planning.",
                    'source' => 'Tech Logistics News',
                    'sentiment' => 'Positive'
                ]
            ],
            'trade' => [
                [
                    'title' => "{$countryName} Signs Bilateral Trade Agreement to Secure Supply of Critical Raw Materials",
                    'desc' => "The new trade deal reduces tariffs on mineral imports, securing key supply chains for electronics manufacturers.",
                    'source' => 'Trade Policy Review',
                    'sentiment' => 'Positive'
                ],
                [
                    'title' => "Tariff Disagreement Threatens {$countryName}'s Agricultural Export Supply Chain",
                    'desc' => "Sudden regulatory changes on import quotas could disrupt export contracts for local farmers, prompting warnings from supply chain experts.",
                    'source' => 'Global Trade Daily',
                    'sentiment' => 'Negative'
                ],
                [
                    'title' => "{$countryName} Manufacturing Sector Recovers as Global Trade Restrictions Ease",
                    'desc' => "Factory orders are growing rapidly, driven by strong export demands from European and Asian supply chain partners.",
                    'source' => 'Industry Update',
                    'sentiment' => 'Positive'
                ],
                [
                    'title' => "Strict Border Controls in {$countryName} Create Import Clearances Backlog",
                    'desc' => "New security checks have slowed customs clearance procedures, forcing importers to pay high storage costs.",
                    'source' => 'Customs Journal',
                    'sentiment' => 'Negative'
                ],
                [
                    'title' => "{$countryName} Digitalizes Export Customs Forms to Speed Up Cargo Operations",
                    'desc' => "The electronic document initiative is expected to shave off 48 hours from standard trade supply chain clearance times.",
                    'source' => 'E-Commerce Trade',
                    'sentiment' => 'Positive'
                ]
            ],
            'shipping' => [
                [
                    'title' => "Shipping Congestion Eases at Major Sea Terminals in {$countryName}",
                    'desc' => "Average vessel waiting times have dropped significantly due to new automated gantry cranes and extra shifts.",
                    'source' => 'Maritime Executive',
                    'sentiment' => 'Positive'
                ],
                [
                    'title' => "Severe Port Congestion in {$countryName} Forces Vessels to Divert Routes",
                    'desc' => "Shipping lines are bypassing main terminals, leading to secondary distribution delays and high freight costs.",
                    'source' => 'Container News',
                    'sentiment' => 'Negative'
                ],
                [
                    'title' => "{$countryName} Port Authority Invests $50M in Deepwater Dock Expansion",
                    'desc' => "The port upgrade will allow massive container vessels to dock, solidifying {$countryName}'s role as a regional supply chain hub.",
                    'source' => 'Port Development',
                    'sentiment' => 'Positive'
                ],
                [
                    'title' => "Vessel Delays Near {$countryName}'s Coast Raise Freight Delay Concerns",
                    'desc' => "Bad weather conditions have halted maritime operations, stalling fuel and raw material imports for critical industries.",
                    'source' => 'Sea Trade Maritime',
                    'sentiment' => 'Negative'
                ],
                [
                    'title' => "Global Carrier Alliance Increases Shipping Services to {$countryName}",
                    'desc' => "Two new weekly container lines will connect local ports directly to US West Coast supply chain networks.",
                    'source' => 'Shipping Gazette',
                    'sentiment' => 'Positive'
                ]
            ],
            'ekonomi' => [
                [
                    'title' => "{$countryName} Economic Growth Forecast Remains Resilient Despite Global Supply Chain Risks",
                    'desc' => "Central bank report suggests domestic demand and commodity exports will shield the economy from overseas freight spikes.",
                    'source' => 'Financial Times',
                    'sentiment' => 'Positive'
                ],
                [
                    'title' => "Rising Supply Chain Costs Drive Up Inflation in {$countryName}",
                    'desc' => "Retailers warn that expensive import freight rates are forcing them to pass higher costs onto local consumers, raising inflation.",
                    'source' => 'Economic Monitor',
                    'sentiment' => 'Negative'
                ],
                [
                    'title' => "{$countryName} Consumer Confidence Rises as Local Supply Chains Stabilize",
                    'desc' => "Improved availability of automotive and electronic parts has spurred retail sales and overall market activity.",
                    'source' => 'Business Journal',
                    'sentiment' => 'Positive'
                ],
                [
                    'title' => "Fuel Price Spikes Threaten Transport Profitability and Economic Output in {$countryName}",
                    'desc' => "Logistics groups call for government fuel subsidies as high diesel prices strain the economy's distribution networks.",
                    'source' => 'Macroeconomics Quarterly',
                    'sentiment' => 'Negative'
                ],
                [
                    'title' => "Corporate Investment in {$countryName} Surges as Supply Chains Redesign Locally",
                    'desc' => "Many manufacturers are choosing local supply chain sourcing over distant imports, boosting the domestic economy.",
                    'source' => 'Investment Digest',
                    'sentiment' => 'Positive'
                ]
            ]
        ];

        $categoryTemplates = $templates[$category] ?? $templates['logistik'];

        foreach ($categoryTemplates as $index => $t) {
            $timeOffset = $index * 3;
            $pubDate = date('d M Y, H:i', strtotime("-{$timeOffset} hours"));

            if ($t['sentiment'] === 'Positive') {
                $posCount += 3;
            } elseif ($t['sentiment'] === 'Negative') {
                $negCount += 3;
            }

            // Arahkan ke Google News Search untuk judul berita mock agar selalu dapat dibaca
            $searchLink = 'https://news.google.com/search?q=' . urlencode($t['title']) . '&hl=en-US&gl=US&ceid=US:en';
            
            // Ambil gambar secara dinamis dari kata kunci judul berita mock
            $keywords = $this->getKeywordsFromTitle($t['title'], $category);
            $imgUrl = "https://loremflickr.com/500/350/" . urlencode($keywords) . "?random=" . ($index + 1);

            $mockArticles[] = [
                'judul'        => $t['title'],
                'deskripsi'    => $t['desc'],
                'link'         => $searchLink,
                'tanggal'      => $pubDate,
                'source'       => $t['source'],
                'sentiment'    => $t['sentiment'],
                'image'        => $imgUrl,
                'full_content' => ''
            ];
        }

        return $mockArticles;
    }

    public function fetchImage(Request $request)
    {
        $url = $request->query('url');
        if (empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return response()->json(['image' => null]);
        }

        // Jangan scraper google search (mock news link)
        if (str_contains($url, 'google.com/search') || $url === '#') {
            return response()->json(['image' => null]);
        }

        try {
            // Request HTTP ke situs web asli dengan timeout 2.5 detik (menolak verifikasi SSL & mengikuti redirect)
            $response = Http::withoutVerifying()->timeout(2.5)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get($url);

            if ($response->successful()) {
                $html = $response->body();
                
                // Parsing meta tag og:image (Open Graph standard)
                if (preg_match('/<meta[^>]*property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                    return response()->json(['image' => html_entity_decode($matches[1])]);
                }
                
                // Parsing meta tag twitter:image (Twitter standard)
                if (preg_match('/<meta[^>]*name=["\']twitter:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                    return response()->json(['image' => html_entity_decode($matches[1])]);
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika timeout atau website tersebut memblokir bot
        }

        return response()->json(['image' => null]);
    }
}