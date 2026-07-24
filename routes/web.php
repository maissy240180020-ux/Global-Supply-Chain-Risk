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
use App\Http\Controllers\NewsController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\WeatherController;
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

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.proses')->middleware('guest');

Route::get('/currency', [NilaiTukarController::class, 'index'])->name('currency.index');

Route::get('/berita', [NewsController::class, 'index'])->name('berita.index');

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

Route::get('/visualisasi/live-data', [VisualizationController::class, 'liveData'])
    ->name('visualisasi.live-data')
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| Pemantauan Cuaca
|--------------------------------------------------------------------------
*/

Route::get('/cuaca', [WeatherController::class, 'index'])
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

Route::get('/berita', [NewsController::class, 'index'])
    ->name('berita.index');

Route::get('/berita/fetch-image', [BeritaController::class, 'fetchImage'])
    ->name('berita.fetch-image');

Route::get('/news', [NewsController::class, 'index'])
    ->name('news.index');

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
    ->name('watchlist.index')
    ->middleware('auth');

Route::post('/watchlist/toggle/{country}', [WatchlistController::class, 'toggle'])
    ->name('watchlist.toggle')
    ->middleware('auth');

Route::get('/watchlist/live-data', [WatchlistController::class, 'liveData'])
    ->name('watchlist.live-data')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Admin - Hanya bisa diakses oleh role 'admin'
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Admin
    Route::get('/', [AdminController::class, 'index'])->name('index');

    // Kelola User
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Kelola Dataset Pelabuhan
    Route::get('/ports', [AdminController::class, 'ports'])->name('ports');
    Route::get('/ports/create', [AdminController::class, 'createPort'])->name('ports.create');
    Route::post('/ports', [AdminController::class, 'storePort'])->name('ports.store');
    Route::get('/ports/{id}/edit', [AdminController::class, 'editPort'])->name('ports.edit');
    Route::put('/ports/{id}', [AdminController::class, 'updatePort'])->name('ports.update');
    Route::delete('/ports/{id}', [AdminController::class, 'destroyPort'])->name('ports.destroy');

    // Kelola Artikel Analisis
    Route::get('/articles', [AdminController::class, 'articles'])->name('articles');
    Route::get('/articles/create', [AdminController::class, 'createArticle'])->name('articles.create');
    Route::post('/articles', [AdminController::class, 'storeArticle'])->name('articles.store');
    Route::get('/articles/{id}/edit', [AdminController::class, 'editArticle'])->name('articles.edit');
    Route::put('/articles/{id}', [AdminController::class, 'updateArticle'])->name('articles.update');
    Route::delete('/articles/{id}', [AdminController::class, 'destroyArticle'])->name('articles.destroy');

    // Dashboard Integrasi & REST API
    Route::get('/api-monitoring', [ApiCountryController::class, 'index'])->name('api.index');
    Route::get('/api-monitoring/ping', [ApiCountryController::class, 'ping'])->name('api.ping');

});

/*
|--------------------------------------------------------------------------
| REST API Mahasiswa (Fitur PDF Page 9)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/countries', [\App\Http\Controllers\WeatherController::class, 'index'])->name('api.countries');
    Route::get('/risk', [\App\Http\Controllers\ReportController::class, 'index'])->name('api.risk');
    Route::get('/ports', [\App\Http\Controllers\PortController::class, 'index'])->name('api.ports');
    Route::get('/news', [\App\Http\Controllers\NewsController::class, 'apiIndex'])->name('api.news');
    Route::get('/currency', [\App\Http\Controllers\CurrencyController::class, 'index'])->name('api.currency');
});