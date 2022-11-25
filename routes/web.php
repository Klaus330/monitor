<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteConfigurationController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteRouteController;
use App\Jobs\CrawlSite;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sites 
    Route::post('site/{user}/create', [SiteController::class, 'create'])->name('site.create');
    Route::get('site/{site}/all-routes', [SiteRouteController::class, 'getAllRoutes'])->name('site.routes.all');
    Route::get('site/{site}/preconfigure', [SiteController::class, 'preconfigure'])->name('site.settings.preconfigure')->middleware('isDraft:site');
    Route::get('site/{site}/configuration', [SiteConfigurationController::class, 'index'])->name('site.configuration');
    Route::patch('site/{site}/configuration', [SiteConfigurationController::class, 'update'])->name('site.configuration.update');
    Route::delete('site/delete', [SiteController::class, 'destroy'])->name('site.delete');


    Route::get('site/{site}/broken-routes', [SiteRouteController::class, 'brokenRoutes'])->name('site.broken.routes');
    Route::get('site/{site}/broken-routes/dowload-csv', [SiteRouteController::class, 'downloadCsvReport'])->name('site.download.broken_routes.report');
    Route::get('site/{site}', [SiteController::class, 'show'])->name('site.show');
    Route::get('site/{site}/route/{route}', [SiteRouteController::class, 'show'])->name('site.route.show');
    Route::get('/test', [SiteController::class, 'test']);
});
