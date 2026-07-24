<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil semua negara untuk dropdown selector
        $countries = Country::orderBy('country_name')->get();

        // 2. Tentukan negara terpilih (default Indonesia / negara pertama)
        $selectedCountryId = request('country_id');
        $selectedCountry = null;

        if ($selectedCountryId) {
            $selectedCountry = Country::find($selectedCountryId);
        }

        if (!$selectedCountry) {
            $selectedCountry = Country::where('country_code', 'ID')->first() ?? Country::first();
        }

        // Jika DB kosong sama sekali (tidak seharusnya terjadi setelah seed)
        if (!$selectedCountry) {
            return view('dashboard.index', [
                'totalCountries' => 0,
                'highRisk' => 0,
                'mediumRisk' => 0,
                'lowRisk' => 0,
                'averageRisk' => 0,
                'topRiskCountries' => collect(),
                'countries' => collect(),
                'selectedCountry' => null,
                'cuaca' => null,
                'kursRate' => 1.0,
                'berita' => [],
                'posCount' => 0,
                'negCount' => 0,
                'sentiment' => 'Neutral',
                'weatherRisk' => 0,
                'inflationRisk' => 0,
                'currencyRisk' => 0,
                'newsSentimentRisk' => 0,
                'calculatedRiskScore' => 0,
                'calculatedRiskLevel' => 'Low',
                'ports' => collect()
            ]);
        }

        // Sync data makroekonomi World Bank secara dinamis jika datanya masih nol
        if ($selectedCountry->gdp == 0 || $selectedCountry->inflation == 0) {
            $code = strtoupper($selectedCountry->country_code);
            $latestGdp = null;
            $latestInflation = null;

            try {
                // Tarik data GDP terbaru (1 tahun terakhir)
                $responseGdp = Http::withoutVerifying()->timeout(3.5)->get("http://api.worldbank.org/v2/country/" . strtolower($code) . "/indicator/NY.GDP.MKTP.CD", [
                    'format' => 'json',
                    'per_page' => 1
                ])->json();

                // Tarik data Laju Inflasi terbaru (1 tahun terakhir)
                $responseInflation = Http::withoutVerifying()->timeout(3.5)->get("http://api.worldbank.org/v2/country/" . strtolower($code) . "/indicator/FP.CPI.TOTL.ZG", [
                    'format' => 'json',
                    'per_page' => 1
                ])->json();

                if (isset($responseGdp[1][0]['value']) && $responseGdp[1][0]['value'] !== null) {
                    $latestGdp = $responseGdp[1][0]['value'];
                }
                if (isset($responseInflation[1][0]['value']) && $responseInflation[1][0]['value'] !== null) {
                    $latestInflation = $responseInflation[1][0]['value'];
                }
            } catch (\Exception $e) {
                // Abaikan error koneksi untuk menggunakan data fallback
            }

            // Peta data cadangan (fallback) makroekonomi jika API offline / terputus
            $fallbackMapping = [
                'ID' => ['gdp' => 1319000000000, 'inflation' => 2.6],
                'US' => ['gdp' => 25460000000000, 'inflation' => 3.4],
                'CN' => ['gdp' => 17960000000000, 'inflation' => 2.0],
                'SG' => ['gdp' => 466800000000, 'inflation' => 4.1],
                'AU' => ['gdp' => 1675000000000, 'inflation' => 3.6],
                'JP' => ['gdp' => 4231000000000, 'inflation' => 2.5],
                'GB' => ['gdp' => 3079000000000, 'inflation' => 2.8],
                'DE' => ['gdp' => 4072000000000, 'inflation' => 2.1],
            ];

            if ($latestGdp === null) {
                $latestGdp = $fallbackMapping[$code]['gdp'] ?? (rand(50, 450) * 1000000000);
            }
            if ($latestInflation === null) {
                $latestInflation = $fallbackMapping[$code]['inflation'] ?? (rand(15, 45) / 10);
            }

            // Simpan ke database agar kunjungan berikutnya tidak perlu memanggil API lagi
            $selectedCountry->update([
                'gdp' => $latestGdp,
                'inflation' => $latestInflation
            ]);
            
            // Perbarui model instance saat ini agar view langsung menampilkan nilai terbaru
            $selectedCountry->gdp = $latestGdp;
            $selectedCountry->inflation = $latestInflation;
        }

        // ==========================
        // FITUR 3: API CUACA REALTIME (Open-Meteo API)
        // ==========================
        $cuaca = null;
        $weatherRisk = 10; // Default
        $weatherDesc = 'Cerah';
        $currentTemp = $selectedCountry->temperature ?? 28;

        try {
            $responseWeather = Http::timeout(5)
                ->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $selectedCountry->latitude ?? 0.0,
                    'longitude' => $selectedCountry->longitude ?? 0.0,
                    'current' => 'temperature_2m,wind_speed_10m,weather_code',
                    'timezone' => 'auto'
                ]);

            if ($responseWeather->successful()) {
                $weatherData = $responseWeather->json();
                if (isset($weatherData['current'])) {
                    $cuaca = $weatherData['current'];
                    $currentTemp = $cuaca['temperature_2m'];
                    $code = $cuaca['weather_code'] ?? 0;

                    // Terjemahkan kode cuaca & tentukan risiko cuaca
                    if (in_array($code, [95, 96, 99])) {
                        $weatherDesc = 'Badai Guntur';
                        $weatherRisk = 25;
                    } elseif (in_array($code, [80, 81, 82])) {
                        $weatherDesc = 'Hujan Deras';
                        $weatherRisk = 20;
                    } elseif (in_array($code, [71, 73, 75, 77, 85, 86])) {
                        $weatherDesc = 'Hujan Salju';
                        $weatherRisk = 20;
                    } elseif (in_array($code, [61, 63, 65, 66, 67])) {
                        $weatherDesc = 'Hujan';
                        $weatherRisk = 15;
                    } elseif (in_array($code, [51, 53, 55, 56, 57])) {
                        $weatherDesc = 'Gerimis';
                        $weatherRisk = 12;
                    } elseif (in_array($code, [45, 48])) {
                        $weatherDesc = 'Kabut';
                        $weatherRisk = 10;
                    } elseif (in_array($code, [1, 2, 3])) {
                        $weatherDesc = 'Sebagian Berawan';
                        $weatherRisk = 7;
                    } else {
                        $weatherDesc = 'Cerah';
                        $weatherRisk = 5;
                    }
                }
            }
        } catch (\Exception $e) {
            // Fallback jika API gagal
            $weatherDesc = $selectedCountry->weather ?? 'Cerah';
            $weatherRisk = ($weatherDesc === 'Cerah') ? 5 : (($weatherDesc === 'Berawan') ? 10 : 20);
        }

        // ==========================
        // FITUR 4: API NILAI TUKAR REALTIME (Frankfurter API)
        // ==========================
        $kursRate = 1.0;
        $currencyRisk = 5; // Default

        // Tentukan risiko dasar mata uang
        $stableCurrencies = ['USD', 'EUR', 'JPY', 'GBP', 'AUD', 'SGD'];
        $moderateCurrencies = ['IDR', 'CNY', 'INR', 'BRL'];
        $volatileCurrencies = ['RUB'];

        $curr = $selectedCountry->currency;
        if (in_array($curr, $stableCurrencies)) {
            $currencyRisk = 5;
        } elseif (in_array($curr, $moderateCurrencies)) {
            $currencyRisk = 15;
        } elseif (in_array($curr, $volatileCurrencies)) {
            $currencyRisk = 25;
        } else {
            $currencyRisk = 20;
        }

        if ($curr !== 'USD') {
            try {
                $responseCurrency = Http::timeout(5)
                    ->get("https://api.frankfurter.app/latest", [
                        'from' => 'USD',
                        'to' => $curr
                    ]);

                if ($responseCurrency->successful()) {
                    $currData = $responseCurrency->json();
                    $kursRate = $currData['rates'][$curr] ?? 1.0;
                }
            } catch (\Exception $e) {
                // Fallback menggunakan dummy rate yang realistis jika API offline
                if ($curr === 'IDR') $kursRate = 16250;
                elseif ($curr === 'CNY') $kursRate = 7.25;
                elseif ($curr === 'EUR') $kursRate = 0.92;
                elseif ($curr === 'JPY') $kursRate = 158.0;
                elseif ($curr === 'AUD') $kursRate = 1.50;
                elseif ($curr === 'INR') $kursRate = 83.50;
                elseif ($curr === 'BRL') $kursRate = 5.40;
                elseif ($curr === 'RUB') $kursRate = 87.0;
                elseif ($curr === 'SGD') $kursRate = 1.35;
            }
        }

        // ==========================
        // FITUR 5 & AI: NEWS INTELLIGENCE & SENTIMENT ANALYSIS
        // ==========================
        $berita = [];
        $posCount = 0;
        $negCount = 0;
        $sentiment = 'Neutral';
        $newsSentimentRisk = 12; // Default (Neutral)

        try {
            $queryNews = urlencode("supply chain " . $selectedCountry->country_name);
            $url = "https://news.google.com/rss/search?q={$queryNews}&hl=en-US&gl=US&ceid=US:en";
            $rss = @simplexml_load_file($url);

            if ($rss && isset($rss->channel->item)) {
                // Ambil kata positif & negatif dari DB Lexicon
                $positiveWords = DB::table('positive_words')->pluck('word')->toArray();
                $negativeWords = DB::table('negative_words')->pluck('word')->toArray();

                $count = 0;
                foreach ($rss->channel->item as $item) {
                    $title = (string)$item->title;
                    $link = (string)$item->link;
                    $pubDate = date('d M Y H:i', strtotime((string)$item->pubDate));

                    // Lakukan Sentiment Analysis Lexicon sederhana
                    $words = preg_split('/[^a-zA-Z]+/', strtolower($title));
                    $itemPos = 0;
                    $itemNeg = 0;

                    foreach ($words as $word) {
                        if (in_array($word, $positiveWords)) {
                            $itemPos++;
                            $posCount++;
                        }
                        if (in_array($word, $negativeWords)) {
                            $itemNeg++;
                            $negCount++;
                        }
                    }

                    $itemSentiment = 'Neutral';
                    if ($itemPos > $itemNeg) {
                        $itemSentiment = 'Positive';
                    } elseif ($itemNeg > $itemPos) {
                        $itemSentiment = 'Negative';
                    }

                    $berita[] = [
                        'judul' => $title,
                        'link' => $link,
                        'tanggal' => $pubDate,
                        'sentiment' => $itemSentiment
                    ];

                    $count++;
                    if ($count >= 4) break; // Ambil 4 berita teratas
                }

                // Tentukan sentimen keseluruhan
                if ($posCount > $negCount) {
                    $sentiment = 'Positive';
                    $newsSentimentRisk = 5;
                } elseif ($negCount > $posCount) {
                    $sentiment = 'Negative';
                    $newsSentimentRisk = 25;
                }
            }
        } catch (\Exception $e) {
            $berita = [];
        }

        // ==========================
        // FITUR 2: RISK SCORING ENGINE (Weather + Inflation + Exchange + News Sentiment)
        // ==========================
        // 1. Inflation Risk (0 - 25 points)
        $inf = $selectedCountry->inflation ?? 2.0;
        if ($inf > 10.0) {
            $inflationRisk = 25;
        } elseif ($inf > 5.0) {
            $inflationRisk = 18;
        } elseif ($inf > 2.0) {
            $inflationRisk = 10;
        } else {
            $inflationRisk = 5;
        }

        // Hitung total skor risiko (max 100)
        $calculatedRiskScore = $weatherRisk + $inflationRisk + $currencyRisk + $newsSentimentRisk;

        // Tentukan tingkat risiko
        if ($calculatedRiskScore <= 35) {
            $calculatedRiskLevel = 'Low';
        } elseif ($calculatedRiskScore <= 60) {
            $calculatedRiskLevel = 'Medium';
        } else {
            $calculatedRiskLevel = 'High';
        }

        // Update ke database agar peta & visualisasi utama ter-update secara otomatis
        $selectedCountry->update([
            'risk_score' => $calculatedRiskScore,
            'risk_level' => $calculatedRiskLevel,
            'temperature' => $currentTemp,
            'weather' => $weatherDesc,
        ]);

        // ==========================
        // FITUR 6: PORT LOCATION (Ambil Pelabuhan dari Database)
        // ==========================
        $ports = DB::table('ports')
            ->where('country_code', $selectedCountry->country_code)
            ->get();

        // 3. Statistik Umum untuk Ringkasan Utama
        $allCountries = Country::all();
        $totalCountries = $allCountries->count();
        $highRisk = $allCountries->where('risk_level', 'High')->count();
        $mediumRisk = $allCountries->where('risk_level', 'Medium')->count();
        $lowRisk = $allCountries->where('risk_level', 'Low')->count();
        $averageRisk = round($allCountries->avg('risk_score'), 1);

        $topRiskCountries = Country::orderByDesc('risk_score')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'countries',
            'selectedCountry',
            'totalCountries',
            'highRisk',
            'mediumRisk',
            'lowRisk',
            'averageRisk',
            'topRiskCountries',
            'cuaca',
            'kursRate',
            'berita',
            'posCount',
            'negCount',
            'sentiment',
            'weatherRisk',
            'inflationRisk',
            'currencyRisk',
            'newsSentimentRisk',
            'calculatedRiskScore',
            'calculatedRiskLevel',
            'ports'
        ));
    }
}