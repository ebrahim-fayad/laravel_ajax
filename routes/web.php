<?php

use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::controller(CountryController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/get_country', 'get_country')->name('get_country');
    Route::post('/countries', 'store')->name('store');
    Route::put('/update', 'update')->name('update');
    Route::get('/countries', 'getAllCountries')->name('countries');
    Route::delete('/deleteCountry', 'destroy')->name('delete');
    Route::delete('/multiple_delete', 'multiple_delete')->name('multiple_delete');
});