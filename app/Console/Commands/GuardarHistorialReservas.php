<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class GuardarHistorialReservas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:guardar-historial-reservas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Guardar reservas antiguas en el historial de reservas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $reservasAntiguas = Reserva::where('fecha', '<', $now)->get();

        foreach ($reservasAntiguas as $reserva)
        {
            DB::table('historial')->insert([
                'user_id' => $reserva->user_id,
                'id_caballo' => $reserva->id_caballo,
                'fecha' => $reserva->fecha,
                'hora' => $reserva->hora,
                'comentarios' => $reserva->comentarios,
                'created_at' => $reserva->created_at,
                'updated_at' => $reserva->updated_at,
            ]);

            $reserva->delete();
        }

    }
}
