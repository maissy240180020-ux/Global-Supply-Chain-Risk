<?php

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

Route::get('/countries/create', [CountryController::class, 'create'])
    ->name('countries.create');

Route::post('/countries', [CountryController::class, 'store'])
    ->name('countries.store');

Route::get('/countries/{country}', [CountryController::class, 'show'])
    ->name('countries.show');

Route::get('/countries/{country}/edit', [CountryController::class, 'edit'])
    ->name('countries.edit');

Route::put('/countries/{country}', [CountryController::class, 'update'])
    ->name('countries.update');

Route::delete('/countries/{country}', [CountryController::class, 'destroy'])
    ->name('countries.destroy');

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
    ->name('compare.index');

Route::post('/compare', [CompareController::class, 'compare'])
    ->name('compare.compare');

/*
|--------------------------------------------------------------------------
| Visualisasi
|--------------------------------------------------------------------------
*/

Route::get('/visualisasi', [VisualizationController::class, 'index'])
    ->name('visualisasi.index');

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
    ->name('pelabuhan.index');

/*
|--------------------------------------------------------------------------
| Watchlist
|--------------------------------------------------------------------------
*/

Route::get('/watchlist', [WatchlistController::class, 'index'])
    ->name('watchlist.index');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::get('/admin', [AdminController::class, 'index'])
    ->name('admin.index');