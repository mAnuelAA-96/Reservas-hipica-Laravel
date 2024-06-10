<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\EnviarRecordatorioTarea;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//Guardar historial de reservas
Artisan::command('app:guardar-historial-reservas', function() {
    $command = new \App\Console\Commands\GuardarHistorialReservas();
    $command->handle();
});
