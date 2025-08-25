<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PractitionerAppointmentController;
use App\Http\Controllers\AvailableSlotsController;
use App\Http\Controllers\PractitionerAvailableSlotsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes for website users
Route::post('/schedule', [AppointmentController::class, 'store'])->name('schedule.store');
Route::get('/diagnose', [AvailableSlotsController::class, 'index90'])->name('diagnose.index90');
Route::get('/treatment', [AvailableSlotsController::class, 'index60'])->name('treatment.index60');

// Routes for practitioners
Route::get('/practitioner/available-slots/index', [PractitionerAvailableSlotsController::class, 'index'])->name('practitioner.available-slots.index');
Route::get('/practitioner/available-slots/index60', [PractitionerAvailableSlotsController::class, 'index60'])->name('practitioner.available-slots.index60');
Route::get('/practitioner/available-slots/index90', [PractitionerAvailableSlotsController::class, 'index90'])->name('practitioner.available-slots.index90');
//Route::get('/practitioner/available-slots/create', [PractitionerAvailableSlotsController::class, 'create'])->name('practitioner.available-slots.create');
Route::post('/practitioner/available-slots/store', [PractitionerAvailableSlotsController::class, 'store'])->name('practitioner.available-slots.store');
Route::post('/practitioner/available-slots/delete', [PractitionerAvailableSlotsController::class, 'destroy'])->name('practitioner.available-slots.destroy');

Route::get('/practitioner/appointments/index', [PractitionerAppointmentController::class, 'index'])->name('practitioner.appointments.index');
//Route::get('/practitioner/appointments/create', [PractitionerAppointmentController::class, 'create'])->name('practitioner.appointments.create');
Route::post('/practitioner/appointments/store', [PractitionerAppointmentController::class, 'store'])->name('practitioner.appointments.store');
Route::post('/practitioner/appointments/delete', [PractitionerAppointmentController::class, 'destroy'])->name('practitioner.appointments.destroy');
//Route::get('/practitioner/appointments/{id}', [PractitionerAppointmentController::class, 'show'])->name('practitioner.appointments.show');
//Route::get('/practitioner/appointments/{id}/edit', [PractitionerAppointmentController::class, 'edit'])->name('practitioner.appointments.edit');
//Route::put('/practitioner/appointments/{id}', [PractitionerAppointmentController::class, 'update'])->name('practitioner.appointments.update');
//Route::delete('/practitioner/appointments/{id}', [PractitionerAppointmentController::class, 'destroy'])->name('practitioner.appointments.destroy');
//Route::get('/practitioner/appointments/search', [PractitionerAppointmentController::class, 'search'])->name('practitioner.appointments.search');
//Route::get('/practitioner/appointments/filter/date', [PractitionerAppointmentController::class, 'filterByDate'])->name('practitioner.appointments.filter.date');
//Route::get('/practitioner/appointments/filter/practitioner', [PractitionerAppointmentController::class, 'filterByPractitioner'])->name('practitioner.appointments.filter.practitioner');