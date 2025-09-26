<?php

use Illuminate\Support\Facades\Route;

// Serve Vue.js SPA for all non-API routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
