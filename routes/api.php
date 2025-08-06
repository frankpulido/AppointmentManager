<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailableSlotsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes for website users
Route::post('/schedule', [AppointmentController::class, 'store'])->name('schedule.store');
Route::get('/diagnose', [AvailableSlotsController::class, 'index90'])->name('diagnose.index90');
Route::get('/treatment', [AvailableSlotsController::class, 'index60'])->name('treatment.index60');