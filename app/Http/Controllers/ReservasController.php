<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Caballo;
use App\Models\User;
use App\Http\Controllers\CaballosController;
use Carbon\Carbon;
use App\Mail\MailReserva;
use App\Mail\MailReservaEditada;
use App\Mail\MailReservaCancelada;
use Illuminate\Support\Facades\Mail;

class ReservasController extends Controller
{
    public function index()
    {
        $currentDate = Carbon::now();
        
        $reservas = auth()->user()->reservas()
            ->where('fecha', '>', $currentDate->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();
            
        return view('reservas.misreservas', ['reservas' => $reservas]);
        
    }

    public function add()
    {        
        $caballos = Caballo::where('enfermo', 0)->get();
        return view('reservas.add', ['caballos' => $caballos]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fecha' => 'required',
            'hora' => 'required',
            'caballo' => 'required',
            'comentarios' => 'nullable|string|max:255',
        ]);

        $mismaReserva = Reserva::where('user_id', auth()->id())
            ->where('fecha', $validatedData['fecha'])
            ->where('hora', $validatedData['hora'])
            ->exists();

        $maximosAlumnos = Reserva::where('fecha', $validatedData['fecha'])
            ->where('hora', $validatedData['hora'])
            ->count();
            
        $caballoReservado = Reserva::where('id_caballo', $validatedData['caballo'])
            ->where('fecha', $validatedData['fecha'])
            ->where('hora', $validatedData['hora'])
            ->exists();

        $fechaSeleccionada = Carbon::createFromFormat('Y-m-d', $validatedData['fecha']);
        $fechaMaxima = Carbon::now()->addDays(30);

        if ($fechaSeleccionada->gt($fechaMaxima))
        {
            return redirect()->back()->withErrors(['mensaje' => 'No puedes reservar con más de 30 días de antelación'])->withInput();
        }
        else if ($mismaReserva)
        {
            return redirect()->back()->withErrors(['mensaje' => 'Ya tienes una reserva para esta fecha y hora'])->withInput();
        }
        else if ($maximosAlumnos >= 5)
        {
            return redirect()->back()->withErrors(['mensaje' => 'No quedan plazas libres para ese día'])->withInput();
        }
        else if ($caballoReservado)
        {
            return redirect()->back()->withErrors(['mensaje' => 'El caballo seleccionado está reservado'])->withInput();
        }
        else{
            $reserva = new Reserva();
            $reserva->fecha = $validatedData['fecha'];
            $reserva->hora = $validatedData['hora'];
            $reserva->user_id = auth()->user()->id;
            $reserva->id_caballo = $validatedData['caballo'];
            $reserva->comentarios = $validatedData['comentarios'];
            $reserva->save();

            $this->sendEmailConfirmacion($reserva);
    
            return redirect()->route('reservas.misreservas');
        }

    }

    public function edit(Reserva $reserva)
    {
        $caballos = Caballo::where('enfermo', 0)->get();
        return view('reservas.edit', ['reserva' => $reserva, 'caballos' => $caballos]);
    }

    public function update(Request $request, Reserva $reserva)
    {
        if(isset($_POST['delete'])) {
            $this->sendEmailCancelada($reserva);
            $reserva->delete();
            return redirect()->route('reservas.misreservas');
        }
        else
        {
            $request->validate([
                'fecha' => 'required',
                'hora' => 'required',
                'caballo' => 'required',
                'comentarios' => 'nullable|string|max:255',
            ]);

            $mismaReserva = Reserva::where('user_id', auth()->id())
                ->where('fecha', $request->fecha)
                ->where('hora', $request->hora)
                ->exists();
    
            $maximosAlumnos = Reserva::where('fecha', $request->fecha)
                ->where('hora', $request->hora)
                ->count();
    
            $caballoReservado = Reserva::where('user_id', '!=', auth()->id())
                ->where('id_caballo', $request->caballo)
                ->where('fecha', $request->fecha)
                ->where('hora', $request->hora)
                ->exists();
    
            $fechaSeleccionada = Carbon::createFromFormat('Y-m-d', $request->fecha);
            $fechaMaxima = Carbon::now()->addDays(30);
    
            if ($fechaSeleccionada->gt($fechaMaxima))
            {
                return redirect()->back()->withErrors(['mensaje' => 'No puedes reservar con más de 30 días de antelación'])->withInput();
            }
            else if ($mismaReserva)
            {
                return redirect()->back()->withErrors(['mensaje' => 'Ya tienes una reserva para esta fecha y hora'])->withInput();
            }
            else if ($maximosAlumnos >= 5)
            {
                return redirect()->back()->withErrors(['mensaje' => 'No quedan plazas libres para ese día'])->withInput();
            }
            else if ($caballoReservado)
            {
                return redirect()->back()->withErrors(['mensaje' => 'El caballo seleccionado está reservado'])->withInput();
            }
            else
            {
                $reserva->fecha = $request->fecha;
                $reserva->hora = $request->hora;
                $reserva->id_caballo = $request->caballo;
                $reserva->comentarios = $request->comentarios;
                $reserva->save();

                $this->sendEmailEditada($reserva);
        
                return redirect()->route('reservas.misreservas');

            }
    
        }
    }

    private function sendEmailConfirmacion($reserva)
    {
        $alumno = User::where('id', $reserva->user_id)->first();
        $caballo = Caballo::where('id', $reserva->id_caballo)->first();
        $detallesReserva = [
            'nombre' => $alumno->name,
            'caballo' => $caballo->nombre,
            'fecha' => $reserva->fecha,
            'hora' => $reserva->hora
        ];

        Mail::to($alumno->email)->send(new MailReserva($detallesReserva));
    }

    private function sendEmailEditada($reserva)
    {
        $alumno = User::where('id', $reserva->user_id)->first();
        $caballo = Caballo::where('id', $reserva->id_caballo)->first();
        $detallesReserva = [
            'nombre' => $alumno->name,
            'caballo' => $caballo->nombre,
            'fecha' => $reserva->fecha,
            'hora' => $reserva->hora
        ];

        Mail::to($alumno->email)->send(new MailReservaEditada($detallesReserva));
    }

    private function sendEmailCancelada($reserva)
    {
        $alumno = User::where('id', $reserva->user_id)->first();
        $detallesReserva = [
            'fecha' => $reserva->fecha,
            'hora' => $reserva->hora
        ];

        Mail::to($alumno->email)->send(new MailReservaCancelada($detallesReserva));
    }

    public function indexApi()
    {
        $user = auth()->user();
    }
}
