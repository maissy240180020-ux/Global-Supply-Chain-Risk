<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class NewsController extends Controller
{
    // Kamus Lexicon sederhana untuk Sentiment Analysis (Bahasa Inggris)
    private $positiveWords = [
        'growth', 'grow', 'success', 'successful', 'profit', 'profitable', 'gain', 'gains', 'up', 'rise', 'rising',
        'improve', 'improving', 'improvement', 'boost', 'boosted', 'stable', 'stability', 'strong', 'strength',
        'recover', 'recovery', 'optimistic', 'optimism', 'positive', 'good', 'great', 'excellent', 'expand', 'expansion',
        'invest', 'investment', 'boom', 'booming', 'surpass', 'record', 'win', 'winning', 'benefit', 'beneficial',
        'innovate', 'innovation', 'secure', 'safe', 'support', 'supported', 'thrive', 'thriving', 'resolve', 'resolved'
    ];

    private $negativeWords = [
        'decline', 'declining', 'drop', 'dropping', 'fall', 'falling', 'down', 'loss', 'losses', 'deficit',
        'crisis', 'crash', 'risk', 'risks', 'risky', 'threat', 'threats', 'threaten', 'disrupt', 'disruption',
        'delay', 'delays', 'delayed', 'shortage', 'shortages', 'inflation', 'recession', 'debt', 'bankrupt',
        'bankruptcy', 'fail', 'failing', 'failure', 'weak', 'weakness', 'worse', 'worst', 'pessimistic', 'negative',
        'bad', 'terrible', 'conflict', 'war', 'strike', 'strikes', 'protest', 'halt', 'halted', 'ban', 'banned',
        'struggle', 'struggling', 'tension', 'tensions', 'plunge', 'plunging', 'cut', 'cuts'
    ];

    public function index(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $country = $request->input('country', 'world');
        $category = $request->input('category', '');

        // Bangun Query Pencarian
        $baseQuery = "supply chain OR economy OR global trade OR logistics OR shipping";
        $finalQuery = "";

        if (!empty($category)) {
            $finalQuery = $category;
        } else {
            $finalQuery = $baseQuery;
        }

        if (!empty($keyword)) {
            $finalQuery = "(" . $finalQuery . ") AND (" . $keyword . ")";
        }

        if ($country !== 'world') {
            $finalQuery .= " AND " . $country;
        }

        $apiKey = env('GNEWS_API_KEY');
        $articles = [];
        $stats = [
            'positive' => 0,
            'neutral'  => 0,
            'negative' => 0
        ];
        $error = null;

        try {
            // GNews API request (sortby=publishedAt untuk berita terbaru)
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->get('https://gnews.io/api/v4/search', [
                    'q'      => $finalQuery,
                    'lang'   => 'en',
                    'max'    => 12,
                    'sortby' => 'publishedAt',
                    'apikey' => $apiKey,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['articles'])) {
                    $articles = $data['articles'];
                    
                    // Proses Lexicon Sentiment Analysis
                    foreach ($articles as &$article) {
                        $sentimentResult = $this->analyzeSentiment($article['title'] . " " . $article['description']);
                        $article['sentiment'] = $sentimentResult['label'];
                        $article['sentiment_score'] = $sentimentResult['score'];
                        
                        // Hitung Statistik
                        if ($article['sentiment'] === 'Positif') $stats['positive']++;
                        elseif ($article['sentiment'] === 'Negatif') $stats['negative']++;
                        else $stats['neutral']++;

                        // Format waktu
                        if (isset($article['publishedAt'])) {
                            $article['published_formatted'] = Carbon::parse($article['publishedAt'])->diffForHumans();
                        }
                    }
                }
            } else {
                $error = "Gagal mengambil data dari API GNews. Kode Status: " . $response->status();
                if ($response->status() == 403) {
                    $error = "Batas maksimal request API harian (Free Plan) mungkin telah tercapai, atau API Key tidak valid.";
                }
            }
        } catch (\Exception $e) {
            $error = "Terjadi kesalahan koneksi saat mengakses GNews API: " . $e->getMessage();
        }

        // Jika request dari AJAX (misal tekan tombol Refresh atau Filter)
        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'articles' => $articles,
                'stats'    => $stats,
                'error'    => $error
            ]);
        }

        // Render view HTML
        return view('berita.index', compact('articles', 'stats', 'keyword', 'country', 'category', 'error'));
    }

    /**
     * Lexicon Based Sentiment Analysis
     */
    private function analyzeSentiment($text)
    {
        if (empty($text)) return ['label' => 'Netral', 'score' => 0];

        $text = strtolower(preg_replace('/[^a-zA-Z\s]/', '', $text)); // Hapus tanda baca
        $words = explode(' ', $text);

        $posCount = 0;
        $negCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords)) {
                $posCount++;
            } elseif (in_array($word, $this->negativeWords)) {
                $negCount++;
            }
        }

        $score = $posCount - $negCount;

        if ($score > 0) {
            return ['label' => 'Positif', 'score' => $score];
        } elseif ($score < 0) {
            return ['label' => 'Negatif', 'score' => $score];
        } else {
            return ['label' => 'Netral', 'score' => 0];
        }
    }
}
