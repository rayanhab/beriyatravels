<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DepartureController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('/destinations', [DestinationController::class, 'index']);
    Route::get('/offers', [OfferController::class, 'index']);
    Route::get('/departures', [DepartureController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');
});
