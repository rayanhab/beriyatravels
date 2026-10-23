<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $sitePath = public_path('site.html');

    if (is_file($sitePath)) {
        return response()->file($sitePath);
    }

    return response()->json([
        'name' => 'Beriya Travels API',
        'status' => 'ok',
        'api' => '/api',
    ]);
});
