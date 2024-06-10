<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservasController;
use App\Http\Controllers\CaballosController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    //Route::get('/dashboard', function () {
    //    return view('dashboard');
    //})->name('dashboard');

    Route::get('/dashboard', [ReservasController::class, 'index'])->name('dashboard');

    //Reservas
    Route::get('/misreservas', [ReservasController::class, 'index'])->name('reservas.misreservas');
    Route::get('/addreserva', [ReservasController::class, 'add'])->name('reservas.add');
    Route::post('/addreserva', [ReservasController::class, 'store'])->name('reservas.store');

    Route::get('/editreserva/{reserva}', [ReservasController::class, 'edit'])->name('reservas.edit');
    Route::post('/editreserva/{reserva}', [ReservasController::class, 'update'])->name('reservas.update');

    //Vista de caballos
    Route::get('/caballos', [CaballosController::class, 'index'])->name('caballos.caballos');
});

Route::get('/email/verify', function() {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request)
{
    $request->fulfill();

    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('messaje', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
