<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\MarkerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [WebController::class, 'index']);

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/markers', [HomeController::class, 'marker'])->name('markers');

// API untuk marker
Route::get('/get-markers', [MarkerController::class, 'getMarkers']);
Route::post('/save-marker', [MarkerController::class, 'store']);