<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PractitionerAppointmentController;
use App\Http\Controllers\AvailableSlotsController;
use App\Http\Controllers\PractitionerAvailableSlotsController;
use App\Http\Controllers\PractitionerVacationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Routes for website users
Route::post('/schedule', [AppointmentController::class, 'store'])->name('schedule.store');
Route::get('/diagnose', [AvailableSlotsController::class, 'index90'])->name('diagnose.index90');
Route::get('/treatment', [AvailableSlotsController::class, 'index60'])->name('treatment.index60');

// Routes for practitioners
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/practitioner/available-slots/index', [PractitionerAvailableSlotsController::class, 'index'])->name('practitioner.available-slots.index');
    Route::get('/practitioner/available-slots/index60', [PractitionerAvailableSlotsController::class, 'index60'])->name('practitioner.available-slots.index60');
    Route::get('/practitioner/available-slots/index90', [PractitionerAvailableSlotsController::class, 'index90'])->name('practitioner.available-slots.index90');
    //Route::get('/practitioner/available-slots/create', [PractitionerAvailableSlotsController::class, 'create'])->name('practitioner.available-slots.create');
    Route::post('/practitioner/available-slots/store', [PractitionerAvailableSlotsController::class, 'store'])->name('practitioner.available-slots.store');
    Route::post('/practitioner/available-slots/delete', [PractitionerAvailableSlotsController::class, 'destroy'])->name('practitioner.available-slots.destroy');
    Route::post('/practitioner/available-slots/seed', [PractitionerAvailableSlotsController::class, 'seed'])->name('practitioner.available-slots.seed');
    Route::get('/practitioner/appointments/index', [PractitionerAppointmentController::class, 'index'])->name('practitioner.appointments.index');
    //Route::get('/practitioner/appointments/create', [PractitionerAppointmentController::class, 'create'])->name('practitioner.appointments.create');
    Route::post('/practitioner/appointments/store', [PractitionerAppointmentController::class, 'store'])->name('practitioner.appointments.store');
    Route::post('/practitioner/appointments/delete', [PractitionerAppointmentController::class, 'destroy'])->name('practitioner.appointments.destroy');
    Route::post('/practitioner/appointments/update/{id}', [PractitionerAppointmentController::class, 'update'])->name('practitioner.appointments.update');
    Route::get('/practitioner/appointments/{id}', [PractitionerAppointmentController::class, 'show'])->name('practitioner.appointments.show');
    Route::get('/practitioner/vacations/index', [PractitionerVacationController::class, 'index'])->name('practitioners.vacations.index');
    Route::post('/practitioner/vacations/store', [PractitionerVacationController::class, 'store'])->name('practitioners.vacations.store');
    Route::post('/practitioner/vacations/delete', [PractitionerVacationController::class, 'destroy'])->name('practitioners.vacations.destroy');
    Route::put('/practitioner/vacations/update', [PractitionerVacationController::class, 'update'])->name('practitioners.vacations.update');
    Route::get('/admin/users-practitioners/index', [AdminController::class, 'index'])->name('admin.users-practitioners.index');
    Route::post('/admin/practitioners/store', [AdminController::class, 'storePractitioner'])->name('admin.practitioners.store');
    //Route::get('/practitioner/appointments/{id}/edit', [PractitionerAppointmentController::class, 'edit'])->name('practitioner.appointments.edit');
    //Route::put('/practitioner/appointments/{id}', [PractitionerAppointmentController::class, 'update'])->name('practitioner.appointments.update');
    //Route::delete('/practitioner/appointments/{id}', [PractitionerAppointmentController::class, 'destroy'])->name('practitioner.appointments.destroy');
    //Route::get('/practitioner/appointments/search', [PractitionerAppointmentController::class, 'search'])->name('practitioner.appointments.search');
    //Route::get('/practitioner/appointments/filter/date', [PractitionerAppointmentController::class, 'filterByDate'])->name('practitioner.appointments.filter.date');
    //Route::get('/practitioner/appointments/filter/practitioner', [PractitionerAppointmentController::class, 'filterByPractitioner'])->name('practitioner.appointments.filter.practitioner');
    //Route::get('/practitioner/appointments/filter/status', [PractitionerAppointmentController::class, 'filterByStatus'])->name('practitioner.appointments.filter.status');
});