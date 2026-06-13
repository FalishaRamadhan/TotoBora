<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\ImmunizationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\GrowthController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ReportController;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Children
    Route::get('/children',            [ChildController::class, 'index'])->name('children.index');
    Route::get('/children/register',   [ChildController::class, 'create'])->name('children.create');
    Route::post('/children',           [ChildController::class, 'store'])->name('children.store');
    Route::get('/children/{child}',    [ChildController::class, 'show'])->name('children.show');
    Route::get('/children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');
    Route::put('/children/{child}',    [ChildController::class, 'update'])->name('children.update');

    // Immunizations
    Route::get('/children/{child}/immunizations/create',
        [ImmunizationController::class, 'create'])->name('immunizations.create');
    Route::post('/children/{child}/immunizations',
        [ImmunizationController::class, 'store'])->name('immunizations.store');
    Route::get('/children/{child}/immunizations',
        [ImmunizationController::class, 'history'])->name('immunizations.history');

    // Appointments
    Route::get('/children/{child}/appointments/create',
        [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/children/{child}/appointments',
        [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/attend',
        [AppointmentController::class, 'attend'])->name('appointments.attend');
    Route::patch('/appointments/{appointment}/miss',
        [AppointmentController::class, 'miss'])->name('appointments.miss');

    // Growth
    Route::get('/children/{child}/growth/create',
        [GrowthController::class, 'create'])->name('growth.create');
    Route::post('/children/{child}/growth',
        [GrowthController::class, 'store'])->name('growth.store');
    Route::get('/children/{child}/growth',
        [GrowthController::class, 'chart'])->name('growth.chart');

    // Reminders
    Route::get('/reminders',          [ReminderController::class, 'index'])->name('reminders.index');
    Route::post('/reminders/dispatch',[ReminderController::class, 'dispatch'])->name('reminders.dispatch');

    // Reports (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

});