<?php

use App\Http\Controllers\ApiCountryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\VisualizationController;
use App\Http\Controllers\CuacaController;
use App\Http\Controllers\NilaiTukarController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PelabuhanController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Halaman Awal
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'index'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.proses');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Data Negara
|--------------------------------------------------------------------------
*/
Route::get('/countries', [CountryController::class, 'index'])
    ->name('countries.index');

Route::get('/countries/{country}', [CountryController::class, 'show'])
    ->name('countries.show');

/*
|--------------------------------------------------------------------------
| Pemantauan Risiko
|--------------------------------------------------------------------------
*/

Route::get('/risk', [RiskController::class, 'index'])
    ->name('risk.index');

/*
|--------------------------------------------------------------------------
| Perbandingan Negara
|--------------------------------------------------------------------------
*/

Route::get('/compare', [CompareController::class, 'index'])
    ->name('compare.index')
    ->middleware('auth');

Route::post('/compare', [CompareController::class, 'compare'])
    ->name('compare.compare')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Visualisasi
|--------------------------------------------------------------------------
*/

Route::get('/visualisasi', [VisualizationController::class, 'index'])
    ->name('visualisasi.index')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Pemantauan Cuaca
|--------------------------------------------------------------------------
*/

Route::get('/cuaca', [CuacaController::class, 'index'])
    ->name('cuaca.index');

/*
|--------------------------------------------------------------------------
| Nilai Tukar
|--------------------------------------------------------------------------
*/

Route::get('/nilai-tukar', [NilaiTukarController::class, 'index'])
    ->name('nilai-tukar.index');

/*
|--------------------------------------------------------------------------
| Berita
|--------------------------------------------------------------------------
*/

Route::get('/berita', [BeritaController::class, 'index'])
    ->name('berita.index');

/*
|--------------------------------------------------------------------------
| Dashboard Pelabuhan
|--------------------------------------------------------------------------
*/

Route::get('/pelabuhan', [PelabuhanController::class, 'index'])
    ->name('pelabuhan.index')
    ->middleware('auth');

Route::get('/pelabuhan/search-global', [PelabuhanController::class, 'searchGlobal'])
    ->name('pelabuhan.search-global')
    ->middleware('auth');

Route::post('/pelabuhan/store-global', [PelabuhanController::class, 'storeGlobalPort'])
    ->name('pelabuhan.store-global')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Watchlist
|--------------------------------------------------------------------------
*/

Route::get('/watchlist', [WatchlistController::class, 'index'])
    ->name('watchlist.index');

Route::post('/watchlist/toggle/{country}', [WatchlistController::class, 'toggle'])
    ->name('watchlist.toggle');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::get('/admin', [AdminController::class, 'index'])
    ->name('admin.index');

Route::get('/countries-api', [ApiCountryController::class,'index'])
        ->name('countries.api');

/*
|--------------------------------------------------------------------------
| REST API Mahasiswa (Fitur PDF Page 9)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/countries', [\App\Http\Controllers\WeatherController::class, 'index'])->name('api.countries');
    Route::get('/risk', [\App\Http\Controllers\ReportController::class, 'index'])->name('api.risk');
    Route::get('/ports', [\App\Http\Controllers\PortController::class, 'index'])->name('api.ports');
    Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('api.news');
    Route::get('/currency', [\App\Http\Controllers\CurrencyController::class, 'index'])->name('api.currency');
});