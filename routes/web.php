<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [WeatherController::class, 'showWeather']);

Route::get('/favorites/add', [WeatherController::class, 'addFavorite'])->name('favorites.add');

Route::get('/favorites', [WeatherController::class, 'showFavorites'])->name('favorites');

Route::get('/welcome', [WeatherController::class, 'showWeather'])->name('welcome');

Route::get('/getCurrentCity', [WeatherController::class, 'getCurrentCity'])->name('getCurrentCity');
