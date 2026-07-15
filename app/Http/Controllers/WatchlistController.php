<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    /**
     * Menampilkan daftar negara yang dipantau (watchlist).
     */
    public function index()
    {
        $watchlist = Country::where('is_favorite', true)
            ->orderBy('country_name')
            ->get();

        return view('watchlist.index', compact('watchlist'));
    }

    /**
     * Meng-toggle status favorit/watchlist suatu negara.
     */
    public function toggle(Country $country)
    {
        $country->update([
            'is_favorite' => !$country->is_favorite
        ]);

        return response()->json([
            'success' => true,
            'is_favorite' => $country->is_favorite,
            'country_name' => $country->country_name
        ]);
    }
}