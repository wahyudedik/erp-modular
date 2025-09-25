<?php

use Illuminate\Support\Facades\Route;

// Serve the Vue.js SPA for all routes except API
Route::get('/{any}', function () {
    return view('spa');
})->where('any', '.*');

// Fallback route for the root
Route::get('/', function () {
    return view('spa');
});
