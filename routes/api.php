<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReservasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ApiController;
use App\Models\Reserva;

Route::get('/user', function (Request $request) {
    return $request->user();

})->middleware('auth:sanctum');

Route::post('/login', [ApiController::class, 'login']);
Route::post('/registrar', [ApiController::class, 'registrar']);
Route::get('/datosusuario/{email}', [ApiController::class, 'getDatosUsuario']);

Route::get('/reservas', [ApiController::class, 'index']);
Route::get('/caballos', [ApiController::class, 'getListaCaballos']);
Route::get('/misreservas/{id}', [ApiController::class, 'misReservas']);

Route::post('/addreserva', [ApiController::class, 'storeReserva']);
Route::post('/editreserva', [ApiController::class, 'editReserva']);
Route::post('/deletereserva', [ApiController::class, 'destroy']);
