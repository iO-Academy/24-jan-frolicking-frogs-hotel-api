<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(RoomController::class)->group(function () {
   Route::get('/rooms', 'all');
});

Route::controller(BookingController::class)->group(function() {
    Route::post('/bookings', 'create');
});
