<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PractitionerAppointmentController;
use App\Http\Controllers\AvailableSlotsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes for website users
Route::post('/schedule', [AppointmentController::class, 'store'])->name('schedule.store');
Route::get('/diagnose', [AvailableSlotsController::class, 'index90'])->name('diagnose.index90');
Route::get('/treatment', [AvailableSlotsController::class, 'index60'])->name('treatment.index60');

// Routes for practitioners
Route::get('/practitioner/appointments', [PractitionerAppointmentController::class, 'index'])->name('practitioner.appointments.index');
//Route::get('/practitioner/appointments/create', [PractitionerAppointmentController::class, 'create'])->name('practitioner.appointments.create');
Route::post('/practitioner/appointments', [PractitionerAppointmentController::class, 'store'])->name('practitioner.appointments.store');
//Route::get('/practitioner/appointments/{id}', [PractitionerAppointmentController::class, 'show'])->name('practitioner.appointments.show');
//Route::get('/practitioner/appointments/{id}/edit', [PractitionerAppointmentController::class, 'edit'])->name('practitioner.appointments.edit');
//Route::put('/practitioner/appointments/{id}', [PractitionerAppointmentController::class, 'update'])->name('practitioner.appointments.update');
//Route::delete('/practitioner/appointments/{id}', [PractitionerAppointmentController::class, 'destroy'])->name('practitioner.appointments.destroy');
//Route::get('/practitioner/appointments/search', [PractitionerAppointmentController::class, 'search'])->name('practitioner.appointments.search');
//Route::get('/practitioner/appointments/filter/date', [PractitionerAppointmentController::class, 'filterByDate'])->name('practitioner.appointments.filter.date');
//Route::get('/practitioner/appointments/filter/practitioner', [PractitionerAppointmentController::class, 'filterByPractitioner'])->name('practitioner.appointments.filter.practitioner');